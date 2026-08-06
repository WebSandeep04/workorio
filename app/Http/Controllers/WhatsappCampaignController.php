<?php

namespace App\Http\Controllers;

use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignMember;
use App\Models\SalesRecord;
use App\Models\Prospectus;
use App\Models\Customer;
use App\Models\Calling;
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
        $data = null;

        switch ($sourceType) {
            case 'SalesRecord':
                $data = SalesRecord::select('id', 'leads_name as name', 'contact_number as phone')->paginate(50);
                break;
            case 'Prospectus':
                $data = Prospectus::select('id', 'contact_person as name', 'contact_number as phone')->paginate(50);
                break;
            case 'Customer':
                $data = Customer::select('id', 'name', 'phone')->paginate(50);
                break;
            case 'Calling':
                $data = Calling::select('id', 'contact_person as name', 'phone')->paginate(50);
                break;
        }

        return response()->json($data);
    }

    public function addMembers(Request $request, $id)
    {
        $whatsapp_campaign = WhatsappCampaign::findOrFail($id);

        $request->validate([
            'source_type' => 'required|string|in:SalesRecord,Prospectus,Customer,Calling',
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
}
