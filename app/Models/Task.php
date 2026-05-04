<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerProject;
use App\Models\WorkflowTask;
use App\Models\TaskReassignment;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'task_name',
        'task',
        'task_type',
        'is_recurring',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_days_of_week',
        'recurrence_day_of_month',
        'recurrence_months',
        'recurrence_end_date',
        'due_date',
        'is_done',
        'created_by',
        'task_status_id',
        'task_priority_id',
        'customer_project_id',
        'workflow_task_id',
        'started_at',
        'completed_at',
        'estimated_efforts',
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_days_of_week' => 'array',
        'recurrence_months' => 'array',
        'recurrence_end_date' => 'date',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship: Task belongs to a customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship: Task assigned to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Task created by a user
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Task belongs to a task status
     */
    public function status()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    /**
     * Relationship: Task belongs to a task priority
     */
    public function priority()
    {
        return $this->belongsTo(TaskPriority::class, 'task_priority_id');
    }

    public function customerProject()
    {
        return $this->belongsTo(CustomerProject::class);
    }

    public function workflowTask()
    {
        return $this->belongsTo(WorkflowTask::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignees')
            ->withTimestamps()
            ->withPivot('assigned_by');
    }

    /**
     * Relationship: Task has many remarks
     */
    public function remarks()
    {
        return $this->hasMany(TaskRemark::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relationship: Task has many images
     */
    public function images()
    {
        return $this->hasMany(TaskImage::class)->orderBy('created_at', 'desc');
    }

    public function reassignments()
    {
        return $this->hasMany(TaskReassignment::class)->orderBy('created_at', 'desc');
    }
}
