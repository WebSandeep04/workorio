<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PettyCashData;
use App\Models\Expense;
use App\Models\Department;
use App\Models\PettyOpeningBalance;
use App\Traits\TenantAwareStorage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PettyCashApiController extends Controller
{
    use TenantAwareStorage;

    public function getStats(Request $request)
    {
        $departmentId = $request->department_id;

        $totalOpeningBalance = PettyOpeningBalance::query()
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
            'success' => true,
            'data' => [
                'total_opening_balance' => (float)$totalOpeningBalance,
                'total_expense' => (float)$totalExpense,
                'remaining_balance' => (float)$remainingBalance,
            ]
        ]);
    }

    public function getFormOptions()
    {
        $departments = Department::select('id', 'name')->get();
        $expenses = Expense::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'departments' => $departments,
                'expenses' => $expenses,
            ]
        ]);
    }

    public function index(Request $request)
    {
        $query = PettyCashData::with(['expense:id,name', 'department:id,name'])
            ->orderBy('created_at', 'desc');

        // Live Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('expense', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhereHas('department', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                })->orWhere('remark', 'like', "%{$search}%");
            });
        }

        // Specific Filters
        if ($request->filled('expense_id')) {
            $query->where('expense_id', $request->expense_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_approved', (bool)$request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month)
                  ->whereYear('created_at', date('Y'));
        }

        $entries = $query->paginate(20);

        // Build absolute path URLs for attachments if present
        $entries->getCollection()->transform(function ($entry) {
            if ($entry->attachment) {
                $entry->attachment_url = asset('storage/' . $entry->attachment);
            } else {
                $entry->attachment_url = null;
            }
            return $entry;
        });

        return response()->json($entries);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'department_id' => 'required|exists:departments,id',
            'price' => 'required|numeric|min:0',
            'is_approved' => 'boolean',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120', // 5MB limit
            'remark' => 'nullable|string',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->storeTenantFile($request->file('attachment'), 'petty_cash_attachments');
        }

        $entry = PettyCashData::create([
            'expense_id' => $request->expense_id,
            'department_id' => $request->department_id,
            'price' => $request->price,
            'is_approved' => $request->is_approved ?? false,
            'attachment' => $attachmentPath,
            'remark' => $request->remark,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Petty cash entry recorded successfully.',
            'data' => $entry
        ]);
    }

    public function update(Request $request, $id)
    {
        $entry = PettyCashData::findOrFail($id);

        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'department_id' => 'required|exists:departments,id',
            'price' => 'required|numeric|min:0',
            'is_approved' => 'boolean',
            'remark' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
        ]);

        $attachmentPath = $entry->attachment;
        if ($request->hasFile('attachment')) {
            // optionally delete old attachment
            $attachmentPath = $this->storeTenantFile($request->file('attachment'), 'petty_cash_attachments');
        }

        $entry->update([
            'expense_id' => $request->expense_id,
            'department_id' => $request->department_id,
            'price' => $request->price,
            'is_approved' => $request->is_approved ?? $entry->is_approved,
            'remark' => $request->remark,
            'attachment' => $attachmentPath
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry updated successfully.'
        ]);
    }

    public function toggleApproval($id)
    {
        $entry = PettyCashData::findOrFail($id);
        $entry->is_approved = !$entry->is_approved;
        $entry->save();

        return response()->json([
            'success' => true,
            'message' => $entry->is_approved ? 'Entry marked as Approved.' : 'Entry marked as Pending.',
            'data' => ['is_approved' => $entry->is_approved]
        ]);
    }

    public function destroy($id)
    {
        $entry = PettyCashData::findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Petty cash record deleted successfully.'
        ]);
    }
}
