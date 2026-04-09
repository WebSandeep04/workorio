<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TodaysCallingController extends Controller
{
    public function index()
    {
        return view('calling.todayscalling');
    }

    public function getCallings(Request $request)
    {
        $today = now()->toDateString();
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Calling::whereHas('campaigns', function($q) use ($userId, $today) {
            $q->where('calling_campaign_calling.user_id', $userId)
              ->whereNotNull('calling_campaign_calling.next_followup_date')
              ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today);
        });

        return response()->json($query->orderBy('id', 'desc')->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $today = now()->toDateString();
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Calling::whereHas('campaigns', function($q) use ($userId, $today) {
            $q->where('calling_campaign_calling.user_id', $userId)
              ->whereNotNull('calling_campaign_calling.next_followup_date')
              ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today);
        });

        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
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
