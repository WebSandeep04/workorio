<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WhatsappCampaignReportApiController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\WhatsappCampaign::withCount([
            'members',
            'members as sent_count' => function ($query) {
                $query->whereIn('status', ['Sent', 'Delivered', 'Read', 'Completed']);
            },
            'members as failed_count' => function ($query) {
                $query->where('status', 'Failed');
            }
        ])->latest();
        
        $campaigns = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    public function show($id)
    {
        $campaign = \App\Models\WhatsappCampaign::with('members')->find($id);
        
        if (!$campaign) {
            return response()->json(['success' => false, 'message' => 'Campaign not found'], 404);
        }

        $totalMembers = $campaign->members->count();
        $sentCount = $campaign->members->whereIn('status', ['Sent', 'Delivered', 'Read', 'Completed'])->count();
        $failedCount = $campaign->members->where('status', 'Failed')->count();
        $pendingCount = $campaign->members->where('status', 'Pending')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'campaign' => [
                    'id' => $campaign->id,
                    'name' => $campaign->name,
                    'status' => $campaign->status,
                ],
                'summary' => [
                    'total_members' => $totalMembers,
                    'sent' => $sentCount,
                    'failed' => $failedCount,
                    'pending' => $pendingCount,
                ],
                'members' => $campaign->members->map(function($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name ?? '-',
                        'phone_number' => $member->phone_number,
                        'status' => $member->status,
                        'error_message' => $member->error_message ?? ''
                    ];
                })
            ]
        ]);
    }
}
