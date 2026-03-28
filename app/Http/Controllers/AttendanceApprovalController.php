<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceApprovalController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('attendance.approval', compact('users'));
    }

    public function fetch(Request $request)
    {
        $today = Carbon::today('Asia/Kolkata')->toDateString();
        
        // Start query
        $query = Attendance::with(['user', 'movements'])
            ->where('is_approved', 0) // Filter for pending approvals
            ->where('is_locked', 0); // Exclude locked records
        
        // Date Filtering
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
             // Default to today if no date provided? 
             // Or show all pending? Let's show all pending by default if no date filter, 
             // but sort by date desc to see recent ones.
             // If user wants specific date, they use filter.
             // Actually, usually users want to see Today's by default.
             // Let's keep logic: if no filter, show pending from *all time* or just *today*?
             // Previous code forced today. Let's make it optional.
             if (!$request->has('date')) {
                 $query->whereDate('date', $today);
             }
        }

        if ($request->search) {
            $searchTerm = $request->search;
            $query->whereHas('user', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(20);

        $attendances->getCollection()->transform(function ($attendance) {
            $firstMovement = $attendance->movements->first();
            $lastMovement = $attendance->movements->sortByDesc('id')->first();
            
            return [
                'id' => $attendance->id,
                'user_name' => $attendance->user ? $attendance->user->name : 'Unknown',
                'date' => Carbon::parse($attendance->date)->format('d M Y'),
                'in_time' => $firstMovement ? Carbon::parse($firstMovement->time)->setTimezone('Asia/Kolkata')->format('h:i A') : '-',
                'in_time_raw' => $firstMovement ? Carbon::parse($firstMovement->time)->setTimezone('Asia/Kolkata')->format('H:i') : '',
                'in_type' => $firstMovement ? $firstMovement->movement_type : null,
                'out_time' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata')->format('h:i A') : '-',
                'out_time_raw' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? Carbon::parse($lastMovement->time)->setTimezone('Asia/Kolkata')->format('H:i') : '',
                'out_type' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? $lastMovement->movement_type : null,
                'status' => 'Pending',
                'is_emergency' => $attendance->is_emergency,
                'is_wfh' => $attendance->is_wfh,
            ];
        });

        return response()->json($attendances);
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
