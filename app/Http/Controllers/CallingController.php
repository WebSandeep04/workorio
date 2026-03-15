<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Models\CallingRemark;
use App\Models\CallingType;
class CallingController extends Controller
{
    public function index()
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        
        $callings = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', '!=', $junkTypeId)
            ->where('is_locked', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('calling.index', compact('callings'));   
    }

    // my() and junk() are handled by MyCallingController and JunkCallingController respectively

    // Removed: my() and junk() are now handled by separate controllers

    public function getCallings(Request $request)
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', '!=', $junkTypeId)
            ->where('is_locked', 0);
        $callings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($callings);
    }

    public function filterCallings(Request $request)
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', '!=', $junkTypeId)
            ->where('is_locked', 0); // Exclude junk calls
            
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
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get([
                'id',
                \DB::raw('state_name as name')
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
                ->get([
                    'id',
                    \DB::raw('city_name as name')
                ])
        );
    }

    private function getCurrentUserId(): ?int
    {
        return Auth::id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }

    public function remarks($callingId)
    {
        $calling = Calling::with(['state:id,state_name', 'city:id,city_name', 'callingType:id,name', 'remarks' => function($q){
            $q->where('is_locked', 0);
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($callingId);

        // Provide default values from backend
        $defaultCallingType = $calling->calling_type_id; // Current calling type or null
        $defaultNextFollowUp = $calling->next_follow_up_date ?? now()->addDays(7)->toDateString(); // Next week if not set

        return view('calling.remarks', compact('calling', 'defaultCallingType', 'defaultNextFollowUp'));
    }

    public function storeRemark(Request $request, $callingId)
    {
        $request->validate([
            'remark' => 'required|string',
            'remark_id' => 'nullable|integer',
            'remark_date' => 'nullable|date',
            'next_follow_up_date' => 'nullable|date',
            'calling_type_id' => 'nullable|integer|exists:calling_types,id',
        ]);

        if ($request->filled('remark_id')) {
            $remark = CallingRemark::where('id', (int) $request->remark_id)
                ->where('calling_id', (int) $callingId)
                ->firstOrFail();

            $remark->remark = $request->remark;
            if ($request->remark_date) {
                $remark->created_at = \Carbon\Carbon::parse($request->remark_date);
            }
            $remark->save();
        } else {
            CallingRemark::create([
                'calling_id' => (int) $callingId,
                'remark' => $request->remark,
                'created_at' => $request->remark_date ? \Carbon\Carbon::parse($request->remark_date) : now(),
            ]);
        }

        // Update calling record with next follow up date, calling type, and user_id
        $calling = Calling::find((int) $callingId);
        if ($calling) {
            // Always update user_id to current logged-in user when remark is added/updated
            $calling->user_id = $this->getCurrentUserId();
            
            if ($request->filled('next_follow_up_date')) {
                $calling->next_follow_up_date = $request->next_follow_up_date;
            }
            if ($request->filled('calling_type_id')) {
                $calling->calling_type_id = $request->calling_type_id;
            }
            $calling->save();
        }

        return redirect()->route('calling.remarks.show', ['calling' => $callingId])
            ->with('status', 'Remark added and calling details updated successfully');
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
            $calling = Calling::findOrFail($request->calling_id);
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

    /**
     * Lock selected callings to current user
     */
    public function lockSelected(Request $request)
    {
        $request->validate([
            'calling_ids' => 'required|array',
            'calling_ids.*' => 'integer|exists:callings,id'
        ]);

        try {
            $currentUserId = $this->getCurrentUserId();
            $callingIds = $request->calling_ids;

            // Update user_id for selected callings
            $updatedCount = Calling::whereIn('id', $callingIds)
                ->update(['user_id' => $currentUserId, 'is_locked' => 1]);

            return response()->json([
                'success' => true,
                'message' => "Successfully locked {$updatedCount} calling(s) to your account!",
                'locked_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock selected callings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function allCallings()
    {
        return view('calling.all');
    }

    public function getAllCallingsData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])->whereNotNull('user_id');
        
        $callings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json($callings);
    }

    public function filterAllCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])->whereNotNull('user_id');
            
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
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }
}

