<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\StatutoryRule;
use Illuminate\Http\Request;

class StatutoryRuleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(StatutoryRule::orderBy('type')->get());
        }
        return view('payroll.statutory');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:PF,ESI,PT,TDS',
            'employee_rate' => 'nullable|numeric|min:0',
            'employer_rate' => 'nullable|numeric|min:0',
            'salary_limit' => 'nullable|numeric|min:0',
            'calculate_on' => 'nullable|string'
        ]);

        $rule = StatutoryRule::create($validated);
        return response()->json(['success' => true, 'message' => 'Rule created successfully', 'data' => $rule]);
    }

    public function show($id)
    {
        $rule = StatutoryRule::findOrFail($id);
        return response()->json($rule);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:PF,ESI,PT,TDS',
            'employee_rate' => 'nullable|numeric|min:0',
            'employer_rate' => 'nullable|numeric|min:0',
            'salary_limit' => 'nullable|numeric|min:0',
            'calculate_on' => 'nullable|string'
        ]);

        $rule = StatutoryRule::findOrFail($id);
        $rule->update($validated);

        return response()->json(['success' => true, 'message' => 'Rule updated successfully', 'data' => $rule]);
    }

    public function destroy($id)
    {
        $rule = StatutoryRule::findOrFail($id);
        $rule->delete();
        
        return response()->json(['success' => true, 'message' => 'Rule deleted successfully']);
    }
}
