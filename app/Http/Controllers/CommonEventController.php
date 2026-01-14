<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonEventController extends Controller
{
    public function index()
    {
        return view('calendar.common-events');
    }

    public function fetch(Request $request)
    {
        $query = DB::table('common_events');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $events = $query->orderBy('name')->paginate(10);
        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'alert_before_days' => 'nullable|integer|min:0|max:365',
        ]);
        DB::table('common_events')->insert([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'alert_before_days' => $validated['alert_before_days'] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'alert_before_days' => 'nullable|integer|min:0|max:365',
        ]);
        $count = DB::table('common_events')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'alert_before_days' => $validated['alert_before_days'] ?? 0,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('common_events')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}


