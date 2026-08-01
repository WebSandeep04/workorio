<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use App\Models\User;
use App\Models\Worklog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DailyStatusService
{
    /**
     * Get the consolidated status for a user on a specific date.
     * 
     * Priority:
     * 1. Attendance (Punched In)
     * 2. Approved Leave (Full/Half/SL)
     * 3. Holiday
     * 4. Weekoff (Dynamic from Shift)
     * 5. Absent (Default)
     */
    public function getStatus($userId, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $user = User::with(['employee.shiftHistory.shift'])->find($userId);

        if (!$user) {
            return $this->formatStatus('Unknown', '?', '#6c757d');
        }

        // 1. Check Attendance (Highest Priority - if they worked, they are present)
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();

        if ($attendance) {
            $isLate = (int)($attendance->late_minutes ?? 0) > 0;
            return $this->formatStatus(
                'Present', 
                'P', 
                '#28a745', 
                ['attendance' => $attendance, 'is_late' => $isLate]
            );
        }

        // 2. Check Approved Leave
        $leave = LeaveRequest::with('leaveType')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->first();

        if ($leave) {
            $statusName = 'On Leave';
            $label = 'L';
            if ($leave->is_half_day) {
                $statusName = 'Half Day';
                $label = 'HD';
            } elseif ($leave->is_sl) {
                $statusName = 'Short Leave';
                $label = 'SL';
            } elseif ($leave->is_rh) {
                $statusName = 'Restricted Holiday';
                $label = 'RH';
            }

            return $this->formatStatus(
                $statusName, 
                $label, 
                '#ffc107', 
                ['leave' => $leave]
            );
        }

        // 3. Check Holiday
        $holiday = Holiday::whereDate('holiday_date', $date)->first();
        if ($holiday) {
            return $this->formatStatus(
                'Holiday', 
                'H', 
                '#17a2b8', 
                ['holiday' => $holiday]
            );
        }

        // 4. Check Weekoff
        if ($this->isWeekoff($user, $date)) {
            return $this->formatStatus('Weekoff', 'W', '#6c757d');
        }

        // 5. Default: Absent
        return $this->formatStatus('Absent', 'A', '#dc3545');
    }

    /**
     * Check if a date is a missing worklog day for a user.
     * A day is missing ONLY if the user was present but has no worklog.
     */
    public function isWorklogMissing($userId, $date)
    {
        $status = $this->getStatus($userId, $date);
        
        // If they weren't present, they don't need a worklog (unless policy differs)
        if ($status['status'] !== 'Present') {
            return false;
        }

        $hasWorklog = Worklog::where('user_id', $userId)
            ->whereDate('work_date', $date)
            ->exists();

        return !$hasWorklog;
    }

    /**
     * Helper to determine weekoff based on user shift
     */
    private function isWeekoff($user, $date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek; // 0 (Sun) to 6 (Sat)
        
        $employee = $user->employee;
        $shift = $employee ? $employee->getShiftForDate($date) : null;
        if ($shift && is_array($shift->week_offs)) {
            return in_array($dayOfWeek, $shift->week_offs);
        }

        // Fallback to Sunday
        return $dayOfWeek === Carbon::SUNDAY;
    }

    /**
     * Format the output array
     */
    private function formatStatus($status, $label, $color, $metadata = [])
    {
        return [
            'status' => $status,
            'label' => $label,
            'color' => $color,
            'metadata' => $metadata
        ];
    }
}
