<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Get all upcoming holidays for the current year
     */
    public function getUpcomingHolidays()
    {
        $today = Carbon::today();
        $endOfYear = Carbon::now()->endOfYear();

        $holidays = Holiday::whereBetween('holiday_date', [$today, $endOfYear])
            ->orderBy('holiday_date', 'asc')
            ->get()
            ->map(function ($holiday) {
                return [
                    'id' => $holiday->id,
                    'name' => $holiday->name,
                    'date' => $holiday->holiday_date->format('Y-m-d'),
                    'day' => $holiday->holiday_date->format('l'), // Day of week (e.g., Monday)
                    'display_date' => $holiday->holiday_date->format('M d, Y'), // e.g., Jan 26, 2026
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $holidays->count(),
            'data' => $holidays
        ]);
    }
}
