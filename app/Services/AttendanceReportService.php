<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Movement;
use App\Models\User;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceReportService
{
    /**
     * Get shift hours (Full Day and Half Day thresholds)
     */
    public function getThresholds($shift)
    {
        $fullDayHr = 7;
        $halfDayHr = 4;

        if ($shift) {
            if (isset($shift->full_day_hr) && $shift->full_day_hr > 0) {
                $fullDayHr = (float) $shift->full_day_hr;
                $halfDayHr = isset($shift->half_day_hr) && $shift->half_day_hr > 0 
                    ? (float) $shift->half_day_hr 
                    : $fullDayHr / 2;
            } elseif (!empty($shift->start_time) && !empty($shift->end_time)) {
                $start = Carbon::parse($shift->start_time);
                $end = Carbon::parse($shift->end_time);
                
                if ($end->lt($start)) {
                    $end->addDay();
                }
                
                $fullDayHr = $start->diffInMinutes($end) / 60;
                $halfDayHr = $fullDayHr / 2;
            }
        }

        return [$fullDayHr, $halfDayHr];
    }

    /**
     * Calculate total hours based on first IN and last OUT
     */
    public function calculateTotalHours($movements, $shift = null, $date = null): float
    {
        if ($movements->isEmpty()) {
            return 0;
        }

        $movements = $movements->sortBy('time');
        
        $firstPunchIn = null;
        $lastPunchOut = null;
        
        foreach ($movements as $movement) {
            if (in_array($movement->movement_type, ['office', 'field'])) {
                $time = Carbon::parse($movement->time)->setTimezone('Asia/Kolkata');
                if ($movement->movement_action === 'in' && !$firstPunchIn) {
                    $firstPunchIn = $time;
                }
                if ($movement->movement_action === 'out') {
                    $lastPunchOut = $time;
                }
            }
        }
        
        if (!$firstPunchIn) {
            return 0;
        }
        
        if (!$lastPunchOut) {
            // For reports, we usually don't use Carbon::now() for past dates
            // But for today, we might.
            if ($date && Carbon::parse($date)->isToday()) {
                $lastPunchOut = Carbon::now()->setTimezone('Asia/Kolkata');
            } else {
                // If it's a past date and no punch out, we can't assume they are still working.
                // We set it to firstPunchIn so the total hours calculated will be 0.
                $lastPunchOut = $firstPunchIn->copy();
            }
        }

        if ($shift && $date) {
            $shiftDate = Carbon::parse($date)->format('Y-m-d');
            
            if ($shift->start_time) {
                $shiftStartTime = Carbon::parse($shift->start_time)->format('H:i:s');
                $startLimit = Carbon::parse($shiftDate . ' ' . $shiftStartTime, 'Asia/Kolkata');
                if ($firstPunchIn->lt($startLimit)) {
                    $firstPunchIn = $startLimit;
                }
            }
            
            if ($shift->end_time) {
                $shiftEndTime = Carbon::parse($shift->end_time)->format('H:i:s');
                $endLimit = Carbon::parse($shiftDate . ' ' . $shiftEndTime, 'Asia/Kolkata');
                
                if ($shift->extended_hr > 0) {
                    $endLimit->addMinutes((int)($shift->extended_hr * 60));
                }
                
                if ($lastPunchOut->gt($endLimit)) {
                    $lastPunchOut = $endLimit;
                }
            }
        }

        if ($firstPunchIn->gt($lastPunchOut)) {
            return 0;
        }
        
        return $firstPunchIn->diffInMinutes($lastPunchOut) / 60;
    }

    /**
     * Calculate hours for a specific movement type (office, field, break)
     */
    public function calculateTypeHours($movements, $type): float
    {
        $totalMinutes = 0;
        $inTime = null;
        
        $typeMovements = $movements->where('movement_type', $type)->sortBy('time');
        
        foreach ($typeMovements as $m) {
            $time = Carbon::parse($m->time);
            if ($m->movement_action === 'in') {
                if (!$inTime) $inTime = $time;
            } elseif ($m->movement_action === 'out') {
                if ($inTime) {
                    $totalMinutes += $inTime->diffInMinutes($time);
                    $inTime = null;
                }
            }
        }
        
        // If still "in", calculate until now only if it's today
        if ($inTime && $inTime->isToday()) {
            $totalMinutes += $inTime->diffInMinutes(Carbon::now());
        }
        
        return $totalMinutes / 60;
    }

    /**
     * Calculate movement cycles
     */
    public function calculateDayCycles($movements)
    {
        $cycles = ['office' => 0, 'field' => 0, 'break' => 0];
        foreach ($movements as $m) {
            if ($m->movement_action === 'in') {
                if (isset($cycles[$m->movement_type])) {
                    $cycles[$m->movement_type]++;
                }
            }
        }
        return $cycles;
    }

    public function determineStatus($date, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $leaveType = null, $hasHalfDayLeave = false, $shortLeaveHr = 0, $enforceTimeRestriction = 0)
    {
        $hoursInMinutes = (int) round($hours * 60);
        $fullDayInMinutes = (int) round($fullDayHr * 60);
        $halfDayInMinutes = (int) round($halfDayHr * 60);
        $shortLeaveInMinutes = (int) round($shortLeaveHr * 60);
        
        // Prevent SL stacking with Half Day Leave
        if ($hasHalfDayLeave) {
            $shortLeaveInMinutes = 0;
            if ($leaveType === 'SL') {
                $leaveType = 'HD';
            }
        }

        $effectiveInMinutes = $hoursInMinutes + $shortLeaveInMinutes + ($hasHalfDayLeave ? $halfDayInMinutes : 0);

        if ($isWeeklyOff) {
            $dayName = Carbon::parse($date)->format('l');
            $isWorking = $hoursInMinutes > 0;
            if ($enforceTimeRestriction && $isWorking) {
                $isWorking = $effectiveInMinutes >= $halfDayInMinutes;
            }
            return [
                'code' => $isWorking ? 'W/O-W' : 'W/O',
                'label' => $isWorking ? "$dayName Working" : strtolower($dayName),
                'class' => $isWorking ? 'text-success' : 'text-secondary'
            ];
        }

        if ($isHoliday) {
            $isWorking = $hoursInMinutes > 0;
            if ($enforceTimeRestriction && $isWorking) {
                $isWorking = $effectiveInMinutes >= $halfDayInMinutes;
            }
            return [
                'code' => $isWorking ? 'H/W' : 'H',
                'label' => $isWorking ? 'holiday working' : 'holiday',
                'class' => $isWorking ? 'text-success' : 'text-info'
            ];
        }

        // If SL is taken but worked hours < half day hr, it's simply absent
        if ($leaveType === 'SL' && $hoursInMinutes < $halfDayInMinutes) {
            return [
                'code' => 'A',
                'label' => 'absent by less hr',
                'class' => 'text-danger'
            ];
        }

        // Pure full day work takes precedence even if they had half-day leave
        if ($hoursInMinutes >= $fullDayInMinutes) {
            return [
                'code' => 'P',
                'label' => 'present',
                'class' => 'text-success'
            ];
        }

        if ($hasHalfDayLeave) {
            $requiredHalfDayMinutes = (int) round($fullDayInMinutes / 2);
            if ($hoursInMinutes >= $requiredHalfDayMinutes) {
                return [
                    'code' => 'P2',
                    'label' => 'halfday',
                    'class' => 'text-primary'
                ];
            } else {
                return [
                    'code' => 'A',
                    'label' => 'absent by less hr',
                    'class' => 'text-danger'
                ];
            }
        }

        if ($effectiveInMinutes >= $fullDayInMinutes) {
            $code = 'P';
            $label = 'present';
            
            if ($shortLeaveInMinutes > 0) {
                $code = 'P (SL)';
                $label = 'present with SL';
            }
            
            return [
                'code' => $code,
                'label' => $label,
                'class' => 'text-success'
            ];
        } 
        
        // Pure halfday logic without partial leave labels
        if ($hoursInMinutes >= $halfDayInMinutes) {
            return [
                'code' => 'P2',
                'label' => 'halfday',
                'class' => 'text-primary'
            ];
        } 
        
        if ($leaveType === 'L' || $leaveType === 'RH') {
            return [
                'code' => $leaveType,
                'label' => $leaveType === 'L' ? 'leave' : 'restricted holiday',
                'class' => 'text-warning'
            ];
        } 

        if ($leaveType === 'LWP') {
            return [
                'code' => 'LWP',
                'label' => 'unpaid leave',
                'class' => 'text-danger'
            ];
        }
        
        return [
            'code' => 'A',
            'label' => 'absent by less hr',
            'class' => 'text-danger'
        ];
    }

    public function determineStatusAndReason($originalStatusLabel, $hours, $fullDayHr, $halfDayHr, $lateBy, $previousGrace, $isLeave, $isHalfDayLeave, $isGracePunish = 0, $graceBounceDays = 0, $lateDaysExceeded = 0, $exemptGraceOnOvertime = 1)
    {
        $finalStatus = $originalStatusLabel;
        $reason = '-';

        $statusLower = strtolower($originalStatusLabel);
        
        $isOvertime = str_contains($statusLower, 'working') && (str_contains($statusLower, 'holiday') || str_contains($statusLower, 'w/o') || str_contains($statusLower, 'sunday'));

        if (str_contains($statusLower, 'present') || str_contains($statusLower, 'working')) {
            if ($lateBy > 0 && $previousGrace < $lateBy && !($isOvertime && $exemptGraceOnOvertime)) {
                if ($isGracePunish) {
                    if ($lateDaysExceeded > $graceBounceDays) {
                        $finalStatus = 'halfday';
                        $reason = 'Monthly grace exhausted & bounce limits passed';
                    } else {
                        $reason = 'Grace exhausted but covered under bounce day';
                    }
                } else {
                    $reason = 'Monthly grace exhausted';
                }
            } else if ($lateBy > 0) {
                $reason = 'Covered under grace';
            } else if (str_contains($statusLower, 'sl')) {
                $reason = 'Present with SL';
            } else {
                $reason = '-';
            }
        } else if (str_contains($statusLower, 'absent')) {
            if ($hours <= 0 && !$isLeave) {
                $reason = 'No attendance recorded';
            } else {
                $reason = "Worked less than " . intval($halfDayHr) . " hrs";
            }
            $finalStatus = 'absent';
        } else if (str_contains($statusLower, 'halfday')) {
            if ($isHalfDayLeave) {
                $reason = 'Approved Half Day';
            } else if ($lateBy > 0 && $previousGrace < $lateBy) {
                $reason = 'Monthly grace exhausted';
            } else {
                $reason = "Worked less than " . intval($fullDayHr) . " hrs";
            }
            $finalStatus = 'halfday';
        } else if (str_contains($statusLower, 'leave') || str_contains($statusLower, 'holiday') || str_contains($statusLower, 'off')) {
            if (str_contains($statusLower, 'unpaid leave')) {
                $finalStatus = 'unpaid leave';
                $reason = 'Unpaid Leave';
            } elseif (str_contains($statusLower, 'leave') && !str_contains($statusLower, 'sl')) {
                $finalStatus = 'leave';
                $reason = 'On Leave';
            } else {
                $finalStatus = strtolower($originalStatusLabel);
                $reason = '-';
            }
        }

        return ['status' => $finalStatus, 'reason' => $reason];
    }

    /**
     * Calculate monthly summary statistics
     */
    public function calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $user = null)
    {
        $totalWorkingDays = 0;
        $totalDaysWorked = 0;
        $totalHours = 0;
        $totalOfficeHours = 0;
        $totalFieldHours = 0;
        $totalBreakTime = 0;
        $totalCycles = ['office' => 0, 'field' => 0, 'break' => 0];
        $daysOnLeave = 0;
        $totalLeaves = 0; 
        $totalUnpaidLeaves = 0;
        $presentDays = 0; 
        $halfDays = 0; 
        $totalSundays = 0;
        $totalSundaysWorked = 0;
        $totalHolidaysWorked = 0;
        $totalLess8_30 = 0;
        $totalMore8_30 = 0;
        $lateCount = 0;
        $totalLateMinutes = 0;
        $lateLogs = [];
        
        // Removed static $shift fetching from here
        
        // Map leaves to date => type for easier lookup
        $leaveMap = [];
        if (is_array($leaves)) {
            foreach ($leaves as $key => $val) {
                if (is_int($key)) {
                    $leaveMap[$val] = 'L'; // Default to Leave if only date is provided
                } else {
                    $leaveMap[$key] = $val;
                }
            }
        }
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayName = $currentDate->format('l');
            $isWeeklyOff = false;
            
            $shift = $user && $user->employee ? $user->employee->getShiftForDate($currentDate) : null;
            
            if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
            }

            if ($isWeeklyOff) {
                $totalSundays++;
            } elseif (!in_array($currentDate->format('Y-m-d'), $holidays)) {
                $totalWorkingDays++;
            }
            $currentDate->addDay();
        }
        
        $attendanceDates = [];
        $leaveDates = [];
        $holidaysWithAttendance = []; 
        $lateDaysExceeded = 0;
        
        foreach ($attendances as $attendance) {
            $attendanceDate = Carbon::parse($attendance->date);
            $dateStr = $attendanceDate->format('Y-m-d');
            
            $dayName = $attendanceDate->format('l');
            
            $shift = $user && $user->employee ? $user->employee->getShiftForDate($dateStr) : null;
            
            $isWeeklyOff = false;
            $isHalfDayWorking = false;
            if ($shift) {
                if ($shift->week_offs && is_array($shift->week_offs)) {
                    $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
                }
                if ($shift->half_days && is_array($shift->half_days)) {
                    $isHalfDayWorking = in_array(date('w', strtotime($dayName)), $shift->half_days);
                }
            }

            $lateBy = (int) abs($attendance->late_minutes ?? 0);
            $previousGrace = $shift ? ($shift->min_per_month_late_allow - $totalLateMinutes) : 0;
            $totalLateMinutes += $lateBy;

            if ($shift && $lateBy > 0) {
                $isGraceExhaustedNow = ($shift->min_per_month_late_allow - $totalLateMinutes) < 0;
                if ($isGraceExhaustedNow) {
                    $lateDaysExceeded++;
                }
            }

            $dayHours = $this->calculateTotalHours($attendance->movements, $shift, $attendance->date);
            [$fullDayHr, $halfDayHr] = $this->getThresholds($shift);
            if ($isHalfDayWorking) {
                $fullDayHr = $halfDayHr;
                $halfDayHr = $halfDayHr / 2;
            }
            $leaveType = $leaveMap[$dateStr] ?? null;
            $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
            $hasHalfDayLeave = ($leaveType === 'HD');
            $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;

            if ($isWeeklyOff || in_array($dateStr, $holidays)) {
                $statusInfo = $this->determineStatus($dateStr, $dayHours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                if ($statusInfo['code'] === 'W/O-W') {
                    $totalSundaysWorked++;
                } elseif ($statusInfo['code'] === 'H/W') {
                    $totalHolidaysWorked++;
                }
            }
            
            if (!$isWeeklyOff && !in_array($dateStr, $holidays)) {
                $totalHours += $dayHours;
                
                $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                $statusInfo = $this->determineStatus($dateStr, $dayHours, $fullDayHr, $halfDayHr, false, false, $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                $isGracePunish = $shift ? ($shift->is_grace_punish ?? 0) : 0;
                $graceBounceDays = $shift ? ($shift->grace_bounce_day ?? 0) : 0;
                
                $exemptGraceOnOvertime = $shift ? ($shift->exempt_grace_on_overtime ?? 1) : 1;
                $finalStatusData = $this->determineStatusAndReason($statusInfo['label'], $dayHours, $fullDayHr, $halfDayHr, $lateBy, $previousGrace, isset($leaveMap[$dateStr]), $hasHalfDayLeave, $isGracePunish, $graceBounceDays, $lateDaysExceeded, $exemptGraceOnOvertime);
                $finalStatusLabel = strtolower($finalStatusData['status']);

                if ($attendance->is_wfh && str_contains($finalStatusLabel, 'present')) {
                    $finalStatusLabel = 'halfday';
                }

                $lastOutMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if (!$lastOutMov && $dayHours > 0) {
                    $finalStatusLabel = 'absent';
                }

                if (in_array($finalStatusLabel, ['present', 'present with sl', 'present with hd'])) {
                    $presentDays++;
                    $attendanceDates[] = $dateStr;
                    $totalDaysWorked++;
                } elseif (in_array($finalStatusLabel, ['halfday', 'present (partial leave)'])) {
                    $halfDays++;
                    $attendanceDates[] = $dateStr;
                    $totalDaysWorked++;
                }

                [$origFullDayHr, $origHalfDayHr] = $this->getThresholds($shift);
                $effectiveInMinutes = (int) round(($dayHours + $slHours + ($hasHalfDayLeave ? $origHalfDayHr : 0)) * 60);
                if ($effectiveInMinutes >= (int) round($origFullDayHr * 60)) {
                    $totalMore8_30++;
                } elseif ($effectiveInMinutes >= (int) round($origHalfDayHr * 60)) {
                    $totalLess8_30++;
                }
                
                $totalOfficeHours += $this->calculateTypeHours($attendance->movements, 'office');
                $totalFieldHours += $this->calculateTypeHours($attendance->movements, 'field');
                $totalBreakTime += $this->calculateTypeHours($attendance->movements, 'break');
                
                $cycles = $this->calculateDayCycles($attendance->movements);
                $totalCycles['office'] += $cycles['office'];
                $totalCycles['field'] += $cycles['field'];
                $totalCycles['break'] += $cycles['break'];

                // Late Check
                if ($shift && $shift->start_time) {
                    $firstPunch = $attendance->movements->whereIn('movement_type', ['office', 'field'])->first();
                    if ($firstPunch) {
                        $punchTime = Carbon::parse($firstPunch->time)->setTimezone('Asia/Kolkata');
                        $shiftDate = Carbon::parse($attendance->date)->format('Y-m-d');
                        $shiftTime = Carbon::parse($shift->start_time)->format('H:i:s');
                        $shiftStart = Carbon::parse($shiftDate . ' ' . $shiftTime, 'Asia/Kolkata');
                        $lateThreshold = $shiftStart->copy()->addMinutes($shift->late_min ?? 0);
                        
                        $isLate = false;
                        $reason = '';
                        
                        if ($punchTime->gt($lateThreshold)) {
                            if ($dayHours >= ($shift->half_day_hr ?? 4)) {
                                $lateCount++;
                                $isLate = true;
                                $reason = 'Late: Punched after threshold and worked >= half day';
                            } else {
                                $reason = 'Not Late: Punched after threshold but worked < half day';
                            }
                        } else {
                            $reason = 'Not Late: on time';
                        }
                        
                        $lateLogs[] = [
                            'date' => $dateStr,
                            'punch' => $punchTime->toDateTimeString(),
                            'threshold' => $lateThreshold->toDateTimeString(),
                            'hours' => $dayHours,
                            'is_late' => $isLate,
                            'reason' => $reason
                        ];
                    }
                }
            }
        }
        $totalShortLeaves = 0;
        $uniqueLeaves = array_unique(array_keys($leaveMap));
        
        $leaveDates = [];
        foreach ($uniqueLeaves as $dateStr) {
            $leaveType = $leaveMap[$dateStr] ?? 'L';
            
            if ($leaveType === 'SL') {
                $totalShortLeaves++;
            }
            
            $leaveCarbon = Carbon::parse($dateStr);
            $dayName = $leaveCarbon->format('l');
            $dayName = $leaveCarbon->format('l');
            $isWeeklyOff = false;
            
            $shift = $user && $user->employee ? $user->employee->getShiftForDate($dateStr) : null;
            
            if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
            }

            if (!$isWeeklyOff 
                && !in_array($dateStr, $holidays) 
                && !in_array($dateStr, $attendanceDates)
                && !in_array($dateStr, $holidaysWithAttendance)) {
                
                if ($leaveType === 'LWP') {
                    $totalUnpaidLeaves++;
                } else {
                    $totalLeaves++;
                }

                // Do we count SL in daysOnLeave (absents)?
                // Let's assume SL is not a full leave. But for now keep it same logic unless requested.
                if ($leaveType !== 'SL') {
                    if ($leaveType !== 'LWP') {
                        $leaveDates[] = $dateStr;
                        $daysOnLeave++;
                    }
                }
            }
        }
        
        $totalHolidays = 0;
        foreach ($holidays as $holidayDate) {
            $holidayCarbon = Carbon::parse($holidayDate);
            $dayName = $holidayCarbon->format('l');
            $isWeeklyOff = false;
            $shift = $user && $user->employee ? $user->employee->getShiftForDate($holidayDate) : null;
            if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
            }
            if (!$isWeeklyOff) {
                $totalHolidays++;
            }
        }
        
        $daysAbsent = max(0, $totalWorkingDays - $totalDaysWorked - $daysOnLeave - $totalUnpaidLeaves);
        
        $attendancePercentage = $totalWorkingDays > 0 
            ? round((($presentDays + $halfDays) / $totalWorkingDays) * 100, 1) 
            : 0;
        
        return [
            'total_working_days' => $totalWorkingDays,
            'days_worked' => $totalDaysWorked,
            'days_absent' => $daysAbsent,
            'days_on_leave' => $daysOnLeave, 
            'total_unpaid_leaves' => $totalUnpaidLeaves,
            'total_short_leaves' => $totalShortLeaves,
            'attendance_percentage' => min(100, $attendancePercentage),
            'total_present_combined' => $presentDays + $halfDays + $totalHolidaysWorked + $totalSundaysWorked,
            'total_present' => $presentDays,
            'total_halfday' => $halfDays,
            'total_sundays' => $totalSundays,
            'total_sundays_worked' => $totalSundaysWorked,
            'total_holidays' => $totalHolidays,
            'total_holidays_worked' => $totalHolidaysWorked,
            'total_hours' => round($totalHours, 2),
            'total_office_hours' => round($totalOfficeHours, 2),
            'total_field_hours' => round($totalFieldHours, 2),
            'total_break_time' => round($totalBreakTime, 2),
            'avg_hours_per_day' => $totalDaysWorked > 0 ? round($totalHours / $totalDaysWorked, 2) : 0,
            'total_cycles' => $totalCycles,
            'total_less_8_30' => $totalLess8_30,
            'total_more_8_30' => $totalMore8_30,
            'late_count' => $lateCount,
            'total_late_minutes' => $totalLateMinutes,
            'late_logs' => $lateLogs
        ];
    }

    /**
     * Generate daily breakdown for a user and month
     */
    public function generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $user = null)
    {
        $dailyData = [];
        $attendanceByDate = $attendances->keyBy(function ($attendance) {
            return Carbon::parse($attendance->date)->format('Y-m-d');
        });
        
        $cumulativeLateMinutes = 0;
        $lateDaysExceeded = 0;
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayName = $currentDate->format('l');
            $displayDate = $currentDate->format('M j, Y');
            
            $isWeeklyOff = false;
            $isHalfDayWorking = false;
            $shift = $user && $user->employee ? $user->employee->getShiftForDate($dateStr) : null;
            if ($shift) {
                if ($shift->week_offs && is_array($shift->week_offs)) {
                    $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
                }
                if ($shift->half_days && is_array($shift->half_days)) {
                    $isHalfDayWorking = in_array(date('w', strtotime($dayName)), $shift->half_days);
                }
            }

            $dayData = [
                'date' => $dateStr,
                'display_date' => $displayDate,
                'day_name' => $dayName,
                'is_sunday' => $isWeeklyOff,
                'is_holiday' => in_array($dateStr, $holidays),
                'is_leave' => isset($leaves[$dateStr]),
                'leave_type' => $leaves[$dateStr] ?? null,
                'holiday_name' => null,
                'status' => 'absent',
                'status_reason' => '-',
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'cycles' => ['office' => 0, 'field' => 0, 'break' => 0],
                'first_in' => '-',
                'last_out' => '-',
                'late_by' => '-',
                'grace_balance' => '-',
                'late_reason' => '-',
                'movements' => []
            ];
            
            if (isset($attendanceByDate[$dateStr])) {
                $attendance = $attendanceByDate[$dateStr];
                $dayData['hours'] = $this->calculateTotalHours($attendance->movements, $shift, $dateStr);
                $dayData['office_hours'] = $this->calculateTypeHours($attendance->movements, 'office');
                $dayData['field_hours'] = $this->calculateTypeHours($attendance->movements, 'field');
                $dayData['break_time'] = $this->calculateTypeHours($attendance->movements, 'break');
                $dayData['cycles'] = $this->calculateDayCycles($attendance->movements);
                $dayData['late_minutes'] = (int) ($attendance->late_minutes ?? 0);
                $dayData['is_wfh'] = $attendance->is_wfh;
                
                $firstInMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'in')->first();
                if ($firstInMov) {
                    $dayData['first_in'] = Carbon::parse($firstInMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $lastOutMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if ($lastOutMov) {
                    $dayData['last_out'] = Carbon::parse($lastOutMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $dayData['movements'] = $attendance->movements->map(function($movement) {
                    return [
                        'time' => Carbon::parse($movement->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst($movement->movement_type),
                        'action' => ucfirst($movement->movement_action),
                        'description' => $movement->description,
                        'latitude' => $movement->latitude,
                        'longitude' => $movement->longitude
                    ];
                })->toArray();
                
                $descriptions = $attendance->movements
                    ->map(fn($m) => $m->description ? trim($m->description) : null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
                
                $dayData['description'] = !empty($descriptions) ? implode('<br>', $descriptions) : null;

                [$fullDayHr, $halfDayHr] = $this->getThresholds($shift);
                if ($isHalfDayWorking) {
                    $fullDayHr = $halfDayHr;
                    $halfDayHr = $halfDayHr / 2;
                }
                
                $slHours = ($dayData['leave_type'] === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                $hasHalfDayLeave = ($dayData['leave_type'] === 'HD');
                $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                $statusInfo = $this->determineStatus($dateStr, $dayData['hours'], $fullDayHr, $halfDayHr, $isWeeklyOff, $dayData['is_holiday'], $dayData['leave_type'], $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                $origStatus = $statusInfo['label'];
                
                $lateBy = $dayData['late_minutes'];
                $cumulativeLateMinutes += $lateBy;

                if ($shift && $lateBy > 0) {
                    $isGraceExhaustedNow = ($shift->min_per_month_late_allow - $cumulativeLateMinutes) < 0;
                    if ($isGraceExhaustedNow) {
                        $lateDaysExceeded++;
                    }
                }

                $previousGrace = 0;
                if ($shift && isset($shift->min_per_month_late_allow)) {
                    $graceBalanceVal = $shift->min_per_month_late_allow - $cumulativeLateMinutes;
                    $dayData['grace_balance'] = max(0, $graceBalanceVal) . ' min';
                    $previousGrace = $shift->min_per_month_late_allow - ($cumulativeLateMinutes - $lateBy);
                }

                if ($lateBy > 0) {
                    $dayData['late_by'] = $lateBy . ' min';
                    if ($firstInMov && $firstInMov->description) {
                        $desc = trim($firstInMov->description);
                        $prefix = "Late punch-in: ";
                        if (stripos($desc, $prefix) === 0) {
                            $dayData['late_reason'] = trim(substr($desc, strlen($prefix)));
                        } else {
                            $dayData['late_reason'] = trim($desc);
                        }
                    }
                }
                
                $isGracePunish = $shift ? ($shift->is_grace_punish ?? 0) : 0;
                $graceBounceDays = $shift ? ($shift->grace_bounce_day ?? 0) : 0;
                $exemptGraceOnOvertime = $shift ? ($shift->exempt_grace_on_overtime ?? 1) : 1;
                $statusData = $this->determineStatusAndReason($origStatus, $dayData['hours'], $fullDayHr, $halfDayHr, $lateBy, $previousGrace, $dayData['is_leave'], $hasHalfDayLeave, $isGracePunish, $graceBounceDays, $lateDaysExceeded, $exemptGraceOnOvertime);
                
                $dayData['status'] = $statusData['status'];
                $dayData['status_reason'] = $statusData['reason'];
                
                if ($dayData['is_wfh'] && str_contains(strtolower($dayData['status']), 'present')) {
                    $dayData['status'] = 'halfday';
                    $dayData['status_reason'] = 'WFH Policy (Treated as Half Day)';
                }
                
                if ($dayData['last_out'] === '-' && $dayData['hours'] > 0) {
                    $dayData['status'] = 'absent';
                    $dayData['status_reason'] = 'punchout is missing';
                }
                
                if ($dayData['is_holiday'] && $holidaysData && isset($holidaysData[$dateStr])) {
                    $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                }
            } else {
                if ($isWeeklyOff) {
                    $dayData['status'] = 'weekly off';
                } elseif (in_array($dateStr, $holidays)) {
                    $dayData['status'] = 'holiday';
                    if ($holidaysData && isset($holidaysData[$dateStr])) {
                        $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                    }
                } elseif (isset($leaves[$dateStr])) {
                    if ($leaves[$dateStr] === 'RH') {
                        $dayData['status'] = 'restricted holiday';
                    } elseif ($leaves[$dateStr] === 'SL') {
                        $dayData['status'] = 'short leave';
                    } elseif ($leaves[$dateStr] === 'LWP') {
                        $dayData['status'] = 'unpaid leave';
                    } else {
                        $dayData['status'] = 'leave';
                    }
                } else {
                    $dayData['status'] = 'absent';
                    $dayData['status_reason'] = 'No attendance recorded';
                }
                
                // If user is absent today, update grace balance to current cumulative if we have shift data
                if ($dayData['grace_balance'] === '-') {
                    if ($shift && isset($shift->min_per_month_late_allow)) {
                        $graceBalanceVal = $shift->min_per_month_late_allow - $cumulativeLateMinutes;
                        $dayData['grace_balance'] = max(0, $graceBalanceVal) . ' min';
                    }
                }
            }
            
            $dailyData[] = $dayData;
            $currentDate->addDay();
        }
        
        return $dailyData;
    }
}
