<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SalaryStructure::with('components');
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('salary_type', 'like', "%{$search}%");
            }
            
            $structures = $query->latest()->paginate(10);
            return response()->json($structures);
        }
        
        $components = SalaryComponent::where('is_active', true)->get();
        return view('payroll.structures', compact('components'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'salary_type' => 'required|in:fixed,structured',
            'components' => 'nullable|array',
            'components.*.id' => 'required|exists:salary_components,id',
            'components.*.value' => 'nullable|numeric',
            'components.*.formula' => 'nullable|string'
        ]);

        $structure = SalaryStructure::create([
            'name' => $validated['name'],
            'salary_type' => $validated['salary_type']
        ]);

        if (!empty($validated['components'])) {
            $syncData = [];
            foreach ($validated['components'] as $comp) {
                $syncData[$comp['id']] = [
                    'value' => $comp['value'] ?? null,
                    'formula' => $comp['formula'] ?? null
                ];
            }
            $structure->components()->sync($syncData);
        }

        return response()->json(['success' => true, 'data' => $structure->load('components')]);
    }

    public function update(Request $request, $id)
    {
        $structure = SalaryStructure::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'salary_type' => 'required|in:fixed,structured',
            'components' => 'nullable|array',
            'components.*.id' => 'required|exists:salary_components,id',
            'components.*.value' => 'nullable|numeric',
            'components.*.formula' => 'nullable|string'
        ]);

        $structure->update([
            'name' => $validated['name'],
            'salary_type' => $validated['salary_type']
        ]);

        if (isset($validated['components'])) {
            $syncData = [];
            foreach ($validated['components'] as $comp) {
                $syncData[$comp['id']] = [
                    'value' => $comp['value'] ?? null,
                    'formula' => $comp['formula'] ?? null
                ];
            }
            $structure->components()->sync($syncData);
        } else {
            $structure->components()->detach();
        }

        return response()->json(['success' => true, 'data' => $structure->load('components')]);
    }

    public function destroy($id)
    {
        $structure = SalaryStructure::findOrFail($id);
        $structure->components()->detach();
        $structure->delete();
        return response()->json(['success' => true]);
    }
}
