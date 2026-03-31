<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Models\Prospectus;
use App\Models\SalesRecord;
use App\Models\Remark;
use App\Models\User;
use App\Mail\IndiaMartLeadNotification as IndiaMartLeadMail;
use App\Notifications\IndiaMartLeadNotification;
use App\Services\WhatsAppService;

class IndiaMartLeadsController extends Controller
{
    public function index()
    {
        return view('indiamart.index');
    }

    public function junkIndex()
    {
        return view('indiamart.junk');
    }

    public function fetch(Request $request)
    {
        $perPage = (int)($request->input('per_page', 10));
        $query = DB::table('indiamartleads')->where('is_processed', 0);

        // Search across key fields
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where('sender_name', 'like', $like)
                  ->orWhere('sender_mobile', 'like', $like)
                  ->orWhere('sender_email', 'like', $like)
                  ->orWhere('sender_company', 'like', $like)
                  ->orWhere('query_product_name', 'like', $like)
                  ->orWhere('query_message', 'like', $like)
                  ->orWhere('unique_query_id', 'like', $like);
            });
        }

        // Status filter (exclude junk by default)
        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'junk');
        }

        // Query type filter
        if ($queryType = $request->input('query_type')) {
            $query->where('query_type', $queryType);
        }

        // Date range on query_time (UTC)
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('query_time', [ $dateFrom . ' 00:00:00', $dateTo . ' 23:59:59' ]);
        } elseif ($dateFrom) {
            $query->where('query_time', '>=', $dateFrom . ' 00:00:00');
        } elseif ($dateTo) {
            $query->where('query_time', '<=', $dateTo . ' 23:59:59');
        }

        // Sort latest first
        $query->orderByDesc('query_time')->orderByDesc('id');

        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function junkFetch(Request $request)
    {
        $perPage = (int)($request->input('per_page', 10));
        $query = DB::table('indiamartleads')->whereRaw('LOWER(status) = ?', ['junk']);

        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where('sender_name', 'like', $like)
                  ->orWhere('sender_mobile', 'like', $like)
                  ->orWhere('sender_email', 'like', $like)
                  ->orWhere('sender_company', 'like', $like)
                  ->orWhere('query_product_name', 'like', $like)
                  ->orWhere('query_message', 'like', $like)
                  ->orWhere('unique_query_id', 'like', $like);
            });
        }

        $query->orderByDesc('query_time')->orderByDesc('id');
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]);
    }
    
    public function filterOptions()
    {
        // Distinct statuses
        $statuses = DB::table('indiamartleads')
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        // Distinct query types
        $queryTypes = DB::table('indiamartleads')
            ->select('query_type')
            ->whereNotNull('query_type')
            ->distinct()
            ->orderBy('query_type')
            ->pluck('query_type');

        // Date range
        $minDate = DB::table('indiamartleads')->min('query_time');
        $maxDate = DB::table('indiamartleads')->max('query_time');

        return response()->json([
            'statuses' => array_values($statuses->toArray()),
            'query_types' => array_values($queryTypes->toArray()),
            'date_min' => $minDate,
            'date_max' => $maxDate,
        ]);
    }

    public function summaryStats()
    {
        $table = DB::table('indiamartleads');

        $newLeads = (clone $table)
            ->where(function ($query) {
                $query->whereNull('status')
                      ->orWhereRaw("TRIM(status) = ''")
                      ->orWhereRaw("LOWER(status) = 'new'");
            })
            ->count();

        $processingLeads = (clone $table)
            ->whereRaw("LOWER(status) = 'processing'")
            ->count();

        $convertedLeads = (clone $table)
            ->whereRaw("LOWER(status) = 'converted'")
            ->count();

        $assignedLeads = (clone $table)
            ->where(function ($query) {
                $query->whereNotNull('sales_record_id')
                      ->orWhere('is_processed', 1);
            })
            ->count();

        $junkLeads = (clone $table)
            ->whereRaw("LOWER(status) = 'junk'")
            ->count();

        return response()->json([
            'new_leads' => $newLeads,
            'processing_leads' => $processingLeads,
            'converted_leads' => $convertedLeads,
            'assigned_leads' => $assignedLeads,
            'junk_leads' => $junkLeads,
        ]);
    }

    public function statusCounts()
    {
        $statusCounts = DB::table('indiamartleads')
            ->selectRaw("(CASE WHEN status IS NULL OR TRIM(status) = '' THEN 'new' ELSE status END) as status_name, COUNT(*) as count")
            ->groupBy(DB::raw("(CASE WHEN status IS NULL OR TRIM(status) = '' THEN 'new' ELSE status END)"))
            ->orderBy('status_name')
            ->get();

        return response()->json($statusCounts);
    }
    
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $lead = DB::table('indiamartleads')->where('id', $validated['lead_id'])->first();
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        if (!empty($lead->is_processed)) {
            return response()->json(['success' => false, 'message' => 'Lead already processed'], 422);
        }

        // Prevent assigning junk leads
        if (isset($lead->status) && $lead->status === 'junk') {
            return response()->json(['success' => false, 'message' => 'Cannot assign a junk lead'], 422);
        }

        try {
            DB::beginTransaction();

            $commonName = $lead->sender_company ?: ($lead->subject ?: ($lead->sender_name ?: 'IndiaMART Lead'));

            $prospectus = Prospectus::create([
                'prospectus_name' => $commonName,
                'contact_person' => $lead->sender_name ?? '',
                'contact_number' => $lead->sender_mobile ?? '',
                'address' => $lead->sender_address ?? '',
                'state_id' => null,
                'city_id' => null,
                'email' => $lead->sender_email ?? '',
                'website_link' => null,
                'business_type_id' => null,
            ]);

            // Look up IDs using correct column names with safe fallbacks
            // Set status to Interested
            $statusId = DB::table('sales_status')->where('status_name', 'Interested')->value('id');
            if (!$statusId) {
                $statusId = DB::table('sales_status')->orderBy('id')->value('id');
            }

            $leadSourceId = DB::table('sales_lead_sources')->where('source_name', 'IndiaMART')->value('id');
            if (!$leadSourceId) {
                $leadSourceId = DB::table('sales_lead_sources')->orderBy('id')->value('id');
            }

            $productName = $lead->query_product_name ?? $lead->product_name ?? null;
            $productId = null;
            if ($productName) {
                $productId = DB::table('sales_products')->where('product_name', $productName)->value('id');
                if (!$productId) {
                    $productId = DB::table('sales_products')->where('product_name', 'like', '%' . $productName . '%')->value('id');
                }
            }

            $salesRecord = SalesRecord::create([
                'user_id' => (int)$validated['user_id'],
                'leads_name' => $commonName,
                'contact_person' => $lead->sender_name ?? '',
                'contact_number' => $lead->sender_mobile ?? '',
                'address' => $lead->sender_address ?? ($lead->sender_city ?? ''),
                'state_id' => null,
                'city_id' => null,
                'email' => $lead->sender_email ?? '',
                'business_type_id' => null,
                'lead_source_id' => $leadSourceId,
                'status_id' => $statusId,
                'next_follow_up_date' => now()->toDateString(),
                'products_id' => $productId,
                'prospectus_id' => $prospectus->id,
                'createdat' => now()->toDateString(),
                'website_link' => null,
            ]);

            // Create initial remark from query message and new fields
            $remarkParts = [];
            if ($lead->query_message) $remarkParts[] = "Query: " . $lead->query_message;
            if (!empty($lead->no_of_employees)) $remarkParts[] = "No of Employees: " . $lead->no_of_employees;
            if ($lead->remarks) $remarkParts[] = "Lead Remarks: " . $lead->remarks;
            
            $remarkText = implode("\n", $remarkParts);
            
            if (!empty($remarkText)) {
                Remark::create([
                    'remark_date' => now()->toDateString(),
                    'remark' => $remarkText,
                    'sales_remark_id' => $salesRecord->id,
                ]);
            }

            DB::table('indiamartleads')
                ->where('id', $lead->id)
                ->update([
                    'status' => 'converted',
                    'is_processed' => 1,
                    'sales_record_id' => $salesRecord->id,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead assigned successfully',
                'sales_record_id' => $salesRecord->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning lead: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function junk(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer',
            'junk_reason' => 'required|string|max:2000',
        ]);

        $lead = DB::table('indiamartleads')->where('id', $validated['lead_id'])->first();
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        // Prevent junking assigned/processed leads
        if (!empty($lead->is_processed) || !empty($lead->sales_record_id)) {
            return response()->json(['success' => false, 'message' => 'Assigned leads cannot be marked as junk'], 422);
        }

        DB::table('indiamartleads')->where('id', $lead->id)->update([
            'status' => 'junk',
            'junk_reason' => $validated['junk_reason'],
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function junkDelete(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer',
        ]);

        $lead = DB::table('indiamartleads')->where('id', $validated['lead_id'])->first();
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        // Only allow permanent delete for junk leads (case-insensitive check)
        if (strtolower((string)$lead->status) !== 'junk') {
            return response()->json(['success' => false, 'message' => 'Only junk leads can be deleted'], 422);
        }

        $deleted = DB::table('indiamartleads')->where('id', (int)$lead->id)->delete();
        if ($deleted > 0) {
            return response()->json(['success' => true, 'deleted' => $deleted]);
        }
        return response()->json(['success' => false, 'message' => 'Delete failed: record not found or already removed'], 404);
    }

    public function junkRestore(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer',
        ]);

        $lead = DB::table('indiamartleads')->where('id', $validated['lead_id'])->first();
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead not found'], 404);
        }

        // Only allow restore for junk leads
        if (strtolower((string)$lead->status) !== 'junk') {
            return response()->json(['success' => false, 'message' => 'Only junk leads can be restored'], 422);
        }

        // Restore to new and clear junk_reason
        DB::table('indiamartleads')->where('id', $lead->id)->update([
            'status' => 'new',
            'junk_reason' => null,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $leadIds = $validated['lead_ids'];
        $userId = $validated['user_id'];

        // Validate all leads exist and are assignable
        $leads = DB::table('indiamartleads')->whereIn('id', $leadIds)->get();
        if ($leads->count() !== count($leadIds)) {
            return response()->json(['success' => false, 'message' => 'One or more leads not found'], 404);
        }

        // Check for already processed or junk leads
        $invalidLeads = $leads->filter(function ($lead) {
            return !empty($lead->is_processed) || $lead->status === 'junk';
        });

        if ($invalidLeads->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot assign already processed or junk leads'
            ], 422);
        }

        $successCount = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            foreach ($leads as $lead) {
                try {
                    $commonName = $lead->sender_company ?: ($lead->subject ?: ($lead->sender_name ?: 'IndiaMART Lead'));

                    $prospectus = Prospectus::create([
                        'prospectus_name' => $commonName,
                        'contact_person' => $lead->sender_name ?? '',
                        'contact_number' => $lead->sender_mobile ?? '',
                        'address' => $lead->sender_address ?? '',
                        'state_id' => null,
                        'city_id' => null,
                        'email' => $lead->sender_email ?? '',
                        'website_link' => null,
                        'business_type_id' => null,
                    ]);

                    // Look up IDs using correct column names with safe fallbacks
                    $statusId = DB::table('sales_status')->where('status_name', 'Interested')->value('id');
                    if (!$statusId) {
                        $statusId = DB::table('sales_status')->orderBy('id')->value('id');
                    }

                    $leadSourceId = DB::table('sales_lead_sources')->where('source_name', 'IndiaMART')->value('id');
                    if (!$leadSourceId) {
                        $leadSourceId = DB::table('sales_lead_sources')->orderBy('id')->value('id');
                    }

                    $productName = $lead->query_product_name ?? $lead->product_name ?? null;
                    $productId = null;
                    if ($productName) {
                        $productId = DB::table('sales_products')->where('product_name', $productName)->value('id');
                        if (!$productId) {
                            $productId = DB::table('sales_products')->where('product_name', 'like', '%' . $productName . '%')->value('id');
                        }
                    }

                    $salesRecord = SalesRecord::create([
                        'user_id' => (int)$userId,
                        'leads_name' => $commonName,
                        'contact_person' => $lead->sender_name ?? '',
                        'contact_number' => $lead->sender_mobile ?? '',
                        'address' => $lead->sender_address ?? ($lead->sender_city ?? ''),
                        'state_id' => null,
                        'city_id' => null,
                        'email' => $lead->sender_email ?? '',
                        'business_type_id' => null,
                        'lead_source_id' => $leadSourceId,
                        'status_id' => $statusId,
                        'next_follow_up_date' => now()->toDateString(),
                        'products_id' => $productId,
                        'prospectus_id' => $prospectus->id,
                        'createdat' => now()->toDateString(),
                        'website_link' => null,
                    ]);

                    // Create initial remark from query message and new fields
                    $remarkParts = [];
                    if ($lead->query_message) $remarkParts[] = "Query: " . $lead->query_message;
                    if (!empty($lead->no_of_employees)) $remarkParts[] = "No of Employees: " . $lead->no_of_employees;
                    if ($lead->remarks) $remarkParts[] = "Lead Remarks: " . $lead->remarks;
                    
                    $remarkText = implode("\n", $remarkParts);
                    
                    if (!empty($remarkText)) {
                        Remark::create([
                            'remark_date' => now()->toDateString(),
                            'remark' => $remarkText,
                            'sales_remark_id' => $salesRecord->id,
                        ]);
                    }

                    DB::table('indiamartleads')
                        ->where('id', $lead->id)
                        ->update([
                            'status' => 'converted',
                            'is_processed' => 1,
                            'sales_record_id' => $salesRecord->id,
                            'updated_at' => now(),
                        ]);

                    $successCount++;
                } catch (\Throwable $e) {
                    $errors[] = "Lead ID {$lead->id}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$successCount} leads",
                'assigned_count' => $successCount,
                'errors' => $errors
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk assignment: ' . $e->getMessage(),
                'errors' => $errors
            ], 500);
        }
    }

    public function bulkJunk(Request $request)
    {
        $validated = $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer',
        ]);

        $leadIds = $validated['lead_ids'];

        // Validate all leads exist
        $leads = DB::table('indiamartleads')->whereIn('id', $leadIds)->get();
        if ($leads->count() !== count($leadIds)) {
            return response()->json(['success' => false, 'message' => 'One or more leads not found'], 404);
        }

        // Check for already processed leads
        $invalidLeads = $leads->filter(function ($lead) {
            return !empty($lead->is_processed) || !empty($lead->sales_record_id);
        });

        if ($invalidLeads->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot mark assigned leads as junk'
            ], 422);
        }

        try {
            $updated = DB::table('indiamartleads')
                ->whereIn('id', $leadIds)
                ->update([
                    'status' => 'junk',
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully marked {$updated} leads as junk",
                'junked_count' => $updated
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking leads as junk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email notification to sales users about new IndiaMART lead
     * This method is called by the webhook script
     */
    public function notifySalesUsers(Request $request)
    {
        try {
            $leadData = $request->all();
            
            // Validate that we have at least a unique_query_id
            if (empty($leadData['unique_query_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid lead data'
                ], 400);
            }

            // Get all sales users (is_sales = 1) with valid email
            $salesUsers = User::whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->where('is_sales', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            if ($salesUsers->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No sales users to notify',
                    'notified' => 0
                ]);
            }

            $notifiedCount = 0;

            // Send email and in-app notification to each sales user
            foreach ($salesUsers as $user) {
                try {
                    // Send email
                    Mail::to($user->email)->send(new IndiaMartLeadMail($leadData));
                    
                    // Send in-app notification
                    $user->notify(new IndiaMartLeadNotification($leadData));
                    
                    $notifiedCount++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send notification to ' . $user->email . ': ' . $e->getMessage());
                }
            }

            // Send WhatsApp message to the lead (not to sales users, to the customer who inquired)
            try {
                $whatsappService = new WhatsAppService();
                $whatsappService->sendLeadGreeting($leadData);
            } catch (\Exception $e) {
                \Log::error('Failed to send WhatsApp message to lead: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifications sent',
                'notified' => $notifiedCount,
                'total_users' => $salesUsers->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('IndiaMART notification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending notifications: ' . $e->getMessage()
            ], 500);
        }
    }


    public function storeFollowup(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|integer|exists:indiamartleads,id',
            'comment' => 'required|string',
        ]);

        try {
            $followup = \App\Models\ExternalLeadFollowup::create([
                'lead_id' => $validated['lead_id'],
                'comment' => $validated['comment'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Follow-up added successfully',
                'data' => $followup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding follow-up: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFollowups($leadId)
    {
        $followups = \App\Models\ExternalLeadFollowup::where('lead_id', $leadId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $followups
        ]);
    }
}

