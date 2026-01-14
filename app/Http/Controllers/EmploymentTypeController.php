<?php

namespace App\Http\Controllers;

use App\Models\EmploymentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        return view('master.employment-types');
    }

    public function list(): JsonResponse
    {
        return response()->json(
            EmploymentType::orderBy('name')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateEmploymentType($request);
        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode();
        }

        $type = EmploymentType::create($data);
        $type->update(['code' => $this->formatCode($type->id)]);

        return response()->json([
            'success' => true,
            'message' => 'Employment type saved successfully.',
            'employment_type' => $type,
        ]);
    }

    public function update(Request $request, $employmentTypeId): JsonResponse
    {
        $type = EmploymentType::findOrFail($employmentTypeId);
        $request->merge(['code' => $type->code]);

        $data = $this->validateEmploymentType($request, $type->id);
        $type->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Employment type updated successfully.',
            'employment_type' => $type,
        ]);
    }

    public function destroy($employmentTypeId): JsonResponse
    {
        $type = EmploymentType::findOrFail($employmentTypeId);

        if ($type->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete employment type assigned to employees.',
            ], 422);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employment type deleted successfully.',
        ]);
    }

    private function validateEmploymentType(Request $request, ?int $employmentTypeId = null): array
    {
        return $request->validate([
            'code' => 'nullable|string|max:50|unique:employment_types,code,' . $employmentTypeId,
            'name' => 'required|string|max:255|unique:employment_types,name,' . $employmentTypeId,
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function generateTempCode(): string
    {
        return 'employment-type-temp-' . Str::uuid()->toString();
    }

    private function formatCode(int $id): string
    {
        return 'EmploymentType-' . $id;
    }
}

