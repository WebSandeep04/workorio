<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnlockAttendanceController extends Controller
{
    public function index()
    {
        return view('attendance.unlock');
    }

    /**
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        if (auth()->check()) {
            return auth()->user();
        }
        
        if (session()->has('user_id')) {
            $userId = session('user_id');
            return \App\Models\User::find($userId);
        }
        
        return null;
    }

    public function fetch(Request $request)
    {
        $query = DB::table('attendance_unlock_logs')
            ->leftJoin('users', 'attendance_unlock_logs.unlocked_by', '=', 'users.id')
            ->select('attendance_unlock_logs.*', 'users.name as unlocked_by_name');

        if ($request->search) {
            $searchTerm = $request->search;
            $query->where('attendance_unlock_logs.reason', 'like', "%{$searchTerm}%")
                  ->orWhere('users.name', 'like', "%{$searchTerm}%");
        }

        $logs = $query->orderBy('attendance_unlock_logs.created_at', 'desc')->paginate(20);

        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'date' => Carbon::parse($log->date)->format('d M Y'),
                'unlock_date' => Carbon::parse($log->unlock_date)->format('d M Y'),
                'reason' => $log->reason,
                'unlocked_by' => $log->unlocked_by_name ?? 'System',
                'unlocked_by_id' => $log->unlocked_by,
                'created_at' => Carbon::parse($log->created_at)->setTimezone('Asia/Kolkata')->format('d M Y h:i A'),
            ];
        });

        return response()->json($logs);
    }

    public function unlock($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->is_approved = 0;
            $attendance->is_locked = 0; // Unlock the record
            $attendance->save();
            
            return response()->json(['success' => true, 'message' => 'Attendance unlocked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error unlocking attendance: ' . $e->getMessage()], 500);
        }
    }

    public function unlockBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:attendance,id'
        ]);

        try {
            Attendance::whereIn('id', $request->ids)->update([
                'is_approved' => 0,
                'is_locked' => 0
            ]);
            return response()->json(['success' => true, 'message' => 'Selected records unlocked successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error unlocking records: ' . $e->getMessage()], 500);
        }
    }

    public function unlockByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Update all attendance records for that date (unlock approval and lock)
            $affected = Attendance::whereDate('date', $request->date)
                ->update([
                    'is_approved' => 0,
                    'is_locked' => 0
                ]);

            $user = $this->getCurrentUser();

            // Create log entry in our new table
            DB::table('attendance_unlock_logs')->insert([
                'date' => Carbon::now('Asia/Kolkata')->toDateString(),
                'unlock_date' => $request->date,
                'reason' => $request->reason,
                'unlocked_by' => $user ? $user->id : null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => "Successfully unlocked {$affected} records for " . Carbon::parse($request->date)->format('d M Y')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
