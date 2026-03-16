<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Relations\NullRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_worklog',
        'is_manager',
        'salary_per_month',
        'employee_id',
        'is_sales',
        'is_task',
        'is_indiaMart',
        'is_calander',
        'is_login',
        'is_tally_calling',
        'is_projects',
        'is_subscription_and_renewal',
        'is_tracking',
        'is_workflow',
        'is_master',
        'is_attandance',
        'is_reports',
        'is_document',
        'is_petty_cash',
        'is_contact_management',
        'is_asset_management',
        'is_email_marketing',
        'is_software_setup',
        'is_core_setup',
        'is_sales_setup',
        'is_work_setup',
        'is_user_setup',
        'is_tally_calling_setup',
        'is_projects_setup',
        'is_subscription_setup',
        'is_tracking_setup',
        'is_workflow_setup',
        'is_calendar_setup',
        'is_master_setup',
        'is_task_setup',
        'is_attendance_setup',
        'is_reports_setup',
        'is_document_setup',
        'is_petty_cash_setup',
        'is_contact_management_setup',
        'is_asset_management_setup',
        'is_email_marketing_setup',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        if ($this->isMasterConnection()) {
            return new NullRelation($this);
        }

        return $this->belongsTo(Role::class);
    }

// Removed tenant() relationship - no longer needed with separate databases

public function salesRecords()
{
    return $this->hasMany(SalesRecord::class);
}

public function worklogs()
{
    return $this->hasMany(Worklog::class);
}

public function managers()
{
    return $this->belongsToMany(User::class, 'user_managers', 'user_id', 'manager_id')->withTimestamps();
}

public function subordinates()
{
    return $this->belongsToMany(User::class, 'user_managers', 'manager_id', 'user_id')->withTimestamps();
}

    public function attendances()
{
    return $this->hasMany(Attendance::class);
}

    public function leaves()
{
    return $this->hasMany(Leave::class);
}

    /**
     * Return permissions array for the user's role.
     */
    public function rolePermissions(): array
    {
        if ($this->isMasterConnection()) {
            return [];
        }

        $role = $this->role;
        if (!$role || empty($role->permissions_data)) {
            return [];
        }
        // permissions_data may already be cast to array via Role model cast
        if (is_array($role->permissions_data)) {
            return $role->permissions_data;
        }
        // If stored as JSON string for older records, decode safely
        $decoded = json_decode($role->permissions_data, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Check if the user has a specific permission key.
     */
    public function hasPermission(string $permissionKey): bool
    {
        if ($this->isMasterConnection()) {
            return false;
        }

        return in_array($permissionKey, $this->rolePermissions(), true);
    }

    /**
     * Check if user has any permission with given prefix, e.g., "sales." or "worklog.".
     */
    public function hasPermissionPrefix(string $prefix): bool
    {
        if ($this->isMasterConnection()) {
            return false;
        }

        $permissions = $this->rolePermissions();
        foreach ($permissions as $permission) {
            if (str_starts_with($permission, $prefix)) {
                return true;
            }
        }
        return false;
    }

    // Removed tenant-specific connection logic - now handled by TenantModel base class

    protected function isMasterConnection(): bool
    {
        try {
            return DB::getDefaultConnection() === 'mysql';
        } catch (\Throwable $e) {
            return config('database.default') === 'mysql';
        }
    }
}
