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

    public function fetchEvents(Request $request, $clientId)
    {
        $client = Schema::hasTable('calendar_clients')
            ? DB::table('calendar_clients')->where('id', (int)$clientId)->first(['id','name'])
            : null;

        $monthParam = $request->get('month', now()->format('Y-m'));
        $startOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->endOfMonth()->format('Y-m-d');

        $events = Schema::hasTable('calendar_events')
            ? DB::table('calendar_events')
                ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
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
            'month' => 'required|date_format:Y-m',
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
        
        $monthParam = $validated['month'];
        $startOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->endOfMonth()->format('Y-m-d');
         
         $displayedEventIds = DB::table('calendar_events')
            ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
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

    public function fetchCommonEvents(Request $request, $clientId)
    {
        $client = Schema::hasTable('calendar_clients')
            ? DB::table('calendar_clients')->where('id', (int)$clientId)->first(['id','name'])
            : null;

        $commonEvents = Schema::hasTable('common_events')
            ? DB::table('common_events')->where('is_active', 1)->orderBy('name')->get(['id','name','alert_before_days'])
            : collect();

        $monthParam = $request->get('month', now()->format('Y-m'));
        $startOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->endOfMonth()->format('Y-m-d');

        $existing = [];
        if (Schema::hasTable('calendar_client_common_events')) {
            $rows = DB::table('calendar_client_common_events')
                ->where('client_id', (int)$clientId)
                ->whereBetween('event_date', [$startOfMonth, $endOfMonth]) // Filter applied here
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
            'month' => 'required|date_format:Y-m',
        ]);

        if (!Schema::hasTable('calendar_client_common_events')) {
            return response()->json(['success' => false, 'message' => 'Table missing'], 500);
        }

        $monthParam = $validated['month'];
        $startOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->startOfMonth()->format('Y-m-d');
        $endOfMonth = \Carbon\Carbon::parse($monthParam . '-01')->endOfMonth()->format('Y-m-d');

        // Delete existing common events strictly for the targeted month
        DB::table('calendar_client_common_events')
            ->where('client_id', $clientId)
            ->whereBetween('event_date', [$startOfMonth, $endOfMonth])
            ->delete();

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


