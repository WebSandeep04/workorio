<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Msg91Setting;
use Illuminate\Support\Facades\Http;

class Msg91TemplateController extends Controller
{
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
}
