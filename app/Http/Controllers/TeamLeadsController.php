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
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamLeadsController extends Controller
{
    public function index()
    {
        return view('teamleads');
    }

    // Get team leads with pagination
    public function getTeamLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 7);

        // Simple query: Get only subordinates' leads, NOT manager's own leads
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
        ->whereHas('user', function($query) use ($userId) {
            $query->where('is_manager', $userId); // Only leads created by subordinates
        })
        ->orderBy('createdat', 'desc')
        ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $records->items(),
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total()
        ]);
    }

    // Get filtered team leads
    public function filterTeamLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 7);

        // Simple query: Get only subordinates' leads, NOT manager's own leads
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
        ->whereHas('user', function($q) use ($userId) {
            $q->where('is_manager', $userId); // Only leads created by subordinates
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

        return response()->json([
            'success' => true,
            'data' => $records->items(),
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total()
        ]);
    }



    // Get team lead statistics
    public function getTeamLeadStats()
    {
        $userId = $this->getCurrentUserId();

        // Simple query: Get only subordinates' leads stats
        $subordinateQuery = SalesRecord::whereHas('user', function($q) use ($userId) {
            $q->where('is_manager', $userId); // Only leads created by subordinates
        });

        $stats = [
            'total_leads' => $subordinateQuery->count(),
            
            'leads_this_month' => $subordinateQuery->whereMonth('createdat', Carbon::now()->month)
                ->whereYear('createdat', Carbon::now()->year)
                ->count(),
            
            'leads_this_week' => $subordinateQuery->whereBetween('createdat', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            
            'leads_today' => $subordinateQuery->whereDate('createdat', Carbon::today())
                ->count(),
            
            'status_distribution' => $subordinateQuery->with('status')
                ->get()
                ->groupBy('status.status_name')
                ->map(function($group) {
                    return $group->count();
                }),
            
            'follow_ups_due_today' => $subordinateQuery->whereDate('next_follow_up_date', Carbon::today())
                ->count(),
            
            'follow_ups_due_this_week' => $subordinateQuery->whereBetween('next_follow_up_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),

            'team_members' => User::where('is_manager', $userId)->count()
        ];

        return response()->json($stats);
    }

    // Export team leads
    public function exportTeamLeads(Request $request)
    {
        $userId = $this->getCurrentUserId();

        // Get all subordinates (team members) of the current user
        $subordinateIds = User::where('is_manager', $userId)
            ->pluck('id');

        // If no subordinates, return empty result
        if ($subordinateIds->isEmpty()) {
            return response()->json([]);
        }

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
        ->whereIn('user_id', $subordinateIds);

        // Apply same filters as filterTeamLeads method
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('createdat', 'desc')->get();

        return response()->json($leads);
    }

    // Reassign lead to different team member
    public function reassignLead(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $leadId = $request->lead_id;
        $newUserId = $request->new_user_id;

        // Verify the current user is a manager
        $subordinateIds = User::where('is_manager', $userId)
            ->pluck('id');

        if ($subordinateIds->isEmpty()) {
            return response()->json(['error' => 'You are not authorized to reassign leads'], 403);
        }

        // Verify the new user is a subordinate
        if (!$subordinateIds->contains($newUserId)) {
            return response()->json(['error' => 'Invalid team member selected'], 403);
        }

        // Find and update the lead
        $lead = SalesRecord::where('id', $leadId)
            ->whereIn('user_id', $subordinateIds)
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

    // Get team members for reassignment dropdowns
    public function getTeamMembers()
    {
        $userId = $this->getCurrentUserId();

        $teamMembers = User::where('is_manager', $userId)
            ->where('id', '!=', $userId) // Exclude self
            ->select('id', 'name')
            ->get();

        return response()->json($teamMembers);
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
