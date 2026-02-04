<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\EmployeeLocation;
use Illuminate\Support\Facades\Log;

class EmployeeLocationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'tracked_at' => 'nullable|date',
        ]);

        $employee = \App\Models\Employee::find($request->employee_id);

        if (!$employee || !$employee->is_tracking) {
             return response()->json([
                'success' => false,
                'message' => 'Tracking is disabled for this employee.',
            ], 403);
        }

        // Check if the last recorded location is the same
        $lastLocation = EmployeeLocation::where('employee_id', $request->employee_id)
            ->latest('id') // more reliable than tracked_at for "last inserted"
            ->first();

        if ($lastLocation) {
            // Normalize coordinates to 8 decimal places for comparison to avoid floating point issues
            $lastLat = number_format((float)$lastLocation->latitude, 8, '.', '');
            $lastLong = number_format((float)$lastLocation->longitude, 8, '.', '');
            
            $currentLat = number_format((float)$request->latitude, 8, '.', '');
            $currentLong = number_format((float)$request->longitude, 8, '.', '');

            if ($lastLat === $currentLat && $lastLong === $currentLong) {
                return response()->json([
                    'success' => true,
                    'message' => 'Location unchanged. Skipped saving.',
                    'data' => $lastLocation
                ], 200);
            }
        }

        $location = EmployeeLocation::create([
            'employee_id' => $request->employee_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tracked_at' => $request->tracked_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location tracked successfully.',
            'data' => $location
        ], 201);
    }


}
