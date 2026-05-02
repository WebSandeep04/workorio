<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\EmploymentTypeLeaveRule;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessLeaveLapse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:process-lapse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lapse unused leave balances automatically for users based on their employment leave rules.';

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
        $this->info('Starting Leave Lapse Processing...');

        $today = Carbon::today();
        $isFirstDayOfYear = $today->day === 1 && $today->month === 1;
        $isFirstDayOfMonth = $today->day === 1;

        // Fetch users
        $users = User::where('status', 'active')->get();

        foreach ($users as $user) {
            if (!$user->employee_id) continue;

            $employee = \App\Models\Employee::find($user->employee_id);
            if (!$employee || !$employee->employment_type_id) continue;

            // Fetch active leave rules where carry forward is disabled (i.e. No, Lapse)
            $lapseRules = EmploymentTypeLeaveRule::where('employment_type_id', $employee->employment_type_id)
                ->where('carry_forward_allowed', 0)
                ->get();

            foreach ($lapseRules as $rule) {
                $lapseType = strtolower($rule->lapse_type ?? 'yearly');

                // Determine if we should process the lapse today based on rule's lapse type
                $shouldLapse = false;
                $remarks = 'Lapsed balance';

                if ($lapseType === 'yearly' && $isFirstDayOfYear) {
                    $shouldLapse = true;
                    $remarks = 'Yearly balance lapsed';
                } elseif ($lapseType === 'monthly' && $isFirstDayOfMonth) {
                    $shouldLapse = true;
                    $remarks = 'Monthly balance lapsed';
                }

                if ($shouldLapse) {
                    $currentBalance = $this->leaveService->getBalance($user->id, $rule->leave_type_id);
                    if ($currentBalance > 0) {
                        try {
                            $this->leaveService->lapseLeave(
                                $user->id,
                                $rule->leave_type_id,
                                $currentBalance,
                                $remarks
                            );

                            $this->info("Successfully lapsed {$currentBalance} days for User UID {$user->id}, Leave Type ID {$rule->leave_type_id}");
                            Log::info("Lapsed {$currentBalance} days for User ID {$user->id}, Leave Type ID {$rule->leave_type_id}");
                        } catch (\Exception $e) {
                            Log::error("Failed to lapse leave for User ID {$user->id}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        $this->info('Leave Lapse Processing Completed.');
    }
}
