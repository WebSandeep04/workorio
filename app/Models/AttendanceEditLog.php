<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'edited_by',
        'old_in_time',
        'new_in_time',
        'old_out_time',
        'new_out_time',
        'reason'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
