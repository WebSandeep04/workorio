<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesRecord;
use App\Models\Remark;
use App\Models\SalesStatus;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use App\Models\SalesBusinessType;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeadApiController extends Controller
{
    /**
     * Get user's leads with filtering and pagination
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
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

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    /**
     * Store a new lead
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'prospectus_id' => 'required|integer',
            'leads_name' => 'nullable|string',
            'contact_person' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'status_id' => 'required',
            'address' => 'nullable|string',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'email' => 'nullable|email',
            'next_follow_up_date' => 'required|date',
            'business_type_id' => 'nullable|integer',
            'remark' => 'required|string',
            'website_link' => 'nullable|string',
            'lead_source_id' => 'nullable',
            'products_id' => 'nullable'
        ]);

        // Set additional fields
        $validated['user_id'] = Auth::id();
        $validated['createdat'] = now();    

        // Extract remark before saving SalesRecord
        $remarkText = $validated['remark'] ?? null;
        unset($validated['remark']);

        // Save sales record
        $salesRecord = SalesRecord::create($validated);

        // Save remark in 'remarks' table
        if ($remarkText) {
            Remark::create([
                'remark_date' => now()->toDateString(),
                'remark' => $remarkText,
                'sales_remark_id' => $salesRecord->id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $salesRecord
        ], 201);
    }

    /**
     * Reassign a lead to another user
     */
    public function assign(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:sales_records,id',
            'new_user_id' => 'required|exists:users,id'
        ]);

        $userId = Auth::id();
        $leadId = $request->lead_id;
        $newUserId = $request->new_user_id;

        // Find and update the lead (must belong to current user)
        $lead = SalesRecord::where('id', $leadId)
            ->where('user_id', $userId)
            ->first();

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found or you do not have permission to reassign it'
            ], 404);
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

    /**
     * Get filter options for dropdowns
     */
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
    
    /**
     * Get cities by state ID
     */
    public function getCitiesByState($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('city_name')
            ->get(['id', 'city_name']);

        return response()->json($cities);
    }

    /**
     * Get potential team members for reassignment
     */
    public function getTeamMembers()
    {
        $userId = Auth::id();

        // Fetch users managed by the current user (if any logic exists), or all users in tenant
        // Based on MyLeadsController, it seems to filter by is_manager column
        $teamMembers = User::where('is_manager', $userId)
            ->where('id', '!=', $userId) // Exclude self
            ->select('id', 'name')
            ->get();

        return response()->json($teamMembers);
    }
}
