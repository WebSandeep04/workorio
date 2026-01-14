<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTerm;
use Illuminate\Support\Facades\Validator;

class PaymentTermController extends Controller
{
    /**
     * Display the payment terms management page
     */
    public function index()
    {
        return view('setup.payment-terms');
    }

    /**
     * Fetch all payment terms
     */
    /**
     * Fetch all payment terms
     */
    public function fetch(Request $request)
    {
        try {
            $query = PaymentTerm::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }
            
            // Assuming 'ordered' scope exists, otherwise remove or replace with orderBy
            $paymentTerms = $query->orderBy('id', 'desc')->paginate(10);
            return response()->json($paymentTerms);
        } catch (\Exception $e) {
            \Log::error('Payment terms fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Failed to fetch payment terms',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new payment term
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'advance_percentage' => 'required|integer|min:0|max:100',
            'design_dev_percentage' => 'required|integer|min:0|max:100',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        try {
            $data = $request->all();
            // Default new payment terms to active unless explicitly set
            if ($request->has('is_active')) {
                $data['is_active'] = (bool) $request->is_active;
            } else {
                $data['is_active'] = true;
            }
            $paymentTerm = PaymentTerm::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment term created successfully',
                'data' => $paymentTerm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment term',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific payment term for editing
     */
    public function show($id)
    {
        try {
            $paymentTerm = PaymentTerm::findOrFail($id);
            return response()->json($paymentTerm);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment term not found'
            ], 404);
        }
    }

    /**
     * Update a payment term
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'advance_percentage' => 'required|integer|min:0|max:100',
            'design_dev_percentage' => 'required|integer|min:0|max:100',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


        try {
            $paymentTerm = PaymentTerm::findOrFail($id);
            $data = $request->all();
            $data['is_active'] = (bool) $request->is_active;
            $paymentTerm->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment term updated successfully',
                'data' => $paymentTerm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment term',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payment term
     */
    public function destroy($id)
    {
        try {
            $paymentTerm = PaymentTerm::findOrFail($id);
            $paymentTerm->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Payment term deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment term',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        try {
            $paymentTerm = PaymentTerm::findOrFail($id);
            $paymentTerm->is_active = !$paymentTerm->is_active;
            $paymentTerm->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $paymentTerm
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}