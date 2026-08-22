<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;

class MenuBuilder
{
    public static function build(?User $user = null): array
    {
        // If not passed, check if user is authenticated through Laravel Auth (master database users)
        if (!$user) {
            $user = Auth::user();
        }
        
        // If not authenticated through Auth or passed, check session for tenant users
        if (!$user) {
            $userId = session('user_id');
            $userName = session('user_name');
            $userRole = session('user_role');
            $tenantId = session('tenant_id');
            
            if (!$userId || !$userRole) {
                return [];
            }
            
            // Load actual user data from tenant database
            try {
                $dbUser = User::find($userId);
                if ($dbUser) {
                    $user = $dbUser;
                    $user->tenant_id = $tenantId;
                } else {
                    return [];
                }
            } catch (\Exception $e) {
                return [];
            }
        }

        $connectionName = DB::getDefaultConnection();
        $isSuperAdmin = $user && $user->role_id == 3 && $connectionName === 'mysql';

        if ($isSuperAdmin) {
            return [];
        }

        // Get tenant information
        if ($user->tenant_id && !$user->tenant) {
            // For tenant users, get tenant from master database
            try {
                // Switch to master database to get tenant info
                DB::setDefaultConnection('mysql');
                $tenant = Tenant::find($user->tenant_id);
                // Switch back to tenant database
                TenantDatabaseService::setDefaultConnection($user->tenant_id);
            } catch (\Exception $e) {
                // If tenant not found, return empty menu
                return [];
            }
        } else {
            $tenant = optional($user->tenant);
        }
        
        // Set tenant on user object for future use
        if ($tenant && !$user->tenant) {
            $user->tenant = $tenant;
        }
        
        // Load role data for session users
        if (!$user->role && $user->role_id) {
            try {
                $role = Role::find($user->role_id);
                $user->role = $role;
            } catch (\Exception $e) {
                // If role not found, continue with null role
            }
        }
        
        // Load user's manager status for session users
        if ($user->is_manager === null && $user->id) {
            try {
                $hasSubordinates = DB::table('user_managers')->where('manager_id', $user->id)->exists();
                $user->is_manager = $hasSubordinates ? 1 : 0;
            } catch (\Exception $e) {
                // If table not found or error, set default
                $user->is_manager = 0;
            }
        }
        
        $roleName = optional($user->role)->role_name;
        $roleId = $user->role_id;

        $menuConfig = config('menu');
        $hubs = [];
        
        // Loop through all configured hubs
        if (isset($menuConfig['admin_hubs'])) {
            foreach ($menuConfig['admin_hubs'] as $hub) {
                $filteredHub = $hub;
                $filteredHub['sections'] = [];
                
                foreach ($hub['sections'] as $section) {
                    // Global Feature Flag evaluation (Tenant Tier)
                    if (isset($section['feature_flag']) && (!$tenant || !$tenant->{$section['feature_flag']})) {
                        continue;
                    }
                    
                    // Pass into the core filtering engine (which handles both Admins and Custom RBAC correctly)
                    $filteredSection = static::filterItems($section, $user);
                    
                    // Sort items for Software Setup specifically alphabetically
                    if ($hub['key'] === 'software_setup' && !empty($filteredSection['items'])) {
                        usort($filteredSection['items'], function($a, $b) {
                            return strcasecmp($a['title'], $b['title']);
                        });
                    }
                    
                    // Only mount the section to the sidebar if it survived filtering or is a standalone section
                    if (!empty($filteredSection['items']) || (!empty($filteredSection) && isset($filteredSection['route']))) {
                        $filteredHub['sections'][] = $filteredSection;
                    }
                }
                
                if (!empty($filteredHub['sections'])) {
                    $hubs[] = $filteredHub;
                }
            }
        }

        return $hubs;
    }

    private static function filterItems(array $section, $user): array
    {
        // Handle standalone sections (no items, but has route)
        if (!isset($section['items']) && isset($section['route'])) {
            $roleName = optional($user->role)->role_name;
            $tenant = $user->tenant;

            // Check feature flag
            if (isset($section['feature_flag']) && (!$tenant || !$tenant->{$section['feature_flag']})) {
                return [];
            }

            // Admin access
            if ($roleName === 'admin') {
                return $section;
            }

            // RBAC permission check
            if ($roleName !== 'admin') {
                if (isset($section['permission']) && !$user->hasPermission($section['permission'])) {
                    return [];
                }
                if (!isset($section['permission'])) {
                    return [];
                }
            }

            // Condition check
            if (isset($section['condition'])) {
                if ($section['condition'] === 'has_subordinates' && $user->subordinates()->count() <= 0) {
                    return [];
                }
                if ($section['condition'] === 'is_manager' && (!isset($user->is_manager) || $user->is_manager != 1)) {
                    return [];
                }
            }

            return $section;
        }

        if (!isset($section['items'])) {
            return [];
        }

        $items = [];
        $roleName = optional($user->role)->role_name;
        
        // Get tenant information for feature flag checking
        $tenant = $user->tenant;

        foreach ($section['items'] as $item) {
            // Check item-level feature flags first
            if (isset($item['feature_flag']) && (!$tenant || !$tenant->{$item['feature_flag']})) {
                continue;
            }

            // Admin gets access to everything (after feature flag check)
            if ($roleName === 'admin') {
                $items[] = $item;
                continue;
            }

            // RBAC strictly for all non-admins
            if ($roleName !== 'admin') {
                // Special case: Managers get worklog approvals regardless of permissions
                if (isset($item['permission']) && $item['permission'] === 'worklog.approvals') {
                    // Check if user is a manager (is_manager = 1)
                    if (isset($user->is_manager) && $user->is_manager == 1) {
                        $items[] = $item;
                        continue;
                    }
                }
                
                // If item has permission requirement, check it
                if (isset($item['permission']) && !$user->hasPermission($item['permission'])) {
                    continue;
                }
                
                // If no permission specified, skip it (custom roles only get items with permissions)
                if (!isset($item['permission'])) {
                    continue;
                }
            }
            
            // Check conditions (subordinates, manager, etc.)
            if (isset($item['condition'])) {
                if ($item['condition'] === 'has_subordinates' && $user->subordinates()->count() <= 0) {
                    continue;
                }
                if ($item['condition'] === 'is_manager' && (!isset($user->is_manager) || $user->is_manager != 1)) {
                    continue;
                }
            }
            
            $items[] = $item;
        }
        $section['items'] = $items;
        return $section;
    }
}


