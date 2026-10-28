<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Services\AiKeyResolverService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class AiSettingController extends Controller
{
    /**
     * Show the AI API Settings view (Software Setup)
     */
    public function index()
    {
        $setting = null;
        try {
            $setting = AiSetting::first();
        } catch (\Throwable $e) {}

        if (!$setting) {
            $setting = new AiSetting([
                'is_gemini_byo_enabled' => false,
                'gemini_api_key' => '',
                'is_openai_byo_enabled' => false,
                'openai_api_key' => '',
            ]);
        }

        $globalGeminiConfigured = !empty(env('GEMINI_API_KEY'));
        $globalOpenAiConfigured = !empty(env('OPENAI_API_KEY'));

        return view('software-setup.ai-settings.index', compact('setting', 'globalGeminiConfigured', 'globalOpenAiConfigured'));
    }

    /**
     * Save AI API Settings for the current tenant
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'is_gemini_byo_enabled' => 'required|boolean',
            'gemini_api_key' => 'nullable|string|max:500',
            'is_openai_byo_enabled' => 'required|boolean',
            'openai_api_key' => 'nullable|string|max:500',
        ]);

        try {
            $setting = AiSetting::first();
            if (!$setting) {
                $setting = new AiSetting();
            }

            $setting->fill($validated);
            $setting->save();

            return redirect()->route('software-setup.ai-settings.index')
                ->with('success', 'AI API Settings saved successfully!');
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save AI API Settings: ' . $e->getMessage());
        }
    }

    /**
     * Test Gemini API connection using either the provided key or current active key
     */
    public function testGemini(Request $request): JsonResponse
    {
        $apiKey = $request->input('key') ?: AiKeyResolverService::getGeminiKey();

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'No Gemini API key provided or configured.'
            ], 422);
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key=" . urlencode($apiKey);

            $response = Http::timeout(10)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Ping. Respond with "Pong".']
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gemini API key is valid and connected successfully!'
                ]);
            }

            $errBody = $response->json();
            $errMsg = $errBody['error']['message'] ?? $response->body();

            return response()->json([
                'success' => false,
                'message' => 'Gemini API error: ' . $errMsg
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection to Gemini failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test OpenAI API connection using either the provided key or current active key
     */
    public function testOpenAi(Request $request): JsonResponse
    {
        $apiKey = $request->input('key') ?: AiKeyResolverService::getOpenAiKey();

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'No OpenAI API key provided or configured.'
            ], 422);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OpenAI API key is valid and connected successfully!'
                ]);
            }

            $errBody = $response->json();
            $errMsg = $errBody['error']['message'] ?? $response->body();

            return response()->json([
                'success' => false,
                'message' => 'OpenAI API error: ' . $errMsg
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection to OpenAI failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

