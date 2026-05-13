<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Customer;
use App\Models\SalesRecord;
use App\Models\SalesProduct;
use App\Models\SubscriptionStatus;
use Carbon\Carbon;

class SubscriptionApiController extends Controller
{
    /**
     * Get paginated subscriptions for current user.
     */
    public function getSubscriptions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = $request->get('per_page', 15);

        // --- MODE A: Group View (Paginated Customers with Active Subscriptions) ---
        if ($request->boolean('view_group')) {
            $customerQuery = Customer::where('is_subscription_on', 1);

            if ($request->filled('search')) {
                $search = $request->search;
                $customerQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            // Apply Subscriptions-Level Subquery Filtering if any filters are provided
            if ($request->filled('customer_id') || $request->filled('status') || $request->filled('product_id') || $request->filled('is_recurring') || $request->filled('recurrence_type') || $request->filled('is_active')) {
                $customerQuery->whereHas('subscriptions', function($q) use ($request, $user) {
                    $q->where('user_id', $user->id);
                    if ($request->filled('status')) {
                        $status = $request->status;
                        $q->where(function($subQ) use ($status) {
                            $subQ->where('status', $status)
                                 ->orWhereHas('histories', function($hq) use ($status) {
                                     $hq->where('status', $status);
                                 });
                        });
                    }
                    if ($request->filled('product_id')) {
                        $q->where('product_id', $request->product_id);
                    }
                    if ($request->filled('is_recurring')) {
                        $q->where('is_recurring', $request->boolean('is_recurring'));
                    }
                    if ($request->filled('recurrence_type')) {
                        $q->where('recurrence_type', $request->recurrence_type);
                    }
                    if ($request->filled('is_active')) {
                        $q->where('is_active', $request->boolean('is_active'));
                    }
                });
            }

            $customers = $customerQuery->select('id', 'name', 'email', 'phone', 'company_name')
                ->withCount(['subscriptions' => function($q) use ($user) {
                     $q->where('user_id', $user->id);
                }])
                ->orderBy('name')
                ->paginate($perPage);

            return response()->json($customers);
        }

        // --- MODE B: Standard/Individual View ---
        $query = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'latestHistory:subscription_histories.id,subscription_histories.subscription_id,subscription_histories.due_date,subscription_histories.status'
        ])
        ->withCount(['histories' => function ($q) {
            $q->where('status', '!=', 'Payment Received');
        }]);

        // Scope to user's subscriptions
        $query->where('user_id', $user->id);

        // Apply Filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(function($q) use ($status) {
                $q->where('status', $status)
                  ->orWhereHas('histories', function($hq) use ($status) {
                      $hq->where('status', $status);
                  });
            });
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('is_recurring')) {
            $query->where('is_recurring', $request->boolean('is_recurring'));
        }

        if ($request->filled('recurrence_type')) {
            $query->where('recurrence_type', $request->recurrence_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subscription_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($subscriptions);
    }

    /**
     * Get summary KPI stats for dashboard cards.
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $userId = $user->id;

        // 1. Total Unique Customers with active subscription flag
        // Scoped to customers belonging to the current user's subscriptions
        $totalCustomers = Customer::where('is_subscription_on', 1)
            ->whereHas('subscriptions', function($q) use ($userId) {
                 $q->where('user_id', $userId);
            })->count();

        // 2. Total active subscription models
        $totalSubscriptions = Subscription::where('user_id', $userId)->count();

        // 3. Count coming due in 15 Days
        $comingDueCount = DB::table('subscription_histories')
            ->join('subscriptions', 'subscription_histories.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.user_id', $userId)
            ->whereBetween('due_date', [Carbon::now()->startOfDay(), Carbon::now()->addDays(15)->endOfDay()])
            ->whereNotIn(DB::raw('LOWER(subscription_histories.status)'), ['payment received', 'paid'])
            ->count();

        // 4. Overdue (due date is in past)
        $overDueCount = DB::table('subscription_histories')
            ->join('subscriptions', 'subscription_histories.subscription_id', '=', 'subscriptions.id')
            ->where('subscriptions.user_id', $userId)
            ->where('due_date', '<', Carbon::now()->startOfDay())
            ->whereNotIn(DB::raw('LOWER(subscription_histories.status)'), ['payment received', 'paid'])
            ->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_customers' => $totalCustomers,
                'total_subscriptions' => $totalSubscriptions,
                'coming_due' => $comingDueCount,
                'overdue' => $overDueCount
            ]
        ]);
    }

    /**
     * Get individual subscription details.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $subscription = Subscription::with([
            'customer:id,name,email,phone,company_name',
            'product:id,product_name',
            'salesRecord:id,leads_name,products_id',
            'user:id,name',
            'creator:id,name'
        ])->where('user_id', $user->id)->findOrFail($id);

        return response()->json($subscription);
    }

    /**
     * Store a new subscription.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id|required_without:subscription_name',
            'product_id' => 'nullable|exists:sales_products,id',
            'subscription_name' => 'nullable|string|max:255|required_without:customer_id',
            'amount' => 'nullable|numeric|min:0',
            'billing_type' => 'nullable|in:Prepaid,Postpaid',
            'start_date' => 'required|date',
            'status' => 'required|string|max:255',
            'is_recurring' => 'nullable|boolean',
            'recurrence_type' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,quarterly,half_yearly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|required_if:is_recurring,1',
            'alert_before_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscription = DB::transaction(function() use ($request, $user) {
                $isRecurring = $request->boolean('is_recurring');

                $subData = [
                    'customer_id' => $request->customer_id,
                    'product_id' => $request->product_id,
                    'sales_record_id' => $request->sales_record_id ?? null,
                    'user_id' => $user->id,
                    'created_by' => $user->id,
                    'subscription_name' => $request->subscription_name,
                    'amount' => $request->amount,
                    'billing_type' => $request->billing_type,
                    'start_date' => $request->start_date,
                    'status' => $request->status,
                    'notes' => $request->notes,
                    'is_active' => true,
                    'is_recurring' => $isRecurring,
                ];

                if ($isRecurring) {
                    $subData['recurrence_type'] = $request->recurrence_type;
                    $subData['recurrence_interval'] = $request->recurrence_interval ?? 1;
                    $subData['alert_before_days'] = $request->alert_before_days;
                }

                $sub = Subscription::create($subData);

                // Turn on subscription flag for customer
                if ($sub->customer_id) {
                    Customer::where('id', $sub->customer_id)->update(['is_subscription_on' => 1]);
                }

                // Generate histories
                $now = Carbon::now();
                $cycleStart = Carbon::parse($sub->start_date);
                
                do {
                    $cycleEndDate = null;
                    if ($sub->is_recurring && $sub->recurrence_type) {
                        $cycleEndDate = $this->calculateEndDate(
                            $cycleStart->format('Y-m-d'),
                            $sub->recurrence_type,
                            $sub->recurrence_interval ?? 1
                        );
                    }

                    $currentDueDate = $cycleEndDate;
                    if ($sub->billing_type === 'Prepaid') {
                        $currentDueDate = $cycleStart->format('Y-m-d');
                    }

                    DB::table('subscription_histories')->insert([
                        'subscription_id' => $sub->id,
                        'period_start' => $cycleStart->format('Y-m-d'),
                        'period_end' => $cycleEndDate ? Carbon::parse($cycleEndDate)->subDay()->format('Y-m-d') : null,
                        'due_date' => $currentDueDate,
                        'amount' => $sub->amount ?? 0,
                        'status' => $sub->status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!$sub->is_recurring) break;

                    if ($cycleEndDate) {
                        $cycleStart = Carbon::parse($cycleEndDate);
                    } else {
                        break;
                    }
                } while ($cycleStart->lte($now));

                return $sub;
            });

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully!',
                'data' => $subscription->load(['customer:id,name', 'product:id,product_name'])
            ], 201);

        } catch (\Exception $e) {
            Log::error('Subscription API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Status / Handle Renewal.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255',
            'history_id' => 'nullable|exists:subscription_histories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subscription = Subscription::where('user_id', $user->id)->findOrFail($id);
        $newStatus = $request->status;
        $historyId = $request->history_id;

        try {
            DB::transaction(function() use ($subscription, $newStatus, $historyId) {
                $latestHistory = DB::table('subscription_histories')
                    ->where('subscription_id', $subscription->id)
                    ->orderBy('period_start', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($historyId) {
                    DB::table('subscription_histories')
                        ->where('id', $historyId)
                        ->update([
                            'status' => $newStatus,
                            'updated_at' => now(),
                        ]);

                    if ($latestHistory && $latestHistory->id == $historyId) {
                        $this->syncSubscriptionWithHistory($subscription, $newStatus);
                    }
                } else {
                    $this->syncSubscriptionWithHistory($subscription, $newStatus);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Subscription renewal/status updated successfully!',
                'status' => $subscription->fresh()->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Subscription Histories.
     */
    public function getHistory(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $subscription = Subscription::where('user_id', $user->id)->findOrFail($id);

        $histories = DB::table('subscription_histories')
            ->where('subscription_id', $id)
            ->orderBy('period_start', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json($histories);
    }

    /**
     * Fetch customers, products, and filters needed for Creating form.
     */
    public function getFormOptions(Request $request)
    {
        try {
            $products = SalesProduct::orderBy('product_name')->get(['id', 'product_name']);
            $customers = Customer::orderBy('name')
                ->get(['id', 'name', 'company_name', 'email', 'phone']);

            $statuses = SubscriptionStatus::orderBy('status_name')->get(['id', 'status_name']);

            return response()->json([
                'products' => $products,
                'customers' => $customers,
                'statuses' => $statuses
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Core Logic: Syncing and Renewing Subscription Periods.
     */
    private function syncSubscriptionWithHistory($subscription, $newStatus)
    {
        $subscription->status = $newStatus;
        $subscription->save();

        $latestHistory = DB::table('subscription_histories')
            ->where('subscription_id', $subscription->id)
            ->orderBy('period_start', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($latestHistory) {
            DB::table('subscription_histories')
                ->where('id', $latestHistory->id)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            $isPaymentReceived = in_array(strtolower($newStatus), ['payment received', 'paid']);

            // Trigger RENEWAL if Paid + Recurring + Active
            if ($isPaymentReceived && $subscription->is_active && $subscription->is_recurring) {
                $nextStartDate = null;
                if ($latestHistory->period_end) {
                    $nextStartDate = Carbon::parse($latestHistory->period_end)->addDay()->format('Y-m-d');
                } elseif ($latestHistory->due_date) {
                    $nextStartDate = $latestHistory->due_date;
                }

                if ($nextStartDate) {
                    $nextEndDate = $this->calculateEndDate(
                        $nextStartDate,
                        $subscription->recurrence_type,
                        $subscription->recurrence_interval ?? 1
                    );

                    $nextDueDate = $nextEndDate;
                    if ($subscription->billing_type === 'Prepaid') {
                        $nextDueDate = $nextStartDate;
                    }

                    // Insert NEXT CYCLE pending history
                    DB::table('subscription_histories')->insert([
                        'subscription_id' => $subscription->id,
                        'period_start' => $nextStartDate,
                        'period_end' => $nextEndDate ? Carbon::parse($nextEndDate)->subDay()->format('Y-m-d') : null,
                        'due_date' => $nextDueDate,
                        'amount' => $subscription->amount ?? 0,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function calculateEndDate($startDate, $recurrenceType, $interval)
    {
        if (!$startDate || !$recurrenceType) return null;

        $start = Carbon::parse($startDate);
        $interval = (int) ($interval ?? 1);

        switch (strtolower($recurrenceType)) {
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
}
