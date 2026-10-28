<?php

namespace App\Services;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Log;

class AiKeyResolverService
{
    /**
     * Resolve Gemini API Key:
     * 1. Check if tenant has BYO enabled and provided a key.
     * 2. Otherwise fallback to system env('GEMINI_API_KEY').
     */
    public static function getGeminiKey(): ?string
    {
        try {
            $setting = AiSetting::first();
            if ($setting && $setting->is_gemini_byo_enabled && !empty($setting->gemini_api_key)) {
                return trim($setting->gemini_api_key);
            }
        } catch (\Throwable $e) {
            Log::warning('AiKeyResolverService: Error reading tenant Gemini setting, falling back to ENV', [
                'error' => $e->getMessage()
            ]);
        }

        return env('GEMINI_API_KEY');
    }

    /**
     * Resolve OpenAI API Key:
     * 1. Check if tenant has BYO enabled and provided a key.
     * 2. Otherwise fallback to system env('OPENAI_API_KEY').
     */
    public static function getOpenAiKey(): ?string
    {
        try {
            $setting = AiSetting::first();
            if ($setting && $setting->is_openai_byo_enabled && !empty($setting->openai_api_key)) {
                return trim($setting->openai_api_key);
            }
        } catch (\Throwable $e) {
            Log::warning('AiKeyResolverService: Error reading tenant OpenAI setting, falling back to ENV', [
                'error' => $e->getMessage()
            ]);
        }

        return env('OPENAI_API_KEY');
    }

    /**
     * Check whether tenant is currently using their own BYO Gemini key
     */
    public static function isGeminiByoActive(): bool
    {
        try {
            $setting = AiSetting::first();
            return (bool) ($setting && $setting->is_gemini_byo_enabled && !empty($setting->gemini_api_key));
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Check whether tenant is currently using their own BYO OpenAI key
     */
    public static function isOpenAiByoActive(): bool
    {
        try {
            $setting = AiSetting::first();
            return (bool) ($setting && $setting->is_openai_byo_enabled && !empty($setting->openai_api_key));
        } catch (\Throwable $e) {
            return false;
        }
    }
}

