<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InvoiceFollowup;
use App\Models\Invoice;
use App\Models\SalesRecord;

class InvoiceFollowupController extends Controller
{
    public function index($invoiceId)
    {
        $userId = $this->getCurrentUserId();
        
        // Verify invoice belongs to current user
        $invoice = Invoice::with('salesRecord')
            ->where('id', $invoiceId)
            ->first();

        if (!$invoice) {
            return redirect()->route('payment-followup')->with('error', 'Invoice not found.');
        }

        // Verify that the invoice's sales record belongs to the current user (ONLY if a sales record exists)
        if ($invoice->salesRecord && $invoice->salesRecord->user_id != $userId) {
            return redirect()->route('payment-followup')->with('error', 'You do not have permission to view this invoice.');
        }

        // Calculate total paid and remaining amount
        $totalPaid = InvoiceFollowup::where('invoice_id', $invoiceId)->sum('amount_paid');
        $remainingAmount = $invoice->amount - $totalPaid;
        
        // Get all followups for the invoice
        $followups = InvoiceFollowup::where('invoice_id', $invoiceId)->get();

        return view('invoice-followup', compact('invoiceId', 'invoice', 'totalPaid', 'remainingAmount', 'followups'));
    }

    public function getFollowups(Request $request, $invoiceId)
    {
        $userId = $this->getCurrentUserId();
        
        // Verify invoice belongs to current user
        $invoice = Invoice::with('salesRecord')
            ->where('id', $invoiceId)
            ->first();

        // Check permission if sales record exists
        if (!$invoice || ($invoice->salesRecord && $invoice->salesRecord->user_id != $userId)) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'from' => 0,
                'to' => 0,
                'total' => 0
            ], 403);
        }

        $perPage = $request->get('per_page', 10);
        $query = InvoiceFollowup::where('invoice_id', $invoiceId);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_method', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        if ($request->filled('followup_from_date')) {
            $query->whereDate('next_followup_date', '>=', $request->followup_from_date);
        }

        if ($request->filled('followup_to_date')) {
            $query->whereDate('next_followup_date', '<=', $request->followup_to_date);
        }

        $followups = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        return response()->json($followups);
    }

    public function store(Request $request, $invoiceId)
    {
        $userId = $this->getCurrentUserId();
        
        // Verify invoice belongs to current user
        $invoice = Invoice::with('salesRecord')
            ->where('id', $invoiceId)
            ->first();

        // Check permission if sales record exists
        if (!$invoice || ($invoice->salesRecord && $invoice->salesRecord->user_id != $userId)) {
            return response()->json([
                'message' => 'Invoice not found or you do not have permission.'
            ], 403);
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'next_followup_date' => 'nullable|date'
        ]);

        // Generate transaction ID
        $lastFollowup = InvoiceFollowup::orderBy('id', 'desc')->first();
        $nextId = $lastFollowup ? $lastFollowup->id + 1 : 1;
        $transactionId = 'TXN-' . str_pad($nextId, 8, '0', STR_PAD_LEFT);

        $followup = InvoiceFollowup::create([
            'invoice_id' => $invoiceId,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $transactionId,
            'notes' => $request->notes,
            'next_followup_date' => $request->next_followup_date
        ]);

        // Update invoice status if total paid equals or exceeds invoice amount
        $totalPaid = InvoiceFollowup::where('invoice_id', $invoiceId)->sum('amount_paid');
        if ($totalPaid >= $invoice->amount) {
            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Followup added successfully!'
        ]);
    }

    public function getFollowup($invoiceId, $id)
    {
        $userId = $this->getCurrentUserId();
        
        $followup = InvoiceFollowup::with('invoice.salesRecord')
            ->where('id', $id)
            ->where('invoice_id', $invoiceId)
            ->first();

        if (!$followup) {
            return response()->json([
                'message' => 'Followup not found.'
            ], 404);
        }

        if ($followup->invoice->salesRecord && $followup->invoice->salesRecord->user_id != $userId) {
            return response()->json([
                'message' => 'You do not have permission to view this followup.'
            ], 403);
        }

        return response()->json($followup);
    }

    public function update(Request $request, $invoiceId, $id)
    {
        $userId = $this->getCurrentUserId();
        
        $followup = InvoiceFollowup::with('invoice.salesRecord')
            ->where('id', $id)
            ->where('invoice_id', $invoiceId)
            ->first();

        if (!$followup) {
            return response()->json([
                'message' => 'Followup not found.'
            ], 404);
        }

        if ($followup->invoice->salesRecord && $followup->invoice->salesRecord->user_id != $userId) {
            return response()->json([
                'message' => 'You do not have permission to update this followup.'
            ], 403);
        }

        $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'next_followup_date' => 'nullable|date'
        ]);

        $followup->update([
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'next_followup_date' => $request->next_followup_date
        ]);

        // Update invoice status if total paid equals or exceeds invoice amount
        $invoice = $followup->invoice;
        $totalPaid = InvoiceFollowup::where('invoice_id', $invoiceId)->sum('amount_paid');
        if ($totalPaid >= $invoice->amount) {
            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Followup updated successfully!'
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

