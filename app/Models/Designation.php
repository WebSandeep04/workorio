<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'status',
        'notes',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}

