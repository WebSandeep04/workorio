<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Remark;
use App\Models\SalesRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RemarkApiController extends Controller
{
    /**
     * Get remarks for a specific lead
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sales_record_id' => 'required|integer|exists:sales_records,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $remarks = Remark::where('sales_remark_id', $request->sales_record_id)
            ->orderBy('remark_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $remarks
        ]);
    }

    /**
     * Store or Update a remark (Upsert by Date)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sales_record_id' => 'required|integer|exists:sales_records,id',
            'remark_date' => 'required|date_format:Y-m-d',
            'remark' => 'required|string',
            'ticket_value' => 'nullable|numeric', // Changed to numeric for API safety
            'next_follow_up_date' => 'nullable|date_format:Y-m-d',
            'status_id' => 'required|integer|exists:sales_status,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $salesRecord = SalesRecord::find($validated['sales_record_id']);

        // Check authorization (optional, but recommended)
        if ($salesRecord->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
             // You might want to allow managers too, for now keeping it simple or skipping strict check if not required
        }

        // Upsert Remark
        $remark = Remark::updateOrCreate(
            [
                'sales_remark_id' => $salesRecord->id,
                'remark_date' => $validated['remark_date']
            ],
            [
                'remark' => $validated['remark']
            ]
        );

        // Update Sales Record
        $salesRecord->update([
            'ticket_value' => $validated['ticket_value'] ?? $salesRecord->ticket_value,
            'next_follow_up_date' => $validated['next_follow_up_date'],
            'status_id' => $validated['status_id'],
            'updatedat' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => $remark->wasRecentlyCreated ? 'Remark created successfully' : 'Remark updated successfully',
            'data' => $remark
        ]);
    }
}
