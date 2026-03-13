<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PettyCashData;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use App\Traits\TenantAwareStorage;

class PettyCashController extends Controller
{
    use TenantAwareStorage;
    public function index()
    {
        $departments = \App\Models\Department::all();
        return view('pettycash.index', compact('departments'));
    }

    public function fetch(Request $request)
    {
        $query = PettyCashData::with(['expense', 'department'])->orderBy('created_at', 'desc');

        // Search logic
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('expense', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('department', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter: Expense ID
        if ($request->has('expense_id') && !empty($request->expense_id)) {
            $query->where('expense_id', $request->expense_id);
        }

        // Filter: Department ID
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        // Filter: Status (1 = Approved, 0 = Pending)
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_approved', $request->status);
        }

        // Filter: Date Range
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Filter: Month (Current Year)
        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', date('Y'));
        }

        $data = $query->paginate(15);
        
        return response()->json($data);
    }

    public function getStats(Request $request)
    {
        $departmentId = $request->department_id;

        // Overall stats with optional Department filter
        $totalOpeningBalance = \App\Models\PettyOpeningBalance::query()
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->where('department_id', $departmentId);
            })
            ->sum('amount');

        $totalExpense = PettyCashData::query()
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->where('department_id', $departmentId);
            })
            ->sum('price');
            
        $remainingBalance = $totalOpeningBalance - $totalExpense;
        
        return response()->json([
            'total_opening_balance' => $totalOpeningBalance,
            'total_expense' => $totalExpense,
            'remaining_balance' => $remainingBalance,
        ]);
    }

    public function fetchExpenses()
    {
        $expenses = Expense::all();
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'department_id' => 'required|exists:departments,id',
            'price' => 'required|numeric|min:0',
            'is_approved' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:2048',
            'remark' => 'nullable|string',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // Use tenant-aware storage with isolation
            $attachmentPath = $this->storeTenantFile($request->file('attachment'), 'petty_cash_attachments');
        }

        PettyCashData::create([
            'expense_id' => $request->expense_id,
            'department_id' => $request->department_id,
            'price' => $request->price,
            'is_approved' => $request->is_approved ?? false,
            'attachment' => $attachmentPath,
            'remark' => $request->remark,
        ]);

        return response()->json(['success' => true, 'message' => 'Petty cash entry created successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'department_id' => 'required|exists:departments,id',
            'price' => 'required|numeric|min:0',
            'is_approved' => 'boolean',
            'remark' => 'nullable|string',
        ]);

        $entry = PettyCashData::findOrFail($id);
        $entry->update([
            'expense_id' => $request->expense_id,
            'department_id' => $request->department_id,
            'price' => $request->price,
            'is_approved' => $request->is_approved ?? $entry->is_approved,
            'remark' => $request->remark,
        ]);

        return response()->json(['success' => true, 'message' => 'Petty cash entry updated successfully.']);
    }

    public function destroy($id)
    {
        $entry = PettyCashData::findOrFail($id);
        $entry->delete();

        return response()->json(['success' => true, 'message' => 'Entry deleted successfully.']);
    }

    public function toggleApproval($id)
    {
        $entry = PettyCashData::findOrFail($id);
        $entry->is_approved = !$entry->is_approved;
        $entry->save();

        return response()->json(['success' => true, 'message' => 'Approval status updated.']);
    }

    public function approvals()
    {
        return view('pettycash.approvals');
    }

    public function approveBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:petty_cash_datas,id'
        ]);

        PettyCashData::whereIn('id', $request->ids)->update(['is_approved' => true]);

        return response()->json(['success' => true, 'message' => 'Selected entries approved.']);
    }

    public function departmentSummary()
    {
        $departments = \App\Models\Department::all();
        $summary = [];

        foreach ($departments as $department) {
            $openingBalance = \App\Models\PettyOpeningBalance::where('department_id', $department->id)->sum('amount');
            $totalExpense = PettyCashData::where('department_id', $department->id)->sum('price');
            $remaining = $openingBalance - $totalExpense;

            $summary[] = [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'opening_balance' => $openingBalance,
                'total_expense' => $totalExpense,
                'remaining' => $remaining
            ];
        }

        return view('pettycash.department-summary', compact('summary'));
    }

    public function departmentExpenses($id)
    {
        $department = \App\Models\Department::findOrFail($id);
        $expenses = PettyCashData::with('expense')
            ->where('department_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pettycash.department-expenses', compact('department', 'expenses'));
    }

}
