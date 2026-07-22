<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\PayrollSetting::first() ?? new \App\Models\PayrollSetting();
        return view('payroll.settings', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'salary_cycle_start' => 'required|integer|min:1|max:31',
            'salary_cycle_end' => 'required|integer|min:1|max:31',
            'attendance_based' => 'boolean',
            'pf_enabled' => 'boolean',
            'esi_enabled' => 'boolean',
            'pt_enabled' => 'boolean',
            'tds_enabled' => 'boolean',
        ]);

        $settings = \App\Models\PayrollSetting::first();
        if ($settings) {
            $settings->update($data);
        } else {
            \App\Models\PayrollSetting::create($data);
        }

        return response()->json(['success' => true, 'message' => 'Payroll Settings saved successfully.']);
    }
}
