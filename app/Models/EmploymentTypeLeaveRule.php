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
        'max_use_per_month',
        'carry_forward_allowed',
        'max_carry_forward',
        'lapse_type',
        'eligibility_days',
        'halfday_count_value'
    ];

    protected $casts = [
        'carry_forward_allowed' => 'boolean',
        'halfday_count_value' => 'decimal:2',
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
