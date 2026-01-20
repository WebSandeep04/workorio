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

    public function show($id)
    {
        return response()->json(AssetCategory::with('fields')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $id,
            'fields' => 'nullable|array',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|in:text,dropdown',
            'fields.*.options' => 'nullable|array' // allow array of strings
        ]);

        $category = AssetCategory::findOrFail($id);
        $category->name = $request->name;
        $category->save();

        // Sync fields
        $currentFieldIds = $category->fields->pluck('id')->toArray();
        $submittedFieldIds = [];

        if ($request->has('fields')) {
            foreach ($request->fields as $fieldData) {
                if (isset($fieldData['id']) && in_array($fieldData['id'], $currentFieldIds)) {
                    // Update
                    $category->fields()->where('id', $fieldData['id'])->update([
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'options' => isset($fieldData['options']) ? json_encode($fieldData['options']) : null
                    ]);
                    $submittedFieldIds[] = $fieldData['id'];
                } else {
                    // Create
                    $newField = $category->fields()->create([
                        'name' => $fieldData['name'],
                        'type' => $fieldData['type'],
                        'options' => $fieldData['options'] ?? null
                    ]);
                    $submittedFieldIds[] = $newField->id;
                }
            }
        }

        // Delete removed
        $fieldsToDelete = array_diff($currentFieldIds, $submittedFieldIds);
        if (!empty($fieldsToDelete)) {
            $category->fields()->whereIn('id', $fieldsToDelete)->delete();
        }

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
            'name' => 'required|string|max:255|unique:asset_categories,name',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'required|in:text,dropdown',
            'fields.*.options' => 'nullable|array'
        ]);

        $category = AssetCategory::create([
            'name' => $request->name
        ]);

        if ($request->has('fields')) {
            foreach ($request->fields as $field) {
                $category->fields()->create([
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'options' => $field['options'] ?? null
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
    public function manageFields($id)
    {
        $category = AssetCategory::findOrFail($id);
        return view('software-setup.asset-category.fields', compact('category'));
    }
}
