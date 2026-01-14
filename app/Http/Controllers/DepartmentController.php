<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        return view('master.departments', compact('branches'));
    }

    public function list(Request $request): JsonResponse
    {
        $query = Department::with('branch')->orderBy('name');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateDepartment($request);
        if (empty($data['branch_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Branch is required.',
            ], 422);
        }

        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode('dept');
        }

        $department = Department::create($data);
        $department->update(['code' => $this->formatDepartmentCode($department->id)]);
        $department->load('branch');

        return response()->json([
            'success' => true,
            'message' => 'Department saved successfully.',
            'department' => $department,
        ]);
    }

    public function update(Request $request, $departmentId): JsonResponse
    {
        $department = Department::findOrFail($departmentId);
        $request->merge([
            'code' => $department->code,
            'branch_id' => $department->branch_id,
        ]);

        $data = $this->validateDepartment($request, $department->id);
        $department->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'department' => $department->load('branch'),
        ]);
    }

    public function destroy($departmentId): JsonResponse
    {
        $department = Department::findOrFail($departmentId);

        if ($department->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department with employees.',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }

    public function options(Request $request): JsonResponse
    {
        $query = Department::query()->orderBy('name');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        return response()->json($query->get(['id', 'name', 'branch_id']));
    }

    private function validateDepartment(Request $request, ?int $deptId = null): array
    {
        return $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $deptId,
            'name' => 'required|string|max:255|unique:departments,name,' . $deptId . ',id,branch_id,' . $request->branch_id,
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function generateTempCode(string $prefix): string
    {
        return $prefix . '-temp-' . Str::uuid()->toString();
    }

    private function formatDepartmentCode(int $id): string
    {
        return 'Dept-' . $id;
    }
}

