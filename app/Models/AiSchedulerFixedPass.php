<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSchedulerFixedPass extends Model
{
    protected $fillable = [
        'name',
        'run_at',
        'lookback_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lookback_minutes' => 'integer',
    ];
}
