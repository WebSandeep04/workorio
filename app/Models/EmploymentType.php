<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'status',
        'notes',
        'rh_allowed',
        'sl_allowed',
        'no_of_half_days',
        'min_per_month_late_allow',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employment_type_id');
    }

    public function leaveRules()
    {
        return $this->hasMany(EmploymentTypeLeaveRule::class, 'employment_type_id');
    }
}

