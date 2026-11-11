<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $year = $request->input('year', date('Y'));
            
            $query = Payroll::withCount('details')->where('year', $year);
            $payrolls = $query->orderBy('month', 'desc')->paginate(12);
            
            return response()->json($payrolls);
        }
        
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        
        return view('payroll.process', compact('years', 'months'));
    }

    public function generate(Request $request, PayrollCalculationService $payrollService)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100'
        ]);

        $month = $validated['month'];
        $year = $validated['year'];

        // Check if payroll already generated and finalized/approved
        $existing = Payroll::where('month', $month)->where('year', $year)->first();
        if ($existing && in_array($existing->status, ['Finalized', 'Approved'])) {
            return response()->json(['success' => false, 'message' => 'Payroll for this month is already approved/finalized.']);
        }

        try {
            $payroll = $payrollService->processPayroll($month, $year);
            return response()->json([
                'success' => true, 
                'message' => 'Payroll generated successfully.',
                'data' => $payroll
            ]);
        } catch (\Exception $e) {
            \Log::error('Payroll Generation Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate payroll. Ensure employee salary structures are configured correctly.'], 500);
        }
    }

    public function void($id)
    {
        $payroll = Payroll::with('details')->findOrFail($id);
        
        if (in_array($payroll->status, ['Finalized', 'Approved'])) {
            return response()->json(['success' => false, 'message' => 'Cannot void an approved or finalized payroll.'], 403);
        }

        // Rollback Loans
        $loanInstallments = \App\Models\LoanInstallment::where('payroll_id', $payroll->id)->get();
        foreach ($loanInstallments as $installment) {
            $loan = $installment->loan;

            if ($installment->is_system_generated) {
                // If it was created purely by the system as an overflow, delete it
                $installment->delete();
                if ($loan) {
                    $loan->decrement('total_installments');
                }
            } else {
                // Revert status and amount
                $updateData = [
                    'status' => 'pending',
                    'paid_on' => null,
                    'payroll_id' => null,
                    'skip_strategy' => null,
                ];

                if ($installment->original_amount !== null) {
                    $updateData['amount'] = $installment->original_amount;
                    $updateData['original_amount'] = null;
                }

                $installment->update($updateData);
            }

            // If loan was marked completed, revert it
            if ($loan && $loan->status === 'completed') {
                $loan->update(['status' => 'active']);
            }
        }

        // Rollback Advances
        $advanceDeductions = \App\Models\SalaryAdvanceDeduction::where('payroll_id', $payroll->id)->get();
        foreach ($advanceDeductions as $deduction) {
            $advance = $deduction->advance;
            $deduction->delete();

            if ($advance && $advance->status === 'completed' && $advance->remainingBalance() > 0) {
                $advance->update(['status' => 'approved']);
            }
        }

        // Delete all associated details
        foreach ($payroll->details as $detail) {
            $detail->components()->delete();
            $detail->delete();
        }
        
        $payroll->delete();
        
        return response()->json(['success' => true, 'message' => 'Payroll voided successfully.']);
    }

    public function approvals()
    {
        $years = range(date('Y') - 5, date('Y') + 1);
        rsort($years);
        return view('payroll.approvals', compact('years'));
    }

    public function fetchApprovals(Request $request)
    {
        $query = Payroll::with(['approver'])
            ->select('payrolls.*')
            ->selectRaw('(SELECT COUNT(*) FROM payroll_details WHERE payroll_details.payroll_id = payrolls.id) as details_count')
            ->selectRaw('(SELECT COALESCE(SUM(net_salary), 0) FROM payroll_details WHERE payroll_details.payroll_id = payrolls.id) as total_net')
            ->selectRaw('(SELECT COALESCE(SUM(gross_salary), 0) FROM payroll_details WHERE payroll_details.payroll_id = payrolls.id) as total_gross')
            ->selectRaw('(SELECT COALESCE(SUM(total_deductions), 0) FROM payroll_details WHERE payroll_details.payroll_id = payrolls.id) as total_deductions');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = strtolower($request->status);
            if ($status === 'pending') {
                $query->whereIn('status', ['Draft', 'Processed', 'pending']);
            } elseif ($status === 'approved') {
                $query->whereIn('status', ['Approved', 'Finalized', 'approved']);
            } elseif ($status === 'rejected') {
                $query->whereIn('status', ['Rejected', 'rejected']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Summary Counts
        $allPayrolls = (clone $query)->get();
        $counts = [
            'total' => $allPayrolls->count(),
            'pending' => $allPayrolls->filter(fn($p) => in_array(strtolower($p->status), ['draft', 'processed', 'pending']))->count(),
            'approved' => $allPayrolls->filter(fn($p) => in_array(strtolower($p->status), ['approved', 'finalized']))->count(),
            'rejected' => $allPayrolls->filter(fn($p) => strtolower($p->status) === 'rejected')->count(),
            'pending_amount' => $allPayrolls->filter(fn($p) => in_array(strtolower($p->status), ['draft', 'processed', 'pending']))->sum('total_net'),
        ];

        $payrolls = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $payrolls->items(),
            'counts' => $counts,
            'current_page' => $payrolls->currentPage(),
            'last_page' => $payrolls->lastPage(),
            'total' => $payrolls->total(),
            'from' => $payrolls->firstItem() ?? 0,
            'to' => $payrolls->lastItem() ?? 0
        ]);
    }

    public function approve(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);

        if (in_array(strtolower($payroll->status), ['approved', 'finalized'])) {
            return response()->json(['success' => false, 'message' => 'Payroll is already approved.'], 422);
        }

        $userId = auth()->id() ?? session('user_id');

        $payroll->update([
            'status' => 'Approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => null
        ]);

        return response()->json(['success' => true, 'message' => 'Payroll successfully approved!']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $payroll = Payroll::findOrFail($id);

        if (in_array(strtolower($payroll->status), ['approved', 'finalized'])) {
            return response()->json(['success' => false, 'message' => 'Cannot reject an already approved payroll.'], 422);
        }

        $userId = auth()->id() ?? session('user_id');

        $payroll->update([
            'status' => 'Rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        return response()->json(['success' => true, 'message' => 'Payroll rejected successfully.']);
    }

    public function show(Request $request, $id)
    {
        $payroll = Payroll::findOrFail($id);
        
        if ($request->ajax()) {
            $query = \App\Models\PayrollDetail::with('employee')
                        ->where('payroll_id', $id);
            
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->whereHas('employee', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%");
                });
            }
            
            $details = $query->paginate(15);
            return response()->json($details);
        }
        
        return view('payroll.show', compact('payroll'));
    }

    public function downloadPayslip($detail_id)
    {
        $detail = \App\Models\PayrollDetail::with(['employee', 'payroll', 'components.salaryComponent'])->findOrFail($detail_id);
        $payroll = $detail->payroll;
        $employee = $detail->employee;
        
        $earnings = $detail->components->filter(function($c) {
            return $c->salaryComponent && $c->salaryComponent->type === 'earning';
        });
        
        $deductions = $detail->components->filter(function($c) {
            return $c->salaryComponent && $c->salaryComponent->type === 'deduction';
        });

        // Use Barryvdh\DomPDF\Facade\Pdf for PDF generation
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf.payslip', [
            'detail' => $detail,
            'payroll' => $payroll,
            'employee' => $employee,
            'earnings' => $earnings,
            'deductions' => $deductions
        ]);
        
        $fileName = 'Payslip_' . str_replace(' ', '_', $employee->name) . '_' . date('F', mktime(0,0,0,$payroll->month,1)) . '_' . $payroll->year . '.pdf';
        return $pdf->download($fileName);
    }
    
    public function report(Request $request)
    {
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }
        
        return view('payroll.report', compact('years', 'months'));
    }

        public function fetchYearlyReportData(Request $request)
    {
        $year = $request->input('year', date('Y'));

        if (!$year) {
            return response()->json(['success' => false, 'message' => 'Year is required'], 400);
        }

        // Find all payrolls generated for this year ordered by month desc
        $payrolls = \App\Models\Payroll::where('year', $year)
            ->whereHas('details')
            ->orderBy('month', 'desc')
            ->get();

        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $results = [];

        foreach ($payrolls as $payroll) {
            $month = $payroll->month;
            
            // Build request object to invoke internal data builder
            $subReq = new Request(['month' => $month, 'year' => $year]);
            $repRes = $this->fetchReportData($subReq);
            $repJson = $repRes->getData(true);

            if ($repJson['success'] && !empty($repJson['data'])) {
                $rows = $repJson['data'];
                $cols = $repJson['columns'] ?? [];
                
                // Calculate column totals for this month
                $totals = [
                    'gross_salary' => 0,
                    'total_working_days' => 0,
                    'total_present_combined' => 0,
                    'total_present' => 0,
                    'total_halfday' => 0,
                    'sunday_work' => 0,
                    'holiday_work' => 0,
                    'leave' => 0,
                    'unpaid_leave' => 0,
                    'absent' => 0,
                    'total_weekly_off' => 0,
                    'total_holidays' => 0,
                    'total_deduction_days' => 0,
                    'lop_deduction' => 0,
                    'advance_deduction' => 0,
                    'loan_deduction' => 0,
                    'pf_deduction' => 0,
                    'esi_deduction' => 0,
                    'deduction_amount' => 0,
                    'salary_before_penalty' => 0,
                    'total_late_occurrences' => 0,
                    'exempted_late_occurrences' => 0,
                    'penalty_eligible_lates' => 0,
                    'penalty_days' => 0,
                    'penalty_amount' => 0,
                    'paid_salary' => 0,
                    'employer_pf' => 0,
                    'employer_esi' => 0,
                    'total_employer_contribution' => 0,
                    'components' => array_fill_keys($cols, 0)
                ];

                $employeeCount = count($rows);

                foreach ($rows as $r) {
                    $totals['gross_salary'] += (float) ($r['gross_salary'] ?? 0);
                    $totals['total_working_days'] += (float) ($r['total_working_days'] ?? 0);
                    $totals['total_present_combined'] += (float) ($r['total_present_combined'] ?? 0);
                    $totals['total_present'] += (float) ($r['total_present'] ?? 0);
                    $totals['total_halfday'] += (float) ($r['total_halfday'] ?? 0);
                    $totals['sunday_work'] += (float) ($r['sunday_work'] ?? 0);
                    $totals['holiday_work'] += (float) ($r['holiday_work'] ?? 0);
                    $totals['leave'] += (float) ($r['leave'] ?? 0);
                    $totals['unpaid_leave'] += (float) ($r['unpaid_leave'] ?? 0);
                    $totals['absent'] += (float) ($r['absent'] ?? 0);
                    $totals['total_weekly_off'] += (float) ($r['total_weekly_off'] ?? 0);
                    $totals['total_holidays'] += (float) ($r['total_holidays'] ?? 0);
                    $totals['total_deduction_days'] += (float) ($r['total_deduction_days'] ?? 0);
                    $totals['lop_deduction'] += (float) ($r['lop_deduction'] ?? 0);
                    $totals['advance_deduction'] += (float) ($r['advance_deduction'] ?? 0);
                    $totals['loan_deduction'] += (float) ($r['loan_deduction'] ?? 0);
                    $totals['pf_deduction'] += (float) ($r['pf_deduction'] ?? 0);
                    $totals['esi_deduction'] += (float) ($r['esi_deduction'] ?? 0);
                    $totals['deduction_amount'] += (float) ($r['deduction_amount'] ?? 0);
                    
                    if (is_numeric($r['salary_before_penalty'] ?? null)) {
                        $totals['salary_before_penalty'] += (float) $r['salary_before_penalty'];
                    }
                    $totals['total_late_occurrences'] += (float) ($r['total_late_occurrences'] ?? 0);
                    $totals['exempted_late_occurrences'] += (float) ($r['exempted_late_occurrences'] ?? 0);
                    $totals['penalty_eligible_lates'] += (float) ($r['penalty_eligible_lates'] ?? 0);
                    $totals['penalty_days'] += (float) ($r['penalty_days'] ?? 0);
                    $totals['penalty_amount'] += (float) ($r['penalty_amount'] ?? 0);
                    
                    if (is_numeric($r['paid_salary'] ?? null)) {
                        $totals['paid_salary'] += (float) $r['paid_salary'];
                    }
                    $totals['employer_pf'] += (float) ($r['employer_pf'] ?? 0);
                    $totals['employer_esi'] += (float) ($r['employer_esi'] ?? 0);
                    $totals['total_employer_contribution'] += (float) ($r['total_employer_contribution'] ?? 0);

                    foreach ($cols as $c) {
                        $totals['components'][$c] += (float) ($r['components'][$c] ?? 0);
                    }
                }

                $results[] = [
                    'month' => $month,
                    'month_name' => $monthNames[$month] ?? "Month {$month}",
                    'payroll_status' => $payroll->status ?? 'Draft',
                    'employee_count' => $employeeCount,
                    'columns' => $cols,
                    'totals' => $totals,
                    'data' => $rows,
                    'show_pf' => $repJson['show_pf'] ?? false,
                    'show_esi' => $repJson['show_esi'] ?? false,
                    'show_employer_cont' => $repJson['show_employer_cont'] ?? false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'year' => $year,
            'payrolls' => $results
        ]);
    }

    public function fetchReportData(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return response()->json(['success' => false, 'message' => 'Month and year are required'], 400);
        }

        // Get attendance summaries
        $summaries = \App\Models\MonthlyAttendanceSummary::with(['employee.branch', 'employee.departmentRelation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Get payroll details for the month
        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $settings = \App\Models\PayrollSetting::first();
        $statutoryRules = \App\Models\StatutoryRule::all()->keyBy('type');
        $employeeSalaries = \App\Models\EmployeeSalary::where('effective_from', '<=', Carbon::create($year, $month)->endOfMonth())
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('employee_id')
            ->map->first();

        $paidSalaries = [];
        $grossSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $uniqueComponents = collect();

        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $employeeLopDeductions = [];
        $employeePfDeductions = [];
        $employeeEsiDeductions = [];
        $employeeEmployerPfs = [];
        $employeeEmployerEsis = [];
        $employeeEmployerTotals = [];
        $employeePenaltyAmounts = [];
        $employeePenaltyDays = [];
        $employeeTotalLates = [];
        $employeeExemptedLates = [];
        $employeeActualLates = [];

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $grossSalaries[$detail->employee_id] = $detail->gross_salary;
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeEmploymentTypes[$detail->employee_id] = $detail->employment_type;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;

                $statutory = $this->resolveStatutoryBreakdown($detail, $employeeSalaries[$detail->employee_id] ?? null, $settings, $statutoryRules);
                $employeePfDeductions[$detail->employee_id] = $statutory['pf'];
                $employeeEsiDeductions[$detail->employee_id] = $statutory['esi'];
                $employeeEmployerPfs[$detail->employee_id] = $statutory['employer_pf'];
                $employeeEmployerEsis[$detail->employee_id] = $statutory['employer_esi'];
                $employeeEmployerTotals[$detail->employee_id] = $statutory['total_employer_contribution'];
                
                $penaltyAmount = $detail->penalty_deduction_amount ?? 0;
                $employeePenaltyAmounts[$detail->employee_id] = $penaltyAmount;
                $employeePenaltyDays[$detail->employee_id] = $detail->penalty ? $detail->penalty->penalty_days : 0;
                $employeeTotalLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->total_late_count : 0;
                $employeeExemptedLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->exempted_late_count : 0;
                $employeeActualLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->late_count : 0;
                $empComps = [];
                foreach ($detail->components as $c) {
                    if ($c->salaryComponent) {
                        $compName = $c->salaryComponent->name;
                        $empComps[$compName] = $c->amount;
                        $uniqueComponents->push($compName);
                    }
                }
                $employeeComponents[$detail->employee_id] = $empComps;
            }
        }
        
        $uniqueComponents = $uniqueComponents->unique()->values()->toArray();

        // Attach paid salary to each summary
        $data = $summaries->map(function ($summary) use ($paidSalaries, $grossSalaries, $employeeComponents, $employeeDeductions, $employeeAdvanceDeductions, $employeeLoanDeductions, $employeeLopDeductions, $employeePfDeductions, $employeeEsiDeductions, $employeeEmployerPfs, $employeeEmployerEsis, $employeeEmployerTotals, $employeePenaltyAmounts, $employeePenaltyDays, $employeeTotalLates, $employeeExemptedLates, $employeeActualLates, $employeeEmploymentTypes, $uniqueComponents) {
            $paid = $paidSalaries[$summary->employee_id] ?? null;
            $comps = $employeeComponents[$summary->employee_id] ?? [];
            $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
            $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
            $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
            $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
            $pfDeduction = $employeePfDeductions[$summary->employee_id] ?? 0;
            $esiDeduction = $employeeEsiDeductions[$summary->employee_id] ?? 0;
            $employerPf = $employeeEmployerPfs[$summary->employee_id] ?? 0;
            $employerEsi = $employeeEmployerEsis[$summary->employee_id] ?? 0;
            $totalEmployer = $employeeEmployerTotals[$summary->employee_id] ?? 0;
            $penaltyAmount = $employeePenaltyAmounts[$summary->employee_id] ?? 0;
            $penaltyDays = $employeePenaltyDays[$summary->employee_id] ?? 0;
            $totalLates = $employeeTotalLates[$summary->employee_id] ?? 0;
            $exemptedLates = $employeeExemptedLates[$summary->employee_id] ?? 0;
            $actualLates = $employeeActualLates[$summary->employee_id] ?? 0;
            
            // Map components to guarantee 0 for missing ones
            $normalizedComps = [];
            foreach ($uniqueComponents as $uc) {
                $normalizedComps[$uc] = isset($comps[$uc]) ? round($comps[$uc]) : 0;
            }

            $empGross = isset($grossSalaries[$summary->employee_id]) 
                ? (float) $grossSalaries[$summary->employee_id] 
                : (count($normalizedComps) > 0 ? array_sum($normalizedComps) : 0);
            
            // Calculate deduction days and payable days
            $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
            $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;
            
            $empType = !empty($employeeEmploymentTypes[$summary->employee_id]) 
                ? $employeeEmploymentTypes[$summary->employee_id] 
                : '-';

            return [
                'employee_code' => $summary->employee ? $summary->employee->employee_code : '-',
                'employee_name' => $summary->employee ? $summary->employee->name : 'Unknown',
                'employment_type' => $empType,
                'branch_name' => $summary->employee && $summary->employee->branch ? $summary->employee->branch->name : '-',
                'department_name' => $summary->employee && $summary->employee->departmentRelation ? $summary->employee->departmentRelation->name : '-',
                'components' => $normalizedComps,
                'gross_salary' => number_format($empGross, 2, '.', ''),
                
                'total_working_days' => $summary->total_working_days ?? 0,
                'total_present_combined' => $summary->total_present_combined ?? 0, // Total Present
                'total_present' => $summary->total_present ?? 0, // Full Day
                'total_halfday' => $summary->total_halfday ?? 0,
                'sunday_work' => $summary->total_weekly_offs_worked ?? 0,
                'holiday_work' => $summary->total_holidays_worked ?? 0,
                'leave' => $summary->days_on_leave ?? 0,
                'unpaid_leave' => $summary->total_unpaid_leaves ?? 0,
                'absent' => $summary->days_absent ?? 0,
                'total_weekly_off' => $summary->total_weekly_offs ?? 0,
                'total_holidays' => $summary->total_holidays ?? 0,
                'total_deduction_days' => $deductionDays,
                'lop_deduction' => $lopDeduction,
                'advance_deduction' => $advanceDeduction,
                'loan_deduction' => $loanDeduction,
                'pf_deduction' => number_format($pfDeduction, 2, '.', ''),
                'esi_deduction' => number_format($esiDeduction, 2, '.', ''),
                'total_late_occurrences' => $totalLates,
                'exempted_late_occurrences' => $exemptedLates,
                'penalty_eligible_lates' => $actualLates,
                'penalty_days' => $penaltyDays,
                'penalty_amount' => $penaltyAmount,
                'deduction_amount' => $deductionAmount - $penaltyAmount, // Total Deduction before penalty
                'salary_before_penalty' => $paid !== null ? number_format($paid + $penaltyAmount, 0, '', '') : 'Not Generated',
                'payable_days' => $payableDays, // 2nd Working Days
                
                'is_locked' => $summary->is_locked,
                'paid_salary' => $paid !== null ? number_format($paid, 0, '', '') : 'Not Generated',
                'employer_pf' => number_format($employerPf, 2, '.', ''),
                'employer_esi' => number_format($employerEsi, 2, '.', ''),
                'total_employer_contribution' => number_format($totalEmployer, 2, '.', '')
            ];
        });

        $totalPfRecorded = array_sum($employeePfDeductions) + array_sum($employeeEmployerPfs);
        $totalEsiRecorded = array_sum($employeeEsiDeductions) + array_sum($employeeEmployerEsis);
        $showPf = ($settings->pf_enabled ?? false) || ($totalPfRecorded > 0);
        $showEsi = ($settings->esi_enabled ?? false) || ($totalEsiRecorded > 0);
        $showEmployerCont = $showPf || $showEsi;

        return response()->json([
            'success' => true, 
            'columns' => $uniqueComponents,
            'data' => $data,
            'show_pf' => $showPf,
            'show_esi' => $showEsi,
            'show_employer_cont' => $showEmployerCont
        ]);
    }
    public function exportReport(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $withPenalty = $request->input('with_penalty', 1) == 1;

        if (!$month || !$year) {
            return back()->with('error', 'Month and year are required');
        }

        $summaries = \App\Models\MonthlyAttendanceSummary::with(['employee.branch', 'employee.departmentRelation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $settings = \App\Models\PayrollSetting::first();
        $statutoryRules = \App\Models\StatutoryRule::all()->keyBy('type');
        $employeeSalaries = \App\Models\EmployeeSalary::where('effective_from', '<=', Carbon::create($year, $month)->endOfMonth())
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('employee_id')
            ->map->first();

        $paidSalaries = [];
        $grossSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $employeeLopDeductions = [];
        $employeePfDeductions = [];
        $employeeEsiDeductions = [];
        $employeeEmployerPfs = [];
        $employeeEmployerEsis = [];
        $employeeEmployerTotals = [];
        $employeePenaltyDays = [];
        $employeePenaltyAmounts = [];
        $employeeTotalLates = [];
        $employeeExemptedLates = [];
        $employeeActualLates = [];
        $employeeEmploymentTypes = [];
        $uniqueComponents = collect();

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $grossSalaries[$detail->employee_id] = $detail->gross_salary;
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeEmploymentTypes[$detail->employee_id] = $detail->employment_type;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;

                $statutory = $this->resolveStatutoryBreakdown($detail, $employeeSalaries[$detail->employee_id] ?? null, $settings, $statutoryRules);
                $employeePfDeductions[$detail->employee_id] = $statutory['pf'];
                $employeeEsiDeductions[$detail->employee_id] = $statutory['esi'];
                $employeeEmployerPfs[$detail->employee_id] = $statutory['employer_pf'];
                $employeeEmployerEsis[$detail->employee_id] = $statutory['employer_esi'];
                $employeeEmployerTotals[$detail->employee_id] = $statutory['total_employer_contribution'];
                
                $penaltyAmount = $detail->penalty_deduction_amount ?? 0;
                $employeePenaltyAmounts[$detail->employee_id] = $penaltyAmount;
                $employeePenaltyDays[$detail->employee_id] = $detail->penalty ? $detail->penalty->penalty_days : 0;
                $employeeTotalLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->total_late_count : 0;
                $employeeExemptedLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->exempted_late_count : 0;
                $employeeActualLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->late_count : 0;
                $empComps = [];
                foreach ($detail->components as $c) {
                    if ($c->salaryComponent) {
                        $compName = $c->salaryComponent->name;
                        $empComps[$compName] = $c->amount;
                        $uniqueComponents->push($compName);
                    }
                }
                $employeeComponents[$detail->employee_id] = $empComps;
            }
        }
        
        $uniqueComponents = $uniqueComponents->unique()->values()->toArray();

        $totalPfRecorded = array_sum($employeePfDeductions) + array_sum($employeeEmployerPfs);
        $totalEsiRecorded = array_sum($employeeEsiDeductions) + array_sum($employeeEmployerEsis);
        $showPf = ($settings->pf_enabled ?? false) || ($totalPfRecorded > 0);
        $showEsi = ($settings->esi_enabled ?? false) || ($totalEsiRecorded > 0);
        $showEmployerCont = $showPf || $showEsi;

        $fileName = 'Payroll_Report_' . date('F', mktime(0, 0, 0, $month, 1)) . '_' . $year . '.csv';

        $headers = array(
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($summaries, $uniqueComponents, $paidSalaries, $grossSalaries, $employeeComponents, $employeeDeductions, $employeeAdvanceDeductions, $employeeLoanDeductions, $employeeLopDeductions, $employeePfDeductions, $employeeEsiDeductions, $employeeEmployerPfs, $employeeEmployerEsis, $employeeEmployerTotals, $employeePenaltyDays, $employeePenaltyAmounts, $employeeTotalLates, $employeeExemptedLates, $employeeActualLates, $withPenalty, $showPf, $showEsi, $showEmployerCont) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM so Excel opens text/symbols correctly
            fputs($file, "\xEF\xBB\xBF");
            
            $grandTotalPaidSalary = 0;
            
            // Header Row
            $baseColumns = [
                'Total Working Days', 'Full Day', 'Half Day', 'Leave', 'Unpaid Leave', 
                'Absent', 'Total Weekly Off', 'Total Holidays', 'Holiday Work', 'Sunday Work', 
                'Total Present', 'Deduction Days', 'Days deduction amount', 'Advance Deduction', 'Loan Deduction'
            ];
            if ($showPf) {
                $baseColumns[] = 'PF Deduction';
            }
            if ($showEsi) {
                $baseColumns[] = 'ESI Deduction';
            }
            $baseColumns[] = 'Total Deduction';
            $baseColumns[] = 'Salary';

            if ($withPenalty) {
                $baseColumns = array_merge($baseColumns, ['Total Lates', 'Exempted Late Occurrences', 'Penalty-Eligible Lates', 'Penalty Days', 'Penalty Amount', 'Final Salary']);
            }
            if ($showPf) {
                $baseColumns[] = 'Employer PF';
            }
            if ($showEsi) {
                $baseColumns[] = 'Employer ESI';
            }
            if ($showEmployerCont) {
                $baseColumns[] = 'Total Employer Contribution';
            }

            $columns = array_merge(
                ['Employee Code', 'Employee Name', 'Employment Type', 'Branch', 'Department'],
                $uniqueComponents,
                ['Gross Salary'],
                $baseColumns
            );

            // Size colTotals accurately based on total columns to prevent out-of-bounds error
            $colTotals = array_fill(0, count($columns), 0);

            fputcsv($file, $columns);

            foreach ($summaries as $summary) {
                $paid = $paidSalaries[$summary->employee_id] ?? null;
                $comps = $employeeComponents[$summary->employee_id] ?? [];
                $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
                $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
                $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
                $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
                $pfDeduction = $employeePfDeductions[$summary->employee_id] ?? 0;
                $esiDeduction = $employeeEsiDeductions[$summary->employee_id] ?? 0;
                $employerPf = $employeeEmployerPfs[$summary->employee_id] ?? 0;
                $employerEsi = $employeeEmployerEsis[$summary->employee_id] ?? 0;
                $totalEmployer = $employeeEmployerTotals[$summary->employee_id] ?? 0;
                $penaltyDays = $employeePenaltyDays[$summary->employee_id] ?? 0;
                $penaltyAmount = $employeePenaltyAmounts[$summary->employee_id] ?? 0;
                $totalLates = $employeeTotalLates[$summary->employee_id] ?? 0;
                $exemptedLates = $employeeExemptedLates[$summary->employee_id] ?? 0;
                $actualLates = $employeeActualLates[$summary->employee_id] ?? 0;
                
                $empGross = isset($grossSalaries[$summary->employee_id]) 
                    ? (float) $grossSalaries[$summary->employee_id] 
                    : (count($comps) > 0 ? array_sum($comps) : 0);

                $empType = !empty($employeeEmploymentTypes[$summary->employee_id]) 
                    ? $employeeEmploymentTypes[$summary->employee_id] 
                    : '-';

                $row = [
                    $summary->employee ? $summary->employee->employee_code : '-',
                    $summary->employee ? $summary->employee->name : 'Unknown',
                    $empType,
                    $summary->employee && $summary->employee->branch ? $summary->employee->branch->name : '-',
                    $summary->employee && $summary->employee->departmentRelation ? $summary->employee->departmentRelation->name : '-'
                ];

                foreach ($uniqueComponents as $uc) {
                    $row[] = isset($comps[$uc]) ? round($comps[$uc]) : 0;
                }
                $row[] = round($empGross);

                $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
                
                if ($paid !== null) {
                    $grandTotalPaidSalary += (float)($withPenalty ? $paid : ($paid + $penaltyAmount));
                }
                
                // Track colTotals here...
                $colIdx = 5; // Starts after Employee Code, Name, Employment Type, Branch, Dept
                foreach ($uniqueComponents as $uc) {
                    $colTotals[$colIdx] += isset($comps[$uc]) ? round($comps[$uc]) : 0;
                    $colIdx++;
                }
                $colTotals[$colIdx++] += round($empGross);
                $colTotals[$colIdx++] += $summary->total_working_days ?? 0;
                $colTotals[$colIdx++] += $summary->total_present ?? 0;
                $colTotals[$colIdx++] += $summary->total_halfday ?? 0;
                $colTotals[$colIdx++] += $summary->days_on_leave ?? 0;
                $colTotals[$colIdx++] += $summary->total_unpaid_leaves ?? 0;
                $colTotals[$colIdx++] += $summary->days_absent ?? 0;
                $colTotals[$colIdx++] += $summary->total_weekly_offs ?? 0;
                $colTotals[$colIdx++] += $summary->total_holidays ?? 0;
                $colTotals[$colIdx++] += $summary->total_holidays_worked ?? 0;
                $colTotals[$colIdx++] += $summary->total_weekly_offs_worked ?? 0;
                $colTotals[$colIdx++] += $summary->total_present_combined ?? 0;
                $colTotals[$colIdx++] += $deductionDays;
                $colTotals[$colIdx++] += $lopDeduction;
                $colTotals[$colIdx++] += $advanceDeduction;
                $colTotals[$colIdx++] += $loanDeduction;
                if ($showPf) {
                    $colTotals[$colIdx++] += $pfDeduction;
                }
                if ($showEsi) {
                    $colTotals[$colIdx++] += $esiDeduction;
                }
                $colTotals[$colIdx++] += ($deductionAmount - $penaltyAmount);
                if ($paid !== null) {
                    $colTotals[$colIdx] += ($paid + $penaltyAmount);
                }
                $colIdx++;
                if ($withPenalty) {
                    $colTotals[$colIdx++] += $totalLates;
                    $colTotals[$colIdx++] += $exemptedLates;
                    $colTotals[$colIdx++] += $actualLates;
                    $colTotals[$colIdx++] += $penaltyDays;
                    $colTotals[$colIdx++] += $penaltyAmount;
                    if ($paid !== null) {
                        $colTotals[$colIdx] += $paid;
                    }
                    $colIdx++;
                }
                if ($showPf) {
                    $colTotals[$colIdx++] += $employerPf;
                }
                if ($showEsi) {
                    $colTotals[$colIdx++] += $employerEsi;
                }
                if ($showEmployerCont) {
                    $colTotals[$colIdx++] += $totalEmployer;
                }

                $baseRow = [
                    $summary->total_working_days ?? 0,
                    $summary->total_present ?? 0,
                    $summary->total_halfday ?? 0,
                    $summary->days_on_leave ?? 0,
                    $summary->total_unpaid_leaves ?? 0,
                    $summary->days_absent ?? 0,
                    $summary->total_weekly_offs ?? 0,
                    $summary->total_holidays ?? 0,
                    $summary->total_holidays_worked ?? 0,
                    $summary->total_weekly_offs_worked ?? 0,
                    $summary->total_present_combined ?? 0,
                    $deductionDays,
                    $lopDeduction,
                    $advanceDeduction,
                    $loanDeduction
                ];
                if ($showPf) {
                    $baseRow[] = $pfDeduction;
                }
                if ($showEsi) {
                    $baseRow[] = $esiDeduction;
                }
                $baseRow[] = $deductionAmount - $penaltyAmount;
                $baseRow[] = $paid !== null ? number_format($paid + $penaltyAmount, 0, '', '') : 'Not Generated';

                if ($withPenalty) {
                    $baseRow = array_merge($baseRow, [
                        $totalLates,
                        $exemptedLates,
                        $actualLates,
                        $penaltyDays,
                        $penaltyAmount,
                        $paid !== null ? number_format($paid, 0, '', '') : 'Not Generated'
                    ]);
                }

                if ($showPf) {
                    $baseRow[] = number_format($employerPf, 2, '.', '');
                }
                if ($showEsi) {
                    $baseRow[] = number_format($employerEsi, 2, '.', '');
                }
                if ($showEmployerCont) {
                    $baseRow[] = number_format($totalEmployer, 2, '.', '');
                }

                $row = array_merge($row, $baseRow);
                fputcsv($file, $row);
            }
            
            $totalRow = array_fill(0, count($columns), '');
            $totalRow[4] = 'Total:';
            for ($i = 5; $i < count($columns); $i++) {
                $totalRow[$i] = $colTotals[$i] ?? 0;
            }
            fputcsv($file, $totalRow);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportReportPdf(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $withPenalty = $request->input('with_penalty', 1) == 1;

        if (!$month || !$year) {
            return back()->with('error', 'Month and year are required');
        }

        $summaries = \App\Models\MonthlyAttendanceSummary::with(['employee.branch', 'employee.departmentRelation'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $settings = \App\Models\PayrollSetting::first();
        $statutoryRules = \App\Models\StatutoryRule::all()->keyBy('type');
        $employeeSalaries = \App\Models\EmployeeSalary::where('effective_from', '<=', Carbon::create($year, $month)->endOfMonth())
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('employee_id')
            ->map->first();

        $paidSalaries = [];
        $grossSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $employeeLopDeductions = [];
        $employeePfDeductions = [];
        $employeeEsiDeductions = [];
        $employeeEmployerPfs = [];
        $employeeEmployerEsis = [];
        $employeeEmployerTotals = [];
        $employeePenaltyDays = [];
        $employeePenaltyAmounts = [];
        $employeeTotalLates = [];
        $employeeExemptedLates = [];
        $employeeActualLates = [];
        $uniqueComponents = collect();

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $grossSalaries[$detail->employee_id] = $detail->gross_salary;
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeEmploymentTypes[$detail->employee_id] = $detail->employment_type;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;

                $statutory = $this->resolveStatutoryBreakdown($detail, $employeeSalaries[$detail->employee_id] ?? null, $settings, $statutoryRules);
                $employeePfDeductions[$detail->employee_id] = $statutory['pf'];
                $employeeEsiDeductions[$detail->employee_id] = $statutory['esi'];
                $employeeEmployerPfs[$detail->employee_id] = $statutory['employer_pf'];
                $employeeEmployerEsis[$detail->employee_id] = $statutory['employer_esi'];
                $employeeEmployerTotals[$detail->employee_id] = $statutory['total_employer_contribution'];

                $employeePenaltyDays[$detail->employee_id] = $detail->penalty ? $detail->penalty->penalty_days : 0;
                $employeePenaltyAmounts[$detail->employee_id] = $detail->penalty_deduction_amount ?? 0;
                $employeeTotalLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->total_late_count : 0;
                $employeeExemptedLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->exempted_late_count : 0;
                $employeeActualLates[$detail->employee_id] = $detail->penalty ? $detail->penalty->late_count : 0;
                $empComps = [];
                foreach ($detail->components as $c) {
                    if ($c->salaryComponent) {
                        $compName = $c->salaryComponent->name;
                        $empComps[$compName] = $c->amount;
                        $uniqueComponents->push($compName);
                    }
                }
                $employeeComponents[$detail->employee_id] = $empComps;
            }
        }
        
        $uniqueComponents = $uniqueComponents->unique()->values()->toArray();
        $totalPfRecorded = array_sum($employeePfDeductions) + array_sum($employeeEmployerPfs);
        $totalEsiRecorded = array_sum($employeeEsiDeductions) + array_sum($employeeEmployerEsis);
        $showPf = ($settings->pf_enabled ?? false) || ($totalPfRecorded > 0);
        $showEsi = ($settings->esi_enabled ?? false) || ($totalEsiRecorded > 0);
        $showEmployerCont = $showPf || $showEsi;

        $fileName = 'Payroll_Report_' . date('F', mktime(0, 0, 0, $month, 1)) . '_' . $year . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf.report', compact('summaries', 'uniqueComponents', 'paidSalaries', 'grossSalaries', 'employeeComponents', 'employeeDeductions', 'employeeAdvanceDeductions', 'employeeLoanDeductions', 'employeeLopDeductions', 'employeePfDeductions', 'employeeEsiDeductions', 'employeeEmployerPfs', 'employeeEmployerEsis', 'employeeEmployerTotals', 'employeePenaltyDays', 'employeePenaltyAmounts', 'employeeTotalLates', 'employeeExemptedLates', 'employeeActualLates', 'employeeEmploymentTypes', 'month', 'year', 'withPenalty', 'showPf', 'showEsi', 'showEmployerCont'))
                ->setPaper('a3', 'landscape');
                
        return $pdf->download($fileName);
    }

    private function resolveStatutoryBreakdown($detail, $empSalary, $settings, $statutoryRules)
    {
        if (!$detail) {
            return [
                'pf' => 0, 
                'esi' => 0, 
                'employer_pf' => 0, 
                'employer_esi' => 0, 
                'total_employer_contribution' => 0
            ];
        }

        // If explicitly stored in database columns on payroll_details
        $hasStoredBreakdown = ($detail->pf_deduction_amount !== null || $detail->employer_pf_amount !== null);
        $pf = (float) ($detail->pf_deduction_amount ?? 0);
        $esi = (float) ($detail->esi_deduction_amount ?? 0);
        $empPf = (float) ($detail->employer_pf_amount ?? 0);
        $empEsi = (float) ($detail->employer_esi_amount ?? 0);
        $totalEmp = (float) ($detail->employer_contribution_amount ?? ($empPf + $empEsi));

        $statutoryRecorded = (float) ($detail->statutory_deduction_amount ?? 0);
        if ($hasStoredBreakdown && ($pf > 0 || $esi > 0 || $empPf > 0 || $empEsi > 0 || $statutoryRecorded == 0)) {
            return [
                'pf' => $pf,
                'esi' => $esi,
                'employer_pf' => $empPf,
                'employer_esi' => $empEsi,
                'total_employer_contribution' => $totalEmp,
            ];
        }

        // Dynamic fallback if record was created prior to new columns
        $empComps = [];
        foreach ($detail->components as $c) {
            if ($c->salaryComponent) {
                $slug = strtolower(str_replace(' ', '_', $c->salaryComponent->name));
                $empComps[$slug] = (float) $c->amount;
            }
        }

        $isPfApplicable = $empSalary ? (bool) ($empSalary->is_pf_applicable ?? true) : true;
        $isEsiApplicable = $empSalary ? (bool) ($empSalary->is_esi_applicable ?? true) : true;

        $pf = 0;
        $esi = 0;
        $empPf = 0;
        $empEsi = 0;

        // PF
        if ($isPfApplicable && ($settings->pf_enabled ?? false) && isset($statutoryRules['PF'])) {
            $rule = $statutoryRules['PF'];
            $calcOn = strtolower(trim($rule->calculate_on ?? ''));
            $normCalcOn = str_replace([' ', '+'], '_', $calcOn);
            if ($calcOn === 'basic' || $calcOn === 'basic + da' || $calcOn === 'basic_da' || $calcOn === 'basic_+_da') {
                $base = $empComps['basic'] ?? $empComps['basic_+_da'] ?? $empComps['basic_da'] ?? 0;
            } elseif (!empty($normCalcOn) && isset($empComps[$normCalcOn])) {
                $base = $empComps[$normCalcOn];
            } else {
                $base = (float) $detail->gross_salary;
            }
            if ($rule->salary_limit > 0 && $base > $rule->salary_limit) {
                $base = $rule->salary_limit;
            }
            if ((float)($detail->statutory_deduction_amount ?? 0) > 0) {
                $pf = round(($base * (float)$rule->employee_rate) / 100);
            }
            if ((float)($rule->employer_rate ?? 0) > 0) {
                $empPf = round(($base * (float)$rule->employer_rate) / 100);
            }
        }

        // ESI
        if ($isEsiApplicable && ($settings->esi_enabled ?? false) && isset($statutoryRules['ESI'])) {
            $rule = $statutoryRules['ESI'];
            $calcOn = strtolower(trim($rule->calculate_on ?? ''));
            $normCalcOn = str_replace([' ', '+'], '_', $calcOn);
            if ($calcOn === 'basic' || $calcOn === 'basic + da' || $calcOn === 'basic_da' || $calcOn === 'basic_+_da') {
                $base = $empComps['basic'] ?? $empComps['basic_+_da'] ?? $empComps['basic_da'] ?? 0;
            } elseif (!empty($normCalcOn) && isset($empComps[$normCalcOn])) {
                $base = $empComps[$normCalcOn];
            } else {
                $base = (float) $detail->gross_salary;
            }
            $hasLimit = $rule->salary_limit > 0;
            $gross = (float) $detail->gross_salary;
            if (!$hasLimit || $gross <= $rule->salary_limit) {
                if ((float)($detail->statutory_deduction_amount ?? 0) > 0) {
                    $esi = round(($base * (float)$rule->employee_rate) / 100);
                }
                if ((float)($rule->employer_rate ?? 0) > 0) {
                    $empEsi = round(($base * (float)$rule->employer_rate) / 100);
                }
            }
        }

        if ((float)($detail->statutory_deduction_amount ?? 0) > 0) {
            $recorded = (float) $detail->statutory_deduction_amount;
            if (($pf + $esi) != $recorded) {
                if ($pf > 0 && $esi == 0) {
                    $pf = $recorded;
                } elseif ($esi > 0 && $pf == 0) {
                    $esi = $recorded;
                }
            }
        } else {
            $pf = 0;
            $esi = 0;
        }

        return [
            'pf' => $pf,
            'esi' => $esi,
            'employer_pf' => $empPf,
            'employer_esi' => $empEsi,
            'total_employer_contribution' => ($empPf + $empEsi)
        ];
    }
}
