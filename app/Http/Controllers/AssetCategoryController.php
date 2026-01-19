<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetCategory;
use Illuminate\Support\Facades\Schema;

class AssetCategoryController extends Controller
{
    public function fetch(Request $request)
    {
        // Gracefully handle missing table if migration hasn't stuck yet
        if (!Schema::hasTable('asset_categories')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = AssetCategory::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->paginate(10);
        return response()->json($categories);
    }

    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        
        return view('software-setup.asset-category.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $id
        ]);

        $category = AssetCategory::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        return response()->json(['message' => 'Asset category updated']);
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Asset category deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name'
        ]);

        AssetCategory::create([
            'name' => $request->name
        ]);

        return response()->json(['success' => true]);
    }
}
