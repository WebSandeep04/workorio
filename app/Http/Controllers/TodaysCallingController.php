<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\CallingType;

class TodaysCallingController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $followUpTypeId = CallingType::where('name', 'Follow Up')->value('id');
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name'])
            ->where('calling_type_id', $followUpTypeId) // Only show Follow Up calls
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->orderBy('next_follow_up_date')
            ->orderBy('created_at', 'desc');

        $userId = $this->getCurrentUserId();
        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }

        $callings = $query->get();

        $grouped = $callings->groupBy(function ($c) {
            return $c->next_follow_up_date ? Carbon::parse($c->next_follow_up_date)->format('Y-m-d') : 'No Date';
        });

        return view('calling.todayscalling', [
            'groupedCallings' => $grouped,
            'today' => $today,
        ]);
    }

    public function getCallings(Request $request)
    {
        $today = now()->toDateString();
        $followUpTypeId = CallingType::where('name', 'Follow Up')->value('id');
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', $followUpTypeId)
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->orderBy('next_follow_up_date')
            ->orderBy('created_at', 'desc');

        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }

        return response()->json($query->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $today = now()->toDateString();
        $followUpTypeId = CallingType::where('name', 'Follow Up')->value('id');
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', $followUpTypeId)
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<=', $today)
            ->orderBy('next_follow_up_date')
            ->orderBy('created_at', 'desc');

        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }

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
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_type_id', $request->calling_type_id);
        }

        return response()->json($query->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get(['id', \DB::raw('state_name as name')]),
            'calling_types' => CallingType::orderBy('name')->get(['id', 'name']),
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


