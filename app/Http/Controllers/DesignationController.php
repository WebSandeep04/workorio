<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesignationController extends Controller
{
    public function index()
    {
        return view('master.designations');
    }

    public function list(): JsonResponse
    {
        return response()->json(
            Designation::orderBy('title')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateDesignation($request);
        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode();
        }

        $designation = Designation::create($data);
        $designation->update(['code' => $this->formatCode($designation->id)]);

        return response()->json([
            'success' => true,
            'message' => 'Designation saved successfully.',
            'designation' => $designation,
        ]);
    }

    public function update(Request $request, $designationId): JsonResponse
    {
        $designation = Designation::findOrFail($designationId);
        $request->merge(['code' => $designation->code]);

        $data = $this->validateDesignation($request, $designation->id);
        $designation->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Designation updated successfully.',
            'designation' => $designation,
        ]);
    }

    public function destroy($designationId): JsonResponse
    {
        $designation = Designation::findOrFail($designationId);

        if ($designation->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete designation assigned to employees.',
            ], 422);
        }

        $designation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully.',
        ]);
    }

    private function validateDesignation(Request $request, ?int $designationId = null): array
    {
        return $request->validate([
            'code' => 'nullable|string|max:50|unique:designations,code,' . $designationId,
            'title' => 'required|string|max:255|unique:designations,title,' . $designationId,
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function generateTempCode(): string
    {
        return 'designation-temp-' . Str::uuid()->toString();
    }

    private function formatCode(int $id): string
    {
        return 'Designation-' . $id;
    }
}

