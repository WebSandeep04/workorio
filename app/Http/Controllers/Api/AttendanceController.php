<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attendance;
use App\Models\Movement;
use App\Models\User;
use App\Models\Worklog;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Services\AttendanceReportService;

class AttendanceController extends Controller
{
    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        return auth()->user();
    }

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
     * Check if user can perform attendance actions based on worklog completion
     * Users with isWorklog = 1 must complete previous day's worklog before attendance
     */
    private function canPerformAttendanceAction()
    {
        $user = $this->getCurrentUser();
        
        // If user doesn't have worklog access, allow attendance
        if (!$user || !$user->is_worklog) {
            return ['can_perform' => true, 'message' => ''];
        }
        
        $today = Carbon::today('Asia/Kolkata');
        $userCreatedDate = $user->created_at ? Carbon::parse($user->created_at)->startOfDay() : Carbon::today('Asia/Kolkata');
        
        // Start checking from the user's creation date
        $checkDate = $userCreatedDate;
        
        // Loop through each day from creation date to yesterday
        while ($checkDate->lt($today)) {
            $dateStr = $checkDate->format('Y-m-d');
            
            // Skip if user was not present (absent) on this date
            $hasAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->exists();

            if (!$hasAttendance) {
                // If no attendance, we only skip if it's a Weekoff or Holiday
                $isHoliday = Holiday::where('holiday_date', $dateStr)->exists();
                
                // Dynamic weekoff from Shift
                $isWeekoff = false;
                $employee = $user->employee;
                if ($employee && $employee->shiftRelation && is_array($employee->shiftRelation->week_offs)) {
                    // Carbon dayOfWeek returns 0 (Sunday) to 6 (Saturday)
                    $isWeekoff = in_array($checkDate->dayOfWeek, $employee->shiftRelation->week_offs);
                } else {
                    // Fallback to hardcoded Sunday if no shift or weekoffs defined
                    $isWeekoff = $checkDate->dayOfWeek === Carbon::SUNDAY;
                }
                
                if ($isHoliday || $isWeekoff) {
                    $checkDate->addDay();
                    continue;
                }
                
                // For regular days with no attendance, the system currently skips as well
                $checkDate->addDay();
                continue;
            }

            $hasWorklogEntry = Worklog::where('user_id', $user->id)
                ->where('work_date', $dateStr)
                ->exists();
            
            $hasLeave = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('start_date', '<=', $dateStr)
                ->where('end_date', '>=', $dateStr)
                ->where('status', 'approved')
                ->exists();
            
            if (!$hasWorklogEntry && !$hasLeave) {
                $formattedDate = $checkDate->format('l, F j, Y');
                return [
                    'can_perform' => false, 
                    'message' => "You must complete your worklog entry or have leave for {$formattedDate} before you can perform attendance actions. Please complete your worklog entries chronologically starting from your account creation date."
                ];
            }
            
            // Move to next day
            $checkDate->addDay();
        }
        
        return ['can_perform' => true, 'message' => ''];
    }

    public function punchIn(Request $request): JsonResponse
    {
        $request->validate([
            'movement_type' => 'required|in:office,field,break',
            'late_reason' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'emergency_attendance' => 'nullable|boolean',
            'work_from_home' => 'nullable|boolean',
        ]);

        $user = $this->getCurrentUser();
        Log::info('Punch-in started (API)', [
            'user_id' => $user ? $user->id : null,
            'movement_type' => $request->movement_type,
        ]);
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            Log::warning('Punch-in blocked by worklog validation (API)', [
                'user_id' => $user->id,
                'reason' => $attendanceCheck['message'],
            ]);
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today('Asia/Kolkata');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if today's attendance is locked
        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is locked. Please contact your administrator.'
            ], 403);
        }

        // Check if user is currently on break - if so, prevent punch in/out actions.
        $existingAttendance = $attendance;
        if ($request->movement_type !== 'break') {
            $existingAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();
                
            if ($existingAttendance) {
                $lastBreakMovement = Movement::where('attendance_id', $existingAttendance->id)
                    ->where('movement_type', 'break')
                    ->orderBy('time', 'desc')
                    ->first();
                    
                if ($lastBreakMovement && $lastBreakMovement->movement_action === 'start') {
                    Log::info('Punch-in prevented while on break (API)', [
                        'user_id' => $user->id,
                        'attendance_id' => $existingAttendance->id,
                        'movement_type' => $request->movement_type,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'You are currently on break. Please end your break first before punching in/out.'
                    ], 400);
                }
            }
        }
        
        $description = null;
        if (in_array($request->movement_type, ['office', 'field'], true)) {
            // Check if there is already any office/field IN movement for today
            $hasAnyIn = false;
            if (Schema::hasTable('attendance') && Schema::hasTable('movements')) {
                try {
                    $hasAnyIn = Movement::whereHas('attendance', function ($q) use ($user, $today) {
                            $q->where('user_id', $user->id)
                              ->whereDate('date', $today);
                        })
                        ->whereIn('movement_type', ['office', 'field'])
                        ->where('movement_action', 'in')
                        ->exists();
                } catch (\Exception $e) {
                    $hasAnyIn = false;
                }
            }

            // --- Location Validation Start (API) ---
            $detectedPlaceName = null;
            if (!$hasAnyIn) {
                $employee = null;
                if ($user instanceof \App\Models\User) {
                    $employee = $user->employee;
                } elseif (isset($user->id)) {
                     $realUser = \App\Models\User::find($user->id);
                     if ($realUser) $employee = $realUser->employee;
                }

                if ($employee && $employee->is_place_allowed) {
                    $isEmergency = $request->boolean('emergency_attendance');

                    if (empty($request->latitude) || empty($request->longitude)) {
                        if (!$isEmergency) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Location access is required for attendance. Please enable location services.',
                            ], 422);
                        }
                    } else {
                        $allowedPlaces = $employee->places;
                        
                        if ($allowedPlaces->count() > 0) {
                            $isWithinRange = false;
                            $userLat = (float) $request->latitude;
                            $userLong = (float) $request->longitude;
                            
                            $minDistance = null;
                            $closestPlaceRadius = 0;
                            $closestPlaceName = '';

                            foreach ($allowedPlaces as $place) {
                                $distance = $this->haversineGreatCircleDistance(
                                    $userLat, $userLong, 
                                    $place->latitude, $place->longitude
                                );
                                
                                if (is_null($minDistance) || $distance < $minDistance) {
                                    $minDistance = $distance;
                                    $closestPlaceRadius = $place->radius;
                                    $closestPlaceName = $place->placename;
                                }

                                if ($distance <= $place->radius) {
                                    $isWithinRange = true;
                                    $detectedPlaceName = $place->placename;
                                    break;
                                }
                            }

                            if (!$isWithinRange && !$isEmergency) {
                                $distStr = number_format($minDistance, 1);
                                return response()->json([
                                    'success' => false,
                                    'message' => "You are not within the allowed radius. Closest: {$closestPlaceName} ({$distStr}m away, allowed {$closestPlaceRadius}m).",
                                ], 403);
                            }
                        }
                    }
                }
            }
            // --- Location Validation End (API) ---

            // ONLY check for late reason if this is the FIRST office/field IN of the day
            $lateMinutesToRecord = 0;
            if (!$hasAnyIn) {
                $employee = $user->employee;
                if ($employee && $employee->shiftRelation && $employee->shiftRelation->start_time) {
                    $shift = $employee->shiftRelation;
                    $shiftStart = Carbon::parse($today->format('Y-m-d') . ' ' . $shift->start_time, 'Asia/Kolkata');
                    $allowedLateMinutes = (int) ($shift->late_min ?? 0);
                    $cutoffTime = $shiftStart->copy()->addMinutes($allowedLateMinutes);
                    $now = Carbon::now('Asia/Kolkata');

                    if ($now->greaterThan($cutoffTime)) {
                        // Check if user already has an approved or pending leave for today
                        $hasLeaveToday = LeaveRequest::where('user_id', $user->id)
                            ->where('start_date', '<=', $today->toDateString())
                            ->where('end_date', '>=', $today->toDateString())
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();

                        if ($hasLeaveToday) {
                            $lateMinutesToRecord = 0;
                        } else {
                            $lateMinutesToRecord = (int) abs($now->diffInMinutes($cutoffTime));

                            // Check if monthly late allowance exceeded
                            $thisMonth = Carbon::now('Asia/Kolkata')->startOfMonth();
                            $alreadyUsedLateMinutes = (int) abs(Attendance::where('user_id', $user->id)
                                ->where('date', '>=', $thisMonth)
                                ->sum('late_minutes'));
                                
                            $monthlyLateAllowance = (int) ($shift->min_per_month_late_allow ?? 0);
                            
                            // The late allowance check is now only for information/logging, not a blocker.
                            // We previously blocked punch-in here if (alreadyUsedLateMinutes + lateMinutesToRecord) > monthlyLateAllowance.

                            // User is late; require reason
                            if (empty($request->late_reason)) {
                                $lateReasons = \App\Models\LateReason::where('active', true)->get(['id', 'reason']);
                                return response()->json([
                                     'success' => false,
                                     'require_late_reason' => true,
                                     'message' => 'Please provide a reason for late punch-in.',
                                     'late_reasons' => $lateReasons
                                 ], 422);
                            }
                            $description = 'Late punch-in: ' . $request->late_reason;
                        }
                    }

                }
            }
        }

        // All validations passed. Now ensure we have an attendance row for today.
        if ($existingAttendance) {
            $attendance = $existingAttendance;
            
            $updates = [];
            if ($request->boolean('emergency_attendance') && !$attendance->is_emergency) {
                $updates['is_emergency'] = 1;
            }
            if (isset($lateMinutesToRecord) && $lateMinutesToRecord > 0) {
                 $updates['late_minutes'] = $lateMinutesToRecord;
            }
            if (!empty($updates)) {
                $attendance->update($updates);
            }
        } else {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'is_emergency' => $request->boolean('emergency_attendance') ? 1 : 0,
                'is_wfh' => $request->boolean('work_from_home') ? 1 : 0,
                'late_minutes' => $lateMinutesToRecord ?? 0,
            ]);
        }

        // Get the last movement for this specific movement type
        $lastMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time', 'desc')
            ->first();

        if ($lastMovement && $lastMovement->movement_action === 'in') {
            return response()->json([
                'success' => false,
                'message' => 'Already punched in for ' . $request->movement_type . '. Please punch out first.'
            ]);
        }

        // If punching in for office, automatically punch out from field if active
        if ($request->movement_type === 'office') {
            $this->autoPunchOutField($attendance);
        }
        
        // If punching in for field, automatically punch out from office if active
        if ($request->movement_type === 'field') {
            $this->autoPunchOutOffice($attendance);
        }

        // Create movement record
        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => $request->movement_type,
            'movement_action' => 'in',
            'time' => Carbon::now(),
            'description' => $description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mode' => 'mobile',
            'place' => $detectedPlaceName,
        ]);

        // Smart Early Return Detection
        $overlappingLeave = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $today->toDateString())
            ->where('end_date', '>=', $today->toDateString())
            ->where('is_half_day', 0)
            ->where('is_sl', 0)
            ->first();

        if ($overlappingLeave) {
            $overlappingLeave->update(['has_attendance_overlap' => true]);
            Log::info('Smart Early Return detected (API)', [
                'user_id' => $user->id,
                'leave_id' => $overlappingLeave->id,
                'date' => $today->toDateString()
            ]);
        }

        Log::info('Punch-in movement created (API)', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'movement_id' => $movement->id,
        ]);

        // Get current cycle number
        $typeMovements = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time')
            ->get();
        
        $currentCycle = $this->getCurrentCycle($typeMovements);

        $message = 'Punched in for ' . $request->movement_type . ' (Cycle ' . $currentCycle . ')';
        if ($request->movement_type === 'field') {
            $message .= ' - Office work automatically ended';
        } elseif ($request->movement_type === 'office') {
            $message .= ' - Field work automatically ended';
        }

        // Check if user has pending tasks
        $hasPendingTasks = $this->hasPendingTasks($user->id);

        $isTracking = 0;
        $employee = null;
        if ($user instanceof \App\Models\User) {
             $employee = $user->employee;
        } elseif (isset($user->id)) {
             $realUser = \App\Models\User::find($user->id);
             if ($realUser) $employee = $realUser->employee;
        }

        if ($employee) {
            $isTracking = $employee->is_tracking ? 1 : 0;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'movement' => $movement,
            'cycle' => $currentCycle,
            'show_task_reminder' => $hasPendingTasks,
            'punch_type' => 'in',
            'is_tracking' => $isTracking
        ]);
    }

    public function punchOut(Request $request): JsonResponse
    {
        $request->validate([
            'movement_type' => 'required|in:office,field,break',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $user = $this->getCurrentUser();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today('Asia/Kolkata');

        // Check for pending tasks not updated today (Blocker)
        if (in_array($request->movement_type, ['office', 'field'])) {
            $notUpdatedTasksCount = \App\Models\Task::where('user_id', $user->id)
                ->where(function($query) {
                    $query->where('task_status_id', '!=', 3) // Not Completed
                          ->orWhereNull('task_status_id');
                })
                ->where(function($query) {
                    $query->where('is_done', 0)
                          ->orWhereNull('is_done');
                })
                ->whereDate('due_date', '<=', $today) // Only check tasks due today or older
                ->where('updated_at', '<', $today) // Updated before today
                ->count();

            if ($notUpdatedTasksCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "You have {$notUpdatedTasksCount} pending task(s) that were not updated today. Please update your tasks status or add remarks before punching out."
                ], 422);
            }
        }
        
        // Check if user is currently on break - if so, prevent punch out actions
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if today's attendance is locked
        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is locked. Please contact your administrator.'
            ], 403);
        }
            
        if ($attendance) {
            $lastBreakMovement = Movement::where('attendance_id', $attendance->id)
                ->where('movement_type', 'break')
                ->orderBy('time', 'desc')
                ->first();
                
            if ($lastBreakMovement && $lastBreakMovement->movement_action === 'start') {
                return response()->json([
                    'success' => false,
                    'message' => 'You are currently on break. Please end your break first before punching out.'
                ], 400);
            }
        }

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found for today'
            ]);
        }

        // Check if punched in for this type
        // Check if currently punched in for this type
        $punchInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time', 'desc')
            ->first();

        if (!$punchInMovement || $punchInMovement->movement_action !== 'in') {
            return response()->json([
                'success' => false,
                'message' => 'Not punched in for ' . $request->movement_type . '. Please punch in first.'
            ]);
        }


        // --- Location Validation Start (API) ---
        $detectedPlaceName = null;
        if (in_array($request->movement_type, ['office', 'field'])) {
            $employee = null;
            if ($user instanceof \App\Models\User) {
                $employee = $user->employee;
            } elseif (isset($user->id)) {
                 $realUser = \App\Models\User::find($user->id);
                 if ($realUser) $employee = $realUser->employee;
            }

            if ($employee && $employee->is_place_allowed) {
                $isEmergency = $request->boolean('emergency_attendance');

                if (empty($request->latitude) || empty($request->longitude)) {
                    if (!$isEmergency) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Location access is required for attendance. Please enable location services.',
                        ], 422);
                    }
                } else {
                    $allowedPlaces = $employee->places;
                    
                    if ($allowedPlaces->count() > 0) {
                        $isWithinRange = false;
                        $userLat = (float) $request->latitude;
                        $userLong = (float) $request->longitude;
                        
                        $minDistance = null;
                        $closestPlaceRadius = 0;
                        $closestPlaceName = '';

                        foreach ($allowedPlaces as $place) {
                            $distance = $this->haversineGreatCircleDistance(
                                $userLat, $userLong, 
                                $place->latitude, $place->longitude
                            );
                            
                            if (is_null($minDistance) || $distance < $minDistance) {
                                $minDistance = $distance;
                                $closestPlaceRadius = $place->radius;
                                $closestPlaceName = $place->placename;
                            }

                            if ($distance <= $place->radius) {
                                $isWithinRange = true;
                                $detectedPlaceName = $place->placename;
                                break;
                            }
                        }

                        if (!$isWithinRange && !$isEmergency) {
                            $distStr = number_format($minDistance, 1);
                            return response()->json([
                                'success' => false,
                                'message' => "You are not within the allowed radius. Closest: {$closestPlaceName} ({$distStr}m away, allowed {$closestPlaceRadius}m).",
                            ], 403);
                        }
                    }
                }
            }
        }
        // --- Location Validation End (API) ---

        // Create punch out movement
        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => $request->movement_type,
            'movement_action' => 'out',
            'time' => Carbon::now(),
            'description' => null,
            'message' => 'Successfully punched out for ' . $request->movement_type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mode' => 'mobile',
            'place' => $detectedPlaceName,
        ]);

        // Get current cycle number
        $typeMovements = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time')
            ->get();
        
        $currentCycle = $this->getCurrentCycle($typeMovements);

        // Check if user has pending tasks
        $hasPendingTasks = $this->hasPendingTasks($user->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully punched out for ' . $request->movement_type . ' (Cycle ' . $currentCycle . ' completed)',
            'movement' => $movement,
            'cycle' => $currentCycle,
            'show_task_reminder' => $hasPendingTasks,
            'punch_type' => 'out'
        ]);
    }

    public function startBreak(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today('Asia/Kolkata');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if today's attendance is locked
        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is locked. Please contact your administrator.'
            ], 403);
        }

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today
            ]);
        }

        $lastBreakMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->orderBy('time', 'desc')
            ->first();

        if ($lastBreakMovement && $lastBreakMovement->movement_action === 'start') {
            return response()->json([
                'success' => false,
                'message' => 'Break already started. Please end the current break first.'
            ]);
        }

        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'start',
            'time' => Carbon::now(),
            'description' => null,
            'message' => 'Break started successfully',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mode' => 'mobile',
            'place' => null,
        ]);

        // Get current cycle number
        $typeMovements = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->orderBy('time')
            ->get();
        
        $currentCycle = $this->getCurrentCycle($typeMovements);

        return response()->json([
            'success' => true,
            'message' => 'Break started successfully (Cycle ' . $currentCycle . ')',
            'movement' => $movement,
            'cycle' => $currentCycle
        ]);
    }

    public function endBreak(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today('Asia/Kolkata');
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if today's attendance is locked
        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance for today is locked. Please contact your administrator.'
            ], 403);
        }

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found for today'
            ]);
        }

        // Find the most recent break that was started but not ended
        $breakStart = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->where('movement_action', 'start')
            ->whereNotExists(function ($query) use ($attendance) {
                $query->select(\DB::raw(1))
                    ->from('movements as m2')
                    ->whereRaw('m2.attendance_id = movements.attendance_id')
                    ->where('m2.movement_type', 'break')
                    ->where('m2.movement_action', 'end')
                    ->whereRaw('m2.time > movements.time');
            })
            ->orderBy('time', 'desc')
            ->first();

        if (!$breakStart) {
            return response()->json([
                'success' => false,
                'message' => 'No active break found to end'
            ]);
        }

        $movement = Movement::create([
            'attendance_id' => $attendance->id,
            'movement_type' => 'break',
            'movement_action' => 'end',
            'time' => Carbon::now(),
            'description' => null,
            'message' => 'Break ended successfully',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mode' => 'mobile',
            'place' => null,
        ]);

        // Get current cycle number
        $typeMovements = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'break')
            ->orderBy('time')
            ->get();
        
        $currentCycle = $this->getCurrentCycle($typeMovements);

        return response()->json([
            'success' => true,
            'message' => 'Break ended successfully (Cycle ' . $currentCycle . ' completed)',
            'movement' => $movement,
            'cycle' => $currentCycle
        ]);
    }

    public function getTodayStatus(): JsonResponse
    {
        $user = $this->getCurrentUser();
        $today = Carbon::today('Asia/Kolkata');
        
        // Check if user has worklog access
        $attendanceCheck = $this->canPerformAttendanceAction();

        // Get tracking status
        $isTracking = 0;
        $employee = null;
        if ($user instanceof \App\Models\User) {
             $employee = $user->employee;
        } elseif (isset($user->id)) {
             $realUser = \App\Models\User::find($user->id);
             if ($realUser) $employee = $realUser->employee;
        }

        if ($employee) {
            $isTracking = $employee->is_tracking ? 1 : 0;
        }
        
        $attendance = Attendance::with(['movements' => function($q){
                $q->orderBy('time');
            }])
            ->where('user_id', $user->id)
            ->whereDate('date', Carbon::parse($today)->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return response()->json([
                'attendance' => null,
                'working_hours' => '0h 0m',
                'completed_hours' => '0h 0m',
                'last_action_time' => null,
                'movements' => [],
                'status' => [
                    'office' => [
                        'status' => 'Ready for New Cycle',
                        'badge_class' => 'badge-secondary',
                        'can_start' => true,
                        'can_end' => false,
                        'current_cycle' => 1,
                        'last_action_time' => null
                    ],
                    'field' => [
                        'status' => 'Ready for New Cycle',
                        'badge_class' => 'badge-secondary',
                        'can_start' => true,
                        'can_end' => false,
                        'current_cycle' => 1,
                        'last_action_time' => null
                    ],
                    'break' => [
                        'status' => 'Ready for New Cycle',
                        'badge_class' => 'badge-secondary',
                        'can_start' => true,
                        'can_end' => false,
                        'current_cycle' => 1,
                        'last_action_time' => null
                    ]
                ],
                'cycles' => [
                    'office_cycles' => 0,
                    'field_cycles' => 0,
                    'break_cycles' => 0
                ],
                'worklog_validation' => [
                    'can_perform_attendance' => $attendanceCheck['can_perform'],
                    'message' => $attendanceCheck['message']
                ],
                'is_tracking' => $isTracking
            ]);
        }

        $movements = $attendance->movements;

        // Group movements by type for frontend compatibility
        $movementsByType = $movements->sortBy('time')->groupBy('movement_type');

        // Calculate completed working hours (sum of completed sessions)
        $officeSeconds = $this->calculateCompletedDuration($movementsByType->get('office', collect()));
        $fieldSeconds = $this->calculateCompletedDuration($movementsByType->get('field', collect()));
        
        $totalCompletedSeconds = $officeSeconds + $fieldSeconds;
        
        // Format Completed Hours: Hh Mm
        $completedHours = floor($totalCompletedSeconds / 3600);
        $completedMinutes = floor(($totalCompletedSeconds % 3600) / 60);
        $completedHoursFormatted = sprintf('%dh %dm', $completedHours, $completedMinutes);

        // --- NEW LOGIC: Total Elapsed Time since First Punch-In ---
        // 1. Find the very first 'in' punch of the day (office or field)
        $allMovements = $movements->sortBy('time');
        $firstPunchIn = $allMovements->first(function ($movement) {
            return in_array($movement->movement_type, ['office', 'field']) && $movement->movement_action === 'in';
        });

        $elapsedSeconds = 0;

        if ($firstPunchIn) {
            $startTime = Carbon::parse($firstPunchIn->time);
            
            // Determine if the user is currently "Active" (Punched In)
            // Check Office active
            $officeIn = $movementsByType->get('office', collect())->where('movement_action', 'in')->last();
            $officeOut = $movementsByType->get('office', collect())->where('movement_action', 'out')->last();
            $officeActive = $officeIn && (!$officeOut || $officeOut->time < $officeIn->time);

            // Check Field active
            $fieldIn = $movementsByType->get('field', collect())->where('movement_action', 'in')->last();
            $fieldOut = $movementsByType->get('field', collect())->where('movement_action', 'out')->last();
            $fieldActive = $fieldIn && (!$fieldOut || $fieldOut->time < $fieldIn->time);

            $isCurrentlyActive = $officeActive || $fieldActive;

            if ($isCurrentlyActive) {
                // If active, calculate time from First IN to NOW
                $endTime = Carbon::now();
            } else {
                // If fully checked out, calculate time from First IN to Last OUT
                $lastPunchOut = $allMovements->last(function ($movement) {
                    return in_array($movement->movement_type, ['office', 'field']) && $movement->movement_action === 'out';
                });
                
                // Fallback to NOW if no OUT found (shouldn't happen if inactive but safe fallback)
                $endTime = $lastPunchOut ? Carbon::parse($lastPunchOut->time) : Carbon::now();
            }

            $elapsedSeconds = abs($endTime->getTimestamp() - $startTime->getTimestamp());
        }

        // Format Working Hours (Elapsed): Hh Mm
        $elapsedHours = floor($elapsedSeconds / 3600);
        $elapsedMinutes = floor(($elapsedSeconds % 3600) / 60);
        $workingHoursFormatted = sprintf('%dh %dm', $elapsedHours, $elapsedMinutes);


        // Calculate cycles for each type
        $cycles = [
            'office_cycles' => $this->calculateCycles($movementsByType->get('office', collect())),
            'field_cycles' => $this->calculateCycles($movementsByType->get('field', collect())),
            'break_cycles' => $this->calculateCycles($movementsByType->get('break', collect()))
        ];

        // Determine current status for each type and find active session start time
        $status = [];
        $activeLastActionTime = null;

        foreach (['office', 'field', 'break'] as $type) {
            $typeMovements = $movementsByType->get($type, collect());
            
            if ($type === 'break') {
                // For breaks, check if currently on break
                $breakStart = $typeMovements->where('movement_action', 'start')->last();
                $breakEnd = $typeMovements->where('movement_action', 'end')->last();
                
                $isCurrentlyOnBreak = $breakStart && (!$breakEnd || $breakEnd->time < $breakStart->time);
                
                $status[$type] = [
                    'status' => $isCurrentlyOnBreak ? 'On Break' : 'Ready for New Cycle',
                    'badge_class' => $isCurrentlyOnBreak ? 'badge-warning' : 'badge-success',
                    'can_start' => !$isCurrentlyOnBreak,
                    'can_end' => $isCurrentlyOnBreak,
                    'current_cycle' => $isCurrentlyOnBreak ? $this->getCurrentCycle($typeMovements) : null,
                    'last_action_time' => $breakStart ? $breakStart->time : null
                ];
            } else {
                // For office and field, check if currently punched in
                $punchIn = $typeMovements->where('movement_action', 'in')->last();
                $punchOut = $typeMovements->where('movement_action', 'out')->last();
                
                // Check if currently active (punched in but not punched out)
                $isCurrentlyActive = $punchIn && (!$punchOut || $punchOut->time < $punchIn->time);
                
                if ($isCurrentlyActive && $punchIn) {
                    $activeLastActionTime = Carbon::parse($punchIn->time)->format('Y-m-d H:i:s');
                }

                $statusText = $isCurrentlyActive ? 
                    ($type === 'office' ? 'Punched In' : 'In Field') : 
                    'Ready for New Cycle';
                
                $badgeClass = $isCurrentlyActive ? 
                    ($type === 'office' ? 'badge-success' : 'badge-info') : 
                    'badge-success';
                
                $status[$type] = [
                    'status' => $statusText,
                    'badge_class' => $badgeClass,
                    'can_start' => !$isCurrentlyActive,
                    'can_end' => $isCurrentlyActive,
                    'current_cycle' => $isCurrentlyActive ? $this->getCurrentCycle($typeMovements) : null,
                    'last_action_time' => $punchIn ? $punchIn->time : null
                ];
            }
        }

        // Convert grouped movements to array format expected by frontend
        $movementsArray = [];
        foreach ($movementsByType as $type => $typeMovements) {
            $movementsArray[$type] = $typeMovements->toArray();
        }

        return response()->json([
            'attendance' => $attendance,
            'working_hours' => $workingHoursFormatted, // Total elapsed from First IN
            'completed_hours' => $completedHoursFormatted, // Actual worked time (sum of completed sessions)
            'last_action_time' => $activeLastActionTime,
            'movements' => $movementsArray,
            'status' => $status,
            'cycles' => $cycles,
            'worklog_validation' => [
                'can_perform_attendance' => $attendanceCheck['can_perform'],
                'message' => $attendanceCheck['message']
            ],
            'is_tracking' => $isTracking
        ]);
    }

    /**
     * Check worklog validation status for attendance
     */
    public function checkWorklogValidation(): JsonResponse
    {
        $user = $this->getCurrentUser();
        $validation = $this->canPerformAttendanceAction();
        
        return response()->json([
            'can_perform_attendance' => $validation['can_perform'],
            'message' => $validation['message'],
            'user_has_worklog_access' => $user ? $user->is_worklog : false
        ]);
    }

    /**
     * Calculate the number of complete cycles for a movement type
     */
    private function calculateCycles($movements)
    {
        if ($movements->isEmpty()) {
            return 0;
        }

        $cycles = 0;
        $inAction = null;

        foreach ($movements as $movement) {
            if ($movement->movement_action === 'in' || $movement->movement_action === 'start') {
                $inAction = $movement;
            } elseif (($movement->movement_action === 'out' || $movement->movement_action === 'end') && $inAction) {
                $cycles++;
                $inAction = null;
            }
        }

        return $cycles;
    }

    /**
     * Get the current cycle number for a movement type
     */
    private function getCurrentCycle($movements)
    {
        if ($movements->isEmpty()) {
            return 1;
        }

        $cycles = 0;
        $inAction = null;

        foreach ($movements as $movement) {
            if ($movement->movement_action === 'in' || $movement->movement_action === 'start') {
                $inAction = $movement;
            } elseif (($movement->movement_action === 'out' || $movement->movement_action === 'end') && $inAction) {
                $cycles++;
                $inAction = null;
            }
        }

        // If there's an active in/start action, the current cycle is cycles + 1
        // If not active (just completed), return the count of the just-finished cycle.
        return $inAction ? $cycles + 1 : $cycles;
    }

    /**
     * Calculate total duration of completed sessions for a movement type (in seconds)
     */
    private function calculateCompletedDuration($movements)
    {
        if ($movements->isEmpty()) {
            return 0;
        }

        // Ensure movements are sorted by time
        $sortedMovements = $movements->sortBy('time');

        $totalSeconds = 0;
        $inAction = null;

        foreach ($sortedMovements as $movement) {
            // Only process 'in' (start) and 'out' (end) movements
            // Only consider office and field types for working hours, but this method can be generic
            if ($movement->movement_action === 'in' || $movement->movement_action === 'start') {
                $inAction = $movement;
            } elseif (($movement->movement_action === 'out' || $movement->movement_action === 'end') && $inAction) {
                // Determine duration
                $startTime = Carbon::parse($inAction->time);
                $endTime = Carbon::parse($movement->time);
                
                // Use absolute timestamp difference to prevent negative values
                $totalSeconds += abs($endTime->getTimestamp() - $startTime->getTimestamp());
                
                $inAction = null;
            }
        }

        return $totalSeconds;
    }

    /**
     * Get attendance history with summary details
     */
    public function getHistory(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        // Establish strict window of observation
        if ($request->has('month') && $request->has('year')) {
            $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
        } else {
            $startDate = Carbon::now('Asia/Kolkata')->startOfMonth();
            $endDate = Carbon::now('Asia/Kolkata')->endOfMonth();
        }

        // Eager load relation to ensure parity with Web App logic
        $user->loadMissing(['employee.shiftRelation']);

        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->get();
        
        // Fetch leaves and holidays for summary
        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return Carbon::parse($holiday->holiday_date)->format('Y-m-d');
            });
        
        $holidays = $holidaysData->keys()->toArray();

        $leaveRequests = LeaveRequest::with('leaveType')->where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      });
            })->get();
            
        $leavesDetails = [];
        foreach ($leaveRequests as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                    $d = $dt->format('Y-m-d');
                    if (!isset($leavesDetails[$d])) {
                        if ($req->leaveType && !$req->leaveType->is_paid) {
                            $leavesDetails[$d] = 'LWP';
                        } elseif ($req->is_rh) {
                            $leavesDetails[$d] = 'RH';
                        } elseif ($req->is_sl) {
                            $leavesDetails[$d] = 'SL';
                        } elseif ($req->is_half_day) {
                            $leavesDetails[$d] = 'HD';
                        } else {
                            $leavesDetails[$d] = 'L';
                        }
                    }
                }
            }
        }

        $reportService = app(AttendanceReportService::class);
        $summary = $reportService->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $user);
        
        $shift = $user->employee->shiftRelation ?? null;
        $dailyBreakdown = $reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $shift);

        // Reverse the daily breakdown so it is descending, to match mobile UI requirements
        $dailyBreakdownDesc = array_reverse($dailyBreakdown);

        // Map back to legacy schema required by mobile app frontend
        $mappedBreakdown = array_map(function($day) {
            $formatMinutes = function($h) {
                if ($h <= 0) return '0h 0m';
                $m = round($h * 60);
                $hours = floor($m / 60);
                $mins = $m % 60;
                return sprintf('%dh %dm', $hours, $mins);
            };

            $fieldIn = '-';
            $fieldOut = '-';
            if (isset($day['movements']) && is_array($day['movements'])) {
                foreach ($day['movements'] as $mov) {
                    if (strtolower($mov['type']) === 'field') {
                        if (strtolower($mov['action']) === 'in' && $fieldIn === '-') {
                            $fieldIn = $mov['time'];
                        }
                        if (strtolower($mov['action']) === 'out') {
                            $fieldOut = $mov['time'];
                        }
                    }
                }
            }

            // Fallback for punch_in/punch_out using first_in/last_out from generateDailyBreakdown
            $punchIn = $day['first_in'] ?? '-';
            $punchOut = $day['last_out'] ?? '-';

            // Ensure status is capitalized
            $status = isset($day['status']) ? ucwords($day['status']) : 'Absent';
            if ($status === 'Absent' && isset($day['is_holiday']) && $day['is_holiday']) {
                 $status = 'Holiday';
            }

            return array_merge($day, [
                'display_date' => Carbon::parse($day['date'])->format('D, M d, Y'),
                'status' => $status,
                'status_reason' => $day['status_reason'] ?? '-',
                'punch_in' => $punchIn,
                'punch_out' => $punchOut,
                'field_in' => $fieldIn,
                'field_out' => $fieldOut,
                'break_time' => $day['break_time'] ?? 0,
                'late_by' => $day['late_by'] ?? '-',
                'grace_balance' => $day['grace_balance'] ?? '-',
                'late_reason' => $day['late_reason'] ?? '-',
                'formatted_hours' => [
                    'office' => $formatMinutes($day['office_hours'] ?? 0),
                    'field' => $formatMinutes($day['field_hours'] ?? 0),
                    'total' => $formatMinutes($day['hours'] ?? 0)
                ]
            ]);
        }, $dailyBreakdownDesc);

        // Structure the response adhering strictly to valid Paginator contract schemas assumed by Redux state.
        return response()->json([
            'data' => $mappedBreakdown,
            'summary' => $summary,
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => count($mappedBreakdown),
            'total' => count($mappedBreakdown),
        ]);
    }
    
    /**
     * Calculate duration in minutes from movements
     */
    private function calculateDuration($movements): int
    {
        $totalMinutes = 0;
        $inTime = null;

        foreach ($movements as $movement) {
            // Force IST Timezone for calculation consistency
            if ($movement->movement_action === 'in' || $movement->movement_action === 'start') {
                $inTime = Carbon::parse($movement->time)->timezone('Asia/Kolkata');
            } elseif (($movement->movement_action === 'out' || $movement->movement_action === 'end') && $inTime) {
                $outTime = Carbon::parse($movement->time)->timezone('Asia/Kolkata');
                // Use absolute difference to prevent negative values
                $totalMinutes += abs($outTime->diffInMinutes($inTime));
                $inTime = null;
            }
        }

        // Handle active sessions
        if ($inTime) {
            $nowIst = Carbon::now('Asia/Kolkata');
            
            // Check if active session is essentially "today" (or just currently valid)
            // We calculate duration up to now regardless of day boundary if it's considered an active shift.
            // But strict logic: if inTime is today in IST.
            if ($inTime->isSameDay($nowIst)) {
                 $totalMinutes += abs($nowIst->diffInMinutes($inTime));
            }
        }

        return $totalMinutes;
    }

    /**
     * Format minutes to H:i format (e.g., "08:30" hrs)
     */
    private function formatDuration($minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d hrs', $hours, $mins);
    }

    /**
     * Format minutes to "Xh Ym" format
     */
    private function formatHoursMinutes($totalMinutes)
    {
        if ($totalMinutes <= 0) return '-';
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%dh %02dm', $hours, $minutes);
    }

    /**
     * Automatically punch out from field work when starting office work
     */
    private function autoPunchOutField($attendance): void
    {
        $fieldInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'field')
            ->orderBy('time', 'desc')
            ->first();

        if ($fieldInMovement && $fieldInMovement->movement_action === 'in') {
            // Create automatic field punch out
            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'field',
                'movement_action' => 'out',
                'time' => Carbon::now(),
                'description' => 'Auto-ended (Office work started)']);
        }
    }

    /**
     * Automatically punch out from office work when starting field work
     */
    private function autoPunchOutOffice($attendance): void
    {
        $officeInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'office')
            ->orderBy('time', 'desc')
            ->first();

        if ($officeInMovement && $officeInMovement->movement_action === 'in') {
            // Create automatic office punch out
            Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'office',
                'movement_action' => 'out',
                'time' => Carbon::now(),
                'description' => 'Auto-ended (Field work started)']);
        }
    }

    /**
     * Check if user has pending tasks
     */
    private function hasPendingTasks($userId): bool
    {
        return Task::where('user_id', $userId)
            ->where(function($query) {
                $query->where('is_done', false)
                      ->orWhere('is_done', 0)
                      ->orWhereNull('is_done');
            })
            ->exists();
    }

    /**
     * Fetch Attendance Unlock Logs (Admin only)
     */
    public function fetchUnlockLogs(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        try {
            $query = DB::table('attendance_unlock_logs')
                ->leftJoin('users', 'attendance_unlock_logs.unlocked_by', '=', 'users.id')
                ->select('attendance_unlock_logs.*', 'users.name as unlocked_by_name');

            if ($request->search) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('attendance_unlock_logs.reason', 'like', "%{$searchTerm}%")
                      ->orWhere('users.name', 'like', "%{$searchTerm}%");
                });
            }

            $logs = $query->orderBy('attendance_unlock_logs.created_at', 'desc')->paginate(20);

            $logs->getCollection()->transform(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => Carbon::parse($log->date)->format('d M Y'),
                    'unlock_date' => Carbon::parse($log->unlock_date)->format('d M Y'),
                    'reason' => $log->reason,
                    'unlocked_by' => $log->unlocked_by_name ?? 'System',
                    'unlocked_by_id' => $log->unlocked_by,
                    'created_at' => Carbon::parse($log->created_at)->setTimezone('Asia/Kolkata')->format('d M Y h:i A'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching unlock logs: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Unlock Attendance records by Date (Admin only)
     */
    public function unlockByDate(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Update all attendance records for that date (unlock approval and lock)
            $affected = Attendance::whereDate('date', $request->date)
                ->update([
                    'is_approved' => 0,
                    'is_locked' => 0
                ]);

            // Create log entry
            DB::table('attendance_unlock_logs')->insert([
                'date' => Carbon::now('Asia/Kolkata')->toDateString(),
                'unlock_date' => $request->date,
                'reason' => $request->reason,
                'unlocked_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => "Successfully unlocked {$affected} records for " . Carbon::parse($request->date)->format('d M Y')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error unlocking by date: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Unlock individual Attendance Record (Admin only)
     */
    public function unlockIndividual($id): JsonResponse
    {
        $user = $this->getCurrentUser();
        if (!$user || $user->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Forbidden. Admin access required.'], 403);
        }

        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->is_approved = 0;
            $attendance->is_locked = 0; // Unlock the record
            $attendance->save();
            
            return response()->json(['success' => true, 'message' => 'Attendance record unlocked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error unlocking attendance record: ' . $e->getMessage()], 500);
        }
    }
}
