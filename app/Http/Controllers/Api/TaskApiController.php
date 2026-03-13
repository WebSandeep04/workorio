<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Customer;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use App\Models\TaskRemark;
use App\Models\TaskImage;
use App\Models\TaskReassignment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\TenantAwareStorage;

class TaskApiController extends Controller
{
    use TenantAwareStorage;
    /**
     * Get Form Data (Customers, Users, Statutes, Priorities)
     * optimized for mobile app to reduce network calls
     */
    public function getFormData(): JsonResponse
    {
        $customers = Customer::select('id', 'name')->orderBy('name')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();
        $statuses = TaskStatus::orderBy('order')->get();
        $priorities = TaskPriority::orderBy('order')->get();

        return response()->json([
            'success' => true,
            'customers' => $customers,
            'users' => $users,
            'statuses' => $statuses,
            'priorities' => $priorities
        ]);
    }

    /**
     * Fetch tasks created by current user
     */
    public function createdTasks(): JsonResponse
    {
        $userId = auth()->id();

        try {
            $tasks = Task::with($this->includeAssignedUsers([
                    'user',
                    'customer',
                    'creator',
                    'status',
                    'priority',
                    'customerProject',
                    'workflowTask.successorDependencies.type',
                ]))
                ->where('created_by', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            $this->loadImagesIfTableExists($tasks);
            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json(['success' => true, 'tasks' => $tasks->values()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch tasks assigned to current user
     */
    public function myTasks(): JsonResponse
    {
        $userId = auth()->id();

        try {
            $tasks = Task::with($this->includeAssignedUsers([
                    'user',
                    'customer',
                    'creator',
                    'status',
                    'priority',
                    'remarks.user',
                    'customerProject',
                    'workflowTask.successorDependencies.type',
                ]))
                ->whereHas('assignedUsers', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $this->loadImagesIfTableExists($tasks);
            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json(['success' => true, 'tasks' => $tasks->values()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show task details
     */
    public function show($id): JsonResponse
    {
        $task = Task::with($this->includeAssignedUsers([
                'user', 'customer', 'creator', 'status', 'priority', 'remarks.user', 'images'
            ]))->find($id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }

        return response()->json(['success' => true, 'task' => $task]);
    }

    /**
     * Store new task
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'task_name' => 'required|string|max:255',
            'task' => 'required|string',
            'task_type' => 'nullable|in:task,qc,cp',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'is_recurring' => 'nullable|boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_days_of_week' => 'nullable|array',
            'recurrence_days_of_week.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'recurrence_day_of_month' => 'nullable|integer|min:1|max:31',
            'recurrence_months' => 'nullable|array',
            'recurrence_months.*' => 'integer|min:1|max:12',
            'recurrence_end_date' => 'nullable|date|after_or_equal:today',
            'due_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $createdBy = auth()->id();
        $assigneeIds = $this->normalizeAssigneeIds($request);
        $primaryAssigneeId = $assigneeIds[0] ?? null;

        if (!$primaryAssigneeId) {
             return response()->json(['success' => false, 'message' => 'No valid assignees selected'], 422);
        }

        DB::beginTransaction();
        try {
            $task = Task::create([
                'customer_id' => $request->customer_id,
                'user_id' => $primaryAssigneeId,
                'task_name' => $request->task_name,
                'task' => $request->task,
                'task_type' => $request->task_type ?? 'task',
                'is_recurring' => (bool)($request->is_recurring ?? false),
                'recurrence_type' => $request->is_recurring ? ($request->recurrence_type ?? null) : null,
                'recurrence_interval' => $request->is_recurring ? ($request->recurrence_interval ?? null) : null,
                'recurrence_days_of_week' => $request->is_recurring ? ($request->recurrence_days_of_week ?? null) : null,
                'recurrence_day_of_month' => $request->is_recurring ? ($request->recurrence_day_of_month ?? null) : null,
                'recurrence_months' => $request->is_recurring ? ($request->recurrence_months ?? null) : null,
                'recurrence_end_date' => $request->is_recurring ? ($request->recurrence_end_date ?? null) : null,
                'due_date' => $request->due_date ?: null,
                'created_by' => $createdBy,
                'task_status_id' => $request->task_status_id ?? 1,
                'task_priority_id' => $request->task_priority_id,
            ]);

            $this->syncTaskAssignees($task, $assigneeIds, $createdBy);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    // Use tenant-aware storage with isolation
                    $path = $this->storeTenantFile($image, 'task_images');
                    TaskImage::create([
                        'task_id' => $task->id,
                        'image_path' => $path,
                        'original_name' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize()
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Task created successfully',
                'task' => $task->load($this->includeAssignedUsers(['user', 'customer', 'status', 'priority', 'images']))
            ]);

        } catch (\Exception $e) {
            DB::rollback();
             return response()->json(['success' => false, 'message' => 'Failed to create task: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update task
     */
    public function update(Request $request, $id): JsonResponse
    {
         $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'user_ids' => 'required|array|min:1',
            'task_name' => 'required|string|max:255',
            'task' => 'required|string',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::findOrFail($id);
        
        // Authorization: Only creator or assignee can update? usually standard logic applies.
        // For now, allowing authenticated users (or you might want to add policy checks)
        
        $assigneeIds = $this->normalizeAssigneeIds($request);
        $primaryAssigneeId = $assigneeIds[0];
        $currentUserId = auth()->id();

        $previousUserId = $task->user_id ? (int) $task->user_id : null;

        $task->update([
            'customer_id' => $request->customer_id,
            'user_id' => $primaryAssigneeId,
            'task_name' => $request->task_name,
            'task' => $request->task,
            'task_type' => $request->task_type ?? $task->task_type,
            'is_recurring' => $request->has('is_recurring') ? (bool)$request->is_recurring : $task->is_recurring,
            // (Assuming full recurrence update logic is same as store if present)
            'due_date' => $request->due_date ?: null,
            'task_status_id' => $request->task_status_id,
            'task_priority_id' => $request->task_priority_id,
        ]);

        $this->syncTaskAssignees($task, $assigneeIds, $currentUserId);

        if ($previousUserId !== $primaryAssigneeId) {
            TaskReassignment::create([
                'task_id' => $task->id,
                'previous_user_id' => $previousUserId,
                'new_user_id' => $primaryAssigneeId,
                'reassigned_by' => $currentUserId,
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Use tenant-aware storage with isolation
                $path = $this->storeTenantFile($image, 'task_images');
                TaskImage::create([
                    'task_id' => $task->id,
                    'image_path' => $path,
                    'original_name' => $image->getClientOriginalName(),
                    'file_size' => $image->getSize()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task->load($this->includeAssignedUsers(['user', 'customer', 'status', 'priority', 'images']))
        ]);
    }

    /**
     * Mark task as done/undone
     */
    public function toggleDone($id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $task->is_done = !$task->is_done;
        
        if ($task->is_done) {
            $task->completed_at = now();
            if (!$task->started_at) {
                $task->started_at = now();
            }
        } else {
            $task->completed_at = null;
        }
        $task->save();

        return response()->json([
            'success' => true,
            'message' => $task->is_done ? 'Task marked as done' : 'Task marked as pending',
            'is_done' => $task->is_done,
            'task' => $task
        ]);
    }

    /**
     * Update task status (Kanban or Dropdown)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
         $request->validate([
             // Accept either status name or ID
             'status' => 'nullable|string', 
             'task_status_id' => 'nullable|exists:task_statuses,id'
         ]);

         $task = Task::findOrFail($id);
         
         if ($request->filled('task_status_id')) {
             $status = TaskStatus::find($request->task_status_id);
         } elseif ($request->filled('status')) {
             $status = TaskStatus::where('name', ucfirst($request->status))->first();
         } else {
             return response()->json(['success' => false, 'message' => 'Status ID or Name required'], 422);
         }

         if (!$status) {
             return response()->json(['success' => false, 'message' => 'Status not found'], 404);
         }

         $task->task_status_id = $status->id;
         
         // Auto-start / Auto-complete logic
         if (!$task->started_at) {
             $task->started_at = now();
         }
         
         $statusName = Str::lower($status->name);
         $completionStatuses = ['done', 'completed', 'complete', 'finished', 'closed'];
         
         if (in_array($statusName, $completionStatuses, true)) {
             $task->completed_at = now();
         }

         $task->save();
         
         return response()->json([
             'success' => true,
             'message' => 'Status updated to ' . $status->name
         ]);
    }

    /**
     * Delete Task
     */
    public function destroy($id): JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found'], 404);
        }
        $task->delete();
        return response()->json(['success' => true, 'message' => 'Task deleted']);
    }

    /**
     * Add Remark
     */
    public function addRemark(Request $request, $id): JsonResponse
    {
        $request->validate(['remark' => 'required|string']);
        
        $remark = TaskRemark::create([
            'task_id' => $id,
            'user_id' => auth()->id(),
            'remark' => $request->remark
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Remark added',
            'remark' => $remark->load('user')
        ]);
    }

    /**
     * Delete Image
     */
    public function deleteImage($id, $imageId): JsonResponse
    {
        $image = TaskImage::where('id', $imageId)->where('task_id', $id)->first();
        if (!$image) {
             return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        if ($image->image_path) {
            $this->deleteTenantFile($image->image_path);
        }
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted']);
    }
    
    // ============================================
    // PRIVATE HELPERS (Ported from TaskController)
    // ============================================
    
    protected function normalizeAssigneeIds(Request $request): array
    {
        $ids = $request->input('user_ids', []);
        
        // Handle comma-separated string if sent from multipart form
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }
        
        // Handle single user_id legacy
        if (empty($ids) && $request->filled('user_id')) {
            $ids = [$request->input('user_id')];
        }

        return collect($ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function syncTaskAssignees(Task $task, array $userIds, ?int $assignedBy = null): void
    {
        if (!Schema::hasTable('task_assignees')) {
            return;
        }

        $payload = collect($userIds)
            ->mapWithKeys(fn ($id) => [$id => ['assigned_by' => $assignedBy]])
            ->toArray();

        $task->assignedUsers()->sync($payload);
    }

    protected function includeAssignedUsers(array $relations = []): array
    {
        if (Schema::hasTable('task_assignees')) {
            $relations[] = 'assignedUsers';
        }
        return $relations;
    }
    
    protected function loadImagesIfTableExists($tasks)
    {
        if (Schema::hasTable('task_images')) {
            $tasks->load('images');
        } else {
            $tasks->each(fn($t) => $t->setRelation('images', collect([])));
        }
    }

    protected function filterCriticalPathVisibility($tasks)
    {
        $collection = $tasks instanceof Collection ? $tasks : collect($tasks);

        $cpGroups = $collection->filter(function ($task) {
            return ($task->task_type === 'cp') && $task->customer_project_id && $task->workflow_task_id && $task->workflowTask;
        })->groupBy('customer_project_id');

        if ($cpGroups->isEmpty()) return $collection;

        $hiddenIds = [];

        foreach ($cpGroups as $group) {
            $cpByWorkflow = $group->keyBy('workflow_task_id');
            foreach ($group as $task) {
                $dependencies = optional($task->workflowTask)->successorDependencies ?? collect();
                if ($dependencies->isEmpty()) continue;

                $blocked = false;
                foreach ($dependencies as $dependency) {
                    $predecessorTask = $cpByWorkflow->get($dependency->predecessor_task_id);
                    if (!$predecessorTask) continue;
                    if (!$this->dependencyIsSatisfied($dependency, $predecessorTask)) {
                        $blocked = true;
                        break;
                    }
                }
                if ($blocked) $hiddenIds[] = $task->id;
            }
        }
        
        if (empty($hiddenIds)) return $collection;
        
        return $collection->reject(fn ($task) => in_array($task->id, $hiddenIds, true));
    }

    protected function dependencyIsSatisfied($dependency, $predecessorTask): bool
    {
        $typeCode = strtoupper(optional($dependency->type)->code ?? 'FS');
        $lag = (int) ($dependency->lag_days ?? 0);
        $now = now();

        switch ($typeCode) {
            case 'SS':
                if (!$this->taskHasStarted($predecessorTask)) return false;
                if ($lag > 0 && $predecessorTask->started_at) {
                    return $now->greaterThanOrEqualTo(optional($predecessorTask->started_at)->copy()->addDays($lag));
                }
                return true;
            case 'SF':
                if (!$this->taskHasStarted($predecessorTask)) return false;
                if ($lag > 0 && $predecessorTask->started_at) {
                    return $now->greaterThanOrEqualTo(optional($predecessorTask->started_at)->copy()->addDays($lag));
                }
                return true;
            case 'FF':
                if (!$this->taskIsComplete($predecessorTask)) return false;
                if ($lag > 0 && $predecessorTask->completed_at) {
                    return $now->greaterThanOrEqualTo(optional($predecessorTask->completed_at)->copy()->addDays($lag));
                }
                return true;
            case 'FS':
            default:
                if (!$this->taskIsComplete($predecessorTask)) return false;
                if ($lag > 0 && $predecessorTask->completed_at) {
                    return $now->greaterThanOrEqualTo(optional($predecessorTask->completed_at)->copy()->addDays($lag));
                }
                return true;
        }
    }

    protected function taskIsComplete($task): bool
    {
        if ($task->is_done) return true;
        $statusName = Str::lower(optional($task->status)->name ?? '');
        return in_array($statusName, ['done', 'completed', 'complete', 'finished', 'closed'], true);
    }

    protected function taskHasStarted($task): bool
    {
        if ($this->taskIsComplete($task)) return true;
        $statusName = Str::lower(optional($task->status)->name ?? '');
        if ($statusName === '') return false;
        if ($task->started_at) return true;
        return !in_array($statusName, ['pending', 'waiting', 'not started', 'planning', 'planned', 'todo', 'to do'], true);
    }
}
