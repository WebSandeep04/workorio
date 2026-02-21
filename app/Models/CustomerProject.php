<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'project_name',
        'start_date',
        'end_date',
        'status',
        'description',
        'original_value',
        'estimated_value',
        'profit_value',
        'critical_path_enabled',
        'workflow_template_id',
        'completed_percentage',
        'is_favourite',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'critical_path_enabled' => 'boolean',
        'is_favourite' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }

    public function customerProjectModules()
    {
        return $this->hasMany(CustomerProjectModule::class);
    }

    public function workflowTemplate()
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'customer_project_users')
            ->withPivot(['days_allocated'])
            ->withTimestamps();
    }

    public function remarks()
    {
        return $this->hasMany(CustomerProjectRemark::class, 'customer_project_id');
    }

    public function latestRemark()
    {
        return $this->hasOne(CustomerProjectRemark::class, 'customer_project_id')->latestOfMany();
    }
}
