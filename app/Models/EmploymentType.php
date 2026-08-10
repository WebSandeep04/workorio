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
        'max_loan_percentage',
        'max_advance_percentage',
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

