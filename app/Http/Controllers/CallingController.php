<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\CallingCampaign;
use App\Models\CallingType;
use Illuminate\Support\Facades\DB;

class CallingController extends Controller
{
    /**
     * Show the master list of all contacts.
     */
    public function index()
    {
        return view('calling.index');
    }

    /**
     * Show the Lock Calling page for campaign-specific work.
     */
    public function lockIndex()
    {
        return view('calling.lock');
    }

    /**
     * Fetch all records via AJAX.
     */
    public function getCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $callings = Calling::orderBy('id', 'desc')->paginate($perPage);
            
        return response()->json($callings);
    }

    /**
     * Get list of all campaigns.
     */
    public function getCampaigns()
    {
        return response()->json(CallingCampaign::orderBy('id', 'desc')->get());
    }

    /**
     * Filter records via AJAX.
     */
    public function filterCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::query();

        // Filter by Campaign if provided
        if ($request->filled('campaign_id')) {
            $query->whereHas('campaigns', function($q) use ($request) {
                $q->where('calling_campaigns.id', $request->campaign_id);
                // If on the Lock Calling page, filter out leads already locked
                if ($request->header('referer') && str_contains($request->header('referer'), '/calling/lock')) {
                    // Show only AVAILABLE leads (is_locked = 0)
                    $q->where('calling_campaign_calling.is_locked', 0);
                }
            });
        }
            
        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('phone', 'like', $like)
                  ->orWhere('address', 'like', $like);
            });
        }

        if ($request->filled('state_id')) {
            $query->where('state', 'like', '%' . $request->state_id . '%');
        }

        if ($request->filled('city_id')) {
            $query->where('city', 'like', '%' . $request->city_id . '%');
        }

        return response()->json($query->orderBy('id', 'desc')->paginate($perPage));
    }

    /**
     * Provide filter options for the search box.
     */
    public function getFilterOptions()
    {
        return response()->json([
            'states' => Calling::distinct()
                ->whereNotNull('state')
                ->orderBy('state')
                ->get(['state as id', 'state as name']),
        ]);
    }

    /**
     * Create a new campaign and assign selected callings to it.
     */
    public function createCampaign(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'calling_ids' => 'required|array',
            'calling_ids.*' => 'integer|exists:callings,id'
        ]);

        try {
            DB::beginTransaction();

            $campaign = CallingCampaign::create([
                'name' => $request->campaign_name,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $pivotData = [];
            foreach ($request->calling_ids as $id) {
                $pivotData[] = [
                    'calling_id' => $id,
                    'calling_campaign_id' => $campaign->id,
                    'user_id' => null,
                    'is_locked' => 0,
                    'calling_type_id' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            DB::table('calling_campaign_calling')->insert($pivotData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Campaign '{$campaign->name}' created with " . count($request->calling_ids) . " contacts!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create campaign: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lock selected leads for the current user.
     */
    public function lockLeads(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:calling_campaigns,id',
            'calling_ids' => 'required|array',
            'calling_ids.*' => 'integer|exists:callings,id'
        ]);

        try {
            $userId = $this->getCurrentUserId();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to identify user. Please re-login.'
                ], 401);
            }

            DB::table('calling_campaign_calling')
                ->where('calling_campaign_id', $request->campaign_id)
                ->whereIn('calling_id', $request->calling_ids)
                ->update([
                    'user_id' => $userId,
                    'is_locked' => 1,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => count($request->calling_ids) . ' leads have been locked and assigned to you.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user ID from Auth or Session.
     */
    private function getCurrentUserId(): ?int
    {
        return auth()->id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }

    /**
     * Show the remarks and interaction history for a lead.
     */
    public function remarks($id)
    {
        $calling = Calling::findOrFail($id);
        $campaignId = request('campaign_id');
        $currentCampaign = null;
        $pivotData = null;

        if ($campaignId) {
            $currentCampaign = CallingCampaign::find($campaignId);
            $pivotData = DB::table('calling_campaign_calling')
                ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
                ->where('calling_id', $calling->id)
                ->where('calling_campaign_id', $campaignId)
                ->select('calling_campaign_calling.*', 'calling_types.name as status_name')
                ->first();
        }

        $callingTypes = CallingType::orderBy('name')->get();
        $currentUserId = $this->getCurrentUserId();

        return view('calling.remarks', compact('calling', 'currentCampaign', 'pivotData', 'callingTypes', 'currentUserId'));
    }

    /**
     * Store a new remark and update campaign status.
     */
    public function storeRemark(Request $request, $id)
    {
        $calling = Calling::findOrFail($id);
        
        $request->validate([
            'remark' => 'required|string',
            'calling_type_id' => 'nullable|exists:calling_types,id',
            'next_followup_date' => 'nullable|date',
            'campaign_id' => 'nullable|integer|exists:calling_campaigns,id'
        ]);

        $userId = $this->getCurrentUserId();

        try {
            DB::beginTransaction();

            // 1. Create the interaction log (global)
            $newRemarkId = DB::table('calling_remarks')->insertGetId([
                'calling_id' => $calling->id,
                'calling_campaign_id' => $request->campaign_id,
                'user_id' => $userId,
                'remark' => $request->remark,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Update campaign specific context in pivot table
            if ($request->filled('campaign_id')) {
                DB::table('calling_campaign_calling')
                    ->where('calling_id', $calling->id)
                    ->where('calling_campaign_id', $request->campaign_id)
                    ->update([
                        'calling_type_id' => $request->calling_type_id,
                        'next_followup_date' => $request->next_followup_date,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Remark saved successfully.',
                    'remark' => [
                        'id' => $newRemarkId,
                        'text' => $request->remark,
                        'user_id' => $userId,
                        'created_at' => now()->format('d M Y, h:i A')
                    ],
                    'pivot' => [
                        'status' => DB::table('calling_types')->where('id', $request->calling_type_id)->value('name') ?? 'Not Set',
                        'next_date' => $request->next_followup_date
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Remark saved and campaign status updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save remark: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing remark via AJAX.
     */
    public function updateRemark(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string',
            'calling_type_id' => 'nullable|exists:calling_types,id',
            'next_followup_date' => 'nullable|date',
            'campaign_id' => 'nullable|integer|exists:calling_campaigns,id'
        ]);

        try {
            DB::beginTransaction();

            $remarkRow = DB::table('calling_remarks')->where('id', $id)->first();
            if (!$remarkRow) throw new \Exception("Remark not found.");

            // 1. Update the interaction log
            DB::table('calling_remarks')
                ->where('id', $id)
                ->update([
                    'remark' => $request->remark,
                    'updated_at' => now()
                ]);

            // 2. Update campaign specific context in pivot table
            if ($request->filled('campaign_id')) {
                DB::table('calling_campaign_calling')
                    ->where('calling_id', $remarkRow->calling_id)
                    ->where('calling_campaign_id', $request->campaign_id)
                    ->update([
                        'calling_type_id' => $request->calling_type_id,
                        'next_followup_date' => $request->next_followup_date,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Remark updated successfully.',
                'pivot' => [
                    'status' => DB::table('calling_types')->where('id', $request->calling_type_id)->value('name') ?? 'Not Set',
                    'next_date' => $request->next_followup_date
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX helper for Cities.
     */
    public function getCitiesByState($stateName)
    {
        return response()->json(
            Calling::distinct()
                ->where('state', $stateName)
                ->whereNotNull('city')
                ->orderBy('city')
                ->get(['city as id', 'city as name'])
        );
    }
}
