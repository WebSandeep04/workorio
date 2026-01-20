<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

use App\Models\AssetStatus;

class AssetManagementController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::all();
        $employees = Employee::where('status', 'Active')->get();
        $statuses = AssetStatus::all();
        return view('admin.asset-management.index', compact('categories', 'employees', 'statuses'));
    }

    public function fetch(Request $request)
    {
        if (!Schema::hasTable('asset_assignments')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = AssetAssignment::with(['asset', 'employee', 'asset.category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('asset', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_id', 'like', "%{$search}%");
                })->orWhereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('assigned_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
             $query->whereDate('assigned_date', '<=', $request->to_date);
        }

        if ($request->filled('category_id')) {
             $query->whereHas('asset', function($q) use ($request) {
                 $q->where('asset_category_id', $request->category_id);
             });
        }

        if ($request->filled('employee_id')) {
             $query->where('employee_id', $request->employee_id);
        }

        $assignments = $query->latest()->paginate(10);
        return response()->json($assignments);
    }

    public function store(Request $request) 
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_date' => 'required|date',
            'return_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        $assignment = AssetAssignment::create([
            'asset_id' => $request->asset_id,
            'employee_id' => $request->employee_id,
            'assigned_date' => $request->assigned_date,
            'return_date' => $request->return_date,
            'description' => $request->description,
            'status' => 'assigned'
        ]);

        // Update asset status
        Asset::where('id', $request->asset_id)->update(['status' => 'Assigned']);

        return response()->json(['success' => true, 'message' => 'Asset assigned successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
             'employee_id' => 'required|exists:employees,id',
             'return_date' => 'nullable|date',
             'status' => 'required|string',
             'description' => 'nullable|string'
        ]);

        $assignment = AssetAssignment::findOrFail($id);

        if ($assignment->employee_id != $request->employee_id) {
            \App\Models\AssetAssignmentLog::create([
                'asset_assignment_id' => $assignment->id,
                'previous_employee_id' => $assignment->employee_id,
                'new_employee_id' => $request->employee_id,
                'updated_by' => auth()->id() ?? null,
            ]);
        }

        $assignment->update([
             'employee_id' => $request->employee_id,
             'return_date' => $request->return_date,
             'status' => $request->status,
             'description' => $request->description
        ]);

        if($request->status == 'returned') {
             Asset::where('id', $assignment->asset_id)->update(['status' => 'Available']);
        }

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully']);
    }

    public function destroy($id)
    {
        $assignment = AssetAssignment::findOrFail($id);
        // Revert asset status to available
        Asset::where('id', $assignment->asset_id)->update(['status' => 'Available']);
        $assignment->delete();
        return response()->json(['success' => true, 'message' => 'Assignment deleted successfully']);
    }

    public function getAssetsByCategory(Request $request)
    {
        $categoryId = $request->category_id;
        $assets = Asset::where('asset_category_id', $categoryId)
                       ->where('status', 'Available') // Only fetch available assets
                       ->select('id', 'name', 'asset_id')
                       ->get();
        return response()->json($assets);
    }

    public function show($id)
    {
        $assignment = AssetAssignment::with(['asset', 'employee'])->findOrFail($id);
        return response()->json($assignment);
    }
    public function getSummaryStats(Request $request)
    {
        // 1. Total Assets
        $totalQuery = Asset::query();
        if($request->filled('category_id')) {
            $totalQuery->where('asset_category_id', $request->category_id);
        }
        // If employee selected, usually we only care about assigned assets, but 'Total Assets' might mean 'All assets matching criteria'
        // We'll leave Total Assets filtered only by Category to represent "Inventory Size"
        $totalAssets = $totalQuery->count();

        // 2. Available Assets
        $availableQuery = Asset::where('status', 'Available');
        if($request->filled('category_id')) {
            $availableQuery->where('asset_category_id', $request->category_id);
        }
        $availableAssets = $availableQuery->count();

        // 3. Assigned Assets (Active Assignments)
        $assignedQuery = AssetAssignment::where('status', 'assigned');
        
        if($request->filled('category_id')) {
             $assignedQuery->whereHas('asset', function($q) use ($request){
                 $q->where('asset_category_id', $request->category_id);
             });
        }
        if($request->filled('employee_id')) {
             $assignedQuery->where('employee_id', $request->employee_id);
        }
        if ($request->filled('from_date')) {
            $assignedQuery->whereDate('assigned_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
             $assignedQuery->whereDate('assigned_date', '<=', $request->to_date);
        }
        
        $assignedAssets = $assignedQuery->count();

        // 4. Return Due
        $returnDueQuery = AssetAssignment::where('status', 'assigned')
                                    ->whereDate('return_date', '<', now());

        if($request->filled('category_id')) {
             $returnDueQuery->whereHas('asset', function($q) use ($request){
                 $q->where('asset_category_id', $request->category_id);
             });
        }
        if($request->filled('employee_id')) {
             $returnDueQuery->where('employee_id', $request->employee_id);
        }
        // Date range usually applies to assignment date, but we can respect it here too if desired.
        // For Return Due, existing overdue is what matters regardless of when it was assigned, usually.
        // But for consistency let's filter by assignment window.
         if ($request->filled('from_date')) {
            $returnDueQuery->whereDate('assigned_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
             $returnDueQuery->whereDate('assigned_date', '<=', $request->to_date);
        }

        $returnDue = $returnDueQuery->count();

        return response()->json([
            'total_assets' => $totalAssets,
            'available_assets' => $availableAssets,
            'assigned_assets' => $assignedAssets,
            'return_due' => $returnDue
        ]);
    }
}
