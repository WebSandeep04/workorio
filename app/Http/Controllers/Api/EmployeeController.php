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
            ->whereNotNull('date_of_birth')
            ->select('id', 'name', 'date_of_birth', 'employee_code')
            ->get();

        $today = now()->startOfDay();

        // Sort by upcoming birthday
        $sortedEmployees = $employees->sortBy(function ($employee) use ($today) {
            $dob = \Carbon\Carbon::parse($employee->date_of_birth);
            
            // Set birthday to current year
            $birthdayThisYear = $dob->copy()->year($today->year);
            
            // If birthday has already passed this year, next one is next year
            if ($birthdayThisYear->lt($today)) {
                return $birthdayThisYear->addYear()->timestamp;
            }
            
            return $birthdayThisYear->timestamp;
        });

        // Map to format
        $data = $sortedEmployees->values()->map(function ($employee) {
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
