<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\MonthlyAttendanceSummary;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MonthlyAttendanceReviewController extends Controller
{
    public function index(Request $request)
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
}
