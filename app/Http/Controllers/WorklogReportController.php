<?php

namespace App\Http\Controllers;

use App\Models\Worklog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorklogReportController extends Controller
{
    /**
     * Display the worklog report page
     */
    public function index()
    {
        $user = $this->getCurrentUser();
        
        // Check if user is authenticated (no worklog permission required for reports)
        if (!$user) {
            return redirect()->back()->with('error', 'You must be logged in to access this page.');
        }
        
        $customers = Customer::select('id', 'name')->orderBy('name')->get();
        $users = \App\Models\User::select('id','name')->orderBy('name')->get();
        return view('worklog.report', compact('customers', 'users'));
    }

    /**
     * Fetch worklogs for report with filtering
     */
    public function fetchWorklogs(Request $request)
    {
        $user = $this->getCurrentUser();
        
        // Check if user is authenticated (no worklog permission required for reports)
        if (!$user) {
            return response()->json(['error' => 'You must be logged in to access this data.'], 401);
        }

        $query = Worklog::with(['entryType', 'customer', 'module', 'service', 'customerProject', 'user'])
            ->orderBy('work_date', 'desc')
            ->orderBy('created_at', 'desc');

        // For reports, show all worklogs unless user_id is explicitly provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Optional filters: customer and date range
        if ($request->filled('customer_id')) {
            // When customer is selected, show worklogs for that customer
            // This will include all customer projects for that customer
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('work_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('work_date', '<=', $request->to);
        }
        // Filter by service/project if provided
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('customer_project_id')) {
            // If specific customer project is selected, filter by that project
            $query->where('customer_project_id', $request->customer_project_id);
        }
        
        // Check if grouping by user is requested
        $groupByUser = $request->boolean('group_by_user', false);

        $worklogs = $query->get();

        // Format the data for report display
        $formattedData = $worklogs->map(function($worklog) {
            return [
                'id' => $worklog->id,
                'work_date' => $worklog->work_date->format('Y-m-d'),
                'user' => $worklog->user ? $worklog->user->name : 'N/A',
                'user_id' => $worklog->user_id,
                'entry_type' => $worklog->entryType ? (object)['name' => $worklog->entryType->name] : (object)['name' => $worklog->entry_type_name ?: 'N/A'],
                'customer' => $worklog->customer ? (object)['name' => $worklog->customer->name] : (object)['name' => $worklog->customer_name ?: 'N/A'],
                // For reports, use customer project as primary project field
                'project' => $worklog->customerProject ? (object)['name' => $worklog->customerProject->name] : (object)['name' => $worklog->customer_project_name ?: 'N/A'],
                'service' => $worklog->service ? (object)['name' => $worklog->service->name] : (object)['name' => $worklog->service_name ?: 'N/A'],
                'customer_project' => $worklog->customerProject ? (object)['name' => $worklog->customerProject->name] : (object)['name' => $worklog->customer_project_name ?: 'N/A'],
                'customer_project_name' => $worklog->customer_project_name,
                'module' => $worklog->module ? (object)['name' => $worklog->module->name] : (object)['name' => $worklog->module_name ?: 'N/A'],
                'hours' => (int)$worklog->hours,
                'minutes' => (int)$worklog->minutes,
                'description' => $worklog->description,
                'status' => $worklog->status,
                'created_at' => $worklog->created_at->format('Y-m-d H:i:s')
            ];
        });

        // Calculate summary statistics
        $totalHours = 0;
        $totalMinutes = 0;
        
        foreach ($worklogs as $worklog) {
            $totalHours += (int)$worklog->hours;
            $totalMinutes += (int)$worklog->minutes;
        }
        
        // Convert minutes to hours
        if ($totalMinutes >= 60) {
            $extraHours = intval($totalMinutes / 60);
            $totalHours += $extraHours;
            $totalMinutes = $totalMinutes % 60;
        }
        
        // Get unique user IDs and count them
        $allUserIds = $worklogs->pluck('user_id');
        $uniqueUserIds = $allUserIds->filter()->unique()->values();
        
        // (debug logging removed)
        
        $summary = [
            'total_users' => $uniqueUserIds->count(),
            'total_hours' => $totalHours,
            'total_minutes' => $totalMinutes,
            'total_entries' => $worklogs->count()
        ];

        $response = [
            'data' => $formattedData,
            'summary' => $summary,
            'debug_info' => [
                'customer_id' => $request->customer_id,
                'total_worklogs' => $worklogs->count(),
                'all_user_ids' => $allUserIds->toArray(),
                'unique_user_ids' => $uniqueUserIds->toArray(),
                'unique_user_count' => $uniqueUserIds->count(),
                'null_user_ids_count' => $allUserIds->filter(function($id) { return $id === null; })->count()
            ]
        ];

        // Add grouped data if requested
        if ($groupByUser) {
            $response['grouped_data'] = $this->groupWorklogsByUser($formattedData);
        }

        return response()->json($response);
    }

    /**
     * Group worklogs by user for report display
     */
    private function groupWorklogsByUser($formattedData)
    {
        $grouped = [];
        
        foreach ($formattedData as $worklog) {
            $userName = $worklog['user'];
            
            if (!isset($grouped[$userName])) {
                $grouped[$userName] = [
                    'entries' => [],
                    'total_hours' => 0,
                    'total_minutes' => 0
                ];
            }
            
            $grouped[$userName]['entries'][] = $worklog;
            $grouped[$userName]['total_hours'] += (int)$worklog['hours'];
            $grouped[$userName]['total_minutes'] += (int)$worklog['minutes'];
        }
        
        // Convert minutes to hours for each user
        foreach ($grouped as $userName => &$userData) {
            if ($userData['total_minutes'] >= 60) {
                $extraHours = intval($userData['total_minutes'] / 60);
                $userData['total_hours'] += $extraHours;
                $userData['total_minutes'] = $userData['total_minutes'] % 60;
            }
            
            // Ensure values are integers
            $userData['total_hours'] = (int)$userData['total_hours'];
            $userData['total_minutes'] = (int)$userData['total_minutes'];
        }
        
        return $grouped;
    }

    /**
     * Get worklog statistics for report
     */
    public function getStats()
    {
        $user = $this->getCurrentUser();
        
        // Check if user is authenticated (no worklog permission required for reports)
        if (!$user) {
            return response()->json(['error' => 'You must be logged in to access this data.'], 401);
        }
        
        // For reports, get stats for all worklogs
        $stats = Worklog::selectRaw('
            COUNT(*) as total_entries,
            SUM(hours) as total_hours,
            SUM(minutes) as total_minutes,
            COUNT(DISTINCT user_id) as total_users,
            COUNT(DISTINCT DATE(work_date)) as total_days
        ')->first();

        // Convert total minutes to hours
        $totalHours = $stats->total_hours + floor($stats->total_minutes / 60);
        $remainingMinutes = $stats->total_minutes % 60;

        return response()->json([
            'total_entries' => $stats->total_entries ?? 0,
            'total_hours' => $totalHours ?? 0,
            'total_minutes' => $remainingMinutes ?? 0,
            'total_users' => $stats->total_users ?? 0,
            'total_days' => $stats->total_days ?? 0
        ]);
    }

    /**
     * User-wise report: page
     */
    public function userReport()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->back()->with('error', 'You must be logged in to access this page.');
        }
        // minimal user list for filter (id, name)
        $users = \App\Models\User::select('id','name')->orderBy('name')->get();
        $customers = \App\Models\Customer::select('id','name')->orderBy('name')->get();
        return view('worklog.user-report', compact('users','customers'));
    }

    /**
     * Fetch user-wise worklogs (date-wise list)
     */
    public function fetchUserWorklogs(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return response()->json(['error' => 'You must be logged in to access this data.'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_project_id' => 'nullable|integer'
        ]);

        $query = Worklog::with(['customer','customerProject','module','service','entryType'])
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

        $data = $worklogs->map(function($w){
            return [
                'date' => $w->work_date instanceof \Carbon\Carbon ? $w->work_date->format('Y-m-d') : (string)$w->work_date,
                'customer' => $w->customer?->name ?? $w->customer_name,
                'project' => $w->customerProject?->name ?? $w->customer_project_name,
                'module' => $w->module?->name ?? $w->module_name,
                'entry_type' => $w->entryType?->name ?? $w->entry_type_name,
                'hours' => (int)$w->hours,
                'minutes' => (int)$w->minutes,
                'description' => $w->description,
            ];
        });

        // summary
        $sumHours = (int)$worklogs->sum('hours');
        $sumMinutes = (int)$worklogs->sum('minutes');
        if ($sumMinutes >= 60) {
            $sumHours += intdiv($sumMinutes, 60);
            $sumMinutes = $sumMinutes % 60;
        }

        return response()->json([
            'data' => $data,
            'summary' => [
                'total_entries' => $worklogs->count(),
                'total_hours' => $sumHours,
                'total_minutes' => $sumMinutes,
            ]
        ]);
    }

    /**
     * Customers for selected user from worklogs (distinct)
     */
    public function fetchCustomersForUser(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $customerIds = Worklog::where('user_id', $request->user_id)
            ->whereNotNull('customer_id')
            ->distinct()
            ->pluck('customer_id');
        $customers = \App\Models\Customer::whereIn('id', $customerIds)->select('id','name')->orderBy('name')->get();
        return response()->json(['data' => $customers]);
    }

    /**
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        // Check if user is authenticated via Auth facade (super admin)
        if (Auth::check()) {
            return Auth::user();
        }
        
        // Check if user is authenticated via session (tenant users)
        if (session()->has('user_id')) {
            $userId = session('user_id');
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user;
                }
            } catch (\Exception $e) {
                // If user not found, return null
                return null;
            }
        }
        
        return null;
    }
}
