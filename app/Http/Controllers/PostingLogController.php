<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class PostingLogController extends Controller
{
    public function index()
    {
        return view('setup.posting_log.index');
    }

    public function fetch(Request $request)
    {
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        if ($endDate->isFuture()) {
            $endDate = Carbon::today();
        }

        $records = Attendance::with(['user.employee'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where(function ($query) {
                $query->where('is_approved', '!=', 1)
                      ->orWhere('is_locked', 0);
            })
            ->get();

        $data = $records->map(function ($record) {
            $statusStr = [];
            if ($record->is_approved != 1) {
                $statusStr[] = 'Not Approved';
            }
            if ($record->is_locked == 0) {
                $statusStr[] = 'Not Locked';
            }

            $employeeName = 'Unknown';
            $employeeCode = 'N/A';
            
            if ($record->user) {
                if ($record->user->employee) {
                    $employeeName = $record->user->employee->name;
                    $employeeCode = $record->user->employee->employee_code ?: 'N/A';
                } else {
                    $employeeName = $record->user->name;
                }
            }

            return [
                'employee_name' => $employeeName,
                'employee_code' => $employeeCode,
                'date' => Carbon::parse($record->date)->format('Y-m-d'),
                'reason' => implode(' & ', $statusStr)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
