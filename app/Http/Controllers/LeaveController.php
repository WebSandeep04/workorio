<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Worklog;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        if (!Schema::hasTable('leave_requests')) {
            return response()->json(['success' => false, 'message' => 'System migration pending.'], 500);
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->getCurrentUser();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
        
        // Calculate Total Days
        try {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $totalDays = $start->diffInDays($end) + 1; // Assuming full days for now
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Invalid date format.'], 422);
        }

        // Check Overlaps
        $overlappingLeave = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->where('start_date', '<=', $request->end_date)
                      ->where('end_date', '>=', $request->start_date);
            })
            ->first();

        if ($overlappingLeave) {
            return response()->json(['success' => false, 'message' => 'You already have an active leave request overlapping these dates.'], 422);
        }

        // Verify Balance
        $balanceService = app(LeaveBalanceService::class);
        $currentBalance = $balanceService->getBalance($user->id, $request->leave_type_id);

        if ($currentBalance < $totalDays) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient leave balance. You only have {$currentBalance} days available, but requested {$totalDays} days."
            ], 422);
        }

        DB::beginTransaction();
        try {
            $leaveReq = LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'approved' // Automatically approved right now per prior implementation standard
            ]);

            // Deduct the balance
            $balanceService->debitLeave(
                $user->id, 
                $request->leave_type_id, 
                $totalDays, 
                $leaveReq, 
                'Automated Leave deduction for approved request via portal'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Leave application for {$totalDays} days submitted and processed successfully.",
                'data' => $leaveReq
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to apply leave: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $this->getCurrentUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);

        $leaveReq = LeaveRequest::where('id', $id)->where('user_id', $user->id)->first();

        if (!$leaveReq) return response()->json(['success' => false, 'message' => 'Leave request not found.'], 404);

        if ($leaveReq->status === 'approved') {
             return response()->json(['success' => false, 'message' => 'Cannot edit an already approved and debited leave request. Please cancel it instead.'], 422);
        }

        try {
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $totalDays = $start->diffInDays($end) + 1; 

            $leaveReq->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_days' => $totalDays,
                'reason' => $request->reason
            ]);

            return response()->json(['success' => true, 'message' => 'Leave request updated successfully.', 'data' => $leaveReq]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update leave.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);

        $leaveReq = LeaveRequest::where('id', $id)->where('user_id', $user->id)->first();
        if (!$leaveReq) return response()->json(['success' => false, 'message' => 'Leave not found.'], 404);

        DB::beginTransaction();
        try {
            if ($leaveReq->status === 'approved') {
                // Refund the ledger
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->creditLeave(
                    $leaveReq->user_id,
                    $leaveReq->leave_type_id,
                    $leaveReq->total_days,
                    $leaveReq,
                    'Refund for cancelled leave'
                );
            }
            
            $leaveReq->update(['status' => 'cancelled']); // Keep audit trail instead of deleting fully
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Leave cancelled and balance refunded successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete leave.'], 500);
        }
    }

    /**
     * Fetch leaves for the authenticated user.
     */
    public function fetch()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            
            if (!Schema::hasTable('leave_requests')) {
                return response()->json(['data' => []]);
            }

            $leaves = LeaveRequest::with(['leaveType'])
                ->where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json(['data' => $leaves]);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }

    /**
     * Fetch valid active leave types along with user balances.
     */
    public function fetchLeaveTypes()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            
            if (!Schema::hasTable('leave_types')) {
                return response()->json(['data' => []]);
            }

            $query = LeaveType::where('status', true)->orderBy('name');

            // Get the employee's employment type safely
            $empTypeId = $user->employee->employment_type_id ?? null;

            if (!empty($empTypeId) && Schema::hasTable('employment_type_leave_rules')) {
                $allowedLeaveIds = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                    ->pluck('leave_type_id');
                $query->whereIn('id', $allowedLeaveIds);
            }

            $leaveTypes = $query->get();
            $balanceService = app(LeaveBalanceService::class);

            $mapped = $leaveTypes->map(function ($type) use ($balanceService, $user) {
                // Fetch balance safely
                $balance = 0;
                if (Schema::hasTable('leave_ledgers')) {
                     $balance = $balanceService->getBalance($user->id, $type->id);
                }
                $type->balance = $balance;
                return $type;
            });

            return response()->json(['data' => $mapped]);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }

    /**
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        if (Auth::check()) {
            return Auth::user();
        }
        if (session()->has('user_id')) {
            $userId = session('user_id');
            try {
                $user = \App\Models\User::find($userId);
                if ($user) return $user;
            } catch (\Exception $e) {}
        }
        return null;
    }
}
