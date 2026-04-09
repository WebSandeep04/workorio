<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use App\Models\CallingType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamCallingController extends Controller
{
    public function index()
    {
        return view('calling.team');
    }

    private function getTeamQuery($userId)
    {
        $junkTypeId = \App\Models\CallingType::where('name', 'Junk')->value('id');
        
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        })->pluck('id');

        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->whereIn('calling_campaign_calling.user_id', $subordinateIds)
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
                'calling_campaign_calling.user_id as agent_id',
                'users.name as agent_name',
                'calling_types.name as status_name',
                'calling_campaign_calling.id as pivot_id',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function getTeamCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);
        $query = $this->getTeamQuery($userId);

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function filterTeamCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);
        $query = $this->getTeamQuery($userId);

        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like)
                  ->orWhere('users.name', 'like', $like);
            });
        }
        if ($request->filled('state_id')) {
            $stateName = State::where('id', $request->state_id)->value('state_name');
            if ($stateName) {
                $query->where('callings.state', $stateName);
            }
        }
        if ($request->filled('city_id')) {
            $cityName = City::where('id', $request->city_id)->value('city_name');
            if ($cityName) {
                $query->where('callings.city', $cityName);
            }
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get([
                'id', \DB::raw('state_name as name')
            ]),
            'calling_types' => CallingType::where('name', '!=', 'Junk')
                ->orderBy('name')->get([
                    'id',
                    'name'
                ]),
        ]);
    }

    public function reassignCalling(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $callingId = $request->calling_id;
        $campaignId = $request->campaign_id;
        $newUserId = $request->new_user_id;

        // Verify manager access
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        })->pluck('id');

        if (!$subordinateIds->contains($newUserId)) {
            return response()->json(['error' => 'Invalid team member'], 403);
        }

        // Get current assignment
        $pivot = DB::table('calling_campaign_calling')
            ->where('calling_id', $callingId)
            ->where('calling_campaign_id', $campaignId)
            ->first();

        if (!$pivot || !$subordinateIds->contains($pivot->user_id)) {
            return response()->json(['error' => 'Unauthorized or missing assignment'], 403);
        }

        $oldUserId = $pivot->user_id;

        // Update
        DB::table('calling_campaign_calling')
            ->where('id', $pivot->id)
            ->update([
                'user_id' => $newUserId,
                'updated_at' => now()
            ]);

        // Log
        \App\Models\CallingAssignmentLog::create([
            'calling_id' => $callingId,
            'calling_campaign_id' => $campaignId,
            'from_user_id' => $oldUserId,
            'to_user_id' => $newUserId,
            'assigned_by' => $userId,
            'remark' => 'Manager reassignment'
        ]);

        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Lead reassigned to ' . ($newUser->name ?? 'User')
        ]);
    }

    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();
        return response()->json(
            User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })->select('id', 'name')->orderBy('name')->get()
        );
    }

    public function getCitiesByState($stateId)
    {
        return response()->json(
            City::where('state_id', $stateId)
                ->orderBy('city_name')
                ->get(['id', \DB::raw('city_name as name')])
        );
    }

    private function getCurrentUserId()
    {
        if (Auth::check()) return Auth::id();
        return session('user_id');
    }
}
