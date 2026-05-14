<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Worklog;
use App\Models\Customer;
use App\Models\CustomerProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WorklogReportApiController extends Controller
{
    /**
     * Extract current authenticated tenant user
     */
    private function getCurrentUser()
    {
        return Auth::user();
    }

    /**
     * Fetch filter criteria metadata (Users & Customers)
     */
    public function fetchFilters(): JsonResponse
    {
        try {
            // Fetch users who are enabled for worklogs
            $users = User::select('id', 'name')
                ->where('is_worklog', 1)
                ->orderBy('name')
                ->get();

            // Fetch all active customers
            $customers = Customer::select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users,
                'customers' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching filter configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch customer-linked projects (cascading filter)
     */
    public function fetchCustomerProjects(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id'
        ]);

        try {
            $projects = CustomerProject::where('customer_id', $request->customer_id)
                ->select('id', 'project_name')
                ->whereNotNull('project_name')
                ->orderBy('project_name')
                ->get();

            return response()->json([
                'success' => true,
                'projects' => $projects
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching projects: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch Timesheet General Report Data
     */
    public function fetchGeneralReport(Request $request): JsonResponse
    {
        try {
            $query = Worklog::with(['entryType', 'customer', 'module', 'service', 'customerProject', 'user'])
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc');

            // Filtering
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->filled('customer_project_id')) {
                $query->where('customer_project_id', $request->customer_project_id);
            }
            if ($request->filled('from')) {
                $query->whereDate('work_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('work_date', '<=', $request->to);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $worklogs = $query->get();

            // Map/Format payload
            $formattedData = $worklogs->map(function($worklog) {
                return [
                    'id' => $worklog->id,
                    'work_date' => $worklog->work_date instanceof \Carbon\Carbon ? $worklog->work_date->format('Y-m-d') : (string)$worklog->work_date,
                    'user' => $worklog->user ? $worklog->user->name : 'N/A',
                    'user_id' => $worklog->user_id,
                    'entry_type' => $worklog->entryType ? $worklog->entryType->name : ($worklog->entry_type_name ?: 'N/A'),
                    'customer' => $worklog->customer ? $worklog->customer->name : ($worklog->customer_name ?: 'N/A'),
                    'project' => $worklog->customerProject ? $worklog->customerProject->project_name : ($worklog->customer_project_name ?: 'N/A'),
                    'service' => $worklog->service ? $worklog->service->name : ($worklog->service_name ?: 'N/A'),
                    'module' => $worklog->module ? $worklog->module->name : ($worklog->module_name ?: 'N/A'),
                    'hours' => (int)$worklog->hours,
                    'minutes' => (int)$worklog->minutes,
                    'description' => $worklog->description,
                    'status' => $worklog->status,
                ];
            });

            // Accumulate total time and distinct users
            $totalHours = 0;
            $totalMinutes = 0;
            foreach ($worklogs as $w) {
                $totalHours += (int)$w->hours;
                $totalMinutes += (int)$w->minutes;
            }
            if ($totalMinutes >= 60) {
                $totalHours += intval($totalMinutes / 60);
                $totalMinutes = $totalMinutes % 60;
            }

            $uniqueUserIds = $worklogs->pluck('user_id')->filter()->unique()->values();
            
            $summary = [
                'total_users' => $uniqueUserIds->count(),
                'total_hours' => $totalHours,
                'total_minutes' => $totalMinutes,
                'total_entries' => $worklogs->count(),
            ];

            $response = [
                'success' => true,
                'data' => $formattedData,
                'summary' => $summary
            ];

            // Handle Grouping by User if requested
            if ($request->boolean('group_by_user', false)) {
                $response['grouped_data'] = $this->groupWorklogsByUser($formattedData);
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error gathering worklog report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch User Wise Timesheet Report Data
     */
    public function fetchUserWiseReport(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $query = Worklog::with(['customer', 'customerProject', 'module', 'service', 'entryType'])
                ->where('user_id', $request->user_id)
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc');

            if ($request->filled('from')) {
                $query->whereDate('work_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('work_date', '<=', $request->to);
            }
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->filled('customer_project_id')) {
                $query->where('customer_project_id', $request->customer_project_id);
            }

            $worklogs = $query->get();

            $formattedData = $worklogs->map(function($w) {
                return [
                    'id' => $w->id,
                    'work_date' => $w->work_date instanceof \Carbon\Carbon ? $w->work_date->format('Y-m-d') : (string)$w->work_date,
                    'customer' => $w->customer ? $w->customer->name : ($w->customer_name ?: 'N/A'),
                    'project' => $w->customerProject ? $w->customerProject->project_name : ($w->customer_project_name ?: 'N/A'),
                    'module' => $w->module ? $w->module->name : ($w->module_name ?: 'N/A'),
                    'entry_type' => $w->entryType ? $w->entryType->name : ($w->entry_type_name ?: 'N/A'),
                    'hours' => (int)$w->hours,
                    'minutes' => (int)$w->minutes,
                    'description' => $w->description,
                    'status' => $w->status,
                ];
            });

            // Calculate Summary
            $sumHours = (int)$worklogs->sum('hours');
            $sumMinutes = (int)$worklogs->sum('minutes');
            if ($sumMinutes >= 60) {
                $sumHours += intdiv($sumMinutes, 60);
                $sumMinutes = $sumMinutes % 60;
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'summary' => [
                    'total_entries' => $worklogs->count(),
                    'total_hours' => $sumHours,
                    'total_minutes' => $sumMinutes,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error querying user metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Group data utility helper
     */
    private function groupWorklogsByUser($formattedData)
    {
        $grouped = [];

        foreach ($formattedData as $worklog) {
            $userName = $worklog['user'];

            if (!isset($grouped[$userName])) {
                $grouped[$userName] = [
                    'user_name' => $userName,
                    'user_id' => $worklog['user_id'],
                    'entries' => [],
                    'total_hours' => 0,
                    'total_minutes' => 0
                ];
            }

            $grouped[$userName]['entries'][] = $worklog;
            $grouped[$userName]['total_hours'] += (int)$worklog['hours'];
            $grouped[$userName]['total_minutes'] += (int)$worklog['minutes'];
        }

        // Standardize minutes overflow
        foreach ($grouped as $userName => &$userData) {
            if ($userData['total_minutes'] >= 60) {
                $extraHours = intval($userData['total_minutes'] / 60);
                $userData['total_hours'] += $extraHours;
                $userData['total_minutes'] = $userData['total_minutes'] % 60;
            }
        }

        return array_values($grouped);
    }
}
