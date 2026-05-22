<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Movement;
use App\Models\Worklog;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KioskAttendanceController extends Controller
{
    /**
     * Check if user can perform attendance actions based on worklog completion
     * Users with isWorklog = 1 must complete previous day's worklog before attendance
     */
    private function canPerformAttendanceAction($user)
    {
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
                
                $checkDate->addDay();
                continue;
            }

            $hasWorklogEntry = Worklog::where('user_id', $user->id)
                ->where('work_date', $dateStr)
                ->exists();
            
            $hasLeave = LeaveRequest::where('user_id', $user->id)
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

    /**
     * Get active embeddings for sync to the edge device
     */
    public function getEmbeddings(): JsonResponse
    {
        try {
            $employees = Employee::with('user')
                ->where('is_face_enrolled', 1)
                ->whereNotNull('face_embeddings')
                ->get(['id', 'name', 'face_embeddings']);

            $formattedData = $employees->map(function ($emp) {
                return [
                    'employee_id' => $emp->id,
                    'user_id' => $emp->user ? $emp->user->id : null,
                    'name' => trim($emp->name),
                    'embeddings' => json_decode($emp->face_embeddings) // Decodes vector array
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching embeddings (Kiosk)', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch embeddings.'
            ], 500);
        }
    }

    /**
     * Process a kiosk punch-in for an identified user ID
     */
    public function punchInByKiosk(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'confidence_match' => 'nullable|numeric',
            'device_name' => 'nullable|string'
        ]);

        try {
            $userId = $request->user_id;
            $today = Carbon::today('Asia/Kolkata');
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Worklog check
            $attendanceCheck = $this->canPerformAttendanceAction($user);
            if (!$attendanceCheck['can_perform']) {
                return response()->json([
                    'success' => false,
                    'message' => $attendanceCheck['message']
                ], 403);
            }

            // Retrieve attendance record or create if missing
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $userId, 'date' => $today->toDateString()],
                ['is_emergency' => 0, 'late_minutes' => 0]
            );

            // Security validation: Prevent duplicate punch-ins within 2 minutes
            $recentMovement = Movement::where('attendance_id', $attendance->id)
                ->orderBy('time', 'desc')
                ->first();

            if ($recentMovement) {
                // Use created_at to avoid timezone casting issues with the 'time' column
                $minutesSinceLastPunch = $recentMovement->created_at->diffInMinutes(now());
                
                Log::info('Kiosk Punch Cooldown Check', [
                    'last_punch_created_at' => $recentMovement->created_at->toIso8601String(),
                    'now' => now()->toIso8601String(),
                    'minutes_passed' => $minutesSinceLastPunch
                ]);
                
                if ($minutesSinceLastPunch < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Action already logged recently for this employee. Please wait.'
                    ], 429);
                }
            }

            // Smart Logic: First punch is 'in', all subsequent punches on the same day are 'out'
            $action = 'in';
            $hasAnyIn = Movement::where('attendance_id', $attendance->id)
                ->where('movement_action', 'in')
                ->exists();
                
            if ($hasAnyIn) {
                $action = 'out';
            }

            // Pending Task Check on Punch Out
            if ($action === 'out') {
                $notUpdatedTasksCount = Task::where('user_id', $user->id)
                    ->where(function($query) {
                        $query->where('task_status_id', '!=', 3) // Not Completed
                              ->orWhereNull('task_status_id');
                    })
                    ->where(function($query) {
                        $query->where('is_done', 0)
                              ->orWhereNull('is_done');
                    })
                    ->whereDate('due_date', '<=', $today)
                    ->where('updated_at', '<', $today)
                    ->count();

                if ($notUpdatedTasksCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "You have {$notUpdatedTasksCount} pending task(s) that were not updated today. Please update your tasks status or add remarks before punching out."
                    ], 422);
                }
            }

            // Late Minutes & Early Return Detection on Punch In
            if ($action === 'in') {
                $hasAnyIn = Movement::where('attendance_id', $attendance->id)
                    ->where('movement_action', 'in')
                    ->exists();

                if (!$hasAnyIn) {
                    $employee = $user->employee;
                    if ($employee && $employee->shiftRelation && $employee->shiftRelation->start_time) {
                        $shift = $employee->shiftRelation;
                        $shiftStart = Carbon::parse($today->format('Y-m-d') . ' ' . $shift->start_time, 'Asia/Kolkata');
                        $allowedLateMinutes = (int) ($shift->late_min ?? 0);
                        $cutoffTime = $shiftStart->copy()->addMinutes($allowedLateMinutes);
                        $now = Carbon::now('Asia/Kolkata');

                        if ($now->greaterThan($cutoffTime)) {
                            $hasLeaveToday = LeaveRequest::where('user_id', $user->id)
                                ->where('start_date', '<=', $today->toDateString())
                                ->where('end_date', '>=', $today->toDateString())
                                ->whereIn('status', ['pending', 'approved'])
                                ->exists();

                            if (!$hasLeaveToday) {
                                $lateMinutesToRecord = (int) abs($now->diffInMinutes($cutoffTime));
                                $attendance->update(['late_minutes' => $lateMinutesToRecord]);
                            }
                        }
                    }
                }

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
                    Log::info('Smart Early Return detected (Kiosk)', [
                        'user_id' => $user->id,
                        'leave_id' => $overlappingLeave->id,
                        'date' => $today->toDateString()
                    ]);
                }
            }

            // Create Movement logged with 'kiosk' identifier
            $movement = Movement::create([
                'attendance_id' => $attendance->id,
                'movement_type' => 'office',
                'movement_action' => $action,
                'time' => Carbon::now('Asia/Kolkata'),
                'description' => "Logged via AI Kiosk (Confidence: {$request->confidence_match}%)",
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'mode' => 'kiosk',
                'device_name' => $request->device_name,
            ]);

            Log::info('Kiosk attendance recorded successfully', [
                'user_id' => $userId,
                'action' => $action,
                'time' => $movement->time
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully punched {$action} via Kiosk!",
                'employee_name' => $user ? $user->name : 'Employee',
                'action' => $action,
                'time' => Carbon::parse($movement->time)->format('h:i A')
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving Kiosk Attendance', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording attendance.'
            ], 500);
        }
    }

    /**
     * API to save face vector data from enrollment view
     */
    public function enrollFace(Request $request, $id): JsonResponse
    {
        $request->validate([
            'embeddings' => 'required|array' // Requires float array
        ]);

        try {
            $employee = Employee::findOrFail($id);
            $employee->update([
                'face_embeddings' => json_encode($request->embeddings),
                'is_face_enrolled' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Face profile enrolled successfully for ' . trim($employee->name)
            ]);
        } catch (\Exception $e) {
            Log::error('Error enrolling employee face vector', [
                'employee_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll face profile.'
            ], 500);
        }
    }
}
