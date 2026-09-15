<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SignalTaskController extends Controller
{
    /**
     * Display the signal task page
     */
    public function index()
    {
        return view('worklog.signal-task');
    }

    /**
     * Fetch all tasks from signal_whatsapp_msg table
     */
    public function fetch()
    {
        try {
            $tasks = DB::table('signal_whatsapp_msg')
                ->leftJoin('signal_ai_tasks', 'signal_whatsapp_msg.id', '=', 'signal_ai_tasks.message_id')
                ->select('signal_whatsapp_msg.*', 'signal_ai_tasks.title as ai_title', 'signal_ai_tasks.description as ai_description', 'signal_ai_tasks.id as ai_task_id', 'signal_ai_tasks.status as ai_status')
                ->orderBy('signal_whatsapp_msg.id', 'desc')
                ->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Error fetching signal tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process AI tasks on demand
     */
    public function processAi()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('process:signal-tasks');
            return response()->json(['success' => true, 'message' => 'AI processing completed successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error processing AI tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing AI tasks', 'message' => $e->getMessage()], 500);
        }
    }
}
