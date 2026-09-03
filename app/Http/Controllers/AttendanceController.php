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
use App\Services\AttendanceReportService;

class AttendanceController extends Controller
{
    protected $reportService;

    public function __construct(AttendanceReportService $reportService)
    {
        $this->reportService = $reportService;
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
            $hasAttendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $dateStr)
                ->exists();

            if (!$hasAttendance) {
                // If no attendance, we only skip if it's a Weekoff or Holiday
                $isHoliday = Holiday::where('holiday_date', $dateStr)->exists();
                
                // Dynamic weekoff from Shift
                $isWeekoff = false;
                $employee = $user->employee;
                $shift = $employee ? $employee->getShiftForDate($dateStr) : null;
                if ($shift && is_array($shift->week_offs)) {
                    // Carbon dayOfWeek returns 0 (Sunday) to 6 (Saturday)
                    $isWeekoff = in_array($checkDate->dayOfWeek, $shift->week_offs);
                } else {
                    // Fallback to hardcoded Sunday if no shift or weekoffs defined
                    $isWeekoff = $checkDate->dayOfWeek === Carbon::SUNDAY;
                }
                
                if ($isHoliday || $isWeekoff) {
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
        
        $today = Carbon::today('Asia/Kolkata');
        
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
                $shift = $employee ? $employee->getShiftForDate($today->format('Y-m-d')) : null;
                Log::info('Late check: employee + shift lookup', [
                    'user_id' => $user->id,
                    'employee_id' => $employee ? $employee->id : null,
                    'has_shift' => $shift ? true : false,
                ]);

                if ($shift && $shift->start_time) {
                    $shiftStart = Carbon::parse($today->format('Y-m-d') . ' ' . $shift->start_time, 'Asia/Kolkata');
                    $allowedLateMinutes = (int) ($shift->late_min ?? 0);
                    $cutoffTime = $shiftStart->copy()->addMinutes($allowedLateMinutes);
                    $now = Carbon::now('Asia/Kolkata');

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
                        // Check if user already has an approved or pending leave for today
                        // (which would exempt them from the late calculation and allowance check)
                        $hasLeaveToday = LeaveRequest::where('user_id', $user->id)
                            ->where('start_date', '<=', $today->toDateString())
                            ->where('end_date', '>=', $today->toDateString())
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();

                        if ($hasLeaveToday) {
                            // If they have a leave, we don't record late minutes
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
                        }

                        Log::info('Late check: timing comparison', [
                            'user_id' => $user->id,
                            'shift_start' => $shiftStart->toDateTimeString(),
                            'late_min' => $allowedLateMinutes,
                            'cutoff_time' => $cutoffTime->toDateTimeString(),
                            'now' => $now->toDateTimeString(),
                            'is_late' => true,
                            'late_minutes' => $lateMinutesToRecord,
                            'has_leave' => $hasLeaveToday
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
            Log::info('Smart Early Return detected', [
                'user_id' => $user->id,
                'leave_id' => $overlappingLeave->id,
                'date' => $today->toDateString()
            ]);
        }

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
        
        $today = Carbon::today('Asia/Kolkata');

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

        // --- Location Validation Start ---
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
                $isWFH = $request->boolean('work_from_home');
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
        // --- Location Validation End ---

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
        
        $today = Carbon::today('Asia/Kolkata');
        
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
        
        $today = Carbon::today('Asia/Kolkata');
        
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
        $today = Carbon::today('Asia/Kolkata');
        
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
        $user = $this->getCurrentUser();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('attendance.history')) {
            abort(403, 'Unauthorized');
        }
        return view('attendance.history');
    }

    public function getHistoryData(Request $request): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Eager load relation to ensure parity with _fetchUserReportData logic
        $user->loadMissing(['employee.shiftHistory.shift']);

        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('attendance.history')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        
        $perPage = $request->get('per_page', 15);
        $month = $request->get('month', Carbon::now('Asia/Kolkata')->format('Y-m'));
        
        try {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } catch (\Exception $e) {
            $startDate = Carbon::now('Asia/Kolkata')->startOfMonth();
            $endDate = Carbon::now('Asia/Kolkata')->endOfMonth();
        }

        $attendances = Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();
        
        // Fetch leaves and holidays for summary
        $holidaysData = Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->holiday_date->format('Y-m-d');
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

        $summary = $this->reportService->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $user);
        
        $shift = $user->employee->shiftRelation ?? null;
        $dailyBreakdown = $this->reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leavesDetails, $holidaysData, $user);

        return response()->json([
            'attendances' => $dailyBreakdown,
            'summary' => $summary
        ]);
    }

    public function getAttendanceStats(): JsonResponse
    {
        $user = $this->getCurrentUser();
        $today = Carbon::today('Asia/Kolkata');
        $thisMonth = Carbon::now('Asia/Kolkata')->startOfMonth();
        
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
            $stats['today_hours'] = $this->reportService->calculateTotalHours($todayAttendance->movements, null, $todayAttendance->date);
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
            $totalMonthHours += $this->reportService->calculateTotalHours($attendance->movements, null, $attendance->date);
            $totalLateMinutes += (int) abs($attendance->late_minutes ?? 0);
        }
        
        $stats['month_hours'] = $totalMonthHours;
        $stats['total_late_minutes'] = $totalLateMinutes;
        
        // Late Allowance
        $lateAllowance = 0;
        if ($user && $user->employee) {
            $shift = $user->employee->getShiftForDate($today->format('Y-m-d'));
            if ($shift) {
                $lateAllowance = (int) $shift->min_per_month_late_allow;
            }
        }
        $stats['late_allowance'] = $lateAllowance;

        $stats['avg_hours_per_day'] = $stats['total_days'] > 0 ? round($totalMonthHours / $stats['total_days'], 2) : 0;

        return response()->json($stats);
    }





















    public function reportView()
    {
        // Get all users who have any attendance records
        $userIdsWithAttendance = \App\Models\Attendance::pluck('user_id')->unique()->toArray();

        // Get all users for the dropdown - including active employees and inactive employees with attendance
        $users = User::select('id', 'name', 'email')
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();
            
        return view('attendance.report', compact('users'));
    }

    private function _fetchUserReportData($userId, $month)
    {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $user = \App\Models\User::with(['employee.shiftHistory.shift'])->find($userId);
        if (!$user || !$user->is_attendance) {
            return null;
        }

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('user_id', $userId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($holiday) {
                return $holiday->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })->get();

        $leaves = [];
        foreach ($leavesRaw as $leave) {
            $lStart = \Carbon\Carbon::parse($leave->start_date)->max($startDate);
            $lEnd = \Carbon\Carbon::parse($leave->end_date)->min($endDate);
            
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            
            $curr = $lStart->copy();
            while ($curr->lte($lEnd)) {
                $leaves[$curr->format('Y-m-d')] = $lType;
                $curr->addDay();
            }
        }

        $dailyData = $this->reportService->generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData, $user);
        
        $leavesList = array_map(function($k, $v) { return $v; }, array_keys($leaves), $leaves);
        $formattedLeavesForSummary = array_combine(array_keys($leaves), $leavesList);

        $summary = $this->reportService->calculateMonthlySummary($attendances, $startDate, $endDate, $holidays, $formattedLeavesForSummary, $holidaysData, $user);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'designation' => $user->employee->designation->name ?? 'N/A'
            ],
            'daily_status' => $dailyData,
            'summary' => $summary
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
        $dateObj = \Carbon\Carbon::parse($date);
        $dateStr = $dateObj->format('Y-m-d');

        $userIdsWithAttendance = \App\Models\Attendance::where('date', $dateStr)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        $attendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->where('date', $dateStr)
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::where('holiday_date', $dateStr)->get()->keyBy(function($holiday) {
            return $holiday->holiday_date->format('Y-m-d');
        });
        $holidays = $holidaysData->keys()->toArray();

        $leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function($query) use ($dateStr) {
                $query->where('start_date', '<=', $dateStr)
                      ->where('end_date', '>=', $dateStr);
            })->get();

        $userLeaves = [];
        foreach ($leavesRaw as $leave) {
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            $userLeaves[$leave->user_id] = $lType;
        }

        $reportData = [];
        foreach ($users as $user) {
            $userAttendance = $attendances->get($user->id, collect());
            
            $leaveArr = isset($userLeaves[$user->id]) ? [$dateStr => $userLeaves[$user->id]] : [];
            
            $dailyData = $this->reportService->generateDailyBreakdown($userAttendance, $dateObj, $dateObj, $holidays, $leaveArr, $holidaysData, $user);
            $dayData = $dailyData[0];
            
            $reportData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'designation' => $user->employee->designation->name ?? 'N/A'
                ],
                'data' => $dayData
            ];
        }

        return [
            'date' => $dateObj->format('M j, Y'),
            'summary' => [
                'total_working' => count($users),
                'total_present' => collect($reportData)->whereIn('data.code', ['P', 'P2', 'W/O-W', 'H/W'])->count(),
                'total_absent' => collect($reportData)->where('data.code', 'A')->count(),
                'total_leaves' => collect($reportData)->whereIn('data.code', ['L', 'HD', 'SL', 'RH', 'LWP'])->count(),
                'total_na' => collect($reportData)->where('data.code', 'NA')->count()
            ],
            'users' => $reportData
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
            $header = array_merge($header, ['Work Days', 'Total Present', 'Full Day', 'Half Day', 'Sunday Work', 'Holiday Work', 'Leave', 'Absent', 'Less Shift Hr', 'More Shift Hr', 'Late Count', 'Late Min']);
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
        
        $hide_status = $request->has('hide_status');

        $pdf = Pdf::loadView('attendance.date-report-pdf', compact('data', 'hide_status'))
                ->setPaper('a4', 'landscape');
                
        return $pdf->download("date_attendance_report_{$request->date}.pdf");
    }

    private function _fetchMonthlyReportData($month)
    {
        $startDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $dates = [];
        $curr = $startDate->copy();
        while ($curr->lte($endDate)) {
            $dates[] = [
                'date' => $curr->format('Y-m-d'),
                'day' => $curr->format('d'),
                'day_name' => $curr->format('D'),
                'is_sunday' => $curr->dayOfWeek === \Carbon\Carbon::SUNDAY
            ];
            $curr->addDay();
        }

        $userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('user_id')
            ->unique()
            ->toArray();

        $users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->whereHas('employee', function($q) {
                    $q->where('status', 'active');
                })->orWhereIn('id', $userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        $allAttendances = \App\Models\Attendance::with(['movements' => function($query) {
                $query->orderBy('time');
            }])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        $holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function($item) {
                return $item->holiday_date->format('Y-m-d');
            });
        $holidays = $holidaysData->keys()->toArray();

        $allLeavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                            ->where('end_date', '>=', $endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })->get();

        $userLeaves = [];
        foreach ($allLeavesRaw as $leave) {
            $lStart = \Carbon\Carbon::parse($leave->start_date)->max($startDate);
            $lEnd = \Carbon\Carbon::parse($leave->end_date)->min($endDate);
            
            $lType = 'L';
            if ($leave->is_half_day) $lType = 'HD';
            elseif ($leave->is_sl) $lType = 'SL';
            elseif ($leave->is_rh) $lType = 'RH';
            elseif ($leave->leaveType && strtolower($leave->leaveType->name) === 'lwp') $lType = 'LWP';
            
            $currL = $lStart->copy();
            while ($currL->lte($lEnd)) {
                $userLeaves[$leave->user_id][$currL->format('Y-m-d')] = $lType;
                $currL->addDay();
            }
        }

        $usersData = [];
        foreach ($users as $user) {
            $userAttendances = $allAttendances->get($user->id, collect());
            $userLeavesDetails = $userLeaves[$user->id] ?? [];
            
            $dailyData = $this->reportService->generateDailyBreakdown($userAttendances, $startDate, $endDate, $holidays, $userLeavesDetails, $holidaysData, $user);
            
            $dailyStatuses = [];
            foreach ($dailyData as $d) {
                $dailyStatuses[] = [
                    'date' => $d['date'],
                    'code' => $d['code'],
                    'class' => $d['class']
                ];
            }
            
            $leavesList = array_map(function($k, $v) { return $v; }, array_keys($userLeavesDetails), $userLeavesDetails);
            $formattedLeavesForSummary = array_combine(array_keys($userLeavesDetails), $leavesList);

            $summary = $this->reportService->calculateMonthlySummary($userAttendances, $startDate, $endDate, $holidays, $formattedLeavesForSummary, $holidaysData, $user);

            $usersData[] = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'designation' => $user->employee->designation->name ?? 'N/A'
                ],
                'daily_status' => $dailyStatuses,
                'summary' => $summary
            ];
        }
        
        return [
            'month' => [
                'display' => $startDate->format('F Y'),
                'value' => $month
            ],
            'dates' => $dates,
            'users' => $usersData
        ];
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
