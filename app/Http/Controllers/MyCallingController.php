<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\CallingType;

class MyCallingController extends Controller
{
    public function index()
    {
        $userId = $this->getCurrentUserId();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name'])
            ->where('calling_type_id', '!=', $junkTypeId)
            ->orderBy('created_at', 'desc');
        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }
        $callings = $query->get();
        return view('calling.mycalling', compact('callings'));
    }

    public function getCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', '!=', $junkTypeId);
        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', '!=', $junkTypeId); // Exclude junk calls
            
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
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
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

    /**
     * Update calling type
     */
    public function updateCallingType(Request $request)
    {
        $request->validate([
            'calling_id' => 'required|integer|exists:callings,id',
            'calling_type_id' => 'required|integer|exists:calling_types,id'
        ]);

        try {
            $userId = $this->getCurrentUserId();
            $query = Calling::where('id', $request->calling_id);
            
            // Ensure user can only update their own calls
            if ($userId && Schema::hasColumn('callings', 'user_id')) {
                $query->where('user_id', $userId);
            }
            
            $calling = $query->firstOrFail();
            $calling->calling_type_id = $request->calling_type_id;
            $calling->save();

            return response()->json([
                'success' => true,
                'message' => 'Calling type updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update calling type: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();

        $teamMembers = \App\Models\User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->where('users.id', '!=', $userId) // Exclude self
            ->select('users.id', 'users.name')
            ->get();

        return response()->json($teamMembers);
    }

    public function reassignCalling(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $callingId = $request->calling_id;
        $newUserId = $request->new_user_id;

        // Find and update the calling (must belong to current user)
        $query = Calling::where('id', $callingId);
        if ($userId && Schema::hasColumn('callings', 'user_id')) {
            $query->where('user_id', $userId);
        }

        $calling = $query->first();

        if (!$calling) {
            return response()->json(['error' => 'Calling not found or not accessible'], 404);
        }

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
            'remark' => 'Calling reassigned by user'
        ]);

        // Get the new user's name for response
        $newUser = \App\Models\User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Calling reassigned successfully to ' . ($newUser ? $newUser->name : 'Unknown User'),
            'new_user_name' => $newUser ? $newUser->name : 'Unknown User'
        ]);
    }
}


