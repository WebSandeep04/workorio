<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'tenant_name',
        'tenant_code',
        'is_setup_enabled',
        'is_sales_enabled',
        'is_worklog_enabled',
        'is_attendance_enabled',
        'is_subscription_enabled',
        'is_sales_setup_enabled',
        'is_work_setup_enabled',
        'is_user_setup_enabled',
        'is_document_management_enabled',
        'is_petty_cash_enable',
        'is_approval_enabled',
    ];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (empty($tenant->tenant_code)) {
                $tenant->tenant_code = 'TEN-' . strtoupper(Str::random(6));
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function salesRecords()
    {
        return $this->hasMany(SalesRecord::class);
    }
}
