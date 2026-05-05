<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Movement;
use Carbon\Carbon;
use App\Mail\AttendanceRejectedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Services\AttendanceReportService;

class AttendanceApprovalController extends Controller
{
    protected $reportService;

    public function __construct(AttendanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('attendance.approval', compact('users'));
    }

    public function fetch(Request $request)
    {
        $date = $request->filled('date') ? $request->date : Carbon::today('Asia/Kolkata')->toDateString();
        
        // 1. Fetch Active Employees who have a login account (Excluding Admins with Role ID 1)
        $empQuery = \App\Models\Employee::with(['user', 'shiftRelation'])
            ->where('status', 'active')
            ->whereHas('user', function($q) {
                $q->where('role_id', '!=', 1);
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
                $statusInfo = $this->reportService->determineStatus($hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $lType, $hasHalfDayLeave, $slHours);
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

        return response()->json([
            'data' => $employees->items(),
            'total' => $employees->total(),
            'pending_count' => $pendingCount,
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
            $attendances = Attendance::with('user')->whereIn('id', $request->ids)->get();
            
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

            Attendance::whereIn('id', $request->ids)->update([
                'is_approved' => 1
            ]);
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
            // Find all pending records for this date
            $pendingAttendances = Attendance::with('user')
                ->whereDate('date', $date)
                ->where('is_approved', 0)
                ->get();

            if ($pendingAttendances->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No pending records to post for this date.'], 422);
            }

            DB::beginTransaction();

            foreach ($pendingAttendances as $attendance) {
                // Restriction: Check for previous pending records for each user
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
}
