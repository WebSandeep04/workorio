<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarClientController extends Controller
{
    public function index()
    {
        return view('calendar.clients');
    }

    public function fetch(Request $request)
    {
        // 1. Fetch clients with search and pagination
        $query = DB::table('calendar_clients');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $clients = $query->orderBy('name')->paginate(10);

        // 2. Fetch all social handles (for the modal)
        $socialHandles = \Illuminate\Support\Facades\Schema::hasTable('calendar_social_handles')
            ? DB::table('calendar_social_handles')->orderBy('name')->get()
            : collect();

        // 3. Fetch relationships ONLY for the paginated clients
        $relationships = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('calendar_client_social') && $clients->count() > 0) {
            $clientIds = $clients->pluck('id')->toArray();
            $relationships = DB::table('calendar_client_social')
                ->whereIn('client_id', $clientIds)
                ->select('client_id', 'social_handle_id')
                ->get()
                ->groupBy('client_id')
                ->map(function ($items) {
                    return $items->pluck('social_handle_id')->toArray();
                })->toArray();
        }

        return response()->json([
            'clients' => $clients,
            'social_handles' => $socialHandles,
            'relationships' => $relationships,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'social_handle_ids' => 'nullable|array',
            'social_handle_ids.*' => 'integer|exists:calendar_social_handles,id',
        ]);
        $clientId = DB::table('calendar_clients')->insertGetId([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Save selected social handles relationships at creation time
        if (!empty($validated['social_handle_ids']) && \Illuminate\Support\Facades\Schema::hasTable('calendar_client_social')) {
            $rows = [];
            foreach ($validated['social_handle_ids'] as $sid) {
                $rows[] = [
                    'client_id' => $clientId,
                    'social_handle_id' => (int)$sid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($rows)) {
                DB::table('calendar_client_social')->insert($rows);
            }
        }

        return response()->json(['success' => true, 'id' => $clientId]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'social_handle_ids' => 'nullable|array',
            'social_handle_ids.*' => 'integer|exists:calendar_social_handles,id',
        ]);
        $count = DB::table('calendar_clients')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'updated_at' => now(),
        ]);
        // Update social handles relationships
        if (\Illuminate\Support\Facades\Schema::hasTable('calendar_client_social')) {
            DB::table('calendar_client_social')->where('client_id', (int)$id)->delete();
            $rows = [];
            foreach (($validated['social_handle_ids'] ?? []) as $sid) {
                $rows[] = [
                    'client_id' => (int)$id,
                    'social_handle_id' => (int)$sid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($rows)) {
                DB::table('calendar_client_social')->insert($rows);
            }
        }
        return response()->json(['success' => $count > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('calendar_clients')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}


