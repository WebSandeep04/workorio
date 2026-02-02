<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    public function index()
    {
        return view('places.index');
    }

    public function list()
    {
        $places = DB::table('places')->orderBy('id', 'desc')->get();
        return response()->json($places);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placename' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:0',
        ]);

        DB::table('places')->insert([
            'placename' => $validated['placename'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius' => $validated['radius'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Place created successfully']);
    }

    public function update(Request $request, $placeId)
    {
        $validated = $request->validate([
            'placename' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'required|integer|min:0',
        ]);

        DB::table('places')->where('id', $placeId)->update([
            'placename' => $validated['placename'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'radius' => $validated['radius'],
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Place updated successfully']);
    }

    public function destroy($placeId)
    {
        DB::table('places')->where('id', $placeId)->delete();
        return response()->json(['success' => true, 'message' => 'Place deleted successfully']);
    }
}
