<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'placename',
        'latitude',
        'longitude',
        'radius',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_attendance_places');
    }
}
