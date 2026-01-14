<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTask;
use App\Models\WorkflowTaskDependency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WorkflowTemplateController extends Controller
{
    /**
     * Display the Workflow Templates page.
     */
    public function index()
    {
        return view('workflow.templates.index');
    }

    /**
     * Fetch all workflow templates.
     */
    public function fetch(): JsonResponse
    {
        $templates = WorkflowTemplate::query()
            ->with(['tasks' => function ($query) {
                $query->orderBy('position');
            }])
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'description', 'created_at', 'updated_at']);

        return response()->json([
            'success' => true,
            'data' => $templates,
        ]);
    }

    /**
     * Store a newly created workflow template.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tasks' => ['nullable'],
        ]);

        $tasksPayload = $this->parseTasks($request->input('tasks'));
        $taskValidation = Validator::make(
            ['tasks' => $tasksPayload],
            [
                'tasks' => ['array'],
                'tasks.*.id' => ['nullable', 'integer'],
                'tasks.*.name' => ['required', 'string', 'max:255'],
                'tasks.*.owner_name' => ['nullable', 'string', 'max:255'],
                'tasks.*.owner_id' => ['nullable', 'integer', 'exists:users,id'],
                'tasks.*.duration_days' => ['nullable', 'integer', 'min:0'],
            ],
            [],
            [
                'tasks.*.id' => 'task id',
                'tasks.*.name' => 'task name',
                'tasks.*.owner_name' => 'task owner',
                'tasks.*.duration_days' => 'task duration (days)',
            ]
        );

        if ($taskValidation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $taskValidation->errors()->first(),
                'errors' => $taskValidation->errors(),
            ], 422);
        }

        $userId = auth()->id() ?? (int) session('user_id');

        $template = DB::transaction(function () use ($validated, $userId, $taskValidation) {
            $template = WorkflowTemplate::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'created_by' => $userId ?: null,
                'updated_by' => $userId ?: null,
            ]);

            $tasks = collect($taskValidation->validated()['tasks'] ?? [])
                ->map(function ($task, $index) use ($template) {
                    return [
                        'workflow_template_id' => $template->id,
                        'name' => $task['name'],
                        'owner_name' => $task['owner_name'] ?? null,
                        'owner_id' => $task['owner_id'] ?? null,
                        'position' => $index,
                        'duration_days' => isset($task['duration_days']) && $task['duration_days'] !== ''
                            ? (int) $task['duration_days']
                            : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })
                ->toArray();

            if (!empty($tasks)) {
                WorkflowTask::insert($tasks);
            }

            return $template->load(['tasks' => function ($query) {
                $query->orderBy('position');
            }]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully.',
            'data' => $template,
        ]);
    }

    /**
     * Update an existing workflow template.
     */
    public function show($workflowTemplateId)
    {
        $template = WorkflowTemplate::query()
            ->with(['tasks' => function ($query) {
                $query->orderBy('position');
            }])
            ->findOrFail($workflowTemplateId);

        return view('workflow.templates.show', [
            'template' => $template,
        ]);
    }

    /**
     * Update an existing workflow template.
     */
    public function update(Request $request, $workflowTemplateId): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tasks' => ['nullable'],
        ]);

        $tasksPayload = $this->parseTasks($request->input('tasks'));
        $taskValidation = Validator::make(
            ['tasks' => $tasksPayload],
            [
                'tasks' => ['array'],
                'tasks.*.id' => ['nullable', 'integer'],
                'tasks.*.name' => ['required', 'string', 'max:255'],
                'tasks.*.owner_name' => ['nullable', 'string', 'max:255'],
                'tasks.*.owner_id' => ['nullable', 'integer', 'exists:users,id'],
                'tasks.*.duration_days' => ['nullable', 'integer', 'min:0'],
            ]
        );

        if ($taskValidation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $taskValidation->errors()->first(),
                'errors' => $taskValidation->errors(),
            ], 422);
        }

        $userId = auth()->id() ?? (int) session('user_id');
        $workflowTemplate = WorkflowTemplate::query()->findOrFail($workflowTemplateId);

        $workflowTemplate = DB::transaction(function () use ($workflowTemplate, $validated, $userId, $taskValidation) {
            $workflowTemplate->fill([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'updated_by' => $userId ?: null,
            ])->save();

            $tasksPayload = collect($taskValidation->validated()['tasks'] ?? []);
            $existingTasks = $workflowTemplate->tasks()->get()->keyBy('id');
            $retainedIds = collect();
            $position = 0;

            foreach ($tasksPayload as $task) {
                $taskId = $task['id'] ?? null;
                $attributes = [
                    'name' => $task['name'],
                    'owner_name' => $task['owner_name'] ?? null,
                    'owner_id' => $task['owner_id'] ?? null,
                    'position' => $position++,
                    'duration_days' => isset($task['duration_days']) && $task['duration_days'] !== ''
                        ? (int) $task['duration_days']
                        : null,
                ];

                if ($taskId) {
                    if (!$existingTasks->has($taskId)) {
                        throw ValidationException::withMessages([
                            'tasks' => ['One or more tasks could not be found for this template.'],
                        ]);
                    }

                    $workflowTemplate->tasks()
                        ->whereKey($taskId)
                        ->update($attributes);

                    $retainedIds->push($taskId);
                } else {
                    $workflowTemplate->tasks()->create($attributes + [
                        'workflow_template_id' => $workflowTemplate->id,
                    ]);
                }
            }

            $deleteIds = $existingTasks->keys()->diff($retainedIds);
            if ($deleteIds->isNotEmpty()) {
                $workflowTemplate->tasks()->whereIn('id', $deleteIds)->delete();
            }

            return $workflowTemplate->load(['tasks' => function ($query) {
                $query->orderBy('position');
            }]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully.',
            'data' => $workflowTemplate,
        ]);
    }

    /**
     * Remove the specified workflow template.
     */
    public function destroy($workflowTemplateId): JsonResponse
    {
        $workflowTemplate = WorkflowTemplate::query()->findOrFail($workflowTemplateId);
        $workflowTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);
    }

    /**
     * Provide list of users to assign as task owners.
     */
    public function users(): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->email ?? 'User #' . $user->id,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Decode and normalize task payload.
     */
    protected function parseTasks($tasks): array
    {
        if (is_string($tasks)) {
            $decoded = json_decode($tasks, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $tasks = $decoded;
            }
        }

        if (!is_array($tasks)) {
            return [];
        }

        $tasks = array_values(array_map(function ($task) {
            if (!is_array($task)) {
                return [];
            }
            $id = isset($task['id']) ? trim((string) $task['id']) : null;
            if ($id === '') {
                $id = null;
            }
            $name = isset($task['name']) ? trim($task['name']) : null;
            $ownerName = isset($task['owner_name']) ? trim($task['owner_name']) : null;
            $durationDays = isset($task['duration_days']) ? trim((string) $task['duration_days']) : null;
            if ($durationDays === '') {
                $durationDays = null;
            } elseif ($durationDays !== null) {
                $durationDays = (int) $durationDays;
            }
            return [
                'id' => $id,
                'name' => $name,
                'owner_name' => $ownerName,
                'owner_id' => $task['owner_id'] ?? null,
                'duration_days' => $durationDays,
            ];
        }, array_filter($tasks, function ($task) {
            return is_array($task) && (isset($task['name']) || isset($task['owner_name']) || isset($task['owner_id']) || isset($task['duration_days']));
        })));

        if (empty($tasks)) {
            return [];
        }

        $ownerIds = collect($tasks)
            ->pluck('owner_id')
            ->filter()
            ->unique()
            ->values();

        $ownerNames = [];
        if ($ownerIds->isNotEmpty()) {
            $ownerNames = User::query()
                ->whereIn('id', $ownerIds)
                ->pluck('name', 'id')
                ->toArray();
        }

        return array_map(function ($task) use ($ownerNames) {
            $ownerId = $task['owner_id'] ?? null;
            if ($ownerId !== null && $ownerId !== '') {
                $ownerId = (int) $ownerId;
            } else {
                $ownerId = null;
            }
            $task['owner_id'] = $ownerId;
            if (!empty($task['id'])) {
                $task['id'] = (int) $task['id'];
            } else {
                $task['id'] = null;
            }
            if ($ownerId) {
                $task['owner_name'] = $ownerNames[$ownerId] ?? $task['owner_name'] ?? null;
            }
            if (empty($task['owner_name'])) {
                $task['owner_name'] = null;
            }
            return $task;
        }, $tasks);
    }

    /**
     * Duplicate a workflow template along with its tasks and dependencies.
     */
    public function duplicate(Request $request, $workflowTemplateId): JsonResponse
    {
        $workflowTemplate = WorkflowTemplate::query()
            ->with(['tasks', 'taskDependencies'])
            ->findOrFail($workflowTemplateId);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ],
            [],
            [
                'name' => 'template name',
                'description' => 'template description',
            ]
        );

        $userId = auth()->id() ?? (int) session('user_id');
        $description = isset($validated['description'])
            ? trim((string) $validated['description'])
            : null;
        if ($description === '') {
            $description = null;
        }

        $copy = DB::transaction(function () use ($workflowTemplate, $validated, $userId, $description) {
            $newTemplate = WorkflowTemplate::create([
                'name' => $validated['name'],
                'description' => $description,
                'created_by' => $userId ?: null,
                'updated_by' => $userId ?: null,
            ]);

            $taskIdMap = [];
            foreach ($workflowTemplate->tasks as $task) {
                $newTask = $newTemplate->tasks()->create([
                    'name' => $task->name,
                    'owner_name' => $task->owner_name,
                    'owner_id' => $task->owner_id,
                    'position' => $task->position,
                    'duration_days' => $task->duration_days,
                ]);
                $taskIdMap[$task->id] = $newTask->id;
            }

            foreach ($workflowTemplate->taskDependencies as $dependency) {
                $predecessorId = $taskIdMap[$dependency->predecessor_task_id] ?? null;
                $successorId = $taskIdMap[$dependency->successor_task_id] ?? null;

                if (!$predecessorId || !$successorId) {
                    continue;
                }

                WorkflowTaskDependency::create([
                    'workflow_template_id' => $newTemplate->id,
                    'predecessor_task_id' => $predecessorId,
                    'successor_task_id' => $successorId,
                    'dependency_type_id' => $dependency->dependency_type_id,
                    'lag_days' => $dependency->lag_days,
                    'notes' => $dependency->notes,
                ]);
            }

            return $newTemplate->load(['tasks' => function ($query) {
                $query->orderBy('position');
            }]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Template duplicated successfully.',
            'data' => $copy,
        ]);
    }
}


