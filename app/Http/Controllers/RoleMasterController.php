<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TenantDatabaseService;
use App\Services\TenantFeatureService;

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
            TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }
        
        if (!$tenant) {
            abort(403, 'No tenant associated with user');
        }


        $roles = Role::where('is_custom', true)
                    ->withCount('users')
                    ->get();

        $tenantFeatures = TenantFeatureService::getFeatures($tenant);

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
            TenantDatabaseService::setDefaultConnection($tenantId);
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
            TenantDatabaseService::setDefaultConnection($tenantId);
        } else {
            $tenant = $user->tenant;
        }
        
        if (!$tenant) {
            abort(403, 'No tenant associated with user');
        }

        $tenantFeatures = TenantFeatureService::getFeatures($tenant);

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
            TenantDatabaseService::setDefaultConnection($tenantId);
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
                TenantDatabaseService::setDefaultConnection($tenantId);
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
            TenantDatabaseService::setDefaultConnection($tenantId);
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
            TenantDatabaseService::setDefaultConnection($tenantId);
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

