<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Schema;

class LeaveTypeController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        return view('software-setup.leave-type.index');
    }

    public function fetch(Request $request)
    {
        if (!Schema::hasTable('leave_types')) {
             return response()->json(['current_page' => 1, 'data' => [], 'total' => 0, 'last_page' => 1]);
        }

        $query = LeaveType::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->orderBy('name')->paginate(10));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('leave_types')) {
             return response()->json(['success' => false, 'message' => 'Please run migrations first.'], 500);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:leave_types,name',
            'is_paid' => 'boolean',
            'is_deductible' => 'boolean',
            'is_short_leave' => 'boolean',
            'is_restricted' => 'boolean',
            'full_day_weight' => 'required|numeric|min:0',
            'half_day_weight' => 'required|numeric|min:0',
            'allow_half_day' => 'boolean',
            'quota_type' => 'required|in:monthly,yearly',
            'color_code' => 'nullable|string|max:50',
            'status' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        LeaveType::create($request->only('name', 'is_paid', 'is_deductible', 'is_short_leave', 'is_restricted', 'full_day_weight', 'half_day_weight', 'allow_half_day', 'quota_type', 'color_code', 'status', 'description'));
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:leave_types,name,' . $id,
            'is_paid' => 'boolean',
            'is_deductible' => 'boolean',
            'is_short_leave' => 'boolean',
            'is_restricted' => 'boolean',
            'full_day_weight' => 'required|numeric|min:0',
            'half_day_weight' => 'required|numeric|min:0',
            'allow_half_day' => 'boolean',
            'quota_type' => 'required|in:monthly,yearly',
            'color_code' => 'nullable|string|max:50',
            'status' => 'boolean',
            'description' => 'nullable|string|max:1000'
        ]);

        LeaveType::findOrFail($id)->update($request->only('name', 'is_paid', 'is_deductible', 'is_short_leave', 'is_restricted', 'full_day_weight', 'half_day_weight', 'allow_half_day', 'quota_type', 'color_code', 'status', 'description'));
        return response()->json(['success' => true, 'message' => 'Leave type updated']);
    }

    public function destroy($id)
    {
        LeaveType::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Leave type deleted']);
    }
}
