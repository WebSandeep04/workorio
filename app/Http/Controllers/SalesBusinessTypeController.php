<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesBusinessType;

class SalesBusinessTypeController extends Controller
{
    public function fetchSalesBusiness(Request $request)
    {
        try {
             // Check if sales_business_types table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_business_types')) {
                return response()->json([]);
            }

            $query = SalesBusinessType::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('business_name', 'like', "%{$search}%");
            }

            $business = $query->paginate(10);
            return response()->json($business);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function index()
    {
        $business = SalesBusinessType::paginate(10);
        return view('business');
    }

    public function update(Request $request, $id)
    {
        $business = SalesBusinessType::findOrFail($id);
        $business->business_name = $request->business_name;
        $business->save();

        return response()->json(['message' => 'business updated']);
    }

    public function destroy($id)
    {
        $business = SalesBusinessType::findOrFail($id);
        $business->delete();

        return response()->json(['message' => 'business deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255']);

        SalesBusinessType::create([
            'business_name' => $request->business_name]);

        return response()->json(['success' => true]);
    }

    public function getbusiness(){
        try {
            // Check if sales_business_types table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_business_types')) {
                return response()->json([]);
            }

            $businesses = SalesBusinessType::get(); 
            return response()->json($businesses);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
