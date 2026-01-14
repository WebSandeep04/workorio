<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\EntryType;
use App\Models\Worklog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('leave.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'leave_type_id' => 'required|exists:entry_types,id',
            'reason' => 'nullable|string|max:1000']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
        
        // Check if leave already exists for this date
        $existingLeave = Leave::where('user_id', $user->id)
            ->where('date', $request->date)
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
            $leave = Leave::create([
                'user_id' => $user->id,
                'date' => $request->date,
                'leave_type_id' => $request->leave_type_id,
                'reason' => $request->reason,
                'status' => 'approved' // Leaves are automatically approved
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave applied successfully.',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply leave. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'leave_type_id' => 'required|exists:entry_types,id',
            'reason' => 'nullable|string|max:1000']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
        $leave = Leave::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leave) {
            return response()->json([
                'success' => false,
                'message' => 'Leave not found.'
            ], 404);
        }



        // Check if leave already exists for this date (excluding current leave)
        $existingLeave = Leave::where('user_id', $user->id)
            ->where('date', $request->date)
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
                'date' => $request->date,
                'leave_type_id' => $request->leave_type_id,
                'reason' => $request->reason]);

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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
        $leave = Leave::where('id', $id)
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

    /**
     * Fetch leaves for the authenticated user.
     */
    public function fetch()
    {
        try {
            $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
            
            // Check if leaves table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('leaves')) {
                return response()->json(['data' => []]);
            }

            $leaves = Leave::with(['leaveType'])
                ->where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->get();

            return response()->json([
                'data' => $leaves
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => []
            ]);
        }
    }

    /**
     * Fetch leave types (entry types with working_hours = 0).
     */
    public function fetchLeaveTypes()
    {
        try {
            $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }
            
            // Check if entry_types table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('entry_types')) {
                return response()->json(['data' => []]);
            }

            $leaveTypes = EntryType::where('working_hours', 0)
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $leaveTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => []
            ]);
        }
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
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user; // Return the actual user model
                }
            } catch (\Exception $e) {
                // If user not found, return null
            }
        }
        
        return null;
    }
}
