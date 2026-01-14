<?php

namespace App\Http\Controllers;

use App\Models\LateReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LateReasonController extends Controller
{
    public function index()
    {
        return view('master.late_reasons');
    }

public function list(): JsonResponse
{
    return response()->json(
        LateReason::where('id', '!=', 6)
            ->orderBy('reason')
            ->get()
    );
}


    public function store(Request $request): JsonResponse
    {
        $data = $this->validateLateReason($request);

        $lateReason = LateReason::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Late reason saved successfully.',
            'late_reason' => $lateReason,
        ]);
    }

    public function update(Request $request, $lateReasonId): JsonResponse
    {
        $lateReason = LateReason::findOrFail($lateReasonId);

        $data = $this->validateLateReason($request, $lateReason->id);
        $lateReason->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Late reason updated successfully.',
            'late_reason' => $lateReason,
        ]);
    }

    public function destroy($lateReasonId): JsonResponse
    {
        $lateReason = LateReason::findOrFail($lateReasonId);
        $lateReason->delete();

        return response()->json([
            'success' => true,
            'message' => 'Late reason deleted successfully.',
        ]);
    }

    private function validateLateReason(Request $request, ?int $lateReasonId = null): array
    {
        return $request->validate([
            'reason' => 'required|string|max:255|unique:late_reasons,reason,' . $lateReasonId,
            'active' => 'nullable|boolean',
        ]);
    }
}
