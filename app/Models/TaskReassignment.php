<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReassignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'previous_user_id',
        'new_user_id',
        'reassigned_by',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function previousUser()
    {
        return $this->belongsTo(User::class, 'previous_user_id');
    }

    public function newUser()
    {
        return $this->belongsTo(User::class, 'new_user_id');
    }

    public function reassignedByUser()
    {
        return $this->belongsTo(User::class, 'reassigned_by');
    }
}


