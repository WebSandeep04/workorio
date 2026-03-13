<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SalesRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AllDataController extends Controller
{

    public function index(){
        return view('alldata');
    }

    // Get summary stats for all sales records (not user-specific)
    public function getSummaryStats()
    {
        $today = Carbon::today()->toDateString();

        // Today's Follow Ups - all records where next_follow_up_date is today or past
        $todayFollowups = DB::table('sales_records')
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->count();

        // Under Process - updated today and next_follow_up_date is today
        $underProcess = DB::table('sales_records')
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();

        // Today Completed - updated today and next_follow_up_date is future
        $todayCompleted = DB::table('sales_records')
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();

        // Today Pending - next_follow_up_date is today or past
        $todayPending = DB::table('sales_records')
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();

        // Today New - created today
        $todayNew = DB::table('sales_records')
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('createdat', $today)
            ->count();

        return response()->json([
            'today_followups' => $todayFollowups,
            'under_process' => $underProcess,
            'today_completed' => $todayCompleted,
            'today_pending' => $todayPending,
            'today_new' => $todayNew
        ]);
    }

    // Get status counts for all sales records
    public function getStatusCounts()
    {
        $statusCounts = DB::table('sales_status')
            ->leftJoin('sales_records', 'sales_status.id', '=', 'sales_records.status_id')
            ->select(
                'sales_status.id',
                'sales_status.status_name',
                DB::raw('COUNT(sales_records.id) as count')
            )
            ->groupBy('sales_status.id', 'sales_status.status_name')
            ->orderBy('sales_status.status_name')
            ->get();

        return response()->json($statusCounts);
    }

    
    public function fetchalldata()
{
    $records = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin(DB::raw('(
            SELECT r1.id, r1.sales_remark_id, r1.remark
            FROM remarks r1
            INNER JOIN (
                SELECT sales_remark_id, MAX(remark_date) as latest_date
                FROM remarks
                GROUP BY sales_remark_id
            ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
        ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
        ->orderBy('sales_records.createdat', 'desc')
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'latest_remarks.remark as latest_remark'
        )
        ->paginate(20);

    return response()->json($records);
}


// adddatafilter

 public function alldatafilter(Request $request)
{
    $userId = Auth::id();
    $query = DB::table('sales_records')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                 ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date) 
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                 )');
        });

    // Apply filters
    if ($request->status) {
        $query->where('sales_records.status_id', $request->status);
    }
    if ($request->user_id) {
        $query->where('sales_records.user_id', $request->user_id);
    }
    // no role filter needed; dropdown already provides only sales users

    if ($request->state) {
        $query->where('prospectuses.state_id', $request->state);
    }

    if ($request->city) {
        $query->where('prospectuses.city_id', $request->city);
    }

    if ($request->business) {
        $query->where('prospectuses.business_type_id', $request->business);
    }

    if ($request->source) {
        $query->where('sales_records.lead_source_id', $request->source);
    }

    if ($request->product) {
        $query->where('sales_records.products_id', $request->product);
    }

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(20);

    return response()->json($sales);
}

// alldatasearch
public function alldatasearch(Request $request)
{
    $userId = Auth::id();
    $searchTerm = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date)
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                )');
        });

    // Apply search if provided
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('sales_records.leads_name', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_person', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_number', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.prospectus_name', 'like', "%$searchTerm%")
              ->orWhere('sales_status.status_name', 'like', "%$searchTerm%")
              ->orWhere('sales_business_types.business_name', 'like', "%$searchTerm%")
              ->orWhere('sales_lead_sources.source_name', 'like', "%$searchTerm%")
              ->orWhere('sales_products.product_name', 'like', "%$searchTerm%")
              ->orWhere('states.state_name', 'like', "%$searchTerm%")
              ->orWhere('cities.city_name', 'like', "%$searchTerm%");
        });
    }
    if ($request->user_id) {
        $query->where('sales_records.user_id', $request->user_id);
    }
    // no role filter needed here

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'prospectuses.contact_person',
        'prospectuses.contact_number',
        'prospectuses.email',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(20);

    return response()->json($sales);
}

// date filter
public function alldatafilterdate(Request $request)
{
    $userId = Auth::id();
    $query = DB::table('sales_records')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('states', 'prospectuses.state_id', '=', 'states.id')
        ->leftJoin('cities', 'prospectuses.city_id', '=', 'cities.id')
        ->leftJoin('sales_business_types', 'prospectuses.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('remarks as r', function ($join) {
            $join->on('r.sales_remark_id', '=', 'sales_records.id')
                 ->whereRaw('r.remark_date = (
                    SELECT MAX(remark_date) 
                    FROM remarks 
                    WHERE sales_remark_id = sales_records.id
                 )');
        });

    // Filter by next_follow_up_date between from and to
    if ($request->from_date && $request->to_date) {
        try {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();

            $query->whereBetween('sales_records.next_follow_up_date', [
                $from->format('Y-m-d'),
                $to->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid date format'
            ], 422);
        }
    }

    // Other filters
    if ($request->status) {
        $query->where('sales_records.status_id', $request->status);
    }
    if ($request->user_id) {
        $query->where('sales_records.user_id', $request->user_id);
    }
    // no role filter needed here
    if ($request->state) {
        $query->where('prospectuses.state_id', $request->state);
    }
    if ($request->city) {
        $query->where('prospectuses.city_id', $request->city);
    }
    if ($request->business) {
        $query->where('prospectuses.business_type_id', $request->business);
    }
    if ($request->source) {
        $query->where('sales_records.lead_source_id', $request->source);
    }
    if ($request->product) {
        $query->where('sales_records.products_id', $request->product);
    }

    $sales = $query->select(
        'sales_records.*',
        'prospectuses.prospectus_name',
        'states.state_name',
        'cities.city_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'sales_status.status_name',
        'r.remark as last_remark',
        'r.remark_date'
    )->paginate(20);

    return response()->json($sales);
}

    // Reassign lead to different team member (for managers and admins)
    public function reassignLead(Request $request)
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role_id;
        $leadId = $request->lead_id;
        $newUserId = $request->new_user_id;

        // Find the lead
        $lead = SalesRecord::where('id', $leadId)
            ->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead not found or not accessible'], 404);
        }

        // Authorization logic
        if ($userRole == 1) {
            // Admin can reassign any lead
            // Verify the new user exists
            $newUser = User::where('id', $newUserId)
                ->first();
            
            if (!$newUser) {
                return response()->json(['error' => 'Invalid user selected'], 403);
            }
        } else {
            // For managers, check if they have subordinates and if the lead belongs to them
            $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->pluck('users.id');

            if ($subordinateIds->isEmpty()) {
                return response()->json(['error' => 'You are not authorized to reassign leads'], 403);
            }

            // Verify the new user is a subordinate
            if (!$subordinateIds->contains($newUserId)) {
                return response()->json(['error' => 'Invalid team member selected'], 403);
            }

            // Verify the lead belongs to the current user
            if ($lead->user_id != $userId) {
                return response()->json(['error' => 'Lead not found or not accessible'], 404);
            }
        }

        // Update the lead assignment
        $lead->user_id = $newUserId;
        $lead->updatedat = now();
        $lead->save();

        // Get the new user's name for response
        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Lead reassigned successfully to ' . $newUser->name,
            'new_user_name' => $newUser->name
        ]);
    }

    // Get team members for reassignment dropdowns (for managers and admins)
    public function getTeamMembers()
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role_id;

        if ($userRole == 1) {
            // Admin can assign to any sales user
            $teamMembers = User::where('role_id', 2) // sales_user role
                ->where('id', '!=', $userId) // Exclude self
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } else {
            // Managers can only assign to their subordinates
            $teamMembers = User::whereHas('managers', function($q) use ($userId) {
                    $q->where('manager_id', $userId);
                })
                ->where('users.id', '!=', $userId) // Exclude self
                ->select('users.id', 'users.name')
                ->orderBy('users.name')
                ->get();
        }

        return response()->json($teamMembers);
    }

    public function todayFollowupsTable()
    {
        return view('alldata.today-followups');
    }

    public function todayFollowupsData()
    {
        $today = Carbon::today()->toDateString();

        $records = DB::table('sales_records')
            ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
            ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
            ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
            ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
            ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
            ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
            ->leftJoin(DB::raw('(
                SELECT r1.id, r1.sales_remark_id, r1.remark
                FROM remarks r1
                INNER JOIN (
                    SELECT sales_remark_id, MAX(remark_date) as latest_date
                    FROM remarks
                    GROUP BY sales_remark_id
                ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
            ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
            ->where(function ($query) use ($today) {
                $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                            ->whereDate('sales_records.updatedat', '=', $today);
                      });
            })
            ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
            ->orderBy('sales_records.next_follow_up_date', 'asc')
            ->select(
                'sales_records.*',
                'sales_status.status_name',
                'prospectuses.prospectus_name',
                'sales_business_types.business_name',
                'sales_lead_sources.source_name',
                'sales_products.product_name',
                'states.state_name',
                'cities.city_name',
                'latest_remarks.remark as latest_remark'
            )->paginate(20);

        return response()->json($records);
    }

    public function underProcessTable()
    {
        return view('alldata.under-process');
    }

    public function underProcessData()
    {
        $today = Carbon::today()->toDateString();

        $records = DB::table('sales_records')
            ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
            ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
            ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
            ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
            ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
            ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
            ->leftJoin(DB::raw('(
                SELECT r1.id, r1.sales_remark_id, r1.remark
                FROM remarks r1
                INNER JOIN (
                    SELECT sales_remark_id, MAX(remark_date) as latest_date
                    FROM remarks
                    GROUP BY sales_remark_id
                ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
            ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
            ->whereDate('sales_records.next_follow_up_date', '=', $today)
            ->whereDate('sales_records.updatedat', '=', $today)
            ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
            ->orderBy('sales_records.next_follow_up_date', 'asc')
            ->select(
                'sales_records.*',
                'sales_status.status_name',
                'prospectuses.prospectus_name',
                'sales_business_types.business_name',
                'sales_lead_sources.source_name',
                'sales_products.product_name',
                'states.state_name',
                'cities.city_name',
                'latest_remarks.remark as latest_remark'
            )->paginate(20);

        return response()->json($records);
    }

    public function todayCompletedTable()
    {
        return view('alldata.today-completed');
    }

    public function todayCompletedData()
    {
        $today = Carbon::today()->toDateString();

        $records = DB::table('sales_records')
            ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
            ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
            ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
            ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
            ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
            ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
            ->leftJoin(DB::raw('(
                SELECT r1.id, r1.sales_remark_id, r1.remark
                FROM remarks r1
                INNER JOIN (
                    SELECT sales_remark_id, MAX(remark_date) as latest_date
                    FROM remarks
                    GROUP BY sales_remark_id
                ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
            ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
            ->whereDate('sales_records.next_follow_up_date', '>', $today)
            ->whereDate('sales_records.updatedat', '=', $today)
            ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
            ->orderBy('sales_records.next_follow_up_date', 'asc')
            ->select(
                'sales_records.*',
                'sales_status.status_name',
                'prospectuses.prospectus_name',
                'sales_business_types.business_name',
                'sales_lead_sources.source_name',
                'sales_products.product_name',
                'states.state_name',
                'cities.city_name',
                'latest_remarks.remark as latest_remark'
            )->paginate(20);

        return response()->json($records);
    }

    public function todayPendingTable()
    {
        return view('alldata.today-pending');
    }

    public function todayPendingData()
    {
        $today = Carbon::today()->toDateString();

        $records = DB::table('sales_records')
            ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
            ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
            ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
            ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
            ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
            ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
            ->leftJoin(DB::raw('(
                SELECT r1.id, r1.sales_remark_id, r1.remark
                FROM remarks r1
                INNER JOIN (
                    SELECT sales_remark_id, MAX(remark_date) as latest_date
                    FROM remarks
                    GROUP BY sales_remark_id
                ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
            ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
            ->where(function ($query) use ($today) {
                $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                      ->orWhereNull('sales_records.next_follow_up_date');
            })
            ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
            ->orderBy('sales_records.next_follow_up_date', 'asc')
            ->select(
                'sales_records.*',
                'sales_status.status_name',
                'prospectuses.prospectus_name',
                'sales_business_types.business_name',
                'sales_lead_sources.source_name',
                'sales_products.product_name',
                'states.state_name',
                'cities.city_name',
                'latest_remarks.remark as latest_remark'
            )->paginate(20);

        return response()->json($records);
    }

    public function todayNewTable()
    {
        return view('alldata.today-new');
    }

    public function todayNewData()
    {
        $today = Carbon::today()->toDateString();

        $records = DB::table('sales_records')
            ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
            ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
            ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
            ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
            ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
            ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
            ->leftJoin(DB::raw('(
                SELECT r1.id, r1.sales_remark_id, r1.remark
                FROM remarks r1
                INNER JOIN (
                    SELECT sales_remark_id, MAX(remark_date) as latest_date
                    FROM remarks
                    GROUP BY sales_remark_id
                ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
            ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
            ->whereDate('sales_records.createdat', '=', $today)
            ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
            ->orderBy('sales_records.createdat', 'desc')
            ->select(
                'sales_records.*',
                'sales_status.status_name',
                'prospectuses.prospectus_name',
                'sales_business_types.business_name',
                'sales_lead_sources.source_name',
                'sales_products.product_name',
                'states.state_name',
                'cities.city_name',
                'latest_remarks.remark as latest_remark'
            )->paginate(20);

        return response()->json($records);
    }
}
