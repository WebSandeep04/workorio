<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetType;

class AssetTypeController extends Controller
{
    public function fetch(Request $request)
    {
        // Gracefully handle missing table if migration hasn't stuck yet
        if (!\Illuminate\Support\Facades\Schema::hasTable('asset_types')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = AssetType::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $assetTypes = $query->paginate(10);
        return response()->json($assetTypes);
    }

    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        
        return view('software-setup.asset-type.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_types,name,' . $id
        ]);

        $assetType = AssetType::findOrFail($id);
        $assetType->name = $request->name;
        $assetType->save();

        return response()->json(['message' => 'Asset type updated']);
    }

    public function destroy($id)
    {
        $assetType = AssetType::findOrFail($id);
        $assetType->delete();

        return response()->json(['message' => 'Asset type deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_types,name'
        ]);

        AssetType::create([
            'name' => $request->name
        ]);

        return response()->json(['success' => true]);
    }
}
