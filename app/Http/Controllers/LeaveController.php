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
            'leave_type_id' => 'required', // Can be 'rh', 'sl' or an ID
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
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
        
        $isRH = $request->leave_type_id === 'rh';
        $isSL = $request->leave_type_id === 'sl';
        $actualLeaveTypeId = ($isRH || $isSL) ? null : $request->leave_type_id;

        if ($isRH) {
            // Check RH restrictions
            $empTypeId = $user->employee->employment_type_id ?? null;
            if (!$empTypeId) {
                 return response()->json(['success' => false, 'message' => 'Employment type not found for RH check.'], 422);
            }
            $employmentType = \App\Models\EmploymentType::find($empTypeId);
            if (!$employmentType || $employmentType->rh_allowed <= 0) {
                 return response()->json(['success' => false, 'message' => 'You are not eligible for any Restricted Holidays.'], 422);
            }
            $totalRH = $employmentType->rh_allowed;
            $takenRH = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('is_rh', 1)
                ->whereYear('start_date', date('Y'))
                ->whereIn('status', ['pending', 'approved'])
                ->sum('total_days');
                
            if (($takenRH + $totalDays) > $totalRH) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient RH balance. You only have " . max(0, $totalRH - $takenRH) . " days available."
                ], 422);
            }
        } elseif ($isSL) {
            if (!$request->start_time || !$request->end_time) {
                 return response()->json(['success' => false, 'message' => 'Start time and end time are required for Short Leave.'], 422);
            }
            if ($totalDays > 1) {
                 return response()->json(['success' => false, 'message' => 'Short Leaves can only be taken for a single day.'], 422);
            }
            
            $employee = $user->employee;
            if (!$employee) {
                 return response()->json(['success' => false, 'message' => 'Employee profile not found.'], 422);
            }

            $empTypeId = $employee->employment_type_id ?? null;
            $employmentType = \App\Models\EmploymentType::find($empTypeId);
            if (!$employmentType || $employmentType->sl_allowed <= 0) {
                 return response()->json(['success' => false, 'message' => 'You are not eligible for Short Leaves.'], 422);
            }

            $shift = $employee->shiftRelation;
            if (!$shift) {
                 return response()->json(['success' => false, 'message' => 'No shift assigned. Short Leave logic requires active shift hours.'], 422);
            }

            // --- Shift-Based SL Validation ---
            try {
                $reqStart = Carbon::parse($request->start_time);
                $reqEnd = Carbon::parse($request->end_time);
                
                $shiftStart = Carbon::parse($shift->start_time);
                $shiftEnd = Carbon::parse($shift->end_time);
                
                $startLimitHours = (int) ($shift->sl_start_limit ?? 0);
                $endLimitHours = (int) ($shift->sl_end_limit ?? 0);
                
                $morningMax = (clone $shiftStart)->addHours($startLimitHours);
                $eveningMin = (clone $shiftEnd)->subHours($endLimitHours);
                
                // Rule 1: Morning SL: Must be within [ShiftStart, ShiftStart + Limit]
                $isValidMorning = ($reqStart->format('H:i:s') >= $shiftStart->format('H:i:s') && $reqEnd->format('H:i:s') <= $morningMax->format('H:i:s'));
                
                // Rule 2: Evening SL: Must be within [ShiftEnd - Limit, ShiftEnd]
                $isValidEvening = ($reqStart->format('H:i:s') >= $eveningMin->format('H:i:s') && $reqEnd->format('H:i:s') <= $shiftEnd->format('H:i:s'));

                if (!$isValidMorning && !$isValidEvening) {
                    $morningWindow = $shiftStart->format('h:i A') . " - " . $morningMax->format('h:i A');
                    $eveningWindow = $eveningMin->format('h:i A') . " - " . $shiftEnd->format('h:i A');
                    
                    return response()->json([
                        'success' => false, 
                        'message' => "Invalid Short Leave time. SL is only allowed during Morning window ($morningWindow) or Evening window ($eveningWindow). Core working hours are protected."
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error validating SL timing: ' . $e->getMessage()], 422);
            }
            
            $totalSL = $employmentType->sl_allowed;
            $takenSL = \App\Models\LeaveRequest::where('user_id', $user->id)
                ->where('is_sl', 1)
                ->whereYear('start_date', Carbon::parse($request->start_date)->year)
                ->whereMonth('start_date', Carbon::parse($request->start_date)->month)
                ->whereIn('status', ['pending', 'approved'])
                ->count();
                
            if ($takenSL >= $totalSL) {
                 return response()->json(['success' => false, 'message' => 'You have exhausted your Short Leave quota for this month.'], 422);
            }
            
            // Total Days for SL can be 0 or fractional. Usually we just record it as 0 to not mess up normal days ledger
            $totalDays = 0; 
        } else {
            // Verify Balance for normal leave
            $balanceService = app(LeaveBalanceService::class);
            $currentBalance = $balanceService->getBalance($user->id, $actualLeaveTypeId);

            if ($currentBalance < $totalDays) {
                 return response()->json([
                     'success' => false,
                     'message' => "Insufficient leave balance. You only have {$currentBalance} days available, but requested {$totalDays} days."
                 ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $leaveReq = LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $actualLeaveTypeId,
                'is_rh' => $isRH,
                'is_sl' => $isSL,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $isSL ? $request->start_time : null,
                'end_time' => $isSL ? $request->end_time : null,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'pending' // Automatically starts as pending for Manager workflow
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Leave application for {$totalDays} days submitted and is pending approval.",
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
            if ($leaveReq->status === 'approved' && !$leaveReq->is_rh && !$leaveReq->is_sl) {
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

            $mapped = $leaveTypes->map(function ($type) use ($balanceService, $user, $empTypeId) {
                // Fetch balance safely
                $balance = 0;
                if (Schema::hasTable('leave_ledgers')) {
                     $balance = $balanceService->getBalance($user->id, $type->id);
                }
                
                $totalAllowed = 0;
                if (!empty($empTypeId) && Schema::hasTable('employment_type_leave_rules')) {
                     $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                         ->where('leave_type_id', $type->id)
                         ->first();
                     if ($rule) {
                         $totalAllowed = $rule->value;
                     }
                }
                
                $pending = 0;
                if (Schema::hasTable('leave_requests')) {
                     $pending = \App\Models\LeaveRequest::where('user_id', $user->id)
                         ->where('leave_type_id', $type->id)
                         ->where('status', 'pending')
                         ->sum('total_days');
                }

                $type->balance = $balance;
                $type->total_allowed = $totalAllowed;
                $type->pending = $pending;
                return $type;
            });

            // Handle RH virtual type
            if (!empty($empTypeId)) {
                $employmentType = \App\Models\EmploymentType::find($empTypeId);
                if ($employmentType && $employmentType->rh_allowed > 0) {
                    $totalRH = $employmentType->rh_allowed;
                    
                    $pendingOrTakenRH = \App\Models\LeaveRequest::where('user_id', $user->id)
                        ->where('is_rh', 1)
                        ->whereYear('start_date', date('Y'))
                        ->whereIn('status', ['pending', 'approved'])
                        ->sum('total_days');
                        
                    $rhList = \App\Models\Holiday::where('is_rh', 1)
                        ->whereYear('holiday_date', date('Y'))
                        ->orderBy('holiday_date', 'asc')
                        ->get(['id', 'name', 'holiday_date']);
                        
                    $mapped->push((object)[
                        'id' => 'rh',
                        'name' => 'Restricted Holiday (RH)',
                        'balance' => max(0, $totalRH - $pendingOrTakenRH),
                        'total_allowed' => $totalRH,
                        'pending' => 0,
                        'rh_list' => $rhList
                    ]);
                }

                if ($employmentType && $employmentType->sl_allowed > 0) {
                    $totalSL = $employmentType->sl_allowed;
                    
                    $pendingOrTakenSL = \App\Models\LeaveRequest::where('user_id', $user->id)
                        ->where('is_sl', 1)
                        ->whereYear('start_date', date('Y'))
                        ->whereMonth('start_date', date('m'))
                        ->whereIn('status', ['pending', 'approved'])
                        ->count();
                        
                    $userShift = $user->employee->shift ?? null;
                    $shiftStart = $userShift ? $userShift->start_time : '09:00:00';
                    $shiftEnd = $userShift ? $userShift->end_time : '18:00:00';
                        
                    $mapped->push((object)[
                        'id' => 'sl',
                        'name' => 'Short Leave (SL)',
                        'balance' => max(0, $totalSL - $pendingOrTakenSL),
                        'total_allowed' => $totalSL,
                        'pending' => 0,
                        'shift_start' => $shiftStart,
                        'shift_end' => $shiftEnd,
                        'start_limit_hours' => $userShift->sl_start_limit ?? 0,
                        'end_limit_hours' => $userShift->sl_end_limit ?? 0,
                    ]);
                }
            }

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

    public function fetchLedger(Request $request)
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            
            if (!Schema::hasTable('leave_ledgers')) {
                return response()->json(['data' => []]);
            }

            $query = \App\Models\LeaveLedger::with(['leaveType'])->where('user_id', $user->id);
            
            if ($request->has('leave_type_id') && $request->leave_type_id) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            $ledger = $query->orderBy('created_at', 'desc')->get();

            return response()->json(['success' => true, 'data' => $ledger]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    /**
     * Display approvals view.
     */
    public function approvals()
    {
        return view('leave.approvals');
    }

    /**
     * Fetch all leaves for approval.
     */
    public function fetchApprovals()
    {
        try {
            $user = $this->getCurrentUser();
            if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            
            if (!Schema::hasTable('leave_requests')) {
                return response()->json(['data' => []]);
            }

            if ($user->role_id == 1) {
                // Admin: Show leaves from users who have no manager
                $leaves = LeaveRequest::with(['leaveType', 'user'])
                    ->whereIn('status', ['pending', 'approved', 'rejected'])
                    ->whereHas('user', function($query) {
                        $query->whereDoesntHave('managers');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Manager: Show leaves from their mapped subordinates
                // We fetch subordinate IDs manually to perfectly isolate tenant SQL mappings
                $actualUser = clone $user; 
                try { $actualUser = \App\Models\User::find($user->id); } catch(\Exception $e) {}
                
                $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];

                if (!empty($subordinateIds)) {
                    $leaves = LeaveRequest::with(['leaveType', 'user'])
                        ->whereIn('status', ['pending', 'approved', 'rejected'])
                        ->whereIn('user_id', $subordinateIds)
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {
                    $leaves = collect([]);
                }
            }

            return response()->json(['data' => $leaves]);
        } catch (\Exception $e) {
            \Log::error('fetchLeaveApprovals Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response()->json(['data' => []]);
        }
    }

    /**
     * Approve leave request
     */
    public function approve($id)
    {
        $user = $this->getCurrentUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);

        try {
            if ($user->role_id == 1) {
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereHas('user', function($query) {
                        $query->whereDoesntHave('managers');
                    })->firstOrFail();
            } else {
                $actualUser = clone $user; 
                try { $actualUser = \App\Models\User::find($user->id); } catch(\Exception $e) {}
                
                $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereIn('user_id', $subordinateIds)
                    ->firstOrFail();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Leave not found or unauthorized.'], 404);
        }

        if ($leaveReq->status !== 'pending') return response()->json(['success' => false, 'message' => 'Leave is already ' . $leaveReq->status], 422);

        DB::beginTransaction();
        try {
            $leaveReq->status = 'approved';
            $leaveReq->approved_by = $user->id;
            $leaveReq->save();

            if (!$leaveReq->is_rh && !$leaveReq->is_sl) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->debitLeave(
                    $leaveReq->user_id, 
                    $leaveReq->leave_type_id, 
                    $leaveReq->total_days, 
                    $leaveReq, 
                    'Leave deduction upon approval'
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Leave approved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to approve leave: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject leave request
     */
    public function reject($id)
    {
        $user = $this->getCurrentUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);

        try {
            if ($user->role_id == 1) {
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereHas('user', function($query) {
                        $query->whereDoesntHave('managers');
                    })->firstOrFail();
            } else {
                $actualUser = clone $user; 
                try { $actualUser = \App\Models\User::find($user->id); } catch(\Exception $e) {}
                
                $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereIn('user_id', $subordinateIds)
                    ->firstOrFail();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Leave not found or unauthorized.'], 404);
        }

        if ($leaveReq->status !== 'pending') return response()->json(['success' => false, 'message' => 'Leave is not pending'], 422);

        $leaveReq->status = 'rejected';
        $leaveReq->approved_by = $user->id;
        $leaveReq->save();

        return response()->json(['success' => true, 'message' => 'Leave rejected successfully']);
    }
}
