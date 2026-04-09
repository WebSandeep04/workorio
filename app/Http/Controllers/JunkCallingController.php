<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use App\Models\CallingType;
use App\Models\CallingRemark;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JunkCallingController extends Controller
{
    public function index()
    {
        return view('calling.junk');
    }

    private function getJunkQuery()
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->where('calling_campaign_calling.calling_type_id', $junkTypeId)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_campaign_calling.calling_campaign_id',
                'users.name as agent_name',
                'calling_types.name as status_name',
                'calling_campaign_calling.id as pivot_id',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function getCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = $this->getJunkQuery();

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function filterCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = $this->getJunkQuery();

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

    public function destroy($id)
    {
        try {
            // $id here is pivot_id to be safe
            $pivot = DB::table('calling_campaign_calling')->where('id', $id)->first();
            if (!$pivot) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

            // Delete remarks for THIS specific lead
            DB::table('calling_remarks')->where('calling_id', $pivot->calling_id)->delete();
            
            // Delete the pivot entry
            DB::table('calling_campaign_calling')->where('id', $id)->delete();
            
            // Check if this lead exists in any other campaign
            $existsElse = DB::table('calling_campaign_calling')->where('calling_id', $pivot->calling_id)->exists();
            if (!$existsElse) {
                DB::table('callings')->where('id', $pivot->calling_id)->delete();
            }

            return response()->json(['success' => true, 'message' => 'Lead removed from Junk pool.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            $pivot = DB::table('calling_campaign_calling')->where('id', $id)->first();
            if (!$pivot) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

            $defaultType = CallingType::where('name', '!=', 'Junk')->orderBy('id')->value('id');
            
            DB::table('calling_campaign_calling')
                ->where('id', $id)
                ->update([
                    'calling_type_id' => $defaultType,
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Lead restored successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
