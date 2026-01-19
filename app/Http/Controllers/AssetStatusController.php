<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetStatus;
use Illuminate\Support\Facades\Schema;

class AssetStatusController extends Controller
{
    public function fetch(Request $request)
    {
        // Gracefully handle missing table if migration hasn't stuck yet
        if (!Schema::hasTable('asset_statuses')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = AssetStatus::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $statuses = $query->paginate(10);
        return response()->json($statuses);
    }

    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        
        return view('software-setup.asset-status.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_statuses,name,' . $id
        ]);

        $status = AssetStatus::findOrFail($id);
        $status->name = $request->name;
        $status->save();

        return response()->json(['message' => 'Asset status updated']);
    }

    public function destroy($id)
    {
        $status = AssetStatus::findOrFail($id);
        $status->delete();

        return response()->json(['message' => 'Asset status deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_statuses,name'
        ]);

        AssetStatus::create([
            'name' => $request->name
        ]);

        return response()->json(['success' => true]);
    }
}
