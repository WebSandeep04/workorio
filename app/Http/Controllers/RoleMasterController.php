<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleMasterController extends Controller
{
    public function index()
    {
        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            // For session-based tenant users, get tenant from session
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                abort(403, 'No tenant associated with user');
            }
            // Switch to master database to get tenant info
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            // Switch back to tenant database
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }
        
        if (!$tenant) {
            abort(403, 'No tenant associated with user');
        }


        $roles = Role::where('is_custom', true)
                    ->withCount('users')
                    ->get();

        $tenantFeatures = [
            'sales' => [
                'enabled' => (bool) ($tenant->is_sales_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_sales_setup_enabled ?? true),
                'permissions' => [
                    // Submenu-level permissions for Sales
                    'sales.alldata' => 'Sales: All Data',
                    'sales.analytics' => 'Sales: Analytics',
                    'sales.leads' => 'Sales: Leads',
                    'sales.indiamart' => 'Sales: IndiaMART Leads',
                    'sales.indiamart.junk' => 'Sales: IndiaMART Junk Leads',
                    'sales.myleads' => 'Sales: My Leads',
                    'sales.teamleads' => 'Sales: Team Leads',
                    'sales.assignedleads' => 'Sales: Assigned Leads',
                    'sales.followup' => 'Sales: Follow Up',
                    'sales.payment_followup' => 'Sales: Payment Followup',
                    // Calling
                    'sales.calling' => 'Sales: Calling',
                    'sales.calling.my' => 'Sales: My Calls',
                    'sales.calling.junk' => 'Sales: Junk Calls',
                    'sales.calling.todays' => "Sales: Today's Calls",
                    // Setup
                    'sales.setup' => 'Sales Setup Management']
            ],
            'worklog' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_work_setup_enabled ?? true),
                'permissions' => [
                    // Submenu-level permissions for Worklog
                    'worklog.entry' => 'Worklog: Entry',
                    'worklog.history' => 'Worklog: History',
                    'worklog.leave' => 'Worklog: Leave',
                    'worklog.approvals' => 'Worklog: Approvals',
                    'worklog.missing_summary' => 'Worklog: Missing Entries Summary',
                    // Task permissions
                    'task.view' => 'Task: View All Tasks',
                    'task.create' => 'Task: Create Tasks',
                    'task.edit' => 'Task: Edit Tasks',
                    'task.delete' => 'Task: Delete Tasks',
                    'task.toggle' => 'Task: Toggle Done/Pending Status',
                    'task.my_tasks' => 'Task: View My Assigned Tasks',
                    'task.my_created' => 'Task: View My Created Tasks',
                    'task.status' => 'Task: Task Status Management',
                    // Setup
                    'worklog.setup' => 'Worklog Setup Management']
            ],
            'attendance' => [
                'enabled' => (bool) ($tenant->is_attendance_enabled ?? true),
                'setup_enabled' => false, // Attendance doesn't have setup
                'permissions' => [
                    // Submenu-level permissions for Attendance
                    'attendance.entry' => 'Attendance: Entry',
                    'attendance.history' => 'Attendance: History']
            ],
            'subscription' => [
                'enabled' => (bool) ($tenant->is_subscription_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    // Subscription permissions
                    'subscription.view' => 'Subscription: View Subscriptions',
                    'subscription.create' => 'Subscription: Create Subscriptions',
                    'subscription.edit' => 'Subscription: Edit Subscriptions',
                    'subscription.delete' => 'Subscription: Delete Subscriptions',
                ]
            ],
            'documents' => [
                'enabled' => (bool) ($tenant->is_document_management_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    // Document permissions
                    'documents.manage' => 'Manage Documents',
                    'documents.my_documents' => 'My Documents']
            ],
            'user_management' => [
                'enabled' => (bool) ($tenant->is_user_setup_enabled ?? true),
                'setup_enabled' => false, // User management doesn't have setup
                'permissions' => [
                    'user.view' => 'View Users',
                    'user.create' => 'Create Users',
                    'user.edit' => 'Edit Users',
                    'user.delete' => 'Delete Users',
                    'role.manage' => 'Manage Roles']
            ],
            'reports' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => false, // Reports don't have setup
                'permissions' => [
                    'attendance.stats' => 'Attendance Stats',
                    'attendance.report' => 'Attendance Report',
                    'worklog.history' => 'Worklog Report']
            ],
            'calendar' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'permissions' => [
                    // Calendar main features
                    'calendar.view' => 'Calendar: View Calendar',
                    'calendar.client_event_links' => 'Calendar: Client-Event Links',
                    // Calendar Setup
                    'calendar.events' => 'Calendar: Manage Calendar Events',
                    'calendar.status' => 'Calendar: Manage Calendar Status',
                    'calendar.status_checklist' => 'Calendar: Status-Checklist Management',
                    'calendar.common_events' => 'Calendar: Manage Common Events',
                    'calendar.social_handles' => 'Calendar: Manage Social Handles',
                    'calendar.clients' => 'Calendar: Manage Calendar Clients',
                    'calendar.client_social' => 'Calendar: Client Social Handles Management',
                    // Setup
                    'calendar.setup' => 'Calendar Setup Management']
            ],
            'petty_cash' => [
                'enabled' => (bool) ($tenant->is_petty_cash_enable ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'petty_cash.view' => 'Petty Cash: View Cash',
                ]
            ],
            'approvals' => [
                'enabled' => (bool) ($tenant->is_approval_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'approvals.petty' => 'Approvals: Petty Approval',
                ]
            ],
            'contact_management' => [
                'enabled' => (bool) ($tenant->is_contact_management ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'contact_management.access' => 'Contact Management: Access',
                ]
            ]
        ];


        return view('role-master.index', compact('tenantFeatures'));
    }

    public function fetch(Request $request)
    {
        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                return response()->json([]);
            }
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }
        
        if (!$tenant) {
            return response()->json([]);
        }

        $query = Role::where('is_custom', true)->withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->paginate(10);
        return response()->json($roles);
    }

    public function getPermissions()
    {
        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            // For session-based tenant users, get tenant from session
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                abort(403, 'No tenant associated with user');
            }
            // Switch to master database to get tenant info
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            // Switch back to tenant database
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }
        
        if (!$tenant) {
            abort(403, 'No tenant associated with user');
        }

        $tenantFeatures = [
            'sales' => [
                'enabled' => (bool) ($tenant->is_sales_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_sales_setup_enabled ?? true),
                'permissions' => [
                    'sales.alldata' => 'Sales: All Data',
                    'sales.analytics' => 'Sales: Analytics',
                    'sales.leads' => 'Sales: Leads',
                    'sales.indiamart' => 'Sales: IndiaMART Leads',
                    'sales.indiamart.junk' => 'Sales: IndiaMART Junk Leads',
                    'sales.myleads' => 'Sales: My Leads',
                    'sales.teamleads' => 'Sales: Team Leads',
                    'sales.assignedleads' => 'Sales: Assigned Leads',
                    'sales.followup' => 'Sales: Follow Up',
                    'sales.payment_followup' => 'Sales: Payment Followup',
                    'sales.calling' => 'Sales: Calling',
                    'sales.calling.my' => 'Sales: My Calls',
                    'sales.calling.junk' => 'Sales: Junk Calls',
                    'sales.calling.todays' => "Sales: Today's Calls",
                    'sales.setup' => 'Sales Setup Management']
            ],
            'worklog' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_work_setup_enabled ?? true),
                'permissions' => [
                    'worklog.entry' => 'Worklog: Entry',
                    'worklog.history' => 'Worklog: History',
                    'worklog.leave' => 'Worklog: Leave',
                    'worklog.approvals' => 'Worklog: Approvals',
                    'worklog.missing_summary' => 'Worklog: Missing Entries Summary',
                    'task.view' => 'Task: View All Tasks',
                    'task.create' => 'Task: Create Tasks',
                    'task.edit' => 'Task: Edit Tasks',
                    'task.delete' => 'Task: Delete Tasks',
                    'task.toggle' => 'Task: Toggle Done/Pending Status',
                    'task.my_tasks' => 'Task: View My Assigned Tasks',
                    'task.my_created' => 'Task: View My Created Tasks',
                    'task.status' => 'Task: Task Status Management',
                    'worklog.setup' => 'Worklog Setup Management']
            ],
            'attendance' => [
                'enabled' => (bool) ($tenant->is_attendance_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'attendance.entry' => 'Attendance: Entry',
                    'attendance.history' => 'Attendance: History']
            ],
            'subscription' => [
                'enabled' => (bool) ($tenant->is_subscription_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'subscription.view' => 'Subscription: View Subscriptions',
                    'subscription.create' => 'Subscription: Create Subscriptions',
                    'subscription.edit' => 'Subscription: Edit Subscriptions',
                    'subscription.delete' => 'Subscription: Delete Subscriptions',
                ]
            ],
            'documents' => [
                'enabled' => (bool) ($tenant->is_document_management_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'documents.manage' => 'Manage Documents',
                    'documents.my_documents' => 'My Documents']
            ],
            'user_management' => [
                'enabled' => (bool) ($tenant->is_user_setup_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'user.view' => 'View Users',
                    'user.create' => 'Create Users',
                    'user.edit' => 'Edit Users',
                    'user.delete' => 'Delete Users',
                    'role.manage' => 'Manage Roles']
            ],
            'reports' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'attendance.stats' => 'Attendance Stats',
                    'attendance.report' => 'Attendance Report',
                    'worklog.history' => 'Worklog Report']
            ],
            'calendar' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'permissions' => [
                    // Calendar main features
                    'calendar.view' => 'Calendar: View Calendar',
                    'calendar.client_event_links' => 'Calendar: Client-Event Links',
                    // Calendar Setup
                    'calendar.events' => 'Calendar: Manage Calendar Events',
                    'calendar.status' => 'Calendar: Manage Calendar Status',
                    'calendar.status_checklist' => 'Calendar: Status-Checklist Management',
                    'calendar.common_events' => 'Calendar: Manage Common Events',
                    'calendar.social_handles' => 'Calendar: Manage Social Handles',
                    'calendar.clients' => 'Calendar: Manage Calendar Clients',
                    'calendar.client_social' => 'Calendar: Client Social Handles Management',
                    // Setup
                    'calendar.setup' => 'Calendar Setup Management']
            ],
            'petty_cash' => [
                'enabled' => (bool) ($tenant->is_petty_cash_enable ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'petty_cash.view' => 'Petty Cash: View Cash',
                ]
            ],
            'approvals' => [
                'enabled' => (bool) ($tenant->is_approval_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'approvals.petty' => 'Approvals: Petty Approval',
                ]
            ],
            'contact_management' => [
                'enabled' => (bool) ($tenant->is_contact_management ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'contact_management.access' => 'Contact Management: Access',
                ]
            ]
        ];

        // Return only permissions for enabled features
        $permissions = [];
        foreach ($tenantFeatures as $feature => $featureData) {
            $isEnabled = isset($featureData['enabled']) ? (bool) $featureData['enabled'] : false;
            $hasSetup = isset($featureData['setup_enabled']) ? (bool) $featureData['setup_enabled'] : false;
            $shouldShow = $isEnabled || $hasSetup;
            
            if ($shouldShow && isset($featureData['permissions'])) {
                $permissions[$feature] = $featureData['permissions'];
            }
        }

        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string'
        ]);

        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            // For session-based tenant users, get tenant from session
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                abort(403, 'No tenant associated with user');
            }
            // Switch to master database to get tenant info
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            // Switch back to tenant database
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
            // Create a mock user for session-based users
            $user = (object) ['id' => session('user_id', 1)];
        } else {
            $tenant = $user->tenant;
        }

        if (!$tenant) {
            abort(403, 'No tenant associated with user');
        }

        DB::beginTransaction();
        try {
            // Create the role
            $role = Role::create([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'is_custom' => true,
                'created_by' => $user->id]);

            // Store permissions as JSON for now (you can implement proper permission system later)
            $role->permissions_data = json_encode($request->permissions);
            $role->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Role created successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to create role: ' . $e->getMessage()], 500);
        }
    }

    public function edit($roleId)
    {
        try {
            // Get user from Auth or session
            $user = Auth::user();
            if (!$user) {
                // For session-based tenant users, get tenant from session
                $tenantId = session('tenant_id');
                if (!$tenantId) {
                    return response()->json(['success' => false, 'message' => 'No tenant associated with user'], 403);
                }
                // Switch to master database to get tenant info
                DB::setDefaultConnection('mysql');
                $tenant = Tenant::find($tenantId);
                // Switch back to tenant database
                \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
            } else {
                $tenant = $user->tenant;
            }

            if (!$tenant) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to modify this role'], 403);
            }

            // Find the role after setting correct database connection
            $role = Role::find($roleId);
            
            if (!$role) {
                return response()->json(['success' => false, 'message' => 'Role not found'], 404);
            }

            // Get role permissions
            $permissions = [];
            if ($role->permissions_data) {
                // Handle both JSON string and array
                if (is_string($role->permissions_data)) {
                    $permissions = json_decode($role->permissions_data, true) ?? [];
                } else if (is_array($role->permissions_data)) {
                    $permissions = $role->permissions_data;
                }
            }

            return response()->json([
                'role' => $role,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            \Log::error('Role edit error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error loading role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $roleId)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string'
        ]);

        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            // For session-based tenant users, get tenant from session
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                abort(403, 'No tenant associated with user');
            }
            // Switch to master database to get tenant info
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            // Switch back to tenant database
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }

        if (!$tenant) {
            abort(403, 'Unauthorized to modify this role');
        }

        // Find the role after setting correct database connection
        $role = Role::findOrFail($roleId);

        DB::beginTransaction();
        try {
            $role->update([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'permissions_data' => json_encode($request->permissions)]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Role updated successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Failed to update role: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($roleId)
    {
        // Get user from Auth or session
        $user = Auth::user();
        if (!$user) {
            // For session-based tenant users, get tenant from session
            $tenantId = session('tenant_id');
            if (!$tenantId) {
                abort(403, 'No tenant associated with user');
            }
            // Switch to master database to get tenant info
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::find($tenantId);
            // Switch back to tenant database
            \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }

        if (!$tenant) {
            abort(403, 'Unauthorized to delete this role');
        }

        // Find the role after setting correct database connection
        $role = Role::findOrFail($roleId);

        if ($role->users()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete role: Users are assigned to this role'], 400);
        }

        $role->delete();
        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }
}
