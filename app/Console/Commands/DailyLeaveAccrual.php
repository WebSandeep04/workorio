<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmploymentTypeLeaveRule;
use App\Models\LeaveAccrualCounter;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Services\LeaveBalanceService;
use App\Services\AttendanceReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DailyLeaveAccrual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:daily-accruals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process daily valid attendance to accrue leave balances incrementally based on employment rules.';

    protected $leaveService;
    protected $reportService;

    public function __construct(LeaveBalanceService $leaveService, AttendanceReportService $reportService)
    {
        parent::__construct();
        $this->leaveService = $leaveService;
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily Leave Accrual Processing...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Daily Leave Accrual Processing Completed.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $targetDate = Carbon::yesterday()->format('Y-m-d');
            
            $users = User::whereHas('employee', function ($query) {
                $query->where('status', 'active');
            })->with(['employee.shiftHistory.shift'])->get();

            $holidays = Holiday::whereDate('holiday_date', $targetDate)->pluck('holiday_date')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();

            foreach ($users as $user) {
            // Check if user has an Employment Type mapped
            $joiningDateString = $user->employee ? $user->employee->date_of_joining : null;
            if (!$joiningDateString || !$user->employee->employment_type_id) {
                continue;
            }
            
            if (Carbon::parse($joiningDateString)->gt(Carbon::parse($targetDate))) {
                continue;
            }

            $startOfMonth = Carbon::parse($targetDate)->startOfMonth()->format('Y-m-d');

            // Fetch attendances from start of month to calculate grace periods correctly
            $attendances = Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$startOfMonth, $targetDate])
                ->with('movements')
                ->get();
            
            $leaves = LeaveRequest::with('leaveType')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $targetDate)
                ->whereDate('end_date', '>=', $targetDate)
                ->get()
                ->mapWithKeys(function ($leave) use ($targetDate) {
                    $code = ($leave->leaveType && $leave->leaveType->code) ? $leave->leaveType->code : 'L';
                    if ($leave->is_rh) $code = 'RH';
                    elseif ($leave->is_sl) $code = 'SL';
                    elseif ($leave->is_half_day) $code = 'HD';
                    return [$targetDate => $code];
                })->toArray();
                
            $shift = $user->employee->shiftRelation ?? null;

            $dailyBreakdown = $this->reportService->generateDailyBreakdown(
                $attendances, 
                Carbon::parse($startOfMonth), 
                Carbon::parse($targetDate), 
                $holidays, 
                $leaves, 
                null, $user);
            
            // Get ONLY the breakdown for the target date (which is the last one in the array)
            $targetDayData = collect($dailyBreakdown)->last();
            $status = strtolower($targetDayData['status'] ?? 'absent');

            // If absolutely no valid paid log exists, they were absent/LWP. Skip tracking.
            if (str_contains($status, 'absent') || $status === 'unpaid leave' || $status === 'lwp') {
                continue; 
            }

            // Fetch active Accrual Rules for their specific Employment Type
            $accrualRules = EmploymentTypeLeaveRule::where('employment_type_id', $user->employee->employment_type_id)
                ->where('generation_type', 'accrual')
                ->get();

            foreach ($accrualRules as $rule) {
                // Step 4.5: Validate Eligibility (Wait Period) Check
                $eligibilityDays = $rule->eligibility_days ?? 0;
                
                if ($eligibilityDays > 0) {
                    $daysSinceJoining = Carbon::parse($joiningDateString)->diffInDays(Carbon::parse($targetDate), false);
                    if ($daysSinceJoining < $eligibilityDays) {
                        continue; // User is still in wait period
                    }
                }

                // Fetch or Create Counter for this exact Leave Type
                $counter = LeaveAccrualCounter::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $rule->leave_type_id],
                    ['valid_days_count' => 0]
                );

                // Increment their valid paid days
                $increment = in_array($status, ['halfday', 'present (partial leave)']) ? ($rule->halfday_count_value ?? 1.0) : 1.0;
                $counter->valid_days_count += $increment;
                
                $totalValidDays = (float) $counter->valid_days_count;

                // Threshold Check: Calculate how many leaves they earned based on the value
                $earnedLeaves = floor($totalValidDays / $rule->value);
                
                if ($earnedLeaves > 0) {
                    try {
                        // Reward Leave via Ledger!
                        $this->leaveService->creditLeave(
                            $user->id,
                            $rule->leave_type_id,
                            $earnedLeaves,
                            null,
                            "Earned Accrual for reaching valid days limit. Earning {$earnedLeaves} credits."
                        );

                        // Keep the remainder for the next cycle
                        $counter->valid_days_count = $totalValidDays - ($earnedLeaves * $rule->value);
                        Log::info("User ID {$user->id} earned {$earnedLeaves} Leave Type ID {$rule->leave_type_id}.");
                        $this->info("Credited {$earnedLeaves} leave to UID {$user->id} for type {$rule->leave_type_id}");
                    } catch (\Exception $e) {
                         Log::error("Failed crediting leave to UID {$user->id} : " . $e->getMessage());
                    }
                }

                // Save Counter state for tomorrow
                $counter->save();
            }
        }
        } catch (\Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
