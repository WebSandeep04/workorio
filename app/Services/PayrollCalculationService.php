<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\MonthlyAttendanceSummary;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\PayrollComponentDetail;
use App\Models\PayrollSetting;
use App\Models\StatutoryRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class PayrollCalculationService
{
    protected $expressionLanguage;
    protected $settings;
    protected $statutoryRules;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * Process payroll for a specific month and year
     */
    public function processPayroll($month, $year)
    {
        $this->settings = PayrollSetting::first() ?? new PayrollSetting();
        
        $this->statutoryRules = StatutoryRule::all()->keyBy('type');
        
        $payroll = Payroll::firstOrCreate(
            ['month' => $month, 'year' => $year],
            ['status' => 'Draft']
        );

        // Fetch all locked attendance summaries for the month
        $summaries = MonthlyAttendanceSummary::where('month', $month)
            ->where('year', $year)
            ->where('is_locked', true)
            ->get();

        foreach ($summaries as $summary) {
            $this->calculateEmployeePayroll($payroll, $summary);
        }

        return $payroll;
    }

    /**
     * Calculate payroll for a single employee based on their attendance summary
     */
    protected function calculateEmployeePayroll(Payroll $payroll, MonthlyAttendanceSummary $summary)
    {
        $employeeId = $summary->employee_id;
        
        // Get employee's salary structure
        $employeeSalary = EmployeeSalary::where('employee_id', $employeeId)
            ->where('effective_from', '<=', Carbon::create($summary->year, $summary->month)->endOfMonth())
            ->orderBy('effective_from', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$employeeSalary || !$employeeSalary->structure) {
            return; // Cannot process without salary structure
        }

        $grossSalary = $employeeSalary->gross_salary;
        $components = $employeeSalary->structure->components;

        // Calculate Loss of Pay (LOP) based on total deduction days if setting is enabled
        $deductionAmount = 0;
        if ($this->settings->attendance_based && $summary->total_deduction_days > 0) {
            $perDaySalary = $grossSalary / 26;
            $deductionAmount = round($perDaySalary * $summary->total_deduction_days);
        }

        $calculatedComponents = [];
        $totalEarnings = 0;
        $totalDeductions = 0;
        
        // Context for expression language
        $context = [
            'employee_id' => $employeeId,
            'gross' => $grossSalary,
            'base_gross' => $grossSalary,
            'working_days' => $summary->total_working_days,
            'payable_days' => $summary->days_worked + $summary->days_on_leave + $summary->total_holidays + $summary->total_weekly_offs,
            'absent_days' => $summary->days_absent
        ];

        // First Pass: Calculate all fixed and percentage earnings to build full context
        foreach ($components as $component) {
            if ($component->type === 'earning') {
                $amount = $this->calculateComponentValue($component, $context, $grossSalary);
                $calculatedComponents[$component->id] = $amount;
                // Add to context by name for other formulas (e.g., basic)
                $context[strtolower(str_replace(' ', '_', $component->name))] = $amount;
                $totalEarnings += $amount;
            }
        }

        // Second Pass: Deductions and Employer Contributions (which might depend on earnings like 'basic')
        foreach ($components as $component) {
            if (in_array($component->type, ['deduction', 'employer_contribution'])) {
                $amount = $this->calculateComponentValue($component, $context, $grossSalary);
                $calculatedComponents[$component->id] = $amount;
                
                if ($component->type === 'deduction') {
                    $totalDeductions += $amount;
                }
            }
        }

        // Apply Statutory Deductions
        $statutoryDeductions = $this->calculateStatutoryDeductions($context, $totalEarnings);
        foreach ($statutoryDeductions as $type => $amount) {
            $totalDeductions += $amount;
        }

        // Apply LOP Deduction to total deductions
        if ($deductionAmount > 0) {
            $totalDeductions += $deductionAmount;
        }

        $netSalary = $totalEarnings - $totalDeductions;

        // Save Payroll Detail
        $payrollDetail = PayrollDetail::updateOrCreate(
            ['payroll_id' => $payroll->id, 'employee_id' => $employeeId],
            [
                'gross_salary' => $totalEarnings, 
                'net_salary' => $netSalary,
                'total_deductions' => $totalDeductions,
                'lop_deduction_amount' => $deductionAmount,
                'statutory_deduction_amount' => array_sum($statutoryDeductions)
            ]
        );

        // Save Component Details
        foreach ($calculatedComponents as $componentId => $amount) {
            PayrollComponentDetail::updateOrCreate(
                ['payroll_detail_id' => $payrollDetail->id, 'salary_component_id' => $componentId],
                ['amount' => $amount]
            );
        }
    }

    /**
     * Parse and calculate a single component value
     */
    protected function calculateComponentValue($component, $context, $gross)
    {
        $pivot = $component->pivot;
        $employeeId = $context['employee_id'] ?? null;
        
        switch ($component->calculation_type) {
            case 'fixed':
                $val = round((float) $pivot->value);
                return $val;
            case 'percentage':
                $val = round(($gross * (float) $pivot->value) / 100);
                return $val;
            case 'formula':
                if (empty($pivot->formula)) return 0;
                try {
                    $val = round($this->expressionLanguage->evaluate($pivot->formula, $context));
                    return $val;
                } catch (\Exception $e) {
                    \Log::error("Payroll Formula Error: " . $e->getMessage());
                    return 0;
                }
            default:
                return 0;
            }
    }

    /**
     * Calculate Statutory Deductions based on settings and rules
     */
    protected function calculateStatutoryDeductions($context, $totalEarnings)
    {
        $deductions = [];
        $employeeId = $context['employee_id'] ?? null;

        // PF
        if ($this->settings->pf_enabled && isset($this->statutoryRules['PF'])) {
            $rule = $this->statutoryRules['PF'];
            
            $baseForPF = $rule->calculate_on === 'basic' ? ($context['basic'] ?? 0) : $totalEarnings;
            if ($rule->salary_limit > 0 && $baseForPF > $rule->salary_limit) {
                $baseForPF = $rule->salary_limit;
            }
            $deductions['PF'] = round(($baseForPF * $rule->employee_rate) / 100);
        }

        // ESI
        if ($this->settings->esi_enabled && isset($this->statutoryRules['ESI'])) {
            $rule = $this->statutoryRules['ESI'];
            
            $hasLimit = $rule->salary_limit > 0;

            if (!$hasLimit || $totalEarnings <= $rule->salary_limit) {
                $deductions['ESI'] = round(($totalEarnings * $rule->employee_rate) / 100);
            }
        }

        // PT (simplified example)
        if ($this->settings->pt_enabled && isset($this->statutoryRules['PT'])) {
            $rule = $this->statutoryRules['PT'];
            $deductions['PT'] = round($rule->employee_rate); // Often flat rate or slab based
        }

        return $deductions;
    }

    /**
     * Calculate working days based on monthly attendance summary data.
     *
     * @param mixed $summary An array or object representing the monthly summary.
     * @return float
     */
    public function calculateWorkingDays($summary): float
    {
        // Extract values handling both array and object formats
        $totalPresent = (float) ($summary['total_present'] ?? (is_object($summary) ? $summary->total_present : 0) ?? 0);
        $totalHalfday = (float) ($summary['total_halfday'] ?? (is_object($summary) ? $summary->total_halfday : 0) ?? 0);
        $daysOnLeave  = (float) ($summary['days_on_leave'] ?? (is_object($summary) ? $summary->days_on_leave : 0) ?? 0);
        $totalHolidays = (float) ($summary['total_holidays'] ?? (is_object($summary) ? $summary->total_holidays : 0) ?? 0);
        $totalWeeklyOffs = (float) ($summary['total_weekly_offs'] ?? (is_object($summary) ? $summary->total_weekly_offs : 0) ?? 0);

        // User requested: "change payable days to like working days and show total working days its not count absent and unpaid leave and count halfday as 0.5"
        // Working Days = Total Working Days - Absent Days - Unpaid Leave Days - (0.5 * Half Days)
        
        $totalWorkingDays = (float) ($summary['total_working_days'] ?? (is_object($summary) ? $summary->total_working_days : 0) ?? 0);
        $daysAbsent = (float) ($summary['days_absent'] ?? (is_object($summary) ? $summary->days_absent : 0) ?? 0);
        $unpaidLeaves = (float) ($summary['total_unpaid_leaves'] ?? (is_object($summary) ? $summary->total_unpaid_leaves : 0) ?? 0);
        
        // Base it strictly on Working Days (ignores Weekly Offs and Holidays)
        $workingDays = $totalWorkingDays - $daysAbsent - $unpaidLeaves;
        
        // Deduct 0.5 for each half day
        $workingDays -= ($totalHalfday * 0.5);

        return round($workingDays, 1);
    }

    /**
     * Calculate total deduction days based on monthly attendance summary data.
     *
     * @param mixed $summary An array or object representing the monthly summary.
     * @return float
     */
    public function calculateTotalDeductionDays($summary): float
    {
        $totalHalfday = (float) ($summary['total_halfday'] ?? (is_object($summary) ? $summary->total_halfday : 0) ?? 0);
        $daysAbsent = (float) ($summary['days_absent'] ?? (is_object($summary) ? $summary->days_absent : 0) ?? 0);
        $unpaidLeaves = (float) ($summary['total_unpaid_leaves'] ?? (is_object($summary) ? $summary->total_unpaid_leaves : 0) ?? 0);
        
        $deductionDays = $daysAbsent + $unpaidLeaves + ($totalHalfday * 0.5);
        
        return round($deductionDays, 1);
    }
}