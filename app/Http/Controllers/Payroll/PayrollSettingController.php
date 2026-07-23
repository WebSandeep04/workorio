<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollSettingController extends Controller
{
    public function index()
    {
        Log::info('Action: Fetched Payroll Settings', [
            'model' => \App\Models\PayrollSetting::class,
            'table' => (new \App\Models\PayrollSetting())->getTable(),
            'details' => 'Fetching the first record from the payroll settings table or creating a new instance if it does not exist to pass to the view.',
        ]);
        $settings = \App\Models\PayrollSetting::first() ?? new \App\Models\PayrollSetting();
        return view('payroll.settings', compact('settings'));
    }

    public function store(Request $request)
    {
        Log::info('Action: Initiated Saving Payroll Settings', [
            'model' => \App\Models\PayrollSetting::class,
            'table' => (new \App\Models\PayrollSetting())->getTable(),
            'details' => 'Validating incoming request data to update or create a record in the payroll settings table.',
            'incoming_data' => $request->all(),
        ]);

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
            Log::info('Action: Updating Payroll Settings', [
                'model' => \App\Models\PayrollSetting::class,
                'table' => $settings->getTable(),
                'details' => 'Existing record found. Updating the payroll settings table with new validated data.',
                'old_data' => $settings->toArray(),
                'new_data' => $data,
            ]);
            $settings->update($data);
        } else {
            Log::info('Action: Creating Payroll Settings', [
                'model' => \App\Models\PayrollSetting::class,
                'table' => (new \App\Models\PayrollSetting())->getTable(),
                'details' => 'No existing record found. Creating a new record in the payroll settings table.',
                'data' => $data,
            ]);
            \App\Models\PayrollSetting::create($data);
        }

        return response()->json(['success' => true, 'message' => 'Payroll Settings saved successfully.']);
    }
}
