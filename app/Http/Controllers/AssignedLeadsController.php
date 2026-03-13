<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesRecord;
use App\Models\SalesStatus;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use App\Models\SalesBusinessType;
use App\Models\State;
use App\Models\City;
use App\Models\Prospectus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignedLeadsController extends Controller
{
    public function index()
    {
        return view('assignedleads');
    }

    // Get assigned leads with pagination
    public function getAssignedLeads(Request $request)
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
            'latestRemark',
            'user'
        ])
        ->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('assignmentLogs', function($aq) use ($userId) {
                  $aq->where('assigned_by', $userId);
              });
        })
        ->orderBy('createdat', 'desc')
        ->paginate($perPage);

        return response()->json($records);
    }

    // Get filtered assigned leads
    public function filterAssignedLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 7);

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user'
        ])
        ->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('assignmentLogs', function($aq) use ($userId) {
                  $aq->where('assigned_by', $userId);
              });
        });

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('state_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('business_type_id')) {
            $query->whereHas('prospectus', function($q) use ($request) {
                $q->where('business_type_id', $request->business_type_id);
            });
        }

        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->lead_source_id);
        }

        if ($request->filled('products_id')) {
            $query->where('products_id', $request->products_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('prospectus', function($pq) use ($search) {
                      $pq->where('prospectus_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('createdat', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('createdat', '<=', $request->date_to);
        }

        if ($request->filled('follow_up_date_from')) {
            $query->whereDate('next_follow_up_date', '>=', $request->follow_up_date_from);
        }

        if ($request->filled('follow_up_date_to')) {
            $query->whereDate('next_follow_up_date', '<=', $request->follow_up_date_to);
        }

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    public function getSummaryStats()
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();

        $baseQuery = SalesRecord::where(function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->orWhereHas('assignmentLogs', function($aq) use ($userId) {
                      $aq->where('assigned_by', $userId);
                  });
            })
            ->whereNotIn('status_id', [1, 2, 15, 20]);

        $todayFollowups = (clone $baseQuery)
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->count();

        $underProcess = (clone $baseQuery)
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();

        $todayCompleted = (clone $baseQuery)
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();

        $todayPending = (clone $baseQuery)
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();

        $todayNew = (clone $baseQuery)
            ->whereDate('createdat', $today)
            ->count();

        return response()->json([
            'today_followups' => $todayFollowups,
            'under_process' => $underProcess,
            'today_completed' => $todayCompleted,
            'today_pending' => $todayPending,
            'today_new' => $todayNew,
        ]);
    }

    public function getStatusCounts()
    {
        $userId = $this->getCurrentUserId();

        $statusCounts = DB::table('sales_status')
            ->leftJoin('sales_records', function ($join) use ($userId) {
                $join->on('sales_status.id', '=', 'sales_records.status_id')
                     ->where(function($q) use ($userId) {
                         $q->where('sales_records.user_id', '=', $userId)
                           ->orWhereExists(function ($query) use ($userId) {
                               $query->select(DB::raw(1))
                                     ->from('lead_assignment_logs')
                                     ->whereColumn('lead_assignment_logs.sales_record_id', 'sales_records.id')
                                     ->where('lead_assignment_logs.assigned_by', $userId);
                           });
                     });
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

    // Get filter options for dropdowns
    public function getFilterOptions()
    {
        $options = [
            'statuses' => SalesStatus::orderBy('status_name')
                ->get(['id', 'status_name']),
            
            'states' => State::orderBy('state_name')
                ->get(['id', 'state_name']),
            
            'cities' => City::orderBy('city_name')
                ->get(['id', 'city_name']),
            
            'business_types' => SalesBusinessType::orderBy('business_name')
                ->get(['id', 'business_name']),
            
            'lead_sources' => SalesLeadSource::orderBy('source_name')
                ->get(['id', 'source_name']),
            
            'products' => SalesProduct::orderBy('product_name')
                ->get(['id', 'product_name'])];

        return response()->json($options);
    }

    // Get cities by state
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('city_name')
            ->get(['id', 'city_name']);

        return response()->json($cities);
    }
    
    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        // Check if user is authenticated via Auth facade (super admin)
        if (Auth::check()) {
            return Auth::id();
        }
        
        // Check if user is authenticated via session (tenant users)
        if (session()->has('user_id')) {
            return session('user_id');
        }
        
        return null;
    }
}
