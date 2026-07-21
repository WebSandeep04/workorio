<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SalaryComponent::query();
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%");
            }
            
            $components = $query->latest()->paginate(10);
            return response()->json($components);
        }
        
        return view('payroll.components');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage,formula,rule',
            'is_active' => 'required|boolean'
        ]);

        $component = SalaryComponent::create($validated);
        return response()->json(['success' => true, 'data' => $component]);
    }

    public function update(Request $request, $id)
    {
        $component = SalaryComponent::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction,employer_contribution',
            'calculation_type' => 'required|in:fixed,percentage,formula,rule',
            'is_active' => 'required|boolean'
        ]);

        $component->update($validated);
        return response()->json(['success' => true, 'data' => $component]);
    }

    public function destroy($id)
    {
        $component = SalaryComponent::findOrFail($id);
        $component->delete();
        return response()->json(['success' => true]);
    }
}
