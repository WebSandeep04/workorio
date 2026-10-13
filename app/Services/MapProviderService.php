<?php

namespace App\Services;

use App\Models\MapApiUsage;
use App\Models\MapSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapProviderService
{
    /**
     * Get active provider configuration based on 3-tier hierarchy:
     * 1. Tenant BYO-API (Custom API key configured by the tenant)
     * 2. Global Product Pool on Master DB (Google -> Ola with quota limits)
     * 3. OpenStreetMap / Leaflet (Permanent zero-cost fallback)
     *
     * @return array
     */
    public function getActiveProviderConfig(): array
    {
        // -----------------------------------------------------------------
        // 1. TIER 1: Check Tenant BYO-API (Custom API Key in Tenant DB)
        // -----------------------------------------------------------------
        $byoConfig = $this->resolveTenantByoConfig();
        if ($byoConfig !== null) {
            return $byoConfig;
        }

        // -----------------------------------------------------------------
        // 2. TIER 2: Global Product Pool (Master DB: map_api_usages)
        // -----------------------------------------------------------------
        $cycleStart = Carbon::now()->startOfMonth()->toDateString();

        $googleKey = config('services.maps.google.api_key');
        $googleLimit = (int) config('services.maps.google.limit', 10000);

        $olaKey = config('services.maps.ola.api_key');
        $olaLimit = (int) config('services.maps.ola.limit', 50000);

        // Check Product Google Maps
        if (!empty($googleKey) && $googleLimit > 0) {
            $googleUsage = $this->getOrCreateUsage('google', $cycleStart);
            if ($googleUsage->request_count < $googleLimit) {
                return [
                    'provider' => 'google',
                    'api_key' => $googleKey,
                    'is_byo' => false,
                    'usage_id' => $googleUsage->id,
                    'request_count' => $googleUsage->request_count,
                    'limit' => $googleLimit,
                ];
            }
        }

        // Check Product Ola Maps
        if (!empty($olaKey) && $olaLimit > 0) {
            $olaUsage = $this->getOrCreateUsage('ola', $cycleStart);
            if ($olaUsage->request_count < $olaLimit) {
                return [
                    'provider' => 'ola',
                    'api_key' => $olaKey,
                    'is_byo' => false,
                    'usage_id' => $olaUsage->id,
                    'request_count' => $olaUsage->request_count,
                    'limit' => $olaLimit,
                ];
            }
        }

        // -----------------------------------------------------------------
        // 3. TIER 3: Zero-Cost Fallback (OpenStreetMap)
        // -----------------------------------------------------------------
        return [
            'provider' => 'osm',
            'api_key' => '',
            'is_byo' => false,
            'usage_id' => null,
            'request_count' => 0,
            'limit' => null,
        ];
    }

    /**
     * Resolve Tenant's own custom API Key if enabled in their tenant DB.
     * Implements "Same as Global" automatic quota failover:
     * Primary Provider -> Secondary Provider (when primary quota finished) -> OpenStreetMap.
     */
    protected function resolveTenantByoConfig(): ?array
    {
        try {
            $setting = MapSetting::first();
            if (!$setting || !$setting->is_byo_enabled) {
                return null;
            }

            // Ensure current month billing cycle is reset if month changed
            $setting->resetCycleIfExpired();

            $preferred = $setting->preferred_provider ?? 'google';

            // Define order based on tenant preference (Google first or Ola first)
            $order = ($preferred === 'ola') ? ['ola', 'google'] : ['google', 'ola'];

            foreach ($order as $provider) {
                if ($provider === 'google') {
                    $key = $setting->google_api_key;
                    $limit = (int) ($setting->google_limit ?? 10000);
                    $usage = (int) ($setting->google_usage ?? 0);

                    // If key is provided and limit is not exhausted (or limit 0 = unlimited)
                    if (!empty($key) && ($limit === 0 || $usage < $limit)) {
                        return [
                            'provider' => 'google',
                            'api_key' => $key,
                            'is_byo' => true,
                            'usage_id' => null, // 0 Master Quota deducted
                            'request_count' => $usage,
                            'limit' => $limit,
                            'is_failover' => ($provider !== $order[0]),
                        ];
                    }
                } elseif ($provider === 'ola') {
                    $key = $setting->ola_api_key;
                    $limit = (int) ($setting->ola_limit ?? 50000);
                    $usage = (int) ($setting->ola_usage ?? 0);

                    // If key is provided and limit is not exhausted (or limit 0 = unlimited)
                    if (!empty($key) && ($limit === 0 || $usage < $limit)) {
                        return [
                            'provider' => 'ola',
                            'api_key' => $key,
                            'is_byo' => true,
                            'usage_id' => null, // 0 Master Quota deducted
                            'request_count' => $usage,
                            'limit' => $limit,
                            'is_failover' => ($provider !== $order[0]),
                        ];
                    }
                }
            }

            // If BYO is enabled but both tenant keys have exhausted their quota:
            // Seamlessly fall back to OpenStreetMap (0-cost, uninterrupted tracking)
            return [
                'provider' => 'osm',
                'api_key' => '',
                'is_byo' => true,
                'usage_id' => null,
                'request_count' => 0,
                'limit' => null,
                'quota_exhausted' => true,
            ];
        } catch (\Throwable $e) {
            // Gracefully ignore if tenant table does not exist yet
            Log::debug('Tenant map_settings resolution: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get or create usage record for provider and billing cycle in Master DB.
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
     * Increment usage counter for a given usage ID or provider.
     * When isByo is true: increments the tenant DB map_settings counter (0 Master DB quota consumed).
     * When isByo is false: increments Master DB map_api_usages counter.
     */
    public function recordUsage(?int $usageId = null, ?string $provider = null, bool $isByo = false): bool
    {
        // Custom BYO API keys consume Tenant quota, not Master product quota
        if ($isByo) {
            if (!$provider) {
                return true;
            }
            try {
                $setting = MapSetting::first();
                if ($setting && $setting->is_byo_enabled) {
                    $setting->resetCycleIfExpired();
                    if ($provider === 'google') {
                        $setting->increment('google_usage');
                    } elseif ($provider === 'ola') {
                        $setting->increment('ola_usage');
                    }
                }
                return true;
            } catch (\Throwable $e) {
                Log::error('Failed to increment Tenant DB map usage: ' . $e->getMessage());
                return false;
            }
        }

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
            Log::error('Failed to increment Master DB map usage: ' . $e->getMessage());
            return false;
        }
    }
}
