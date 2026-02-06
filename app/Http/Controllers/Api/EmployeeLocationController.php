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
     * Calculate greatest circle distance between two coordinates
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric',
            'tracked_at' => 'nullable|date',
        ]);

        $employee = \App\Models\Employee::find($request->employee_id);

        if (!$employee || !$employee->is_tracking) {
             return response()->json([
                'success' => false,
                'message' => 'Tracking is disabled for this employee.',
            ], 403);
        }

        // Fetch last 5 locations to check associated logic
        $recentLocations = EmployeeLocation::where('employee_id', $request->employee_id)
            ->latest('id')
            ->take(5)
            ->get();

        if ($recentLocations->isNotEmpty()) {
            $lastLocation = $recentLocations->first();

            // Normalize coordinates to 8 decimal places for comparison to avoid floating point issues
            $lastLat = number_format((float)$lastLocation->latitude, 8, '.', '');
            $lastLong = number_format((float)$lastLocation->longitude, 8, '.', '');
            
            $currentLat = number_format((float)$request->latitude, 8, '.', '');
            $currentLong = number_format((float)$request->longitude, 8, '.', '');

            // 1. Exact duplicate check
            if ($lastLat === $currentLat && $lastLong === $currentLong) {
                return response()->json([
                    'success' => true,
                    'message' => 'Location unchanged. Skipped saving.',
                    'data' => $lastLocation
                ], 200);
            }

            // 2. Stationary/Jitter Filter
            // If distance between last 3–5 points < 10–15 meters And speed < 1 km/h
            $speed = $request->input('speed', 0); // Default to 0 if not provided
            
            // Check if speed is low (< 1 km/h)
            if ($speed < 1) {
                $allWithinRange = true;
                
                // Check distance against recent points (up to 5)
                foreach ($recentLocations as $loc) {
                    $dist = $this->haversineGreatCircleDistance(
                        $request->latitude, $request->longitude,
                        $loc->latitude, $loc->longitude
                    );

                    // If any point is further than 15 meters, we consider it valid movement
                    if ($dist > 15) {
                        $allWithinRange = false;
                        break;
                    }
                }

                if ($allWithinRange) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Location filtered (stationary/jitter).',
                        'data' => $lastLocation
                    ], 200);
                }
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

