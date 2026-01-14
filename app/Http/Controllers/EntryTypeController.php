<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntryType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EntryTypeController extends Controller
{
    public function index()
    {
        return view('entry-type.index');
    }

    public function fetch(Request $request)
    {
        try {
            // Check if entry_types table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('entry_types')) {
                return response()->json(['data' => []]);
            }

            $query = EntryType::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            }

            $entryTypes = $query->orderBy('name')->paginate(10);
            
            return response()->json($entryTypes);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }

    public function create()
    {
        return view('entry-type.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'working_hours' => 'required|integer|min:0|max:24',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $entryType = EntryType::create([
            'name' => $request->name,
            'working_hours' => $request->working_hours,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry Type created successfully!',
            'data' => $entryType
        ]);
    }

    public function edit($id)
    {
        $entryType = EntryType::findOrFail($id);
        
        return view('entry-type.edit', compact('entryType'));
    }

    public function update(Request $request, $id)
    {
        $entryType = EntryType::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'working_hours' => 'required|integer|min:0|max:24',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $entryType->update([
            'name' => $request->name,
            'working_hours' => $request->working_hours,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry Type updated successfully!',
            'data' => $entryType
        ]);
    }

    public function destroy($id)
    {
        $entryType = EntryType::findOrFail($id);

        // Check if this entry type is being used in worklogs
        if ($entryType->worklogs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete Entry Type. It is being used in worklog entries.'
            ], 422);
        }

        $entryType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entry Type deleted successfully!'
        ]);
    }
}
