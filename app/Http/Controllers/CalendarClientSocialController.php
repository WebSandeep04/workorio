<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarClientSocialController extends Controller
{
    public function index()
    {
        return view('calendar.client-social');
    }

    public function fetch(Request $request)
    {
        // 1. Fetch clients with search and pagination (Active only)
        $query = DB::table('calendar_clients')->where('is_active', 1);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $clients = $query->orderBy('name')->paginate(10);

        // 2. Fetch all active social handles (for the modal)
        // Check if is_active column exists in social_handles table
        $hasIsActive = Schema::hasColumn('calendar_social_handles', 'is_active');
        $socialHandles = $hasIsActive 
            ? DB::table('calendar_social_handles')->where('is_active', 1)->orderBy('name')->get()
            : DB::table('calendar_social_handles')->orderBy('name')->get();
        
        // 3. Get existing relationships ONLY for paginated clients
        $relationships = [];
        if (Schema::hasTable('calendar_client_social') && $clients->count() > 0) {
            $clientIds = $clients->pluck('id')->toArray();
            $relationships = DB::table('calendar_client_social')
                ->whereIn('client_id', $clientIds)
                ->select('client_id', 'social_handle_id')
                ->get()
                ->groupBy('client_id')
                ->map(function ($items) {
                    return $items->pluck('social_handle_id')->toArray();
                })
                ->toArray();
        }
        
        return response()->json([
            'clients' => $clients,
            'social_handles' => $socialHandles,
            'relationships' => $relationships
        ]);
    }

    public function updateRelationships(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:calendar_clients,id',
            'social_handle_ids' => 'nullable|array',
            'social_handle_ids.*' => 'exists:calendar_social_handles,id',
        ]);
        
        $clientId = $validated['client_id'];
        $socialHandleIds = $validated['social_handle_ids'] ?? [];
        
        if (!Schema::hasTable('calendar_client_social')) {
            return response()->json(['success' => false, 'message' => 'Table not found. Please run migrations.'], 500);
        }
        
        // Delete existing relationships
        DB::table('calendar_client_social')->where('client_id', $clientId)->delete();
        
        // Insert new relationships
        $insertData = [];
        foreach ($socialHandleIds as $socialHandleId) {
            $insertData[] = [
                'client_id' => $clientId,
                'social_handle_id' => $socialHandleId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($insertData)) {
            DB::table('calendar_client_social')->insert($insertData);
        }
        
        return response()->json(['success' => true, 'message' => 'Relationships updated successfully']);
    }
}
