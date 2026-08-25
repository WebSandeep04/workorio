<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Msg91Setting;
use App\Models\WhatsappTemplateMapping;
use App\Traits\TenantAwareStorage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Msg91TemplateController extends Controller
{
    use TenantAwareStorage;
    public function index()
    {
        return view('whatsapp_templates.index');
    }

    public function fetch(Request $request)
    {
        $setting = Msg91Setting::first();
        if (!$setting || !$setting->auth_key || !$setting->whatsapp_number) {
            return response()->json([
                'success' => false,
                'message' => 'MSG91 Settings not configured.'
            ], 400);
        }

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $setting->auth_key,
                'content-type' => 'text/plain'
            ])->get("https://control.msg91.com/api/v5/whatsapp/get-template-client/{$setting->whatsapp_number}?page_size=500");

            if ($response->successful()) {
                $data = $response->json();
                $templates = $data['data'] ?? [];
                
                $formatted = [];
                $total = 0;
                $approved = 0;
                $rejected = 0;

                foreach ($templates as $t) {
                    if (isset($t['languages']) && is_array($t['languages'])) {
                        foreach ($t['languages'] as $lang) {
                            $total++;
                            $status = strtolower($lang['status'] ?? 'pending');
                            if ($status === 'approved') {
                                $approved++;
                            } else {
                                $rejected++;
                            }
                            
                            $formatted[] = [
                                'name' => $t['name'] ?? '',
                                'category' => $t['category'] ?? 'N/A',
                                'status' => $lang['status'] ?? 'pending',
                                'language' => $lang['language'] ?? 'en_US',
                                'variables' => $lang['variables'] ?? [],
                                'components' => $lang['code'] ?? []
                            ];
                        }
                    }
                }

                // Apply Search
                $search = $request->input('search');
                if ($search) {
                    $formatted = array_filter($formatted, function($item) use ($search) {
                        return stripos($item['name'], $search) !== false || stripos($item['category'], $search) !== false;
                    });
                    // Re-index array
                    $formatted = array_values($formatted);
                }

                // Apply Pagination
                $page = (int)$request->input('page', 1);
                $perPage = 10;
                $offset = ($page - 1) * $perPage;
                $paginatedData = array_slice($formatted, $offset, $perPage);
                $totalPages = ceil(count($formatted) / $perPage);

                return response()->json([
                    'success' => true,
                    'summary' => [
                        'total' => $total,
                        'approved' => $approved,
                        'rejected' => $rejected
                    ],
                    'templates' => [
                        'data' => $paginatedData,
                        'current_page' => $page,
                        'last_page' => $totalPages,
                        'total' => count($formatted)
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch templates from MSG91.'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getMapping($template_name)
    {
        $mapping = WhatsappTemplateMapping::where('template_name', $template_name)->first();
        return response()->json([
            'success' => true,
            'mapping' => $mapping
        ]);
    }

    public function storeMapping(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
        ]);

        $templateName = $request->template_name;
        $mappings = json_decode($request->mappings, true) ?? [];
        
        $mappingRecord = WhatsappTemplateMapping::firstOrNew(['template_name' => $templateName]);
        
        $existingMediaUrls = $mappingRecord->media_urls ?? [];
        
        if ($request->hasFile('media')) {
            $basePath = $this->getTenantPath('template_media');
            
            foreach ($request->file('media') as $variable => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs($basePath, $filename, 'public');
                $existingMediaUrls[$variable] = asset('storage/' . $path);
            }
        }

        $mappingRecord->mappings = $mappings;
        $mappingRecord->media_urls = $existingMediaUrls;
        $mappingRecord->save();

        return response()->json([
            'success' => true,
            'message' => 'Template mapping saved successfully!',
            'data' => $mappingRecord
        ]);
    }

    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $setting = Msg91Setting::first();
        if (!$setting || !$setting->auth_key || !$setting->whatsapp_number) {
            return response()->json([
                'success' => false,
                'message' => 'MSG91 Settings not configured.'
            ], 400);
        }

        $phone = $request->phone_number;
        // MSG91 requires 91 prefix for India if not present
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }

        // Get template mapping
        $mappingRecord = WhatsappTemplateMapping::where('template_name', $request->template_name)->first();
        
        $components = new \stdClass();
        if ($mappingRecord) {
            $mappings = $mappingRecord->mappings ?? [];
            $mediaUrls = $mappingRecord->media_urls ?? [];

            foreach ($mappings as $variable => $field) {
                $value = 'Test';
                if ($field === 'phone_number') {
                    $value = $phone;
                }
                $components->{$variable} = [
                    "type" => "text",
                    "value" => $value
                ];
            }

            foreach ($mediaUrls as $variable => $url) {
                $components->{$variable} = [
                    "type" => "image",
                    "value" => $url
                ];
            }
        }

        $toAndComponents = [
            [
                "to" => [ $phone ],
                "components" => $components
            ]
        ];

        $payload = [
            "integrated_number" => $setting->whatsapp_number,
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => $request->template_name,
                    "language" => [
                        "code" => "en", 
                        "policy" => "deterministic"
                    ],
                    "namespace" => $setting->whatsapp_namespace ?? '',
                    "to_and_components" => $toAndComponents
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $setting->auth_key,
                'content-type' => 'application/json'
            ])->post("https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/", $payload);

            if ($response->successful() && isset($response->json()['hasError']) && $response->json()['hasError'] === false) {
                return response()->json(['success' => true, 'message' => 'Test message sent successfully!']);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test message: ' . ($response->json()['message'] ?? 'Unknown error')
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
