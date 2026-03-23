<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Worklog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        return auth()->user();
    }

    /**
     * Fetch all leaves for the authenticated user
     */
    public function index(): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        // Check if leaves table exists
        if (!DB::getSchemaBuilder()->hasTable('leave_requests')) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $leaves = LeaveRequest::with(['leaveType'])
            ->where('user_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $leaves
        ]);
    }

    /**
     * Fetch leave types (entry types with working_hours = 0)
     */
    public function getLeaveTypes(): JsonResponse
    {
        // Check if leave_types table exists
        if (!DB::getSchemaBuilder()->hasTable('leave_types')) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $leaveTypes = LeaveType::where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $leaveTypes
        ]);
    }

    /**
     * Store a new leave request
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->getCurrentUser();

        // Check if leave already exists for this date
        $existingLeave = LeaveRequest::where('user_id', $user->id)
            ->where('start_date', '<=', $request->date)
            ->where('end_date', '>=', $request->date)
            ->where('status', '!=', 'rejected')
            ->first();

        if ($existingLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave already exists for this date.'
            ], 422);
        }

        // Check if worklog exists for this date
        $existingWorklog = Worklog::where('user_id', $user->id)
            ->where('work_date', $request->date)
            ->first();

        if ($existingWorklog) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot apply leave for a date when worklog already exists.'
            ], 422);
        }

        try {
            $leave = LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->date,
                'end_date' => $request->date,
                'total_days' => 1,
                'reason' => $request->reason,
                'status' => 'approved' // Automatically approved
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave applied successfully.',
                'data' => $leave
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply leave. Please try again.'
            ], 500);
        }
    }

    /**
     * Update an existing leave
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->getCurrentUser();
        
        $leave = LeaveRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave not found.'
            ], 404);
        }

        // Check if leave already exists for this date (excluding current leave)
        $existingLeave = LeaveRequest::where('user_id', $user->id)
            ->where('start_date', '<=', $request->date)
            ->where('end_date', '>=', $request->date)
            ->where('status', '!=', 'rejected')
            ->where('id', '!=', $id)
            ->first();

        if ($existingLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave already exists for this date.'
            ], 422);
        }

        try {
            $leave->update([
                'start_date' => $request->date,
                'end_date' => $request->date,
                'total_days' => 1,
                'leave_type_id' => $request->leave_type_id,
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave updated successfully.',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update leave. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a leave
     */
    public function destroy($id): JsonResponse
    {
        $user = $this->getCurrentUser();
        
        $leave = LeaveRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave not found.'
            ], 404);
        }

        try {
            $leave->delete();

            return response()->json([
                'success' => true,
                'message' => 'Leave deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete leave. Please try again.'
            ], 500);
        }
    }
}
