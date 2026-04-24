<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        return view('master.shifts');
    }

    public function list(): JsonResponse
    {
        return response()->json(
            Shift::orderBy('name')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateShift($request);

        $shift = Shift::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Shift saved successfully.',
            'shift' => $shift,
        ]);
    }

    public function update(Request $request, $shiftId): JsonResponse
    {
        $shift = Shift::findOrFail($shiftId);

        $data = $this->validateShift($request, $shift->id);
        $shift->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Shift updated successfully.',
            'shift' => $shift,
        ]);
    }

    public function destroy($shiftId): JsonResponse
    {
        $shift = Shift::findOrFail($shiftId);
        $shift->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shift deleted successfully.',
        ]);
    }

    private function validateShift(Request $request, ?int $shiftId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255|unique:shifts,name,' . $shiftId,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'late_min' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'sl_start_limit' => 'nullable|integer|min:0',
            'sl_end_limit' => 'nullable|integer|min:0',
            'week_offs' => 'nullable|array',
            'week_offs.*' => 'integer|between:0,6',
            'half_days' => 'nullable|array',
            'half_days.*' => 'integer|between:0,6',
            'full_day_hr' => 'nullable|numeric|min:0',
            'half_day_hr' => 'nullable|numeric|min:0',
            'extended_hr' => 'nullable|numeric|min:0',
        ]);
    }
}


