<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantDatabaseService;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class SetTenantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Header (API / Explicit)
        if ($request->hasHeader('X-Tenant-ID')) {
             $tenantId = $request->header('X-Tenant-ID');

             $this->switchTenant($tenantId);
             return $next($request);
        }

        // Check if user is authenticated via Auth facade (master database users - super admin only)
        if (Auth::check()) {
            return $next($request);
        }
        // Check if user is authenticated via session (tenant database users)
        elseif (session()->has('user_id')) {
            $tenantId = session('tenant_id', 1); // Default to tenant 1 if not set
            $this->switchTenant($tenantId);
        }
        
        return $next($request);
    }

    private function switchTenant($tenantId)
    {
        // Get tenant from master database
        $tenant = Tenant::find($tenantId);
        
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
