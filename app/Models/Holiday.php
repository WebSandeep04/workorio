<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    use HasFactory;

    protected $fillable = [
        'name',
        'holiday_date',
        'is_rh',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];
}
