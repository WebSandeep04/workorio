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
}
