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
        'is_grace_punish',
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
        'is_grace_punish' => 'boolean',
        'sl_end_limit' => 'integer',
        'week_offs' => 'array',
        'half_days' => 'array',
        'full_day_hr' => 'decimal:2',
        'half_day_hr' => 'decimal:2',
        'extended_hr' => 'decimal:2',
    ];

    public function getStartTimeAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value, 'UTC')
                ->setTimezone('Asia/Kolkata')
                ->format('H:i:s');
        }
        return $value;
    }

    public function getEndTimeAttribute($value)
    {
        if ($value) {
            return \Carbon\Carbon::parse($value, 'UTC')
                ->setTimezone('Asia/Kolkata')
                ->format('H:i:s');
        }
        return $value;
    }

    public function setStartTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['start_time'] = \Carbon\Carbon::parse($value, 'Asia/Kolkata')
                ->setTimezone('UTC')
                ->format('H:i:s');
        } else {
            $this->attributes['start_time'] = null;
        }
    }

    public function setEndTimeAttribute($value)
    {
        if ($value) {
            $this->attributes['end_time'] = \Carbon\Carbon::parse($value, 'Asia/Kolkata')
                ->setTimezone('UTC')
                ->format('H:i:s');
        } else {
            $this->attributes['end_time'] = null;
        }
    }
}
