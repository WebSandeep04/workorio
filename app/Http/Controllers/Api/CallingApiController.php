<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Calling;
use App\Models\State;
use App\Models\City;
use App\Models\CallingType;

class CallingApiController extends Controller
{
    /**
     * Fetch and filter All Callings data.
     */
    public function getAllCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        // Building basic query equivalent to getAllCallingsData on web
        $query = DB::table('callings')
            ->leftJoin('calling_campaign_calling', function($join) {
                $join->on('callings.id', '=', 'calling_campaign_calling.calling_id')
                    ->whereRaw('calling_campaign_calling.id = (SELECT MAX(id) FROM calling_campaign_calling WHERE calling_id = callings.id)');
            })
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('calling_remarks', function($join) {
                $join->on('callings.id', '=', 'calling_remarks.calling_id')
                    ->whereRaw('calling_remarks.id = (SELECT MAX(id) FROM calling_remarks WHERE calling_id = callings.id)');
            })
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            });

        // Apply Search Filter
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.company_name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        // Apply Categorical Filters
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }

        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }

        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        // Selection Columns
        $query->select(
            'callings.*',
            'calling_types.name as calling_type_name',
            'calling_campaign_calling.calling_type_id',
            'calling_campaign_calling.next_followup_date as next_follow_up_date',
            'calling_remarks.remark as latest_remark_text'
        );

        $paginated = $query->orderBy('callings.id', 'desc')->paginate($perPage);

        // Map items structure for direct unified consumption in mobile components
        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->calling_type_name ? (object)['name' => $item->calling_type_name] : null;
            $item->status = $item->calling_type_name ? (object)['status_name' => $item->calling_type_name] : null;
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Load distinct states, cities, and calling types for UI filtering.
     */
    public function getFilterOptions()
    {
        $states = Calling::distinct()
            ->whereNotNull('state')
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        $callingTypes = CallingType::where('name', '!=', 'Junk')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'states' => $states,
            'cities' => $cities,
            'calling_types' => $callingTypes
        ]);
    }

    /**
     * Retrieve lead details and historical interaction logs.
     */
    public function getRemarks($id)
    {
        $lead = DB::table('callings')
            ->leftJoin('calling_campaign_calling', 'callings.id', '=', 'calling_campaign_calling.calling_id')
            ->leftJoin('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('callings.id', $id)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_types.name as status_name'
            )
            ->first();

        if (!$lead) {
            return response()->json(['error' => 'Calling record not found'], 404);
        }

        $remarks = DB::table('calling_remarks')
            ->leftJoin('users', 'calling_remarks.user_id', '=', 'users.id')
            ->where('calling_id', $id)
            ->orderBy('calling_remarks.created_at', 'desc')
            ->select('calling_remarks.*', 'users.name as user_name')
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'date' => Carbon::parse($r->created_at)->format('d M Y, h:i A'),
                    'remark' => $r->remark,
                    'user' => $r->user_name ?? 'System'
                ];
            });

        return response()->json([
            'lead' => $lead,
            'remarks' => $remarks
        ]);
    }
}
