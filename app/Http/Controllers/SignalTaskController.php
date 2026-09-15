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
            $tasks = DB::table('signal_whatsapp_msg')->orderBy('id', 'desc')->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            \Log::error('Error fetching signal tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }
}
