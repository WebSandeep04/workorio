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
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $user = \App\Models\User::with(['employee.shiftHistory.shift'])->find($userId);
        if (!$user || !$user->is_attendance) {
            return null;
        }

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })->get();

        $leaves = [];
        foreach ($leavesRaw as $leave) {
            $lStart = \Carbon\Carbon::parse($leave->start_date)->max($startDate);
            $lEnd = \Carbon\Carbon::parse($leave->end_date)->min($endDate);
            
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            
            $curr = $lStart->copy();
            while ($curr->lte($lEnd)) {
                $leaves[$curr->format('Y-m-d')] = $lType;
                $curr->addDay();
            }
        }

        $dailyData = $this->reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData, $user);
        
        $leavesList = array_map(function($k, $v) { return $v; }, array_keys($leaves), $leaves);
        $formattedLeavesForSummary = array_combine(array_keys($leaves), $leavesList);

        $summary = $this->reportService->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $formattedLeavesForSummary, $holidaysData, $user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'designation' => $user->employee->designation->name ?? 'N/A'
            ],
            'daily_breakdown' => $dailyData,
            'summary' => $summary
        ];
    }

    private function _fetchMonthlyReportData($month)
    {
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

        $userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        $allAttendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $allLeavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })->get();

        $userLeaves = [];
        foreach ($allLeavesRaw as $leave) {
            $lStart = \Carbon\Carbon::parse($leave->start_date)->max($startDate);
            $lEnd = \Carbon\Carbon::parse($leave->end_date)->min($endDate);
            
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            
            $currL = $lStart->copy();
            while ($currL->lte($lEnd)) {
                $userLeaves[$leave->user_id][$currL->format('Y-m-d')] = $lType;
                $currL->addDay();
            }
        }

        $usersData = [];
        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect());
            $userLeavesDetails = $userLeaves[$user->id] ?? [];
            
            $dailyData = $this->reportService->generateDailyBreakdown($userAttendances, $startDate, $endDate, $holidays, $userLeavesDetails, $holidaysData, $user);
            
            $dailyStatuses = [];
            foreach ($dailyData as $d) {
                $dailyStatuses[] = [
                    'date' => $d['date'],
                    'code' => $d['code'],
                    'class' => $d['class']
                ];
            }
            
            $leavesList = array_map(function($k, $v) { return $v; }, array_keys($userLeavesDetails), $userLeavesDetails);
            $formattedLeavesForSummary = array_combine(array_keys($userLeavesDetails), $leavesList);

            $summary = $this->reportService->calculateMonthlySummary($userAttendances, $startDate, $endDate, $holidays, $formattedLeavesForSummary, $holidaysData, $user);

            $usersData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'designation' => $user->employee->designation->name ?? 'N/A'
                ],
                'daily_statuses' => $dailyStatuses,
                'summary' => $summary
            ];
        }
        
        return [
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'dates' => $dates
            ],
            'data' => $usersData
        ];
    }

    private function _fetchDateReportData($date)
    {
        $dateObj = \Carbon\Carbon::parse($date);
        $dateStr = $dateObj->format('Y-m-d');

        $userIdsWithAttendance = \App\Models\Attendance::where('date', $dateStr)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('date', $dateStr)
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::where('holiday_date', $dateStr)->get()->keyBy(function($holiday) {
            return $holiday->holiday_date->format('Y-m-d');
        });
        $holidays = $holidaysData->keys()->toArray();

        $leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function($query) use ($dateStr) {
                $query->where('start_date', '<=', $dateStr)
                      ->where('end_date', '>=', $dateStr);
            })->get();

        $userLeaves = [];
        foreach ($leavesRaw as $leave) {
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            $userLeaves[$leave->user_id] = $lType;
        }

        $reportData = [];
        foreach ($users as $user) {
            $userAttendance = $attendances->get($user->id, collect());
            
            $leaveArr = isset($userLeaves[$user->id]) ? [$dateStr => $userLeaves[$user->id]] : [];
            
            $dailyData = $this->reportService->generateDailyBreakdown($userAttendance, $dateObj, $dateObj, $holidays, $leaveArr, $holidaysData, $user);
            $dayData = $dailyData[0];
            
            $dayData['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'designation' => $user->employee->designation->name ?? 'N/A'
            ];
            $reportData[] = $dayData;
        }

        return [
            'date' => $dateObj->format('M j, Y'),
            'summary' => [
                'total_working' => count($users),
                'total_present' => collect($reportData)->whereIn('code', ['P', 'P2', 'W/O-W', 'H/W'])->count(),
                'total_absent' => collect($reportData)->where('code', 'A')->count(),
                'total_leaves' => collect($reportData)->whereIn('code', ['L', 'HD', 'SL', 'RH', 'LWP'])->count(),
                'total_na' => collect($reportData)->where('code', 'NA')->count()
            ],
            'data' => $reportData
        ];
    }
}
