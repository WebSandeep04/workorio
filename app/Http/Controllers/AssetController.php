<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;

class AssetController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        
        $categories = AssetCategory::all(); // Pass categories for dropdown
        $statuses = \App\Models\AssetStatus::all();
        $suppliers = \App\Models\Supplier::all();
        $assetTypes = \App\Models\AssetType::all();
        return view('software-setup.assets.index', compact('categories', 'statuses', 'suppliers', 'assetTypes'));
    }

    public function fetch(Request $request)
    {
         if (!Schema::hasTable('assets')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = Asset::with(['category', 'supplier', 'assetType', 'currentAssignment.employee']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('asset_id', 'like', "%{$search}%")
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }

        if ($request->filled('employee_id')) {
            $query->whereHas('currentAssignment', function($q) use ($request) {
                $q->where('employee_id', $request->employee_id);
            });
        }

        if ($request->filled('from_date')) {
             $query->whereHas('currentAssignment', function($q) use ($request) {
                $q->whereDate('assigned_date', '>=', $request->from_date);
             });
        }

        if ($request->filled('to_date')) {
             $query->whereHas('currentAssignment', function($q) use ($request) {
                $q->whereDate('assigned_date', '<=', $request->to_date);
             });
        }

        $assets = $query->latest()->paginate(10);

        return response()->json($assets);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|string|unique:assets,asset_id',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_type_id' => 'nullable|exists:asset_types,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|string',
            'remark' => 'nullable|string',
            'custom_fields' => 'nullable|array' // Field values keyed by field ID or name? Name is better for display, but ID is stable. 
                                                // Actually, we store user input.
                                                // Let's expect 'custom_fields' as [field_name => value]
        ]);

        $asset = Asset::create([
            'asset_id' => $request->asset_id,
            'asset_category_id' => $request->asset_category_id,
            'asset_type_id' => $request->asset_type_id,
            'supplier_id' => $request->supplier_id,
            'status' => $request->status,
            'remark' => $request->remark,
            'custom_fields_data' => $request->custom_fields // Laravel casts this to json
        ]);

        return response()->json(['success' => true, 'message' => 'Asset created successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_id' => 'required|string|unique:assets,asset_id,' . $id,
            'asset_category_id' => 'required|exists:asset_categories,id',
            'asset_type_id' => 'nullable|exists:asset_types,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'status' => 'required|string',
            'remark' => 'nullable|string',
            'custom_fields' => 'nullable|array'
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update([
            'asset_id' => $request->asset_id,
            'asset_category_id' => $request->asset_category_id,
            'asset_type_id' => $request->asset_type_id,
            'supplier_id' => $request->supplier_id,
            'status' => $request->status,
            'remark' => $request->remark,
            'custom_fields_data' => $request->custom_fields
        ]);

        return response()->json(['success' => true, 'message' => 'Asset updated successfully']);
    }

    public function destroy($id)
    {
        Asset::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Asset deleted successfully']);
    }
    
    public function show($id) {
         return response()->json(Asset::with(['category'])->findOrFail($id));
    }


    
    // Helper to get generic category fields if needed, 
    // though the frontend can use AssetCategoryController@show
    
    public function history($id)
    {
        // Get all assignments for this asset (active and historic)
        // We include the employee (who had it), and logs (changes to the assignment)
        $assignments = \App\Models\AssetAssignment::where('asset_id', $id)
            ->with(['employee', 'asset']) // Load employee
            ->orderBy('assigned_date', 'desc')
            ->get();
            
        // For each assignment, we can also see logs if manual changes happened
        // But mainly the list of assignments IS the history.
        // We can also fetch pure logs if needed, but let's send assignments structure.
        
        // Let's also fetch logs separately if the user wants purely "Log" table rows
        // But since logs are children of assignments, better to attach them.
        
        // Actually, let's look at the screenshot requirement.
        // It shows a table of logs. 
        // We will return a merged timeline or just the raw data.
        
        foreach($assignments as $assignment) {
             // attach logs manually or via relationship if defined
             $assignment->logs = \App\Models\AssetAssignmentLog::where('asset_assignment_id', $assignment->id)
                                ->with(['previousEmployee', 'newEmployee'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        }
        
        return response()->json($assignments);
    }
}
