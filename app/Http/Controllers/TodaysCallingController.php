<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TodaysCallingController extends Controller
{
    public function index()
    {
        return view('calling.todayscalling');
    }

    /**
     * Helper to get the base query for Today's Calls
     */
    private function getTodaysCallsQuery($userId, $today)
    {
        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->whereNotNull('calling_campaign_calling.next_followup_date')
            ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_campaign_calling.calling_campaign_id',
                'calling_types.name as pivot_status'
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
                  ->orWhere('callings.phone', 'like', $like)
                  ->orWhere('callings.address', 'like', $like);
            });
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
