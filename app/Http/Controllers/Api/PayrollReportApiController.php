<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollReportApiController extends Controller
{
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
        
        $paidSalaries = [];
        $employeeComponents = [];
        $employeeDeductions = [];
        $uniqueComponents = collect();

        $employeeAdvanceDeductions = [];
        $employeeLoanDeductions = [];
        $employeeLopDeductions = [];
        $employeePenaltyAmounts = [];
        $employeePenaltyDays = [];
        $employeeTotalLates = [];
        $employeeExemptedLates = [];
        $employeeActualLates = [];

        if ($payroll) {
            foreach ($payroll->details as $detail) {
                $paidSalaries[$detail->employee_id] = $detail->net_salary;
                $employeeDeductions[$detail->employee_id] = $detail->total_deductions ?? 0;
                $employeeAdvanceDeductions[$detail->employee_id] = $detail->advance_deduction_amount ?? 0;
                $employeeLoanDeductions[$detail->employee_id] = $detail->loan_deduction_amount ?? 0;
                $employeeLopDeductions[$detail->employee_id] = $detail->lop_deduction_amount ?? 0;
                
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
        $data = $summaries->map(function ($summary) use ($paidSalaries, $employeeComponents, $employeeDeductions, $employeeAdvanceDeductions, $employeeLoanDeductions, $employeeLopDeductions, $employeePenaltyAmounts, $employeePenaltyDays, $employeeTotalLates, $employeeExemptedLates, $employeeActualLates, $uniqueComponents) {
            $paid = $paidSalaries[$summary->employee_id] ?? null;
            $comps = $employeeComponents[$summary->employee_id] ?? [];
            $deductionAmount = $employeeDeductions[$summary->employee_id] ?? 0;
            $advanceDeduction = $employeeAdvanceDeductions[$summary->employee_id] ?? 0;
            $loanDeduction = $employeeLoanDeductions[$summary->employee_id] ?? 0;
            $lopDeduction = $employeeLopDeductions[$summary->employee_id] ?? 0;
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
            
            // Calculate deduction days and payable days
            $deductionDays = ($summary->total_unpaid_leaves ?? 0) + ($summary->days_absent ?? 0) + (($summary->total_halfday ?? 0) * 0.5);
            $payableDays = ($summary->total_working_days ?? 0) - $deductionDays;
            
            return [
                'employee_code' => $summary->employee ? $summary->employee->employee_code : '-',
                'employee_name' => $summary->employee ? $summary->employee->name : 'Unknown',
                'branch_name' => $summary->employee && $summary->employee->branch ? $summary->employee->branch->name : '-',
                'department_name' => $summary->employee && $summary->employee->departmentRelation ? $summary->employee->departmentRelation->name : '-',
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
                'total_late_occurrences' => $totalLates,
                'exempted_late_occurrences' => $exemptedLates,
                'penalty_eligible_lates' => $actualLates,
                'penalty_days' => $penaltyDays,
                'penalty_amount' => $penaltyAmount,
                'deduction_amount' => $deductionAmount - $penaltyAmount, // Total Deduction before penalty
                'salary_before_penalty' => $paid !== null ? number_format($paid + $penaltyAmount, 0, '', '') : 'Not Generated',
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
}
