<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\TenantDatabaseService;

abstract class TenantModel extends Model
{
    /**
     * The connection name for the model.
     * Will be set dynamically based on the current user's tenant.
     */
    protected $connection = null;

    /**
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set the connection based on the current user's tenant
        $this->setTenantConnection();
    }

    /**
     * Set the tenant-specific database connection
     */
    protected function setTenantConnection(): void
    {
        $tenantId = null;
        
        // Check for Laravel Auth user (super admin)
        if (Auth::check()) {
            $user = Auth::user();
            $tenantId = $user->tenant_id ?? null;
        }
        // Check for session-based tenant user
        elseif (session()->has('tenant_id')) {
            $tenantId = session('tenant_id');
        }
        
        if ($tenantId) {
            // Get tenant from master database
            $tenant = \App\Models\Tenant::find($tenantId);
            
            if ($tenant) {
                // Create connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                
                // Set the connection name
                $this->setConnection(TenantDatabaseService::getConnectionName($tenant->id));
            }
        }
    }

    /**
     * Override the newQuery method to ensure tenant connection
     */
    public function newQuery()
    {
        $this->setTenantConnection();
        return parent::newQuery();
    }

    /**
     * Override the newQueryWithoutScopes method
     */
    public function newQueryWithoutScopes()
    {
        $this->setTenantConnection();
        return parent::newQueryWithoutScopes();
    }

    /**
     * Get the database connection for the model.
     */
    public function getConnection()
    {
        $this->setTenantConnection();
        return parent::getConnection();
    }

    /**
     * Get the table associated with the model.
     */
    public function getTable()
    {
        return $this->table ?? str_replace('\\', '', Str::snake(Str::plural(class_basename($this))));
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure connection is set when model is used
        static::creating(function ($model) {
            $model->setTenantConnection();
        });
        
        static::updating(function ($model) {
            $model->setTenantConnection();
        });
        
        static::deleting(function ($model) {
            $model->setTenantConnection();
        });
    }
}
