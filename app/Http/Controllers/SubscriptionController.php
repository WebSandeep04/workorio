<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\SalesRecord;
use App\Models\State;
use App\Models\City;
use App\Models\SalesBusinessType;
use App\Models\SalesLeadSource;
use App\Models\SalesProduct;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('is_subscription_on', 1);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Apply subscription filters to group view
        if ($request->filled('customer_id')) {
            $query->where('id', $request->customer_id);
        }

        if ($request->filled('products_id') || $request->filled('status') || $request->filled('is_recurring') || $request->filled('recurrence_type') || $request->filled('is_active')) {
            $query->whereHas('subscriptions', function($q) use ($request) {
                if ($request->filled('status')) {
                    $status = $request->status;
                    $q->where(function($subQ) use ($status) {
                        $subQ->where('status', $status)
                             ->orWhereHas('histories', function($hq) use ($status) {
                                 $hq->where('status', $status);
                             });
                    });
                }
                if ($request->filled('products_id')) {
                    $q->where('product_id', $request->products_id);
                }
                if ($request->filled('is_recurring')) {
                    $q->where('is_recurring', $request->is_recurring);
                }
                if ($request->filled('recurrence_type')) {
                    $q->where('recurrence_type', $request->recurrence_type);
                }
                if ($request->filled('is_active')) {
                    $q->where('is_active', $request->is_active);
                }
            });
        }

        $customers = $query->select('id', 'name', 'email', 'phone', 'company_name')
            ->withCount('subscriptions')
            ->paginate(10);

        // Summary Stats
        $totalCustomers = Customer::where('is_subscription_on', 1)->count();
        $totalSubscriptions = Subscription::count();
        
        // Count subscriptions due in next 15 days
        // We look at the latest history record for each subscription to see if it's due soon
        // Or simpler: any history record with due_date in [today, today+15] that is 'pending' or similar?
        // Let's assume 'status' !='Payment Received' logic similar to other queries, but simpler:
        // Join subscriptions to check user_id/ownership if needed, but for "all" stats:
        $comingDueCount = DB::table('subscription_histories')
            ->whereBetween('due_date', [Carbon::now(), Carbon::now()->addDays(15)])
            ->where('status', '!=', 'Payment Received') // Adjust based on your status logic
            ->count();
            
        // Count OVERDUE subscriptions (due_date < today)
        $overDueCount = DB::table('subscription_histories')
            ->where('due_date', '<', Carbon::now())
            ->where('status', '!=', 'Payment Received') 
            ->count();

        if ($request->ajax()) {
            return response()->json($customers);
        }
            
        return view('subscription.index', compact('customers', 'totalCustomers', 'totalSubscriptions', 'comingDueCount', 'overDueCount'));
    }

    public function getEmailViewData(Request $request)
    {
        // Add DB facade if needed (already imported?)
        // Let's assume DB is imported. If not, we use \Illuminate\Support\Facades\DB
        
        $sql = "
            SELECT 
                s.id,
                s.subscription_name,
                s.amount,
                s.recurrence_type,
                s.billing_type,
                s.created_at as sub_created_at,
                c.name AS customer_name, 
                c.company_name, 
                c.phone,
                c.email,
                p.product_name,
                h.period_start,
                h.period_end,
                h.due_date,
                h.status AS history_status,
                h.id AS history_id,
                h.amount AS history_amount,
                h.created_at AS history_created_at,
                h.updated_at AS history_updated_at
            FROM subscriptions s 
            LEFT JOIN customers c ON s.customer_id = c.id 
            LEFT JOIN sales_products p ON s.product_id = p.id 
            LEFT JOIN subscription_histories h ON s.id = h.subscription_id
            WHERE s.is_active = 1 
            ORDER BY c.name ASC, h.due_date DESC
        ";

        $results = \Illuminate\Support\Facades\DB::select($sql);

        $overdueItems = [];
        $statusGroups = []; 
        $totalActive = 0;
        $totalReceivable = 0;
        $totalPendingAmount = 0;
        $totalInvoiceSentAmount = 0;
        $today = \Carbon\Carbon::today('Asia/Kolkata')->format('Y-m-d');

        if (!empty($results)) {
            foreach ($results as $row) {
                $row = (array) $row;
                $totalActive++;
                
                $statusRaw = $row['history_status'] ?? 'Pending';
                $statusKey = ucwords(strtolower($statusRaw));
                $dueDate = $row['due_date'];
                
                if (in_array(strtolower($statusRaw), ['payment received', 'last payment received'])) {
                    continue;
                }

                $item = [
                    'id' => $row['id'],
                    'history_id' => $row['history_id'],
                    'customer' => $row['customer_name'] ?: ($row['company_name'] ?: 'Unknown'),
                    'product' => $row['product_name'] ?: ($row['subscription_name'] ?: 'Sub #' . $row['id']),
                    'amount' => (float)$row['amount'],
                    'due_date' => $dueDate,
                    'status' => $statusKey, 
                    'recurrence' => ucfirst($row['recurrence_type'] ?? 'One Time'),
                    'billing' => ucfirst($row['billing_type'] ?? '-'),
                ];

                $isPaid = in_array(strtolower($statusKey), ['paid', 'payment received', 'completed']);

                if (!$isPaid) {
                    if (strpos(strtolower($statusKey), 'pending') !== false) {
                        $totalPendingAmount += $item['amount'];
                    } elseif (strpos(strtolower($statusKey), 'invoice') !== false || strpos(strtolower($statusKey), 'sent') !== false) {
                        $totalInvoiceSentAmount += $item['amount'];
                    }
                }
                $isOverdue = (!$isPaid && $dueDate && $dueDate < $today);

                if ($isOverdue) {
                    $daysOver = \Carbon\Carbon::parse($dueDate)->diffInDays(\Carbon\Carbon::parse($today));
                    $item['notes'] = floor($daysOver) . " days overdue";
                    $overdueItems[] = $item;
                    $totalReceivable += $item['amount'];
                } else {
                    if (!$isPaid) {
                         $totalReceivable += $item['amount'];
                         if ($dueDate) {
                             if (\Carbon\Carbon::parse($dueDate)->greaterThanOrEqualTo(\Carbon\Carbon::parse($today))) {
                                $daysLeft = \Carbon\Carbon::parse($today)->diffInDays(\Carbon\Carbon::parse($dueDate));
                                if ($daysLeft >= 0 && $daysLeft <= 7) {
                                    $item['notes'] = "Due in " . ceil($daysLeft) . " days";
                                }
                             }
                         }
                    }
                    $statusGroups[$statusKey][] = $item;
                }
            }
        }
        
        ksort($statusGroups);

        $payload = [
            'overdueItems' => $overdueItems,
            'statusGroups' => $statusGroups,
            'totalActive' => $totalActive,
            'totalReceivable' => $totalReceivable,
            'totalPendingAmount' => $totalPendingAmount,
            'totalInvoiceSentAmount' => $totalInvoiceSentAmount,
            'dateDisplay' => \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y')
        ];

        return response()->json($payload);
    }

    public function customerSubscriptions($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        return view('subscription.customer_subscriptions', compact('customer'));
    }

    // Get subscriptions with pagination
    public function getSubscriptions(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'salesRecord.product:id,product_name',
            'user:id,name',
            'creator:id,name',
            'latestHistory:subscription_histories.id,subscription_histories.subscription_id,subscription_histories.due_date'
        ])
        ->withCount(['histories' => function ($query) {
            $query->where('status', '!=', 'Payment Received');
        }])
        ->where('user_id', $userId);

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')
        ->paginate($perPage);

        return response()->json($subscriptions);
    }

    // Get ALL subscriptions (for Individual View in Index)
    public function fetchAllSubscriptions(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $query = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'salesRecord.product:id,product_name',
            'user:id,name',
            'creator:id,name',
            'latestHistory:subscription_histories.id,subscription_histories.subscription_id,subscription_histories.due_date'
        ])
        ->withCount(['histories' => function ($query) {
            $query->where('status', '!=', 'Payment Received');
        }])
        ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subscription_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $subscriptions = $query->paginate($perPage);

        return response()->json($subscriptions);
    }

    public function show($id)
    {
        $subscription = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'salesRecord.product:id,product_name',
            'user:id,name',
            'creator:id,name'
        ])->findOrFail($id);

        return response()->json(['subscription' => $subscription]);
    }

    // Get filtered subscriptions
    public function filterSubscriptions(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $perPage = $request->get('per_page', 10);

        $query = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'salesRecord.product:id,product_name',
            'user:id,name',
            'creator:id,name',
            'latestHistory:subscription_histories.id,subscription_histories.subscription_id,subscription_histories.due_date'
        ])
        ->withCount(['histories' => function ($query) {
            $query->where('status', '!=', 'Payment Received');
        }]);

        // Filter by customer_id if provided
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(function($q) use ($status) {
                $q->where('status', $status)
                  ->orWhereHas('histories', function($hq) use ($status) {
                      $hq->where('status', $status);
                  });
            });
        }

        if ($request->filled('products_id')) {
            $query->where('product_id', $request->products_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhere('subscription_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // New subscription-specific filters
        if ($request->filled('is_recurring')) {
            $query->where('is_recurring', $request->is_recurring);
        }

        if ($request->filled('recurrence_type')) {
            $query->where('recurrence_type', $request->recurrence_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($subscriptions);
    }

    // Get filter options for dropdowns (with caching for better performance)
    public function getFilterOptions()
    {
        // Cache filter options for 1 hour as they don't change frequently
        $options = cache()->remember('subscription_filter_options', 3600, function() {
            return [
                'states' => State::orderBy('state_name')
                    ->get(['id', 'state_name']),
                
                'cities' => City::orderBy('city_name')
                    ->get(['id', 'city_name']),
                
                'business_types' => SalesBusinessType::orderBy('business_name')
                    ->get(['id', 'business_name']),
                
                'lead_sources' => SalesLeadSource::orderBy('source_name')
                    ->get(['id', 'source_name']),
                
                'products' => SalesProduct::orderBy('product_name')
                    ->get(['id', 'product_name']),
            ];
        });

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

    // Get customers for dropdown with their close-won product types (optimized to avoid N+1)
    public function getCustomers()
    {
        // Optimized: Fetch all customers with their close-won sales records in minimal queries
        $customers = Customer::select('id', 'name', 'email', 'phone', 'company_name', 'prospectus_id')
            ->orderBy('name')
            ->get();
        return response()->json($customers);
    }

    // Get all products
    public function getProducts()
    {
        $products = SalesProduct::orderBy('product_name')->get(['id', 'product_name']);
        return response()->json($products);
    }



    // Store subscription
    public function store(Request $request)
    {
        $validated = $request->validate($this->getValidationRules($request));

        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $subscription = Subscription::create($this->prepareSubscriptionData($request, $userId));

        // Update customer subscription status
        if ($subscription->customer_id) {
            Customer::where('id', $subscription->customer_id)->update(['is_subscription_on' => 1]);
        }

        // 1. Create initial history record
        // 1. Create history records (handle backdated start_date)
        $now = \Carbon\Carbon::now();
        $cycleStart = \Carbon\Carbon::parse($subscription->start_date);
        
        // If non-recurring, loop once. If recurring, loop until cycle start is in future (or generate at least one)
        // Actually, we want to generate all "due" periods up to Now.
        // If start_date is future, we generate just one (the first one).
        // If start_date is past, we generate multiple.
        
        $isFirst = true;

        do {
            // Calculate end date for this cycle
            $cycleEndDate = null;
            if ($subscription->is_recurring && $subscription->recurrence_type) {
                // Use the helper but pass the current cycleStart object (format it as string if helper expects string)
                $cycleEndDate = $this->calculateEndDate(
                    $cycleStart->format('Y-m-d'),
                    $subscription->recurrence_type,
                    $subscription->recurrence_interval ?? 1
                );
            } else {
                // Non-recurring: Start Date + 1 month? Or just null? 
                // Previously it was null if non-recurring logic wasn't hit?
                // Wait, logic above was: if recurring && type => calc. Else null.
                // For non-recurring, end_date might be null or specific?
                // Let's stick to existing: null if not recurring.
            }

            // Determine due date based on billing type
            $currentDueDate = $cycleEndDate;
            if ($subscription->billing_type === 'Prepaid') {
                $currentDueDate = $cycleStart->format('Y-m-d');
            }

            DB::table('subscription_histories')->insert([
                'subscription_id' => $subscription->id,
                'period_start' => $cycleStart->format('Y-m-d'),
                'period_end' => $cycleEndDate ? \Carbon\Carbon::parse($cycleEndDate)->subDay()->format('Y-m-d') : null,
                'due_date' => $currentDueDate,
                'amount' => $subscription->amount ?? 0,
                'status' => $subscription->status, // All get the initial status? Or 'Pending'? User didn't specify, sticking to created status.
                'created_at' => now(),
                'updated_at' => now(), 
            ]);

            if (!$subscription->is_recurring) {
                break; 
            }

            // Advance to next cycle
            // We can use the calculated end date as the next start date?
            // Strictly speaking, next start = current end.
            if ($cycleEndDate) {
                $cycleStart = \Carbon\Carbon::parse($cycleEndDate);
            } else {
                break; // Should not happen if recurring
            }
            
            $isFirst = false;

        } while ($cycleStart->lte($now));

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription->load([
                'customer:id,name,email,phone,company_name',
                'product:id,product_name',
                'salesRecord:id,leads_name,products_id',
                'salesRecord.product:id,product_name',
                'user:id,name',
                'creator:id,name'
            ])
        ], 201);
    }

    // Update subscription
    public function update(Request $request, $id)
    {
        $validated = $request->validate($this->getValidationRules($request, $id));

        $subscription = Subscription::findOrFail($id);
        $subscription->update($this->prepareSubscriptionData($request, $subscription->created_by, false));

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription->load([
                'customer:id,name,email,phone,company_name',
                'product:id,product_name',
                'salesRecord:id,leads_name,products_id',
                'salesRecord.product:id,product_name',
                'user:id,name',
                'creator:id,name'
            ])
        ]);
    }

    // Delete subscription
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully'
        ]);
    }

    /**
     * Get validation rules for subscription
     */
    private function getValidationRules(Request $request, $subscriptionId = null)
    {
        $rules = [
            // Either customer_id OR subscription_name must be present
            'customer_id' => 'nullable|exists:customers,id|required_without:subscription_name',
            // 'sales_record_id' => 'nullable|exists:sales_records,id', // OLD
            'product_id' => 'nullable|exists:sales_products,id',
            'subscription_name' => 'nullable|string|max:255|required_without:customer_id',
            'amount' => 'nullable|numeric|min:0',
            'billing_type' => 'nullable|in:Prepaid,Postpaid',
            'start_date' => 'required|date',
            // Status must exist in subscription_status master table
            'status' => 'required|string|max:255|exists:subscription_status,status_name',
            'is_recurring' => 'nullable|boolean',
            'recurrence_type' => 'nullable|required_if:is_recurring,true|in:daily,weekly,monthly,quarterly,half_yearly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|required_if:is_recurring,true',
            'alert_before_days' => 'nullable|integer|min:0',
            'recurrence_days_of_week' => 'nullable|array|required_if:recurrence_type,weekly',
            'recurrence_days_of_week.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            // Day of month is optional even for monthly recurrence (we can default it in UI/controller)
            'recurrence_day_of_month' => 'nullable|integer|min:1|max:31',
            'recurrence_months' => 'nullable|array',
            'recurrence_months.*' => 'integer|min:1|max:12',
            'recurrence_end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];

        return $rules;
    }

    /**
     * Prepare subscription data for create/update
     */
    private function prepareSubscriptionData(Request $request, $userId, $isNew = true)
    {
        $isRecurring = $request->boolean('is_recurring');
        
        $data = [
            'customer_id' => $request->customer_id,
            'sales_record_id' => $request->sales_record_id, // Keep if provided
            'product_id' => $request->product_id,
            'user_id' => $request->user_id ?? $userId,
            'subscription_name' => $request->subscription_name ?? null,
            'amount' => $request->amount,
            'billing_type' => $request->billing_type,
            'start_date' => $request->start_date,
            'status' => $request->status ?? 'pending',
            'notes' => $request->notes,
            'is_active' => $request->boolean('is_active', true), // Default to true if not provided
        ];

        // Recurrence fields (only if recurring)
        if ($isRecurring) {
            $data['is_recurring'] = true;
            $data['recurrence_type'] = $request->recurrence_type;
            $data['recurrence_interval'] = $request->recurrence_interval ?? 1;
            $data['alert_before_days'] = $request->alert_before_days;
            $data['recurrence_days_of_week'] = $request->recurrence_type === 'weekly' ? $request->recurrence_days_of_week : null;
            $data['recurrence_day_of_month'] = $request->recurrence_type === 'monthly' ? $request->recurrence_day_of_month : null;
            $data['recurrence_months'] = $request->recurrence_type === 'yearly' ? $request->recurrence_months : null;
            $data['recurrence_end_date'] = $request->recurrence_end_date;
            
            // end_date is now calculated only when status changes to "payment received"
        } else {
            $data['is_recurring'] = false;
            $data['recurrence_type'] = null;
            $data['recurrence_interval'] = null;
            $data['alert_before_days'] = null;
            $data['recurrence_days_of_week'] = null;
            $data['recurrence_day_of_month'] = null;
            $data['recurrence_months'] = null;
            $data['recurrence_end_date'] = null;
        }

        if ($isNew) {
            $data['created_by'] = $userId;
        }

        return $data;
    }

    /**
     * Calculate end date based on start date, recurrence type, and interval
     */
    private function calculateEndDate($startDate, $recurrenceType, $interval)
    {
        if (!$startDate || !$recurrenceType || !$interval) {
            return null;
        }

        $start = \Carbon\Carbon::parse($startDate);
        $interval = (int) $interval;

        switch ($recurrenceType) {
            case 'daily':
                return $start->copy()->addDays($interval)->format('Y-m-d');
            case 'weekly':
                return $start->copy()->addWeeks($interval)->format('Y-m-d');
            case 'monthly':
                return $start->copy()->addMonths($interval)->format('Y-m-d');
            case 'quarterly':
                return $start->copy()->addMonths(3 * $interval)->format('Y-m-d');
            case 'half_yearly':
                return $start->copy()->addMonths(6 * $interval)->format('Y-m-d');
            case 'yearly':
                return $start->copy()->addYears($interval)->format('Y-m-d');
            default:
                return null;
        }
    }

    /**
     * Update status inline from table (can target specific history row)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|max:255|exists:subscription_status,status_name',
            'history_id' => 'nullable|exists:subscription_histories,id',
        ]);

        $subscription = Subscription::findOrFail($id);
        $newStatus = $request->status;
        $historyId = $request->history_id;

        // Find the latest history record to check if we are updating the current Head
        $latestHistory = DB::table('subscription_histories')
            ->where('subscription_id', $subscription->id)
            ->orderBy('period_start', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // If history_id is provided, we are targeting a specific row
        if ($historyId) {
            // Update the specific history record
            DB::table('subscription_histories')
                ->where('id', $historyId)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            // If this history record is the LATEST one, we must sync the Subscription Model
            if ($latestHistory && $latestHistory->id == $historyId) {
                $this->syncSubscriptionWithHistory($subscription, $newStatus);
            }
        } else {
            // Original Logic: No history_id provided, imply updating "Current" status
            // This updates Subscription + Latest History
            $this->syncSubscriptionWithHistory($subscription, $newStatus);
        }

        return response()->json([
            'success' => true,
            'status' => $subscription->fresh()->status, // Return fresh status
            'message' => 'Status updated successfully.'
        ]);
    }

    /**
     * Helper to sync Subscription status/dates and update/create latest history
     */
    private function syncSubscriptionWithHistory($subscription, $newStatus)
    {
        $oldStatus = $subscription->status;
        $subscription->status = $newStatus;
        $subscription->save();

        // 1. Get Latest History (to determining base date and to update it)
        $latestHistory = DB::table('subscription_histories')
            ->where('subscription_id', $subscription->id)
            ->orderBy('period_start', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // 2. Update Latest History or Create if missing
        if ($latestHistory) {
            DB::table('subscription_histories')
                ->where('id', $latestHistory->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            // Check if status is "Payment Received" and subscription is active/recurring to generate next cycle
            // We interpret "Payment Received" flexibly to include "payment received" based on common usage
            $isPaymentReceived = in_array(strtolower($newStatus), ['Payment Received', 'payment received']);

            if ($isPaymentReceived && $subscription->is_active && $subscription->is_recurring) {
                // Determine Next Start Date
                // If period_end is available, next start is period_end + 1 day
                // Fallback to due_date (legacy behavior for Postpaid) if period_end is missing
                $nextStartDate = null;
                if ($latestHistory->period_end) {
                    $nextStartDate = \Carbon\Carbon::parse($latestHistory->period_end)->addDay()->format('Y-m-d');
                } elseif ($latestHistory->due_date) {
                    // Fallback: If we don't have period_end, assume Postpaid where Due Date = Cycle End
                    // Note: If previous record was Prepaid but lacked period_end, this might be wrong (Start = Due), 
                    // but we assume this is a new feature so legacy data is Postpaid-like.
                    $nextStartDate = $latestHistory->due_date;
                }

                if ($nextStartDate) {
                    // Next Start determined
                    // Calculate Next End
                    $nextEndDate = $this->calculateEndDate(
                        $nextStartDate,
                        $subscription->recurrence_type,
                        $subscription->recurrence_interval ?? 1
                    );

                    // Determine Next Due Date based on billing type
                    $nextDueDate = $nextEndDate;
                    if ($subscription->billing_type === 'Prepaid') {
                        $nextDueDate = $nextStartDate;
                    }

                    // Create new pending history record
                    DB::table('subscription_histories')->insert([
                        'subscription_id' => $subscription->id,
                        'period_start' => $nextStartDate,
                        'period_end' => $nextEndDate ? \Carbon\Carbon::parse($nextEndDate)->subDay()->format('Y-m-d') : null,
                        'due_date' => $nextDueDate,
                        'amount' => $subscription->amount,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

        } else {
            // If missing, create one with calculated end date
            $calculatedEndDate = null;
             if ($subscription->is_recurring && $subscription->recurrence_type) {
                $calculatedEndDate = $this->calculateEndDate(
                    $subscription->start_date,
                    $subscription->recurrence_type,
                    $subscription->recurrence_interval ?? 1
                );
            }

            // Determine due date
            $initialDueDate = $calculatedEndDate;
            if ($subscription->billing_type === 'Prepaid') {
                $initialDueDate = $subscription->start_date;
            }

            DB::table('subscription_histories')->insert([
                'subscription_id' => $subscription->id,
                'period_start' => $subscription->start_date,
                'period_end' => $calculatedEndDate ? \Carbon\Carbon::parse($calculatedEndDate)->subDay()->format('Y-m-d') : null,
                'due_date' => $initialDueDate,
                'amount' => $subscription->amount,
                'status' => $newStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Get current user ID from Auth or session
     */
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


    /**
     * View subscription history
     */
    public function history(Request $request, $id)
    {
        $subscription = Subscription::with(['customer:id,name'])->findOrFail($id);
        
        $histories = DB::table('subscription_histories')
            ->where('subscription_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return response()->json($histories);
        }

        return view('subscription.history', compact('subscription', 'histories'));
    }
}
