<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapApiUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'request_count',
        'billing_cycle_start',
    ];

    protected $casts = [
        'request_count' => 'integer',
        'billing_cycle_start' => 'date',
    ];
}
