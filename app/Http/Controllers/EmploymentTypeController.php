<?php

namespace App\Http\Controllers;

use App\Models\EmploymentType;
use App\Models\EmploymentTypeLeaveRule;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = collect();
        if (Schema::hasTable('leave_types')) {
            $leaveTypes = LeaveType::where('status', true)->get();
        }
        return view('master.employment-types', compact('leaveTypes'));
    }

    public function list(): JsonResponse
    {
        $query = EmploymentType::orderBy('name');
        
        if (Schema::hasTable('employment_type_leave_rules')) {
            $query->with('leaveRules');
        }

        return response()->json($query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateEmploymentType($request);
        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode();
        }

        DB::beginTransaction();
        try {
            $type = EmploymentType::create($data);
            $type->update(['code' => $this->formatCode($type->id)]);

            $this->syncLeaveRules($type, $request->input('rules', []));

            DB::commit();

            // Load relations for response
            if (Schema::hasTable('employment_type_leave_rules')) {
                $type->load('leaveRules');
            }

            return response()->json([
                'success' => true,
                'message' => 'Employment type saved successfully.',
                'employment_type' => $type,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error saving: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $employmentTypeId): JsonResponse
    {
        $type = EmploymentType::findOrFail($employmentTypeId);
        $request->merge(['code' => $type->code]);

        $data = $this->validateEmploymentType($request, $type->id);

        DB::beginTransaction();
        try {
            $type->update($data);

            $this->syncLeaveRules($type, $request->input('rules', []));

            DB::commit();

            if (Schema::hasTable('employment_type_leave_rules')) {
                $type->load('leaveRules');
            }

            return response()->json([
                'success' => true,
                'message' => 'Employment type updated successfully.',
                'employment_type' => $type,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
        }
    }

    private function syncLeaveRules(EmploymentType $type, array $rulesData)
    {
        if (!Schema::hasTable('employment_type_leave_rules')) {
            return;
        }

        foreach ($rulesData as $leaveTypeId => $rule) {
            if (!isset($rule['enabled']) || $rule['enabled'] != 1) {
                // Remove if unchecked
                EmploymentTypeLeaveRule::where('employment_type_id', $type->id)
                    ->where('leave_type_id', $leaveTypeId)
                    ->delete();
                continue;
            }

            EmploymentTypeLeaveRule::updateOrCreate(
                [
                    'employment_type_id' => $type->id,
                    'leave_type_id' => $leaveTypeId,
                ],
                [
                    'generation_type' => $rule['generation_type'] ?? 'prefill',
                    'value' => (int) ($rule['value'] ?? 0),
                    'carry_forward_allowed' => isset($rule['carry_forward_allowed']) && $rule['carry_forward_allowed'] == 1,
                    'max_carry_forward' => (int) ($rule['max_carry_forward'] ?? 0),
                ]
            );
        }
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
            'rh_allowed' => 'nullable|integer|min:0',
            'sl_allowed' => 'nullable|integer|min:0',
            'no_of_half_days' => 'nullable|integer|min:0',
            'rules' => 'nullable|array',
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
