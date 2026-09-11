<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenaltyExemption;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;

class PenaltyExemptionController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        $departments = Department::all();
        $employees = Employee::where('status', 'active')->get();
        
        return view('payroll.exemptions', compact('branches', 'departments', 'employees'));
    }

    public function fetch(Request $request)
    {
        $exemptions = PenaltyExemption::with(['branch', 'department', 'employee'])->orderBy('date', 'desc')->get();

        $data = $exemptions->map(function ($row) {
            $target = 'N/A';
            if ($row->type == 'branch') {
                $target = $row->branch ? 'Branch: ' . $row->branch->name : 'N/A';
            } elseif ($row->type == 'department') {
                $target = $row->department ? 'Department: ' . $row->department->name : 'N/A';
            } elseif ($row->type == 'employee') {
                $target = $row->employee ? 'Employee: ' . $row->employee->name . ' (' . $row->employee->employee_code . ')' : 'N/A';
            }
            
            return [
                'id' => $row->id,
                'date' => $row->date,
                'type' => $row->type,
                'target' => $target,
                'reason' => $row->reason,
                'action' => '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>'
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:branch,department,employee',
            'branch_id' => 'required_if:type,branch|nullable|exists:branches,id',
            'department_id' => 'required_if:type,department|nullable|exists:departments,id',
            'employee_id' => 'required_if:type,employee|nullable|exists:employees,id',
            'date' => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        PenaltyExemption::create($validated);

        return response()->json(['success' => true, 'message' => 'Exemption added successfully']);
    }

    public function destroy($id)
    {
        $exemption = PenaltyExemption::findOrFail($id);

        if ($exemption->delete()) {
            return response()->json(['success' => true, 'message' => 'Exemption deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to delete exemption'], 500);
    }

    public function getDepartments(Request $request)
    {
        $query = \App\Models\Department::query();
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        return response()->json($query->orderBy('name')->get(['id', 'name']));
    }

    public function getEmployees(Request $request)
    {
        $query = \App\Models\Employee::where('status', 'active');
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        return response()->json($query->orderBy('name')->get(['id', 'name', 'employee_code']));
    }
}
