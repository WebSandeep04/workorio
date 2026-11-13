<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PettyCashData;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use App\Traits\TenantAwareStorage;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $totalPendingCount = PettyCashData::query()
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->where('department_id', $departmentId);
            })
            ->where('is_approved', false)
            ->count();

        $totalPendingAmount = PettyCashData::query()
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->where('department_id', $departmentId);
            })
            ->where('is_approved', false)
            ->sum('price');
        
        return response()->json([
            'total_opening_balance' => $totalOpeningBalance,
            'total_expense' => $totalExpense,
            'remaining_balance' => $remainingBalance,
            'total_pending_count' => $totalPendingCount,
            'total_pending_amount' => $totalPendingAmount,
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

    public function departmentExpenses(Request $request, $id = null)
    {
        $departments = \App\Models\Department::all();
        $query = PettyCashData::with(['expense', 'department'])->orderBy('created_at', 'desc');

        $selectedDeptId = $id ?: $request->department_id;

        if ($selectedDeptId) {
            $query->where('department_id', $selectedDeptId);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', date('Y'));
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_approved', $request->status);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('expense', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhereHas('department', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('remark', 'like', "%{$search}%");
            });
        }

        $totalAmount = (clone $query)->sum('price');
        $paginated = $query->paginate(15);

        // Stats for summary cards
        $totalOpeningBalance = \App\Models\PettyOpeningBalance::query()
            ->when($selectedDeptId, function($q) use ($selectedDeptId) {
                return $q->where('department_id', $selectedDeptId);
            })
            ->sum('amount');
        $remainingBalance = $totalOpeningBalance - $totalAmount;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total_amount' => $totalAmount,
                'total_opening_balance' => $totalOpeningBalance,
                'remaining_balance' => $remainingBalance,
                'data' => $paginated->items(),
                'links' => $paginated->linkCollection()->toArray(),
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage()
            ]);
        }

        $expenses = $paginated;

        if ($selectedDeptId) {
            $department = \App\Models\Department::find($selectedDeptId);
            $title = ($department ? $department->name : 'Department') . ' - Expense History';
        } else {
            $department = null;
            $title = 'All Departments - Expense History';
        }

        return view('pettycash.department-expenses', compact('department', 'departments', 'expenses', 'title', 'selectedDeptId', 'totalAmount', 'totalOpeningBalance', 'remainingBalance'));
    }

    public function departmentOpeningBalances(Request $request, $id = null)
    {
        $departments = \App\Models\Department::all();
        $query = \App\Models\PettyOpeningBalance::with('department')->orderBy('created_at', 'desc');

        $selectedDeptId = $id ?: $request->department_id;

        if ($selectedDeptId) {
            $query->where('department_id', $selectedDeptId);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', date('Y'));
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('department', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('amount', 'like', "%{$search}%");
            });
        }

        $totalAmount = (clone $query)->sum('amount');
        $paginated = $query->paginate(15);

        // Stats for summary cards
        $totalExpense = PettyCashData::query()
            ->when($selectedDeptId, function($q) use ($selectedDeptId) {
                return $q->where('department_id', $selectedDeptId);
            })
            ->sum('price');
        $remainingBalance = $totalAmount - $totalExpense;

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total_amount' => $totalAmount,
                'total_opening_balance' => $totalAmount,
                'total_expense' => $totalExpense,
                'remaining_balance' => $remainingBalance,
                'data' => $paginated->items(),
                'links' => $paginated->linkCollection()->toArray(),
                'current_page' => $paginated->currentPage(),
                'per_page' => $paginated->perPage()
            ]);
        }

        $balances = $paginated;

        if ($selectedDeptId) {
            $department = \App\Models\Department::find($selectedDeptId);
            $title = ($department ? $department->name : 'Department') . ' - Opening Balance History';
        } else {
            $department = null;
            $title = 'All Departments - Opening Balance History';
        }

        return view('pettycash.department-opening-balances', compact('department', 'departments', 'balances', 'title', 'selectedDeptId', 'totalAmount', 'totalExpense', 'remainingBalance'));
    }

    public function openingBalanceHistory(Request $request)
    {
        $departmentId = $request->department_id;

        $query = \App\Models\PettyOpeningBalance::with('department')
            ->orderBy('created_at', 'desc');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $balances = $query->get();
        $totalAmount = $balances->sum('amount');

        return response()->json([
            'success' => true,
            'total_amount' => $totalAmount,
            'data' => $balances
        ]);
    }

    private function buildFilteredQuery(Request $request)
    {
        $query = PettyCashData::with(['expense', 'department'])->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('expense', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhereHas('department', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })->orWhere('remark', 'like', "%{$search}%");
            });
        }

        if ($request->has('expense_id') && !empty($request->expense_id)) {
            $query->where('expense_id', $request->expense_id);
        }

        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_approved', $request->status);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', date('Y'));
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $items = $this->buildFilteredQuery($request)->get();
        $filename = 'petty_cash_report_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['#', 'Date', 'Wallet / Department', 'Expense Name', 'Price (INR)', 'Remark', 'Status'];

        $callback = function() use ($items, $columns) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens it with proper encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            $totalPrice = 0;
            foreach ($items as $index => $item) {
                $price = (float)$item->price;
                $totalPrice += $price;

                fputcsv($file, [
                    $index + 1,
                    $item->created_at ? $item->created_at->format('d M Y') : '-',
                    $item->department ? $item->department->name : '-',
                    $item->expense ? $item->expense->name : 'N/A',
                    number_format($price, 2, '.', ''),
                    $item->remark ?? '-',
                    $item->is_approved ? 'Approved' : 'Pending'
                ]);
            }

            // Total row
            fputcsv($file, ['', '', '', 'Total:', number_format($totalPrice, 2, '.', ''), '', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $items = $this->buildFilteredQuery($request)->get();
        $totalExpense = $items->sum('price');

        $departmentId = $request->department_id;
        $totalOpeningBalance = \App\Models\PettyOpeningBalance::query()
            ->when($departmentId, function($q) use ($departmentId) {
                return $q->where('department_id', $departmentId);
            })
            ->sum('amount');

        $remainingBalance = $totalOpeningBalance - $totalExpense;

        $deptName = 'All Wallets';
        if ($departmentId) {
            $dept = \App\Models\Department::find($departmentId);
            if ($dept) $deptName = $dept->name;
        }
        $title = "Petty Cash Report ({$deptName})";

        $pdf = Pdf::loadView('pettycash.pdf', compact(
            'items',
            'title',
            'totalOpeningBalance',
            'totalExpense',
            'remainingBalance'
        ))->setPaper('a4', 'landscape');

        $filename = 'petty_cash_report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }
}
