<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Get all active employees with Name and DOB
     */
    public function getActiveEmployeesBirthdayList()
    {
        // Fetch active employees
        // We select minimal fields as requested
        $employees = Employee::where('status', 'active')
            ->select('id', 'name', 'date_of_birth', 'employee_code') // Included ID and code for unique identification if needed
            ->orderBy('name', 'asc')
            ->get();

        // Map to format if strict "Name and DOB only" is needed, but returning structured JSON is safer
        $data = $employees->map(function ($employee) {
            return [
                'name' => $employee->name,
                'dob' => $employee->date_of_birth,
                // Optional: formatted date or just ISO string. 
                // DB usually returns Y-m-d.
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data
        ]);
    }
}
