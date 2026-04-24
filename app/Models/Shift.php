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
        'min_per_month_late_allow',
        'is_active',
        'sl_end_limit',
        'week_offs',
        'half_days',
        'full_day_hr',
        'half_day_hr',
        'extended_hr',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'late_min' => 'integer',
        'min_per_month_late_allow' => 'integer',
        'sl_end_limit' => 'integer',
        'week_offs' => 'array',
        'half_days' => 'array',
        'full_day_hr' => 'decimal:2',
        'half_day_hr' => 'decimal:2',
        'extended_hr' => 'decimal:2',
    ];
}
