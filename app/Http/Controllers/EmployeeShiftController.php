<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmployeeShiftController extends Controller
{
    /**
     * Display the shift management index page.
     */
    public function index()
    {
        $shifts = Shift::all();
        return view('employees.shifts', compact('shifts'));
    }

    /**
     * Get list of employees with their current shifts for the datatable.
     */
    public function list(Request $request): JsonResponse
    {
        $employees = Employee::with(['shiftRelation'])
            ->where('status', 'active')
            ->get()
            ->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'employee_code' => $employee->employee_code,
                    'current_shift' => $employee->shiftRelation ? $employee->shiftRelation->name : 'N/A',
                ];
            });

        return response()->json($employees);
    }

    /**
     * Get shift history for a specific employee.
     */
    public function history($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $history = EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('employees.shift_history', compact('employee', 'history'));
    }

    /**
     * Assign shifts to one or more employees.
     */
    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'shift_effective_date' => 'required|date',
        ]);

        $employeeIds = $request->employee_ids;
        $shiftId = $request->shift_id;
        $effectiveDate = $request->shift_effective_date;

        foreach ($employeeIds as $id) {
            $employee = Employee::find($id);
            if ($employee) {
                // We update the static shift_id on the employee.
                // Since the request contains 'shift_effective_date', the model's
                // 'updated' event will automatically create the EmployeeShift history record.
                $employee->update(['shift_id' => $shiftId]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Shifts assigned successfully.']);
    }
}
