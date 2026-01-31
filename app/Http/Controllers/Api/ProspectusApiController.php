<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prospectus;
use Illuminate\Support\Facades\Validator;

class ProspectusApiController extends Controller
{
    /**
     * Get list of prospects (supports search)
     */
    public function index(Request $request)
    {
        $query = Prospectus::with(['state', 'city', 'businessType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('prospectus_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 20);
        $prospects = $query->orderBy('prospectus_name')->paginate($perPage);

        return response()->json($prospects);
    }

    /**
     * Store a new prospect
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prospectus_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string',
            'address' => 'nullable|string',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'email' => 'nullable|email',
            'business_type_id' => 'nullable|integer',
            'website_link' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $prospect = Prospectus::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Prospect created successfully',
            'data' => $prospect
        ], 201);
    }

    /**
     * Get a single prospect by ID
     */
    public function show($id)
    {
        $prospect = Prospectus::with(['state', 'city', 'businessType'])->find($id);

        if (!$prospect) {
            return response()->json([
                'success' => false,
                'message' => 'Prospect not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $prospect
        ]);
    }
}
