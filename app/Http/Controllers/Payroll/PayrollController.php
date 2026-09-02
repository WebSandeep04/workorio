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

        // Check if payroll already generated and finalized
        $existing = Payroll::where('month', $month)->where('year', $year)->first();
        if ($existing && $existing->status === 'Finalized') {
            return response()->json(['success' => false, 'message' => 'Payroll for this month is already finalized.']);
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
        $payroll = Payroll::findOrFail($id);
        
        if ($payroll->status === 'Finalized') {
            return response()->json(['success' => false, 'message' => 'Cannot void a finalized payroll.'], 403);
        }

        // Delete all associated details
        foreach ($payroll->details as $detail) {
            $detail->components()->delete();
            $detail->delete();
        }
        
        $payroll->delete();
        
        return response()->json(['success' => true, 'message' => 'Payroll voided successfully.']);
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

    public function fetchReportData(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return response()->json(['success' => false, 'message' => 'Month and year are required'], 400);
        }

        // Get attendance summaries
        $summaries = \App\Models\MonthlyAttendanceSummary::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Get payroll details for the month
        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $paidSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $uniqueComponents = collect();

        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];

        $employeeLopDeductions = [];

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;
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
        $data = $summaries->map(function ($summary) use ($paidSalaries, $employeeComponents, $employeeDeductions, $employeeAdvanceDeductions, $employeeLoanDeductions, $employeeLopDeductions, $uniqueComponents) {
            $paid = $paidSalaries[$summary->employee_id] ?? null;
            $comps = $employeeComponents[$summary->employee_id] ?? [];
            $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
            $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
            $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
            $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
            
            // Map components to guarantee 0 for missing ones
            $normalizedComps = [];
            foreach ($uniqueComponents as $uc) {
                $normalizedComps[$uc] = isset($comps[$uc]) ? round($comps[$uc]) : 0;
            }
            
            // Calculate deduction days and payable days
            $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
            $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;
            
            return [
                'employee_code' => $summary->employee ? $summary->employee->employee_code : '-',
                'employee_name' => $summary->employee ? $summary->employee->name : 'Unknown',
                'components' => $normalizedComps,
                
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
                'deduction_amount' => $deductionAmount,
                'payable_days' => $payableDays, // 2nd Working Days
                
                'is_locked' => $summary->is_locked,
                'paid_salary' => $paid !== null ? number_format($paid, 0, '', '') : 'Not Generated'
            ];
        });

        return response()->json([
            'success' => true, 
            'columns' => $uniqueComponents,
            'data' => $data
        ]);
    }
    public function exportReport(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return back()->with('error', 'Month and year are required');
        }

        $summaries = \App\Models\MonthlyAttendanceSummary::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $paidSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $uniqueComponents = collect();

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;
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

        $fileName = 'Payroll_Report_' . date('F', mktime(0, 0, 0, $month, 1)) . '_' . $year . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($summaries, $uniqueComponents, $paidSalaries, $employeeComponents, $employeeDeductions, $employeeAdvanceDeductions, $employeeLoanDeductions, $employeeLopDeductions) {
            $file = fopen('php://output', 'w');
            
            // Header Row
            $columns = array_merge(
                ['Employee Code', 'Employee Name'],
                $uniqueComponents,
                [
                    'Total Working Days', 'Full Day', 'Half Day', 'Leave', 'Unpaid Leave', 
                    'Absent', 'Total Weekly Off', 'Total Holidays', 'Holiday Work', 'Sunday Work', 
                    'Total Present', 'Deduction Days', 'Days Deduction', 'Advance Deduction', 'Loan Deduction', 'Total Deduction', 'Payable Days', 'Paid Salary'
                ]
            );
            fputcsv($file, $columns);

            foreach ($summaries as $summary) {
                $paid = $paidSalaries[$summary->employee_id] ?? null;
                $comps = $employeeComponents[$summary->employee_id] ?? [];
                $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
                $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
                $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
                $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
                
                $row = [
                    $summary->employee ? $summary->employee->employee_code : '-',
                    $summary->employee ? $summary->employee->name : 'Unknown'
                ];

                foreach ($uniqueComponents as $uc) {
                    $row[] = isset($comps[$uc]) ? round($comps[$uc]) : 0;
                }

                $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
                $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;

                $row = array_merge($row, [
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
                    $loanDeduction,
                    $deductionAmount,
                    $payableDays,
                    $paid !== null ? number_format($paid, 0, '', '') : 'Not Generated'
                ]);

                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportReportPdf(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$month || !$year) {
            return back()->with('error', 'Month and year are required');
        }

        $summaries = \App\Models\MonthlyAttendanceSummary::with('employee')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $payroll = \App\Models\Payroll::with(['details.components.salaryComponent'])->where('month', $month)->where('year', $year)->first();
        
        $paidSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $uniqueComponents = collect();

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;
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
        $fileName = 'Payroll_Report_' . date('F', mktime(0, 0, 0, $month, 1)) . '_' . $year . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf.report', compact('summaries', 'uniqueComponents', 'paidSalaries', 'employeeComponents', 'employeeDeductions', 'employeeAdvanceDeductions', 'employeeLoanDeductions', 'employeeLopDeductions', 'month', 'year'))
                ->setPaper('a4', 'landscape');
                
        return $pdf->download($fileName);
    }
}
