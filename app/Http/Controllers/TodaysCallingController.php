<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TodaysCallingController extends Controller
{
    public function index()
    {
        return view('calling.todayscalling');
    }

    private function getTodaysCallsQuery($userId, $today)
    {
        $junkTypeId = \App\Models\CallingType::where('name', 'Junk')->value('id');

        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->whereNotNull('calling_campaign_calling.next_followup_date')
            ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today)
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
                'calling_types.name as status_name',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function getCallings(Request $request)
    {
        $today = now()->toDateString();
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = $this->getTodaysCallsQuery($userId, $today);

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $today = now()->toDateString();
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = $this->getTodaysCallsQuery($userId, $today);

        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
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

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get([
                'id', \DB::raw('state_name as name')
            ]),
        ]);
    }

    public function getCitiesByState($stateId)
    {
        return response()->json(
            City::where('state_id', $stateId)
                ->orderBy('city_name')
                ->get(['id', \DB::raw('city_name as name')])
        );
    }

    private function getCurrentUserId(): ?int
    {
        return Auth::id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }
}
