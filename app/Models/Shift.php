<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'late_min',
        'is_active',
        'sl_start_limit',
        'sl_end_limit',
        'week_offs',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'late_min' => 'integer',
        'sl_start_limit' => 'integer',
        'sl_end_limit' => 'integer',
        'week_offs' => 'array',
    ];
}
