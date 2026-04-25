<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\AttendanceRejectedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AttendanceApprovalController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('attendance.approval', compact('users'));
    }

    public function fetch(Request $request)
    {
        $date = $request->filled('date') ? $request->date : Carbon::today('Asia/Kolkata')->toDateString();
        
        // 1. Fetch Active Employees who have a login account (Excluding Admins with Role ID 1)
        $empQuery = \App\Models\Employee::with('user')
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
                ->where(function($q) {
                    $q->whereRaw('LOWER(status) = ?', ['approved'])
                      ->orWhereRaw('LOWER(status) = ?', ['pending']);
                })
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->first();

            $firstMovement = $attendance ? $attendance->movements->sortBy('id')->first() : null;
            $lastMovement = $attendance ? $attendance->movements->sortByDesc('id')->first() : null;

            // Determine Status and Display Badge
            $status = 'Absent';
            $leaveDetails = null;

            if ($attendance) {
                if ($attendance->is_approved == 1) $status = 'Approved';
                elseif ($attendance->is_approved == 2) $status = 'Rejected';
                else $status = 'Pending';
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
                    $leaveDetails = "Overlap with {$leaveType} (" . ucfirst($leave->status) . ")";
                } else {
                    $status = (strtolower($leave->status) === 'approved') ? 'On Leave' : 'Pending Leave';
                    $leaveDetails = $leaveType;
                }
            }

            return [
                'id' => $attendance ? $attendance->id : null,
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
                'status' => $status,
                'is_emergency' => $attendance ? $attendance->is_emergency : false,
                'is_wfh' => $attendance ? $attendance->is_wfh : false,
                'leave_details' => $leaveDetails
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
            'out_time' => 'nullable'
        ]);

        try {
            $attendance = Attendance::with('movements')->findOrFail($id);
            $dateStr = $attendance->date->toDateString(); // Ensure Y-m-d format

            // Update First Movement (Punch In)
            $firstMovement = $attendance->movements->sortBy('id')->first();
            if ($firstMovement) {
                // Convert submitted IST time back to UTC for database
                $inTimeStr = "{$dateStr} {$request->in_time}";
                $inTime = Carbon::createFromFormat('Y-m-d H:i', $inTimeStr, 'Asia/Kolkata')->setTimezone('UTC');
                $firstMovement->update(['time' => $inTime]);
            }

            // Update Last Movement (Punch Out)
            $lastMovement = $attendance->movements->sortByDesc('id')->first();
            if ($request->filled('out_time')) {
                $outTimeStr = "{$dateStr} {$request->out_time}";
                $outTime = Carbon::createFromFormat('Y-m-d H:i', $outTimeStr, 'Asia/Kolkata')->setTimezone('UTC');
                
                if ($lastMovement && $lastMovement->id != $firstMovement->id) {
                    $lastMovement->update(['time' => $outTime]);
                } else {
                    // If no out movement exists or it's the same as "In", create a new "Out" movement
                    $attendance->movements()->create([
                        'movement_type' => $firstMovement ? $firstMovement->movement_type : 'office',
                        'movement_action' => 'out',
                        'time' => $outTime,
                        'description' => 'Manual adjustment'
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Times updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating times: ' . $e->getMessage()], 500);
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
}
