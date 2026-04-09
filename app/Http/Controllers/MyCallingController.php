<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CallingAssignmentLog;

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

    private function getMyCallsQuery($userId)
    {
        $junkTypeId = \App\Models\CallingType::where('name', 'Junk')->value('id');

        // We query from the pivot table to allow same lead in different campaigns
        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            })
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
            $stateName = \App\Models\State::where('id', $request->state_id)->value('state_name');
            if ($stateName) {
                $query->where('callings.state', $stateName);
            }
        }
        if ($request->filled('city_id')) {
            $cityName = \App\Models\City::where('id', $request->city_id)->value('city_name');
            if ($cityName) {
                $query->where('callings.city', $cityName);
            }
        }

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => \App\Models\State::orderBy('state_name')->get([
                'id', \DB::raw('state_name as name')
            ]),
            'calling_types' => \App\Models\CallingType::where('name', '!=', 'Junk')
                ->orderBy('name')->get([
                    'id',
                    'name'
                ]),
        ]);
    }

    public function getCitiesByState($stateId)
    {
        return response()->json(
            \App\Models\City::where('state_id', $stateId)
                ->orderBy('city_name')
                ->get(['id', 'city_name as name'])
        );
    }

    /**
     * Reassign calling to different user/team member
     */
    public function reassignCalling(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $callingId = $request->calling_id;
        $campaignId = $request->campaign_id;
        $newUserId = $request->new_user_id;

        // Find the pivot record
        $pivot = DB::table('calling_campaign_calling')
            ->where('calling_id', $callingId)
            ->where('calling_campaign_id', $campaignId)
            ->where('user_id', $userId)
            ->first();

        if (!$pivot) {
            return response()->json(['error' => 'Assignment not found or not accessible'], 404);
        }

        // Update the assignment
        DB::table('calling_campaign_calling')
            ->where('calling_id', $callingId)
            ->where('calling_campaign_id', $campaignId)
            ->update([
                'user_id' => $newUserId,
                'updated_at' => now()
            ]);

        // Log the assignment
        CallingAssignmentLog::create([
            'calling_id' => $callingId,
            'calling_campaign_id' => $campaignId,
            'from_user_id' => $userId,
            'to_user_id' => $newUserId,
            'assigned_by' => $userId,
            'remark' => 'Reassigned from My Calling'
        ]);

        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Calling reassigned successfully to ' . ($newUser->name ?? 'User'),
        ]);
    }

    /**
     * Get team members for reassignment
     */
    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();

        // Get all members of the same teams or subordinates
        // To keep it simple for now, let's fetch all active users if the user has permission
        // Or just subordinates from TeamCalling logic
        $teamMembers = User::where('id', '!=', $userId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($teamMembers);
    }

    private function getCurrentUserId(): ?int
    {
        return Auth::id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }
}
