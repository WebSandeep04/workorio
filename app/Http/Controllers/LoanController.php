<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = \App\Models\Loan::with(['employee', 'installments'])->latest()->get();
        $employees = \App\Models\Employee::where('status', 'active')->get();
        return view('loans.index', compact('loans', 'employees'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::all();
        return view('loans.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'emi_amount' => 'required|numeric|min:1|max:' . $request->amount,
            'reason' => 'nullable|string',
            'start_month' => [
                'required',
                'date_format:Y-m',
                function ($attribute, $value, $fail) {
                    $nextMonth = \Carbon\Carbon::now()->startOfMonth()->addMonth();
                    $inputMonth = \Carbon\Carbon::createFromFormat('Y-m', $value)->startOfMonth();
                    if ($inputMonth->lt($nextMonth)) {
                        $fail('The start month must be at least from the next month.');
                    }
                },
            ],
        ]);

        $employee = \App\Models\Employee::with(['salaries', 'employmentTypeRelation'])->findOrFail($request->employee_id);
        
        $latestSalary = $employee->salaries->first();
        $grossSalary = $latestSalary ? $latestSalary->gross_salary : 0;
        $maxLoanPercentage = $employee->employmentTypeRelation ? $employee->employmentTypeRelation->max_loan_percentage : 0;

        $maxAllowedLoan = ($grossSalary * $maxLoanPercentage) / 100;

        if ($request->amount > $maxAllowedLoan) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['amount' => ["Loan amount exceeds maximum allowed limit ({$maxAllowedLoan}) for this employee type."]]
                ], 422);
            }
            return back()->withErrors(['amount' => "Loan amount exceeds maximum allowed limit ({$maxAllowedLoan}) for this employee type."])->withInput();
        }

        // total_installments will be updated after calculation
        $loan = \App\Models\Loan::create([
            'employee_id' => $employee->id,
            'amount' => $request->amount,
            'total_installments' => 0,
            'installment_amount' => $request->emi_amount,
            'status' => 'pending',
            'reason' => $request->reason,
        ]);

        $startMonth = \Carbon\Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
        $remainingAmount = $request->amount;
        $i = 1;

        while ($remainingAmount > 0) {
            $currentInstAmount = min($remainingAmount, $request->emi_amount);

            \App\Models\LoanInstallment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_month' => $startMonth->copy()->addMonths($i - 1)->format('Y-m'),
                'amount' => $currentInstAmount,
                'status' => 'pending',
            ]);

            $remainingAmount -= $currentInstAmount;
            $i++;
        }

        // Update the actual calculated total installments
        $loan->update(['total_installments' => $i - 1]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Loan request created successfully and is pending approval.']);
        }

        return redirect()->route('loans.index')->with('success', 'Loan request created successfully and is pending approval.');
    }

    public function adminIndex()
    {
        return view('loans.admin_index');
    }

    public function manageIndex()
    {
        return view('loans.manage_index');
    }

    public function adminFetch(Request $request)
    {
        $loans = \App\Models\Loan::with(['employee', 'installments'])->latest()->get();
        $loans->map(function ($loan) {
            $loan->remaining_balance = $loan->remainingBalance();
            return $loan;
        });
        return response()->json(['data' => $loans]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $loan = \App\Models\Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending loans can be updated.']);
        }

        $loan->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Loan status updated successfully.']);
    }

    public function skipInstallment(Request $request, $id)
    {
        $installment = \App\Models\LoanInstallment::findOrFail($id);

        $request->validate([
            'skip_strategy' => 'required|in:add_to_next,extend_period'
        ]);

        if ($installment->status !== 'pending') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only pending installments can be skipped.']);
            }
            return back()->withErrors(['error' => 'Only pending installments can be skipped.']);
        }

        $loan = $installment->loan;

        $installment->update([
            'status' => 'skipped',
            'skip_strategy' => $request->skip_strategy,
        ]);

        if ($request->skip_strategy === 'add_to_next') {
            $nextInstallment = $loan->installments()
                ->where('status', 'pending')
                ->where('id', '!=', $installment->id)
                ->orderBy('installment_number', 'asc')
                ->first();

            if ($nextInstallment) {
                $nextInstallment->update([
                    'amount' => $nextInstallment->amount + $installment->amount
                ]);
            } else {
                // If there is no next installment, extend period instead
                $this->extendPeriod($loan, $installment->amount);
            }
        } elseif ($request->skip_strategy === 'extend_period') {
            $this->extendPeriod($loan, $installment->amount);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Installment skipped successfully.']);
        }
        return back()->with('success', 'Installment skipped successfully.');
    }

    private function extendPeriod($loan, $amount)
    {
        $lastInstallment = $loan->installments()->orderBy('installment_number', 'desc')->first();
        
        $newInstallmentNumber = $lastInstallment ? $lastInstallment->installment_number + 1 : 1;
        
        $lastDueMonth = $lastInstallment ? \Carbon\Carbon::createFromFormat('Y-m', $lastInstallment->due_month)->startOfMonth() : now()->startOfMonth();
        $newDueMonth = $lastDueMonth->addMonth()->format('Y-m');

        \App\Models\LoanInstallment::create([
            'loan_id' => $loan->id,
            'installment_number' => $newInstallmentNumber,
            'due_month' => $newDueMonth,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $loan->increment('total_installments');
    }

    public function list(Request $request)
    {
        $loans = \App\Models\Loan::with(['employee', 'installments'])->latest()->get();
        $loans->map(function ($loan) {
            $loan->remaining_balance = $loan->remainingBalance();
            return $loan;
        });
        return response()->json(['data' => $loans]);
    }

    public function destroy($id)
    {
        $loan = \App\Models\Loan::findOrFail($id);
        
        if ($loan->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending loans can be deleted.'], 403);
        }

        $loan->delete();

        return response()->json(['success' => true, 'message' => 'Loan deleted successfully.']);
    }
}
