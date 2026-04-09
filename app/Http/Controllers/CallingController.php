<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\CallingCampaign;
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
                    'status' => 'Cold',
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
            DB::table('calling_campaign_calling')
                ->where('calling_campaign_id', $request->campaign_id)
                ->whereIn('calling_id', $request->calling_ids)
                ->update([
                    'user_id' => auth()->id(),
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
