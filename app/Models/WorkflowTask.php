<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_template_id',
        'name',
        'owner_name',
        'owner_id',
        'position',
        'duration_days',
    ];

    public function template()
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function predecessorDependencies()
    {
        return $this->hasMany(WorkflowTaskDependency::class, 'predecessor_task_id');
    }

    public function successorDependencies()
    {
        return $this->hasMany(WorkflowTaskDependency::class, 'successor_task_id');
    }
}


