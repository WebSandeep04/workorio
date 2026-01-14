<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



use Illuminate\Http\Request;

use App\Models\SalesRecord;
use Illuminate\Support\Facades\DB;


class FollowupController extends Controller
{

    public function index(){
        return view('followup');
    }
    
    public function getSalesRecords(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $records = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark'
        ])
        ->where('user_id', $userId)
        ->whereDate('next_follow_up_date', '<=', Carbon::today())
        ->whereNotIn('status_id', [1, 2, 15, 20])
        ->orderBy('next_follow_up_date')
        ->paginate($perPage);

        return response()->json($records);
    }


  public function filter(Request $request)
{
    $userId = $this->getCurrentUserId();
    
    $perPage = $request->get('per_page', 10);

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
        })
        ->where('sales_records.user_id', $userId)
        ->whereDate('sales_records.next_follow_up_date', '<=', Carbon::today())
        ->whereNotIn('sales_records.status_id', [1, 2,15,20]); // Exclude Close Won (1) and Close Lost (2)

    // Apply filters
    if ($request->status) {
        $query->where('sales_records.status_id', $request->status);
    }

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
    )->paginate($perPage);

    return response()->json($sales);
}


public function search(Request $request)
{
    $userId = $this->getCurrentUserId();
    $perPage = $request->get('per_page', 10);
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
        })
        ->where('sales_records.user_id', $userId)
        ->whereDate('sales_records.next_follow_up_date', '<=', Carbon::today());

    // Apply search if provided
    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('sales_records.leads_name', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_person', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.contact_number', 'like', "%$searchTerm%")
              ->orWhere('prospectuses.prospectus_name', 'like', "%$searchTerm%")
              ->orWhere('sales_records.address', 'like', "%$searchTerm%")
              ->orWhere('sales_status.status_name', 'like', "%$searchTerm%")
              ->orWhere('sales_business_types.business_name', 'like', "%$searchTerm%")
              ->orWhere('sales_lead_sources.source_name', 'like', "%$searchTerm%")
              ->orWhere('sales_products.product_name', 'like', "%$searchTerm%")
              ->orWhere('states.state_name', 'like', "%$searchTerm%")
              ->orWhere('cities.city_name', 'like', "%$searchTerm%");
        });
    }

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
    )->paginate($perPage);

    return response()->json($sales);
}


public function filterdate(Request $request)
{
    $userId = $this->getCurrentUserId();
    $perPage = $request->get('per_page', 10);
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
        })
        ->where('sales_records.user_id', $userId)
        ->whereDate('sales_records.next_follow_up_date', '<=', Carbon::today())
        ->whereNotIn('sales_records.status_id', [1, 2,15,20]); // Exclude Close Won (1) and Close Lost (2)

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
    )->paginate($perPage);

    return response()->json($sales);
}

    public function getSummaryStats()
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();

        $todayFollowups = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->count();

        $underProcess = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();

        $todayCompleted = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();

        $todayPending = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();

        $todayNew = SalesRecord::where('user_id', $userId)
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

    public function getStatusCounts()
    {
        $userId = $this->getCurrentUserId();

        $statusCounts = DB::table('sales_status')
            ->leftJoin('sales_records', function($join) use ($userId) {
                $join->on('sales_status.id', '=', 'sales_records.status_id')
                     ->where('sales_records.user_id', '=', $userId);
            })
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

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        return session('user_id');
    }

}
