<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class AssetManagementController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }

        $categories = AssetCategory::all();
        $employees = Employee::select('id', 'name', 'employee_code')->get();
        return view('admin.asset-management.index', compact('categories', 'employees'));
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
            $query->whereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_id', 'like', "%{$search}%");
            })->orWhereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
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
}
