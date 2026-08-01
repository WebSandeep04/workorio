<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\EmployeeLocation;
use Carbon\Carbon;

class TrackingReportApiController extends Controller
{
    /**
     * Fetch Users available for location tracking reports
     */
    public function fetchReportUsers()
    {
        try {
            $users = User::where('role_id', '!=', 1)
                ->whereHas('employee', function ($query) {
                    $query->where('status', 'active')
                          ->where('is_tracking', 1);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'email']);

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tracking list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate physical distance between coordinates (Meters)
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Accumulate coordinates list into KM scale with anti-jitter filter
     */
    private function calculateTotalDistanceKm($locations)
    {
        if ($locations->count() <= 1) {
            return 0.00;
        }

        $totalDistanceMeters = 0;
        $prev = null;

        foreach ($locations as $loc) {
            $lat = (float)$loc->latitude;
            $lng = (float)$loc->longitude;
            if (!$lat || !$lng) continue;

            if ($prev) {
                $prevLat = (float)$prev->latitude;
                $prevLng = (float)$prev->longitude;
                
                $dist = $this->haversineGreatCircleDistance($prevLat, $prevLng, $lat, $lng);
                // Ensure Carbon formats exist to handle parsed objects correctly
                $trackedAt = is_string($loc->tracked_at) ? Carbon::parse($loc->tracked_at) : $loc->tracked_at;
                $prevTrackedAt = is_string($prev->tracked_at) ? Carbon::parse($prev->tracked_at) : $prev->tracked_at;
                $timeDiff = $trackedAt->diffInSeconds($prevTrackedAt);

                // Speed/Jitter elimination filters found in TrackingController
                if ($dist < 50 && $timeDiff < 300) {
                    continue;
                }

                if ($timeDiff > 0) {
                    $speed = $dist / $timeDiff;
                    if ($speed > 25 && $dist > 150) {
                        continue;
                    }
                }

                $totalDistanceMeters += $dist;
            }

            $prev = $loc;
        }

        return round($totalDistanceMeters / 1000, 2);
    }

    /**
     * Tab 1: User-Wise Monthly Tracking Report
     */
    public function getUserWiseReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        try {
            $userId = $request->user_id;
            $month = $request->month;

            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            $user = User::with(['employee.shiftHistory.shift'])->find($userId);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Target profile missing.'], 404);
            }

            

            // Load Attendances
            $attendances = Attendance::with(['movements' => function ($query) {
                    $query->orderBy('time');
                }])
                ->where('user_id', $userId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });

            // Load Holidays
            $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(function ($item) {
                    return $item->holiday_date->format('Y-m-d');
                });

            // Load Leaves
            $leaveRequests = LeaveRequest::where('user_id', $userId)
                ->where('status', 'approved')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhere(function ($q) use ($startDate, $endDate) {
                              $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                                ->where('end_date', '>=', $endDate->format('Y-m-d'));
                          });
                })->get();

            $leavesDetails = [];
            foreach ($leaveRequests as $req) {
                $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
                foreach ($period as $dt) {
                    if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                        $d = $dt->format('Y-m-d');
                        if (!isset($leavesDetails[$d])) {
                            if ($req->is_rh) {
                                $leavesDetails[$d] = 'RH';
                            } elseif ($req->is_sl) {
                                $leavesDetails[$d] = 'SL';
                            } elseif ($req->is_half_day) {
                                $leavesDetails[$d] = 'HD';
                            } else {
                                $leavesDetails[$d] = 'L';
                            }
                        }
                    }
                }
            }

            // Fetch Locations aggregated by day
            $locationsByDate = EmployeeLocation::where('employee_id', $user->employee_id)
                ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->orderBy('tracked_at', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    // Ensure proper carbon formatting
                    $tracked = is_string($item->tracked_at) ? Carbon::parse($item->tracked_at) : $item->tracked_at;
                    return $tracked->format('Y-m-d');
                });

            $reportService = app(\App\Services\AttendanceReportService::class);
            $dailyData = [];
            $totalDistanceKm = 0.0;
            $presentDaysCount = 0;
            $absentDaysCount = 0;

            $curr = $startDate->copy();
            while ($curr->lte($endDate)) {
                $dateStr = $curr->format('Y-m-d');
                $dayName = $curr->format('l');

                $attendance = $attendances->get($dateStr);
                $leaveType = $leavesDetails[$dateStr] ?? null;
                $holiday = $holidaysData->get($dateStr);

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

                $statusLabel = 'absent';
                $hours = 0.0;

                if ($attendance) {
                    $hours = $reportService->calculateTotalHours($attendance->movements, $shift, $dateStr);
                    [$fullDayHr, $halfDayHr] = $reportService->getThresholds($shift);
                    if ($isHalfDayWorking) {
                        $fullDayHr = $halfDayHr;
                        $halfDayHr = $halfDayHr / 2;
                    }

                    $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                    $hasHalfDayLeave = ($leaveType === 'HD');
                    $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                    $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                    $statusLabel = $statusInfo['label'];
                } elseif ($isWeeklyOff) {
                    $statusLabel = 'weekly off';
                } elseif ($holiday) {
                    $statusLabel = 'holiday';
                } elseif ($leaveType) {
                    if ($leaveType === 'RH') {
                        $statusLabel = 'restricted holiday';
                    } elseif ($leaveType === 'SL') {
                        $statusLabel = 'short leave';
                    } else {
                        $statusLabel = 'leave';
                    }
                }

                $dayLocations = $locationsByDate->get($dateStr, collect());
                $kmTravelled = 0.0;
                if ($dayLocations->count() > 1) {
                    $kmTravelled = $this->calculateTotalDistanceKm($dayLocations);
                }

                $totalDistanceKm += $kmTravelled;
                
                $isPresent = in_array($statusLabel, ['present', 'present with SL', 'present with HD', 'present (partial leave)', 'halfday', 'sunday working', 'holiday working', 'S/W', 'H/W']);
                if ($isPresent) {
                    $presentDaysCount++;
                } elseif ($statusLabel === 'absent') {
                    $absentDaysCount++;
                }

                $dailyData[] = [
                    'date' => $dateStr,
                    'display_date' => $curr->format('M j, Y'),
                    'day_name' => $curr->format('D'),
                    'status' => $statusLabel,
                    'is_present' => $isPresent,
                    'km_travelled' => $kmTravelled,
                    'locations_count' => $dayLocations->count(),
                    'hours' => round($hours, 2),
                ];

                $curr->addDay();
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'month' => [
                    'display' => $startDate->format('F Y'),
                ],
                'summary' => [
                    'total_distance_km' => round($totalDistanceKm, 2),
                    'total_present' => $presentDaysCount,
                    'total_absent' => $absentDaysCount,
                    'total_days' => count($dailyData),
                ],
                'data' => $dailyData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Audit failure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tab 2: Monthly Staff Matrix (Collated)
     */
    public function getMonthlySummaryReport(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        try {
            $month = $request->month;

            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            // Build Days Index
            $dates = [];
            $curr = $startDate->copy();
            while ($curr->lte($endDate)) {
                $dates[] = [
                    'date' => $curr->format('Y-m-d'),
                    'day' => $curr->format('d'),
                    'day_name' => $curr->format('D'),
                    'is_sunday' => $curr->dayOfWeek === Carbon::SUNDAY
                ];
                $curr->addDay();
            }

            $users = User::with(['employee.shiftHistory.shift'])
                ->where('role_id', '!=', 1)
                ->whereHas('employee', function ($query) {
                    $query->where('status', 'active')
                          ->where('is_tracking', 1);
                })
                ->orderBy('name')
                ->get();

            $userIds = $users->pluck('id')->toArray();
            $employeeIds = $users->pluck('employee_id')->filter()->toArray();

            // Attendances Matrix
            $allAttendances = Attendance::with(['movements' => function ($query) {
                    $query->orderBy('time');
                }])
                ->whereIn('user_id', $userIds)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->groupBy('user_id');

            // Holidays Index
            $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(function ($item) {
                    return $item->holiday_date->format('Y-m-d');
                });
            $holidays = $holidaysData->keys()->toArray();

            // Leaves Registry
            $allLeavesRaw = LeaveRequest::where('status', 'approved')
                ->whereIn('user_id', $userIds)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                          ->orWhere(function ($q) use ($startDate, $endDate) {
                              $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                                ->where('end_date', '>=', $endDate->format('Y-m-d'));
                          });
                })->get();

            $leavesData = [];
            foreach ($allLeavesRaw as $req) {
                $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
                foreach ($period as $dt) {
                    $d = $dt->format('Y-m-d');
                    if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                        if (!isset($leavesData[$req->user_id])) {
                            $leavesData[$req->user_id] = collect();
                        }
                        $leavesData[$req->user_id]->push((object)[
                            'date' => Carbon::parse($d),
                            'is_rh' => $req->is_rh,
                            'is_sl' => $req->is_sl,
                            'is_half_day' => $req->is_half_day
                        ]);
                    }
                }
            }
            $allLeaves = collect($leavesData);

            // Fetch massive Location block bounded to date ranges
            $allLocations = EmployeeLocation::whereIn('employee_id', $employeeIds)
                ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->orderBy('tracked_at', 'asc')
                ->get()
                ->groupBy(function ($item) {
                    $tracked = is_string($item->tracked_at) ? Carbon::parse($item->tracked_at) : $item->tracked_at;
                    return $item->employee_id . '_' . $tracked->format('Y-m-d');
                });

            $reportService = app(\App\Services\AttendanceReportService::class);
            $reportData = [];

            foreach ($users as $user) {
                $userAttendances = $allAttendances->get($user->id, collect())->keyBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });

                $rawLeaves = $allLeaves->get($user->id, collect());
                $userLeavesDetails = [];
                foreach ($rawLeaves as $l) {
                    $d = $l->date->format('Y-m-d');
                    if (!isset($userLeavesDetails[$d])) {
                        if ($l->is_rh) {
                            $userLeavesDetails[$d] = 'RH';
                        } elseif ($l->is_sl) {
                            $userLeavesDetails[$d] = 'SL';
                        } elseif ($l->is_half_day) {
                            $userLeavesDetails[$d] = 'HD';
                        } else {
                            $userLeavesDetails[$d] = 'L';
                        }
                    }
                }

                $dailyStatuses = [];
                $totalDistanceKm = 0.0;
                $presentDaysCount = 0;
                $absentDaysCount = 0;

                foreach ($dates as $d) {
                    $dateStr = $d['date'];
                    $statusCode = '-';
                    $statusClass = 'text-muted';
                    $kmTravelled = 0.0;

                    
                    $dayName = Carbon::parse($dateStr)->format('l');
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

                    $statusLabel = 'absent';
                    if (isset($userAttendances[$dateStr])) {
                        $att = $userAttendances[$dateStr];
                        $hours = $reportService->calculateTotalHours($att->movements, $shift, $dateStr);
                        [$fullDayHr, $halfDayHr] = $reportService->getThresholds($shift);
                        if ($isHalfDayWorking) {
                            $fullDayHr = $halfDayHr;
                            $halfDayHr = $halfDayHr / 2;
                        }

                        $leaveType = $userLeavesDetails[$dateStr] ?? null;
                        $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                        $hasHalfDayLeave = ($leaveType === 'HD');
                        $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                        $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                        
                        $statusCode = $statusInfo['code'];
                        $statusClass = $statusInfo['class'];
                        $statusLabel = $statusInfo['label'];
                    } elseif (in_array($dateStr, $holidays)) {
                        $statusCode = 'H';
                        $statusClass = 'text-secondary';
                        $statusLabel = 'holiday';
                    } elseif ($isWeeklyOff) {
                        $statusCode = 'S';
                        $statusClass = 'text-danger small';
                        $statusLabel = 'weekly off';
                    } elseif (isset($userLeavesDetails[$dateStr])) {
                        $lType = $userLeavesDetails[$dateStr];
                        $statusCode = $lType;
                        $statusClass = $lType === 'RH' ? 'text-primary' : ($lType === 'SL' ? 'text-info' : 'text-warning');
                        $statusLabel = $lType === 'RH' ? 'restricted holiday' : ($lType === 'SL' ? 'short leave' : 'leave');
                    } else {
                        $statusCode = 'A';
                        $statusClass = 'text-danger';
                        $statusLabel = 'absent';
                    }

                    // Find mapped positions
                    $dayLocations = $allLocations->get($user->employee_id . '_' . $dateStr, collect());
                    if ($dayLocations->count() > 1) {
                        $kmTravelled = $this->calculateTotalDistanceKm($dayLocations);
                    }

                    $totalDistanceKm += $kmTravelled;
                    $isPresent = in_array($statusLabel, ['present', 'present with SL', 'present with HD', 'present (partial leave)', 'halfday', 'sunday working', 'holiday working', 'S/W', 'H/W']);
                    
                    if ($isPresent) {
                        $presentDaysCount++;
                    } elseif ($statusLabel === 'absent') {
                        $absentDaysCount++;
                    }

                    $dailyStatuses[] = [
                        'date' => $dateStr,
                        'code' => $statusCode,
                        'status' => $statusLabel,
                        'km_travelled' => $kmTravelled,
                        'is_present' => $isPresent,
                    ];
                }

                $reportData[] = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                    'summary' => [
                        'total_distance_km' => round($totalDistanceKm, 2),
                        'total_present' => $presentDaysCount,
                        'total_absent' => $absentDaysCount,
                    ],
                    'daily_statuses' => $dailyStatuses
                ];
            }

            return response()->json([
                'success' => true,
                'month' => [
                    'display' => $startDate->format('F Y'),
                    'dates' => $dates
                ],
                'data' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Monthly build failure: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tab 3: Daily Snapshot Breakdown for All Tracking Staff
     */
    public function getDateWiseReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            $dateStr = $request->date;
            $date = Carbon::parse($dateStr);

            $users = User::with(['employee.shiftHistory.shift'])
                ->where('role_id', '!=', 1)
                ->whereHas('employee', function ($query) {
                    $query->where('status', 'active')
                          ->where('is_tracking', 1);
                })
                ->orderBy('name')
                ->get();

            $userIds = $users->pluck('id')->toArray();
            $employeeIds = $users->pluck('employee_id')->filter()->toArray();

            // Target Day Attendances
            $attendances = Attendance::with(['movements' => function ($query) {
                    $query->orderBy('time');
                }])
                ->whereIn('user_id', $userIds)
                ->whereDate('date', $dateStr)
                ->get()
                ->keyBy('user_id');

            $holiday = Holiday::whereDate('holiday_date', $dateStr)->first();

            // Target Day Leaves
            $leavesData = LeaveRequest::whereIn('user_id', $userIds)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $dateStr)
                ->whereDate('end_date', '>=', $dateStr)
                ->get()
                ->keyBy(function ($item) {
                    return $item->user_id;
                });

            // Target Day Locations
            $locations = EmployeeLocation::whereIn('employee_id', $employeeIds)
                ->whereDate('tracked_at', $dateStr)
                ->orderBy('tracked_at', 'asc')
                ->get()
                ->groupBy('employee_id');

            $reportService = app(\App\Services\AttendanceReportService::class);
            $reportData = [];
            $totalDistanceSum = 0.0;
            $presentCount = 0;
            $absentCount = 0;

            foreach ($users as $user) {
                $attendance = $attendances->get($user->id);
                $leave = $leavesData->get($user->id);
                
                $leaveType = null;
                if ($leave) {
                    if ($leave->is_rh) $leaveType = 'RH';
                    elseif ($leave->is_sl) $leaveType = 'SL';
                    elseif ($leave->is_half_day) $leaveType = 'HD';
                    else $leaveType = 'L';
                }

                
                $dayName = $date->format('l');
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

                $statusLabel = 'absent';
                $hours = 0.0;
                if ($attendance) {
                    $hours = $reportService->calculateTotalHours($attendance->movements, $shift, $dateStr);
                    [$fullDayHr, $halfDayHr] = $reportService->getThresholds($shift);
                    if ($isHalfDayWorking) {
                        $fullDayHr = $halfDayHr;
                        $halfDayHr = $halfDayHr / 2;
                    }

                    $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                    $hasHalfDayLeave = ($leaveType === 'HD');
                    $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                    $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                    $statusLabel = $statusInfo['label'];
                } elseif ($isWeeklyOff) {
                    $statusLabel = 'weekly off';
                } elseif ($holiday) {
                    $statusLabel = 'holiday';
                } elseif ($leaveType) {
                    if ($leaveType === 'RH') {
                        $statusLabel = 'restricted holiday';
                    } elseif ($leaveType === 'SL') {
                        $statusLabel = 'short leave';
                    } else {
                        $statusLabel = 'leave';
                    }
                }

                $dayLocations = $locations->get($user->employee_id, collect());
                $kmTravelled = 0.0;
                if ($dayLocations->count() > 1) {
                    $kmTravelled = $this->calculateTotalDistanceKm($dayLocations);
                }

                $totalDistanceSum += $kmTravelled;
                $isPresent = in_array($statusLabel, ['present', 'present with SL', 'present with HD', 'present (partial leave)', 'halfday', 'sunday working', 'holiday working', 'S/W', 'H/W']);
                
                if ($isPresent) {
                    $presentCount++;
                } elseif ($statusLabel === 'absent') {
                    $absentCount++;
                }

                $reportData[] = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                    'status' => $statusLabel,
                    'km_travelled' => $kmTravelled,
                    'locations_count' => $dayLocations->count(),
                    'hours' => round($hours, 2),
                ];
            }

            return response()->json([
                'success' => true,
                'date' => [
                    'display' => $date->format('M j, Y'),
                    'date' => $dateStr,
                ],
                'summary' => [
                    'total_users' => count($users),
                    'total_distance_km' => round($totalDistanceSum, 2),
                    'present' => $presentCount,
                    'absent' => $absentCount,
                ],
                'data' => $reportData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Daily audit failure: ' . $e->getMessage()
            ], 500);
        }
    }
}
