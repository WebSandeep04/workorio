<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attendance;
use App\Models\Movement;
use App\Models\User;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AttendanceReportService;

class AttendanceReportApiController extends Controller
{
    protected $reportService;

    public function __construct(AttendanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Helper to extract tenant user context
     */
    private function getCurrentUser()
    {
        return Auth::user();
    }

    /**
     * Check if requesting user is an administrator
     */
    private function checkAdminPermission()
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->role_id != 1) {
            return false;
        }
        return true;
    }

    /**
     * Fetch active users for dropdown mapping (Admin Only)
     */
    public function fetchReportUsers(): JsonResponse
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        try {
            $users = User::select('id', 'name', 'email')
                ->where('role_id', '!=', 1)
                ->where('is_attendance', 1)
                ->whereHas('employee', function($query) {
                    $query->where('status', 'active');
                })
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching users: ' . $e->getMessage()], 500);
        }
    }

    /**
     * User Wise Monthly Report Data
     */
    public function getUserWiseReport(Request $request): JsonResponse
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        try {
            if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
                return response()->json(['success' => false, 'message' => 'Cannot generate report for future months.'], 422);
            }

            $data = $this->_fetchUserReportData($request->user_id, $request->month);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching user report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Monthly Summary Matrix Report Data
     */
    public function getMonthlySummaryReport(Request $request): JsonResponse
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        try {
            if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
                return response()->json(['success' => false, 'message' => 'Cannot generate report for future months.'], 422);
            }

            $data = $this->_fetchMonthlyReportData($request->month);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching monthly report: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Date Wise Organizational Report Data
     */
    public function getDateWiseReport(Request $request): JsonResponse
    {
        if (!$this->checkAdminPermission()) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            if (Carbon::parse($request->date)->startOfDay()->gt(Carbon::today())) {
                return response()->json(['success' => false, 'message' => 'Cannot generate report for future dates.'], 422);
            }

            $data = $this->_fetchDateReportData($request->date);
            unset($data['carbonDate']);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching date report: ' . $e->getMessage()], 500);
        }
    }

    /* ------------------------------------------------------------------------
       INTERNAL REPORT BUILDERS (Replicated from Web Controller Logic)
    ------------------------------------------------------------------------ */

    private function _fetchUserReportData($userId, $month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        $user = User::with(['employee.shiftRelation'])->find($userId);
        $shift = $user->employee->shiftRelation ?? null;
        
        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get();

        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->holiday_date->format('Y-m-d');
            });
        
        $holidays = $holidaysData->keys()->toArray();

        $leaveRequests = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      });
            })->get();
            
        $leavesList = [];
        $leavesDetails = [];
        foreach ($leaveRequests as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                    $d = $dt->format('Y-m-d');
                    $leavesList[] = $d;
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
        $leaves = collect($leavesList)->unique()->values()->toArray();

        $summary = $this->reportService->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $user);
        
        $dailyBreakdown = $this->reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $shift);
        
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'summary' => $summary,
            'daily_breakdown' => $dailyBreakdown,
            'holidays' => $holidays,
            'leaves' => $leaves
        ];
    }

    private function _fetchMonthlyReportData($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

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

        $users = User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get(); 

        $allAttendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $allLeavesRaw = LeaveRequest::where('status', 'approved')
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
                        'date' => Carbon::parse($d),
                        'is_rh' => $req->is_rh,
                        'is_sl' => $req->is_sl,
                        'is_half_day' => $req->is_half_day
                    ]);
                }
            }
        }
        $allLeaves = collect($leavesData);

        $reportData = [];

        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect());
            $attendancesByDate = $userAttendances->keyBy(function($item) {
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
            
            foreach ($dates as $d) {
                $dateStr = $d['date'];
                $statusCode = '-';
                $statusClass = '';
                
                $shift = $user->employee->shiftRelation ?? null;
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

                if (isset($attendancesByDate[$dateStr])) {
                    $att = $attendancesByDate[$dateStr];
                    $hours = $this->reportService->calculateTotalHours($att->movements, $shift, $dateStr);
                    
                    [$fullDayHr, $halfDayHr] = $this->reportService->getThresholds($shift);
                    if ($isHalfDayWorking) {
                        $fullDayHr = $halfDayHr;
                        $halfDayHr = $halfDayHr / 2;
                    }

                    $leaveType = $userLeavesDetails[$dateStr] ?? null;
                    $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                    $hasHalfDayLeave = ($leaveType === 'HD');
                    $statusInfo = $this->reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours);
                    
                    $statusCode = $statusInfo['code'];
                    $statusClass = $statusInfo['class'];
                } elseif (in_array($dateStr, $holidays)) {
                    $statusCode = 'H';
                    $statusClass = 'text-secondary';
                } elseif ($isWeeklyOff) {
                    $statusCode = 'S';
                    $statusClass = 'text-danger small';
                } elseif (isset($userLeavesDetails[$dateStr])) {
                    $lType = $userLeavesDetails[$dateStr];
                    if ($lType === 'RH') {
                        $statusCode = 'RH';
                        $statusClass = 'text-primary';
                    } elseif ($lType === 'SL') {
                        $statusCode = 'SL';
                        $statusClass = 'text-info';
                    } else {
                        $statusCode = 'L';
                        $statusClass = 'text-warning';
                    }
                } else {
                    $statusCode = 'A';
                    $statusClass = 'text-danger';
                }
                
                $dailyStatuses[] = [
                    'date' => $dateStr,
                    'code' => $statusCode,
                    'class' => $statusClass
                ];
            }

            $summary = $this->reportService->calculateMonthlySummary($userAttendances, $startDate, $endDate, $holidays, $userLeavesDetails, null, $user);

            $reportData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name
                ],
                'summary' => $summary,
                'daily_statuses' => $dailyStatuses
            ];
        }

        return [
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'dates' => $dates
            ],
            'data' => $reportData
        ];
    }

    private function _fetchDateReportData($date)
    {
        $carbonDate = Carbon::parse($date);
        
        $users = User::with(['employee.shiftRelation'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('date', $date)
            ->get()
            ->keyBy('user_id');

        $holiday = Holiday::where('holiday_date', $date)->first();
        $isSunday = $carbonDate->dayOfWeek === Carbon::SUNDAY;

        $leavesRaw = LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();
        
        $leaves = collect();
        foreach ($leavesRaw as $req) {
            if (!$leaves->has($req->user_id)) {
                $leaves->put($req->user_id, collect([(object)[
                    'date' => Carbon::parse($date), 
                    'is_rh' => $req->is_rh, 
                    'is_sl' => $req->is_sl,
                    'is_half_day' => $req->is_half_day
                ]]));
            }
        }

        $dayName = $carbonDate->format('l');
        $reportData = [];

        foreach ($users as $user) {
            $attendance = $attendances->get($user->id);
            $userLeaves = $leaves->get($user->id);
            $leaveObj = $userLeaves ? $userLeaves->first() : null;
            $leaveType = $leaveObj ? ($leaveObj->is_rh ? 'RH' : ($leaveObj->is_sl ? 'SL' : ($leaveObj->is_half_day ? 'HD' : 'L'))) : null;
            
            $dayData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name
                ],
                'status' => 'absent',
                'holiday_name' => $holiday ? $holiday->name : null,
                'is_weekly_off' => $isSunday,
                'is_holiday' => !!$holiday,
                'is_leave' => !!$userLeaves,
                'leave_type' => $leaveType,
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'first_in' => '-',
                'last_out' => '-',
                'description' => null,
                'is_wfh' => false,
                'movements' => []
            ];

            $shift = $user->employee->shiftRelation ?? null;
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

            $dayData['is_weekly_off'] = $isWeeklyOff;

            if ($attendance) {
                $dayData['hours'] = $this->reportService->calculateTotalHours($attendance->movements, $shift, $date);
                $dayData['office_hours'] = $this->reportService->calculateTypeHours($attendance->movements, 'office');
                $dayData['field_hours'] = $this->reportService->calculateTypeHours($attendance->movements, 'field');
                $dayData['break_time'] = $this->reportService->calculateTypeHours($attendance->movements, 'break');
                $dayData['is_wfh'] = (bool)$attendance->is_wfh;
                
                $firstInMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'in')->first();
                if ($firstInMov) {
                    $dayData['first_in'] = Carbon::parse($firstInMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $lastOutMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if ($lastOutMov) {
                    $dayData['last_out'] = Carbon::parse($lastOutMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }

                [$fullDayHr, $halfDayHr] = $this->reportService->getThresholds($shift);
                if ($isHalfDayWorking) {
                    $fullDayHr = $halfDayHr;
                    $halfDayHr = $halfDayHr / 2;
                }

                $slHours = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                $hasHalfDayLeave = ($leaveType === 'HD');
                $statusInfo = $this->reportService->determineStatus($date, $dayData['hours'], $fullDayHr, $halfDayHr, $isWeeklyOff, !!$holiday, $leaveType, $hasHalfDayLeave, $slHours);
                $dayData['status'] = $statusInfo['label'];

                $descriptions = $attendance->movements
                    ->map(fn($m) => $m->description ? trim($m->description) : null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
                $dayData['description'] = !empty($descriptions) ? implode(', ', $descriptions) : null;
                
                $dayData['movements'] = $attendance->movements->map(function($m) {
                    return [
                        'time' => Carbon::parse($m->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst($m->movement_type),
                        'action' => ucfirst($m->movement_action),
                        'description' => $m->description
                    ];
                })->toArray();
            } elseif ($isWeeklyOff) {
                $dayData['status'] = 'weekly off';
            } elseif ($holiday) {
                $dayData['status'] = 'holiday';
            } elseif ($userLeaves) {
                if ($leaveType === 'RH') {
                    $dayData['status'] = 'restricted holiday';
                } elseif ($leaveType === 'SL') {
                    $dayData['status'] = 'short leave';
                } else {
                    $dayData['status'] = 'leave';
                }
            }

            $reportData[] = $dayData;
        }

        $summary = [
            'total_users' => count($users),
            'present' => 0,
            'halfday' => 0,
            'absent' => 0,
            'leave' => 0,
            'weekly_off_working' => 0,
            'holiday_working' => 0
        ];

        foreach ($reportData as $row) {
            $u = $users->firstWhere('id', $row['user']['id']);
            $shift = $u->employee->shiftRelation ?? null;
            [$fullDayHr, $halfDayHr] = $this->reportService->getThresholds($shift);

            if ($row['hours'] > 0) {
                if ($row['is_weekly_off']) {
                    $summary['weekly_off_working']++;
                } elseif ($row['is_holiday']) {
                    $summary['holiday_working']++;
                } else {
                    if ($row['hours'] >= $fullDayHr) {
                        $summary['present']++;
                    } elseif ($row['hours'] >= $halfDayHr) {
                        $summary['halfday']++;
                    } else {
                        $summary['absent']++;
                    }
                }
            } else {
                if ($row['is_leave']) {
                    $summary['leave']++;
                } elseif (!$row['is_weekly_off'] && !$row['is_holiday']) {
                    $summary['absent']++;
                }
            }
        }

        return [
            'date' => $carbonDate->format('M j, Y'),
            'summary' => $summary,
            'data' => $reportData,
            'carbonDate' => $carbonDate
        ];
    }
}
