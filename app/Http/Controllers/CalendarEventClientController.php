<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarEventClientController extends Controller
{
    public function index()
    {
        return view('calendar.event-client');
    }

    public function fetch()
    {
        $events = DB::table('calendar_events')->orderBy('name')->get();
        $clients = DB::table('calendar_clients')->where('is_active', 1)->orderBy('name')->get();
        
        // Get existing event-client relationships
        $relationships = [];
        if (Schema::hasTable('calendar_event_client')) {
            $relationships = DB::table('calendar_event_client')
                ->select('event_id', 'client_id')
                ->get()
                ->groupBy('event_id')
                ->map(function ($items) {
                    return $items->pluck('client_id')->toArray();
                })
                ->toArray();
        }
        
        // Get client-social handle relationships (to show in the view)
        $clientSocialHandles = [];
        if (Schema::hasTable('calendar_client_social')) {
            $clientSocialHandles = DB::table('calendar_client_social')
                ->select('client_id', 'social_handle_id')
                ->get()
                ->groupBy('client_id')
                ->map(function ($items) {
                    return $items->pluck('social_handle_id')->toArray();
                })
                ->toArray();
        }
        
        // Get all social handles
        $socialHandles = [];
        if (Schema::hasTable('calendar_social_handles')) {
            $hasIsActive = Schema::hasColumn('calendar_social_handles', 'is_active');
            $socialHandles = $hasIsActive 
                ? DB::table('calendar_social_handles')->where('is_active', 1)->orderBy('name')->get()
                : DB::table('calendar_social_handles')->orderBy('name')->get();
        }
        
        return response()->json([
            'events' => $events,
            'clients' => $clients,
            'relationships' => $relationships,
            'client_social_handles' => $clientSocialHandles,
            'social_handles' => $socialHandles
        ]);
    }

    public function updateRelationships(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:calendar_events,id',
            'client_ids' => 'nullable|array',
            'client_ids.*' => 'exists:calendar_clients,id',
        ]);
        
        $eventId = $validated['event_id'];
        $clientIds = $validated['client_ids'] ?? [];
        
        if (!Schema::hasTable('calendar_event_client')) {
            return response()->json(['success' => false, 'message' => 'Table not found. Please run migrations.'], 500);
        }
        
        // Delete existing relationships for this event
        DB::table('calendar_event_client')->where('event_id', $eventId)->delete();
        
        // Insert new relationships
        $insertData = [];
        foreach ($clientIds as $clientId) {
            $insertData[] = [
                'event_id' => $eventId,
                'client_id' => $clientId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (!empty($insertData)) {
            DB::table('calendar_event_client')->insert($insertData);
        }
        
        return response()->json(['success' => true, 'message' => 'Relationships updated successfully']);
    }
}
