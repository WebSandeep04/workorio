<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClientEventLinkController extends Controller
{
    public function clientsView()
    {
        return view('calendar.client-event-links.clients');
    }

    public function fetchClients()
    {
        $clients = Schema::hasTable('calendar_clients')
            ? DB::table('calendar_clients')->orderBy('name')->get(['id','name','is_active'])
            : collect();
        return response()->json($clients);
    }

    public function eventsView($clientId)
    {
        return view('calendar.client-event-links.events', ['clientId' => (int)$clientId]);
    }

    public function fetchEvents($clientId)
    {
        $client = Schema::hasTable('calendar_clients')
            ? DB::table('calendar_clients')->where('id', (int)$clientId)->first(['id','name'])
            : null;

        $startOfMonth = now()->startOfMonth()->format('Y-m-d');

        $events = Schema::hasTable('calendar_events')
            ? DB::table('calendar_events')
                ->where('event_date', '>=', $startOfMonth)
                ->orderBy('event_date', 'asc') // Changed to asc for better upcoming view
                ->get(['id','name','event_date'])
            : collect();

        $linked = [];
        if (Schema::hasTable('calendar_event_client')) {
            $linked = DB::table('calendar_event_client')
                ->where('client_id', (int)$clientId)
                ->pluck('event_id')
                ->toArray();
        }

        return response()->json([
            'client' => $client,
            'events' => $events,
            'linked_event_ids' => $linked,
        ]);
    }

    public function saveLinks(Request $request, $clientId)
    {
        $validated = $request->validate([
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'integer|exists:calendar_events,id',
        ]);

        if (!Schema::hasTable('calendar_event_client')) {
            return response()->json(['success' => false, 'message' => 'Link table missing'], 500);
        }

        $clientId = (int)$clientId;
        $eventIds = $validated['event_ids'] ?? [];

        // We should carefully update. If we delete ALL for client, we might lose past links if they weren't in the filtered list sent to frontend.
        // However, the frontend sends "selected" list. If the user only sees future events, they only send future events.
        // If we delete all, we lose past history.
        // To preserve history: We should only sync/delete future links or handle it carefully.
        // But typically "save links" implies "this is the state".
        // The user request is visual "don't show".
        // If I simply hide them, and the user clicks "Save", the frontend sends ONLY the checked visible items.
        // If the backend wipes everything and inserts only these, the past records are LOST.
        // I must address this.
        
        // Revised Strategy for Save:
        // 1. Get existing links for this client.
        // 2. Separate them into "past" (before cutoff) and "future" (after cutoff).
        // 3. Keep "past" links untouched.
        // 4. Replace "future" links with the new list from frontend.
        
        $cutoff = now()->startOfMonth()->format('Y-m-d');
        
        // 1. Get all event IDs for this client that are "future" (>= cutoff)
        // actually, we need to join with calendar_events to know the date
        $futureLinkedIds = DB::table('calendar_event_client')
            ->join('calendar_events', 'calendar_event_client.event_id', '=', 'calendar_events.id')
            ->where('calendar_event_client.client_id', $clientId)
            ->where('calendar_events.event_date', '>=', $cutoff)
            ->pluck('calendar_event_client.event_id')
            ->toArray();
            
        // Delete existing *future* links for this client
        // We need raw delete with join or whereIn
         DB::table('calendar_event_client')
            ->whereIn('event_id', $futureLinkedIds)
            ->where('client_id', $clientId)
            ->delete();

         // (Alternative: Delete where event_id is in the list of events we displayed? 
         //  The displayed list was "all events >= cutoff".
         //  So we should delete links for any event >= cutoff.
         //  Then insert the new selection.)
         
         $displayedEventIds = DB::table('calendar_events')
            ->where('event_date', '>=', $cutoff)
            ->pluck('id')
            ->toArray();
            
         if(!empty($displayedEventIds)){
             DB::table('calendar_event_client')
                ->where('client_id', $clientId)
                ->whereIn('event_id', $displayedEventIds)
                ->delete();
         }

        $rows = [];
        foreach ($eventIds as $eid) {
            // Only insert if it's actually a future date? 
            // The frontend should only allow selecting future dates if we filtered the view.
            $rows[] = [
                'event_id' => (int)$eid,
                'client_id' => $clientId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if (!empty($rows)) {
            DB::table('calendar_event_client')->insert($rows);
        }

        return response()->json(['success' => true]);
    }

    public function fetchCommonEvents($clientId)
    {
        $client = Schema::hasTable('calendar_clients')
            ? DB::table('calendar_clients')->where('id', (int)$clientId)->first(['id','name'])
            : null;

        $commonEvents = Schema::hasTable('common_events')
            ? DB::table('common_events')->where('is_active', 1)->orderBy('name')->get(['id','name','alert_before_days'])
            : collect();

        $startOfMonth = now()->startOfMonth()->format('Y-m-d');

        $existing = [];
        if (Schema::hasTable('calendar_client_common_events')) {
            $rows = DB::table('calendar_client_common_events')
                ->where('client_id', (int)$clientId)
                ->where('event_date', '>=', $startOfMonth) // Filter applied here
                ->get(['common_event_id','event_date']);
            foreach ($rows as $r) {
                $existing[$r->common_event_id] = $existing[$r->common_event_id] ?? [];
                $existing[$r->common_event_id][] = $r->event_date;
            }
        }

        return response()->json([
            'client' => $client,
            'common_events' => $commonEvents,
            'existing' => $existing,
        ]);
    }

    public function saveCommonEvents(Request $request, $clientId)
    {
        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.common_event_id' => 'required|integer|exists:common_events,id',
            'items.*.dates' => 'required|array',
            'items.*.dates.*' => 'date',
        ]);

        if (!Schema::hasTable('calendar_client_common_events')) {
            return response()->json(['success' => false, 'message' => 'Table missing'], 500);
        }

        $clientId = (int)$clientId;
        // Clear existing rows for provided common_event_ids only, to allow partial updates
        $commonIds = array_map(function($it){ return (int)$it['common_event_id']; }, $validated['items'] ?? []);
        if (!empty($commonIds)) {
            DB::table('calendar_client_common_events')
                ->where('client_id', $clientId)
                ->whereIn('common_event_id', $commonIds)
                ->delete();
        }

        $rows = [];
        foreach (($validated['items'] ?? []) as $it) {
            $cid = (int)$it['common_event_id'];
            foreach ($it['dates'] as $d) {
                $rows[] = [
                    'client_id' => $clientId,
                    'common_event_id' => $cid,
                    'event_date' => $d,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if (!empty($rows)) {
            DB::table('calendar_client_common_events')->insert($rows);
        }

        return response()->json(['success' => true]);
    }
}


