<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesLeadSource;

class SalesLeadSourceController extends Controller
{   
    public function fetchSaleSources(Request $request)
    {
        try {
             // Check if sales_lead_sources table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_lead_sources')) {
                return response()->json([]);
            }

            $query = SalesLeadSource::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('source_name', 'like', "%{$search}%");
            }

            $sources = $query->paginate(10);
            return response()->json($sources);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function index()
    {
        $leadSources = SalesLeadSource::paginate(10);
        return view('source');
    }

    public function update(Request $request, $id)
    {
        $lead = SalesLeadSource::findOrFail($id);
        $lead->source_name = $request->source_name;
        $lead->save();

        return response()->json(['message' => 'Lead updated']);
    }

    public function destroy($id)
    {
        $lead = SalesLeadSource::findOrFail($id);
        $lead->delete();

        return response()->json(['message' => 'Lead deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'source_name' => 'required|string|max:255']);

        SalesLeadSource::create([
            'source_name' => $request->source_name]);

        return response()->json(['success' => true]);
    }

    public function getsource(){
        try {
            // Check if sales_lead_sources table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_lead_sources')) {
                return response()->json([]);
            }

            $sources = SalesLeadSource::get(); 
            return response()->json($sources);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
