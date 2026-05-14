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

    /**
     * Fetch tracking employees list
     */
    public function fetchTrackingList(Request $request): JsonResponse
    {
        try {
            // Get active employees with tracking enabled
            $employees = \App\Models\Employee::where('status', 'active')
                ->where('is_tracking', true)
                ->select('id', 'name', 'designation', 'profile_picture')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracking list: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch raw location trace logs and computed real-time session states
     */
    public function fetchTrackingLogs(Request $request): JsonResponse
    {
        try {
            $query = EmployeeLocation::with(['employee' => function($q) {
                $q->select('id', 'name', 'designation', 'profile_picture');
            }]);

            if ($request->has('employee_id') && $request->employee_id) {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->has('date') && $request->date) {
                $dateToCheck = $request->date;
                $query->whereDate('tracked_at', $dateToCheck);
            } else {
                $dateToCheck = \Carbon\Carbon::now()->format('Y-m-d');
                $query->whereDate('tracked_at', $dateToCheck);
            }

            $locations = $query->orderBy('tracked_at', 'asc')->get();

            // Calculate statuses for employees to determine badge indicators
            $employeeIds = $locations->pluck('employee_id')->unique()->values();
            
            if ($employeeIds->isEmpty() && $request->has('employee_id') && $request->employee_id) {
                $employeeIds = collect([(int)$request->employee_id]);
            }
            
            $users = \App\Models\User::whereIn('employee_id', $employeeIds)
                ->with(['attendances' => function($q) use ($dateToCheck) {
                    $q->select('id', 'user_id', 'date')
                      ->whereDate('date', $dateToCheck)
                      ->with(['movements' => function($m) {
                          $m->select('id', 'attendance_id', 'movement_type', 'movement_action', 'time')
                            ->orderBy('time');
                      }]);
                }])
                ->select('id', 'employee_id')
                ->get()
                ->keyBy('employee_id');

            $employeeDetails = [];

            foreach ($employeeIds as $empId) {
                 $user = $users->get($empId);
                 $color = '#94a3b8'; // Gray (Offline)
                 $statusText = 'Offline';
                 $details = [
                     'office_in' => '-',
                     'office_out' => '-',
                     'field_in' => '-',
                     'field_out' => '-',
                     'break' => '-'
                 ];
                 
                 if ($user && $user->attendances->isNotEmpty()) {
                     $attendance = $user->attendances->first();
                     $movements = $attendance->movements;
                     
                     // Check Break
                     $breakStart = $movements->where('movement_type', 'break')->where('movement_action', 'start')->last();
                     $breakEnd = $movements->where('movement_type', 'break')->where('movement_action', 'end')->last();
                     $isOnBreak = $breakStart && (!$breakEnd || $breakEnd->time < $breakStart->time);
                     
                     if ($isOnBreak) {
                         $color = '#F59E0B'; // Yellow
                         $statusText = 'On Break';
                         $details['break'] = 'On Break (' . \Carbon\Carbon::parse($breakStart->time)->format('h:i A') . ')';
                     } else {
                         // Check In (Office or Field)
                         $officeIn = $movements->where('movement_type', 'office')->where('movement_action', 'in')->last();
                         $officeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                         $isOffice = $officeIn && (!$officeOut || $officeOut->time < $officeIn->time);

                         $fieldIn = $movements->where('movement_type', 'field')->where('movement_action', 'in')->last();
                         $fieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                         $isField = $fieldIn && (!$fieldOut || $fieldOut->time < $fieldIn->time);
                         
                         if ($isOffice || $isField) {
                             $color = '#10B981'; // Green
                         }
                         
                         $statusText = 'Punched Out';
                         
                         if ($isOffice) {
                             $statusText = 'Punched In';
                         } elseif ($isField) {
                             $statusText = 'Field In';
                         } else {
                             $lastOfficeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                             $lastFieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                             
                             $lastOutTime = null;
                             $outType = null;
                             
                             if ($lastOfficeOut) {
                                 $lastOutTime = $lastOfficeOut->time;
                                 $outType = 'office';
                             }
                             
                             if ($lastFieldOut) {
                                 if (!$lastOutTime || $lastFieldOut->time > $lastOutTime) {
                                     $lastOutTime = $lastFieldOut->time;
                                     $outType = 'field';
                                 }
                             }
                             
                             if ($outType === 'office') {
                                 $statusText = 'Punched Out';
                             } elseif ($outType === 'field') {
                                 $statusText = 'Field Out';
                             } else {
                                 $statusText = 'Offline';
                                 $color = '#94a3b8';
                             }
                         }
                     }
                 }
                 
                 $employeeDetails[$empId] = [
                     'color' => $color,
                     'current_status' => $statusText,
                     'details' => $details
                 ];
            }

            return response()->json([
                'success' => true,
                'data' => $locations,
                'employee_details' => $employeeDetails
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tracking logs: ' . $e->getMessage()
            ], 500);
        }
    }
}

