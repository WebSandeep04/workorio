<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskRemark extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'remark'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['task'];

    /**
     * Relationship: Remark belongs to a task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relationship: Remark belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
