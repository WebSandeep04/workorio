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
use App\Models\User;

class LeadGenerationController extends Controller
{
    /**
     * My Gen Leads Index
     */
    public function myLeads()
    {
        $user = $this->getCurrentUser();
        $hasSubordinates = $user && $user->subordinates()->exists();

        return view('leadgen.my', compact('hasSubordinates'));
    }

    // --- My Leads Mirror Functions ---

    public function getMyLeads(Request $request)
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
        ->whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
        })
        ->orderBy('createdat', 'desc')
        ->paginate($perPage);

        return response()->json($records);
    }

    public function filterLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

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
        ->whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
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

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    public function getFilterOptions()
    {
        $options = [
            'statuses' => SalesStatus::orderBy('status_name')->get(['id', 'status_name']),
            'states' => State::orderBy('state_name')->get(['id', 'state_name']),
            'cities' => City::orderBy('city_name')->get(['id', 'city_name']),
            'business_types' => SalesBusinessType::orderBy('business_name')->get(['id', 'business_name']),
            'lead_sources' => SalesLeadSource::orderBy('source_name')->get(['id', 'source_name']),
            'products' => SalesProduct::orderBy('product_name')->get(['id', 'product_name'])
        ];

        return response()->json($options);
    }

    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)->orderBy('city_name')->get(['id', 'city_name']);
        return response()->json($cities);
    }

    public function getLeadStats()
    {
        $userId = $this->getCurrentUserId();

        $stats = [
            'total_leads' => SalesRecord::where('user_id', $userId)->count(),
            'leads_this_month' => SalesRecord::where('user_id', $userId)
                ->whereMonth('createdat', Carbon::now()->month)
                ->whereYear('createdat', Carbon::now()->year)
                ->count(),
            'leads_today' => SalesRecord::where('user_id', $userId)->whereDate('createdat', Carbon::today())->count(),
        ];

        return response()->json($stats);
    }

    public function getSummaryStats()
    {
        $userId = $this->getCurrentUserId();

        $query = SalesRecord::whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
        });

        return response()->json([
            'today_followups' => (clone $query)->whereDate('next_follow_up_date', now())->count(),
            'under_process' => (clone $query)->where('status_id', 2)->count(),
            'today_completed' => (clone $query)->where('status_id', 4)->count(),
            'today_pending' => (clone $query)->where('status_id', 5)->count(),
            'today_new' => (clone $query)->where('status_id', 1)->count(),
        ]);
    }

    public function getStatusCounts()
    {
        $userId = $this->getCurrentUserId();
        $counts = SalesStatus::select('sales_statuses.id', 'sales_statuses.status_name')
            ->selectRaw('count(sales_records.id) as count')
            ->leftJoin('sales_records', function($join) use ($userId) {
                $join->on('sales_statuses.id', '=', 'sales_records.status_id')
                     ->whereIn('sales_records.id', function($query) use ($userId) {
                         $query->select('sales_record_id')
                               ->from('lead_assignment_logs')
                               ->where('assigned_by', $userId);
                     });
            })
            ->groupBy('sales_statuses.id', 'sales_statuses.status_name')
            ->get();

        return response()->json($counts);
    }

    public function exportLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();

        $query = SalesRecord::with(['status', 'prospectus', 'city', 'state', 'businessType', 'leadSource', 'product', 'latestRemark'])
            ->whereHas('assignmentLogs', function($aq) use ($userId) {
                $aq->where('assigned_by', $userId);
            });

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        $leads = $query->orderBy('createdat', 'desc')->get();
        return response()->json($leads);
    }

    public function reassignLead(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $leadId = $request->lead_id;
        $newUserId = $request->new_user_id;

        $lead = SalesRecord::where('id', $leadId)->where('user_id', $userId)->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead not found or not accessible'], 404);
        }

        $lead->user_id = $newUserId;
        $lead->updatedat = now();
        $lead->save();

        $newUser = User::find($newUserId);

        return response()->json([
            'success' => true,
            'message' => 'Lead reassigned successfully to ' . $newUser->name,
            'new_user_name' => $newUser->name
        ]);
    }

    public function getTeamMembers()
    {
        $teamMembers = User::where('is_sales', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($teamMembers);
    }

    /**
     * Helper Methods
     */

    private function getCurrentUserId()
    {
        if (Auth::check()) return Auth::id();
        if (session()->has('user_id')) return session('user_id');
        return null;
    }

    private function getCurrentUser()
    {
        if (Auth::check()) return Auth::user();
        if (session()->has('user_id')) {
            $userId = session('user_id');
            try {
                return \App\Models\User::find($userId);
            } catch (\Exception $e) { }
        }
        return null;
    }
}
