<?php

namespace App\Services;

use App\Models\LeaveLedger;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LeaveBalanceService
{
    /**
     * Get the current balance for a user's specific leave type.
     */
    public function getBalance($userId, $leaveTypeId): float
    {
        $latestTransaction = LeaveLedger::where('user_id', $userId)
            ->where('leave_type_id', $leaveTypeId)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestTransaction) {
            return (float) $latestTransaction->balance_after;
        }

        $user = \App\Models\User::find($userId);
        if ($user && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
            if ($employee && $employee->employment_type_id) {
                $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $employee->employment_type_id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->first();
                if ($rule && $rule->generation_type === 'unlimited') {
                    return 365.0;
                }
            }
        }

        return 0.00;
    }

    /**
     * Credit a leave manually (e.g., from Accrual Job)
     */
    public function creditLeave(int $userId, int $leaveTypeId, float $amount, ?Model $reference = null, string $remarks = ''): LeaveLedger
    {
        return DB::transaction(function () use ($userId, $leaveTypeId, $amount, $reference, $remarks) {
            $currentBalance = $this->getBalance($userId, $leaveTypeId);
            $newBalance = $currentBalance + $amount;

            return LeaveLedger::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'transaction_type' => 'credit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'remarks' => $remarks
            ]);
        });
    }

    /**
     * Debit a leave (e.g., when Request implies Approved)
     */
    public function debitLeave(int $userId, int $leaveTypeId, float $amount, ?Model $reference = null, string $remarks = ''): LeaveLedger
    {
        return DB::transaction(function () use ($userId, $leaveTypeId, $amount, $reference, $remarks) {
            $currentBalance = $this->getBalance($userId, $leaveTypeId);
            
            // Allow negative or throw exception? Safer to allow if Manager forced approval.
            $newBalance = $currentBalance - $amount;

            return LeaveLedger::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'transaction_type' => 'debit',
                'amount' => -$amount, // Stored as pure reduction
                'balance_after' => $newBalance,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'remarks' => $remarks
            ]);
        });
    }

    /**
     * Lapse remaining carry forward completely or partially at Year-End
     */
    public function lapseLeave(int $userId, int $leaveTypeId, float $amountToLapse, string $remarks = 'Year-End Lapsed'): LeaveLedger
    {
        return DB::transaction(function () use ($userId, $leaveTypeId, $amountToLapse, $remarks) {
            $currentBalance = $this->getBalance($userId, $leaveTypeId);
            $newBalance = max(0, $currentBalance - $amountToLapse);

            return LeaveLedger::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'transaction_type' => 'lapsed',
                'amount' => -$amountToLapse,
                'balance_after' => $newBalance,
                'reference_type' => null,
                'reference_id' => null,
                'remarks' => $remarks
            ]);
        });
    }

    /**
     * Automatically initialize prefill leaves for a User based on their assigned Employee profile.
     */
    public function initializePrefillLeaves(User $user): void
    {
        if (!$user->employee_id) return;
        
        $employee = \App\Models\Employee::find($user->employee_id);
        if (!$employee || !$employee->employment_type_id) return;

        // Ensure tables exist before running logic
        if (!\Illuminate\Support\Facades\Schema::hasTable('employment_type_leave_rules')) return;

        $rules = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $employee->employment_type_id)
            ->whereIn('generation_type', ['prefill', 'unlimited'])
            ->get();

        foreach ($rules as $rule) {
            // Check if user has already received ANY ledger history for this specific leave type
            $hasLedgerHistory = LeaveLedger::where('user_id', $user->id)
                ->where('leave_type_id', $rule->leave_type_id)
                ->exists();

            // If history doesn't exist, this is a legally new assignment. Prefill it exactly once.
            if (!$hasLedgerHistory) {
                $creditAmount = $rule->generation_type === 'unlimited' ? 365.0 : (float) $rule->value;
                $remarks = $rule->generation_type === 'unlimited' 
                    ? 'Initial System Unlimited Balance based on Employment Type' 
                    : 'Initial System Prefill based on Employment Type';

                $this->creditLeave(
                    $user->id,
                    $rule->leave_type_id,
                    $creditAmount,
                    null,
                    $remarks
                );
            }
        }
    }
}
