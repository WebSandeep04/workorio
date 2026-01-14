<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDependencyType;
use App\Models\WorkflowTask;
use App\Models\WorkflowTaskDependency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkflowTaskDependencyController extends Controller
{
    public function index($workflowTemplateId): JsonResponse
    {
        $dependencies = WorkflowTaskDependency::query()
            ->with([
                'type:id,code,name,allows_lag',
                'predecessor' => function ($query) {
                    $query->select('id', 'name', 'owner_name');
                },
                'successor' => function ($query) {
                    $query->select('id', 'name', 'owner_name');
                },
            ])
            ->where('workflow_template_id', $workflowTemplateId)
            ->orderBy('id')
            ->get()
            ->map(function ($dependency) {
                return [
                    'id' => $dependency->id,
                    'workflow_template_id' => $dependency->workflow_template_id,
                    'dependency_type_id' => $dependency->dependency_type_id,
                    'dependency_type_code' => $dependency->type->code ?? null,
                    'dependency_type_name' => $dependency->type->name ?? null,
                    'allows_lag' => (bool) ($dependency->type->allows_lag ?? false),
                    'predecessor_task_id' => $dependency->predecessor_task_id,
                    'predecessor_name' => $dependency->predecessor->name ?? null,
                    'predecessor_owner_name' => $dependency->predecessor->owner_name ?? null,
                    'successor_task_id' => $dependency->successor_task_id,
                    'successor_name' => $dependency->successor->name ?? null,
                    'successor_owner_name' => $dependency->successor->owner_name ?? null,
                    'lag_days' => $dependency->lag_days,
                    'notes' => $dependency->notes,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $dependencies,
        ]);
    }

    public function store(Request $request, $workflowTemplateId): JsonResponse
    {
        $validated = $this->validatePayload($request, $workflowTemplateId);

        $dependency = WorkflowTaskDependency::create($validated + [
            'workflow_template_id' => $workflowTemplateId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dependency added.',
            'data' => $this->serialize($dependency->fresh(['type', 'predecessor', 'successor'])),
        ]);
    }

    public function update(Request $request, $workflowTemplateId, $dependencyId): JsonResponse
    {
        $dependency = WorkflowTaskDependency::query()
            ->where('workflow_template_id', $workflowTemplateId)
            ->findOrFail($dependencyId);

        $validated = $this->validatePayload($request, $workflowTemplateId, $dependencyId);

        $dependency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dependency updated.',
            'data' => $this->serialize($dependency->fresh(['type', 'predecessor', 'successor'])),
        ]);
    }

    public function destroy($workflowTemplateId, $dependencyId): JsonResponse
    {
        $dependency = WorkflowTaskDependency::query()
            ->where('workflow_template_id', $workflowTemplateId)
            ->findOrFail($dependencyId);

        $dependency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dependency removed.',
        ]);
    }

    public function types(): JsonResponse
    {
        $types = WorkflowDependencyType::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'allows_lag']);

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    protected function validatePayload(Request $request, $workflowTemplateId, $dependencyId = null): array
    {
        $templateTaskIds = WorkflowTask::query()
            ->where('workflow_template_id', $workflowTemplateId)
            ->pluck('id')
            ->all();

        if (empty($templateTaskIds)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Add tasks to this template before creating dependencies.',
            ], 422));
        }

        $validated = $request->validate([
            'dependency_type_id' => ['required', 'exists:workflow_dependency_types,id'],
            'predecessor_task_id' => ['required', 'integer', Rule::in($templateTaskIds)],
            'successor_task_id' => ['required', 'integer', 'different:predecessor_task_id', Rule::in($templateTaskIds)],
            'lag_days' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $type = WorkflowDependencyType::findOrFail($validated['dependency_type_id']);

        if (!$type->allows_lag) {
            $validated['lag_days'] = null;
        }
        if (isset($validated['lag_days']) && $validated['lag_days'] !== null) {
            $validated['lag_days'] = (int) $validated['lag_days'];
        }

        if (!isset($validated['notes']) || $validated['notes'] === '') {
            $validated['notes'] = null;
        }

        $existsQuery = WorkflowTaskDependency::query()
            ->where('workflow_template_id', $workflowTemplateId)
            ->where('predecessor_task_id', $validated['predecessor_task_id'])
            ->where('successor_task_id', $validated['successor_task_id'])
            ->where('dependency_type_id', $validated['dependency_type_id']);

        if ($dependencyId) {
            $existsQuery->where('id', '!=', $dependencyId);
        }

        if ($existsQuery->exists()) {
            abort(response()->json([
                'success' => false,
                'message' => 'This dependency already exists.',
            ], 422));
        }

        return $validated;
    }

    protected function serialize(WorkflowTaskDependency $dependency): array
    {
        return [
            'id' => $dependency->id,
            'workflow_template_id' => $dependency->workflow_template_id,
            'dependency_type_id' => $dependency->dependency_type_id,
            'dependency_type_code' => $dependency->type->code ?? null,
            'dependency_type_name' => $dependency->type->name ?? null,
            'allows_lag' => (bool) ($dependency->type->allows_lag ?? false),
            'predecessor_task_id' => $dependency->predecessor_task_id,
            'predecessor_name' => $dependency->predecessor->name ?? null,
            'predecessor_owner_name' => $dependency->predecessor->owner_name ?? null,
            'successor_task_id' => $dependency->successor_task_id,
            'successor_name' => $dependency->successor->name ?? null,
            'successor_owner_name' => $dependency->successor->owner_name ?? null,
            'lag_days' => $dependency->lag_days,
            'notes' => $dependency->notes,
        ];
    }
}


