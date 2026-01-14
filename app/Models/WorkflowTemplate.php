<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function tasks()
    {
        return $this->hasMany(WorkflowTask::class)->orderBy('position');
    }

    public function taskDependencies()
    {
        return $this->hasMany(WorkflowTaskDependency::class);
    }
}


