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
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveStatusMail;

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
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type_id' => 'required', // Can be 'rh', 'sl' or an ID
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'sl_period' => 'nullable|in:morning,evening',
            'is_half_day' => 'nullable|boolean',
            'half_day_period' => 'nullable|in:pre_lunch,post_lunch',
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
        
        $leaveType = LeaveType::find($request->leave_type_id);
        if (!$leaveType) {
            return response()->json(['success' => false, 'message' => 'Invalid leave type selected.'], 422);
        }

        // Calculate Total Days based on Dynamic Weights
        try {
            $isHalfDay = $request->boolean('is_half_day');
            
            if ($isHalfDay) {
                if (!$leaveType->allow_half_day) {
                    return response()->json(['success' => false, 'message' => "Half days are not allowed for {$leaveType->name}."], 422);
                }
                $totalDays = (float) $leaveType->half_day_weight;
                $request->merge(['end_date' => $request->start_date]);
            } else {
                $start = Carbon::parse($request->start_date);
                $end = Carbon::parse($request->end_date);
                $dayCount = $start->diffInDays($end) + 1;
                $totalDays = $dayCount * (float) $leaveType->full_day_weight;
            }
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Invalid date format.'], 422);
        }

        // Short Leave Logic (Dynamic)
        if ($leaveType->is_short_leave) {
            $employee = $user->employee;
            if (!$employee || !$employee->shiftRelation) {
                return response()->json(['success' => false, 'message' => 'Active shift required for Short Leave logic.'], 422);
            }

            $shift = $employee->shiftRelation;
            try {
                $shiftEnd = Carbon::parse($shift->end_time);
                $endLimitHours = (int) ($shift->sl_end_limit ?? 0);
                $eveningMin = (clone $shiftEnd)->subHours($endLimitHours);

                $request->merge(['sl_period' => 'evening']);
                $request->merge([
                    'start_time' => $eveningMin->format('H:i'),
                    'end_time' => $shiftEnd->format('H:i')
                ]);

                $reqStart = Carbon::parse($request->start_time);
                $reqEnd = Carbon::parse($request->end_time);
                
                $isValidEvening = ($reqStart->format('H:i:s') >= $eveningMin->format('H:i:s') && $reqEnd->format('H:i:s') <= $shiftEnd->format('H:i:s'));

                if (!$isValidEvening) {
                    $eveningWindow = $eveningMin->format('h:i A') . " - " . $shiftEnd->format('h:i A');
                    return response()->json(['success' => false, 'message' => "Short Leave is only allowed during: $eveningWindow"], 422);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error validating SL timing.'], 422);
            }
        }

        // Restricted Holiday Logic (Dynamic)
        if ($leaveType->is_restricted) {
            $isHoliday = \App\Models\Holiday::where('holiday_date', $request->start_date)->where('is_rh', 1)->exists();
            if (!$isHoliday) {
                return response()->json(['success' => false, 'message' => 'Selected date is not a valid Restricted Holiday.'], 422);
            }
        }

        // Verify Balance/Quota (Dynamic based on Monthly/Yearly)
        if ($leaveType->is_deductible || $leaveType->is_restricted || $leaveType->is_short_leave) {
            $empTypeId = $user->employee->employment_type_id ?? null;
            $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                ->where('leave_type_id', $leaveType->id)
                ->first();
            $maxAllowed = $rule ? (float) $rule->value : 0;

            if ($leaveType->quota_type === 'monthly') {
                // Monthly logic: Check usage in the current month
                $usage = LeaveRequest::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereYear('start_date', Carbon::parse($request->start_date)->year)
                    ->whereMonth('start_date', Carbon::parse($request->start_date)->month)
                    ->count(); // Typically Monthly types like SL are count-based

                if (($usage + 1) > $maxAllowed) {
                    return response()->json(['success' => false, 'message' => "Monthly limit reached. You are allowed {$maxAllowed} per month."], 422);
                }
            } else {
                // Yearly/Standard logic: Check ledger balance for deductible leaves
                if ($leaveType->is_deductible) {
                    $balanceService = app(LeaveBalanceService::class);
                    $currentBalance = $balanceService->getBalance($user->id, $leaveType->id);

                    if ($currentBalance < $totalDays) {
                         return response()->json(['success' => false, 'message' => "Insufficient balance. Available: {$currentBalance}, Requested: {$totalDays}"], 422);
                    }
                } else if ($leaveType->is_restricted) {
                    // Restricted Holiday logic (yearly count)
                    $usage = LeaveRequest::where('user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->whereYear('start_date', Carbon::parse($request->start_date)->year)
                        ->sum('total_days');

                    if (($usage + $totalDays) > $maxAllowed) {
                        return response()->json(['success' => false, 'message' => "Yearly RH limit reached. You have {$maxAllowed} days per year."], 422);
                    }
                }
            }
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

        DB::beginTransaction();
        try {
            $leaveReq = LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'is_rh' => $leaveType->is_restricted,
                'is_sl' => $leaveType->is_short_leave,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $leaveType->is_short_leave ? $request->start_time : null,
                'end_time' => $leaveType->is_short_leave ? $request->end_time : null,
                'sl_period' => $leaveType->is_short_leave ? $request->sl_period : null,
                'is_half_day' => $isHalfDay ? 1 : 0,
                'half_day_period' => $isHalfDay ? $request->half_day_period : null,
                'total_days' => $totalDays,
                'reason' => $request->reason,
                'status' => 'pending' // Automatically starts as pending for Manager workflow
            ]);

            // Immediate Deduction Logic
            if ($leaveType->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->debitLeave(
                    $user->id, 
                    $leaveType->id, 
                    $totalDays, 
                    $leaveReq, 
                    'Immediate deduction upon leave application'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Leave application for {$totalDays} days submitted. Balance has been deducted and request is pending approval.",
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
            // Refund the ledger if the leave was already deducted (Pending or Approved)
            if (in_array($leaveReq->status, ['pending', 'approved']) && $leaveReq->leaveType && $leaveReq->leaveType->is_deductible) {
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

    public function curtail(Request $request, $id)
    {
        $request->validate([
            'resume_date' => 'required|date'
        ]);

        $user = $this->getCurrentUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);

        $leaveReq = LeaveRequest::with('leaveType')->findOrFail($id);
        
        // Authorization: Owner or Admin or Manager
        $isAuthorized = ($leaveReq->user_id == $user->id || $user->role_id == 1);
        
        if (!$isAuthorized) {
            // Check if user is a manager of the leave owner
            $actualUser = \App\Models\User::find($user->id);
            $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
            if (in_array($leaveReq->user_id, $subordinateIds)) {
                $isAuthorized = true;
            }
        }

        if (!$isAuthorized) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }
        
        if ($leaveReq->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved leaves can be curtailed.'], 422);
        }

        DB::beginTransaction();
        try {
            $resumeDate = Carbon::parse($request->resume_date);
            $startDate = Carbon::parse($leaveReq->start_date);

            // Days taken is the count of days from start until the day BEFORE resumption
            $daysCount = $startDate->diffInDays($resumeDate);
            if ($resumeDate->lt($startDate)) $daysCount = 0;

            $type = $leaveReq->leaveType;
            $weight = $type ? (float)$type->full_day_weight : 1.0;
            $newTotalDays = $daysCount * $weight;

            $refundAmount = (float)$leaveReq->total_days - $newTotalDays;

            if ($refundAmount > 0 && $type && $type->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->creditLeave(
                    $leaveReq->user_id,
                    $leaveReq->leave_type_id,
                    $refundAmount,
                    $leaveReq,
                    "Refund for early return on {$request->resume_date} (Original: {$leaveReq->total_days}, New: {$newTotalDays})"
                );
            }

            $leaveReq->update([
                'end_date' => $resumeDate->copy()->subDay()->toDateString(),
                'total_days' => $newTotalDays,
                'resumed_at' => $resumeDate->toDateTimeString(),
                'has_attendance_overlap' => false,
                'is_early_return' => 1
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Employee resumed work. Balance updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to process resumption: ' . $e->getMessage()], 500);
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
                ->where('is_early_return', 0)
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
                $balance = 0;
                $totalAllowed = 0;
                $pending = 0;

                // 1. Fetch Quota (Max Allowed) from Rules
                if (!empty($empTypeId) && Schema::hasTable('employment_type_leave_rules')) {
                    $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                        ->where('leave_type_id', $type->id)
                        ->first();
                    if ($rule) {
                        $totalAllowed = $rule->value;
                    }
                }

                // 2. Fetch Balance/Remaining based on Type
                if ($type->quota_type === 'monthly') {
                    // Monthly Balance: TotalAllowed - TakenThisMonth
                    $takenThisMonth = \App\Models\LeaveRequest::where('user_id', $user->id)
                        ->where('leave_type_id', $type->id)
                        ->whereIn('status', ['pending', 'approved'])
                        ->whereYear('start_date', now()->year)
                        ->whereMonth('start_date', now()->month)
                        ->count();
                    $balance = max(0, $totalAllowed - $takenThisMonth);
                } else {
                    // Yearly/Ledger Balance
                    if (Schema::hasTable('leave_ledgers')) {
                        $balance = $balanceService->getBalance($user->id, $type->id);
                    }
                }
                
                // 3. Fetch Pending
                if (Schema::hasTable('leave_requests')) {
                     $pending = \App\Models\LeaveRequest::where('user_id', $user->id)
                         ->where('leave_type_id', $type->id)
                         ->where('status', 'pending')
                         ->sum('total_days');
                }

                $type->balance = $balance;
                $type->total_allowed = $totalAllowed;
                $type->pending = $pending;
                
                // 4. Attach special lists (RH list) if needed
                if ($type->is_restricted) {
                    $type->rh_list = \App\Models\Holiday::where('is_rh', 1)
                        ->whereYear('holiday_date', date('Y'))
                        ->orderBy('holiday_date', 'asc')
                        ->get(['id', 'name', 'holiday_date']);
                }

                return $type;
            });

            // No more virtual types (RH/SL/HD). Everything comes from the database.
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
                // Admin sees all relevant requests
                $leaves = LeaveRequest::with(['leaveType', 'user'])
                    ->whereIn('status', ['pending', 'approved', 'rejected'])
                    ->where('is_early_return', 0)
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
                        ->where('is_early_return', 0)
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

            DB::commit();

            // Only notify if employee is active
            if ($leaveReq->user && $leaveReq->user->email && ($leaveReq->user->employee && $leaveReq->user->employee->status === 'active')) {
                try {
                    Mail::to($leaveReq->user->email)->send(new LeaveStatusMail([
                        'leave_request_id' => $leaveReq->id,
                        'user_id' => $leaveReq->user->id,
                        'status' => 'approved'
                    ]));
                } catch (\Exception $e) {
                    \Log::error("Failed to send leave approval email: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Leave approved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to approve leave: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Reject leave request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

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

        DB::beginTransaction();
        try {
            $leaveReq->status = 'rejected';
            $leaveReq->approved_by = $user->id;
            $leaveReq->reject_reason = $request->reason;
            $leaveReq->save();

            // Refund logic for rejected leaves (since they were deducted on store)
            if ($leaveReq->leaveType && $leaveReq->leaveType->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->creditLeave(
                    $leaveReq->user_id,
                    $leaveReq->leave_type_id,
                    $leaveReq->total_days,
                    $leaveReq,
                    'Refund for rejected leave request'
                );
            }

            DB::commit();

            // Only notify if employee is active
            if ($leaveReq->user && $leaveReq->user->email && ($leaveReq->user->employee && $leaveReq->user->employee->status === 'active')) {
                try {
                    Mail::to($leaveReq->user->email)->send(new LeaveStatusMail([
                        'leave_request_id' => $leaveReq->id,
                        'user_id' => $leaveReq->user->id,
                        'status' => 'rejected',
                        'reason' => $request->reason
                    ]));
                } catch (\Exception $e) {
                    \Log::error("Failed to send leave rejection email: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Leave rejected successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reject leave: ' . $e->getMessage()], 500);
        }
    }
}
