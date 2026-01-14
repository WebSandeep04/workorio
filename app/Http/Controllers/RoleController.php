<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
  
    public function fetchrole()
    {
        try {
            // Check if roles table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('roles')) {
                return response()->json([]);
            }

            // Get user from Auth or session
            $user = Auth::user();
            if (!$user) {
                // For session-based tenant users, create a mock user object
                $user = (object) [
                    'role_id' => session('user_role', 1),
                    'role' => null
                ];
            }

            $roleName = optional($user->role)->role_name;
            $roleId = $user->role_id;
            $isAdmin = ($roleId == 1 || $roleName === 'admin');
            $isSubAdmin = ($roleId == 5 || $roleName === 'sub admin');

            $query = Role::query()
                // Hide super admin from UI (only filter by role_name, not ID)
                ->where('role_name', '!=', 'super_admin')
                ->orderBy('role_name');

            if ($isAdmin || $isSubAdmin) {
                // Tenant admins/sub-admins see built-in roles + their own tenant's custom roles
                $query->where(function ($q) {
                    $q->where('is_custom', false)
                      ->orWhere('is_custom', true);
                });
            } else {
                // Other roles only see built-in roles (no tenant-specific custom roles)
                $query->where('is_custom', false);
            }

            return response()->json($query->get());
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

}
