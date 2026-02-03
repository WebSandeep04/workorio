<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{

    public function index()
    {
        $employees = \App\Models\Employee::all()->where('status', 'active');
        return view('tracking.index', compact('employees'));
    }

    public function fetchLocations(Request $request)
    {
        $query = \App\Models\EmployeeLocation::with('employee');

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('tracked_at', $date);
        } else {
            // Default to today if no date is provided
            $query->whereDate('tracked_at', now());
        }

        $locations = $query->orderBy('tracked_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}

