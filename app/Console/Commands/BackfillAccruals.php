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

class BackfillAccruals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:backfill-accruals {--tenant= : The ID of the tenant to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill leave accruals from May 1st, 2026';

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
        $this->info('Starting Backfill Leave Accrual Processing...');

        $tenantId = $this->option('tenant');
        $query = Tenant::on('mysql');
        
        if ($tenantId) {
            $query->where('id', $tenantId);
            $this->info("Filtering by Tenant ID: {$tenantId}");
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Backfill Leave Accrual Processing Completed.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $baseStartDate = Carbon::parse('2026-05-01');
            $today = Carbon::today();
            
            $users = User::whereHas('employee', function ($query) {
                $query->where('status', 'active');
            })->with(['employee.shiftHistory.shift'])->get();

            foreach ($users as $user) {
            // Only process users who have joined on or before today
            $joiningDateString = $user->employee ? $user->employee->date_of_joining : null;
            if (!$joiningDateString || !$user->employee->employment_type_id) {
                continue;
            }
            
            $joiningDate = Carbon::parse($joiningDateString);
            if ($joiningDate->gt($today)) {
                continue;
            }

            // Fetch active Accrual Rules
            $accrualRules = EmploymentTypeLeaveRule::where('employment_type_id', $user->employee->employment_type_id)
                ->where('generation_type', 'accrual')
                ->get();
                
            if ($accrualRules->isEmpty()) {
                continue;
            }

            $this->info("Processing User ID: {$user->id}");

            foreach ($accrualRules as $rule) {
                $eligibilityDays = $rule->eligibility_days ?? 0;
                
                // Calculate Effective Start Date
                $eligibilityDate = $joiningDate->copy()->addDays($eligibilityDays);
                $effectiveStartDate = $baseStartDate->copy()->max($eligibilityDate);

                if ($effectiveStartDate->gt($today)) {
                    // Still in waiting period
                    continue;
                }

                $this->info("  - Rule Leave Type: {$rule->leave_type_id}, Effective Start Date: {$effectiveStartDate->format('Y-m-d')}");

                // Fetch Bulk Data between Effective Start Date and Today
                $attendances = Attendance::where('user_id', $user->id)
                    ->whereBetween('date', [$effectiveStartDate->format('Y-m-d'), $today->format('Y-m-d')])
                    ->with('movements')
                    ->get()
                    ->groupBy('user_id')
                    ->get($user->id, collect()); // Match structure of other reports
                
                $holidaysData = Holiday::whereBetween('holiday_date', [$effectiveStartDate->format('Y-m-d'), $today->format('Y-m-d')])
                    ->get()
                    ->keyBy(function($holiday) {
                        return Carbon::parse($holiday->holiday_date)->format('Y-m-d');
                    });
                $holidays = $holidaysData->keys()->toArray();
                    
                $leavesArray = [];
                $leavesReq = LeaveRequest::with('leaveType')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', $today->format('Y-m-d'))
                    ->where('end_date', '>=', $effectiveStartDate->format('Y-m-d'))
                    ->get();

                foreach ($leavesReq as $leave) {
                    $current = Carbon::parse($leave->start_date);
                    $end = Carbon::parse($leave->end_date);
                    
                    $code = ($leave->leaveType && !$leave->leaveType->is_paid) ? 'LWP' : ($leave->is_rh ? 'RH' : ($leave->is_sl ? 'SL' : ($leave->is_half_day ? 'HD' : 'L')));
                    
                    while ($current->lte($end)) {
                        $leavesArray[$current->format('Y-m-d')] = $code;
                        $current->addDay();
                    }
                }
                    
                $shift = $user->employee->shiftRelation ?? null;

                // Generate Daily Breakdown Month by Month to reset late grace periods correctly
                $dailyBreakdown = [];
                $currentMonthStart = $effectiveStartDate->copy()->startOfMonth();
                
                while ($currentMonthStart->lte($today)) {
                    $currentMonthEnd = $currentMonthStart->copy()->endOfMonth();
                    if ($currentMonthEnd->gt($today)) {
                        $currentMonthEnd = $today->copy();
                    }
                    
                    $actualStart = $currentMonthStart->copy()->max($effectiveStartDate);
                    
                    $monthlyBreakdown = $this->reportService->generateDailyBreakdown(
                        $attendances, 
                        $actualStart, 
                        $currentMonthEnd, 
                        $holidays, 
                        $leavesArray, 
                        $holidaysData, $user);
                    
                    foreach ($monthlyBreakdown as $data) {
                        $dailyBreakdown[] = $data;
                    }
                    
                    $currentMonthStart->addMonth()->startOfMonth();
                }

                $totalValidDays = 0.0;
                
                // --- Testing ONLY: Monthly Summary ---
                $monthlySummaries = [];
                
                foreach ($dailyBreakdown as $dateKey => $dayData) {
                    $dateStr = $dayData['date'] ?? (is_string($dateKey) ? $dateKey : 'unknown_date');
                    $status = strtolower($dayData['status'] ?? 'absent');
                    
                    $monthKey = Carbon::parse($dateStr)->format('F Y');
                    if (!isset($monthlySummaries[$monthKey])) {
                        $monthlySummaries[$monthKey] = [
                            'present' => 0,
                            'halfday' => 0,
                            'absent' => 0,
                            'leave' => 0,
                            'unpaid_leave' => 0,
                            'holiday_working' => 0,
                            'weekly_off_working' => 0,
                            'holiday' => 0,
                            'weekly_off' => 0
                        ];
                    }

                    if ($status === 'unpaid leave' || $status === 'lwp') {
                        $monthlySummaries[$monthKey]['unpaid_leave']++;
                    } elseif (in_array($status, ['leave', 'restricted holiday', 'leave (rh)'])) {
                        $monthlySummaries[$monthKey]['leave']++;
                    } elseif (str_contains($status, 'absent')) {
                        $monthlySummaries[$monthKey]['absent']++;
                    } elseif (in_array($status, ['halfday', 'present (partial leave)'])) {
                        $monthlySummaries[$monthKey]['halfday']++;
                    } elseif (in_array($status, ['present', 'present with sl', 'present with hd'])) {
                        $monthlySummaries[$monthKey]['present']++;
                    } elseif ($status === 'holiday working') {
                        $monthlySummaries[$monthKey]['holiday_working']++;
                    } elseif ($status === 'holiday') {
                        $monthlySummaries[$monthKey]['holiday']++;
                    } elseif ($status === 'h/w') {
                        $monthlySummaries[$monthKey]['holiday_working']++;
                    } elseif (!empty($dayData['is_sunday'])) {
                        if (str_contains($status, 'working') || $status === 'w/o-w') {
                            $monthlySummaries[$monthKey]['weekly_off_working']++;
                        } else {
                            $monthlySummaries[$monthKey]['weekly_off']++;
                        }
                    }

                    if (str_contains($status, 'absent') || $status === 'unpaid leave' || $status === 'lwp') {
                        continue;
                    }
                    
                    $increment = in_array($status, ['halfday', 'present (partial leave)']) ? ($rule->halfday_count_value ?? 1.0) : 1.0;
                    $totalValidDays += $increment;
                }
                
                $overallCounts = [
                    'present' => 0,
                    'halfday' => 0,
                    'absent' => 0,
                    'leave' => 0,
                    'unpaid_leave' => 0,
                    'holiday_working' => 0,
                    'weekly_off_working' => 0,
                    'holiday' => 0,
                    'weekly_off' => 0
                ];
                
                foreach ($monthlySummaries as $month => $sum) {
                    foreach ($sum as $k => $v) {
                        $overallCounts[$k] += $v;
                    }
                    $logStr = "Testing UID {$user->id} | {$month} | Rule {$rule->leave_type_id} => " . json_encode($sum);
                    Log::info($logStr);
                    $this->info($logStr);
                }
                
                $hdVal = $rule->halfday_count_value ?? 1.0;
                $formula = "({$overallCounts['present']} P + {$overallCounts['holiday_working']} HW + {$overallCounts['weekly_off_working']} WOW + {$overallCounts['leave']} L + {$overallCounts['holiday']} H + {$overallCounts['weekly_off']} WO) + ({$overallCounts['halfday']} HD * {$hdVal})";
                
                $this->info("    -> Total Valid Days: {$totalValidDays} | Formula: {$formula}");
                Log::info("Testing UID {$user->id} | Rule {$rule->leave_type_id} | Total Valid Days: {$totalValidDays} | Formula: {$formula}");

                // Calculate Earned Leaves and Remainder
                $earnedLeaves = floor($totalValidDays / $rule->value);
                $remainderDays = $totalValidDays - ($earnedLeaves * $rule->value);
                
                $this->info("    -> Earned Credits: {$earnedLeaves}, Remainder: {$remainderDays}");
                Log::info("Testing UID {$user->id} | Rule {$rule->leave_type_id} | Earned Credits: {$earnedLeaves}, Remainder: {$remainderDays}");

                if ($earnedLeaves > 0) {
                    try {
                        $this->leaveService->creditLeave(
                            $user->id,
                            $rule->leave_type_id,
                            $earnedLeaves,
                            null,
                            "Backfill Accrual (Total valid days: {$totalValidDays}) earning {$earnedLeaves} credits."
                        );
                        $this->info("    -> Successfully credited {$earnedLeaves} leaves.");
                        Log::info("User ID {$user->id} earned {$earnedLeaves} Leave Type ID {$rule->leave_type_id} during backfill.");
                    } catch (\Exception $e) {
                         Log::error("Failed crediting backfill leave to UID {$user->id} : " . $e->getMessage());
                         $this->error("    -> Failed to credit leaves.");
                    }
                }

                // Strictly Replace the Accrual Counter Remainder
                $counter = LeaveAccrualCounter::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $rule->leave_type_id],
                    ['valid_days_count' => 0]
                );
                
                $counter->valid_days_count = $remainderDays;
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
