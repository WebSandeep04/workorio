<?php

namespace App\Http\Controllers;

use App\Models\WorkflowDependencyType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowDependencyController extends Controller
{
    /**
     * Display the Workflow Dependencies page.
     */
    public function index()
    {
        return view('workflow.dependencies.index');
    }

    /**
     * Fetch dependency types.
     */
    public function fetch(): JsonResponse
    {
        $dependencies = WorkflowDependencyType::query()
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'description', 'allows_lag', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => $dependencies,
        ]);
    }

    /**
     * Store a dependency type.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:workflow_dependency_types,code'],
            'name' => ['required', 'string', 'max:255'],
            'allows_lag' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $dependency = WorkflowDependencyType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dependency saved successfully.',
            'data' => $dependency,
        ]);
    }

    /**
     * Update a dependency type.
     */
    public function update(Request $request, $dependencyId): JsonResponse
    {
        $dependency = WorkflowDependencyType::query()->findOrFail($dependencyId);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:workflow_dependency_types,code,' . $dependency->id],
            'name' => ['required', 'string', 'max:255'],
            'allows_lag' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $dependency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dependency updated successfully.',
            'data' => $dependency,
        ]);
    }

    /**
     * Remove a dependency type.
     */
    public function destroy($dependencyId): JsonResponse
    {
        $dependency = WorkflowDependencyType::query()->findOrFail($dependencyId);
        $dependency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dependency deleted successfully.',
        ]);
    }
}

