<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Customer;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use App\Models\TaskRemark;
use App\Models\TaskImage;
use App\Models\TaskReassignment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Traits\TenantAwareStorage;

class TaskController extends Controller
{
    use TenantAwareStorage;
    /**
     * Display the task management page (created by current user)
     */
    public function index()
    {
        return view('worklog.task');
    }

    /**
     * Fetch tasks created by current user
     */
    public function fetch()
    {
        // Get current user ID from Auth or session
        $userId = $this->getCurrentUserId();

        if (!$userId) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        try {
            $tasks = Task::with($this->includeAssignedUsers([
                    'user',
                    'customer',
                    'creator',
                    'status',
                    'priority',
                    'customerProject',
                    'workflowTask.successorDependencies.type',
                    'remarks.user',
                ]))
                ->where('created_by', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Load images separately to avoid errors if table doesn't exist
            if (Schema::hasTable('task_images')) {
                $tasks->load('images');
            } else {
                // Add empty images array if table doesn't exist
                $tasks->each(function($task) {
                    $task->setRelation('images', collect([]));
                });
            }

            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json($tasks->values());
        } catch (\Exception $e) {
            \Log::error('Error fetching tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display all tasks page
     */
    public function allTasks()
    {
        return view('worklog.all-tasks');
    }

    /**
     * Fetch all tasks with relationships
     */
    public function fetchAllTasks()
    {
        try {
            $tasks = Task::with($this->includeAssignedUsers([
                    'user',
                    'customer',
                    'creator',
                    'status',
                    'priority',
                    'customerProject',
                    'workflowTask.successorDependencies.type',
                    'remarks.user',
                ]))
                // Exclude tasks that are already marked as done
                ->where(function ($q) {
                    $q->whereNull('is_done')
                      ->orWhere('is_done', false)
                      ->orWhere('is_done', 0);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Load images separately to avoid errors if table doesn't exist
            if (Schema::hasTable('task_images')) {
                $tasks->load('images');
            } else {
                $tasks->each(function($task) {
                    $task->setRelation('images', collect([]));
                });
            }

            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json($tasks->values());
        } catch (\Exception $e) {
            \Log::error('Error fetching all tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display my tasks page
     */
    public function myTasks()
    {
        return view('worklog.my-tasks');
    }

    /**
     * Fetch tasks assigned to current user
     */
    public function fetchMyTasks()
    {
        // Get current user ID from Auth or session
        $userId = $this->getCurrentUserId();

        if (!$userId) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

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

            // Load images separately to avoid errors if table doesn't exist
            if (Schema::hasTable('task_images')) {
                $tasks->load('images');
            } else {
                $tasks->each(function($task) {
                    $task->setRelation('images', collect([]));
                });
            }

            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json($tasks->values());
        } catch (\Exception $e) {
            \Log::error('Error fetching my tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get users for dropdown
     */
    public function getUsers()
    {
        $users = User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }

    /**
     * Get customers for dropdown
     */
    public function getCustomers()
    {
        $customers = Customer::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($customers);
    }

    /**
     * Get task statuses for dropdown
     */
    public function getTaskStatuses()
    {
        $statuses = TaskStatus::orderBy('order')
            ->get();

        return response()->json($statuses);
    }

    /**
     * Get task priorities for dropdown
     */
    public function getTaskPriorities()
    {
        $priorities = TaskPriority::orderBy('order')
            ->get();

        return response()->json($priorities);
    }

    /**
     * Debug: Get all task statuses
     */
    public function debugStatuses()
    {
        $statuses = TaskStatus::all();
        return response()->json([
            'statuses' => $statuses,
            'count' => $statuses->count()
        ]);
    }

    /**
     * Fetch tasks for a specific customer project
     */
    public function fetchByCustomerProject($projectId)
    {
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
                ->where('customer_project_id', $projectId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Load images separately
            if (Schema::hasTable('task_images')) {
                $tasks->load('images');
            } else {
                $tasks->each(function($task) {
                    $task->setRelation('images', collect([]));
                });
            }

            $tasks = $this->filterCriticalPathVisibility($tasks);

            return response()->json($tasks->values());
        } catch (\Exception $e) {
            \Log::error('Error fetching project tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading tasks', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new task
     */
    public function store(Request $request)
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
            'customer_project_id' => 'nullable|exists:customer_projects,id',
            'workflow_task_id' => 'nullable|exists:workflow_tasks,id',
            // Recurrence
            'is_recurring' => 'nullable|boolean',
            'recurrence_type' => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_days_of_week' => 'nullable|array',
            'recurrence_days_of_week.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'recurrence_day_of_month' => 'nullable|integer|min:1|max:31',
            'recurrence_months' => 'nullable|array',
            'recurrence_months.*' => 'integer|min:1|max:12',
            'recurrence_end_date' => 'nullable|date|after_or_equal:today',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip|max:5120', // Max 5MB per file
            'estimated_efforts' => 'nullable|string|max:255'
        ]);

        // Get current user ID from Auth or session
        $createdBy = $this->getCurrentUserId();

        if (!$createdBy) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $assigneeIds = $this->normalizeAssigneeIds($request);
        if (empty($assigneeIds)) {
            return response()->json(['message' => 'Please select at least one assignee'], 422);
        }
        $primaryAssigneeId = $assigneeIds[0];

        $task = Task::create([
            'customer_id' => $request->customer_id,
            'user_id' => $primaryAssigneeId,
            'task_name' => $request->task_name,
            'task' => $request->task,
            'task_type' => $request->task_type ?? 'task', // Default to 'task'
            'is_recurring' => (bool)($request->is_recurring ?? false),
            'recurrence_type' => $request->is_recurring ? ($request->recurrence_type ?? null) : null,
            'recurrence_interval' => $request->is_recurring ? ($request->recurrence_interval ?? null) : null,
            'recurrence_days_of_week' => $request->is_recurring ? ($request->recurrence_days_of_week ?? null) : null,
            'recurrence_day_of_month' => $request->is_recurring ? ($request->recurrence_day_of_month ?? null) : null,
            'recurrence_months' => $request->is_recurring ? ($request->recurrence_months ?? null) : null,
            'recurrence_end_date' => $request->is_recurring ? ($request->recurrence_end_date ?? null) : null,
            'due_date' => $request->due_date ?: null,
            'created_by' => $createdBy,
            'task_status_id' => $request->task_status_id ?? 1, // Default to Pending
            'task_priority_id' => $request->task_priority_id,
            'customer_project_id' => $request->customer_project_id ?? null,
            'workflow_task_id' => $request->workflow_task_id ?? null,
            'estimated_efforts' => $request->estimated_efforts ?? null,
        ]);

        $this->syncTaskAssignees($task, $assigneeIds, $createdBy);

        // Handle image uploads
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
            'message' => 'Task created successfully',
            'task' => $task->load($this->includeAssignedUsers(['user', 'customer', 'creator', 'status', 'priority', 'images']))
        ]);
    }

    /**
     * Update a task
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'user_ids' => 'sometimes|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'task_name' => 'sometimes|string|max:255',
            'task' => 'sometimes|string',
            'task_type' => 'nullable|in:task,qc,cp',
            'task_status_id' => 'nullable|exists:task_statuses,id',
            'task_priority_id' => 'nullable|exists:task_priorities,id',
            'customer_project_id' => 'nullable|exists:customer_projects,id',
            'workflow_task_id' => 'nullable|exists:workflow_tasks,id',
            // Recurrence
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
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,csv,txt,zip|max:5120', // Max 5MB per file
            'estimated_efforts' => 'nullable|string|max:255'
        ]);

        $task = Task::findOrFail($id);
        $previousUserId = $task->user_id ? (int) $task->user_id : null;
        $assigneeIds = $this->normalizeAssigneeIds($request);
        if (empty($assigneeIds)) {
            return response()->json(['message' => 'Please select at least one assignee'], 422);
        }
        $primaryAssigneeId = $assigneeIds[0];
        $updateData = [];
        if($request->has('customer_id')) $updateData['customer_id'] = $request->customer_id;
        if($primaryAssigneeId) $updateData['user_id'] = $primaryAssigneeId;
        if($request->has('task_name')) $updateData['task_name'] = $request->task_name;
        if($request->has('task')) $updateData['task'] = $request->task;
        if($request->has('task_type')) $updateData['task_type'] = $request->task_type;
        
        // Recurrence logic needs care to not overwrite if not sent, or handle as group
        if($request->has('is_recurring')) {
             $updateData['is_recurring'] = (bool)$request->is_recurring;
             if($updateData['is_recurring']) {
                 if($request->has('recurrence_type')) $updateData['recurrence_type'] = $request->recurrence_type;
                 if($request->has('recurrence_interval')) $updateData['recurrence_interval'] = $request->recurrence_interval;
                 if($request->has('recurrence_days_of_week')) $updateData['recurrence_days_of_week'] = $request->recurrence_days_of_week;
                 if($request->has('recurrence_day_of_month')) $updateData['recurrence_day_of_month'] = $request->recurrence_day_of_month;
                 if($request->has('recurrence_months')) $updateData['recurrence_months'] = $request->recurrence_months;
                 if($request->has('recurrence_end_date')) $updateData['recurrence_end_date'] = $request->recurrence_end_date;
             } else {
                 // If disabling recurrence, clear fields? OR just set is_recurring false. 
                 // Keeping it simple: just update what's sent.
             }
        }

        if($request->has('due_date')) $updateData['due_date'] = $request->due_date ?: null;
        if($request->has('task_status_id')) $updateData['task_status_id'] = $request->task_status_id;
        if($request->has('task_priority_id')) $updateData['task_priority_id'] = $request->task_priority_id;
        if($request->has('customer_project_id')) $updateData['customer_project_id'] = $request->customer_project_id;
        if($request->has('workflow_task_id')) $updateData['workflow_task_id'] = $request->workflow_task_id;
        if($request->has('started_at')) $updateData['started_at'] = $request->started_at;
        if($request->has('completed_at')) $updateData['completed_at'] = $request->completed_at;
        if($request->has('estimated_efforts')) $updateData['estimated_efforts'] = $request->estimated_efforts ?: null;

        $task->update($updateData);

        $this->syncTaskAssignees($task, $assigneeIds, $this->getCurrentUserId());

        if ($previousUserId !== $primaryAssigneeId) {
            TaskReassignment::create([
                'task_id' => $task->id,
                'previous_user_id' => $previousUserId,
                'new_user_id' => $primaryAssigneeId,
                'reassigned_by' => $this->getCurrentUserId(),
            ]);
        }

        // Handle new image uploads
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
            'message' => 'Task updated successfully',
            'task' => $task->load($this->includeAssignedUsers(['user', 'customer', 'creator', 'status', 'priority', 'images']))
        ]);
    }

    /**
     * Mark task as done/undone
     */
    public function toggleDone($id)
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
            'message' => $task->is_done ? 'Task marked as done' : 'Task marked as pending',
            'task' => $task->load(['user', 'customer', 'creator', 'status', 'priority'])
        ]);
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:pending,in progress,completed,cancelled'
            ]);

            $task = Task::findOrFail($id);
            
            // Get the actual status ID from the database
            $status = TaskStatus::where('name', ucfirst($request->status))->first();
            
            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status not found: ' . $request->status
                ], 404);
            }
            
            $oldStatus = $task->status ? $task->status->name : 'Unknown';
            $task->task_status_id = $status->id;
            if (!$task->started_at) {
                $task->started_at = now();
            }

            $statusName = Str::lower($status->name ?? '');
            if (in_array($statusName, ['done', 'completed', 'complete', 'finished', 'closed'], true)) {
                $task->completed_at = now();
                if (!$task->started_at) {
                    $task->started_at = now();
                }
            }

            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task status updated from ' . $oldStatus . ' to ' . $status->name,
                'task' => $task->load(['user', 'customer', 'creator', 'status', 'priority'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update task status by status_id (for kanban drag-and-drop)
     */
    public function updateStatusById(Request $request, $id)
    {
        try {
            $request->validate([
                'task_status_id' => 'required|exists:task_statuses,id'
            ]);

            $task = Task::findOrFail($id);
            $oldStatusId = $task->task_status_id;
            $task->task_status_id = $request->task_status_id;
            if (!$task->started_at) {
                $task->started_at = now();
            }
            $statusName = Str::lower(optional($task->status)->name ?? '');
            if (in_array($statusName, ['done', 'completed', 'complete', 'finished', 'closed'], true)) {
                $task->completed_at = now();
            }
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => $task->load(['user', 'customer', 'creator', 'status', 'priority'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a task
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Serve task image
     */
    public function serveImage($taskId, $imageId)
    {
        $task = Task::findOrFail($taskId);
        $image = TaskImage::where('id', $imageId)
            ->where('task_id', $taskId)
            ->firstOrFail();

        $path = Storage::disk('public')->path($image->image_path);
        
        if (!file_exists($path)) {
            abort(404, 'Image not found');
        }

        return response()->file($path);
    }

    /**
     * Delete a task image
     */
    public function deleteImage($taskId, $imageId)
    {
        $task = Task::findOrFail($taskId);
        $image = TaskImage::where('id', $imageId)
            ->where('task_id', $taskId)
            ->firstOrFail();

        // Delete file from storage
        if ($image->image_path) {
            $this->deleteTenantFile($image->image_path);
        }

        // Delete record from database
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    /**
     * Send a poke email to the assigned user for a task
     */
    public function poke($id)
    {
        try {
            $task = Task::with($this->includeAssignedUsers(['user.employee', 'assignedUsers.employee', 'customer', 'creator', 'status', 'priority', 'images']))->findOrFail($id);

            $recipients = $task->assignedUsers
                ? $task->assignedUsers->filter(fn ($user) => !empty($user->email) && ($user->employee && $user->employee->status === 'active'))
                : collect();

            if ($recipients->isEmpty() && $task->user && !empty($task->user->email) && ($task->user->employee && $task->user->employee->status === 'active')) {
                $recipients = collect([$task->user]);
            }

            if ($recipients->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Assigned user email not found'], 422);
            }

            $subject = 'Poke: Task Reminder - ' . ($task->task_name ?: 'Untitled Task');
            foreach ($recipients as $recipient) {
                $this->sendPokeMailToRecipient($task, $recipient, $subject);
            }

            return response()->json(['success' => true, 'message' => 'Poke email sent']);
        } catch (\Exception $e) {
            \Log::error('Poke email failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send poke email'], 500);
        }
    }

    /**
     * Filter out critical-path tasks whose predecessors are not yet complete.
     */
    protected function filterCriticalPathVisibility($tasks)
    {
        $collection = $tasks instanceof Collection ? $tasks : collect($tasks);

        $cpGroups = $collection->filter(function ($task) {
            return ($task->task_type === 'cp')
                && $task->customer_project_id
                && $task->workflow_task_id
                && $task->workflowTask;
        })->groupBy('customer_project_id');

        if ($cpGroups->isEmpty()) {
            return $collection;
        }

        $hiddenIds = [];

        foreach ($cpGroups as $group) {
            $cpByWorkflow = $group->keyBy('workflow_task_id');

            foreach ($group as $task) {
                $dependencies = optional($task->workflowTask)->successorDependencies ?? collect();
                if ($dependencies->isEmpty()) {
                    continue;
                }

                $blocked = false;
                foreach ($dependencies as $dependency) {
                    $predecessorTask = $cpByWorkflow->get($dependency->predecessor_task_id);
                    if (!$predecessorTask) {
                        continue;
                    }

                    if (!$this->dependencyIsSatisfied($dependency, $predecessorTask)) {
                        $blocked = true;
                        break;
                    }
                }

                if ($blocked) {
                    $hiddenIds[] = $task->id;
                }
            }
        }

        if (empty($hiddenIds)) {
            return $collection;
        }

        return $collection->reject(function ($task) use ($hiddenIds) {
            return in_array($task->id, $hiddenIds, true);
        });
    }

    protected function dependencyIsSatisfied($dependency, $predecessorTask): bool
    {
        $typeCode = strtoupper(optional($dependency->type)->code ?? 'FS');
        $lag = (int) ($dependency->lag_days ?? 0);
        $now = now();

        switch ($typeCode) {
            case 'SS':
                if (!$this->taskHasStarted($predecessorTask)) {
                    return false;
                }
                if ($lag > 0 && $predecessorTask->started_at) {
                    return now()->greaterThanOrEqualTo(optional($predecessorTask->started_at)->copy()->addDays($lag));
                }
                return true;
            case 'SF':
                if (!$this->taskHasStarted($predecessorTask)) {
                    return false;
                }
                if ($lag > 0 && $predecessorTask->started_at) {
                    return now()->greaterThanOrEqualTo(optional($predecessorTask->started_at)->copy()->addDays($lag));
                }
                return true;
            case 'FF':
                if (!$this->taskIsComplete($predecessorTask)) {
                    return false;
                }
                if ($lag > 0 && $predecessorTask->completed_at) {
                    return now()->greaterThanOrEqualTo(optional($predecessorTask->completed_at)->copy()->addDays($lag));
                }
                return true;
            case 'FS':
            default:
                if (!$this->taskIsComplete($predecessorTask)) {
                    return false;
                }
                if ($lag > 0 && $predecessorTask->completed_at) {
                    return now()->greaterThanOrEqualTo(optional($predecessorTask->completed_at)->copy()->addDays($lag));
                }
                return true;
        }
    }

    protected function taskIsComplete($task): bool
    {
        if ($task->is_done) {
            return true;
        }

        $statusName = Str::lower(optional($task->status)->name ?? '');

        return in_array($statusName, [
            'done',
            'completed',
            'complete',
            'finished',
            'closed',
        ], true);
    }

    protected function taskHasStarted($task): bool
    {
        if ($this->taskIsComplete($task)) {
            return true;
        }

        $statusName = Str::lower(optional($task->status)->name ?? '');

        if ($statusName === '') {
            return false;
        }

        if ($task->started_at) {
            return true;
        }

        if (!in_array($statusName, [
            'pending',
            'waiting',
            'not started',
            'planning',
            'planned',
            'todo',
            'to do',
        ], true)) {
            return true;
        }

        return false;
    }

    protected function normalizeAssigneeIds(Request $request): array
    {
        $ids = $request->input('user_ids', []);

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

    protected function sendPokeMailToRecipient(Task $task, $recipient, string $subject): void
    {
        if (!$recipient || empty($recipient->email)) {
            return;
        }

        $customerName = $task->customer ? $task->customer->name : 'N/A';
        $creatorName = $task->creator ? $task->creator->name : 'Someone';
        $statusName = $task->status ? $task->status->name : 'Pending';
        $priorityName = $task->priority ? $task->priority->name : 'None';

        $safe = fn ($v) => e((string) $v);

        $imagesHtml = '';
        if ($task->relationLoaded('images') && $task->images && $task->images->count() > 0) {
            $thumbs = '';
            foreach ($task->images as $img) {
                if (!empty($img->image_path)) {
                    $url = Storage::disk('public')->url($img->image_path);
                    $alt = $safe($img->original_name ?? 'image');
                    $thumbs .= "<a href='".$safe($url)."' target='_blank' style='display:inline-block;margin:4px;text-decoration:none;'>"
                             . "<img src='".$safe($url)."' alt='".$alt."' style='width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;'>"
                             . "</a>";
                }
            }
            if ($thumbs !== '') {
                $imagesHtml = "<tr>"
                    . "<th style='text-align:left;padding:10px;vertical-align:top;'>Images</th>"
                    . "<td style='padding:10px;'>".$thumbs."</td>"
                    . "</tr>";
            }
        }

        $assignedRow = "<tr>"
            . "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Assigned To</th>"
            . "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($recipient->name ?? 'N/A')." (".$safe($recipient->email).")</td>"
            . "</tr>";

        $html =
            "<table width='100%' cellspacing='0' cellpadding='0' style='background:#f6f9fc;padding:24px 0;font-family:Arial,Helvetica,sans-serif;'>".
            "<tr><td align='center'>".
            "<table width='94%' cellpadding='0' cellspacing='0' style='max-width:720px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.08);'>".
            "<tr><td style='background:linear-gradient(135deg,#0d6efd,#1e90ff);color:blue;padding:16px 20px;'>".
            "<div style='font-size:18px;font-weight:700;'>🔔 Task Poke</div>".
            "<div style='font-size:12px;opacity:.9;margin-top:2px;'>Please review and update the task</div>".
            "</td></tr>".
            "<tr><td style='padding:16px 20px;background:#ffffff;'>".
            "<table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse;font-size:13px;border:1px solid #eef2f7;border-radius:10px;overflow:hidden;'>".
            "<tbody>".
            "<tr style='background:#f8fafc;'>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;width:160px;'>Task</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($task->task_name ?: 'Untitled Task')."</td>".
            "</tr>".
            "<tr>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Type</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe(strtoupper($task->task_type ?? 'task'))."</td>".
            "</tr>".
            "<tr style='background:#f8fafc;'>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Customer</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($customerName)."</td>".
            "</tr>".
            $assignedRow.
            "<tr style='background:#f8fafc;'>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Status</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($statusName)."</td>".
            "</tr>".
            "<tr>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Priority</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($priorityName)."</td>".
            "</tr>".
            "<tr style='background:#f8fafc;'>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Created By</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($creatorName)."</td>".
            "</tr>".
            "<tr>".
            "<th style='text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;'>Created At</th>".
            "<td style='padding:10px;border-bottom:1px solid #e2e8f0;'>".$safe($task->created_at ? $task->created_at->format('d M Y, g:i A') : 'N/A')."</td>".
            "</tr>".
            "<tr style='background:#f8fafc;'>".
            "<th style='text-align:left;padding:10px;vertical-align:top;'>Description</th>".
            "<td style='padding:10px;white-space:pre-wrap;word-break:break-word;'>".$safe($task->task ?: '-')."</td>".
            "</tr>".
            $imagesHtml.
            "</tbody></table>".
            "<div style='margin-top:12px;font-size:12px;color:#64748b;'>This is an automated reminder. Please update the task status if needed.</div>".
            "</td></tr>".
            "<tr><td style='padding:12px 20px;background:#f8fafc;color:#64748b;font-size:12px;border-top:1px solid #eef2f7;text-align:center;'>© ".date('Y')." Workorio</td></tr>".
            "</table>".
            "</td></tr>".
            "</table>";

        Mail::html($html, function ($message) use ($recipient, $subject) {
            $message->to($recipient->email)
                    ->subject($subject);
        });
    }

    /**
     * Save a remark for a task
     */
    public function saveRemark(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'remark' => 'required|string|max:1000'
        ]);

        $userId = $this->getCurrentUserId();

        if (!$userId) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $remark = TaskRemark::create([
            'task_id' => $request->task_id,
            'user_id' => $userId,
            'remark' => $request->remark
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Remark added successfully',
            'remark' => $remark->load('user')
        ]);
    }

    /**
     * Export tasks to Excel (CSV format)
     */
    public function exportTasks(Request $request)
    {
        $type = $request->query('type', 'all');
        $userId = $this->getCurrentUserId();

        $query = Task::with($this->includeAssignedUsers([
            'user',
            'customer',
            'creator',
            'status',
            'priority',
            'customerProject'
        ]));

        if ($request->filled('ids')) {
            $ids = is_array($request->input('ids')) ? $request->input('ids') : explode(',', $request->input('ids'));
            $query->whereIn('id', $ids);
        } else {
            if ($type === 'created') {
                $query->where('created_by', $userId);
            } elseif ($type === 'assigned') {
                $query->where(function($q) use ($userId) {
                    $q->whereHas('assignedUsers', function ($sq) use ($userId) {
                        $sq->where('users.id', $userId);
                    })->orWhere('user_id', $userId);
                });
            }
            
            // Apply additional filters if provided
            if ($request->filled('user_id')) {
                $fUserId = $request->input('user_id');
                $query->where(function($q) use ($fUserId) {
                    $q->whereHas('assignedUsers', function ($sq) use ($fUserId) {
                        $sq->where('users.id', $fUserId);
                    })->orWhere('user_id', $fUserId);
                });
            }

            if ($request->filled('status')) {
                $status = $request->input('status');
                if ($status === 'done') {
                    $query->where(function ($q) {
                        $q->where('is_done', true)
                          ->orWhereHas('status', function ($sq) {
                              $sq->whereIn('name', ['Done', 'Completed', 'Finish', 'Closed']);
                          });
                    });
                } else {
                    $query->where('task_status_id', $status);
                }
            }

            if ($request->filled('priority')) {
                $query->where('task_priority_id', $request->input('priority'));
            }

            if ($request->filled('task_type')) {
                $query->where('task_type', $request->input('task_type'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('task_name', 'like', "%{$search}%")
                      ->orWhere('task', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        $filename = "tasks_" . $type . "_" . date('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Task Name', 'Description', 'Customer', 'Project', 'Assignee', 'Creator', 'Status', 'Priority', 'Due Date', 'Completed At', 'Created At'];

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $assignees = collect();
                if ($task->relationLoaded('assignedUsers')) {
                    $assignees = $task->assignedUsers->pluck('name');
                }
                
                if ($assignees->isEmpty() && $task->user) {
                    $assignees = collect([$task->user->name]);
                }
                
                $assigneeStr = $assignees->implode(', ');

                fputcsv($file, [
                    $task->task_name,
                    $task->task,
                    $task->customer ? $task->customer->name : 'N/A',
                    $task->customerProject ? $task->customerProject->project_name : 'N/A',
                    $assigneeStr ?: 'N/A',
                    $task->creator ? $task->creator->name : 'N/A',
                    $task->status ? $task->status->name : 'Pending',
                    $task->priority ? $task->priority->name : 'None',
                    $task->due_date ? date('d-m-Y', strtotime($task->due_date)) : 'N/A',
                    $task->completed_at ? date('d-m-Y', strtotime($task->completed_at)) : 'N/A',
                    $task->created_at ? $task->created_at->format('d-m-Y') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        // Check Laravel Auth first (for super admin)
        if (Auth::check()) {
            return Auth::id();
        }

        // Check session auth (for tenant users)
        if (session()->has('user_id')) {
            return session('user_id');
        }

        return null;
    }

    // --- Task Helper API Methods for Dropdowns ---

    public function users()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        return response()->json($users);
    }

    public function statuses()
    {
        $statuses = TaskStatus::select('id', 'name', 'color')->orderBy('id')->get();
        return response()->json($statuses);
    }

    public function priorities()
    {
        $priorities = TaskPriority::select('id', 'name', 'color')->orderBy('id')->get();
        return response()->json($priorities);
    }

    public function fetchByProject($projectId)
    {
        $tasks = Task::with(
                $this->includeAssignedUsers(['user', 'customer', 'creator', 'status', 'priority', 'images'])
            )
            ->where('customer_project_id', $projectId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($tasks);
    }
}
