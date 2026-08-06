<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryAdvance;
use App\Models\Employee;

class SalaryAdvanceController extends Controller
{
    public function index()
    {
        $advances = SalaryAdvance::with(['employee', 'deductions'])->latest()->get();
        $employees = Employee::where('status', 'active')->get();
        return view('salary_advances.index', compact('advances', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
            'deduction_start_month' => 'required|date_format:Y-m',
        ]);

        $employee = Employee::with(['salaries', 'employmentTypeRelation'])->findOrFail($request->employee_id);
        
        $latestSalary = $employee->salaries->first();
        $grossSalary = $latestSalary ? $latestSalary->gross_salary : 0;
        $maxPercentage = $employee->employmentTypeRelation ? $employee->employmentTypeRelation->max_advance_percentage : 0;

        $maxAllowed = ($grossSalary * $maxPercentage) / 100;

        $existingAdvances = SalaryAdvance::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->get();
        
        $currentOutstanding = $existingAdvances->sum(function($adv) {
            return $adv->remainingBalance();
        });

        $totalRequested = $currentOutstanding + $request->amount;

        if ($totalRequested > $maxAllowed) {
            $errMessage = "Total outstanding advance balance including this request exceeds the maximum allowed limit ({$maxAllowed}) for this employee type. Current outstanding is {$currentOutstanding}.";
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['amount' => [$errMessage]]
                ], 422);
            }
            return back()->withErrors(['amount' => $errMessage])->withInput();
        }

        SalaryAdvance::create([
            'employee_id' => $employee->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'reason' => $request->reason,
            'deduction_start_month' => $request->deduction_start_month,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Salary advance request created successfully and is pending approval.']);
        }

        return redirect()->route('salary_advances.index')->with('success', 'Salary advance request created successfully and is pending approval.');
    }

    public function fetch(Request $request)
    {
        $advances = SalaryAdvance::with(['employee', 'deductions'])->latest()->get();
        $advances->map(function ($adv) {
            $adv->remaining_balance = $adv->remainingBalance();
            return $adv;
        });
        return response()->json(['data' => $advances]);
    }

    public function adminIndex()
    {
        return view('salary_advances.admin_index');
    }

    public function manageIndex()
    {
        return view('salary_advances.manage_index');
    }

    public function adminFetch(Request $request)
    {
        $advances = SalaryAdvance::with(['employee', 'deductions'])->latest()->get();
        $advances->map(function ($adv) {
            $adv->remaining_balance = $adv->remainingBalance();
            return $adv;
        });
        return response()->json(['data' => $advances]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $adv = SalaryAdvance::findOrFail($id);

        if ($adv->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending advances can be updated.']);
        }

        $adv->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Salary advance status updated successfully.']);
    }

    public function destroy($id)
    {
        $adv = SalaryAdvance::findOrFail($id);
        
        if ($adv->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending advances can be deleted.'], 403);
        }

        $adv->delete();

        return response()->json(['success' => true, 'message' => 'Salary advance deleted successfully.']);
    }
}
