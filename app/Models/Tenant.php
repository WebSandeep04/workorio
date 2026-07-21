<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $connection = 'mysql';

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
        'is_contact_management',
        'is_asset_management_enable',
        'is_email_marketing_enable',
        'is_tally_calling_enabled',
        'is_leadgen_enabled',
        'is_projects_enabled',
        'is_tracking_enabled',
        'is_workflow_enabled',
        'is_social_media_calendar_enabled',
        'is_master_enabled',
        'is_task_reminders_enabled',
        'is_reports_enabled',
        'is_core_setup_enabled',
        'is_tally_calling_setup_enabled',
        'is_leadgen_setup_enabled',
        'is_projects_setup_enabled',
        'is_subscription_setup_enabled',
        'is_tracking_setup_enabled',
        'is_workflow_setup_enabled',
        'is_calendar_setup_enabled',
        'is_master_setup_enabled',
        'is_task_setup_enabled',
        'is_attendance_setup_enabled',
        'is_reports_setup_enabled',
        'is_document_setup_enabled',
        'is_petty_cash_setup_enabled',
        'is_contact_management_setup_enabled',
        'is_asset_management_setup_enabled',
        'is_email_marketing_setup_enabled',
        'is_payroll_enabled',
        'is_payroll_setup_enabled',
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
