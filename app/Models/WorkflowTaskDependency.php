<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTaskDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'predecessor_task_id',
        'successor_task_id',
        'dependency_type_id',
        'lag_days',
        'notes',
    ];

    public function template()
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function predecessor()
    {
        return $this->belongsTo(WorkflowTask::class, 'predecessor_task_id');
    }

    public function successor()
    {
        return $this->belongsTo(WorkflowTask::class, 'successor_task_id');
    }

    public function type()
    {
        return $this->belongsTo(WorkflowDependencyType::class, 'dependency_type_id');
    }
}


