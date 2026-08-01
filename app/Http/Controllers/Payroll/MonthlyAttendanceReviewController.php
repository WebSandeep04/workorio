<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\MonthlyAttendanceSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Services\AttendanceReportService;
use App\Services\PayrollCalculationService;

class MonthlyAttendanceReviewController extends Controller
{
    public function index(Request $request, PayrollCalculationService $payrollService)
    {
        if ($request->ajax()) {
            $month = $request->input('month', date('n'));
            $year = $request->input('year', date('Y'));
            
            $query = MonthlyAttendanceSummary::with('employee')
                        ->where('month', $month)
                        ->where('year', $year);
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
                });
            }
            
            $summaries = $query->latest()->paginate(15);
            return response()->json($summaries);
        }
        
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        
        return view('payroll.attendance_review', compact('months', 'years'));
    }

    public function finalAttendanceView()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        
        return view('payroll.final_attendance', compact('months', 'years'));
    }

    public function lock(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:monthly_attendance_summaries,id'
        ]);

        $summary = MonthlyAttendanceSummary::findOrFail($validated['id']);
        $summary->update(['is_locked' => true]);
        
        return response()->json(['success' => true, 'message' => 'Attendance locked successfully.']);
    }

    public function unlock(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:monthly_attendance_summaries,id'
        ]);

        $summary = MonthlyAttendanceSummary::findOrFail($validated['id']);
        $summary->update(['is_locked' => false]);
        
        return response()->json(['success' => true, 'message' => 'Attendance unlocked successfully.']);
    }

    public function sync(Request $request, AttendanceReportService $reportService, PayrollCalculationService $payrollService)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer'
        ]);

        $month = $request->month;
        $year = $request->year;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        if ($endDate->isFuture()) {
            $endDate = Carbon::today();
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
                return Carbon::parse($item->holiday_date)->format('Y-m-d');
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
            })
            ->get();

        $allLeaves = collect();
        foreach ($allLeavesRaw as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                $d = $dt->format('Y-m-d');
                if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                    $type = 'L';
                    if ($req->is_half_day) $type = 'HD';
                    elseif ($req->is_rh) $type = 'RH';
                    elseif ($req->is_sl) $type = 'SL';
                    elseif ($req->leaveType && !$req->leaveType->is_paid) $type = 'LWP';
                    
                    $allLeaves->push([
                        'user_id' => $req->user_id,
                        'date' => $d,
                        'type' => $type
                    ]);
                }
            }
        }
        $leavesByUser = $allLeaves->groupBy('user_id');

        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect());
            
            $userLeaves = [];
            if (isset($leavesByUser[$user->id])) {
                foreach ($leavesByUser[$user->id] as $l) {
                    $userLeaves[$l['date']] = $l['type'];
                }
            }

            $summary = $reportService->calculateMonthlySummary($userAttendances, $startDate, $endDate, $holidays, $userLeaves, $holidaysData, $user);

            MonthlyAttendanceSummary::updateOrCreate(
                [
                    'employee_id' => $user->employee->id,
                    'month' => $month,
                    'year' => $year
                ],
                [
                    'is_locked' => 1,
                    'total_working_days' => $summary['total_working_days'] ?? 0,
                    'working_days' => $payrollService->calculateWorkingDays($summary),
                    'total_deduction_days' => $payrollService->calculateTotalDeductionDays($summary),
                    'days_worked' => $summary['days_worked'] ?? $summary['total_present_combined'] ?? 0,
                    'days_absent' => $summary['days_absent'] ?? 0,
                    'attendance_percentage' => $summary['attendance_percentage'] ?? 0.00,
                    'total_present_combined' => $summary['total_present_combined'] ?? 0,
                    'total_present' => $summary['total_present'] ?? 0,
                    'total_halfday' => $summary['total_halfday'] ?? 0,
                    'total_weekly_offs_worked' => $summary['total_sundays_worked'] ?? 0,
                    'total_holidays_worked' => $summary['total_holidays_worked'] ?? 0,
                    'days_on_leave' => $summary['days_on_leave'] ?? 0,
                    'total_unpaid_leaves' => $summary['total_unpaid_leaves'] ?? 0,
                    'total_short_leaves' => $summary['total_short_leaves'] ?? 0,
                    'total_weekly_offs' => $summary['total_sundays'] ?? 0,
                    'total_holidays' => count($holidays),
                    'total_hours' => $summary['total_hours'] ?? '00:00',
                    'total_office_hours' => $summary['total_office_hours'] ?? '00:00',
                    'total_field_hours' => $summary['total_field_hours'] ?? '00:00',
                    'total_break_time' => $summary['total_break_time'] ?? '00:00',
                    'avg_hours_per_day' => $summary['avg_hours_per_day'] ?? '00:00',
                    'total_less_8_30' => $summary['total_less_8_30'] ?? 0,
                    'total_more_8_30' => $summary['total_more_8_30'] ?? 0,
                    'late_count' => $summary['late_count'] ?? 0,
                    'total_late_minutes' => $summary['total_late_minutes'] ?? 0,
                    'total_cycles' => $summary['total_cycles'] ?? [],
                    'late_logs' => $summary['late_logs'] ?? []
                ]
            );
        }

        return response()->json(['success' => true, 'message' => 'Attendance synchronized successfully.']);
    }
}
