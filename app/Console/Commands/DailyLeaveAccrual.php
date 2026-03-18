<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmploymentTypeLeaveRule;
use App\Models\LeaveAccrualCounter;
use App\Models\Worklog;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public function __construct(LeaveBalanceService $leaveService)
    {
        parent::__construct();
        $this->leaveService = $leaveService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily Leave Accrual Processing...');
        
        // We evaluate attendance for "Yesterday"
        $targetDate = Carbon::yesterday()->format('Y-m-d');
        
        $users = User::where('status', 'active')->get();

        foreach ($users as $user) {
            // Check if user has an Employment Type mapped
            if (!$user->employment_type_id) {
                continue;
            }

            // Does user have a valid worklog/attendance yesterday indicating they were technically "Present/Paid"?
            // E.g., checking if they have ANY worklog for yesterday. Or, you can expand this to checking if they exist in approved LeaveRequests.
            $hasValidDay = Worklog::where('user_id', $user->id)->where('work_date', $targetDate)->exists();
            
            // Or if they were on approved Leave yesterday, it also counts as a Valid Day:
            // $isOnApprovedLeave = LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->where('start_date', '<=', $targetDate)->where('end_date', '>=', $targetDate)->exists();

            // If absolutely no valid paid log exists, they were absent/LWP. Skip tracking.
            if (!$hasValidDay) {
                continue; 
            }

            // Fetch active Accrual Rules for their specific Employment Type
            $accrualRules = EmploymentTypeLeaveRule::where('employment_type_id', $user->employment_type_id)
                ->where('generation_type', 'accrual')
                ->get();

            foreach ($accrualRules as $rule) {
                // Fetch or Create Counter for this exact Leave Type
                $counter = LeaveAccrualCounter::firstOrCreate(
                    ['user_id' => $user->id, 'leave_type_id' => $rule->leave_type_id],
                    ['valid_days_count' => 0]
                );

                // Increment their valid paid days
                $counter->valid_days_count += 1;

                // Threshold Check: Did they hit the limit (e.g. 30 days) to earn the reward?
                if ($counter->valid_days_count >= $rule->value) {
                    try {
                        // Reward +1.00 Leave via Ledger!
                        $this->leaveService->creditLeave(
                            $user->id,
                            $rule->leave_type_id,
                            1.00, // Earning 1 full leave per cycle
                            null,
                            "Earned Accrual for reaching " . $rule->value . " valid days limit."
                        );

                        // Reset Counter cleanly
                        $counter->valid_days_count = 0;
                        Log::info("User ID {$user->id} earned 1 Leave Type ID {$rule->leave_type_id}.");
                        $this->info("Credited 1 leave to UID {$user->id} for type {$rule->leave_type_id}");
                    } catch (\Exception $e) {
                         Log::error("Failed crediting leave to UID {$user->id} : " . $e->getMessage());
                    }
                }

                // Save Counter state for tomorrow
                $counter->save();
            }
        }

        $this->info('Daily Leave Accrual Processing Completed.');
    }
}
