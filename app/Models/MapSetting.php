<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_byo_enabled',
        'preferred_provider',
        'google_api_key',
        'google_limit',
        'google_usage',
        'ola_api_key',
        'ola_limit',
        'ola_usage',
        'billing_cycle_start',
    ];

    protected $casts = [
        'is_byo_enabled' => 'boolean',
        'google_limit' => 'integer',
        'google_usage' => 'integer',
        'ola_limit' => 'integer',
        'ola_usage' => 'integer',
        'billing_cycle_start' => 'date',
    ];

    /**
     * Automatically reset monthly usage counters on the 1st of each month
     */
    public function resetCycleIfExpired(): void
    {
        $currentMonth = \Carbon\Carbon::now()->startOfMonth()->toDateString();
        $storedMonth = $this->billing_cycle_start ? $this->billing_cycle_start->format('Y-m-d') : null;

        if ($storedMonth !== $currentMonth) {
            $this->google_usage = 0;
            $this->ola_usage = 0;
            $this->billing_cycle_start = $currentMonth;
            $this->save();
        }
    }
}
