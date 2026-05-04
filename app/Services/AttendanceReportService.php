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
                // If it's a past date and no punch out, we can't assume they are still working
                // However, the original code used Carbon::now().
                $lastPunchOut = Carbon::now()->setTimezone('Asia/Kolkata');
            }
        }

        if ($shift && $date) {
            $shiftDate = Carbon::parse($date)->format('Y-m-d');
            
            if ($shift->start_time) {
                $shiftStartTime = Carbon::parse($shift->start_time)->format('H:i:s');
                $startLimit = Carbon::parse($shiftDate . ' ' . $shiftStartTime, 'UTC')->setTimezone('Asia/Kolkata');
                if ($firstPunchIn->lt($startLimit)) {
                    $firstPunchIn = $startLimit;
                }
            }
            
            if ($shift->end_time) {
                $shiftEndTime = Carbon::parse($shift->end_time)->format('H:i:s');
                $endLimit = Carbon::parse($shiftDate . ' ' . $shiftEndTime, 'UTC')->setTimezone('Asia/Kolkata');
                
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
        if ($inTime) {
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

    /**
     * Determine status label and class
     */
    public function determineStatus($hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $leaveType = null)
    {
        if ($isWeeklyOff) {
            return [
                'code' => 'S/W',
                'label' => 'weekly off working',
                'class' => 'text-info'
            ];
        }

        if ($isHoliday) {
            return [
                'code' => 'H/W',
                'label' => 'holiday working',
                'class' => 'text-info'
            ];
        }

        if ($leaveType === 'SL') {
            if ($hours >= $fullDayHr) {
                return [
                    'code' => 'P (SL)',
                    'label' => 'present',
                    'class' => 'text-success'
                ];
            } else {
                return [
                    'code' => 'SL',
                    'label' => 'short leave',
                    'class' => 'text-info'
                ];
            }
        }

        if ($hours >= $fullDayHr) {
            return [
                'code' => 'P',
                'label' => 'present',
                'class' => 'text-success'
            ];
        } elseif ($hours >= $halfDayHr) {
            return [
                'code' => 'P2',
                'label' => 'halfday',
                'class' => 'text-warning'
            ];
        } else {
            return [
                'code' => 'A',
                'label' => 'absent by less hr',
                'class' => 'text-danger'
            ];
        }
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
        
        $shift = $user ? ($user->employee->shiftRelation ?? null) : null;
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayName = $currentDate->format('l');
            $isWeeklyOff = false;
            
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
        
        foreach ($attendances as $attendance) {
            $attendanceDate = Carbon::parse($attendance->date);
            $dateStr = $attendanceDate->format('Y-m-d');
            
            $dayName = $attendanceDate->format('l');
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

            if ($isWeeklyOff) {
                $totalSundaysWorked++;
            } elseif (in_array($dateStr, $holidays)) {
                $totalHolidaysWorked++;
            }
            
            if (!$isWeeklyOff && !in_array($dateStr, $holidays)) {
                $dayHours = $this->calculateTotalHours($attendance->movements, $shift, $attendance->date);
                $totalHours += $dayHours;
                
                [$fullDayHr, $halfDayHr] = $this->getThresholds($shift);

                if ($isHalfDayWorking) {
                    $fullDayHr = $halfDayHr;
                    $halfDayHr = $halfDayHr / 2;
                }

                if ($dayHours >= $halfDayHr) {
                    $attendanceDates[] = $dateStr;
                    $totalDaysWorked++;
                }

                if ($dayHours >= $fullDayHr) {
                    $presentDays++;
                } elseif ($dayHours >= $halfDayHr) {
                    $halfDays++;
                }

                [$origFullDayHr, $origHalfDayHr] = $this->getThresholds($shift);
                if ($dayHours >= $origFullDayHr) {
                    $totalMore8_30++;
                } elseif ($dayHours >= $halfDayHr) {
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
                        $shiftStart = Carbon::parse($shiftDate . ' ' . $shiftTime, 'UTC')->setTimezone('Asia/Kolkata');
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
                
                $totalLateMinutes += (int) abs($attendance->late_minutes ?? 0);
            }
        }
        
        $uniqueLeaves = array_unique($leaves);
        foreach ($uniqueLeaves as $leaveDate) {
            $dateStr = is_string($leaveDate) ? $leaveDate : Carbon::parse($leaveDate)->format('Y-m-d');
            if (!in_array($dateStr, $holidays) && !in_array($dateStr, $holidaysWithAttendance)) {
                $totalLeaves++;
            }
        }
        
        foreach ($uniqueLeaves as $leaveDate) {
            $dateStr = is_string($leaveDate) ? $leaveDate : Carbon::parse($leaveDate)->format('Y-m-d');
            $leaveCarbon = Carbon::parse($dateStr);
            $dayName = $leaveCarbon->format('l');
            $isWeeklyOff = false;
            if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
            }

            if (!$isWeeklyOff 
                && !in_array($dateStr, $holidays) 
                && !in_array($dateStr, $attendanceDates)
                && !in_array($dateStr, $holidaysWithAttendance)) {
                $leaveDates[] = $dateStr;
                $daysOnLeave++;
            }
        }
        
        $totalHolidays = 0;
        foreach ($holidays as $holidayDate) {
            $holidayCarbon = Carbon::parse($holidayDate);
            $dayName = $holidayCarbon->format('l');
            $isWeeklyOff = false;
            if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
            }
            if (!$isWeeklyOff) {
                $totalHolidays++;
            }
        }
        
        $daysAbsent = max(0, $totalWorkingDays - $totalDaysWorked - $daysOnLeave);
        
        $attendancePercentage = $totalWorkingDays > 0 
            ? round((($presentDays + $halfDays) / $totalWorkingDays) * 100, 1) 
            : 0;
        
        return [
            'total_working_days' => $totalWorkingDays,
            'days_worked' => $totalDaysWorked,
            'days_absent' => $daysAbsent,
            'days_on_leave' => $totalLeaves, 
            'attendance_percentage' => min(100, $attendancePercentage),
            'total_present_combined' => $presentDays + $halfDays + $totalHolidaysWorked + $totalSundaysWorked,
            'total_present' => $presentDays,
            'total_halfday' => $halfDays,
            'total_sundays' => $totalSundays,
            'total_sundays_worked' => $totalSundaysWorked,
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
    public function generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $shift = null)
    {
        $dailyData = [];
        $attendanceByDate = $attendances->keyBy(function ($attendance) {
            return Carbon::parse($attendance->date)->format('Y-m-d');
        });
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayName = $currentDate->format('l');
            $displayDate = $currentDate->format('M j, Y');
            
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
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'cycles' => ['office' => 0, 'field' => 0, 'break' => 0],
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
                
                $dayData['movements'] = $attendance->movements->map(function($movement) {
                    return [
                        'time' => Carbon::parse($movement->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst($movement->movement_type),
                        'action' => ucfirst($movement->movement_action),
                        'description' => $movement->description
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
                
                $statusInfo = $this->determineStatus($dayData['hours'], $fullDayHr, $halfDayHr, $isWeeklyOff, $dayData['is_holiday'], $dayData['leave_type']);
                $dayData['status'] = $statusInfo['label'];
                
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
                    } else {
                        $dayData['status'] = 'leave';
                    }
                } else {
                    $dayData['status'] = 'absent';
                }
            }
            
            $dailyData[] = $dayData;
            $currentDate->addDay();
        }
        
        return $dailyData;
    }
}
