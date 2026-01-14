<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarEventsSetupController extends Controller
{
    public function index()
    {
        return view('calendar.events');
    }

    public function fetch(Request $request)
    {
        if (!Schema::hasTable('calendar_events')) {
            return response()->json([]);
        }

        $query = DB::table('calendar_events')->select('id','name','event_date','created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $events = $query->orderByDesc('event_date')->orderByDesc('id')->paginate(10);
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_date' => 'nullable|date',
        ]);
        $id = DB::table('calendar_events')->insertGetId([
            'name' => $validated['name'],
            'event_date' => $validated['event_date'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_date' => 'nullable|date',
        ]);
        $count = DB::table('calendar_events')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'event_date' => $validated['event_date'] ?? null,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('calendar_events')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}
