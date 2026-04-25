<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_paid',
        'is_deductible',
        'is_short_leave',
        'is_restricted',
        'full_day_weight',
        'half_day_weight',
        'allow_half_day',
        'quota_type',
        'color_code',
        'status',
        'description'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_deductible' => 'boolean',
        'is_short_leave' => 'boolean',
        'is_restricted' => 'boolean',
        'allow_half_day' => 'boolean',
        'status' => 'boolean',
        'full_day_weight' => 'decimal:2',
        'half_day_weight' => 'decimal:2',
    ];

    public function rules()
    {
        return $this->hasMany(EmploymentTypeLeaveRule::class);
    }
}
