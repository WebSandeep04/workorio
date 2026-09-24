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
     * Fetch all AI tasks from signal_ai_tasks table
     */
    public function fetchAiTasks()
    {
        try {
            $tasks = DB::table('signal_ai_tasks')
                ->orderBy('id', 'desc')
                ->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Error fetching AI tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading AI tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Process AI tasks on demand
     */
    public function processAi(\App\Services\AiTaskDetectorService $aiService)
    {
        try {
            $aiService->processPendingMessages();
            return response()->json(['success' => true, 'message' => 'AI processing completed successfully.']);
        } catch (\Exception $e) {
            \Log::error('Error processing AI tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing AI tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark a signal task or AI task as converted
     */
    public function markConverted(Request $request)
    {
        try {
            $id = $request->id;
            $type = $request->type;
            
            if ($type === 'ai_task') {
                DB::table('signal_ai_tasks')->where('id', $id)->update(['status' => 'converted']);
            } else if ($type === 'message') {
                DB::table('signal_whatsapp_msg')->where('id', $id)->update(['status' => 'converted']);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error marking converted: ' . $e->getMessage());
            return response()->json(['error' => 'Error marking converted', 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * Store an immediate task directly
     */
    public function storeImmediateTask(Request $request)
    {
        try {
            DB::table('immediate_tasks')->insert([
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Mark the original message/ai_task as converted as well
            if ($request->type === 'ai_task' && $request->id) {
                DB::table('signal_ai_tasks')->where('id', $request->id)->update(['status' => 'converted']);
            } else if ($request->type === 'message' && $request->id) {
                DB::table('signal_whatsapp_msg')->where('id', $request->id)->update(['status' => 'converted']);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error storing immediate task: ' . $e->getMessage());
            return response()->json(['error' => 'Error storing immediate task', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch immediate tasks
     */
    public function fetchImmediateTasks()
    {
        try {
            $tasks = DB::table('immediate_tasks')->orderBy('id', 'desc')->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Error fetching immediate tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching immediate tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark an immediate task as done
     */
    public function markImmediateTaskDone(Request $request)
    {
        try {
            DB::table('immediate_tasks')->where('id', $request->id)->update([
                'status' => 'done',
                'updated_at' => now()
            ]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error marking immediate task done: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating immediate task', 'message' => $e->getMessage()], 500);
        }
    }
}
