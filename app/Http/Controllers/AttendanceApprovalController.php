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
        return view('attendance.approval');
    }

    public function fetch(Request $request)
    {
        $today = Carbon::today()->toDateString();
        
        // Start query
        $query = Attendance::with(['user', 'movements'])
            ->where('is_approved', 0); // Filter for pending approvals
        
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
                'in_time' => $firstMovement ? Carbon::parse($firstMovement->time)->format('h:i A') : '-',
                'in_type' => $firstMovement ? $firstMovement->movement_type : null,
                'out_time' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? Carbon::parse($lastMovement->time)->format('h:i A') : '-',
                'out_type' => ($lastMovement && $lastMovement->id !== ($firstMovement ? $firstMovement->id : null)) ? $lastMovement->movement_type : null,
                'status' => 'Pending',
                'is_emergency' => $attendance->is_emergency,
            ];
        });

        return response()->json($attendances);
    }

    public function approve($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
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
            Attendance::whereIn('id', $request->ids)->update(['is_approved' => 1]);
            return response()->json(['success' => true, 'message' => 'Selected records approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error approving records: ' . $e->getMessage()], 500);
        }
    }
}
