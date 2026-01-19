<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessCardScan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        Log::info('BusinessCardController@index: Fetching all business card scans.');

        // Use pagination for performance
        $cards = BusinessCardScan::orderBy('created_at', 'desc')
            ->paginate(20);

        Log::info('BusinessCardController@index: Fetched ' . $cards->count() . ' cards.');

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
        Log::info('BusinessCardController@store: Attempting to store new business card.', ['data' => $request->all()]);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone_primary' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'social_links' => 'nullable|array',
            'raw_text' => 'nullable|string',
            'raw_ai_response' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            Log::warning('BusinessCardController@store: Validation failed.', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getCurrentUser();
            Log::info('BusinessCardController@store: Current user identified.', ['user_id' => $user ? $user->id : 'null']);
            
            $data = $request->all();
            $data['created_by'] = $user ? $user->id : null;

            $card = BusinessCardScan::create($data);

            Log::info('BusinessCardController@store: Business card created successfully.', ['card_id' => $card->id]);

            return response()->json([
                'success' => true,
                'message' => 'Business card saved successfully.',
                'data' => $card
            ], 201);

        } catch (\Exception $e) {
            Log::error('BusinessCardController@store: Exception occurred.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        Log::info('BusinessCardController@show: Fetching business card.', ['id' => $id]);
        $card = BusinessCardScan::find($id);

        if (!$card) {
            Log::warning('BusinessCardController@show: Business card not found.', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Business card not found.'
            ], 404);
        }

        Log::info('BusinessCardController@show: Business card retrieved successfully.', ['id' => $id]);
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
        Log::info('BusinessCardController@update: Attempting to update business card.', ['id' => $id, 'data' => $request->all()]);
        $card = BusinessCardScan::find($id);

        if (!$card) {
            Log::warning('BusinessCardController@update: Business card not found.', ['id' => $id]);
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
            Log::warning('BusinessCardController@update: Validation failed.', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $card->update($request->all());

            Log::info('BusinessCardController@update: Business card updated successfully.', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Business card updated successfully.',
                'data' => $card
            ]);
        } catch (\Exception $e) {
            Log::error('BusinessCardController@update: Exception occurred.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
        Log::info('BusinessCardController@destroy: Attempting to delete business card.', ['id' => $id]);
        $card = BusinessCardScan::find($id);

        if (!$card) {
            Log::warning('BusinessCardController@destroy: Business card not found.', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Business card not found.'
            ], 404);
        }

        try {
            $card->delete();
            Log::info('BusinessCardController@destroy: Business card deleted successfully.', ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Business card deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('BusinessCardController@destroy: Exception occurred.', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete business card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
