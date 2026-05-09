<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{

    public function index()
    {
        $employees = \App\Models\Employee::all()->where('status', 'active');
        return view('tracking.index', compact('employees'));
    }

    public function fetchLocations(Request $request)
    {
        $query = \App\Models\EmployeeLocation::with('employee');

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('tracked_at', $date);
        } else {
            // Default to today if no date is provided
            $query->whereDate('tracked_at', now());
        }

        $locations = $query->orderBy('tracked_at', 'asc')->get();

        // Calculate statuses for employees to determine marker color
        $employeeIds = $locations->pluck('employee_id')->unique()->values();
        $dateToCheck = $request->has('date') ? $request->date : \Carbon\Carbon::now()->format('Y-m-d');
        
        // Optimize: Only fetch necessary fields and relationships
        $users = \App\Models\User::whereIn('employee_id', $employeeIds)
            ->with(['attendances' => function($q) use ($dateToCheck) {
                $q->select('id', 'user_id', 'date')
                  ->whereDate('date', $dateToCheck)
                  ->with(['movements' => function($m) {
                      $m->select('id', 'attendance_id', 'movement_type', 'movement_action', 'time')
                        ->orderBy('time');
                  }]);
            }])
            ->select('id', 'employee_id')
            ->get()
            ->keyBy('employee_id');

        $employeeDetails = [];

        foreach ($employeeIds as $empId) {
             $user = $users->get($empId);
             $color = '#dc3545'; // Red (Default/Punched Out)
             $details = [
                 'office_in' => '-',
                 'office_out' => '-',
                 'field_in' => '-',
                 'field_out' => '-',
                 'break' => '-'
             ];
             
             if ($user && $user->attendances->isNotEmpty()) {
                 $attendance = $user->attendances->first();
                 $movements = $attendance->movements; // Already loaded sorted
                 
                 // Check Break
                 $breakStart = $movements->where('movement_type', 'break')->where('movement_action', 'start')->last();
                 $breakEnd = $movements->where('movement_type', 'break')->where('movement_action', 'end')->last();
                 $isOnBreak = $breakStart && (!$breakEnd || $breakEnd->time < $breakStart->time);
                 
                 if ($isOnBreak) {
                     $color = '#ffc107'; // Yellow
                     $details['break'] = 'On Break (' . \Carbon\Carbon::parse($breakStart->time)->format('h:i A') . ')';
                 } else {
                     // Check In (Office or Field)
                     $officeIn = $movements->where('movement_type', 'office')->where('movement_action', 'in')->last();
                     $officeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                     $isOffice = $officeIn && (!$officeOut || $officeOut->time < $officeIn->time);

                     $fieldIn = $movements->where('movement_type', 'field')->where('movement_action', 'in')->last();
                     $fieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                     $isField = $fieldIn && (!$fieldOut || $fieldOut->time < $fieldIn->time);
                     
                     if ($isOffice || $isField) {
                         $color = '#28a745'; // Green
                     }
                     
                     // Determine Primary Status
                     $statusText = 'Not Started';
                     
                     // Check if currently on break
                     if ($isOnBreak) {
                         $statusText = 'On Break';
                     } else {
                         // Check active sessions
                         if ($isOffice) {
                             $statusText = 'Punched In'; // Office In
                         } elseif ($isField) {
                             $statusText = 'Field In';
                         } else {
                             // Not active in office or field. Check what was the last action.
                             // We need to look at the very last valid movement to decide if it was an "Out"
                             $lastOfficeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                             $lastFieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                             
                             $lastOutTime = null;
                             $outType = null;
                             
                             if ($lastOfficeOut) {
                                 $lastOutTime = $lastOfficeOut->time;
                                 $outType = 'office';
                             }
                             
                             if ($lastFieldOut) {
                                 if (!$lastOutTime || $lastFieldOut->time > $lastOutTime) {
                                     $lastOutTime = $lastFieldOut->time;
                                     $outType = 'field';
                                 }
                             }
                             
                             if ($outType === 'office') {
                                 $statusText = 'Punched Out';
                             } elseif ($outType === 'field') {
                                 $statusText = 'Field Out';
                             }
                         }
                     }
                     
                     $details['current_status'] = $statusText;
                 }
             }
             $employeeDetails[$empId] = [
                 'color' => $color,
                 'details' => $details
             ];
        }

        return response()->json([
            'success' => true,
            'data' => $locations,
            'employee_details' => $employeeDetails
        ]);
    }

    public function reportView()
    {
        $users = \App\Models\User::where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active')
                      ->where('is_tracking', 1);
            })
            ->orderBy('name')
            ->get();

        return view('tracking.report', compact('users'));
    }

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

    private function calculateTotalDistanceKm($locations)
    {
        if ($locations->count() <= 1) {
            return 0.00;
        }

        $totalDistanceMeters = 0;
        $prev = null;

        foreach ($locations as $loc) {
            $lat = (float) $loc->latitude;
            $lng = (float) $loc->longitude;
            if (!$lat || !$lng) continue;

            if ($prev) {
                $prevLat = (float) $prev->latitude;
                $prevLng = (float) $prev->longitude;
                
                $dist = $this->haversineGreatCircleDistance($prevLat, $prevLng, $lat, $lng);
                $timeDiff = $loc->tracked_at->diffInSeconds($prev->tracked_at);

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

    public function getReportData(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        $userId = $request->user_id;
        $month = $request->month;

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $user = \App\Models\User::with(['employee.shiftRelation'])->find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $shift = $user->employee->shiftRelation ?? null;

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $leaveRequests = \App\Models\LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
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

        $locationsByDate = \App\Models\EmployeeLocation::where('employee_id', $user->employee_id)
            ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderBy('tracked_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->tracked_at->format('Y-m-d');
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
                $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours);
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
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_distance_km' => round($totalDistanceKm, 2),
                'total_present' => $presentDaysCount,
                'total_absent' => $absentDaysCount,
                'total_days' => count($dailyData),
            ],
            'daily_data' => $dailyData
        ]);
    }

    public function getMonthlyReportData(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $month = $request->month;

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $dates = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $dates[] = [
                'date' => $curr->format('Y-m-d'),
                'day' => $curr->format('d'),
                'day_name' => $curr->format('D'),
                'is_sunday' => $curr->dayOfWeek === \Carbon\Carbon::SUNDAY
            ];
            $curr->addDay();
        }

        $users = \App\Models\User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active')
                      ->where('is_tracking', 1);
            })
            ->orderBy('name')
            ->get();

        $userIds = $users->pluck('id')->toArray();
        $employeeIds = $users->pluck('employee_id')->filter()->toArray();

        $allAttendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $allLeavesRaw = \App\Models\LeaveRequest::where('status', 'approved')
            ->whereIn('user_id', $userIds)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
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
                        'date' => \Carbon\Carbon::parse($d),
                        'is_rh' => $req->is_rh,
                        'is_sl' => $req->is_sl,
                        'is_half_day' => $req->is_half_day
                    ]);
                }
            }
        }
        $allLeaves = collect($leavesData);

        $allLocations = \App\Models\EmployeeLocation::whereIn('employee_id', $employeeIds)
            ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderBy('tracked_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->employee_id . '_' . $item->tracked_at->format('Y-m-d');
            });

        $reportService = app(\App\Services\AttendanceReportService::class);
        $reportData = [];

        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect())->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
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

                $shift = $user->employee->shiftRelation ?? null;
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
                    $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours);
                    
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
                    'class' => $statusClass,
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
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'dates' => $dates
            ],
            'data' => $reportData
        ]);
    }

    public function getDateReportData(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $dateStr = $request->date;
        $date = \Carbon\Carbon::parse($dateStr);

        $users = \App\Models\User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active')
                      ->where('is_tracking', 1);
            })
            ->orderBy('name')
            ->get();

        $userIds = $users->pluck('id')->toArray();
        $employeeIds = $users->pluck('employee_id')->filter()->toArray();

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereIn('user_id', $userIds)
            ->whereDate('date', $dateStr)
            ->get()
            ->keyBy('user_id');

        $holiday = \App\Models\Holiday::whereDate('holiday_date', $dateStr)->first();

        $leavesData = \App\Models\LeaveRequest::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->get()
            ->keyBy(function($item) {
                return $item->user_id;
            });

        $locations = \App\Models\EmployeeLocation::whereIn('employee_id', $employeeIds)
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

            $shift = $user->employee->shiftRelation ?? null;
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
                $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours);
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
            'date' => [
                'display' => $date->format('M j, Y'),
                'date' => $dateStr,
                'day_name' => $date->format('D'),
            ],
            'summary' => [
                'total_users' => count($users),
                'total_distance_km' => round($totalDistanceSum, 2),
                'present' => $presentCount,
                'absent' => $absentCount,
            ],
            'data' => $reportData
        ]);
    }

    public function exportUserReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        $userId = $request->user_id;
        $month = $request->month;

        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $user = \App\Models\User::with(['employee.shiftRelation'])->find($userId);
        $shift = $user->employee->shiftRelation ?? null;

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $leaveRequests = \App\Models\LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
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

        $locationsByDate = \App\Models\EmployeeLocation::where('employee_id', $user->employee_id)
            ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderBy('tracked_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->tracked_at->format('Y-m-d');
            });

        $reportService = app(\App\Services\AttendanceReportService::class);
        
        $filename = "tracking_report_{$user->name}_{$month}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($startDate, $endDate, $attendances, $leavesDetails, $holidaysData, $holidays, $shift, $locationsByDate, $reportService) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Date', 'Day', 'Status', 'Total Hours', 'KM Travelled', 'Logged Points']);

            $curr = $startDate->copy();
            $totalDistanceKm = 0.0;
            $travelDaysCount = 0;

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

                    $leaveType = $leavesDetails[$dateStr] ?? null;
                    $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                    $hasHalfDayLeave = ($leaveType === 'HD');
                    $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours);
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
                if ($kmTravelled > 0) {
                    $travelDaysCount++;
                }

                fputcsv($file, [
                    $curr->format('Y-m-d'),
                    $dayName,
                    ucwords($statusLabel),
                    round($hours, 2),
                    $kmTravelled > 0 ? "{$kmTravelled} km" : "0.00 km",
                    $dayLocations->count()
                ]);

                $curr->addDay();
            }

            fputcsv($file, []);
            fputcsv($file, ['Summary Metrics']);
            fputcsv($file, ['Total Distance Travelled', round($totalDistanceKm, 2) . ' km']);
            fputcsv($file, ['Total Days Logged with Travel', $travelDaysCount]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMonthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        $month = $request->month;
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $users = \App\Models\User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active')
                      ->where('is_tracking', 1);
            })
            ->orderBy('name')
            ->get();

        $userIds = $users->pluck('id')->toArray();
        $employeeIds = $users->pluck('employee_id')->filter()->toArray();

        $dates = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $dates[] = [
                'date' => $curr->format('Y-m-d'),
                'day' => $curr->format('d'),
                'day_name' => $curr->format('D')
            ];
            $curr->addDay();
        }

        $allAttendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $allLeavesRaw = \App\Models\LeaveRequest::where('status', 'approved')
            ->whereIn('user_id', $userIds)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
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
                        'date' => \Carbon\Carbon::parse($d),
                        'is_rh' => $req->is_rh,
                        'is_sl' => $req->is_sl,
                        'is_half_day' => $req->is_half_day
                    ]);
                }
            }
        }
        $allLeaves = collect($leavesData);

        $allLocations = \App\Models\EmployeeLocation::whereIn('employee_id', $employeeIds)
            ->whereBetween('tracked_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->orderBy('tracked_at', 'asc')
            ->get()
            ->groupBy(function($item) {
                return $item->employee_id . '_' . $item->tracked_at->format('Y-m-d');
            });

        $reportService = app(\App\Services\AttendanceReportService::class);

        $filename = "monthly_tracking_matrix_{$month}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($users, $allAttendances, $allLeaves, $allLocations, $dates, $holidays, $reportService) {
            $file = fopen('php://output', 'w');
            
            $header = ['Employee Name'];
            foreach ($dates as $d) {
                $header[] = $d['day'] . '(' . $d['day_name'] . ')';
            }
            $header[] = 'Total KM';
            $header[] = 'Present Days';
            $header[] = 'Absent Days';
            fputcsv($file, $header);

            foreach ($users as $user) {
                $row = [$user->name];
                
                $userAttendances = $allAttendances->get($user->id, collect())->keyBy(function($item) {
                    return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
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

                $totalDistanceKm = 0.0;
                $presentDaysCount = 0;
                $absentDaysCount = 0;

                foreach ($dates as $d) {
                    $dateStr = $d['date'];
                    $kmTravelled = 0.0;
                    $statusCode = '-';

                    $shift = $user->employee->shiftRelation ?? null;
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
                        $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours);
                        $statusCode = $statusInfo['code'];
                        $statusLabel = $statusInfo['label'];
                    } elseif (in_array($dateStr, $holidays)) {
                        $statusCode = 'H';
                        $statusLabel = 'holiday';
                    } elseif ($isWeeklyOff) {
                        $statusCode = 'S';
                        $statusLabel = 'weekly off';
                    } elseif (isset($userLeavesDetails[$dateStr])) {
                        $lType = $userLeavesDetails[$dateStr];
                        $statusCode = $lType;
                        $statusLabel = $lType === 'RH' ? 'restricted holiday' : ($lType === 'SL' ? 'short leave' : 'leave');
                    } else {
                        $statusCode = 'A';
                        $statusLabel = 'absent';
                    }

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

                    if ($kmTravelled > 0) {
                        $row[] = round($kmTravelled, 2) . ' km';
                    } else {
                        $row[] = '-';
                    }
                }

                $row[] = round($totalDistanceKm, 2);
                $row[] = $presentDaysCount;
                $row[] = $absentDaysCount;

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDateReport(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        $dateStr = $request->date;
        $date = \Carbon\Carbon::parse($dateStr);

        $users = \App\Models\User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active')
                      ->where('is_tracking', 1);
            })
            ->orderBy('name')
            ->get();

        $userIds = $users->pluck('id')->toArray();
        $employeeIds = $users->pluck('employee_id')->filter()->toArray();

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereIn('user_id', $userIds)
            ->whereDate('date', $dateStr)
            ->get()
            ->keyBy('user_id');

        $holiday = \App\Models\Holiday::whereDate('holiday_date', $dateStr)->first();

        $leavesData = \App\Models\LeaveRequest::whereIn('user_id', $userIds)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->get()
            ->keyBy(function($item) {
                return $item->user_id;
            });

        $locations = \App\Models\EmployeeLocation::whereIn('employee_id', $employeeIds)
            ->whereDate('tracked_at', $dateStr)
            ->orderBy('tracked_at', 'asc')
            ->get()
            ->groupBy('employee_id');

        $reportService = app(\App\Services\AttendanceReportService::class);

        $filename = "daily_tracking_report_{$dateStr}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($users, $attendances, $leavesData, $locations, $holiday, $date, $dateStr, $reportService) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Employee Name', 'Status', 'Total Hours', 'KM Travelled', 'Logged Points Count']);

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

                $shift = $user->employee->shiftRelation ?? null;
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
                    $statusInfo = $reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours);
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

                fputcsv($file, [
                    $user->name,
                    ucwords($statusLabel),
                    round($hours, 2),
                    $kmTravelled > 0 ? "{$kmTravelled} km" : "0.00 km",
                    $dayLocations->count()
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Summary Metrics']);
            fputcsv($file, ['Total Distance Travelled Today', round($totalDistanceSum, 2) . ' km']);
            fputcsv($file, ['Total Active Tracking Employees', count($users)]);
            fputcsv($file, ['Present Count', $presentCount]);
            fputcsv($file, ['Absent Count', $absentCount]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

