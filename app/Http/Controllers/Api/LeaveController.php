<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Worklog;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveStatusMail;

class LeaveController extends Controller
{
    /**
     * Fetch all leaves for the authenticated user
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        try {
            if (!Schema::hasTable('leave_requests')) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $query = LeaveRequest::with(['leaveType'])
                ->where('user_id', $user->id)
                ->orderBy('start_date', 'desc');

            if (Schema::hasColumn('leave_requests', 'is_early_return')) {
                $query->where('is_early_return', 0);
            }

            $leaves = $query->get();

            return response()->json(['success' => true, 'data' => $leaves]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch history.', 'data' => []]);
        }
    }

    /**
     * Fetch valid active leave types along with live user balances/rules.
     */
    public function getLeaveTypes(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        try {
            if (!Schema::hasTable('leave_types')) {
                return response()->json(['success' => true, 'data' => []]);
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
                $isUnlimited = false;
                if (!empty($empTypeId) && Schema::hasTable('employment_type_leave_rules')) {
                    $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                        ->where('leave_type_id', $type->id)
                        ->first();
                    if ($rule) {
                        $totalAllowed = $rule->value;
                        if (($rule->generation_type ?? '') === 'unlimited') {
                            $isUnlimited = true;
                        }
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

                $type->is_unlimited = $isUnlimited;
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

            return response()->json(['success' => true, 'data' => $mapped]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => []]);
        }
    }

    /**
     * Store a new leave request containing advanced validations synced with Web implementation.
     */
    public function store(Request $request): JsonResponse
    {
        if (!Schema::hasTable('leave_requests')) {
            return response()->json(['success' => false, 'message' => 'System migration pending.'], 500);
        }

        $leaveTypeId = $request->input('leave_type_id');
        $leaveType = LeaveType::find($leaveTypeId);

        $startDateRule = 'required|date|after:today';
        if ($leaveType && $leaveType->is_short_leave) {
            $startDateRule = 'required|date|after_or_equal:today';
        }

        $validator = Validator::make($request->all(), [
            'start_date' => $startDateRule,
            'end_date' => 'required|date|after_or_equal:start_date',
            'leave_type_id' => 'required',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'sl_period' => 'nullable|in:morning,evening',
            'is_half_day' => 'nullable|boolean',
            'half_day_period' => 'nullable|in:pre_lunch,post_lunch',
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        
        if (!$leaveType) {
            return response()->json(['success' => false, 'message' => 'Invalid leave type selected.'], 422);
        }

        // Step 1: Calculate Total Days based on Dynamic Weights
        try {
            $isHalfDay = $request->boolean('is_half_day');
            
            if ($isHalfDay) {
                if (!$leaveType->allow_half_day) {
                    return response()->json(['success' => false, 'message' => "Half days are not allowed for {$leaveType->name}."], 422);
                }
                $totalDays = (float) $leaveType->half_day_weight;
                // Force end_date to be start_date for half days
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

        // Step 2: Short Leave (Dynamic Shift Limits)
        if ($leaveType->is_short_leave) {
            $employee = $user->employee;
            $shift = $employee ? $employee->getShiftForDate($request->start_date) : null;
            if (!$shift) {
                return response()->json(['success' => false, 'message' => 'Active shift required for Short Leave logic.'], 422);
            }

            // $shift already assigned above
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

        // Step 3: Restricted Holiday Validation
        if ($leaveType->is_restricted) {
            $isHoliday = \App\Models\Holiday::where('holiday_date', $request->start_date)->where('is_rh', 1)->exists();
            if (!$isHoliday) {
                return response()->json(['success' => false, 'message' => 'Selected date is not a valid Restricted Holiday.'], 422);
            }
        }

        // Step 4: Max Monthly Usage Capping Rule
        $empTypeId = $user->employee->employment_type_id ?? null;
        if ($empTypeId) {
            $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                ->where('leave_type_id', $leaveType->id)
                ->first();
            if ($rule && $rule->generation_type === 'prefill' && !empty($rule->max_use_per_month) && $rule->max_use_per_month > 0) {
                $startOfMonth = Carbon::parse($request->start_date)->startOfMonth()->toDateString();
                $endOfMonth = Carbon::parse($request->start_date)->endOfMonth()->toDateString();
                
                $monthUsage = LeaveRequest::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                        $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                              ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
                    })
                    ->sum('total_days');

                if (($monthUsage + $totalDays) > $rule->max_use_per_month) {
                    return response()->json([
                        'success' => false,
                        'message' => "Monthly usage limit reached. You can only use up to {$rule->max_use_per_month} day(s) per month for this leave type."
                    ], 422);
                }
            }
        }

        // Step 5: Verify Balance Quota (Ledger checks)
        if ($leaveType->is_deductible || $leaveType->is_restricted || $leaveType->is_short_leave) {
            $empTypeId = $user->employee->employment_type_id ?? null;
            $rule = \App\Models\EmploymentTypeLeaveRule::where('employment_type_id', $empTypeId)
                ->where('leave_type_id', $leaveType->id)
                ->first();
            $maxAllowed = $rule ? (float) $rule->value : 0;

            if ($leaveType->quota_type === 'monthly') {
                $usage = LeaveRequest::where('user_id', $user->id)
                    ->where('leave_type_id', $leaveType->id)
                    ->whereIn('status', ['pending', 'approved'])
                    ->whereYear('start_date', Carbon::parse($request->start_date)->year)
                    ->whereMonth('start_date', Carbon::parse($request->start_date)->month)
                    ->count(); 

                if (($usage + 1) > $maxAllowed) {
                    return response()->json(['success' => false, 'message' => "Monthly limit reached. You are allowed {$maxAllowed} per month."], 422);
                }
            } else {
                if ($leaveType->is_deductible) {
                    $balanceService = app(LeaveBalanceService::class);
                    $currentBalance = $balanceService->getBalance($user->id, $leaveType->id);

                    if ($currentBalance < $totalDays) {
                         return response()->json(['success' => false, 'message' => "Insufficient balance. Available: {$currentBalance}, Requested: {$totalDays}"], 422);
                    }
                } else if ($leaveType->is_restricted) {
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

        // Step 7: Check Overlapping Leaves
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

        // Commit and Immediate Ledger Debit
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
                'status' => 'pending' 
            ]);

            if ($leaveType->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->debitLeave(
                    $user->id, 
                    $leaveType->id, 
                    $totalDays, 
                    $leaveReq, 
                    'Immediate deduction upon API leave application'
                );
            }

            DB::commit();

            // Send email to admin and managers
            try {
                $admins = \App\Models\User::where('role_id', 1)->whereNotNull('email')->get();
                $managers = $user->managers()->whereNotNull('email')->get();
                
                $recipients = $admins->merge($managers)->unique('id');

                foreach ($recipients as $recipient) {
                    \Illuminate\Support\Facades\Mail::to($recipient->email)->send(new \App\Mail\LeaveApplicationMail([
                        'leave_request_id' => $leaveReq->id,
                        'applicant_id' => $user->id,
                        'recipient_name' => $recipient->name
                    ]));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send API leave application email: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => "Leave application for {$totalDays} days submitted. Balance deducted and pending approval.",
                'data' => $leaveReq
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to apply leave: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing leave with latest constraints.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        $leaveReq = LeaveRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leaveReq) {
            return response()->json(['success' => false, 'message' => 'Leave request not found.'], 404);
        }

        if ($leaveReq->status === 'approved') {
             return response()->json(['success' => false, 'message' => 'Cannot edit an already approved leave request. Please cancel it instead.'], 422);
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

            return response()->json([
                'success' => true,
                'message' => 'Leave request updated successfully.',
                'data' => $leaveReq
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update leave.'], 500);
        }
    }

    /**
     * Cancel a leave request and process immediate balance refund.
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        
        $leaveReq = LeaveRequest::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$leaveReq) {
            return response()->json(['success' => false, 'message' => 'Leave not found.'], 404);
        }

        DB::beginTransaction();
        try {
            // Immediate Refund Logic for cancelled/deducted leaves
            if (in_array($leaveReq->status, ['pending', 'approved']) && $leaveReq->leaveType && $leaveReq->leaveType->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->creditLeave(
                    $leaveReq->user_id,
                    $leaveReq->leave_type_id,
                    $leaveReq->total_days,
                    $leaveReq,
                    'Refund for cancelled leave via API'
                );
            }
            
            // Status marked as cancelled instead of hard deleting for audit trailing
            $leaveReq->update(['status' => 'cancelled']);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Leave cancelled and balance refunded successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to cancel leave: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch all leave requests pending/processed for approval by manager or admin
     */
    public function fetchApprovals(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        try {
            if (!Schema::hasTable('leave_requests')) {
                return response()->json(['success' => true, 'data' => []]);
            }

            if ($user->role_id == 1) {
                // Admin sees all requests
                $leaves = LeaveRequest::with(['leaveType', 'user'])
                    ->whereIn('status', ['pending', 'approved', 'rejected'])
                    ->where('is_early_return', 0)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Manager: subordinates mapping
                $actualUser = \App\Models\User::find($user->id);
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

            return response()->json(['success' => true, 'data' => $leaves]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching approvals.', 'data' => []]);
        }
    }

    /**
     * Approve a specific leave request
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        try {
            if ($user->role_id == 1) {
                $leaveReq = LeaveRequest::where('id', $id)->firstOrFail();
            } else {
                $actualUser = \App\Models\User::find($user->id);
                $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereIn('user_id', $subordinateIds)
                    ->firstOrFail();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Leave not found or unauthorized.'], 404);
        }

        if ($leaveReq->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Leave is already ' . $leaveReq->status], 422);
        }

        DB::beginTransaction();
        try {
            $leaveReq->status = 'approved';
            $leaveReq->approved_by = $user->id;
            $leaveReq->save();

            DB::commit();

            // Optional: Mailer Notification
            if ($leaveReq->user && $leaveReq->user->email && ($leaveReq->user->employee && $leaveReq->user->employee->status === 'active')) {
                try {
                    Mail::to($leaveReq->user->email)->send(new LeaveStatusMail([
                        'leave_request_id' => $leaveReq->id,
                        'user_id' => $leaveReq->user->id,
                        'status' => 'approved'
                    ]));
                } catch (\Exception $e) {
                    \Log::error("Failed to send API leave approval email: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Leave request approved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to approve leave.'], 500);
        }
    }

    /**
     * Reject a specific leave request
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Rejection reason is required.', 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);

        try {
            if ($user->role_id == 1) {
                $leaveReq = LeaveRequest::where('id', $id)->firstOrFail();
            } else {
                $actualUser = \App\Models\User::find($user->id);
                $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
                $leaveReq = LeaveRequest::where('id', $id)
                    ->whereIn('user_id', $subordinateIds)
                    ->firstOrFail();
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Leave not found or unauthorized.'], 404);
        }

        if ($leaveReq->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Leave is already ' . $leaveReq->status], 422);
        }

        DB::beginTransaction();
        try {
            $leaveReq->status = 'rejected';
            $leaveReq->approved_by = $user->id;
            $leaveReq->reject_reason = $request->reason;
            $leaveReq->save();

            // Refund deductible balance back
            if ($leaveReq->leaveType && $leaveReq->leaveType->is_deductible) {
                $balanceService = app(LeaveBalanceService::class);
                $balanceService->creditLeave(
                    $leaveReq->user_id,
                    $leaveReq->leave_type_id,
                    $leaveReq->total_days,
                    $leaveReq,
                    'Refund for rejected leave request via API'
                );
            }

            DB::commit();

            if ($leaveReq->user && $leaveReq->user->email && ($leaveReq->user->employee && $leaveReq->user->employee->status === 'active')) {
                try {
                    Mail::to($leaveReq->user->email)->send(new LeaveStatusMail([
                        'leave_request_id' => $leaveReq->id,
                        'user_id' => $leaveReq->user->id,
                        'status' => 'rejected',
                        'reason' => $request->reason
                    ]));
                } catch (\Exception $e) {
                    \Log::error("Failed to send API leave rejection email: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Leave request rejected successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reject leave.'], 500);
        }
    }

    /**
     * Get detailed annual leave history and current year balance breakdown for an employee
     */
    public function userHistory($userId): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            \Log::warning("API History Trace: User unauthorized.");
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        \Log::info("API History Trace Request", [
            'auth_user_id' => $user->id,
            'auth_user_role' => $user->role_id,
            'target_user_id' => $userId
        ]);

        // Security check: ensure user is admin OR the manager of the target user
        if ($user->role_id != 1) {
            $actualUser = \App\Models\User::find($user->id);
            $subordinateIds = $actualUser ? $actualUser->subordinates()->pluck('users.id')->toArray() : [];
            
            \Log::info("API History Subordinate Scan", [
                'subordinate_count' => count($subordinateIds),
                'subordinate_ids' => $subordinateIds
            ]);

            if (!in_array($userId, $subordinateIds) && $user->id != $userId) {
                \Log::warning("API History Access Blocked: Reporting mismatch.", [
                    'auth' => $user->id,
                    'target' => $userId
                ]);
                return response()->json(['success' => false, 'message' => 'Forbidden. Employee reporting conflict.'], 403);
            }
        }

        try {
            $year = Carbon::now()->year;
            $leaves = LeaveRequest::with(['leaveType'])
                ->where('user_id', $userId)
                ->whereYear('start_date', $year)
                ->orderBy('start_date', 'desc')
                ->get();

            $targetUser = \App\Models\User::find($userId);
            $empTypeId = $targetUser->employee->employment_type_id ?? null;

            $balances = [];
            if (!empty($empTypeId) && Schema::hasTable('employment_type_leave_rules')) {
                $rules = \App\Models\EmploymentTypeLeaveRule::with('leaveType')
                    ->where('employment_type_id', $empTypeId)
                    ->get();

                $balanceService = app(LeaveBalanceService::class);

                foreach ($rules as $rule) {
                    if (!$rule->leaveType) continue;
                    $type = $rule->leaveType;
                    
                    $totalAllowed = (float) $rule->value;
                    $pending = (float) \App\Models\LeaveRequest::where('user_id', $userId)
                        ->where('leave_type_id', $type->id)
                        ->where('status', 'pending')
                        ->sum('total_days');

                    $consumed = (float) \App\Models\LeaveRequest::where('user_id', $userId)
                        ->where('leave_type_id', $type->id)
                        ->where('status', 'approved')
                        ->sum('total_days');

                    if ($type->quota_type === 'monthly') {
                        $takenThisMonth = \App\Models\LeaveRequest::where('user_id', $userId)
                            ->where('leave_type_id', $type->id)
                            ->whereIn('status', ['pending', 'approved'])
                            ->whereYear('start_date', now()->year)
                            ->whereMonth('start_date', now()->month)
                            ->count();
                        $remaining = max(0, $totalAllowed - $takenThisMonth);
                    } else {
                        $remaining = (float) $balanceService->getBalance($userId, $type->id);
                    }

                    $balances[] = [
                        'leave_type_name' => $type->name,
                        'allowed' => $totalAllowed,
                        'consumed' => $consumed,
                        'pending' => $pending,
                        'remaining' => $remaining
                    ];
                }
            }

            return response()->json([
                'success' => true, 
                'data' => $leaves,
                'balances' => $balances
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch trail: ' . $e->getMessage()], 500);
        }
    }
}
