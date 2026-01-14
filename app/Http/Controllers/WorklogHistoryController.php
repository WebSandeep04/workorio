<?php

namespace App\Http\Controllers;

use App\Models\Worklog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorklogHistoryController extends Controller
{
    public function index()
    {
        $user = $this->getCurrentUser();
        
        // Check if user has worklog permission
        if (!$user || !$user->is_worklog) {
            return redirect()->back()->with('error', 'You do not have permission to access worklog functionality.');
        }
        
        return view('worklog.history');
    }


    public function fetchWorklogs(Request $request)
    {
        $user = $this->getCurrentUser();
        $page = $request->get('page', 1);
        $perPage = 10;

        $query = Worklog::with(['entryType', 'customer', 'project', 'module', 'service', 'customerProject', 'user'])
            ->orderBy('work_date', 'desc')
            ->orderBy('created_at', 'desc');

        // For worklog history, always filter by current user unless explicitly requesting another user's data
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        } else {
            // Default to current user's worklogs for worklog history
            $query->where('user_id', $user->id);
        }

        // Optional filters: customer and date range
        if ($request->filled('customer_id')) {
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
            $query->where('customer_project_id', $request->customer_project_id);
        }
        
        // Check if grouping by user is requested
        $groupByUser = $request->boolean('group_by_user', false);

        $worklogs = $query->paginate($perPage);

        // Format the data to remove time from date and add status
        $formattedData = collect($worklogs->items())->map(function($worklog) {
            return [
                'id' => $worklog->id,
                'work_date' => $worklog->work_date,
                'user' => $worklog->user ? $worklog->user->name : 'N/A',
                'entry_type' => $worklog->entryType ?: (object)['name' => $worklog->entry_type_name ?: 'N/A'],
                'customer' => $worklog->customer ?: (object)['name' => $worklog->customer_name ?: 'N/A'],
                // For history, use service as project
                'service' => $worklog->service ?: (object)['name' => $worklog->service_name ?: 'N/A'],
                'customer_project' => $worklog->customerProject ?: (object)['name' => $worklog->customer_project_name ?: 'N/A'],
                'customer_project_name' => $worklog->customer_project_name,
                'module' => $worklog->module ?: (object)['name' => $worklog->module_name ?: 'N/A'],
                'hours' => $worklog->hours,
                'minutes' => $worklog->minutes,
                'description' => $worklog->description,
                'status' => $worklog->status,
                'created_at' => $worklog->created_at->format('Y-m-d H:i:s')];
        });

        // Calculate summary statistics
        $summary = [
            'total_users' => $worklogs->getCollection()->pluck('user_id')->unique()->count(),
            'total_hours' => $worklogs->getCollection()->sum('hours'),
            'total_minutes' => $worklogs->getCollection()->sum('minutes'),
            'total_entries' => $worklogs->getCollection()->count()
        ];

        // Convert minutes to hours if needed
        if ($summary['total_minutes'] >= 60) {
            $extraHours = intval($summary['total_minutes'] / 60);
            $summary['total_hours'] += $extraHours;
            $summary['total_minutes'] = $summary['total_minutes'] % 60;
        }

        $response = [
            'data' => $formattedData,
            'summary' => $summary,
            'pagination' => [
                'current_page' => $worklogs->currentPage(),
                'last_page' => $worklogs->lastPage(),
                'per_page' => $worklogs->perPage(),
                'total' => $worklogs->total(),
                'from' => $worklogs->firstItem(),
                'to' => $worklogs->lastItem(),
                'has_more_pages' => $worklogs->hasMorePages(),
                'has_previous_pages' => $worklogs->hasPages() && $worklogs->currentPage() > 1]
        ];

        // Add grouped data if requested
        if ($groupByUser) {
            $response['grouped_data'] = $this->groupWorklogsByUser($formattedData);
        }

        return response()->json($response);
    }

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
            $grouped[$userName]['total_hours'] += $worklog['hours'];
            $grouped[$userName]['total_minutes'] += $worklog['minutes'];
        }
        
        // Convert minutes to hours for each user
        foreach ($grouped as $userName => &$userData) {
            if ($userData['total_minutes'] >= 60) {
                $extraHours = intval($userData['total_minutes'] / 60);
                $userData['total_hours'] += $extraHours;
                $userData['total_minutes'] = $userData['total_minutes'] % 60;
            }
        }
        
        return $grouped;
    }

    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        $worklog = Worklog::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Prevent deletion if worklog is approved or rejected
        if (in_array($worklog->status, ['approved', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete worklog that has been approved or rejected.'
            ], 422);
        }

        $worklog->delete();

        return response()->json(['success' => true]);
    }

    public function getWorklogStats()
    {
        $user = $this->getCurrentUser();
        $stats = Worklog::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total_entries,
                SUM(hours) as total_hours,
                SUM(minutes) as total_minutes,
                COUNT(DISTINCT DATE(work_date)) as total_days,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as total_approved
            ')
            ->first();

        // Convert total minutes to hours
        $totalHours = $stats->total_hours + floor($stats->total_minutes / 60);
        $remainingMinutes = $stats->total_minutes % 60;

        return response()->json([
            'total_entries' => $stats->total_entries ?? 0,
            'total_hours' => $totalHours ?? 0,
            'total_minutes' => $remainingMinutes ?? 0,
            'total_days' => $stats->total_days ?? 0,
            'total_pending' => $stats->total_pending ?? 0,
            'total_approved' => $stats->total_approved ?? 0
        ]);
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
            $userName = session('user_name');
            $userRole = session('user_role');
            $tenantId = session('tenant_id');
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user; // Return the actual user model with real is_worklog value
                }
            } catch (\Exception $e) {
                // If user not found, create a mock user with is_worklog = false
            }
            
            // Create a mock user object for tenant users (fallback)
            return new class($userId, $userName, $userRole, $tenantId) {
                public $id;
                public $name;
                public $role_id;
                public $tenant_id;
                public $is_worklog;
                public $created_at;
                
                public function __construct($id, $name, $roleId, $tenantId) {
                    $this->id = $id;
                    $this->name = $name;
                    $this->role_id = $roleId;
                    $this->tenant_id = $tenantId;
                    $this->is_worklog = false; // Default to false for safety
                    $this->created_at = now(); // Default to current time
                }
            };
        }
        
        return null;
    }
}
