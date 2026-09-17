<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSchedulerConfig extends Model
{
    protected $fillable = [
        'office_start_time',
        'office_end_time',
        'office_day_frequency',
        'office_day_lookback',
        'off_day_frequency',
        'off_day_lookback',
        'week_offs',
    ];

    protected $casts = [
        'week_offs' => 'array',
        'office_day_frequency' => 'integer',
        'office_day_lookback' => 'integer',
        'off_day_frequency' => 'integer',
        'off_day_lookback' => 'integer',
    ];
}
