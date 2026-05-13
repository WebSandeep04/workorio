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

use Illuminate\Support\Facades\DB;

class LeadApiController extends Controller
{
    /**
     * Get user's leads with filtering and pagination
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->get('per_page', 10);
        $today = Carbon::today()->toDateString(); // For filters

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy'
        ])
        ->where('user_id', $userId);

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('filter_type')) {
            $filter = $request->filter_type;
            // Exclude closed statuses logic (common to these filters in MyLeadsController)
            $excludedStatuses = [1, 2, 15, 20]; // Adjust as per your business logic

            switch ($filter) {
                case 'today_followups':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhere(function ($sub) use ($today) {
                              $sub->whereDate('next_follow_up_date', '>', $today)
                                  ->whereDate('updatedat', $today);
                          });
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;
                
                case 'under_process':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_completed':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', '>', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_pending':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhereNull('next_follow_up_date');
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_new':
                    $query->whereDate('createdat', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;
            }
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
     * Get summary statistics for dashboard cards
     */
    public function getSummaryStats()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        $excludedStatuses = [1, 2, 15, 20];

        $stats = [
            'today_followups' => SalesRecord::where('user_id', $userId)
                ->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereDate('next_follow_up_date', '>', $today)
                              ->whereDate('updatedat', $today);
                      });
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'under_process' => SalesRecord::where('user_id', $userId)
                ->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_completed' => SalesRecord::where('user_id', $userId)
                ->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', '>', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_pending' => SalesRecord::where('user_id', $userId)
                ->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_new' => SalesRecord::where('user_id', $userId)
                ->whereDate('createdat', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Get status counts for dashboard
     */
    public function getStatusCounts()
    {
        $userId = Auth::id();

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

        return response()->json(['success' => true, 'data' => $statusCounts]);
    }

    /**
     * Get single lead details
     */
    public function show($id)
    {
        $userId = Auth::id();
        
        $lead = SalesRecord::with([
            'status',
            'prospectus',
            'prospectus.state',
            'prospectus.city', 
            'prospectus.businessType',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy',
            'remarks' => function($query) {
                $query->orderBy('remark_date', 'desc');
            }
        ])
        ->find($id);

        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found or access denied'], 404);
        }

        return response()->json(['success' => true, 'data' => $lead]);
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

        // --- Start Email Notification ---
        $creator = \App\Models\User::find($salesRecord->user_id);

        $recipientEmails = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })
            ->where('is_sales', 1)
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();
            
        if ($creator && $creator->email && !in_array($creator->email, $recipientEmails)) {
            $recipientEmails[] = $creator->email;
        }

        $recipientEmails = array_filter($recipientEmails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if (!empty($recipientEmails)) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipientEmails)
                    ->send(new \App\Mail\NewLeadNotification($salesRecord, $creator, $remarkText));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send API new lead email: " . $e->getMessage());
            }
        }
        // --- End Email Notification ---

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
            'products' => SalesProduct::orderBy('product_name')->get(['id', 'product_name']),
            'users' => User::where('is_sales', 1)->orWhere('role_id', 2)->orderBy('name')->get(['id', 'name'])
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
        $teamMembers = User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->where('users.id', '!=', $userId) // Exclude self
            ->select('users.id', 'users.name')
            ->get();

        return response()->json($teamMembers);
    }

    /**
     * Get all leads with filtering and pagination (All Data)
     */
    public function allLeads(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $today = Carbon::today()->toDateString();

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy'
        ]);

        // Apply user filter if provided
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('filter_type')) {
            $filter = $request->filter_type;
            $excludedStatuses = [1, 2, 15, 20];

            switch ($filter) {
                case 'today_followups':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhere(function ($sub) use ($today) {
                              $sub->whereDate('next_follow_up_date', '>', $today)
                                  ->whereDate('updatedat', $today);
                          });
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;
                
                case 'under_process':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_completed':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', '>', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_pending':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhereNull('next_follow_up_date');
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_new':
                    $query->whereDate('createdat', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;
            }
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
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Support date filters for next_follow_up_date like Web App
        $fromDate = $request->date_from ?? $request->start_date;
        $toDate = $request->date_to ?? $request->end_date;

        if ($fromDate) {
            $query->whereDate('next_follow_up_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('next_follow_up_date', '<=', $toDate);
        }

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    /**
     * Get summary statistics for all leads
     */
    public function getAllSummaryStats()
    {
        $today = Carbon::today()->toDateString();
        $excludedStatuses = [1, 2, 15, 20];

        $stats = [
            'today_followups' => SalesRecord::where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereDate('next_follow_up_date', '>', $today)
                              ->whereDate('updatedat', $today);
                      });
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'under_process' => SalesRecord::whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_completed' => SalesRecord::whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', '>', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_pending' => SalesRecord::where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_new' => SalesRecord::whereDate('createdat', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Get status counts for all leads
     */
    public function getAllStatusCounts()
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

        return response()->json(['success' => true, 'data' => $statusCounts]);
    }

    /**
     * Get leads assigned by the current user to others (Assigned Leads)
     */
    public function assignedLeads(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->get('per_page', 10);
        $today = Carbon::today()->toDateString();

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy'
        ])
        ->whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
        })
        ->where('user_id', '!=', $userId);

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('filter_type')) {
            $filter = $request->filter_type;
            $excludedStatuses = [1, 2, 15, 20];

            switch ($filter) {
                case 'today_followups':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhere(function ($sub) use ($today) {
                              $sub->whereDate('next_follow_up_date', '>', $today)
                                  ->whereDate('updatedat', $today);
                          });
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;
                
                case 'under_process':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_completed':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', '>', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_pending':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhereNull('next_follow_up_date');
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_new':
                    $query->whereDate('createdat', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;
            }
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
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $fromDate = $request->date_from ?? $request->start_date;
        $toDate = $request->date_to ?? $request->end_date;

        if ($fromDate) {
            $query->whereDate('next_follow_up_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('next_follow_up_date', '<=', $toDate);
        }

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    /**
     * Get summary statistics for assigned leads
     */
    public function getAssignedSummaryStats()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        $excludedStatuses = [1, 2, 15, 20];

        $baseQuery = SalesRecord::whereHas('assignmentLogs', function($aq) use ($userId) {
                $aq->where('assigned_by', $userId);
            })
            ->where('user_id', '!=', $userId);

        $stats = [
            'today_followups' => (clone $baseQuery)->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereDate('next_follow_up_date', '>', $today)
                              ->whereDate('updatedat', $today);
                      });
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'under_process' => (clone $baseQuery)->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_completed' => (clone $baseQuery)->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', '>', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_pending' => (clone $baseQuery)->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_new' => (clone $baseQuery)->whereDate('createdat', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Get status counts for assigned leads
     */
    public function getAssignedStatusCounts()
    {
        $userId = Auth::id();

        $statusCounts = DB::table('sales_status')
            ->leftJoin('sales_records', function ($join) use ($userId) {
                $join->on('sales_status.id', '=', 'sales_records.status_id')
                     ->whereIn('sales_records.id', function($q) use ($userId) {
                         $q->select('sales_record_id')
                           ->from('lead_assignment_logs')
                           ->where('assigned_by', $userId);
                     })
                     ->where('sales_records.user_id', '!=', $userId);
            })
            ->select(
                'sales_status.id',
                'sales_status.status_name',
                DB::raw('COUNT(sales_records.id) as count')
            )
            ->groupBy('sales_status.id', 'sales_status.status_name')
            ->orderBy('sales_status.status_name')
            ->get();

        return response()->json(['success' => true, 'data' => $statusCounts]);
    }

    /**
     * Get leads belonging to subordinates of the current manager (Team Leads)
     */
    public function teamLeads(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->get('per_page', 10);
        $today = Carbon::today()->toDateString();

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy'
        ])
        ->whereHas('user.managers', function($q) use ($userId) {
            $q->where('manager_id', $userId); // Subordinates only
        });

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('filter_type')) {
            $filter = $request->filter_type;
            $excludedStatuses = [1, 2, 15, 20];

            switch ($filter) {
                case 'today_followups':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhere(function ($sub) use ($today) {
                              $sub->whereDate('next_follow_up_date', '>', $today)
                                  ->whereDate('updatedat', $today);
                          });
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;
                
                case 'under_process':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_completed':
                    $query->whereDate('updatedat', $today)
                          ->whereDate('next_follow_up_date', '>', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_pending':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('next_follow_up_date', '<=', $today)
                          ->orWhereNull('next_follow_up_date');
                    })->whereNotIn('status_id', $excludedStatuses);
                    break;

                case 'today_new':
                    $query->whereDate('createdat', $today)
                          ->whereNotIn('status_id', $excludedStatuses);
                    break;
            }
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
                  })
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $fromDate = $request->date_from ?? $request->start_date;
        $toDate = $request->date_to ?? $request->end_date;

        if ($fromDate) {
            $query->whereDate('next_follow_up_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('next_follow_up_date', '<=', $toDate);
        }

        $records = $query->orderBy('createdat', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    /**
     * Get summary statistics for team leads
     */
    public function getTeamSummaryStats()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        $excludedStatuses = [1, 2, 15, 20];

        $baseQuery = SalesRecord::whereHas('user.managers', function($q) use ($userId) {
            $q->where('manager_id', $userId);
        });

        $stats = [
            'today_followups' => (clone $baseQuery)->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhere(function ($sub) use ($today) {
                          $sub->whereDate('next_follow_up_date', '>', $today)
                              ->whereDate('updatedat', $today);
                      });
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'under_process' => (clone $baseQuery)->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_completed' => (clone $baseQuery)->whereDate('updatedat', $today)
                ->whereDate('next_follow_up_date', '>', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_pending' => (clone $baseQuery)->where(function ($q) use ($today) {
                    $q->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
                })->whereNotIn('status_id', $excludedStatuses)->count(),

            'today_new' => (clone $baseQuery)->whereDate('createdat', $today)
                ->whereNotIn('status_id', $excludedStatuses)->count()
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }

    /**
     * Get status counts for team leads
     */
    public function getTeamStatusCounts()
    {
        $userId = Auth::id();

        $statusCounts = DB::table('sales_status')
            ->leftJoin('sales_records', function ($join) use ($userId) {
                $join->on('sales_status.id', '=', 'sales_records.status_id')
                     ->whereIn('sales_records.user_id', function($q) use ($userId) {
                         $q->select('user_id')
                           ->from('user_managers')
                           ->where('manager_id', $userId);
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

        return response()->json(['success' => true, 'data' => $statusCounts]);
    }

    /**
     * Get follow-up leads for current user (next follow up <= today)
     */
    public function followupLeads(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->get('per_page', 10);
        $today = Carbon::today()->toDateString();

        $query = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product',
            'latestRemark',
            'user',
            'creatorLog.assignedBy'
        ])
        ->where('user_id', $userId)
        ->whereDate('next_follow_up_date', '<=', $today)
        ->whereNotIn('status_id', [1, 2, 15, 20]);

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

        $records = $query->orderBy('next_follow_up_date', 'asc')->paginate($perPage);

        return response()->json($records);
    }
}
