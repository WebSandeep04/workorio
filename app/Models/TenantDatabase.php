<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TenantDatabase extends Model
{
    // Always use the master connection for this model
    protected $connection = 'mysql';
    protected $fillable = [
        'tenant_id',
        'database_name',
        'connection_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'is_active',
        'last_accessed_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the database
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Update last accessed timestamp
     */
    public function updateLastAccessed()
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Check if database is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Mark database as active
     */
    public function markAsActive()
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Mark database as inactive
     */
    public function markAsInactive()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Get database size in MB
     */
    public function getDatabaseSize()
    {
        try {
            $result = \DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$this->database_name]);
            
            return $result[0]->size_mb ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get database status
     */
    public function getStatus()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->last_accessed_at) {
            $daysSinceLastAccess = $this->last_accessed_at->diffInDays(now());
            if ($daysSinceLastAccess > 30) {
                return 'Dormant';
            }
        }

        return 'Active';
    }
}