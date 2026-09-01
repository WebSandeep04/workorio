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
        // No restriction: Allow any authenticated user
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
            // Get all users who have any attendance records
            $userIdsWithAttendance = \App\Models\Attendance::pluck('user_id')->unique()->toArray();

            $users = User::select('id', 'name', 'email')
                ->where('role_id', '!=', 1)
                ->where('is_attendance', 1)
                ->where(function($query) use ($userIdsWithAttendance) {
                    $query->whereHas('employee', function($q) {
                        $q->where('status', 'active');
                    })->orWhereIn('id', $userIdsWithAttendance);
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
        
        $user = User::with(['employee.shiftHistory.shift'])->find($userId);
        
        
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

        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', $userId)
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
                        $is_unpaid = $req->leaveType && !$req->leaveType->is_paid;
                        if ($is_unpaid) {
                            $leavesDetails[$d] = 'LWP';
                        } elseif ($req->is_rh) {
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
        
        $dailyBreakdown = $this->reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $user);
        
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

        // Get all user IDs who have attendance records in the selected month
        $userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
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

        $allLeavesRaw = LeaveRequest::with('leaveType')->where('status', 'approved')
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
                        'is_unpaid' => ($req->leaveType && !$req->leaveType->is_paid),
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
                    if ($l->is_unpaid) {
                        $userLeavesDetails[$d] = 'LWP';
                    } elseif ($l->is_rh) {
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
            $cumulativeLateMinutes = 0;
            $lateDaysExceeded = 0;
            
            foreach ($dates as $d) {
                $dateStr = $d['date'];
                $statusCode = '-';
                $statusClass = '';
                
                
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
                    $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                    $statusInfo = $this->reportService->determineStatus($dateStr, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, in_array($dateStr, $holidays), $leaveType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                    
                    $origStatusCode = $statusInfo['code'];
                    $origStatusClass = $statusInfo['class'];
                    $origStatusLabel = $statusInfo['label'];
                    
                    $lateBy = (int) abs($att->late_minutes ?? 0);
                    $cumulativeLateMinutes += $lateBy;
                    
                    if ($shift && $lateBy > 0) {
                        $isGraceExhaustedNow = ($shift->min_per_month_late_allow - $cumulativeLateMinutes) < 0;
                        if ($isGraceExhaustedNow) {
                            $lateDaysExceeded++;
                        }
                    }
                    
                    $previousGrace = 0;
                    $graceBalance = '-';
                    if ($shift && isset($shift->min_per_month_late_allow)) {
                        $graceBalanceVal = $shift->min_per_month_late_allow - $cumulativeLateMinutes;
                        $graceBalance = max(0, $graceBalanceVal) . ' min';
                        $previousGrace = $shift->min_per_month_late_allow - ($cumulativeLateMinutes - $lateBy);
                    }
                    
                    $isGracePunish = $shift ? ($shift->is_grace_punish ?? 0) : 0;
                    $graceBounceDays = $shift ? ($shift->grace_bounce_day ?? 0) : 0;
                    $exemptGraceOnOvertime = $shift ? ($shift->exempt_grace_on_overtime ?? 1) : 1;
                    $statusData = $this->reportService->determineStatusAndReason($origStatusLabel, $hours, $fullDayHr, $halfDayHr, $lateBy, $previousGrace, isset($userLeavesDetails[$dateStr]), $hasHalfDayLeave, $isGracePunish, $graceBounceDays, $lateDaysExceeded, $exemptGraceOnOvertime);
                    
                    $finalLabel = strtolower($statusData['status']);
                    if ($att && $att->is_wfh && str_contains($finalLabel, 'present')) {
                        $finalLabel = 'halfday';
                    }

                    if ($finalLabel === 'absent') {
                        $statusCode = 'A';
                        $statusClass = 'text-danger';
                    } elseif ($finalLabel === 'halfday') {
                        $statusCode = 'P2';
                        $statusClass = 'text-primary';
                    } else {
                        $statusCode = $origStatusCode;
                        $statusClass = $origStatusClass;
                    }
                    $statusReason = $statusData['reason'];
                    
                    $lastOutMov = $att->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                    if (!$lastOutMov && $hours > 0) {
                        $statusCode = 'A';
                        $statusClass = 'text-danger';
                        $statusReason = 'punchout is missing';
                    }
                } elseif (in_array($dateStr, $holidays)) {
                    $statusCode = 'H';
                    $statusClass = 'text-secondary';
                    $statusReason = '-';
                } elseif ($isWeeklyOff) {
                    $statusCode = 'S';
                    $statusClass = 'text-danger small';
                    $statusReason = '-';
                } elseif (isset($userLeavesDetails[$dateStr])) {
                    $lType = $userLeavesDetails[$dateStr];
                    if ($lType === 'RH') {
                        $statusCode = 'RH';
                        $statusClass = 'text-primary';
                        $statusReason = '-';
                    } elseif ($lType === 'SL') {
                        $statusCode = 'SL';
                        $statusClass = 'text-info';
                        $statusReason = '-';
                    } elseif ($lType === 'LWP') {
                        $statusCode = 'LWP';
                        $statusClass = 'text-danger';
                        $statusReason = 'Unpaid Leave';
                    } else {
                        $statusCode = 'L';
                        $statusClass = 'text-warning';
                        $statusReason = 'On Leave';
                    }
                } else {
                    $statusCode = 'A';
                    $statusClass = 'text-danger';
                    $statusReason = 'No attendance recorded';
                }
                
                $dailyStatuses[] = [
                    'date' => $dateStr,
                    'code' => $statusCode,
                    'class' => $statusClass,
                    'reason' => $statusReason
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
        $startOfMonth = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
        
        // Get all user IDs who have attendance records in the selected month
        $userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [$startOfMonth, $date])
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereBetween('date', [$startOfMonth, $date])
            ->get()
            ->groupBy('user_id');

        $holidaysData = Holiday::whereBetween('holiday_date', [$startOfMonth, $date])
            ->get()
            ->keyBy(function($holiday) {
                return Carbon::parse($holiday->holiday_date)->format('Y-m-d');
            });
            
        $holidays = $holidaysData->keys()->toArray();

        $leavesRaw = LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function($query) use ($startOfMonth, $date) {
                $query->whereBetween('start_date', [$startOfMonth, $date])
                      ->orWhereBetween('end_date', [$startOfMonth, $date])
                      ->orWhere(function($q) use ($startOfMonth, $date) {
                          $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $date);
                      });
            })
            ->get();
        
        $leaves = collect();
        foreach ($leavesRaw as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                $d = $dt->format('Y-m-d');
                if ($dt >= new \DateTime($startOfMonth) && $dt <= new \DateTime($date)) {
                    if (!$leaves->has($req->user_id)) {
                        $leaves->put($req->user_id, []);
                    }
                    $userLeaves = $leaves->get($req->user_id);
                    $userLeaves[$d] = ($req->leaveType && !$req->leaveType->is_paid) ? 'LWP' : ($req->is_rh ? 'RH' : ($req->is_sl ? 'SL' : ($req->is_half_day ? 'HD' : 'L')));
                    $leaves->put($req->user_id, $userLeaves);
                }
            }
        }

        $reportData = [];

        foreach ($users as $user) {
            $userAttendances = $attendances->get($user->id, collect());
            $userLeavesArray = $leaves->get($user->id, []);
            
            
            $dailyBreakdown = $this->reportService->generateDailyBreakdown(
                $userAttendances, 
                Carbon::parse($startOfMonth), 
                Carbon::parse($date), 
                $holidays, 
                $userLeavesArray, 
                $holidaysData, $user);
            
            $dayData = collect($dailyBreakdown)->last();
            
            $dayData['user'] = [
                'id' => $user->id,
                'name' => $user->name
            ];
            
            $reportData[] = $dayData;
        }

        $summary = [
            'total_users' => count($users),
            'present' => 0,
            'halfday' => 0,
            'absent' => 0,
            'leave' => 0,
            'unpaid_leave' => 0,
            'weekly_off_working' => 0,
            'holiday_working' => 0,
            'sunday_working' => 0 // Adding alias for view compatibility
        ];

        foreach ($reportData as $row) {
            $statusStr = strtolower($row['status']);
            
            if (str_contains($statusStr, 'present')) {
                $summary['present']++;
            } elseif (str_contains($statusStr, 'halfday')) {
                $summary['halfday']++;
            } elseif ($statusStr === 'lwp' || str_contains($statusStr, 'unpaid leave')) {
                $summary['unpaid_leave']++;
            } elseif (str_contains($statusStr, 'absent')) {
                $summary['absent']++;
            } elseif (str_contains($statusStr, 'leave') || str_contains($statusStr, 'restricted')) {
                $summary['leave']++;
            } elseif (str_contains($statusStr, 'weekly off') && $row['hours'] > 0) {
                $summary['weekly_off_working']++;
                $summary['sunday_working']++; 
            } elseif (str_contains($statusStr, 'holiday') && $row['hours'] > 0) {
                $summary['holiday_working']++;
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
