<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    public function index()
    {
        return view('holiday.index');
    }

    public function fetchHolidays(Request $request)
    {
        $query = Holiday::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('holiday_date', 'like', "%{$search}%");
        }

        $holidays = $query->orderBy('holiday_date', 'desc')->paginate(10);

        return response()->json($holidays);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,NULL,id']);

        Holiday::create([
            'name' => $request->name,
            'holiday_date' => $request->holiday_date]);

        return response()->json(['success' => true, 'message' => 'Holiday added successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,' . $id . ',id']);

        $holiday = Holiday::where('id', $id)
            ->firstOrFail();

        $holiday->update([
            'name' => $request->name,
            'holiday_date' => $request->holiday_date]);

        return response()->json(['success' => true, 'message' => 'Holiday updated successfully.']);
    }

    public function destroy($id)
    {
        $holiday = Holiday::where('id', $id)
            ->firstOrFail();

        $holiday->delete();

        return response()->json(['success' => true, 'message' => 'Holiday deleted successfully.']);
    }
}
