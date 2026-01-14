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
use App\Models\Invoice;
use App\Models\InvoiceFollowup;
use App\Models\Advance;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentFollowupController extends Controller
{
    public function index()
    {
        return view('payment-followup');
    }

    // Get customers who have invoices (Payment Followup)
    public function getPaymentFollowupLeads(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // Get customers who have at least one invoice
        // derived purely from the 'invoices' table as requested
        $customerIds = Invoice::pluck('customer_id')->unique()->filter()->toArray();

        if (empty($customerIds)) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                $perPage,
                1
            );
        }

        // Fetch customers
        $customers = Customer::whereIn('id', $customerIds)
            ->with([
                'prospectus' => function ($q) {
                    $q->with(['state', 'city', 'businessType']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Transform customers to include invoice-related data
        $customers->getCollection()->transform(function ($customer) {
            
            // Get all invoices for this customer
            $invoiceIds = Invoice::where('customer_id', $customer->id)->pluck('id');
            
            // Get latest followup date across all invoices
            $latestNextFollowup = null;
            if ($invoiceIds->count() > 0) {
                $latestNextFollowup = InvoiceFollowup::whereIn('invoice_id', $invoiceIds)
                    ->whereNotNull('next_followup_date')
                    ->orderBy('next_followup_date', 'desc')
                    ->value('next_followup_date');
            }

            // Calculate total invoices count for this customer
            $totalInvoicesCount = Invoice::where('customer_id', $customer->id)->count();

            // Sales record id (optional now, but useful if exists)
            // We just grab the first one associated with any invoice, or falling back to the generic one
            $firstInvoiceWithSales = Invoice::where('customer_id', $customer->id)
                                          ->whereNotNull('sales_record_id')
                                          ->first();
            $salesRecordId = $firstInvoiceWithSales ? $firstInvoiceWithSales->sales_record_id : null;


            $customerCode = 'CUST-' . str_pad($customer->id, 6, '0', STR_PAD_LEFT);

            return (object) [
                'id' => $customer->id,
                'customer_code' => $customerCode,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'company_name' => $customer->company_name,
                'prospectus' => $customer->prospectus,
                'prospectus_name' => $customer->prospectus->prospectus_name ?? null,
                'created_at' => $customer->created_at,
                'created_at_formatted' => $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $customer->updated_at,
                'updated_at_formatted' => $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : null,
                'sales_record_id' => $salesRecordId,
                'next_followup_date' => $latestNextFollowup,
                'next_followup_date_formatted' => $latestNextFollowup ? Carbon::parse($latestNextFollowup)->format('Y-m-d H:i:s') : null,
                'total_invoices_count' => $totalInvoicesCount,
            ];
        });

        return response()->json($customers);
    }

    // Get filtered customers based on invoices
    public function filterLeads(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        // Start with checking Invoices for basic existence
        // But we also need to apply filters.
        // If filters are on Product/Lead Source, we might need to check Invoice -> SalesRecord (if linked) OR Invoice -> Product
        
        // Base Query for Customers who have invoices
        $query = Customer::whereHas('invoices');

        $query->with([
            'prospectus' => function ($q) {
                $q->with(['state', 'city', 'businessType']);
            }
        ]);

        // Apply customer-level filters
        if ($request->filled('state_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('business_type_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('business_type_id', $request->business_type_id);
            });
        }
        
        // Filter by Product (Check invoices directly first, then sales records of those invoices?)
        // Invoice table has 'product_id'.
        if ($request->filled('products_id')) {
             $query->whereHas('invoices', function($q) use ($request) {
                 $q->where('product_id', $request->products_id);
             });
        }

        // Date filters - Assuming applied to Invoice Created Date as per "from invoice table" request?
        // Or Customer Created Date? The UI shows "Created At" which usually maps to the row entity.
        // Let's stick to Customer Created At for consistency with previous code unless "from invoice table" implies Invoice Date.
        // Given the columns are customer details, filtering "From Date" usually filters when the record (Customer) appeared.
        // BUT, if this is Payment Followup, maybe it's Invoice Date.
        // Let's stick to Customer Created At to be safe, but restrict to those with invoices.
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('prospectus', function ($pq) use ($search) {
                        $pq->where('prospectus_name', 'like', "%{$search}%");
                    });
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform (Same as above)
        $customers->getCollection()->transform(function ($customer) {
            $invoiceIds = Invoice::where('customer_id', $customer->id)->pluck('id');

            $latestNextFollowup = null;
            if ($invoiceIds->count() > 0) {
                $latestNextFollowup = InvoiceFollowup::whereIn('invoice_id', $invoiceIds)
                    ->whereNotNull('next_followup_date')
                    ->orderBy('next_followup_date', 'desc')
                    ->value('next_followup_date');
            }
            
            // Calculate total invoices count for this customer
            $totalInvoicesCount = Invoice::where('customer_id', $customer->id)->count();

            // Sales record id (optional now, but useful if exists)
            $firstInvoiceWithSales = Invoice::where('customer_id', $customer->id)
                                          ->whereNotNull('sales_record_id')
                                          ->first();
            $salesRecordId = $firstInvoiceWithSales ? $firstInvoiceWithSales->sales_record_id : null;

            $customerCode = 'CUST-' . str_pad($customer->id, 6, '0', STR_PAD_LEFT);

            return (object) [
                'id' => $customer->id,
                'customer_code' => $customerCode,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'company_name' => $customer->company_name,
                'prospectus' => $customer->prospectus,
                'prospectus_name' => $customer->prospectus->prospectus_name ?? null,
                'created_at' => $customer->created_at,
                'created_at_formatted' => $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $customer->updated_at,
                'updated_at_formatted' => $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : null,
                'sales_record_id' => $salesRecordId,
                'next_followup_date' => $latestNextFollowup,
                'next_followup_date_formatted' => $latestNextFollowup ? Carbon::parse($latestNextFollowup)->format('Y-m-d H:i:s') : null,
                'total_invoices_count' => $totalInvoicesCount,
            ];
        });

        return response()->json($customers);
    }

    // Get filter options for dropdowns
    public function getFilterOptions()
    {
        $options = [
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
            
            // Remove users from filter options since we're only showing current user's leads
        ];

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

    // Get summary stats for payment followup
    public function getStats(Request $request)
    {
        // Reuse filter logic to match the customer list
        $query = Customer::whereHas('invoices');

        if ($request->filled('state_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('business_type_id')) {
            $query->whereHas('prospectus', function ($q) use ($request) {
                $q->where('business_type_id', $request->business_type_id);
            });
        }

        if ($request->filled('products_id')) {
            $query->whereHas('invoices', function($q) use ($request) {
                $q->where('product_id', $request->products_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('prospectus', function ($pq) use ($search) {
                        $pq->where('prospectus_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get matching customer IDs
        $customerIds = $query->pluck('id');

        // Verify product filter for invoices again (if invoice level filtering differs from customer availability)
        // But since we filtered customers based on having such invoices (via whereHas), we should only count those invoices? 
        // Or all invoices for those customers? Usually strict filtering means only relevant invoices.
        // If I filter by "Product A", I only want stats for Product A invoices? Or all invoices of customers who bought Product A?
        // "Total Invoices" usually implies filtered set.
        // Let's filter the invoices query as well.
        
        $invoiceQuery = Invoice::whereIn('customer_id', $customerIds);

        if ($request->filled('products_id')) {
            $invoiceQuery->where('product_id', $request->products_id);
        }

        $allInvoices = $invoiceQuery->get(); // Get collection to perform calculations (might be heavy but robust)

        $totalInvoices = $allInvoices->count();
        $pendingInvoices = $allInvoices->where('status', '!=', 'paid')->count();
        $paidInvoices = $allInvoices->where('status', 'paid')->count();
        $totalAmount = $allInvoices->sum('amount');
        
        // Calculate Received Amount (sum of all payments for these invoices)
        $invoiceIds = $allInvoices->pluck('id');
        $receivedAmount = InvoiceFollowup::whereIn('invoice_id', $invoiceIds)->sum('amount_paid');
        
        // Total Remaining Amount
        $totalRemainingAmount = $totalAmount - $receivedAmount;

        return response()->json([
            'total_invoices' => $totalInvoices,
            'pending_invoices' => $pendingInvoices,
            'paid_invoices' => $paidInvoices,
            'total_remaining_amount' => $totalRemainingAmount,
            'received_amount' => $receivedAmount,
            'total_amount' => $totalAmount
        ]);
    }

    // Get single lead data for invoice modal
    public function getLeadData($id)
    {
        $userId = $this->getCurrentUserId();
        
        $record = SalesRecord::with([
            'status',
            'prospectus',
            'city',
            'state',
            'businessType',
            'leadSource',
            'product'
        ])
        ->where('id', $id)
        ->where('status_id', 1)
        ->first();

        if (!$record) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Fetch all close won leads' products for the same customer/prospectus
        $allProducts = SalesRecord::with('product')
            ->where('status_id', 1)
            ->where(function($q) use ($record) {
                // Match by customer_id if available
                if ($record->customer_id) {
                    $q->where('customer_id', $record->customer_id);
                }
                // Or match by prospectus_id (if customer_id is not available or as fallback)
                if ($record->prospectus_id) {
                    if ($record->customer_id) {
                        $q->orWhere('prospectus_id', $record->prospectus_id);
                    } else {
                        $q->where('prospectus_id', $record->prospectus_id);
                    }
                }
            })
            ->whereNotNull('products_id')
            ->get()
            ->filter(function ($salesRecord) {
                return $salesRecord->product !== null;
            })
            ->map(function($salesRecord) {
                return (object) [
                    'id' => $salesRecord->product->id,
                    'product_name' => $salesRecord->product->product_name,
                    'sales_record_id' => $salesRecord->id,
                ];
            })
            ->unique('id')
            ->values();

        // Add products to the response
        $record->all_products = $allProducts;

        return response()->json($record);
    }

    // Get status counts for close won leads
    public function getStatusCounts()
    {
        $userId = $this->getCurrentUserId();
        // Since we're only showing close won, return total count for current user
        $totalCount = SalesRecord::where('status_id', 1)
            ->where('user_id', $userId)
            ->count();

        return response()->json([
            [
                'id' => 1,
                'status_name' => 'Close Won',
                'count' => $totalCount
            ]
        ]);
    }

    // Store invoice
    public function storeInvoice(Request $request)
    {
        $userId = $this->getCurrentUserId();
        
        $request->validate([
            'sales_record_id' => 'required|exists:sales_records,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Verify that the sales record belongs to the current user and is close won
        $salesRecord = SalesRecord::where('id', $request->sales_record_id)
            ->where('user_id', $userId)
            ->where('status_id', 1)
            ->first();

        if (!$salesRecord) {
            return response()->json([
                'message' => 'Sales record not found or you do not have permission to create invoice for this lead.'
            ], 403);
        }

        // Generate invoice number
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'sales_record_id' => $request->sales_record_id,
            'invoice_number' => $invoiceNumber,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice raised successfully!',
            'invoice_number' => $invoiceNumber
        ]);
    }

    public function payLumpsum(Request $request)
    {
        $userId = $this->getCurrentUserId();
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'next_followup_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Verify customer exists
        $customer = Customer::where('id', $request->customer_id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found.'
            ], 404);
        }

        // Get all invoices for this customer ordered by creation date (oldest first)
        // Using both created_at and id to ensure consistent ordering
        $invoices = Invoice::where('customer_id', $request->customer_id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($invoices->isEmpty()) {
            return response()->json([
                'message' => 'No invoices found for this lead. Please create an invoice first.'
            ], 400);
        }

        $lumpsumAmount = $request->amount;
        $remainingAmount = $lumpsumAmount;
        $processedInvoices = [];

        DB::beginTransaction();
        try {
            foreach ($invoices as $invoice) {
                if ($remainingAmount <= 0) {
                    break;
                }

                // Calculate remaining amount for this invoice
                $totalPaid = InvoiceFollowup::where('invoice_id', $invoice->id)->sum('amount_paid');
                $invoiceRemaining = $invoice->amount - $totalPaid;

                if ($invoiceRemaining > 0) {
                    // Calculate payment amount for this invoice
                    $paymentAmount = min($remainingAmount, $invoiceRemaining);

                    // Auto-generate transaction ID
                    $lastFollowup = InvoiceFollowup::orderBy('id', 'desc')->first();
                    $nextId = $lastFollowup ? $lastFollowup->id + 1 : 1;
                    $transactionId = 'TXN-' . str_pad($nextId, 8, '0', STR_PAD_LEFT);

                    // Create invoice followup
                    $followup = InvoiceFollowup::create([
                        'invoice_id' => $invoice->id,
                        'amount_paid' => $paymentAmount,
                        'payment_date' => $request->payment_date,
                        'payment_method' => null,
                        'transaction_id' => $transactionId,
                        'notes' => $request->notes,
                        'next_followup_date' => $request->next_followup_date
                    ]);

                    // Update remaining amount
                    $remainingAmount -= $paymentAmount;

                    // Check if invoice is now fully paid
                    $newTotalPaid = InvoiceFollowup::where('invoice_id', $invoice->id)->sum('amount_paid');
                    if ($newTotalPaid >= $invoice->amount) {
                        $invoice->status = 'paid';
                        $invoice->paid_at = now();
                        $invoice->save();
                    }

                    $processedInvoices[] = [
                        'invoice_number' => $invoice->invoice_number,
                        'amount_paid' => $paymentAmount
                    ];
                }
            }

            // If there's excess amount, save it as advance
            if ($remainingAmount > 0) {
                // Get first sales record for this customer for advance
                $firstSalesRecord = SalesRecord::where('customer_id', $request->customer_id)
                    ->where('status_id', 1)
                    ->orderBy('createdat', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($firstSalesRecord) {
                    // Auto-generate transaction ID for advance
                    $lastAdvance = Advance::orderBy('id', 'desc')->first();
                    $lastFollowup = InvoiceFollowup::orderBy('id', 'desc')->first();
                    $maxId = max(
                        $lastAdvance ? $lastAdvance->id : 0,
                        $lastFollowup ? $lastFollowup->id : 0
                    );
                    $nextId = $maxId + 1;
                    $transactionId = 'TXN-' . str_pad($nextId, 8, '0', STR_PAD_LEFT);

                    Advance::create([
                        'sales_record_id' => $firstSalesRecord->id,
                        'amount' => $remainingAmount,
                        'payment_date' => $request->payment_date,
                        'transaction_id' => $transactionId,
                        'notes' => $request->notes . (empty($request->notes) ? '' : ' | ') . 'Excess amount from lumpsum payment',
                        'next_followup_date' => $request->next_followup_date
                    ]);
                }
            }

            DB::commit();

            $message = 'Lumpsum payment of ' . number_format($lumpsumAmount, 2) . ' processed successfully!';
            if ($remainingAmount > 0) {
                $message .= ' Excess amount of ' . number_format($remainingAmount, 2) . ' saved as advance.';
            }
            $message .= ' Applied to ' . count($processedInvoices) . ' invoice(s).';

            return response()->json([
                'success' => true,
                'message' => $message,
                'processed_invoices' => $processedInvoices,
                'excess_amount' => $remainingAmount,
                'advance_saved' => $remainingAmount > 0
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error processing lumpsum payment: ' . $e->getMessage()
            ], 500);
        }
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
     * Get all customers.
     */
    public function getAllCustomers()
    {
        $customers = Customer::orderBy('name')
            ->get(['id', 'name', 'company_name', 'email', 'phone', 'address']);

        return response()->json($customers);
    }

    /**
     * Get all products.
     */
    public function getAllProducts()
    {
        $products = SalesProduct::orderBy('product_name')
            ->get(['id', 'product_name']);

        return response()->json($products);
    }

    /**
     * Get all Close Won sales records for a specific customer.
     */
    public function getCloseWonLeads($customerId)
    {
        $userId = $this->getCurrentUserId();

        $leads = SalesRecord::with('product')
            ->where('customer_id', $customerId)
            // ->where('status_id', 1) // Close Won - Relaxed if needed, but user didn't ask. Keeping restricted for now unless customer has no Close Won. 
            // Actually, if we show ALL customers, we should probably show ALL leads for them?
            // User requested "fetch all customers". If I show a customer with only Open leads, this list is empty.
            // I'll relax this to allow any lead for the customer.
            
            ->get(['id', 'leads_name', 'products_id', 'status_id']); 

        // Transform to include product name clearly if needed, or rely on 'product' relation
        $leads->transform(function($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->leads_name . ' (' . ($lead->product->product_name ?? 'No Product') . ')',
                'products_id' => $lead->products_id
            ];
        });

        return response()->json($leads);
    }
    /**
     * Show details page for specific metric
     */
    public function details($type)
    {
        return view('payment-followup.details', compact('type'));
    }

    /**
     * Get data for details page
     */
    public function getDetailsData(Request $request, $type)
    {
        // Start with base query
        $query = Invoice::with('customer');

        // 1. Customer Relationship Filters (State, City, Business Type)
        if ($request->filled('state_id')) {
            $query->whereHas('customer.prospectus', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('customer.prospectus', function ($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }

        if ($request->filled('business_type_id')) {
            $query->whereHas('customer.prospectus', function ($q) use ($request) {
                $q->where('business_type_id', $request->business_type_id);
            });
        }

        // 2. Invoice Filters
        
        // Product (Invoice level)
        if ($request->filled('products_id')) {
            $query->where('product_id', $request->products_id);
        }

        // Status (Invoice level)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Due Date Filters
        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        // Search (Invoice Number OR Customer details)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%")
                           ->orWhere('phone', 'like', "%{$search}%")
                           ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply Type Filter
        if ($type === 'total-invoices') {
            // No additional filter
        } elseif ($type === 'pending-invoices') {
            $query->where('status', '!=', 'paid');
        } elseif ($type === 'paid-invoices') {
            $query->where('status', 'paid');
        } elseif ($type === 'remaining-amount') {
            $query->where('status', '!=', 'paid');
        } elseif ($type === 'received-amount') {
            $query->whereHas('followups');
        } elseif ($type === 'total-amount') {
            // All invoices
        }

        // Apply recent ordering
        $query->orderBy('created_at', 'desc');

        $invoices = $query->paginate(20);

        // Calculate received amount for each invoice for display
        $invoices->getCollection()->transform(function($invoice) {
            $received = $invoice->followups()->sum('amount_paid');
            $invoice->received_amount = $received;
            return $invoice;
        });

        return response()->json($invoices);
    }
}

