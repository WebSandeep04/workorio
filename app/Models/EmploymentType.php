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
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employment_type_id');
    }
}

