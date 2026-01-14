<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChecklistController extends Controller
{
    public function fetch(Request $request)
    {
        $query = DB::table('checklists');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $rows = $query->orderBy('name')->paginate(10);
        return response()->json($rows);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        DB::table('checklists')->insert([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $count = DB::table('checklists')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('checklists')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }

    // Options
    public function fetchOptions(Request $request)
    {
        $request->validate(['checklist_id' => 'required|integer|exists:checklists,id']);
        $rows = DB::table('checklist_options')
            ->where('checklist_id', (int)$request->checklist_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        return response()->json($rows);
    }

    public function storeOption(Request $request)
    {
        $validated = $request->validate([
            'checklist_id' => 'required|integer|exists:checklists,id',
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        DB::table('checklist_options')->insert([
            'checklist_id' => (int)$validated['checklist_id'],
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function updateOption(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $count = DB::table('checklist_options')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? 1,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0]);
    }

    public function destroyOption($id)
    {
        $deleted = DB::table('checklist_options')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}


