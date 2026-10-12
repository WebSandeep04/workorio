<?php

namespace App\Services;

use App\Models\MapApiUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapProviderService
{
    /**
     * Get active provider configuration based on monthly quota.
     *
     * Order of precedence:
     * 1. Google Maps (if API key present and usage < limit)
     * 2. Ola Maps (if API key present and usage < limit)
     * 3. OpenStreetMap / Leaflet (Fallback, no API key required)
     *
     * @return array
     */
    public function getActiveProviderConfig(): array
    {
        $cycleStart = Carbon::now()->startOfMonth()->toDateString();

        $googleKey = config('services.maps.google.api_key');
        $googleLimit = (int) config('services.maps.google.limit', 10000);

        $olaKey = config('services.maps.ola.api_key');
        $olaLimit = (int) config('services.maps.ola.limit', 50000);

        // 1. Check Google Maps
        if (!empty($googleKey) && $googleLimit > 0) {
            $googleUsage = $this->getOrCreateUsage('google', $cycleStart);
            if ($googleUsage->request_count < $googleLimit) {
                return [
                    'provider' => 'google',
                    'api_key' => $googleKey,
                    'usage_id' => $googleUsage->id,
                    'request_count' => $googleUsage->request_count,
                    'limit' => $googleLimit,
                ];
            }
        }

        // 2. Check Ola Maps
        if (!empty($olaKey) && $olaLimit > 0) {
            $olaUsage = $this->getOrCreateUsage('ola', $cycleStart);
            if ($olaUsage->request_count < $olaLimit) {
                return [
                    'provider' => 'ola',
                    'api_key' => $olaKey,
                    'usage_id' => $olaUsage->id,
                    'request_count' => $olaUsage->request_count,
                    'limit' => $olaLimit,
                ];
            }
        }

        // 3. Fallback to OpenStreetMap (Leaflet)
        $osmUsage = $this->getOrCreateUsage('osm', $cycleStart);
        return [
            'provider' => 'osm',
            'api_key' => '',
            'usage_id' => $osmUsage->id,
            'request_count' => $osmUsage->request_count,
            'limit' => null,
        ];
    }

    /**
     * Get or create usage record for provider and billing cycle.
     */
    public function getOrCreateUsage(string $provider, string $cycleStart): MapApiUsage
    {
        return MapApiUsage::firstOrCreate(
            [
                'provider' => $provider,
                'billing_cycle_start' => $cycleStart,
            ],
            [
                'request_count' => 0,
            ]
        );
    }

    /**
     * Increment usage counter for a given usage ID or provider in the current cycle.
     */
    public function recordUsage(?int $usageId = null, ?string $provider = null): bool
    {
        try {
            if ($usageId) {
                MapApiUsage::where('id', $usageId)->increment('request_count');
                return true;
            }

            if ($provider) {
                $cycleStart = Carbon::now()->startOfMonth()->toDateString();
                $usage = $this->getOrCreateUsage($provider, $cycleStart);
                $usage->increment('request_count');
                return true;
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Failed to increment map usage: ' . $e->getMessage());
            return false;
        }
    }
}
