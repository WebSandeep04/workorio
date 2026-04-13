<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use App\Models\CallingType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\CallingAssignmentLog;

class AssignedCallingController extends Controller
{
    public function index()
    {
        return view('calling.assigned');
    }

    /**
     * Helper to get assigned query with campaign info
     */
    private function getAssignedQuery($userId)
    {
        $junkTypeId = \App\Models\CallingType::where('name', 'Junk')->value('id');

        // Join with pivot to get campaign name and current owner
        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('calling_assignment_logs')
                    ->whereColumn('calling_assignment_logs.calling_id', 'callings.id')
                    ->where('calling_assignment_logs.assigned_by', $userId);
            })
            ->where('calling_campaign_calling.user_id', '!=', $userId) // Assigned to someone else
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
                'calling_campaign_calling.user_id as current_owner_id',
                'users.name as current_owner_name',
                'calling_types.name as pivot_status',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function getAssignedCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = $this->getAssignedQuery($userId);

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
    }

    public function filterAssignedCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = $this->getAssignedQuery($userId);

        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }
        if ($request->filled('state_id')) {
            $stateName = \App\Models\State::where('id', $request->state_id)->value('state_name');
            if ($stateName) {
                $query->where('callings.state', $stateName);
            }
        }
        if ($request->filled('city_id')) {
            $cityName = \App\Models\City::where('id', $request->city_id)->value('city_name');
            if ($cityName) {
                $query->where('callings.city', $cityName);
            }
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        return response()->json($query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage));
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

    public function reassignCalling(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $callingId = $request->calling_id;
        $campaignId = $request->campaign_id;
        $newUserId = $request->new_user_id;

        // Verify I am the one who assigned it or I have total access
        $logExists = CallingAssignmentLog::where('calling_id', $callingId)
            ->where('assigned_by', $userId)
            ->exists();

        if (!$logExists) {
            return response()->json(['error' => 'You are not authorized to reassign this calling'], 403);
        }

        // Get old user id
        $oldUserId = DB::table('calling_campaign_calling')
            ->where('calling_id', $callingId)
            ->where('calling_campaign_id', $campaignId)
            ->value('user_id');

        // Update the assignment
        DB::table('calling_campaign_calling')
            ->where('calling_id', $callingId)
            ->where('calling_campaign_id', $campaignId)
            ->update([
                'user_id' => $newUserId,
                'updated_at' => now()
            ]);

        // Log the assignment
        CallingAssignmentLog::create([
            'calling_id' => $callingId,
            'calling_campaign_id' => $campaignId,
            'from_user_id' => $oldUserId,
            'to_user_id' => $newUserId,
            'assigned_by' => $userId,
            'remark' => 'Re-assigned from Assigned Dashboard'
        ]);

        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Calling reassigned successfully to ' . ($newUser->name ?? 'User'),
        ]);
    }

    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();
        $teamMembers = User::where('id', '!=', $userId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        return response()->json($teamMembers);
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

    public function getLeadDetailsWithRemarks(Request $request)
    {
        $callingId = $request->id ?? $request->sales_record_id;
        
        $lead = DB::table('callings')
            ->leftJoin('calling_campaign_calling', 'callings.id', '=', 'calling_campaign_calling.calling_id')
            ->leftJoin('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('callings.id', $callingId)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_types.name as status_name'
            )
            ->first();

        if (!$lead) return response()->json(['error' => 'Not found'], 404);

        $remarks = DB::table('calling_remarks')
            ->leftJoin('users', 'calling_remarks.user_id', '=', 'users.id')
            ->where('calling_id', $callingId)
            ->orderBy('calling_remarks.created_at', 'desc')
            ->select('calling_remarks.*', 'users.name as user_name')
            ->get()
            ->map(function($r) {
                return [
                    'date' => Carbon::parse($r->created_at)->format('d M Y, h:i A'),
                    'remark' => $r->remark,
                    'user' => $r->user_name
                ];
            });

        return response()->json([
            'lead' => $lead,
            'sales_record' => [
                'id' => $lead->id,
                'leads_name' => $lead->name,
                'contact_person' => $lead->contact_person,
                'contact_number' => $lead->phone,
                'email' => $lead->email,
                'state_name' => $lead->state,
                'city_name' => $lead->city,
                'product_name' => $lead->campaign_name ?? '-',
                'business_name' => $lead->company_name ?? '-',
                'status_name' => $lead->status_name ?? '-',
                'ticket_value' => $lead->turnover ?? '-',
                'next_follow_up_date' => $lead->next_followup_date ?? '-'
            ],
            'remarks' => $remarks
        ]);
    }

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
