<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiSchedulerConfig;
use App\Models\AiSchedulerFixedPass;

class AiSchedulerSetupController extends Controller
{
    public function index()
    {
        return view('setup.ai_scheduler');
    }

    public function fetchConfig()
    {
        $config = AiSchedulerConfig::first();
        $passes = AiSchedulerFixedPass::orderBy('run_at')->get();
        
        return response()->json([
            'config' => $config,
            'passes' => $passes
        ]);
    }

    public function storeConfig(Request $request)
    {
        $data = $request->validate([
            'office_start_time' => 'required|date_format:H:i',
            'office_end_time' => 'required|date_format:H:i',
            'office_day_frequency' => 'required|integer|min:1',
            'office_day_lookback' => 'required|integer|min:1',
            'off_day_frequency' => 'required|integer|min:1',
            'off_day_lookback' => 'required|integer|min:1',
            'week_offs' => 'nullable|array',
            'week_offs.*' => 'integer|between:0,6'
        ]);

        $config = AiSchedulerConfig::first();
        if ($config) {
            $config->update($data);
        } else {
            AiSchedulerConfig::create($data);
        }

        return response()->json(['success' => true, 'message' => 'Configuration updated successfully.']);
    }

    public function storePass(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'run_at' => 'required|date_format:H:i',
            'lookback_minutes' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean'
        ]);

        $pass = AiSchedulerFixedPass::create($data);

        return response()->json(['success' => true, 'message' => 'Fixed pass added successfully.', 'pass' => $pass]);
    }

    public function updatePass(Request $request, $id)
    {
        $pass = AiSchedulerFixedPass::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'run_at' => 'required|date_format:H:i',
            'lookback_minutes' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean'
        ]);

        $pass->update($data);

        return response()->json(['success' => true, 'message' => 'Fixed pass updated successfully.']);
    }

    public function destroyPass($id)
    {
        $pass = AiSchedulerFixedPass::findOrFail($id);
        $pass->delete();

        return response()->json(['success' => true, 'message' => 'Fixed pass deleted successfully.']);
    }
}
