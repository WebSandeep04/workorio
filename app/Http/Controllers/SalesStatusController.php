<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesStatus;

class SalesStatusController extends Controller
{
    public function fetchSaleStatus(Request $request)
    {
        try {
             // Check if sales_status table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_status')) {
                return response()->json([]);
            }

            $query = SalesStatus::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('status_name', 'like', "%{$search}%");
            }

            $statuses = $query->paginate(10);
            return response()->json($statuses);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function index(){
        return view('status');
    }

    public function update(Request $request, $id)
    {
        $status = SalesStatus::findOrFail($id);
        
        $status->status_name = $request->status_name;
        $status->save();

        return response()->json(['message' => 'Status updated']);
    }

    public function destroy($id)
    {
        $status = SalesStatus::findOrFail($id);
        
        $status->delete();

        return response()->json(['message' => 'Status deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:255']);

        SalesStatus::create([
            'status_name' => $request->status_name]);

        return response()->json(['success' => true]);
    }

    public function getStatuses()
    {
        try {
            // Check if sales_status table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_status')) {
                return response()->json([]);
            }

            // For tenant users, get all statuses (no tenant filtering needed in tenant DB)
            $statuses = SalesStatus::all();
            return response()->json($statuses);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
