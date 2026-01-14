<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarStatusController extends Controller
{
    public function index()
    {
        return view('calendar.status');
    }

    public function fetch(Request $request)
    {
        $query = DB::table('calendar_statuses');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $statuses = $query->orderBy('name')->paginate(10);
        return response()->json($statuses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        DB::table('calendar_statuses')->insert([
            'name' => $validated['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Status created']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $count = DB::table('calendar_statuses')->where('id', (int)$id)->update([
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);
        return response()->json(['success' => $count > 0, 'message' => 'Status updated']);
    }

    public function destroy($id)
    {
        $deleted = DB::table('calendar_statuses')->where('id', (int)$id)->delete();
        return response()->json(['success' => $deleted > 0]);
    }
}


