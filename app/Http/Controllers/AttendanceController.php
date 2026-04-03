<?php

namespace App\Http\Controllers;

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
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceController extends Controller
{
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
        
        $today = Carbon::today();
        $userCreatedDate = $user->created_at ? Carbon::parse($user->created_at)->startOfDay() : Carbon::today();
        
        // Start checking from the user's creation date
        $checkDate = $userCreatedDate;
        
        // Loop through each day from creation date to yesterday
        while ($checkDate->lt($today)) {
            $dateStr = $checkDate->format('Y-m-d');
            
            // Skip if user was not present (absent) on this date
            $hasAttendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->exists();

            if (!$hasAttendance) {
                // If no attendance, we only skip if it's a Sunday or Holiday
                $isHoliday = Holiday::where('holiday_date', $dateStr)->exists();
                $isSunday = $checkDate->dayOfWeek === Carbon::SUNDAY;
                
                if ($isHoliday || $isSunday) {
                    $checkDate->addDay();
                    continue;
                }
                
                // For regular days with no attendance, the system currently skips as well
                // according to original logic (lines 56-59)
                $checkDate->addDay();
                continue;
            }

            // If we reach here, user has attendance (could be regular day, Sunday, or Holiday)
            // They MUST have a worklog entry or approved leave
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

    public function index()
    {
        return view('attendance.index');
    }

    public function punchIn(Request $request): JsonResponse
    {
        $request->validate([
            'movement_type' => 'required|in:office,field,break',
            'late_reason' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'work_from_home' => 'nullable|boolean',
            'emergency_attendance' => 'nullable|boolean',
        ]);

        $user = $this->getCurrentUser();
        Log::info('Punch-in started', [
            'user_id' => $user ? $user->id : null,
            'movement_type' => $request->movement_type,
            'lat' => $request->latitude,
            'long' => $request->longitude,
        ]);
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            Log::warning('Punch-in blocked by worklog validation', [
                'user_id' => $user->id,
                'reason' => $attendanceCheck['message'],
            ]);
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        // Check if user is currently on break - if so, prevent punch in/out actions.
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if today's attendance is locked
        if ($existingAttendance && $existingAttendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Your attendance for today has been locked. Please contact your administrator for any changes.'
            ], 403);
        }
                
        if ($existingAttendance) {
            $lastBreakMovement = Movement::where('attendance_id', $existingAttendance->id)
                ->where('movement_type', 'break')
                ->orderBy('time', 'desc')
                ->first();
                    
            if ($lastBreakMovement && $lastBreakMovement->movement_action === 'start') {
                Log::info('Punch-in prevented while on break', [
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
        
        $description = null;
        if (in_array($request->movement_type, ['office', 'field'], true)) {
            // Check if there is already any office/field IN movement for today
            // This determines if this is the FIRST punch-in of the day
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

            Log::info('Punch-in check: first IN detection', [
                'user_id' => $user->id,
                'movement_type' => $request->movement_type,
                'has_any_in' => $hasAnyIn,
            ]);

            // --- Location Validation Start ---
            // Validate ONLY on first punch-in of the day
            $detectedPlaceName = null;
            if (!$hasAnyIn) {
                // Ensure user->employee relation is loaded/accessible
                $employee = null;
                if ($user instanceof \App\Models\User) {
                    $employee = $user->employee;
                } elseif (isset($user->id)) {
                     $realUser = \App\Models\User::find($user->id);
                     if ($realUser) $employee = $realUser->employee;
                }

                if ($employee && $employee->is_place_allowed) {
                    $isWFH = $request->boolean('work_from_home');
                    $isEmergency = $request->boolean('emergency_attendance');

                    if (empty($request->latitude) || empty($request->longitude)) {
                        if (!$isEmergency) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Location access is required for attendance. Please enable location services.',
                            ], 422);
                        }
                    }

                    $allowedPlaces = $employee->places;
                    
                    if ($allowedPlaces->count() > 0) {
                        $isWithinRange = false;
                        $userLat = (float) $request->latitude;
                        $userLong = (float) $request->longitude;
                        
                        $minDistance = null;
                        $closestPlaceRadius = 0;
                        $closestPlaceName = '';

                        foreach ($allowedPlaces as $place) {
                            // Calculate distance in meters
                            $distance = $this->haversineGreatCircleDistance(
                                $userLat, $userLong, 
                                $place->latitude, $place->longitude
                            );
                            
                            if (is_null($minDistance) || $distance < $minDistance) {
                                $minDistance = $distance;
                                $closestPlaceRadius = $place->radius;
                                $closestPlaceName = $place->placename;
                            }
                            
                            // Check if within radius
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
            // --- Location Validation End ---

            $lateMinutesToRecord = 0;
            // ONLY check for late reason if this is the FIRST office/field IN of the day
            if (!$hasAnyIn) {
                $employee = $user->employee;
                Log::info('Late check: employee + shift lookup', [
                    'user_id' => $user->id,
                    'employee_id' => $employee ? $employee->id : null,
                    'has_shift' => $employee && $employee->shiftRelation ? true : false,
                ]);

                if ($employee && $employee->shiftRelation && $employee->shiftRelation->start_time) {
                    $shift = $employee->shiftRelation;
                    $shiftStart = Carbon::parse($today->format('Y-m-d') . ' ' . $shift->start_time);
                    $allowedLateMinutes = (int) ($shift->late_min ?? 0);
                    $cutoffTime = $shiftStart->copy()->addMinutes($allowedLateMinutes);
                    $now = Carbon::now();

                    Log::info('Late check: timing comparison', [
                        'user_id' => $user->id,
                        'shift_start' => $shiftStart->toDateTimeString(),
                        'late_min' => $allowedLateMinutes,
                        'cutoff_time' => $cutoffTime->toDateTimeString(),
                        'now' => $now->toDateTimeString(),
                        'is_late' => $now->greaterThan($cutoffTime),
                    ]);

                    // ONLY require late reason if current time is AFTER (shift_start + late_min)
                    if ($now->greaterThan($cutoffTime)) {
                        $lateMinutesToRecord = (int) abs($now->diffInMinutes($shiftStart));

                        // Check if monthly late allowance exceeded
                        $thisMonth = Carbon::now()->startOfMonth();
                        $alreadyUsedLateMinutes = (int) abs(Attendance::where('user_id', $user->id)
                            ->where('date', '>=', $thisMonth)
                            ->sum('late_minutes'));
                            
                        $monthlyLateAllowance = (int) ($employee->employmentTypeRelation->min_per_month_late_allow ?? 0);

                        // Check if user already has an approved or pending leave for today
                        // (which would exempt them from the late allowance check)
                        $hasLeaveToday = LeaveRequest::where('user_id', $user->id)
                            ->where('start_date', '<=', $today->toDateString())
                            ->where('end_date', '>=', $today->toDateString())
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();
                        
                        // If allowance is set ( > 0 ) and combined late minutes exceed it, block the punch-in
                        // BUT exempt them if they have already applied for or had a leave approved for today
                        if (!$hasLeaveToday && $monthlyLateAllowance > 0 && ($alreadyUsedLateMinutes + $lateMinutesToRecord) > $monthlyLateAllowance) {

                             return response()->json([
                                 'success' => false,
                                 'late_allowance_exceeded' => true,
                                 'message' => 'You have exceeded your monthly late allowance (' . $alreadyUsedLateMinutes . '/' . $monthlyLateAllowance . ' min). Today\'s late of ' . $lateMinutesToRecord . ' min would put you at ' . ($alreadyUsedLateMinutes + $lateMinutesToRecord) . ' min. Please apply for a Short Leave (SL) or Half-Day leave instead.',
                                 'used' => $alreadyUsedLateMinutes,
                                 'today_late' => $lateMinutesToRecord,
                                 'allowance' => $monthlyLateAllowance,
                                 'leave_url' => route('leave.index')
                             ], 403);
                        }
                        
                        // User is late; require reason
                        if (empty($request->late_reason)) {

                            Log::info('Late punch-in requires reason (no DB write)', [
                                'user_id' => $user->id,
                                'movement_type' => $request->movement_type,
                            ]);
                            return response()->json([
                                'success' => false,
                                'require_late_reason' => true,
                                'message' => 'Please provide a reason for late punch-in.',
                            ], 422);
                        }
                        $description = 'Late punch-in: ' . $request->late_reason;
                        Log::info('Late punch-in accepted with reason', [
                            'user_id' => $user->id,
                            'movement_type' => $request->movement_type,
                            'description' => $description,
                        ]);
                    }
                }
            } else {
                // This is NOT the first punch-in, so skip late reason check
                Log::info('Late check skipped: not first punch-in of day', [
                    'user_id' => $user->id,
                    'movement_type' => $request->movement_type,
                ]);
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
                'is_wfh' => $request->boolean('work_from_home') ? 1 : 0,
                'is_emergency' => $request->boolean('emergency_attendance') ? 1 : 0,
                'late_minutes' => $lateMinutesToRecord ?? 0,
            ]);
        }

        Log::info('Punch-in attendance resolved', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'date' => $today->toDateString(),
        ]);

        // Get the last movement for this specific movement type
        $lastMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->orderBy('time', 'desc')
            ->first();

        if ($lastMovement && $lastMovement->movement_action === 'in') {
            Log::info('Punch-in rejected: already punched in for this type', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'movement_type' => $request->movement_type,
                'last_movement_id' => $lastMovement->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Already punched in for ' . $request->movement_type . '. Please punch out first.'
            ]);
        }

        // If punching in for office, automatically punch out from field if active
        if ($request->movement_type === 'office') {
            $this->autoPunchOutField($attendance);
            Log::info('Auto punch-out field executed from office punch-in', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
            ]);
        }
        
        // If punching in for field, automatically punch out from office if active
        if ($request->movement_type === 'field') {
            $this->autoPunchOutOffice($attendance);
            Log::info('Auto punch-out office executed from field punch-in', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
            ]);
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
            'mode' => 'web',
            'place' => $detectedPlaceName,
        ]);

        Log::info('Punch-in movement created', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'movement_id' => $movement->id,
            'movement_type' => $movement->movement_type,
            'description' => $movement->description,
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
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'movement' => $movement,
            'cycle' => $currentCycle,
            'show_task_reminder' => $hasPendingTasks,
            'punch_type' => 'in'
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
        
        $today = Carbon::today();

        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Your attendance for today has been locked. Please contact your administrator for any changes.'
            ], 403);
        }

        // Check for pending tasks not updated today (Blocker)
        // Only valid for 'office' and 'field' punch outs
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
        $punchInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', $request->movement_type)
            ->where('movement_action', 'in')
            ->first();

        if (!$punchInMovement) {
            return response()->json([
                'success' => false,
                'message' => 'Not punched in for ' . $request->movement_type
            ]);
        }

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
            'mode' => 'web',
            'place' => null,
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
        // No validation needed for description

        $user = $this->getCurrentUser();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::firstOrCreate([
            'user_id' => $user->id,
            'date' => $today
        ]);

        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Your attendance for today has been locked. Please contact your administrator for any changes.'
            ], 403);
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
            'mode' => 'web',
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
        // No validation needed for description

        $user = $this->getCurrentUser();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        if (!$attendanceCheck['can_perform']) {
            return response()->json([
                'success' => false,
                'message' => $attendanceCheck['message']
            ], 403);
        }
        
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No attendance record found for today'
            ]);
        }

        if ($attendance && $attendance->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Your attendance for today has been locked. Please contact your administrator for any changes.'
            ], 403);
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mode' => 'web',
            'place' => null,
            'message' => 'Break ended successfully'
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
        $today = Carbon::today();
        
        // Check if user can perform attendance actions
        $attendanceCheck = $this->canPerformAttendanceAction();
        
        $attendance = Attendance::with(['movements' => function($q){
                $q->orderBy('time');
            }])
            ->where('user_id', $user->id)
            ->whereDate('date', Carbon::parse($today)->format('Y-m-d'))
            ->first();

        if (!$attendance) {
            return response()->json([
                'attendance' => null,
                'movements' => [],
                'status' => 'not_started',
                'cycles' => [
                    'office_cycles' => 0,
                    'field_cycles' => 0,
                    'break_cycles' => 0
                ],
                'worklog_validation' => [
                    'can_perform_attendance' => $attendanceCheck['can_perform'],
                    'message' => $attendanceCheck['message']
                ]
            ]);
        }

        $movements = $attendance->movements;

        // Group movements by type for frontend compatibility
        $movementsByType = $movements->sortBy('time')->groupBy('movement_type');

        // Calculate cycles for each type
        $cycles = [
            'office_cycles' => $this->calculateCycles($movementsByType->get('office', collect())),
            'field_cycles' => $this->calculateCycles($movementsByType->get('field', collect())),
            'break_cycles' => $this->calculateCycles($movementsByType->get('break', collect()))
        ];

        $lateMinutes = (int) ($attendance->late_minutes ?? 0);

        // Determine current status for each type
        $status = [];
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
            'movements' => $movementsArray, // Return grouped movements as expected by frontend
            'status' => $status,
            'cycles' => $cycles,
            'late_minutes' => $lateMinutes,
            'worklog_validation' => [
                'can_perform_attendance' => $attendanceCheck['can_perform'],
                'message' => $attendanceCheck['message']
            ]
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
        return $inAction ? $cycles + 1 : $cycles + 1;
    }

    /**
     * Check worklog validation status for attendance
     * This helps frontend show appropriate messages
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

    public function history()
    {
        return view('attendance.history');
    }

    public function getHistoryData(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        
        
        $perPage = $request->get('per_page', 10);
        
        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $user->id)
            
            ->orderBy('date', 'desc')
            ->paginate($perPage);
        
        
        return response()->json($attendances);
    }

    public function getAttendanceStats(): JsonResponse
    {
        $user = $this->getCurrentUser();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        $stats = [
            'today_hours' => 0,
            'month_hours' => 0,
            'total_days' => 0,
            'avg_hours_per_day' => 0
        ];

        // Today's attendance
        $todayAttendance = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->where('date', $today)
            
            ->first();

        if ($todayAttendance) {
            $stats['today_hours'] = $this->calculateHours($todayAttendance->movements);
        }

        // This month's attendance
        $monthAttendances = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->where('date', '>=', $thisMonth)
            
            ->get();

        $stats['total_days'] = $monthAttendances->count();
        
        $totalMonthHours = 0;
        $totalLateMinutes = 0;
        foreach ($monthAttendances as $attendance) {
            $totalMonthHours += $this->calculateHours($attendance->movements);
            $totalLateMinutes += (int) abs($attendance->late_minutes ?? 0);
        }
        
        $stats['month_hours'] = $totalMonthHours;
        $stats['total_late_minutes'] = $totalLateMinutes;
        
        // Late Allowance
        $lateAllowance = 0;
        if ($user && $user->employee && $user->employee->employmentTypeRelation) {
            $lateAllowance = (int) $user->employee->employmentTypeRelation->min_per_month_late_allow;
        }
        $stats['late_allowance'] = $lateAllowance;

        $stats['avg_hours_per_day'] = $stats['total_days'] > 0 ? round($totalMonthHours / $stats['total_days'], 2) : 0;

        return response()->json($stats);
    }

    public function statsView()
    {
        return view('attendance.stats');
    }

    public function getAdvancedStats(): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $now = Carbon::now();
        $today = Carbon::today();
        
        // Get all user attendance
        $allAttendances = Attendance::with('movements')
            ->where('user_id', $user->id)
            ->orderBy('date')
            ->get();

        // Basic stats
        $stats = [
            'overview' => $this->calculateOverviewStats($allAttendances, $today),
            'weekly' => $this->calculateWeeklyStats($allAttendances),
            'monthly' => $this->calculateMonthlyStats($allAttendances),
            'trends' => $this->calculateTrendStats($allAttendances),
            'productivity' => $this->calculateProductivityStats($allAttendances),
            'patterns' => $this->calculatePatternStats($allAttendances)
        ];

        return response()->json($stats);
    }

    private function calculateOverviewStats($attendances, $today)
    {
        $totalDays = $attendances->count();
        $totalHours = 0;
        $totalOfficeHours = 0;
        $totalFieldHours = 0;
        $totalBreakTime = 0;
        $totalLateMinutes = 0;
        $totalCycles = ['office' => 0, 'field' => 0, 'break' => 0];

        foreach ($attendances as $attendance) {
            $dayHours = $this->calculateHours($attendance->movements);
            $totalHours += $dayHours;
            $totalLateMinutes += (int) ($attendance->late_minutes ?? 0);
            
            $officeHours = $this->calculateTypeHours($attendance->movements, 'office');
            $fieldHours = $this->calculateTypeHours($attendance->movements, 'field');
            $breakTime = $this->calculateTypeHours($attendance->movements, 'break');
            
            $totalOfficeHours += $officeHours;
            $totalFieldHours += $fieldHours;
            $totalBreakTime += $breakTime;
            
            $cycles = $this->calculateDayCycles($attendance->movements);
            $totalCycles['office'] += $cycles['office'];
            $totalCycles['field'] += $cycles['field'];
            $totalCycles['break'] += $cycles['break'];
        }

        // Today's specific stats
        $todayStats = ['hours' => 0, 'office_hours' => 0, 'field_hours' => 0, 'break_time' => 0, 'cycles' => ['office' => 0, 'field' => 0, 'break' => 0]];
        $todayAttendance = $attendances->where('date', $today->format('Y-m-d'))->first();
        if ($todayAttendance) {
            $todayStats['hours'] = $this->calculateHours($todayAttendance->movements);
            $todayStats['office_hours'] = $this->calculateTypeHours($todayAttendance->movements, 'office');
            $todayStats['field_hours'] = $this->calculateTypeHours($todayAttendance->movements, 'field');
            $todayStats['break_time'] = $this->calculateTypeHours($todayAttendance->movements, 'break');
            $todayStats['cycles'] = $this->calculateDayCycles($todayAttendance->movements);
        }

        return [
            'total_days' => $totalDays,
            'total_hours' => round($totalHours, 2),
            'total_office_hours' => round($totalOfficeHours, 2),
            'total_field_hours' => round($totalFieldHours, 2),
            'total_break_time' => round($totalBreakTime, 2),
            'total_late_minutes' => $totalLateMinutes,
            'avg_hours_per_day' => $totalDays > 0 ? round($totalHours / $totalDays, 2) : 0,
            'avg_office_hours_per_day' => $totalDays > 0 ? round($totalOfficeHours / $totalDays, 2) : 0,
            'avg_field_hours_per_day' => $totalDays > 0 ? round($totalFieldHours / $totalDays, 2) : 0,
            'total_cycles' => $totalCycles,
            'today' => $todayStats
        ];
    }

    private function calculateWeeklyStats($attendances)
    {
        $weeks = [];
        $currentWeekStart = null;
        $weeklyData = [];

        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->date);
            $weekStart = $date->copy()->startOfWeek();
            $weekKey = $weekStart->format('Y-m-d');

            if (!isset($weeklyData[$weekKey])) {
                $weeklyData[$weekKey] = [
                    'week_start' => $weekStart->format('M j'),
                    'week_end' => $weekStart->copy()->endOfWeek()->format('M j, Y'),
                    'days' => 0,
                    'total_hours' => 0,
                    'office_hours' => 0,
                    'field_hours' => 0,
                    'break_time' => 0,
                    'cycles' => ['office' => 0, 'field' => 0, 'break' => 0]
                ];
            }

            $weeklyData[$weekKey]['days']++;
            $weeklyData[$weekKey]['total_hours'] += $this->calculateHours($attendance->movements);
            $weeklyData[$weekKey]['office_hours'] += $this->calculateTypeHours($attendance->movements, 'office');
            $weeklyData[$weekKey]['field_hours'] += $this->calculateTypeHours($attendance->movements, 'field');
            $weeklyData[$weekKey]['break_time'] += $this->calculateTypeHours($attendance->movements, 'break');
            
            $cycles = $this->calculateDayCycles($attendance->movements);
            $weeklyData[$weekKey]['cycles']['office'] += $cycles['office'];
            $weeklyData[$weekKey]['cycles']['field'] += $cycles['field'];
            $weeklyData[$weekKey]['cycles']['break'] += $cycles['break'];
        }

        // Round all hours and sort by week
        foreach ($weeklyData as $key => $week) {
            $weeklyData[$key]['total_hours'] = round($week['total_hours'], 2);
            $weeklyData[$key]['office_hours'] = round($week['office_hours'], 2);
            $weeklyData[$key]['field_hours'] = round($week['field_hours'], 2);
            $weeklyData[$key]['break_time'] = round($week['break_time'], 2);
            $weeklyData[$key]['avg_hours_per_day'] = $week['days'] > 0 ? round($week['total_hours'] / $week['days'], 2) : 0;
        }

        ksort($weeklyData);
        return array_values($weeklyData);
    }

    private function calculateMonthlyStats($attendances)
    {
        $monthlyData = [];

        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->date);
            $monthKey = $date->format('Y-m');
            $monthName = $date->format('M Y');

            if (!isset($monthlyData[$monthKey])) {
                $monthlyData[$monthKey] = [
                    'month' => $monthName,
                    'days' => 0,
                    'total_hours' => 0,
                    'office_hours' => 0,
                    'field_hours' => 0,
                    'break_time' => 0,
                    'late_minutes' => 0,
                    'cycles' => ['office' => 0, 'field' => 0, 'break' => 0]
                ];
            }

            $monthlyData[$monthKey]['days']++;
            $monthlyData[$monthKey]['total_hours'] += $this->calculateHours($attendance->movements);
            $monthlyData[$monthKey]['office_hours'] += $this->calculateTypeHours($attendance->movements, 'office');
            $monthlyData[$monthKey]['field_hours'] += $this->calculateTypeHours($attendance->movements, 'field');
            $monthlyData[$monthKey]['break_time'] += $this->calculateTypeHours($attendance->movements, 'break');
            $monthlyData[$monthKey]['late_minutes'] += (int) ($attendance->late_minutes ?? 0);
            
            $cycles = $this->calculateDayCycles($attendance->movements);
            $monthlyData[$monthKey]['cycles']['office'] += $cycles['office'];
            $monthlyData[$monthKey]['cycles']['field'] += $cycles['field'];
            $monthlyData[$monthKey]['cycles']['break'] += $cycles['break'];
        }

        // Round all hours and add averages
        foreach ($monthlyData as $key => $month) {
            $monthlyData[$key]['total_hours'] = round($month['total_hours'], 2);
            $monthlyData[$key]['office_hours'] = round($month['office_hours'], 2);
            $monthlyData[$key]['field_hours'] = round($month['field_hours'], 2);
            $monthlyData[$key]['break_time'] = round($month['break_time'], 2);
            $monthlyData[$key]['avg_hours_per_day'] = $month['days'] > 0 ? round($month['total_hours'] / $month['days'], 2) : 0;
        }

        ksort($monthlyData);
        return array_values($monthlyData);
    }

    private function calculateTrendStats($attendances)
    {
        if ($attendances->count() < 2) {
            return [
                'hours_trend' => 'stable',
                'productivity_trend' => 'stable',
                'consistency_score' => 0
            ];
        }

        $recentDays = $attendances->take(-7);
        $previousDays = $attendances->take(-14)->take(7);

        $recentAvg = $recentDays->avg(function ($attendance) {
            return $this->calculateHours($attendance->movements);
        });

        $previousAvg = $previousDays->avg(function ($attendance) {
            return $this->calculateHours($attendance->movements);
        });

        $hoursTrend = 'stable';
        if ($recentAvg > $previousAvg * 1.1) {
            $hoursTrend = 'increasing';
        } elseif ($recentAvg < $previousAvg * 0.9) {
            $hoursTrend = 'decreasing';
        }

        // Calculate consistency score (lower standard deviation = higher consistency)
        $allHours = $attendances->map(function ($attendance) {
            return $this->calculateHours($attendance->movements);
        })->toArray();

        $mean = array_sum($allHours) / count($allHours);
        $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $allHours)) / count($allHours);
        $stdDev = sqrt($variance);
        
        // Consistency score: 100 - (std dev as percentage of mean)
        $consistencyScore = $mean > 0 ? max(0, 100 - (($stdDev / $mean) * 100)) : 0;

        return [
            'hours_trend' => $hoursTrend,
            'recent_avg_hours' => round($recentAvg, 2),
            'previous_avg_hours' => round($previousAvg, 2),
            'consistency_score' => round($consistencyScore, 1)
        ];
    }

    private function calculateProductivityStats($attendances)
    {
        $productiveHours = 0;
        $totalWorkHours = 0;
        $peakDays = [];
        $lowDays = [];

        foreach ($attendances as $attendance) {
            $dayHours = $this->calculateHours($attendance->movements);
            $officeHours = $this->calculateTypeHours($attendance->movements, 'office');
            $fieldHours = $this->calculateTypeHours($attendance->movements, 'field');
            
            $workHours = $officeHours + $fieldHours;
            $totalWorkHours += $workHours;
            
            // Consider productive if > 6 hours of actual work
            if ($workHours >= 6) {
                $productiveHours += $workHours;
                $peakDays[] = [
                    'date' => Carbon::parse($attendance->date)->format('M j'),
                    'hours' => round($workHours, 2)
                ];
            } else {
                $lowDays[] = [
                    'date' => Carbon::parse($attendance->date)->format('M j'),
                    'hours' => round($workHours, 2)
                ];
            }
        }

        // Sort and limit peak/low days
        usort($peakDays, function($a, $b) { return $b['hours'] <=> $a['hours']; });
        usort($lowDays, function($a, $b) { return $a['hours'] <=> $b['hours']; });
        
        return [
            'productive_days' => count($peakDays),
            'low_productivity_days' => count($lowDays),
            'productivity_rate' => $attendances->count() > 0 ? round((count($peakDays) / $attendances->count()) * 100, 1) : 0,
            'avg_productive_hours' => count($peakDays) > 0 ? round($productiveHours / count($peakDays), 2) : 0,
            'top_peak_days' => array_slice($peakDays, 0, 5),
            'recent_low_days' => array_slice($lowDays, 0, 5)
        ];
    }

    private function calculatePatternStats($attendances)
    {
        $dayOfWeekStats = [];
        $hourlyPatterns = [];

        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->date);
            $dayOfWeek = $date->format('l');
            
            if (!isset($dayOfWeekStats[$dayOfWeek])) {
                $dayOfWeekStats[$dayOfWeek] = ['days' => 0, 'total_hours' => 0];
            }
            
            $dayOfWeekStats[$dayOfWeek]['days']++;
            $dayOfWeekStats[$dayOfWeek]['total_hours'] += $this->calculateHours($attendance->movements);

            // Analyze hourly patterns
            foreach ($attendance->movements as $movement) {
                $hour = Carbon::parse($movement->time)->hour;
                if (!isset($hourlyPatterns[$hour])) {
                    $hourlyPatterns[$hour] = 0;
                }
                $hourlyPatterns[$hour]++;
            }
        }

        // Calculate averages for days of week
        foreach ($dayOfWeekStats as $day => $stats) {
            $dayOfWeekStats[$day]['avg_hours'] = $stats['days'] > 0 ? round($stats['total_hours'] / $stats['days'], 2) : 0;
        }

        // Find most active hours
        arsort($hourlyPatterns);
        $mostActiveHours = array_slice(array_keys($hourlyPatterns), 0, 3);

        return [
            'day_of_week_stats' => $dayOfWeekStats,
            'most_active_hours' => $mostActiveHours,
            'hourly_activity' => $hourlyPatterns
        ];
    }

    private function calculateTypeHours($movements, $type): float
    {
        $typeMovements = $movements->where('movement_type', $type)->sortBy('time');
        
        if ($typeMovements->isEmpty()) {
            return 0;
        }
        
        // Find first punch-in and last punch-out for this type
        $firstPunchIn = null;
        $lastPunchOut = null;
        
        foreach ($typeMovements as $movement) {
            if (($movement->movement_action === 'in' || $movement->movement_action === 'start') && !$firstPunchIn) {
                $firstPunchIn = Carbon::parse($movement->time);
            }
            if ($movement->movement_action === 'out' || $movement->movement_action === 'end') {
                $lastPunchOut = Carbon::parse($movement->time);
            }
        }
        
        // If no punch-in found, return 0
        if (!$firstPunchIn) {
            return 0;
        }
        
        // If no punch-out found, calculate until end of day (6 PM) or now
        if (!$lastPunchOut) {
            $endOfDay = $firstPunchIn->copy()->setTime(18, 0, 0); // 6 PM
            $lastPunchOut = Carbon::now()->lt($endOfDay) ? Carbon::now() : $endOfDay;
        }
        
        // Calculate total minutes from first punch-in to last punch-out
        $totalMinutes = $firstPunchIn->diffInMinutes($lastPunchOut);
        
        return $totalMinutes / 60;
    }

    private function calculateDayCycles($movements)
    {
        $cycles = ['office' => 0, 'field' => 0, 'break' => 0];
        $groupedMovements = $movements->groupBy('movement_type');

        foreach ($groupedMovements as $type => $typeMovements) {
            if ($type === 'break') {
                $startCount = $typeMovements->where('movement_action', 'start')->count();
                $endCount = $typeMovements->where('movement_action', 'end')->count();
                $cycles[$type] = min($startCount, $endCount);
            } else {
                $inCount = $typeMovements->where('movement_action', 'in')->count();
                $outCount = $typeMovements->where('movement_action', 'out')->count();
                $cycles[$type] = min($inCount, $outCount);
            }
        }

        return $cycles;
    }

    public function reportView()
    {
        // Get all users for the dropdown
        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
            
        return view('attendance.report', compact('users'));
    }

    private function _fetchUserReportData($userId, $month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        
        $user = User::find($userId);
        
        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date')
            ->get();

        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->holiday_date->format('Y-m-d');
            });
        
        $holidays = $holidaysData->keys()->toArray();

        $leaveRequests = \App\Models\LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      });
            })->get();
            
        $leavesList = [];
        $leavesDetails = [];
        foreach ($leaveRequests as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                    $d = $dt->format('Y-m-d');
                    $leavesList[] = $d;
                    if (!isset($leavesDetails[$d])) {
                        if ($req->is_rh) {
                            $leavesDetails[$d] = 'RH';
                        } elseif ($req->is_sl) {
                            $leavesDetails[$d] = 'SL';
                        } else {
                            $leavesDetails[$d] = 'L';
                        }
                    }
                }
            }
        }
        $leaves = collect($leavesList)->unique()->values()->toArray();

        $summary = $this->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData, $user);
        
        $dailyBreakdown = $this->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData);
        
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ],
            'summary' => $summary,
            'daily_breakdown' => $dailyBreakdown,
            'holidays' => $holidays,
            'leaves' => $leaves,
            'debug' => [
                'holidays_count' => count($holidays),
                'leaves_count' => count($leaves),
                'holidays_list' => $holidays,
                'leaves_list' => $leaves
            ]
        ];
    }

    public function getReportData(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
            return response()->json([
                'message' => 'Cannot generate report for future months.'
            ], 422);
        }

        $data = $this->_fetchUserReportData($request->user_id, $request->month);
        return response()->json($data);
    }

    public function getMonthlyReportData(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
            return response()->json([
                'message' => 'Cannot generate report for future months.'
            ], 422);
        }

        $data = $this->_fetchMonthlyReportData($request->month);

        return response()->json($data);
    }

    private function _fetchDateReportData($date)
    {
        $carbonDate = Carbon::parse($date);
        
        $users = User::where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('date', $date)
            ->get()
            ->keyBy('user_id');

        $holiday = Holiday::where('holiday_date', $date)->first();
        $isSunday = $carbonDate->dayOfWeek === Carbon::SUNDAY;

        $leavesRaw = \App\Models\LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();
        
        $leaves = collect();
        foreach ($leavesRaw as $req) {
            if (!$leaves->has($req->user_id)) {
                $leaves->put($req->user_id, collect([(object)['date' => \Carbon\Carbon::parse($date), 'is_rh' => $req->is_rh, 'is_sl' => $req->is_sl]]));
            }
        }

        $reportData = [];

        foreach ($users as $user) {
            $attendance = $attendances->get($user->id);
            $userLeaves = $leaves->get($user->id);
            $leaveObj = $userLeaves ? $userLeaves->first() : null;
            $leaveType = $leaveObj ? ($leaveObj->is_rh ? 'RH' : ($leaveObj->is_sl ? 'SL' : 'L')) : null;
            
            $dayData = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name
                ],
                'status' => 'absent',
                'holiday_name' => $holiday ? $holiday->name : null,
                'is_sunday' => $isSunday,
                'is_holiday' => !!$holiday,
                'is_leave' => !!$userLeaves,
                'leave_type' => $leaveType,
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'first_in' => '-',
                'last_out' => '-',
                'description' => null,
                'is_wfh' => false,
                'movements' => []
            ];

            if ($attendance) {
                $dayData['hours'] = $this->calculateHours($attendance->movements);
                $dayData['office_hours'] = $this->calculateTypeHours($attendance->movements, 'office');
                $dayData['field_hours'] = $this->calculateTypeHours($attendance->movements, 'field');
                $dayData['break_time'] = $this->calculateTypeHours($attendance->movements, 'break');
                $dayData['is_wfh'] = $attendance->is_wfh;
                
                $firstInMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'in')->first();
                if ($firstInMov) {
                    $dayData['first_in'] = Carbon::parse($firstInMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                $lastOutMov = $attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if ($lastOutMov) {
                    $dayData['last_out'] = Carbon::parse($lastOutMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }

                if ($isSunday) {
                    $dayData['status'] = 'sunday working';
                } elseif ($holiday) {
                    $dayData['status'] = 'holiday working';
                } else {
                    if ($dayData['leave_type'] === 'SL') {
                        if ($dayData['hours'] >= 7) {
                            $dayData['status'] = 'present';
                        } else {
                            $dayData['status'] = 'short leave';
                        }
                    } else {
                        if ($dayData['hours'] >= 7) {
                            $dayData['status'] = 'present';
                        } elseif ($dayData['hours'] >= 4) {
                            $dayData['status'] = 'halfday';
                        } else {
                            $dayData['status'] = 'absent';
                        }
                    }
                }

                $descriptions = $attendance->movements
                    ->map(fn($m) => $m->description ? trim($m->description) : null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
                $dayData['description'] = !empty($descriptions) ? implode(', ', $descriptions) : null;
                
                $dayData['movements'] = $attendance->movements->map(function($m) {
                    return [
                        'time' => Carbon::parse($m->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst($m->movement_type),
                        'action' => ucfirst($m->movement_action),
                        'description' => $m->description
                    ];
                })->toArray();
            } elseif ($isSunday) {
                $dayData['status'] = 'sunday';
            } elseif ($holiday) {
                $dayData['status'] = 'holiday';
            } elseif ($userLeaves) {
                if ($leaveType === 'RH') {
                    $dayData['status'] = 'restricted holiday';
                } elseif ($leaveType === 'SL') {
                    $dayData['status'] = 'short leave';
                } else {
                    $dayData['status'] = 'leave';
                }
            }

            $reportData[] = $dayData;
        }

        $summary = [
            'total_users' => count($users),
            'present' => 0,
            'halfday' => 0,
            'absent' => 0,
            'leave' => 0,
            'sunday_working' => 0,
            'holiday_working' => 0
        ];

        foreach ($reportData as $row) {
            if ($row['hours'] > 0) {
                if ($row['is_sunday']) {
                    $summary['sunday_working']++;
                } elseif ($row['is_holiday']) {
                    $summary['holiday_working']++;
                } else {
                    if ($row['hours'] >= 7) {
                        $summary['present']++;
                    } elseif ($row['hours'] >= 4) {
                        $summary['halfday']++;
                    } else {
                        $summary['absent']++;
                    }
                }
            } else {
                if ($row['is_leave']) {
                    $summary['leave']++;
                } elseif (!$row['is_sunday'] && !$row['is_holiday']) {
                    $summary['absent']++;
                }
            }
        }

        return [
            'date' => $carbonDate->format('M j, Y'),
            'summary' => $summary,
            'data' => $reportData,
            'carbonDate' => $carbonDate
        ];
    }

    public function getDateReportData(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        if (Carbon::parse($request->date)->startOfDay()->gt(Carbon::today())) {
            return response()->json([
                'message' => 'Cannot generate report for future dates.'
            ], 422);
        }

        $data = $this->_fetchDateReportData($request->date);
        unset($data['carbonDate']);

        return response()->json($data);
    }

    public function exportMonthlyReport(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
            return back()->with('error', 'Cannot generate report for future months.');
        }

        $month = $request->month;
        $data = $this->_fetchMonthlyReportData($month);
        
        $filename = "attendance_report_{$month}.csv";
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            $header = ['User'];
            foreach ($data['month']['dates'] as $d) {
                $header[] = $d['day'] . '(' . $d['day_name'] . ')';
            }
            $header = array_merge($header, ['Work Days', 'Total Present', 'Full Day', 'Half Day', 'Sunday Work', 'Holiday Work', 'Leave', 'Absent', 'Less 8:30', 'More 8:30', 'Late Count', 'Late Min']);
            fputcsv($file, $header);

            // Data Rows
            foreach ($data['data'] as $item) {
                $row = [$item['user']['name']];
                
                // Daily statuses
                if (isset($item['daily_statuses'])) {
                    foreach ($item['daily_statuses'] as $status) {
                        $row[] = $status['code'];
                    }
                }
                
                // Summaries
                $s = $item['summary'];
                $row[] = $s['total_working_days'];
                $row[] = $s['total_present_combined'];
                $row[] = $s['total_present'];
                $row[] = $s['total_halfday'];
                $row[] = $s['total_sundays_worked'];
                $row[] = $s['total_holidays_worked'];
                $row[] = $s['days_on_leave'];
                $row[] = $s['days_absent'];
                $row[] = $s['total_less_8_30'];
                $row[] = $s['total_more_8_30'];
                $row[] = $s['late_count'] ?? 0;
                $row[] = $s['total_late_minutes'] ?? 0;
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMonthlyReportPdf(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m'
        ]);

        if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
            return back()->with('error', 'Cannot generate report for future months.');
        }

        $month = $request->month;
        $data = $this->_fetchMonthlyReportData($month);
        
        $pdf = Pdf::loadView('attendance.monthly-report-pdf', compact('data', 'month'))
                ->setPaper('a2', 'landscape');
                
        return $pdf->download("attendance_report_{$month}.pdf");
    }

    public function exportUserReportPdf(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m'
        ]);

        if (Carbon::createFromFormat('Y-m', $request->month)->startOfMonth()->isFuture()) {
            return back()->with('error', 'Cannot generate report for future months.');
        }

        $data = $this->_fetchUserReportData($request->user_id, $request->month);
        
        $pdf = Pdf::loadView('attendance.user-report-pdf', compact('data'))
                ->setPaper('a4', 'landscape');
                
        return $pdf->download("user_attendance_report_{$request->month}.pdf");
    }

    public function exportDateReportPdf(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        if (Carbon::parse($request->date)->isFuture()) {
            return back()->with('error', 'Cannot generate report for future dates.');
        }

        $data = $this->_fetchDateReportData($request->date);
        
        $pdf = Pdf::loadView('attendance.date-report-pdf', compact('data'))
                ->setPaper('a4', 'landscape');
                
        return $pdf->download("date_attendance_report_{$request->date}.pdf");
    }

    private function _fetchMonthlyReportData($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Generate all dates in the month
        $dates = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $dates[] = [
                'date' => $curr->format('Y-m-d'),
                'day' => $curr->format('d'),
                'day_name' => $curr->format('D'), // Mon, Tue
                'is_sunday' => $curr->dayOfWeek === Carbon::SUNDAY
            ];
            $curr->addDay();
        }

        // Get all users who are active employees AND not admin (role_id != 1)
        $users = User::where('role_id', '!=', 1)
            ->whereHas('employee', function($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get(); 

        // Get all attendance for the month
        $allAttendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        // Get holidays
        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        // Get all leaves
        $allLeavesRaw = \App\Models\LeaveRequest::where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      });
            })->get();
            
        $leavesData = [];
        foreach ($allLeavesRaw as $req) {
            $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
            foreach ($period as $dt) {
                $d = $dt->format('Y-m-d');
                if ($dt >= new \DateTime($startDate->format('Y-m-d')) && $dt <= new \DateTime($endDate->format('Y-m-d'))) {
                    if (!isset($leavesData[$req->user_id])) {
                        $leavesData[$req->user_id] = collect();
                    }
                    $leavesData[$req->user_id]->push((object)[
                        'date' => \Carbon\Carbon::parse($d),
                        'is_rh' => $req->is_rh,
                        'is_sl' => $req->is_sl
                    ]);
                }
            }
        }
        $allLeaves = collect($leavesData);

        $reportData = [];

        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect());
            // Key by date for easy lookup - Ensure we use Y-m-d format
            $attendancesByDate = $userAttendances->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m-d');
            });

            $rawLeaves = $allLeaves->get($user->id, collect());
            
            $userLeaves = $rawLeaves->pluck('date')
                ->map(function($date) {
                    return $date->format('Y-m-d');
                })
                ->unique()
                ->toArray(); // Just array of leave dates
                
            $userLeavesDetails = [];
            foreach ($rawLeaves as $l) {
                $d = $l->date->format('Y-m-d');
                if (!isset($userLeavesDetails[$d])) {
                    if ($l->is_rh) {
                        $userLeavesDetails[$d] = 'RH';
                    } elseif ($l->is_sl) {
                        $userLeavesDetails[$d] = 'SL';
                    } else {
                        $userLeavesDetails[$d] = 'L';
                    }
                }
            }
            
            $dailyStatuses = [];
            
            foreach ($dates as $d) {
                $dateStr = $d['date'];
                $statusCode = '-';
                $statusClass = '';
                
                // Priority Logic:
                // 1. Attendance (Present/Half Day)
                // 2. Holiday (if no attendance)
                // 3. Sunday (if no attendance)
                // 4. Leave (if no attendance and on working day)
                // 5. Absent
                
                if (isset($attendancesByDate[$dateStr])) {
                    $att = $attendancesByDate[$dateStr];
                    $hours = $this->calculateHours($att->movements);
                    
                    // Check if it was a Holiday/Sunday working
                    if ($d['is_sunday']) {
                        $statusCode = 'S/W'; // Sunday Working
                        $statusClass = 'text-info';
                    } elseif (in_array($dateStr, $holidays)) {
                        $statusCode = 'H/W'; // Holiday Working
                        $statusClass = 'text-info';
                    } else {
                        $leaveType = $userLeavesDetails[$dateStr] ?? null;
                        if ($leaveType === 'SL') {
                            if ($hours >= 7) {
                                $statusCode = 'P (SL)';
                                $statusClass = 'text-success';
                            } else {
                                $statusCode = 'SL';
                                $statusClass = 'text-info';
                            }
                        } else {
                            // Normal Working Day
                            if ($hours >= 7) {
                                $statusCode = 'P'; // Present
                                $statusClass = 'text-success';
                            } elseif ($hours >= 4) {
                                $statusCode = 'P2'; // Half Day
                                $statusClass = 'text-warning';
                            } else {
                                $statusCode = 'P'; // Falling back to P if punched in but low hours (or could be absent)
                                $statusClass = 'text-success'; 
                            }
                        }
                    }

                } elseif (in_array($dateStr, $holidays)) {
                    $statusCode = 'H'; // Holiday
                    $statusClass = 'text-secondary';
                } elseif ($d['is_sunday']) {
                    $statusCode = 'S'; // Sunday
                    $statusClass = 'text-danger small';
                } elseif (isset($userLeavesDetails[$dateStr])) {
                    $lType = $userLeavesDetails[$dateStr];
                    if ($lType === 'RH') {
                        $statusCode = 'RH';
                        $statusClass = 'text-primary';
                    } elseif ($lType === 'SL') {
                        $statusCode = 'SL';
                        $statusClass = 'text-info';
                    } else {
                        $statusCode = 'L'; // Leave
                        $statusClass = 'text-warning';
                    }
                } else {
                    $statusCode = 'A'; // Absent
                    $statusClass = 'text-danger';
                }
                
                $dailyStatuses[] = [
                    'date' => $dateStr,
                    'code' => $statusCode,
                    'class' => $statusClass
                ];
            }

            // Re-format leaves for the summary function
             $userLeavesSummary = $allLeaves->get($user->id, collect())
                ->pluck('date')
                ->map(function($date) { return $date->format('Y-m-d'); })
                ->unique()
                ->values()
                ->toArray();

            $summary = $this->calculateMonthlySummary($userAttendances, $startDate, $endDate, $holidays, $userLeavesSummary, null, $user);

            $reportData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name
                ],
                'summary' => $summary,
                'daily_statuses' => $dailyStatuses
            ];
        }

        return [
            'month' => [
                'display' => $startDate->format('F Y'),
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'dates' => $dates
            ],
            'data' => $reportData
        ];
    }

    private function calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $user = null)
    {
        $totalWorkingDays = 0;
        $totalDaysWorked = 0;
        $totalHours = 0;
        $totalOfficeHours = 0;
        $totalFieldHours = 0;
        $totalBreakTime = 0;
        $totalCycles = ['office' => 0, 'field' => 0, 'break' => 0];
        $daysAbsent = 0;
        $daysOnLeave = 0;
        $totalLeaves = 0; // Count all leaves for summary display
        $presentDays = 0; // Days with >= 7 hours
        $halfDays = 0; // Days with >= 4 hours but < 7 hours
        $totalSundays = 0;
        $totalSundaysWorked = 0;
        $totalHolidaysWorked = 0;
        $totalLess8_30 = 0;
        $totalMore8_30 = 0;
        $lateCount = 0;
        $totalLateMinutes = 0;
        $lateLogs = [];
        
        $shiftRelation = $user ? ($user->employee->shiftRelation ?? null) : null;
        $shift = $shiftRelation;
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                $totalSundays++;
            } elseif (!in_array($currentDate->format('Y-m-d'), $holidays)) {
                $totalWorkingDays++;
            }
            $currentDate->addDay();
        }
        
        // Create sets to track working days with attendance/leave
        $attendanceDates = [];
        $leaveDates = [];
        $holidaysWithAttendance = []; // Track holidays where user has attendance
        
        // Process attendance records
        foreach ($attendances as $attendance) {
            $attendanceDate = Carbon::parse($attendance->date);
            $dateStr = $attendanceDate->format('Y-m-d');
            
            // Check if attendance is on a holiday
            if (in_array($dateStr, $holidays)) {
                // If user has attendance on a holiday, mark it (don't count as leave)
                $holidaysWithAttendance[] = $dateStr;
            }
            
            // Count total holidays worked (Sunday OR Holiday with attendance)
            if ($attendanceDate->dayOfWeek === Carbon::SUNDAY) {
                $totalSundaysWorked++;
            } elseif (in_array($dateStr, $holidays)) {
                $totalHolidaysWorked++;
            }
            
            // Only count attendance on working days (not Sundays or holidays) for work stats
            if ($attendanceDate->dayOfWeek !== Carbon::SUNDAY && !in_array($dateStr, $holidays)) {
                $attendanceDates[] = $dateStr;
                $totalDaysWorked++;
                
                $dayHours = $this->calculateHours($attendance->movements);
                $totalHours += $dayHours;
                
                // Count present and half days based on hours
                if ($dayHours >= 7) {
                    $presentDays++;
                } elseif ($dayHours >= 4) {
                    $halfDays++;
                }

                if ($dayHours >= 8.5) {
                    $totalMore8_30++;
                } elseif ($dayHours >= 4) {
                    $totalLess8_30++;
                }
                
                $officeHours = $this->calculateTypeHours($attendance->movements, 'office');
                $fieldHours = $this->calculateTypeHours($attendance->movements, 'field');
                $breakTime = $this->calculateTypeHours($attendance->movements, 'break');
                
                $totalOfficeHours += $officeHours;
                $totalFieldHours += $fieldHours;
                $totalBreakTime += $breakTime;
                
                $cycles = $this->calculateDayCycles($attendance->movements);
                $totalCycles['office'] += $cycles['office'];
                $totalCycles['field'] += $cycles['field'];
                $totalCycles['break'] += $cycles['break'];

                // Late Check
                if ($shift && $shift->start_time) {
                    $firstPunch = $attendance->movements->whereIn('movement_type', ['office', 'field'])->first();
                    if ($firstPunch) {
                        $punchTime = Carbon::parse($firstPunch->time)->setTimezone('Asia/Kolkata');
                        
                        $shiftDate = Carbon::parse($attendance->date)->format('Y-m-d');
                        $shiftTime = Carbon::parse($shift->start_time)->format('H:i:s');
                        // Shift time is UTC in DB, convert to IST
                        $shiftStart = Carbon::parse($shiftDate . ' ' . $shiftTime, 'UTC')->setTimezone('Asia/Kolkata');
                        
                        $lateThreshold = $shiftStart->copy()->addMinutes($shift->late_min ?? 0);
                        
                        $isLate = false;
                        $reason = '';
                        
                        if ($punchTime->gt($lateThreshold)) {
                            // Only count late if worked >= 4 hours
                            if ($dayHours >= 4) {
                                $lateCount++;
                                $isLate = true;
                                $reason = 'Late: Punched after threshold and worked >= 4hrs';
                            } else {
                                $reason = 'Not Late: Punched after threshold but worked < 4hrs';
                            }
                        } else {
                            $reason = 'Not Late: on time';
                        }
                        
                        $lateLogs[] = [
                            'date' => $dateStr,
                            'punch' => $punchTime->toDateTimeString(),
                            'threshold' => $lateThreshold->toDateTimeString(),
                            'hours' => $dayHours,
                            'is_late' => $isLate,
                            'reason' => $reason
                        ];
                    }
                }
                
                // Add late minutes from attendance record if it exists
                $totalLateMinutes += (int) abs($attendance->late_minutes ?? 0);
            }
        }
        
        // Count ALL leaves for summary display, excluding leaves on holidays
        // If a day is a holiday, it's counted as holiday only (not leave), regardless of whether user has attendance
        // Also remove duplicates to ensure accurate count
        $uniqueLeaves = array_unique($leaves);
        $totalLeaves = 0;
        foreach ($uniqueLeaves as $leaveDate) {
            $dateStr = is_string($leaveDate) ? $leaveDate : Carbon::parse($leaveDate)->format('Y-m-d');
            // Exclude leaves on holidays (holidays are counted separately, not as leaves)
            // Also exclude leaves on holidays where user has attendance (attendance on holiday = holiday only, not leave)
            if (!in_array($dateStr, $holidays) && !in_array($dateStr, $holidaysWithAttendance)) {
                $totalLeaves++;
            }
        }
        
        // Filter leave records to only count those on working days, excluding days with attendance
        // Also exclude leaves on holidays where user has attendance (attendance on holiday = holiday only)
        // This is used for attendance calculation (absent days), not for summary display
        // Use unique leaves to avoid counting duplicates
        foreach ($uniqueLeaves as $leaveDate) {
            $dateStr = is_string($leaveDate) ? $leaveDate : Carbon::parse($leaveDate)->format('Y-m-d');
            $leaveCarbon = Carbon::parse($dateStr);
            
            // Only count leave on working days (not Sundays or holidays) for attendance calculation
            // Also exclude days that already have attendance (attendance takes precedence)
            // Also exclude holidays where user has attendance (attendance on holiday = holiday only, not leave)
            if ($leaveCarbon->dayOfWeek !== Carbon::SUNDAY 
                && !in_array($dateStr, $holidays) 
                && !in_array($dateStr, $attendanceDates)
                && !in_array($dateStr, $holidaysWithAttendance)) {
                $leaveDates[] = $dateStr;
                $daysOnLeave++;
            }
        }
        
        // Calculate total holidays count (excluding Sundays)
        $totalHolidays = 0;
        foreach ($holidays as $holidayDate) {
            $holidayCarbon = Carbon::parse($holidayDate);
            // Only count holidays that are not Sundays
            if ($holidayCarbon->dayOfWeek !== Carbon::SUNDAY) {
                $totalHolidays++;
            }
        }
        
        // Calculate absent days (working days - days worked - days on leave)
        $daysAbsent = $totalWorkingDays - $totalDaysWorked - $daysOnLeave;
        $daysAbsent = max(0, $daysAbsent); // Ensure non-negative
        
        // Calculate attendance percentage: (Present Days + Half Days) / Total Working Days × 100
        // Note: 
        // - Total Working Days = All days excluding Sundays and holidays
        // - Present Days = Days with >= 7 hours
        // - Half Days = Days with >= 4 hours but < 7 hours
        // - Only Present and Half Days are counted in the numerator
        $attendancePercentage = $totalWorkingDays > 0 
            ? round((($presentDays + $halfDays) / $totalWorkingDays) * 100, 1) 
            : 0;
        $attendancePercentage = min(100, $attendancePercentage); // Cap at 100%
        
        return [
            'total_working_days' => $totalWorkingDays,
            'days_worked' => $totalDaysWorked,
            'days_absent' => $daysAbsent,
            'days_on_leave' => $totalLeaves, // Show total leaves count (all leaves) for summary display
            'attendance_percentage' => $attendancePercentage,
            'total_present_combined' => $presentDays + $halfDays + $totalHolidaysWorked + $totalSundaysWorked,
            'total_present' => $presentDays,
            'total_halfday' => $halfDays,
            'total_sundays' => $totalSundays,
            'total_sundays_worked' => $totalSundaysWorked,
            'total_holidays_worked' => $totalHolidaysWorked,
            'total_hours' => round($totalHours, 2),
            'total_office_hours' => round($totalOfficeHours, 2),
            'total_field_hours' => round($totalFieldHours, 2),
            'total_break_time' => round($totalBreakTime, 2),
            'avg_hours_per_day' => $totalDaysWorked > 0 ? round($totalHours / $totalDaysWorked, 2) : 0,
            'total_cycles' => $totalCycles,
            'total_less_8_30' => $totalLess8_30,
            'total_more_8_30' => $totalMore8_30,
            'late_count' => $lateCount,
            'total_late_minutes' => $totalLateMinutes,
            'late_logs' => $lateLogs
        ];
    }

    private function generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null)
    {
        $dailyData = [];
        // Key attendance by formatted date string to avoid Carbon key mismatch
        $attendanceByDate = $attendances->keyBy(function ($attendance) {
            return Carbon::parse($attendance->date)->format('Y-m-d');
        });
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayName = $currentDate->format('l');
            $displayDate = $currentDate->format('M j, Y');
            
            $dayData = [
                'date' => $dateStr,
                'display_date' => $displayDate,
                'day_name' => $dayName,
                'is_sunday' => $currentDate->dayOfWeek === Carbon::SUNDAY,
                'is_holiday' => in_array($dateStr, $holidays),
                'is_leave' => isset($leaves[$dateStr]),
                'leave_type' => $leaves[$dateStr] ?? null,
                'holiday_name' => null,
                'status' => 'absent',
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'cycles' => ['office' => 0, 'field' => 0, 'break' => 0],
                'movements' => []
            ];
            
            // First check if user has attendance for this date (takes priority for data)
            if (isset($attendanceByDate[$dateStr])) {
                $attendance = $attendanceByDate[$dateStr];
                $dayData['hours'] = $this->calculateHours($attendance->movements);
                $dayData['office_hours'] = $this->calculateTypeHours($attendance->movements, 'office');
                $dayData['field_hours'] = $this->calculateTypeHours($attendance->movements, 'field');
                $dayData['break_time'] = $this->calculateTypeHours($attendance->movements, 'break');
                $dayData['cycles'] = $this->calculateDayCycles($attendance->movements);
                $dayData['late_minutes'] = (int) ($attendance->late_minutes ?? 0);
                $dayData['is_wfh'] = $attendance->is_wfh;
                
                // Format movements for display
                $dayData['movements'] = $attendance->movements->map(function($movement) {
                    return [
                        'time' => Carbon::parse($movement->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst($movement->movement_type),
                        'action' => ucfirst($movement->movement_action),
                        'description' => $movement->description
                    ];
                })->toArray();
                
                // Aggregate descriptions
                $descriptions = $attendance->movements
                    ->map(function($m) { 
                        return $m->description ? trim($m->description) : null; 
                    })
                    ->filter(function($desc) { 
                        return !empty($desc); 
                    })
                    ->unique()
                    ->values()
                    ->toArray();
                
                $dayData['description'] = !empty($descriptions) ? implode('<br>', $descriptions) : null;

                // Set status - handle specific labeling for Sundays/Holidays vs Regular days
                if ($dayData['is_sunday']) {
                    $dayData['status'] = 'S/W';
                } elseif ($dayData['is_holiday']) {
                    $dayData['status'] = 'H/W';
                    if ($holidaysData && isset($holidaysData[$dateStr])) {
                        $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                    }
                } else {
                    if ($dayData['leave_type'] === 'SL') {
                        if ($dayData['hours'] >= 7) {
                            $dayData['status'] = 'present';
                        } else {
                            $dayData['status'] = 'short leave';
                        }
                    } else {
                        if ($dayData['hours'] >= 7) {
                            $dayData['status'] = 'present';
                        } elseif ($dayData['hours'] >= 4) {
                            $dayData['status'] = 'halfday';
                        } else {
                            $dayData['status'] = 'absent by less hr';
                        }
                    }
                }
            } else {
                // NO attendance found, determine fallback status
                if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                    $dayData['status'] = 'sunday';
                } elseif (in_array($dateStr, $holidays)) {
                    $dayData['status'] = 'holiday';
                    if ($holidaysData && isset($holidaysData[$dateStr])) {
                        $dayData['holiday_name'] = $holidaysData[$dateStr]->name;
                    }
                } elseif (isset($leaves[$dateStr])) {
                    if ($leaves[$dateStr] === 'RH') {
                        $dayData['status'] = 'restricted holiday';
                    } elseif ($leaves[$dateStr] === 'SL') {
                        $dayData['status'] = 'short leave';
                    } else {
                        $dayData['status'] = 'leave';
                    }
                } else {
                    $dayData['status'] = 'absent';
                }
            }
            
            $dailyData[] = $dayData;
            $currentDate->addDay();
        }
        
        return $dailyData;
    }

    private function calculateHours($movements): float
    {
        if ($movements->isEmpty()) {
            return 0;
        }

        $movements = $movements->sortBy('time');
        
        // Find first punch-in and last punch-out across all movement types
        $firstPunchIn = null;
        $lastPunchOut = null;
        
        foreach ($movements as $movement) {
            // Only consider office and field movements for total hours calculation
            if (in_array($movement->movement_type, ['office', 'field'])) {
                if ($movement->movement_action === 'in' && !$firstPunchIn) {
                    $firstPunchIn = $movement->time;
                }
                if ($movement->movement_action === 'out') {
                    $lastPunchOut = $movement->time;
                }
            }
        }
        
        // If no punch-in found, return 0
        if (!$firstPunchIn) {
            return 0;
        }
        
        // If no punch-out found, calculate until now
        if (!$lastPunchOut) {
            $lastPunchOut = Carbon::now();
        }
        
        // Calculate total minutes from first punch-in to last punch-out
        $totalMinutes = $firstPunchIn->diffInMinutes($lastPunchOut);
        
        return $totalMinutes / 60;
    }


    /**
     * Automatically punch out from field work when starting office work
     */
    private function autoPunchOutField($attendance): void
    {
        $fieldInMovement = Movement::where('attendance_id', $attendance->id)
            ->where('movement_type', 'field')
            ->where('movement_action', 'in')
            ->first();

        if ($fieldInMovement) {
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
            ->where('movement_action', 'in')
            ->first();

        if ($officeInMovement) {
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
     * Calculate greatest circle distance between two coordinates
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     * @param float $earthRadius
     * @return float Distance in meters
     */
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
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
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        // Check if user is authenticated via Auth facade (super admin)
        if (Auth::check()) {
            return Auth::user();
        }
        
        // Check if user is authenticated via session (tenant users)
        if (session()->has('user_id')) {
            $userId = session('user_id');
            $userName = session('user_name');
            $userRole = session('user_role');
            $tenantId = session('tenant_id');
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user; // Return the actual user model with real is_worklog value
                }
            } catch (\Exception $e) {
                // If user not found, create a mock user with is_worklog = false
            }
            
            // Create a mock user object for tenant users (fallback)
            return new class($userId, $userName, $userRole, $tenantId) {
                public $id;
                public $name;
                public $role_id;
                public $tenant_id;
                public $is_worklog;
                public $created_at;
                
                public function __construct($id, $name, $roleId, $tenantId) {
                    $this->id = $id;
                    $this->name = $name;
                    $this->role_id = $roleId;
                    $this->tenant_id = $tenantId;
                    $this->is_worklog = false; // Default to false for safety
                    $this->created_at = now(); // Default to current time
                }
            };
        }
        
        return null;
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
     * Save task reminder response
     */
    public function saveTaskReminderResponse(Request $request): JsonResponse
    {
        $request->validate([
            'response' => 'required|boolean',
            'punch_type' => 'nullable|in:in,out'
        ]);

        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        DB::table('task_reminder_responses')->insert([
            'user_id' => $user->id,
            'response' => $request->response,
            'response_date' => Carbon::today(),
            'punch_type' => $request->punch_type,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Response saved successfully'
        ]);
    }

}
