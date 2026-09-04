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

    public function determineStatusAndReason($origStatus, $dayHours, $fullDayHr, $halfDayHr, $lateBy, $isLeave, $hasHalfDayLeave)
    {
        $status = $origStatus;
        $reason = '-';
        $statusLower = strtolower($status);

        if ($lateBy > 0) {
            $reason = 'Late by ' . $lateBy . ' mins';
        }

        if (str_contains($statusLower, 'present')) {
            if ($dayHours >= $halfDayHr && $dayHours < $fullDayHr) {
                if (!$hasHalfDayLeave) {
                    $status = 'halfday';
                    $reason = "Worked less than " . intval($fullDayHr) . " hrs";
                }
            }
        } else if (str_contains($statusLower, 'absent')) {
            if ($dayHours <= 0 && !$isLeave) {
                $reason = 'No attendance recorded';
            } else {
                $reason = "Worked less than " . intval($halfDayHr) . " hrs";
            }
            $finalStatus = 'absent';
        }
        
        return [
            'status' => $status,
            'reason' => $reason
        ];
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
        $totalShortLeaves = 0;
        $presentDays = 0; 
        $halfDays = 0; 
        $totalSundays = 0;
        $totalSundaysWorked = 0;
        $totalHolidaysWorked = 0;
        $totalLateMinutes = 0;
        
        // Use the single source of truth mapper!
        $dailyBreakdown = $this->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData, $user);
        
        foreach ($dailyBreakdown as $dayData) {
            $code = $dayData['code'];
            $status = strtolower($dayData['status']);
            $isHoliday = $dayData['is_holiday'];
            $isSunday = $dayData['is_sunday'];
            
            if ($isSunday) {
                $totalSundays++;
            } elseif (!$isHoliday) {
                $totalWorkingDays++;
            }
            
            $totalHours += $dayData['hours'];
            $totalOfficeHours += $dayData['office_hours'];
            $totalFieldHours += $dayData['field_hours'];
            $totalBreakTime += $dayData['break_time'];
            $totalCycles['office'] += $dayData['cycles']['office'] ?? 0;
            $totalCycles['field'] += $dayData['cycles']['field'] ?? 0;
            $totalCycles['break'] += $dayData['cycles']['break'] ?? 0;
            $totalLateMinutes += (int) ($dayData['late_minutes'] ?? 0);
            
            if (in_array($code, ['P'])) {
                $presentDays++;
                $totalDaysWorked++;
            } elseif ($code === 'P2') {
                $halfDays++;
                $totalDaysWorked++;
            } elseif ($code === 'W/O-W' || $code === 'W/O-W<sub>wfh</sub>') {
                $totalSundaysWorked++;
                $totalDaysWorked++;
            } elseif ($code === 'H/W' || $code === 'H/W<sub>wfh</sub>') {
                $totalHolidaysWorked++;
                $totalDaysWorked++;
            } elseif ($code === 'LWP') {
                $totalUnpaidLeaves++;
            } elseif ($code === 'SL') {
                $totalShortLeaves++;
                $totalLeaves++;
            } elseif (in_array($code, ['L', 'RH', 'HD'])) {
                $totalLeaves++;
                $daysOnLeave++;
            }
        }
        
        $totalHolidays = 0;
        foreach ($holidays as $holidayDate) {
            $holidayCarbon = \Carbon\Carbon::parse($holidayDate);
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
            'total_hours' => $totalHours,
            'total_office_hours' => $totalOfficeHours,
            'total_field_hours' => $totalFieldHours,
            'total_break_time' => $totalBreakTime,
            'total_cycles' => $totalCycles,
            'total_late_minutes' => $totalLateMinutes
        ];
    }

    /**
     * Generate daily breakdown for a user and month
     */
    public function generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $user = null)
    {
        $dailyData = [];
        $attendanceByDate = $attendances->keyBy(function ($item) {
            return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
        });
        
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
                'status' => 'NA',
                'status_reason' => '-',
                'code' => 'NA',
                'class' => 'text-secondary',
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'cycles' => ['office' => 0, 'field' => 0, 'break' => 0],
                'first_in' => '-',
                'last_out' => '-',
                'late_by' => '-',
                'late_reason' => '-',
                'movements' => []
            ];
            
            if (isset($attendanceByDate[$dateStr])) {
                $attendance = $attendanceByDate[$dateStr];
                
                if (!empty($attendance->computed_status)) {
                    $dayData['hours'] = (float) $attendance->computed_hours;
                    $dayData['status'] = $attendance->computed_status;
                    $dayData['status_reason'] = $attendance->status_reason;
                } else {
                    $dayData['hours'] = (float) ($attendance->computed_hours ?? 0);
                    $dayData['status'] = 'NA';
                    $dayData['status_reason'] = '-';
                }
                
                $dayData['late_minutes'] = (int) ($attendance->late_minutes ?? 0);
                if ($dayData['late_minutes'] > 0) {
                    $dayData['late_by'] = $dayData['late_minutes'] . ' min';
                }
                
                $dayData['office_hours'] = $this->calculateTypeHours($attendance->movements, 'office');
                $dayData['field_hours'] = $this->calculateTypeHours($attendance->movements, 'field');
                $dayData['break_time'] = $this->calculateTypeHours($attendance->movements, 'break');
                $dayData['cycles'] = $this->calculateDayCycles($attendance->movements);
                $dayData['is_wfh'] = $attendance->is_wfh;
                
                $firstInMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'in')->first();
                if ($firstInMov) {
                    $dayData['first_in'] = \Carbon\Carbon::parse($firstInMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $lastOutMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if ($lastOutMov) {
                    $dayData['last_out'] = \Carbon\Carbon::parse($lastOutMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $dayData['movements'] = $attendance->movements->map(function($movement) {
                    return [
                        'time' => \Carbon\Carbon::parse($movement->time)->setTimezone('Asia/Kolkata')->format('H:i'),
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
                
                if ($dayData['late_minutes'] > 0 && $firstInMov && $firstInMov->description) {
                    $desc = trim($firstInMov->description);
                    $prefix = "Late punch-in: ";
                    if (stripos($desc, $prefix) === 0) {
                        $dayData['late_reason'] = trim(substr($desc, strlen($prefix)));
                    } else {
                        $dayData['late_reason'] = trim($desc);
                    }
                }

                if ($dayData['is_holiday'] && $holidaysData && isset($holidaysData[$dateStr])) {
                    $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                }
                
                $finalLabel = strtolower($dayData['status']);
                $isWfhWowOrHw = false;
                if ($attendance->is_wfh) {
                    if (str_contains($finalLabel, 'working')) {
                        $isWfhWowOrHw = true;
                    } elseif (str_contains($finalLabel, 'present')) {
                        $finalLabel = 'halfday';
                    }
                }

                if ($finalLabel === 'na') {
                    $dayData['code'] = 'NA';
                    $dayData['class'] = 'text-secondary';
                } elseif ($finalLabel === 'absent') {
                    $dayData['code'] = 'A';
                    $dayData['class'] = 'text-danger';
                } elseif (str_contains($finalLabel, 'halfday')) {
                    $dayData['code'] = 'P2';
                    $dayData['class'] = 'text-primary';
                } elseif (str_contains($finalLabel, 'present')) {
                    $dayData['code'] = 'P';
                    $dayData['class'] = 'text-success';
                } elseif (str_contains($finalLabel, 'weekly off working')) {
                    $dayData['code'] = $isWfhWowOrHw ? 'W/O-W<sub>wfh</sub>' : 'W/O-W';
                    $dayData['class'] = 'text-success';
                } elseif (str_contains($finalLabel, 'holiday working')) {
                    $dayData['code'] = $isWfhWowOrHw ? 'H/W<sub>wfh</sub>' : 'H/W';
                    $dayData['class'] = 'text-success';
                } else {
                    $dayData['code'] = 'P';
                    $dayData['class'] = 'text-success';
                }
                
                if (!$lastOutMov && ((float)($attendance->computed_hours ?? 0)) > 0) {
                    $dayData['code'] = 'A';
                    $dayData['class'] = 'text-danger';
                }

            } else {
                if ($isWeeklyOff) {
                    $dayData['status'] = 'weekly off';
                    $dayData['code'] = 'S';
                    $dayData['class'] = 'text-danger small';
                } elseif (in_array($dateStr, $holidays)) {
                    $dayData['status'] = 'holiday';
                    $dayData['code'] = 'H';
                    $dayData['class'] = 'text-secondary';
                    if ($holidaysData && isset($holidaysData[$dateStr])) {
                        $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                    }
                } elseif (isset($leaves[$dateStr])) {
                    if ($leaves[$dateStr] === 'RH') {
                        $dayData['status'] = 'restricted holiday';
                        $dayData['code'] = 'RH';
                        $dayData['class'] = 'text-primary';
                    } elseif ($leaves[$dateStr] === 'SL') {
                        $dayData['status'] = 'short leave';
                        $dayData['code'] = 'SL';
                        $dayData['class'] = 'text-info';
                    } elseif ($leaves[$dateStr] === 'LWP') {
                        $dayData['status'] = 'unpaid leave';
                        $dayData['code'] = 'LWP';
                        $dayData['class'] = 'text-danger';
                    } else {
                        $dayData['status'] = 'leave';
                        $dayData['code'] = 'L';
                        $dayData['class'] = 'text-warning';
                    }
                } else {
                    $dayData['status'] = 'NA';
                    $dayData['status_reason'] = '-';
                    $dayData['code'] = 'NA';
                    $dayData['class'] = 'text-secondary';
                }
            }
            
            $dailyData[] = $dayData;
            $currentDate->addDay();
        }
        
        return $dailyData;
    }

    public function computeAndSaveDailyStatus(\App\Models\Attendance $attendance)
    {
        if ($attendance->is_overridden) {
            return $attendance;
        }

        $dateStr = \Carbon\Carbon::parse($attendance->date)->format('Y-m-d');
        $user = \App\Models\User::with('employee')->find($attendance->user_id);
        $shift = $user && $user->employee ? $user->employee->getShiftForDate($dateStr) : null;
        
        $hours = $this->calculateTotalHours($attendance->movements, $shift, $dateStr);
        
        $leave = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->first();
            
        $leaveType = $leave ? ($leave->is_half_day ? 'HD' : ($leave->is_sl ? 'SL' : ($leave->is_rh ? 'RH' : 'L'))) : null;
        $hasHalfDayLeave = ($leaveType === 'HD');
        $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
        
        $dayName = \Carbon\Carbon::parse($dateStr)->format('l');
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
        
        $isHoliday = \App\Models\Holiday::whereDate('holiday_date', $dateStr)->exists();
        
        [$fullDayHr, $halfDayHr] = $this->getThresholds($shift);
        if ($isHalfDayWorking) {
            $fullDayHr = $halfDayHr;
            $halfDayHr = $halfDayHr / 2;
        }
        
        $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
        
        $statusInfo = $this->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
        $origStatus = $statusInfo['label'];
        
        $lateBy = (int) ($attendance->late_minutes ?? 0);
        
        $statusData = $this->determineStatusAndReason($origStatus, $hours, $fullDayHr, $halfDayHr, $lateBy, ($leave !== null), $hasHalfDayLeave);
        
        $finalStatus = strtolower($statusData['status']);
        $statusReason = $statusData['reason'];
        
        if ($attendance->is_wfh && str_contains($finalStatus, 'present')) {
            $finalStatus = 'halfday';
            $statusReason = 'WFH Policy (Treated as Half Day)';
        }
        
        $lastOutMov = $attendance->movements()->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->latest('id')->first();
        if (!$lastOutMov && $hours > 0) {
            $finalStatus = 'absent';
            $statusReason = 'punchout is missing';
        }
        
        $attendance->update([
            'computed_status' => $finalStatus,
            'computed_hours' => round($hours, 2),
            'is_late' => $lateBy > 0,
            'status_reason' => $statusReason
        ]);
        
        return $attendance;
    }
}
