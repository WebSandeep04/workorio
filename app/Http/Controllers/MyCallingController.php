<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyCallingController extends Controller
{
    public function index()
    {
        return view('calling.mycalling');
    }

    /**
     * Get list of campaigns where the current user has locked leads.
     */
    public function getMyCampaigns()
    {
        $userId = $this->getCurrentUserId();
        
        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->select('calling_campaigns.id', 'calling_campaigns.name', DB::raw('count(*) as leads_count'))
            ->groupBy('calling_campaigns.id', 'calling_campaigns.name')
            ->get();

        return response()->json($campaigns);
    }

    /**
     * Helper to get the base query for My Calls
     */
    private function getMyCallsQuery($userId)
    {
        // We query from the pivot table to allow same lead in different campaigns
        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_campaign_calling.calling_campaign_id',
                'calling_types.name as pivot_status',
                'calling_campaign_calling.next_followup_date as pivot_followup',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function getCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);
        
        $query = $this->getMyCallsQuery($userId);

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);
        
        $query = $this->getMyCallsQuery($userId);
            
        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like)
                  ->orWhere('callings.address', 'like', $like);
            });
        }
        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_id')) {
            $query->where('callings.state', 'like', '%' . $request->state_id . '%');
        }
        if ($request->filled('city_id')) {
            $query->where('callings.city', 'like', '%' . $request->city_id . '%');
        }

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => Calling::distinct()
                ->whereNotNull('state')
                ->orderBy('state')
                ->get(['state as id', 'state as name']),
        ]);
    }

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

    private function getCurrentUserId(): ?int
    {
        return Auth::id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }
}
