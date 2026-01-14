<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\SalesRecord;
use App\Models\InvoiceFollowup;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $customerId = $request->get('customer_id');
        
        if (!$customerId) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'from' => 0,
                'to' => 0,
                'total' => 0
            ]);
        }

        // Get sales record IDs for this customer (if any close won leads)
        $customer = Customer::find($customerId);
        $salesRecordIds = collect([]);
        
        if ($customer) {
            $salesRecordIds = SalesRecord::where('status_id', 1)
                ->where(function($q) use ($customerId, $customer) {
                    $q->where('customer_id', $customerId);
                    if ($customer->prospectus_id) {
                        $q->orWhere('prospectus_id', $customer->prospectus_id);
                    }
                })
                ->pluck('id');
        }

        // We do NOT return early if salesRecordIds is empty anymore, 
        // because we can have invoices directly linked to customer_id with null sales_record_id

        $perPage = $request->get('per_page', 10);
        
        // Match invoices:
        // 1. Where customer_id matches (NEW LOGIC)
        // 2. OR sales_record_id matches one of the customer's close-won leads (OLD LOGIC)
        $query = Invoice::with(['salesRecord.product', 'product']) // Added 'product' relationship load
            ->where(function($q) use ($customerId, $salesRecordIds) {
                $q->where('customer_id', $customerId);
                if ($salesRecordIds->isNotEmpty()) {
                    $q->orWhereIn('sales_record_id', $salesRecordIds);
                }
            });

        // Apply filters
        if ($request->filled('status') && $request->status !== 'all') { // Added 'all' check
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('due_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('due_date', '<=', $request->to_date);
        }

        // For lumpsum preview, we need to order by created_at ASC (oldest first)
        // But for normal listing, we keep desc order
        if ($request->has('per_page') && $request->get('per_page') == 1000) {
            // This is likely for lumpsum preview, order by oldest first
            $invoices = $query->orderBy('created_at', 'asc')->orderBy('id', 'asc')->paginate($perPage);
        } else {
            $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);
        }

        // Add paid amount and remaining amount to each invoice
        $invoices->getCollection()->transform(function ($invoice) {
            $totalPaid = InvoiceFollowup::where('invoice_id', $invoice->id)->sum('amount_paid');
            $invoice->paid_amount = $totalPaid;
            $invoice->remaining_amount = $invoice->amount - $totalPaid;
            
            // Surface product name from linked sales record OR direct product relation
            $productName = null;
            if ($invoice->product) {
                 $productName = $invoice->product->product_name;
            } elseif ($invoice->salesRecord && $invoice->salesRecord->product) {
                 $productName = $invoice->salesRecord->product->product_name;
            }
            $invoice->product_name = $productName;
            
            return $invoice;
        });

        return response()->json($invoices);
    }

    public function show($customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return redirect()->route('payment-followup')->with('error', 'Customer not found.');
        }

        $defaultSalesRecordId = SalesRecord::where('status_id', 1)
            ->where(function($q) use ($customer) {
                $q->where('customer_id', $customer->id);
                if ($customer->prospectus_id) {
                    $q->orWhere('prospectus_id', $customer->prospectus_id);
                }
            })
            ->orderBy('createdat', 'desc')
            ->value('id');

        return view('invoices', [
            'customerId' => $customerId,
            'defaultSalesRecordId' => $defaultSalesRecordId
        ]);
    }
    
    public function getInvoice($id)
    {
        $userId = $this->getCurrentUserId();
        
        $invoice = Invoice::with('salesRecord')
            ->where('id', $id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found.'
            ], 404);
        }

        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        $userId = $this->getCurrentUserId();
        
        $request->validate([
            'sales_record_id' => 'nullable|exists:sales_records,id',
            'customer_id' => 'required_without:sales_record_id|exists:customers,id', // Required if no sales record
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'product_id' => 'nullable|exists:sales_products,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        // If sales record provided, verify permission (if user is not manager/admin - logic depends on requirements, assuming ownership check if lead provided)
        $salesRecord = null;
        if ($request->sales_record_id) {
            $salesRecord = SalesRecord::where('id', $request->sales_record_id)
                // ->where('user_id', $userId) // Strict check? Assuming owner check might be needed or skipped based on previous conv.
                ->first();
             
             // If strict permission for sales record needed:
             // if (!$salesRecord || $salesRecord->user_id != $userId) { ... }
        }

        // Get customer_id: explicitly provided OR from sales record
        $customerId = $request->customer_id ?? ($salesRecord ? $salesRecord->customer_id : null);
        
        if (!$customerId) {
             return response()->json(['message' => 'Customer is required.'], 422);
        }

        // Get product_id: explicitly provided OR from sales record
        $productId = $request->product_id ?? ($salesRecord ? $salesRecord->products_id : null);

        $invoice = Invoice::create([
            'sales_record_id' => $request->sales_record_id,
            'customer_id' => $customerId,
            'product_id' => $productId,
            'invoice_number' => $request->invoice_number,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice added successfully!',
            'invoice_number' => $invoice->invoice_number
        ]);
    }

    public function update(Request $request, $id)
    {
        $userId = $this->getCurrentUserId();
        
        $invoice = Invoice::with('salesRecord')
            ->where('id', $id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found.'
            ], 404);
        }

        $request->validate([
            'invoice_number' => 'required|string|max:50|unique:invoices,invoice_number,' . $id,
            'customer_id' => 'nullable|exists:customers,id',
            'product_id' => 'nullable|exists:sales_products,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'customer_id' => $request->customer_id ?? $invoice->customer_id,
            'product_id' => $request->product_id ?? $invoice->product_id,
            'amount' => $request->amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $userId = $this->getCurrentUserId();
        
        $invoice = Invoice::with('salesRecord')
            ->where('id', $id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found.'
            ], 404);
        }

        // Verify that the invoice's sales record belongs to the current user
        if ($invoice->salesRecord->user_id != $userId) {
            return response()->json([
                'message' => 'You do not have permission to delete this invoice.'
            ], 403);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully!'
        ]);
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

