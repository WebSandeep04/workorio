<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{

    public function index()
    {
        $employees = \App\Models\Employee::all()->where('status', 'active');
        return view('tracking.index', compact('employees'));
    }

    public function fetchLocations(Request $request)
    {
        $query = \App\Models\EmployeeLocation::with('employee');

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('date')) {
            $date = $request->date;
            $query->whereDate('tracked_at', $date);
        } else {
            // Default to today if no date is provided
            $query->whereDate('tracked_at', now());
        }

        $locations = $query->orderBy('tracked_at', 'asc')->get();

        // Calculate statuses for employees to determine marker color
        $employeeIds = $locations->pluck('employee_id')->unique()->values();
        $dateToCheck = $request->has('date') ? $request->date : \Carbon\Carbon::now()->format('Y-m-d');
        
        // Optimize: Only fetch necessary fields and relationships
        $users = \App\Models\User::whereIn('employee_id', $employeeIds)
            ->with(['attendances' => function($q) use ($dateToCheck) {
                $q->select('id', 'user_id', 'date')
                  ->whereDate('date', $dateToCheck)
                  ->with(['movements' => function($m) {
                      $m->select('id', 'attendance_id', 'movement_type', 'movement_action', 'time')
                        ->orderBy('time');
                  }]);
            }])
            ->select('id', 'employee_id')
            ->get()
            ->keyBy('employee_id');

        $employeeDetails = [];

        foreach ($employeeIds as $empId) {
             $user = $users->get($empId);
             $color = '#dc3545'; // Red (Default/Punched Out)
             $details = [
                 'office_in' => '-',
                 'office_out' => '-',
                 'field_in' => '-',
                 'field_out' => '-',
                 'break' => '-'
             ];
             
             if ($user && $user->attendances->isNotEmpty()) {
                 $attendance = $user->attendances->first();
                 $movements = $attendance->movements; // Already loaded sorted
                 
                 // Check Break
                 $breakStart = $movements->where('movement_type', 'break')->where('movement_action', 'start')->last();
                 $breakEnd = $movements->where('movement_type', 'break')->where('movement_action', 'end')->last();
                 $isOnBreak = $breakStart && (!$breakEnd || $breakEnd->time < $breakStart->time);
                 
                 if ($isOnBreak) {
                     $color = '#ffc107'; // Yellow
                     $details['break'] = 'On Break (' . \Carbon\Carbon::parse($breakStart->time)->format('h:i A') . ')';
                 } else {
                     // Check In (Office or Field)
                     $officeIn = $movements->where('movement_type', 'office')->where('movement_action', 'in')->last();
                     $officeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                     $isOffice = $officeIn && (!$officeOut || $officeOut->time < $officeIn->time);

                     $fieldIn = $movements->where('movement_type', 'field')->where('movement_action', 'in')->last();
                     $fieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                     $isField = $fieldIn && (!$fieldOut || $fieldOut->time < $fieldIn->time);
                     
                     if ($isOffice || $isField) {
                         $color = '#28a745'; // Green
                     }
                     
                     // Determine Primary Status
                     $statusText = 'Not Started';
                     
                     // Check if currently on break
                     if ($isOnBreak) {
                         $statusText = 'On Break';
                     } else {
                         // Check active sessions
                         if ($isOffice) {
                             $statusText = 'Punched In'; // Office In
                         } elseif ($isField) {
                             $statusText = 'Field In';
                         } else {
                             // Not active in office or field. Check what was the last action.
                             // We need to look at the very last valid movement to decide if it was an "Out"
                             $lastOfficeOut = $movements->where('movement_type', 'office')->where('movement_action', 'out')->last();
                             $lastFieldOut = $movements->where('movement_type', 'field')->where('movement_action', 'out')->last();
                             
                             $lastOutTime = null;
                             $outType = null;
                             
                             if ($lastOfficeOut) {
                                 $lastOutTime = $lastOfficeOut->time;
                                 $outType = 'office';
                             }
                             
                             if ($lastFieldOut) {
                                 if (!$lastOutTime || $lastFieldOut->time > $lastOutTime) {
                                     $lastOutTime = $lastFieldOut->time;
                                     $outType = 'field';
                                 }
                             }
                             
                             if ($outType === 'office') {
                                 $statusText = 'Punched Out';
                             } elseif ($outType === 'field') {
                                 $statusText = 'Field Out';
                             }
                         }
                     }
                     
                     $details['current_status'] = $statusText;
                 }
             }
             $employeeDetails[$empId] = [
                 'color' => $color,
                 'details' => $details
             ];
        }

        return response()->json([
            'success' => true,
            'data' => $locations,
            'employee_details' => $employeeDetails
        ]);
    }
}

