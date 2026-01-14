<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use App\Models\CallingType;
use App\Models\CallingRemark;

class JunkCallingController extends Controller
{
    public function index()
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        
        $callings = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name'])
            ->where('calling_type_id', $junkTypeId)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('calling.junk', compact('callings'));
    }

    public function getCallings(Request $request)
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $callings = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', $junkTypeId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
            
        return response()->json($callings);
    }

    public function filterCallings(Request $request)
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);
        
        $query = Calling::with(['state:id,state_name', 'city:id,city_name', 'latestRemark', 'callingType:id,name', 'status:id,status_name'])
            ->where('calling_type_id', $junkTypeId); // Only show Junk calls
            
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
        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get(['id', \DB::raw('state_name as name')]),
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

    /**
     * Delete a junk calling and its related remarks
     */
    public function destroy($id)
    {
        try {
            $junkTypeId = CallingType::where('name', 'Junk')->value('id');
            $calling = Calling::where('calling_type_id', $junkTypeId)->findOrFail($id);
            
            // Delete related calling remarks first
            CallingRemark::where('calling_id', $id)->delete();
            
            // Delete the calling record
            $calling->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Junk call and related remarks deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete junk call: ' . $e->getMessage()
            ], 500);
        }
    }
}


