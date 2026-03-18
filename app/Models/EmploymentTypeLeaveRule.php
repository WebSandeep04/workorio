<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentTypeLeaveRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employment_type_id',
        'leave_type_id',
        'generation_type',
        'value',
        'carry_forward_allowed',
        'max_carry_forward'
    ];

    protected $casts = [
        'carry_forward_allowed' => 'boolean',
    ];

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
