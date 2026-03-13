<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Module;
use App\Models\CustomerProject;
use App\Models\CustomerProjectModule;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowTask;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\TenantAwareStorage;

class CustomerProjectController extends Controller
{
    use TenantAwareStorage;
    public function index()
    {
        return view('customer-project.index');
    }

    public function fetchCustomerProjects(Request $request)
    {
        $query = CustomerProject::with(['customer', 'service', 'customerProjectModules.module', 'assignedUsers', 'workflowTemplate'])
        ->where('status', '=', 'Ongoing');
        
        // Filter by customer_id if provided
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $customerProjects = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($customerProjects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'project_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Ongoing,Completed,Closed',
            'description' => 'nullable|string',
            'original_value' => 'nullable|numeric',
            'estimated_value' => 'nullable|numeric',
            'profit_value' => 'nullable|numeric',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*.id' => 'required|exists:users,id',
            'assigned_user_ids.*.days' => 'nullable|numeric|min:0',
            'module_ids' => 'required|array|min:1',
            'module_ids.*' => 'exists:modules,id',
            'critical_path_enabled' => 'nullable|boolean',
            'workflow_template_id' => 'nullable|exists:workflow_templates,id',
            'sow_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->boolean('critical_path_enabled') && !$request->workflow_template_id) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a workflow template when enabling the critical path.',
                'errors' => [
                    'workflow_template_id' => ['The workflow template field is required when critical path is enabled.'],
                ],
            ], 422);
        }

        DB::beginTransaction();
        try {
            $sowPath = null;
            if ($request->hasFile('sow_document')) {
                // Use tenant-aware storage with isolation
                $sowPath = $this->storeTenantFile($request->file('sow_document'), 'customer-projects/sow');
            }

            $customerProject = CustomerProject::create([
                'customer_id' => $validated['customer_id'],
                'service_id' => $validated['service_id'],
                'project_name' => $validated['project_name'],
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'original_value' => $validated['original_value'] ?? null,
                'estimated_value' => $validated['estimated_value'] ?? null,
                'profit_value' => $validated['profit_value'] ?? null,
                'critical_path_enabled' => $request->boolean('critical_path_enabled'),
                'workflow_template_id' => $validated['workflow_template_id'] ?? null,
                'sow_path' => $sowPath,
            ]);
            // Save assigned users
            $assignedUsers = $validated['assigned_user_ids'] ?? [];
            if (!empty($assignedUsers)) {
                foreach ($assignedUsers as $au) {
                    DB::table('customer_project_users')->insert([
                        'customer_project_id' => $customerProject->id,
                        'user_id' => $au['id'],
                        'days_allocated' => $au['days'] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()]);
                }
            }

            // Create customer project modules
            foreach ($validated['module_ids'] as $moduleId) {
                CustomerProjectModule::create([
                    'customer_project_id' => $customerProject->id,
                    'module_id' => $moduleId,
                    'status' => 'pending']);
            }

            DB::commit();

            $customerProject->refresh();
            $this->syncCriticalPathTasks($customerProject);
            $customerProject->load(['customer', 'service', 'customerProjectModules.module', 'workflowTemplate']);

            return response()->json(['success' => true, 'customerProject' => $customerProject]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating customer project: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Error assigning project to customer: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'project_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:Ongoing,Completed,Closed',
            'description' => 'nullable|string',
            'original_value' => 'nullable|numeric',
            'estimated_value' => 'nullable|numeric',
            'profit_value' => 'nullable|numeric',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*.id' => 'required|exists:users,id',
            'assigned_user_ids.*.days' => 'nullable|numeric|min:0',
            'critical_path_enabled' => 'nullable|boolean',
            'workflow_template_id' => 'nullable|exists:workflow_templates,id',
            'sow_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->boolean('critical_path_enabled') && !$request->workflow_template_id) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a workflow template when enabling the critical path.',
                'errors' => [
                    'workflow_template_id' => ['The workflow template field is required when critical path is enabled.'],
                ],
            ], 422);
        }

        $customerProject = CustomerProject::where('id', $id)
            ->firstOrFail();

        $customerProject->update([
            'customer_id' => $validated['customer_id'],
            'service_id' => $validated['service_id'],
            'project_name' => $validated['project_name'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'original_value' => $validated['original_value'] ?? null,
            'estimated_value' => $validated['estimated_value'] ?? null,
            'profit_value' => $validated['profit_value'] ?? null,
            'critical_path_enabled' => $request->boolean('critical_path_enabled'),
            'workflow_template_id' => $validated['workflow_template_id'] ?? null,
        ]);

        if ($request->hasFile('sow_document')) {
            if ($customerProject->sow_path) {
                $this->deleteTenantFile($customerProject->sow_path);
            }
            // Use tenant-aware storage with isolation
            $sowPath = $this->storeTenantFile($request->file('sow_document'), 'customer-projects/sow');
            $customerProject->update(['sow_path' => $sowPath]);
        }

        // Sync assigned users
        DB::table('customer_project_users')
            ->where('customer_project_id', $customerProject->id)
            ->delete();
        $assignedUsers = $validated['assigned_user_ids'] ?? [];
        if (!empty($assignedUsers)) {
            foreach ($assignedUsers as $au) {
                DB::table('customer_project_users')->insert([
                    'customer_project_id' => $customerProject->id,
                    'user_id' => $au['id'],
                    'days_allocated' => $au['days'] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now()]);
            }
        }

        $customerProject->refresh();
        $this->syncCriticalPathTasks($customerProject);
        $customerProject->load(['customer', 'service', 'customerProjectModules.module', 'workflowTemplate']);

        return response()->json(['success' => true, 'customerProject' => $customerProject]);
    }

    public function updateModuleStatus(Request $request, $customerProjectId, $moduleId)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string']);

        $customerProjectModule = CustomerProjectModule::where('customer_project_id', $customerProjectId)
            ->where('module_id', $moduleId)
            ->firstOrFail();

        $customerProjectModule->update([
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description]);

        return response()->json(['success' => true, 'customerProjectModule' => $customerProjectModule]);
    }

    public function destroy($id)
    {
        $customerProject = CustomerProject::where('id', $id)
            ->firstOrFail();

        Task::query()
            ->where('task_type', 'cp')
            ->where('customer_project_id', $customerProject->id)
            ->delete();

        $customerProject->delete();

        return response()->json(['success' => true]);
    }

    public function getCustomers()
    {
        $customers = Customer::orderBy('name')
            ->get();

        return response()->json($customers);
    }

    public function getServices()
    {
        $services = Service::with('modules')
            ->orderBy('name')
            ->get();

        return response()->json($services);
    }

    protected function syncCriticalPathTasks(CustomerProject $project): void
    {
        if (!$project->critical_path_enabled || !$project->workflow_template_id) {
            Task::query()
                ->where('task_type', 'cp')
                ->where('customer_project_id', $project->id)
                ->delete();
            return;
        }

        $project->loadMissing('assignedUsers');

        $project->loadMissing('assignedUsers');

        $template = WorkflowTemplate::with(['tasks' => function ($query) {
            $query->orderBy('position');
        }])->find($project->workflow_template_id);

        if (!$template) {
            Task::query()
                ->where('task_type', 'cp')
                ->where('customer_project_id', $project->id)
                ->delete();
            return;
        }

        $defaultStatusId = TaskStatus::query()->orderBy('order')->value('id')
            ?? TaskStatus::query()->orderBy('id')->value('id');
        if (!$defaultStatusId) {
            return;
        }

        $creatorId = auth()->id() ?? (int) session('user_id');
        if (!$creatorId) {
            $creatorId = optional($project->assignedUsers->first())->id
                ?? optional($template->tasks->firstWhere('owner_id', '!=', null))->owner_id
                ?? Task::query()->whereNotNull('created_by')->value('created_by')
                ?? User::query()->orderBy('id')->value('id');
        }

        if (!$creatorId) {
            return;
        }
        if (!$creatorId) {
            $creatorId = optional($project->assignedUsers->first())->id
                ?? optional($template->tasks->firstWhere('owner_id', '!=', null))->owner_id
                ?? Task::query()->orderBy('created_by')->value('created_by')
                ?? User::query()->orderBy('id')->value('id');
        }

        if (!$creatorId) {
            return;
        }

        $existing = Task::query()
            ->where('task_type', 'cp')
            ->where('customer_project_id', $project->id)
            ->get()
            ->keyBy('workflow_task_id');

        $keptIds = [];

        foreach ($template->tasks as $templateTask) {
            if (!$templateTask->owner_id) {
                continue;
            }

            $payload = [
                'customer_id' => $project->customer_id,
                'customer_project_id' => $project->id,
                'workflow_task_id' => $templateTask->id,
                'user_id' => $templateTask->owner_id,
                'task_name' => $templateTask->name,
                'task' => $templateTask->name ?? ('Critical path task #' . $templateTask->id),
                'task_type' => 'cp',
                'due_date' => $this->calculateCriticalPathDueDate($project, $templateTask),
            ];

            if ($existingTask = $existing->get($templateTask->id)) {
                $existingTask->fill($payload);
                $existingTask->save();
                $keptIds[] = $existingTask->id;
            } else {
                $newTask = Task::create(array_merge($payload, [
                    'created_by' => $creatorId,
                    'task_status_id' => $defaultStatusId,
                    'task_priority_id' => null,
                ]));
                $keptIds[] = $newTask->id;
            }
        }

        Task::query()
            ->where('task_type', 'cp')
            ->where('customer_project_id', $project->id)
            ->when(!empty($keptIds), function ($query) use ($keptIds) {
                $query->whereNotIn('id', $keptIds);
            })
            ->delete();
    }

    protected function calculateCriticalPathDueDate(CustomerProject $project, WorkflowTask $templateTask): ?string
    {
        if (!$project->start_date) {
            return null;
        }

        $duration = $templateTask->duration_days ?? null;
        if ($duration === null || $duration === '') {
            return null;
        }

        $start = Carbon::parse($project->start_date)->startOfDay();

        return $start->copy()->addDays((int) $duration)->format('Y-m-d');
    }
}
