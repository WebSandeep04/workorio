<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarMissedReasonController extends Controller
{
    public function index()
    {
        return view('calendar.missed-reasons');
    }

    public function fetch(Request $request)
    {
        if (!Schema::hasTable('calendar_missed_reasons')) {
            return response()->json([]);
        }

        $query = DB::table('calendar_missed_reasons')->select('id', 'name', 'is_active', 'created_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $reasons = $query->orderBy('name')->paginate(10);
        return response()->json($reasons);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $id = DB::table('calendar_missed_reasons')->insertGetId([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $count = DB::table('calendar_missed_reasons')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? 1,
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0]);
    }

    public function destroy($id)
    {
        $deleted = DB::table('calendar_missed_reasons')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}
