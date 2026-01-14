<?php

namespace App\Http\Controllers;

use App\Models\CallingType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CallingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('calling.callingtype');
    }

    /**
     * Fetch calling types with pagination
     */
    public function fetch(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = CallingType::orderBy('created_at', 'desc');

        // Apply search filter if provided
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $callingTypes = $query->paginate($perPage);
        
        return response()->json($callingTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:calling_types,name'
        ]);

        $callingType = CallingType::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calling Type created successfully!',
            'data' => $callingType
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        $callingType = CallingType::findOrFail($id);
        return response()->json(['data' => $callingType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:calling_types,name,' . $id
        ]);

        $callingType = CallingType::findOrFail($id);
        $callingType->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calling Type updated successfully!',
            'data' => $callingType
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $callingType = CallingType::findOrFail($id);
        $callingType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calling Type deleted successfully!'
        ]);
    }

    /**
     * Get all calling types for select dropdown
     */
    public function getCallingTypes(): JsonResponse
    {
        $callingTypes = CallingType::orderBy('name')->get();
        return response()->json($callingTypes);
    }
}
