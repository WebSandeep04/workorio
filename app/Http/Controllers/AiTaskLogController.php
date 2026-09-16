<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiApiLog;

class AiTaskLogController extends Controller
{
    public function index()
    {
        return view('worklog.ai-task-logs');
    }

    public function fetch(Request $request)
    {
        $query = AiApiLog::query()->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('model', 'like', "%{$searchValue}%")
                  ->orWhere('endpoint', 'like', "%{$searchValue}%")
                  ->orWhere('error_message', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = $query->count();
        
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        $logs = $query->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // basic filtering implemented above actually filters total records so this should be filtered count if using advanced datatables, but keeping it simple
            'data' => $logs
        ]);
    }
}
