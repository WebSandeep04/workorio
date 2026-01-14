<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prospectus;

class ProspectusController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'prospectus_name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'contact_number' => 'nullable|string',
        'address' => 'nullable|string',
        'state_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'email' => 'nullable|email',
        'business_type_id' => 'nullable|integer',
        'website_link' => 'nullable|string']);

    Prospectus::create($validated);

    return response()->json(['message' => 'Prospectus saved successfully.']);
}

public function getProspectus(){
    $prospectus = Prospectus::orderBy('prospectus_name')->get();
    return response()->json($prospectus);
}

public function fillprospectus($id){
    $prospectus = Prospectus::find($id);

    if (!$prospectus) {
        return response()->json(['error' => 'Not found'], 404);
    }

    return response()->json($prospectus);
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
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

    $prospectus = Prospectus::find($id);
    
    if (!$prospectus) {
        return response()->json(['error' => 'Prospectus not found'], 404);
    }

    // Update prospectus
    $prospectus->update($validated);

    // Update all related sales records
    $prospectus->salesRecords()->update([
        'leads_name' => $validated['prospectus_name'],
        'contact_person' => $validated['contact_person'] ?? null,
        'contact_number' => $validated['contact_number'] ?? null,
        'address' => $validated['address'] ?? null,
        'state_id' => $validated['state_id'] ?? null,
        'city_id' => $validated['city_id'] ?? null,
        'email' => $validated['email'] ?? null,
        'business_type_id' => $validated['business_type_id'] ?? null,
        'website_link' => $validated['website_link'] ?? null
    ]);

    return response()->json([
        'message' => 'Prospectus and related sales records updated successfully.',
        'updated_sales_records' => $prospectus->salesRecords()->count()
    ]);
}
}
