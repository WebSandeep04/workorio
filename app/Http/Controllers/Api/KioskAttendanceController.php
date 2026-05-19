<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KioskAttendanceController extends Controller
{
    /**
     * Get active embeddings for sync to the edge device
     */
    public function getEmbeddings(): JsonResponse
    {
        try {
            $employees = Employee::with('user')
                ->where('is_face_enrolled', 1)
                ->whereNotNull('face_embeddings')
                ->get(['id', 'user_id', 'name', 'face_embeddings']);

            $formattedData = $employees->map(function ($emp) {
                return [
                    'employee_id' => $emp->id,
                    'user_id' => $emp->user_id,
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
            'confidence_match' => 'nullable|numeric'
        ]);

        try {
            $userId = $request->user_id;
            $today = Carbon::today('Asia/Kolkata');

            // Retrieve attendance record or create if missing
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $userId, 'date' => $today->toDateString()],
                ['is_emergency' => 0, 'late_minutes' => 0]
            );

            // Security validation: Prevent duplicate punch-ins within 2 minutes
            $recentMovement = Movement::where('attendance_id', $attendance->id)
                ->orderBy('time', 'desc')
                ->first();

            if ($recentMovement && Carbon::parse($recentMovement->time)->diffInMinutes(Carbon::now('Asia/Kolkata')) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action already logged recently for this employee. Please wait.'
                ], 429);
            }

            // Smart Toggling Logic (In or Out depending on last movement)
            $action = 'in';
            if ($recentMovement && $recentMovement->movement_action === 'in') {
                $action = 'out';
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
            ]);

            $user = \App\Models\User::find($userId);

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
