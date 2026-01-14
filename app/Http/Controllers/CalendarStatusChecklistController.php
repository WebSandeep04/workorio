<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarStatusChecklistController extends Controller
{
    public function index()
    {
        return view('calendar.status-checklist');
    }

    public function fetch(Request $request)
    {
        // 1. Fetch statuses with search and pagination
        $query = Schema::hasTable('calendar_statuses')
            ? DB::table('calendar_statuses')->orderBy('name')
            : collect(); // returns Builder or Collection

        // If it's a builder (table exists)
        if ($query instanceof \Illuminate\Database\Query\Builder) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }
            $statuses = $query->paginate(10);
        } else {
            // Empty paginator if table doesn't exist
            $statuses = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        // 2. Fetch all active checklists (for the modal)
        $checklists = Schema::hasTable('checklists')
            ? DB::table('checklists')->where('is_active', 1)->orderBy('name')->get()
            : collect();

        // 3. Fetch relationships ONLY for the paginated statuses
        $relationships = [];
        if (Schema::hasTable('calendar_status_checklist') && $statuses->count() > 0) {
            $statusIds = $statuses->pluck('id')->toArray();
            $relationships = DB::table('calendar_status_checklist')
                ->whereIn('status_id', $statusIds)
                ->select('status_id', 'checklist_id')
                ->get()
                ->groupBy('status_id')
                ->map(function ($items) {
                    return $items->pluck('checklist_id')->toArray();
                })->toArray();
        }

        return response()->json([
            'statuses' => $statuses,
            'checklists' => $checklists,
            'relationships' => $relationships,
        ]);
    }

    public function updateRelationships(Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|integer|exists:calendar_statuses,id',
            'checklist_ids' => 'nullable|array',
            'checklist_ids.*' => 'integer|exists:checklists,id',
        ]);

        if (!Schema::hasTable('calendar_status_checklist')) {
            return response()->json(['success' => false, 'message' => 'Table not found. Please run migrations.'], 500);
        }

        $statusId = (int)$validated['status_id'];
        $checklistIds = $validated['checklist_ids'] ?? [];

        DB::table('calendar_status_checklist')->where('status_id', $statusId)->delete();

        $rows = [];
        foreach ($checklistIds as $cid) {
            $rows[] = [
                'status_id' => $statusId,
                'checklist_id' => (int)$cid,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($rows)) {
            DB::table('calendar_status_checklist')->insert($rows);
        }

        return response()->json(['success' => true, 'message' => 'Relationships updated successfully']);
    }
}


