<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Country extends Model
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
        return $this->hasMany(Employee::class, 'country_id');
    }
}

