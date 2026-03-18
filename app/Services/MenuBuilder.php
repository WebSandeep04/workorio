<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuBuilder
{
    public static function build(): array
    {
        // Check if user is authenticated through Laravel Auth (master database users)
        $user = Auth::user();
        
        // If not authenticated through Auth, check session for tenant users
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
                $dbUser = \App\Models\User::find($userId);
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
                \Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
                $tenant = \App\Models\Tenant::find($user->tenant_id);
                // Switch back to tenant database
                \App\Services\TenantDatabaseService::setDefaultConnection($user->tenant_id);
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
                $role = \App\Models\Role::find($user->role_id);
                $user->role = $role;
            } catch (\Exception $e) {
                // If role not found, continue with null role
            }
        }
        
        // Load user's manager status for session users
        if ($user->is_manager === null && $user->id) {
            try {
                $hasSubordinates = \Illuminate\Support\Facades\DB::table('user_managers')->where('manager_id', $user->id)->exists();
                $user->is_manager = $hasSubordinates ? 1 : 0;
            } catch (\Exception $e) {
                // If table not found or error, set default
                $user->is_manager = 0;
            }
        }
        
        $roleName = optional($user->role)->role_name;
        $roleId = $user->role_id;

        // Simplified role system for tenants
        $isAdmin = ($roleName === 'admin');
        $isCustomRole = ($roleName !== 'admin' && $roleName !== 'user');
        
        $menuConfig = config('menu');
        $sections = [];
        
        // Admin gets full access based on feature flags
        if ($isAdmin) {
            foreach ($menuConfig['admin_sections'] as $section) {
                if (isset($section['feature_flag']) && (!$tenant || !$tenant->{$section['feature_flag']})) {
                    continue;
                }
                $filteredSection = static::filterItems($section, $user);
                
                // Sort items for Software Setup section if requested
                if ($section['key'] === 'software_setup' && !empty($filteredSection['items'])) {
                    usort($filteredSection['items'], function($a, $b) {
                        return strcasecmp($a['title'], $b['title']);
                    });
                }
                
                // Only add section if it has items after filtering or it's a standalone section
                if (!empty($filteredSection['items']) || (!empty($filteredSection) && isset($filteredSection['route']))) {
                    $sections[] = $filteredSection;
                }
            }
        }

        // For custom roles ONLY, use permission-based logic
        if ($isCustomRole) {
            // Custom roles get access based on their permissions
            foreach ($menuConfig['admin_sections'] as $section) {
                if (isset($section['feature_flag']) && (!$tenant || !$tenant->{$section['feature_flag']})) {
                    continue;
                }
                
                // For custom roles, ignore the 'roles' restriction and check permissions instead
                // Filter section items based on user permissions
                $filteredSection = static::filterItems($section, $user);
                
                // Sort items for Software Setup section if requested
                if ($section['key'] === 'software_setup' && !empty($filteredSection['items'])) {
                    usort($filteredSection['items'], function($a, $b) {
                        return strcasecmp($a['title'], $b['title']);
                    });
                }
                
                // Only add section if user has permissions for any items or it's a standalone section
                if (!empty($filteredSection['items']) || (!empty($filteredSection) && isset($filteredSection['route']))) {
                    $sections[] = $filteredSection;
                }
            }
        }

        return $sections;
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


