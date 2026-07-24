<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Movement;
use App\Models\Holiday;
use App\Models\LeaveType;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use App\Mail\AttendanceRejectedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\AttendanceReportService;

class AttendanceApprovalController extends Controller
{
    protected $reportService;
    protected $leaveBalanceService;

    public function __construct(AttendanceReportService $reportService, LeaveBalanceService $leaveBalanceService)
    {
        $this->reportService = $reportService;
        $this->leaveBalanceService = $leaveBalanceService;
    }

    public function index()
    {
        $users = User::where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->orderBy('name')
            ->get();
        return view('attendance.approval', compact('users'));
    }

    public function fetch(Request $request)
    {
        $date = $request->filled('date') ? $request->date : Carbon::today('Asia/Kolkata')->toDateString();
        
        $carbonDate = Carbon::parse($date);
        $startOfMonth = $carbonDate->copy()->startOfMonth()->format('Y-m-d');
        $endOfMonth = $carbonDate->copy()->endOfMonth()->format('Y-m-d');

        // Fetch user IDs who have attendance in the month of the selected date
        $userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->pluck('user_id')
            ->unique()
            ->toArray();
        // 1. Fetch Active Employees OR inactive employees who have attendance on this date (Excluding Admins with Role ID 1 and whose is_attendance is enabled)
        $empQuery = \App\Models\Employee::with(['user', 'shiftRelation'])
            ->where(function($query) use ($userIdsWithAttendance) {
                $query->where('status', 'active')
                      ->orWhereHas('user', function($q) use ($userIdsWithAttendance) {
                          $q->whereIn('id', $userIdsWithAttendance);
                      });
            })
            ->whereHas('user', function($q) {
                $q->where('role_id', '!=', 1)
                  ->where('is_attendance', 1);
            });

        if ($request->search) {
            $searchTerm = $request->search;
            $empQuery->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $employees = $empQuery->orderBy('employee_code')->paginate(20);

        // 2. Map through employees to detect daily status
        $employees->getCollection()->transform(function ($employee) use ($date) {
            $user = $employee->user;
            
            // Handle case where employee has no user account
            if (!$user) {
                return [
                    'id' => null,
                    'user_id' => null,
                    'user_name' => $employee->name . ' (No Account)',
                    'emp_id' => $employee->employee_code,
                    'date' => Carbon::parse($date)->format('d M Y'),
                    'in_time' => '-',
                    'in_time_raw' => '',
                    'in_type' => null,
                    'out_time' => '-',
                    'out_time_raw' => '',
                    'out_type' => null,
                    'status' => 'No User',
                    'is_emergency' => false,
                    'is_wfh' => false,
                    'leave_details' => null
                ];
            }

            // Check Attendance
            $attendance = Attendance::with('movements')
                ->where('user_id', $user->id)
                ->whereDate('date', $date)
                ->first();

            // Check Leave (Any type: Full, Half, or SL)
            $leave = \App\Models\LeaveRequest::with('leaveType')
                ->where('user_id', $user->id)
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->where(function($q) {
                    $q->whereIn(DB::raw('LOWER(status)'), ['approved', 'pending']);
                })
                ->first();

            $shift = $employee->shiftRelation;
            $shiftIn = $shift ? Carbon::parse($shift->start_time)->format('H:i') : '';
            $shiftOut = $shift ? Carbon::parse($shift->end_time)->format('H:i') : '';

            $firstMovement = $attendance ? $attendance->movements->sortBy('id')->first() : null;
            $lastMovement = $attendance ? $attendance->movements->sortByDesc('id')->first() : null;

            // Determine Calculated Status based on Hours
            $status = 'Absent';
            $hours = 0;
            $leaveDetails = null;
            $leaveIdForOverlap = null;
            $isEarlyOut = false;
            $suggestedSlStart = '';
            $suggestedSlEnd = '';

            if ($attendance) {
                $shift = $employee->shiftRelation;
                $hours = $this->reportService->calculateTotalHours($attendance->movements, $shift, $date);
                
                [$fullDayHr, $halfDayHr] = $this->reportService->getThresholds($shift);
                
                // Check if it's a Weekly Off or Holiday
                $dayName = Carbon::parse($date)->format('l');
                $isWeeklyOff = false;
                if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
                    $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
                }
                $isHoliday = \App\Models\Holiday::where('holiday_date', $date)->exists();

                $hasHalfDayLeave = ($leave && $leave->is_half_day && in_array(strtolower($leave->status), ['approved', 'pending']));
                $slHours = 0;
                if ($leave && $leave->is_sl && $leave->start_time && $leave->end_time) {
                    $slS = Carbon::parse($leave->start_time);
                    $slE = Carbon::parse($leave->end_time);
                    $slHours = $slS->diffInMinutes($slE) / 60;
                }

                $lType = ($leave && $leave->is_sl) ? 'SL' : null;
                $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
                $statusInfo = $this->reportService->determineStatus($date, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $lType, $hasHalfDayLeave, $slHours, $enforceTimeRestriction);
                $status = $statusInfo['label'];
            }

            if ($leave) {
                $leaveType = $leave->leaveType ? $leave->leaveType->name : 'Leave';
                if ($leave->is_half_day) {
                    $session = $leave->half_day_period === 'pre_lunch' ? 'Pre-Lunch' : 'Post-Lunch';
                    $leaveType .= " (Half Day - {$session})";
                } elseif ($leave->is_sl) {
                    $leaveType .= " (Short Leave)";
                }
                
                // If they punched in but also have leave (Overlap)
                if ($attendance) {
                    $hasOverlap = true;
                    
                    // For Half Day/SL, check if they actually overlapped with the leave period
                    if ($firstMovement) {
                        $punchInTime = Carbon::parse($firstMovement->time)->setTimezone('Asia/Kolkata');
                        $punchOutTime = $lastMovement ? Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata') : null;
                        $shift = $employee->shiftRelation;

                        if ($leave->is_half_day && $shift) {
                            $shiftStart = Carbon::parse($date . ' ' . $shift->start_time, 'Asia/Kolkata');
                            $shiftEnd = Carbon::parse($date . ' ' . $shift->end_time, 'Asia/Kolkata');
                            $midPoint = $shiftStart->copy()->addMinutes($shiftStart->diffInMinutes($shiftEnd) / 2);
                            
                            if ($leave->half_day_period === 'pre_lunch') {
                                if ($punchInTime->gte($midPoint)) $hasOverlap = false;
                            } else {
                                if ($punchOutTime && $punchOutTime->lte($midPoint)) $hasOverlap = false;
                            }
                        } elseif ($leave->is_sl && $leave->start_time && $leave->end_time) {
                            $slStart = Carbon::parse($date . ' ' . $leave->start_time, 'Asia/Kolkata');
                            $slEnd = Carbon::parse($date . ' ' . $leave->end_time, 'Asia/Kolkata');
                            
                            // No overlap if:
                            // 1. Worked entirely BEFORE the SL started
                            // 2. Started work entirely AFTER the SL ended
                            if (($punchOutTime && $punchOutTime->lte($slStart)) || ($punchInTime->gte($slEnd))) {
                                $hasOverlap = false;
                            }
                        }
                    }

                    if ($hasOverlap) {
                        $leaveDetails = "Overlap with {$leaveType} (" . ucfirst($leave->status) . ")";
                        $leaveIdForOverlap = $leave->id;
                    } else {
                        // Clean split: Leave in one half, Work in the other.
                        $leaveDetails = $leaveType;
                        $leaveIdForOverlap = null;
                    }
                } else {
                    $status = (strtolower($leave->status) === 'approved') ? 'On Leave' : 'Pending Leave';
                    $leaveDetails = $leaveType;
                    $leaveIdForOverlap = null;
                }
            }

            // Detect Early Out for Short Leave option
            if ($attendance && $shift && $shift->end_time && $lastMovement && $lastMovement->movement_action === 'out') {
                $punchOut = Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata');
                $shiftEnd = Carbon::parse($date . ' ' . $shift->end_time, 'Asia/Kolkata');
                
                $slLimitMin = $shift->sl_end_limit ?? 0;
                $slThreshold = $shiftEnd->copy()->subMinutes($slLimitMin);
                
                if ($punchOut->lt($slThreshold)) {
                    $isEarlyOut = true;
                    $suggestedSlStart = $punchOut->format('H:i');
                    $suggestedSlEnd = $shiftEnd->format('H:i');
                }
            }

            return [
                'id' => $attendance ? $attendance->id : null,
                'leave_id' => $leaveIdForOverlap, // Send leave ID only for actual overlap
                'user_id' => $user->id,
                'user_name' => $user->name,
                'emp_id' => $employee->employee_code,
                'date' => Carbon::parse($date)->format('d M Y'),
                'in_time' => $firstMovement ? Carbon::parse($firstMovement->time)->setTimezone('Asia/Kolkata')->format('h:i A') : '-',
                'in_time_raw' => $firstMovement ? Carbon::parse($firstMovement->time)->setTimezone('Asia/Kolkata')->format('H:i') : '',
                'in_type' => $firstMovement ? $firstMovement->movement_type : null,
                'out_time' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata')->format('h:i A') : '-',
                'out_time_raw' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata')->format('H:i') : '',
                'out_type' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? $lastMovement->movement_type : null,
                'shift_in' => $shiftIn,
                'shift_out' => $shiftOut,
                'status' => $status,
                'hours' => round($hours, 2),
                'is_approved' => $attendance ? $attendance->is_approved : 0,
                'is_emergency' => $attendance ? $attendance->is_emergency : false,
                'is_wfh' => $attendance ? $attendance->is_wfh : false,
                'leave_details' => $leaveDetails,
                'is_edited' => ($attendance && $attendance->editLogs()->exists()),
                'is_early_out' => $isEarlyOut,
                'suggested_sl_start' => $suggestedSlStart,
                'suggested_sl_end' => $suggestedSlEnd,
                'edit_history' => ($attendance && $attendance->editLogs()->exists()) ? $attendance->editLogs->map(function($log) {
                    return [
                        'by' => $log->editor->name,
                        'reason' => $log->reason,
                        'at' => $log->created_at->format('d M h:i A')
                    ];
                }) : []
            ];
        })->filter(); // Remove any null users if relationships are broken

        $pendingCount = Attendance::whereDate('date', $date)->where('is_approved', 0)->count();
        $isLockedForDate = Attendance::whereDate('date', $date)->where('is_locked', 1)->exists();

        return response()->json([
            'data' => $employees->items(),
            'total' => $employees->total(),
            'pending_count' => $pendingCount,
            'is_locked_for_date' => $isLockedForDate,
            'current_page' => $employees->currentPage(),
            'last_page' => $employees->lastPage(),
            'links' => $employees->linkCollection()
        ]);
    }

    public function approve($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            // Check for continuity: any previous pending attendance for this user?
            $hasPreviousPending = Attendance::where('user_id', $attendance->user_id)
                ->where('date', '<', $attendance->date)
                ->where('is_approved', 0)
                ->exists();

            if ($hasPreviousPending) {
                return response()->json([
                    'success' => false, 
                    'message' => "Cannot approve. There are pending attendance records for previous dates for this user ({$attendance->user->name}). Please approve them chronologically."
                ], 422);
            }

            $attendance->is_approved = 1;
            $attendance->save();
            
            // Grant 1 credit if it's a Weekly Off or Holiday and they are present
            $this->creditHolidayWorking($attendance);
            
            return response()->json(['success' => true, 'message' => 'Attendance approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error approving attendance: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->is_approved = 2; // 2 = Rejected
            $attendance->reject_reason = $request->reason;
            $attendance->save();
            
            // Only send if the employee is active
            if ($attendance->user && $attendance->user->email && ($attendance->user->employee && $attendance->user->employee->status === 'active')) {
                try {
                    Mail::to($attendance->user->email)->send(new AttendanceRejectedMail([
                        'attendance_id' => $attendance->id,
                        'user_id' => $attendance->user->id,
                        'reason' => $request->reason
                    ]));
                } catch (\Exception $e) {
                    \Log::error("Failed to send attendance rejection email: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Attendance rejected successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error rejecting attendance: ' . $e->getMessage()], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attendance,id'
        ]);

        try {
            $attendances = Attendance::with(['user.employee.shiftRelation', 'movements'])->whereIn('id', $request->ids)->get();
            
            foreach ($attendances as $attendance) {
                // Check if any previous date for this user is pending AND NOT in the list being approved
                $hasPreviousPending = Attendance::where('user_id', $attendance->user_id)
                    ->where('date', '<', $attendance->date)
                    ->where('is_approved', 0)
                    ->whereNotIn('id', $request->ids) // Must not be in the current selection
                    ->exists();

                if ($hasPreviousPending) {
                    $dateStr = $attendance->date instanceof Carbon ? $attendance->date->format('d M Y') : date('d M Y', strtotime($attendance->date));
                    return response()->json([
                        'success' => false, 
                        'message' => "Cannot bulk approve because some users (like {$attendance->user->name} on {$dateStr}) have pending records from earlier dates that are not selected. Please approve in chronological order."
                    ], 422);
                }
            }

            foreach ($attendances as $attendance) {
                $attendance->is_approved = 1;
                $attendance->save();
                
                // Grant 1 credit if it's a Weekly Off or Holiday and they are present
                $this->creditHolidayWorking($attendance);
            }
            return response()->json(['success' => true, 'message' => 'Selected records approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error approving records: ' . $e->getMessage()], 500);
        }
    }

    public function postDaily(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $date = $request->date;

        try {
            // Find all records for this date to ensure credits are processed
            $attendances = Attendance::with(['user.employee.shiftRelation', 'movements'])
                ->whereDate('date', $date)
                ->get();

            if ($attendances->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No attendance records found for this date.'], 422);
            }

            DB::beginTransaction();

            foreach ($attendances as $attendance) {
                // Only handle approval logic for pending records
                if ($attendance->is_approved == 0) {
                    $hasPreviousPending = Attendance::where('user_id', $attendance->user_id)
                        ->where('date', '<', $attendance->date)
                        ->where('is_approved', 0)
                        ->exists();

                    if ($hasPreviousPending) {
                        DB::rollBack();
                        $dateStr = Carbon::parse($attendance->date)->format('d M Y');
                        return response()->json([
                            'success' => false, 
                            'message' => "Cannot post. User '{$attendance->user->name}' has pending attendance from a date before {$dateStr}. Please resolve previous dates first."
                        ], 422);
                    }

                    // Approve the record
                    $attendance->is_approved = 1;
                    $attendance->save();
                }

                // Grant 1 credit if it's a Weekly Off or Holiday and they are present
                // (Already credited records are skipped inside this method)
                $this->creditHolidayWorking($attendance);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'All attendance for this date has been posted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error posting attendance: ' . $e->getMessage()], 500);
        }
    }

    public function updateTimes(Request $request, $id)
    {
        $request->validate([
            'in_time' => 'required',
            'reason' => 'required|string|min:5',
        ]);

        try {
            DB::beginTransaction();

            $attendance = Attendance::with('movements')->findOrFail($id);
            $dateStr = $attendance->date->toDateString();

            // Store OLD values for logging
            $firstMovement = $attendance->movements->sortBy('id')->first();
            $lastMovement = $attendance->movements->sortByDesc('id')->first();
            
            $oldIn = $firstMovement ? $firstMovement->time : null;
            $oldOut = ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? $lastMovement->time : null;

            // Prepare NEW values (IST to UTC)
            $newIn = Carbon::createFromFormat('Y-m-d H:i', "{$dateStr} {$request->in_time}", 'Asia/Kolkata')->setTimezone('UTC');
            $newOut = $request->filled('out_time') 
                ? Carbon::createFromFormat('Y-m-d H:i', "{$dateStr} {$request->out_time}", 'Asia/Kolkata')->setTimezone('UTC') 
                : null;

            // 1. Create the Audit Log
            $editorId = auth()->id() 
                ?? \Auth::id() 
                ?? session('user_id') 
                ?? session('admin_id') 
                ?? \Session::get('user_id');
            
            if (!$editorId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized: No authenticated user found in session or auth guard.'], 401);
            }

            \App\Models\AttendanceEditLog::create([
                'attendance_id' => $id,
                'edited_by' => $editorId,
                'old_in_time' => $oldIn,
                'new_in_time' => $newIn,
                'old_out_time' => $oldOut,
                'new_out_time' => $newOut,
                'reason' => $request->reason,
            ]);

            // 2. Update Movements
            if ($firstMovement) {
                $firstMovement->update(['time' => $newIn]);
            }

            if ($request->filled('out_time')) {
                if ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) {
                    $lastMovement->update(['time' => $newOut]);
                } else {
                    // Create out movement if it didn't exist
                    Movement::create([
                        'attendance_id' => $id,
                        'movement_type' => $firstMovement ? $firstMovement->movement_type : 'office',
                        'movement_action' => 'out',
                        'time' => $newOut,
                        'description' => 'Manual adjustment'
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Attendance times updated and logged successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getLeaveBalances($userId)
    {
        try {
            // Get the latest ledger entry for each leave type for this user
            $balances = \App\Models\LeaveType::all()->map(function($type) use ($userId) {
                $latestLedger = \App\Models\LeaveLedger::where('user_id', $userId)
                    ->where('leave_type_id', $type->id)
                    ->latest('id')
                    ->first();

                return [
                    'type_id' => $type->id,
                    'type_name' => $type->name,
                    'is_sl' => (bool)$type->is_short_leave,
                    'allow_hd' => (bool)$type->allow_half_day,
                    'hd_weight' => (float)$type->half_day_weight,
                    'fd_weight' => (float)$type->full_day_weight,
                    'remaining' => $latestLedger ? (float)$latestLedger->balance_after : 0
                ];
            });

            return response()->json(['success' => true, 'balances' => $balances]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function applyQuickLeave(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'leave_type_id' => 'required|exists:leave_types,id',
            'leave_category' => 'required|in:full,half,short',
            'half_day_period' => 'nullable|required_if:leave_category,half|in:pre_lunch,post_lunch',
            'start_time' => 'nullable|required_if:leave_category,short',
            'end_time' => 'nullable|required_if:leave_category,short',
            'reason' => 'required|string|min:5'
        ]);

        try {
            DB::beginTransaction();

            $editorId = auth()->id() 
                ?? \Auth::id() 
                ?? session('user_id') 
                ?? session('admin_id') 
                ?? \Session::get('user_id');

            if (!$editorId) {
                return response()->json(['success' => false, 'message' => 'Unauthorized: No authenticated user found for logging.'], 401);
            }

            $userId = $request->user_id;
            $leaveTypeId = $request->leave_type_id;
            $date = $request->date;
            $category = $request->leave_category;

            $leaveType = \App\Models\LeaveType::findOrFail($leaveTypeId);
            
            // Calculate total days
            $totalDays = (float)$leaveType->full_day_weight;
            if ($category === 'half') {
                $totalDays = (float)$leaveType->half_day_weight;
            } elseif ($category === 'short') {
                $totalDays = (float)$leaveType->full_day_weight; 
            }

            // 1. Check Balance (Latest entry)
            $latestLedger = \App\Models\LeaveLedger::where('user_id', $userId)
                ->where('leave_type_id', $leaveTypeId)
                ->latest('id')
                ->first();

            $currentBalance = $latestLedger ? (float)$latestLedger->balance_after : 0;

            if ($currentBalance < $totalDays) {
                return response()->json(['success' => false, 'message' => 'Insufficient leave balance. Required: ' . $totalDays], 422);
            }

            // 2. Create Leave Request
            $leave = \App\Models\LeaveRequest::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'is_sl' => $category === 'short',
                'is_half_day' => $category === 'half',
                'half_day_period' => $category === 'half' ? $request->half_day_period : null,
                'start_time' => $category === 'short' ? $request->start_time : null,
                'end_time' => $category === 'short' ? $request->end_time : null,
                'start_date' => $date,
                'end_date' => $date,
                'total_days' => $totalDays,
                'status' => 'approved',
                'reason' => $request->reason,
                'approved_by' => $editorId
            ]);

            // 3. Update Ledger (Create new Debit Entry)
            \App\Models\LeaveLedger::create([
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'transaction_type' => 'debit',
                'amount' => $totalDays,
                'balance_after' => $currentBalance - $totalDays,
                'reference_type' => 'App\Models\LeaveRequest',
                'reference_id' => $leave->id,
                'remarks' => "Absence adjusted (" . ucfirst($category) . "): " . $request->reason
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Absence converted to Leave successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'in_time' => 'required',
            'out_time' => 'nullable',
            'movement_type' => 'required|in:office,field,wfh'
        ]);

        try {
            $dateStr = $request->date;
            
            // Convert submitted IST time back to UTC for database
            $inTimeStr = "{$dateStr} {$request->in_time}";
            $inTime = Carbon::createFromFormat('Y-m-d H:i', $inTimeStr, 'Asia/Kolkata')->setTimezone('UTC');

            // Check if attendance already exists for this date and user
            $attendance = Attendance::firstOrCreate([
                'user_id' => $request->user_id,
                'date' => $request->date,
            ], [
                'is_emergency' => 0,
                'is_wfh' => $request->movement_type === 'wfh' ? 1 : 0,
                'is_approved' => 1 // Auto approve manual additions
            ]);

            $type = $request->movement_type === 'wfh' ? 'office' : $request->movement_type;

            // Add IN movement
            $attendance->movements()->create([
                'movement_type' => $type,
                'movement_action' => 'in',
                'time' => $inTime,
                'description' => 'Manual attendance marked by admin'
            ]);

            // Add OUT movement if provided
            if ($request->filled('out_time')) {
                $outTimeStr = "{$dateStr} {$request->out_time}";
                $outTime = Carbon::createFromFormat('Y-m-d H:i', $outTimeStr, 'Asia/Kolkata')->setTimezone('UTC');
                
                $attendance->movements()->create([
                    'movement_type' => $type,
                    'movement_action' => 'out',
                    'time' => $outTime,
                    'description' => 'Manual attendance marked by admin'
                ]);
            }

            // Grant 1 credit if it's a Weekly Off or Holiday and they are present
            $this->creditHolidayWorking($attendance->fresh(['user.employee.shiftRelation', 'movements']));

            return response()->json(['success' => true, 'message' => 'Attendance marked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error marking attendance: ' . $e->getMessage()], 500);
        }
    }

    public function voidAttendance(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:attendance,id'
        ]);

        try {
            DB::beginTransaction();
            $attendance = Attendance::findOrFail($request->id);
            
            // Delete all movements
            $attendance->movements()->delete();
            
            // Delete the attendance record itself
            $attendance->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Attendance voided successfully. Row is now Absent.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function creditHolidayWorking($attendance)
    {
        $date = $attendance->date;
        $user = $attendance->user;
        
        \Log::info("--- Starting creditHolidayWorking for {$user->name} on {$date} ---");

        // Ensure user and employee relation exists
        if (!$user || !$user->employee) {
            \Log::info("User or Employee relation missing for attendance ID: {$attendance->id}");
            return;
        }

        $employee = $user->employee;
        $shift = $employee->shiftRelation;

        // 1. Determine if it's a weekly off or holiday
        $isHoliday = Holiday::where('holiday_date', $date)->exists();
        
        $dayName = Carbon::parse($date)->format('l');
        $isWeeklyOff = false;
        if ($shift && $shift->week_offs && is_array($shift->week_offs)) {
            $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
        }

        \Log::info("Holiday Check: " . ($isHoliday ? 'YES' : 'NO') . " | Weekly Off Check: " . ($isWeeklyOff ? 'YES' : 'NO'));

        if ($isHoliday || $isWeeklyOff) {
            // 2. Check if they are present and meet time restriction
            $hours = $this->reportService->calculateTotalHours($attendance->movements, $shift, $date);
            \Log::info("Hours worked on this day: " . $hours);
            
            [$fullDayHr, $halfDayHr] = $this->reportService->getThresholds($shift);
            $enforceTimeRestriction = $shift ? ($shift->enforce_time_restriction_on_overtime ?? 0) : 0;
            $statusInfo = $this->reportService->determineStatus($date, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, null, false, 0, $enforceTimeRestriction);
            
            $isValidWorking = in_array($statusInfo['code'], ['W/O-W', 'H/W']);
            \Log::info("Is Valid Working based on time restriction rules: " . ($isValidWorking ? 'YES' : 'NO'));
            
            $grantCompOff = true;
            if ($shift && isset($shift->grant_comp_off_for_overtime)) {
                $grantCompOff = $shift->grant_comp_off_for_overtime;
            }
            \Log::info("Grant Comp Off on Overtime configured as: " . ($grantCompOff ? 'YES' : 'NO'));

            if ($isValidWorking && $grantCompOff) {
                // 3. Find the leave type for "Holiday Working" or "Compensatory Off"
                $leaveType = LeaveType::where('name', 'like', '%Holiday Working%')
                    ->orWhere('name', 'like', '%Compensatory%')
                    ->orWhere('name', 'like', '%Comp Off%')
                    ->orWhere('name', 'like', '%Comp-Off%')
                    ->orWhere('name', 'like', '%C.Off%')
                    ->orWhere('name', 'like', '%C-OFF%')
                    ->first();

                if (!$leaveType) {
                    \Log::info("No matching Leave Type found. Creating 'Holiday Working' leave type...");
                    $leaveType = LeaveType::create([
                        'name' => 'Holiday Working',
                        'is_paid' => true,
                        'is_deductible' => true,
                        'full_day_weight' => 1.0,
                        'half_day_weight' => 1.0,
                        'allow_half_day' => false,
                        'color_code' => '#1cc88a',
                        'status' => true,
                        'description' => 'Automatically created for holiday/weekly off working credits.'
                    ]);
                    \Log::info("Created Leave Type: " . $leaveType->name . " (ID: " . $leaveType->id . ")");
                }

                if ($leaveType) {
                    \Log::info("Target Leave Type: " . $leaveType->name . " (ID: " . $leaveType->id . ")");
                    
                    // Check if already credited for this reference to avoid duplicates
                    $exists = \App\Models\LeaveLedger::where('user_id', $user->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('reference_type', get_class($attendance))
                        ->where('reference_id', $attendance->id)
                        ->exists();

                    if (!$exists) {
                        \Log::info("No previous credit found. Proceeding to grant 1.0 credit.");
                        $this->leaveBalanceService->creditLeave(
                            $user->id,
                            $leaveType->id,
                            1.0,
                            $attendance,
                            "Auto-credit for working on " . ($isHoliday ? "Holiday" : "Weekly Off") . " (" . Carbon::parse($date)->format('d M Y') . ")"
                        );
                        \Log::info("Credit granted successfully.");
                    } else {
                        \Log::info("Credit ALREADY exists for this attendance record. Skipping.");
                    }
                }
            } else {
                \Log::info("Skipping credit: Worked hours is 0.");
            }
        } else {
            \Log::info("Skipping credit: Not a Holiday or Weekly Off.");
        }
        \Log::info("--- Finished creditHolidayWorking for {$user->name} ---");
    }
}
