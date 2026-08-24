<?php

namespace App\Http\Controllers;

use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignMember;
use App\Models\SalesRecord;
use App\Models\Prospectus;
use App\Models\Customer;
use App\Models\Calling;
use App\Models\BusinessCardScan;
use App\Models\Msg91Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WhatsappCampaignController extends Controller
{
    public function index()
    {
        return view('whatsapp_campaigns.index');
    }

    public function create()
    {
        return view('whatsapp_campaigns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        WhatsappCampaign::create([
            'name' => $request->name,
            'status' => 'Draft',
        ]);

        return response()->json(['success' => true, 'message' => 'Campaign created successfully.']);
    }

    public function show($id)
    {
        $whatsapp_campaign = WhatsappCampaign::findOrFail($id);
        $whatsapp_campaign->load('members');
        return view('whatsapp_campaigns.show', compact('whatsapp_campaign'));
    }

    public function getSourceData(Request $request)
    {
        $sourceType = $request->source_type;
        $search = $request->search;
        $data = null;

        switch ($sourceType) {
            case 'SalesRecord':
                $query = SalesRecord::select('id', 'leads_name as name', 'contact_number as phone');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('leads_name', 'like', "%{$search}%")
                          ->orWhere('contact_number', 'like', "%{$search}%");
                    });
                }
                $data = $query->paginate(50);
                break;
            case 'Prospectus':
                $query = Prospectus::select('id', 'contact_person as name', 'contact_number as phone');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('contact_person', 'like', "%{$search}%")
                          ->orWhere('contact_number', 'like', "%{$search}%");
                    });
                }
                $data = $query->paginate(50);
                break;
            case 'Customer':
                $query = Customer::select('id', 'name', 'phone');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
                $data = $query->paginate(50);
                break;
            case 'Calling':
                $query = Calling::select('id', 'contact_person as name', 'phone');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('contact_person', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
                $data = $query->paginate(50);
                break;
            case 'BusinessCardScan':
                $query = BusinessCardScan::select('id', 'name', 'phone_primary as phone');
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('phone_primary', 'like', "%{$search}%");
                    });
                }
                $data = $query->paginate(50);
                break;
        }

        return response()->json($data);
    }

    public function addMembers(Request $request, $id)
    {
        $whatsapp_campaign = WhatsappCampaign::findOrFail($id);

        $request->validate([
            'source_type' => 'required|string|in:SalesRecord,Prospectus,Customer,Calling,BusinessCardScan',
            'member_ids' => 'required_without:select_all|array',
            'member_ids.*' => 'integer',
            'select_all' => 'boolean'
        ]);

        $sourceType = "App\\Models\\" . $request->source_type;
        
        $addedCount = 0;

        if ($request->boolean('select_all')) {
            switch ($request->source_type) {
                case 'SalesRecord':
                    $records = SalesRecord::whereNotNull('contact_number')->get(['id', 'leads_name as name', 'contact_number as phone']);
                    break;
                case 'Prospectus':
                    $records = Prospectus::whereNotNull('contact_number')->get(['id', 'contact_person as name', 'contact_number as phone']);
                    break;
                case 'Customer':
                    $records = Customer::whereNotNull('phone')->get(['id', 'name', 'phone']);
                    break;
                case 'Calling':
                    $records = Calling::whereNotNull('phone')->get(['id', 'contact_person as name', 'phone']);
                    break;
                case 'BusinessCardScan':
                    $records = BusinessCardScan::whereNotNull('phone_primary')->get(['id', 'name', 'phone_primary as phone']);
                    break;
            }
        } else {
            $records = collect();
            foreach ($request->member_ids as $memberId) {
                switch ($request->source_type) {
                    case 'SalesRecord':
                        $record = SalesRecord::find($memberId);
                        if ($record) $records->push((object)['id' => $record->id, 'name' => $record->leads_name, 'phone' => $record->contact_number]);
                        break;
                    case 'Prospectus':
                        $record = Prospectus::find($memberId);
                        if ($record) $records->push((object)['id' => $record->id, 'name' => $record->contact_person, 'phone' => $record->contact_number]);
                        break;
                    case 'Customer':
                        $record = Customer::find($memberId);
                        if ($record) $records->push((object)['id' => $record->id, 'name' => $record->name, 'phone' => $record->phone]);
                        break;
                    case 'Calling':
                        $record = Calling::find($memberId);
                        if ($record) $records->push((object)['id' => $record->id, 'name' => $record->contact_person, 'phone' => $record->phone]);
                        break;
                    case 'BusinessCardScan':
                        $record = BusinessCardScan::find($memberId);
                        if ($record) $records->push((object)['id' => $record->id, 'name' => $record->name, 'phone' => $record->phone_primary]);
                        break;
                }
            }
        }

        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            $phone = $record->phone;

            if ($phone) {
                $exists = WhatsappCampaignMember::where('whatsapp_campaign_id', $whatsapp_campaign->id)
                    ->where('source_type', $sourceType)
                    ->where('source_id', $id)
                    ->exists();

                if (!$exists) {
                    WhatsappCampaignMember::create([
                        'whatsapp_campaign_id' => $whatsapp_campaign->id,
                        'source_type' => $sourceType,
                        'source_id' => $id,
                        'name' => $name,
                        'phone_number' => $phone,
                        'status' => 'Pending'
                    ]);
                    $addedCount++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$addedCount members added successfully."
        ]);
    }

    public function fetch(Request $request)
    {
        $query = WhatsappCampaign::withCount('members')->latest();
        
        $total = $query->count();
        $campaigns = $query->paginate(10);
        
        $active = WhatsappCampaign::where('status', 'Active')->count();
        $draft = WhatsappCampaign::where('status', 'Draft')->count();

        return response()->json([
            'campaigns' => $campaigns,
            'summary' => [
                'total' => $total,
                'active' => $active,
                'draft' => $draft
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|string|in:Active,Draft,Completed',
        ]);

        $campaign = WhatsappCampaign::findOrFail($id);
        $campaign->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Campaign updated successfully.']);
    }

    public function destroy($id)
    {
        $campaign = WhatsappCampaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['success' => true, 'message' => 'Campaign deleted successfully.']);
    }

    public function fetchMembers(Request $request, $id)
    {
        $whatsapp_campaign = WhatsappCampaign::findOrFail($id);
        $members = WhatsappCampaignMember::where('whatsapp_campaign_id', $whatsapp_campaign->id)->latest()->get();
        
        return response()->json([
            'members' => $members
        ]);
    }

    public function removeMember($member_id)
    {
        $member = WhatsappCampaignMember::findOrFail($member_id);
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member removed successfully.'
        ]);
    }

    public function fetchMsg91Templates()
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
                // Depending on MSG91 API response structure, we might need to parse this differently.
                // Assuming $data['data'] or similar contains the templates.
                $templates = $data['data'] ?? [];
                
                // Format the templates
                $formatted = [];
                foreach ($templates as $t) {
                    if (isset($t['languages']) && is_array($t['languages'])) {
                        foreach ($t['languages'] as $lang) {
                            $formatted[] = [
                                'name' => $t['name'] ?? '',
                                'language' => $lang['language'] ?? 'en_US',
                                'variables' => $lang['variables'] ?? []
                            ];
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => $formatted
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

    public function sendCampaign(Request $request, $id)
    {
        $request->validate([
            'template_name' => 'required|string',
        ]);

        $campaign = WhatsappCampaign::findOrFail($id);
        
        $setting = Msg91Setting::first();
        if (!$setting || !$setting->auth_key || !$setting->whatsapp_number || !$setting->whatsapp_namespace) {
            return response()->json([
                'success' => false,
                'message' => 'MSG91 Settings not completely configured.'
            ], 400);
        }

        // Get members that haven't been sent yet
        $members = $campaign->members()->where('status', '!=', 'Sent')->get();

        if ($members->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending members found in this campaign.'
            ], 400);
        }

        $mediaUrls = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $variable => $file) {
                $path = $file->store('campaign_media', 'public');
                $mediaUrls[$variable] = asset('storage/' . $path);
            }
        }

        $toAndComponents = [];

        foreach ($members as $member) {
            if (!$member->phone_number) continue;
            
            $phone = preg_replace('/[^0-9]/', '', $member->phone_number);
            
            // MSG91 requires 91 prefix for India if not present
            if (strlen($phone) == 10) {
                $phone = '91' . $phone;
            }

            $components = new \stdClass();
            $mappings = $request->input('mappings', []);
            
            foreach ($mappings as $variable => $field) {
                $value = '';
                if ($field === 'name') {
                    $value = $member->name ?? 'Customer';
                } elseif ($field === 'phone_number') {
                    $value = $member->phone_number ?? '';
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

            $toAndComponents[] = [
                "to" => [ $phone ],
                "components" => $components
            ];
        }

        if (empty($toAndComponents)) {
             return response()->json([
                'success' => false,
                'message' => 'No valid phone numbers found for the members.'
            ], 400);
        }

        $payload = [
            "integrated_number" => $setting->whatsapp_number,
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => $request->template_name,
                    "language" => [
                        "code" => "en", // MSG91 sometimes wants just 'en' or 'en_US'
                        "policy" => "deterministic"
                    ],
                    "namespace" => $setting->whatsapp_namespace,
                    "to_and_components" => $toAndComponents
                ]
            ]
        ];

        \Log::info('MSG91 Bulk WhatsApp Payload:', $payload);

        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'authkey' => $setting->auth_key,
                'content-type' => 'application/json'
            ])->post("https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/", $payload);

            \Log::info('MSG91 Bulk WhatsApp Response:', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body()
            ]);

            if ($response->successful() && isset($response->json()['hasError']) && $response->json()['hasError'] === false) {
                $campaign->update(['status' => 'Completed']);
                
                foreach($members as $member) {
                    $member->update(['status' => 'Sent']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Campaign messages sent successfully!'
                ]);
            }

            // It might be successful HTTP but hasError true
            if ($response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send campaign: ' . json_encode($response->json())
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send campaign: ' . $response->body()
            ], 400);

        } catch (\Exception $e) {
            \Log::error('MSG91 Bulk WhatsApp Exception:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
