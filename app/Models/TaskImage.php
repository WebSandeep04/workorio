<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'image_path',
        'original_name',
        'file_size'
    ];

    protected $appends = ['url'];

    /**
     * Relationship: Image belongs to a task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get full URL for the image
     */
    public function getUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }
        // Use Storage facade to get proper URL
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path);
    }
}
