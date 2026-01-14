<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'order'
    ];

    /**
     * Relationship: TaskStatus has many tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
