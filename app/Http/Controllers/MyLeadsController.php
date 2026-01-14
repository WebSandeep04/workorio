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
use App\Models\User; // Added for reassignLead method

class MyLeadsController extends Controller
{

    public function index()
    {
        $user = $this->getCurrentUser();
        $hasSubordinates = $user && $user->subordinates()->exists();

        return view('myleads', compact('hasSubordinates'));
    }

    // Get user's leads with pagination
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
            'latestRemark'
        ])
        ->where('user_id', $userId)
        ->orderBy('createdat', 'desc')
        ->paginate(10);

        return response()->json($records);
    }

    // Get filtered leads
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
            'latestRemark'
        ])
        ->where('user_id', $userId);

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

        $records = $query->orderBy('createdat', 'desc')->paginate(10);

        return response()->json($records);
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

    // Get lead statistics for the user
    public function getLeadStats()
    {
        $userId = $this->getCurrentUserId();

        $stats = [
            'total_leads' => SalesRecord::where('user_id', $userId)
                ->count(),
            
            'leads_this_month' => SalesRecord::where('user_id', $userId)
                ->whereMonth('createdat', Carbon::now()->month)
                ->whereYear('createdat', Carbon::now()->year)
                ->count(),
            
            'leads_this_week' => SalesRecord::where('user_id', $userId)
                ->whereBetween('createdat', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            
            'leads_today' => SalesRecord::where('user_id', $userId)
                ->whereDate('createdat', Carbon::today())
                ->count(),
            
            'status_distribution' => SalesRecord::where('user_id', $userId)
                ->with('status')
                ->get()
                ->groupBy('status.status_name')
                ->map(function($group) {
                    return $group->count();
                }),
            
            'follow_ups_due_today' => SalesRecord::where('user_id', $userId)
                ->whereDate('next_follow_up_date', Carbon::today())
                ->count(),
            
            'follow_ups_due_this_week' => SalesRecord::where('user_id', $userId)
                ->whereBetween('next_follow_up_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count()];

        return response()->json($stats);
    }

    // Get summary stats for logged-in user's leads
    public function getSummaryStats()
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();

        // Today's Follow Ups - user's records where next_follow_up_date is today or past
        $todayFollowups = SalesRecord::where('user_id', $userId)
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
        $underProcess = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();

        // Today Completed - updated today and next_follow_up_date is future
        $todayCompleted = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();

        // Today Pending - next_follow_up_date is today or past
        $todayPending = SalesRecord::where('user_id', $userId)
            ->whereNotIn('status_id', [1, 2, 15, 20])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();

        // Today New - created today
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

    // Get status counts for logged-in user's leads
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

    // Export user's leads
    public function exportLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark'
        ])
        ->where('user_id', $userId);

        // Apply same filters as filterLeads method
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('createdat', 'desc')->get();

        return response()->json($leads);
    }

    // Reassign lead to any user (allow for all users on their own leads)
    public function reassignLead(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $leadId = $request->lead_id;
        $newUserId = $request->new_user_id;

        // Find and update the lead (must belong to current user)
        $lead = SalesRecord::where('id', $leadId)
            ->where('user_id', $userId)
            ->first();

        if (!$lead) {
            return response()->json(['error' => 'Lead not found or not accessible'], 404);
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

    // Get team members for reassignment dropdowns (for managers)
    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();

        $teamMembers = User::where('is_manager', $userId)
            ->where('id', '!=', $userId) // Exclude self
            ->select('id', 'name')
            ->get();

        return response()->json($teamMembers);
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
    
    /**
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        // Check if user is authenticated via Auth facade (super admin)
        if (Auth::check()) {
            return Auth::user();
        }
        
        // Check if user is authenticated via session (tenant users)
        if (session()->has('user_id')) {
            $userId = session('user_id');
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user; // Return the actual user model
                }
            } catch (\Exception $e) {
                // If user not found, return null
            }
        }
        
        return null;
    }

}
