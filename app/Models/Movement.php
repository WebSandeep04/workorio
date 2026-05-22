<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    // Removed tenant() relationship - no longer needed with separate databases
    protected $fillable = [
        'attendance_id',
        'movement_type',
        'movement_action',
        'time',
        'description',
        'longitude',
        'latitude',
        'mode',
        'place',
        'device_name',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
