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
            'website_link' => 'nullable|string'
        ]);

        // Check uniqueness by contact_number if provided
        $contactNumber = trim($request->contact_number ?? '');
        if (!empty($contactNumber)) {
            // Strip common non-numeric formatting like spaces, dashes, parentheses to compare cleanly
            $cleanNumber = preg_replace('/[^0-9]/', '', $contactNumber);

            $existing = Prospectus::with(['state', 'city', 'businessType'])
                ->where(function($q) use ($contactNumber, $cleanNumber) {
                    $q->where('contact_number', $contactNumber);
                    if (!empty($cleanNumber)) {
                        $q->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(contact_number, ' ', ''), '-', ''), '(', ''), ')', '') = ?", [$cleanNumber]);
                    }
                })
                ->first();

            if ($existing) {
                return response()->json([
                    'error' => 'duplicate',
                    'message' => 'A prospect with this contact number already exists!',
                    'prospect' => [
                        'id' => $existing->id,
                        'prospectus_name' => $existing->prospectus_name,
                        'contact_person' => $existing->contact_person,
                        'contact_number' => $existing->contact_number,
                        'email' => $existing->email,
                        'address' => $existing->address,
                        'state' => $existing->state ? $existing->state->name : null,
                        'city' => $existing->city ? $existing->city->name : null,
                        'business_type' => $existing->businessType ? $existing->businessType->name : null,
                        'website_link' => $existing->website_link,
                    ]
                ], 422);
            }
        }

        $prospectus = Prospectus::create($validated);

        return response()->json([
            'message' => 'Prospectus saved successfully.',
            'id' => $prospectus->id,
            'prospectus' => $prospectus
        ]);
    }

public function getProspectus(Request $request){
    $query = Prospectus::query()
        ->whereNotNull('prospectus_name')
        ->whereRaw("TRIM(prospectus_name) != ''");

    $q = trim((string) $request->query('q', ''));
    if ($q !== '') {
        $like = '%' . $q . '%';
        $query->where(function ($sub) use ($like) {
            $sub->where('prospectus_name', 'like', $like)
                ->orWhere('contact_person', 'like', $like)
                ->orWhere('contact_number', 'like', $like)
                ->orWhere('email', 'like', $like);
        })->limit(60);
    }

    $prospectus = $query->orderBy('prospectus_name')->get();
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
