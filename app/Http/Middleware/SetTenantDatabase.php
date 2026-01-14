<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TenantDatabaseService;
use Symfony\Component\HttpFoundation\Response;

class SetTenantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Auth facade (master database users - super admin only)
        if (Auth::check()) {
            $user = Auth::user();
            
            // Super admin users (master database) should stay in master database
            // Only super admin users can be authenticated via Auth facade
            if ($user->role_id == 3) {
                // This is a super admin user, stay in master database
                return $next($request);
            }
            
            // For other users authenticated via Auth, determine tenant
            $tenantId = $this->getTenantIdFromUser($user);
            
            if ($tenantId) {
                // Get tenant from master database
                $tenant = \App\Models\Tenant::find($tenantId);
                
                if ($tenant) {
                    // Create connection if it doesn't exist
                    if (!TenantDatabaseService::connectionExists($tenant->id)) {
                        TenantDatabaseService::createConnection($tenant);
                    }
                    
                    // Set default connection for this request
                    TenantDatabaseService::setDefaultConnection($tenant->id);
                }
            }
        }
        // Check if user is authenticated via session (tenant database users)
        elseif (session()->has('user_id')) {
            $tenantId = session('tenant_id', 1); // Default to tenant 1 if not set
            
            // Get tenant from master database
            $tenant = \App\Models\Tenant::find($tenantId);
            
            if ($tenant) {
                // Create connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                
                // Set default connection for this request
                TenantDatabaseService::setDefaultConnection($tenant->id);
            }
        }
        
        return $next($request);
    }
    
    /**
     * Get tenant ID from user - this needs to be implemented based on your business logic
     * For now, we'll use a default tenant ID
     */
    private function getTenantIdFromUser($user)
    {
        // Option 1: Store tenant_id in user session during login
        if (session()->has('tenant_id')) {
            return session('tenant_id');
        }
        
        // Option 2: Use a default tenant for other users
        return 1; // Default tenant ID
        
        // Option 3: Determine from user email domain or other logic
        // $emailDomain = explode('@', $user->email)[1];
        // return $this->getTenantIdByDomain($emailDomain);
    }
}
