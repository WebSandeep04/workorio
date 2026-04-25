<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'is_rh',
        'is_sl',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'sl_period',
        'total_days',
        'reason',
        'status', // pending, approved, rejected, cancelled
        'approved_by',
        'reject_reason',
        'is_half_day',
        'half_day_period',
        'has_attendance_overlap',
        'resumed_at',
        'is_early_return'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'resumed_at' => 'date',
        'total_days' => 'decimal:2',
        'has_attendance_overlap' => 'boolean',
        'is_early_return' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
