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

    // Get team callings with pagination
    public function getTeamCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        })->pluck('id');

        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);

        // Get only subordinates' callings, NOT manager's own callings
        $records = Calling::with([
            'state:id,state_name',
            'city:id,city_name',
            'latestRemark',
            'callingType:id,name',
            'status:id,status_name'
        ])
        ->whereIn('user_id', $subordinateIds)
        ->where('calling_type_id', '!=', $junkTypeId)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

        return response()->json($records);
    }

    // Get filtered team callings
    public function filterTeamCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        })->pluck('id');

        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);

        $query = Calling::with([
            'state:id,state_name',
            'city:id,city_name',
            'latestRemark',
            'callingType:id,name',
            'status:id,status_name'
        ])
        ->whereIn('user_id', $subordinateIds)
        ->where('calling_type_id', '!=', $junkTypeId);

        // Apply filters
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
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_type_id', $request->calling_type_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($records);
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

    // Reassign calling to different team member
    public function reassignCalling(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $callingId = $request->calling_id;
        $newUserId = $request->new_user_id;

        // Verify the current user is a manager
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        })->pluck('id');

        if ($subordinateIds->isEmpty()) {
            return response()->json(['error' => 'You are not authorized to reassign callings'], 403);
        }

        // Verify the new user is a subordinate
        if (!$subordinateIds->contains($newUserId)) {
            return response()->json(['error' => 'Invalid team member selected'], 403);
        }

        // Find and update the calling
        $calling = Calling::where('id', $callingId)
            ->whereIn('user_id', $subordinateIds)
            ->first();

        if (!$calling) {
            return response()->json(['error' => 'Calling not found or not accessible'], 404);
        }

        // Keep track of the old user id
        $fromUserId = $calling->user_id;

        // Update the calling assignment
        $calling->user_id = $newUserId;
        $calling->updated_at = now();
        $calling->save();

        // Log the assignment
        \App\Models\CallingAssignmentLog::create([
            'calling_id' => $calling->id,
            'from_user_id' => $fromUserId,
            'to_user_id' => $newUserId,
            'assigned_by' => $userId,
            'remark' => 'Calling reassigned by manager'
        ]);

        // Get the new user's name for response
        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Calling reassigned successfully to ' . $newUser->name,
            'new_user_name' => $newUser->name
        ]);
    }

    // Get team members for reassignment dropdowns
    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();

        $teamMembers = User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->where('id', '!=', $userId) // Exclude self
            ->select('id', 'name')
            ->get();

        return response()->json($teamMembers);
    }

    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('city_name')
            ->get(['id', \DB::raw('city_name as name')]);

        return response()->json($cities);
    }
    
    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        if (session()->has('user_id')) {
            return session('user_id');
        }
        
        return null;
    }
}
