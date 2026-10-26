<?php

namespace App\Http\Controllers;

use App\Models\Msg91Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91SettingController extends Controller
{
    /**
     * Show the MSG91 WhatsApp settings page.
     */
    public function index()
    {
        $setting = Msg91Setting::first();
        
        // Get current tenant
        $tenantId = session('tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : 1);
        $tenant = \App\Models\Tenant::on('mysql')->find($tenantId);
        $tenantSlug = $tenant ? ($tenant->tenant_code ?: $tenant->id) : 1;

        $inboundWebhookUrl = url("/api/msg91/webhook/{$tenantSlug}/inbound");
        $outboundWebhookUrl = url("/api/msg91/webhook/{$tenantSlug}/outbound");

        return view('software-setup.msg91-settings.index', compact('setting', 'tenant', 'inboundWebhookUrl', 'outboundWebhookUrl'));
    }

    /**
     * Store or update MSG91 WhatsApp settings.
     */
    public function store(Request $request)
    {
        $request->validate([
            'auth_key' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:30',
            'whatsapp_namespace' => 'required|string|max:255',
        ]);

        $setting = Msg91Setting::first();
        if (!$setting) {
            $setting = new Msg91Setting();
        }

        $setting->auth_key = trim($request->input('auth_key'));
        // Clean phone number (leave digits only)
        $cleanNumber = preg_replace('/[^0-9]/', '', $request->input('whatsapp_number'));
        $setting->whatsapp_number = $cleanNumber;
        $setting->whatsapp_namespace = trim($request->input('whatsapp_namespace'));
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'MSG91 WhatsApp settings saved successfully!',
            'setting' => $setting
        ]);
    }

    /**
     * Test MSG91 API credentials and fetch template count.
     */
    public function testConnection(Request $request)
    {
        $authKey = trim($request->input('auth_key'));
        $number = preg_replace('/[^0-9]/', '', $request->input('whatsapp_number'));

        if (!$authKey || !$number) {
            $setting = Msg91Setting::first();
            $authKey = $authKey ?: $setting?->auth_key;
            $number = $number ?: $setting?->whatsapp_number;
        }

        if (!$authKey || !$number) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide both Auth Key and WhatsApp Number to test the connection.'
            ], 422);
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $authKey,
                'content-type' => 'text/plain'
            ])->timeout(12)->get("https://control.msg91.com/api/v5/whatsapp/get-template-client/{$number}?page_size=5");

            if ($response->successful()) {
                $data = $response->json();
                $templatesCount = count($data['data'] ?? []);
                return response()->json([
                    'success' => true,
                    'message' => "Connection successful! Verified with MSG91 and loaded {$templatesCount} initial templates.",
                    'data' => $data
                ]);
            } else {
                $errorMsg = $response->json('message') ?? $response->body() ?? 'Failed to authenticate with MSG91';
                return response()->json([
                    'success' => false,
                    'message' => 'MSG91 API error: ' . $errorMsg
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('MSG91 Connection Test Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
