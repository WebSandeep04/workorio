<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessCardScan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BusinessCardController extends Controller
{
    /**
     * Get current authenticated user
     */
    private function getCurrentUser()
    {
        return auth()->user();
    }

    /**
     * Fetch all business card scans
     * Ordered by most recent
     */
    public function index(): JsonResponse
    {
        // Use pagination for performance
        $cards = BusinessCardScan::orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $cards
        ]);
    }

    /**
     * Store a new business card scan
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_primary' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'social_links' => 'nullable|array',
            'raw_text' => 'nullable|string',
            'raw_ai_response' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getCurrentUser();
            
            $data = $request->all();
            $data['created_by'] = $user ? $user->id : null;

            $card = BusinessCardScan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Business card saved successfully.',
                'data' => $card
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save business card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch a single business card details
     */
    public function show($id): JsonResponse
    {
        $card = BusinessCardScan::find($id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Business card not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $card
        ]);
    }

    /**
     * Update an existing business card
     */
    public function update(Request $request, $id): JsonResponse
    {
        $card = BusinessCardScan::find($id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Business card not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            // Add other validation as needed
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $card->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Business card updated successfully.',
                'data' => $card
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update business card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a business card
     */
    public function destroy($id): JsonResponse
    {
        $card = BusinessCardScan::find($id);

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Business card not found.'
            ], 404);
        }

        try {
            $card->delete();

            return response()->json([
                'success' => true,
                'message' => 'Business card deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete business card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
