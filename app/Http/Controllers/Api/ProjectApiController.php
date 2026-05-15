<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Service;
use App\Models\CustomerProject;
use App\Models\CustomerProjectRemark;
use App\Models\Worklog;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\TenantAwareStorage;

class ProjectApiController extends Controller
{
    use TenantAwareStorage;

    /**
     * Fetch projects with advanced filtering and pagination
     */
    public function fetch(Request $request): JsonResponse
    {
        try {
            $query = CustomerProject::select('customer_projects.*')
                ->leftJoin('customers', 'customer_projects.customer_id', '=', 'customers.id')
                ->with(['customer', 'service', 'assignedUsers'])
                ->where('customer_projects.status', 'Ongoing'); // Kept as per desktop logic default

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('customer_projects.project_name', 'like', "%{$search}%")
                      ->orWhere('customer_projects.description', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('service', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }
            
            // Filter by Custom Status Override
            if ($request->filled('project_status')) {
                $query->where('customer_projects.status', $request->project_status);
            }
            
            // Filter by Customer
            if ($request->filled('customer_id')) {
                $query->where('customer_projects.customer_id', $request->customer_id);
            }

            // Filter by Service
            if ($request->filled('service_id')) {
                $query->where('customer_projects.service_id', $request->service_id);
            }

            // Filter by Starred
            if ($request->boolean('is_starred')) {
                $query->where('customer_projects.is_favourite', 1);
            } else {
                // If not strictly filtering for starred, show all including unstarred
                // Note: the desktop hardcoded toggle between 1 and 0, keeping it for continuity
                $query->where('customer_projects.is_favourite', 0);
            }

            $projects = $query->orderBy('customers.company_name', 'asc')->paginate(50);

            return response()->json($projects);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all Customers and Services lookup values
     */
    public function getOptions(): JsonResponse
    {
        try {
            $customers = Customer::orderBy('name')->get(['id', 'name', 'company_name']);
            $services = Service::orderBy('name')->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'customers' => $customers,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lookup lists: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch Project Details including associated metrics
     */
    public function show($id): JsonResponse
    {
        try {
            $project = CustomerProject::with([
                'customer', 
                'service', 
                'assignedUsers', 
                'remarks' => function($q) {
                    $q->with('user')->latest();
                }, 
                'latestRemark' => function($q) {
                    $q->with('user');
                }
            ])->find($id);

            if (!$project) {
                return response()->json(['success' => false, 'message' => 'Project record not found.'], 404);
            }

            $worklogs = Worklog::where('customer_project_id', $id)
                ->with(['user', 'entryType', 'module'])
                ->orderBy('work_date', 'desc')
                ->get();

            // Fetch modules linked to the specific service for filters
            $modules = $project->service ? $project->service->modules : collect();

            return response()->json([
                'success' => true,
                'project' => $project,
                'worklogs' => $worklogs,
                'modules' => $modules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception encountered: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Starred Favourite Status
     */
    public function toggleFavourite($id): JsonResponse
    {
        try {
            $project = CustomerProject::findOrFail($id);
            $project->is_favourite = !$project->is_favourite;
            $project->save();

            return response()->json([
                'success' => true,
                'is_favourite' => $project->is_favourite,
                'message' => $project->is_favourite ? 'Project added to favourites.' : 'Project removed from favourites.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calibrate completeness percentage velocity
     */
    public function updateProgress(Request $request, $id): JsonResponse
    {
        $request->validate([
            'completed_percentage' => 'required|integer|min:0|max:100',
        ]);

        try {
            $project = CustomerProject::findOrFail($id);
            $project->update(['completed_percentage' => $request->completed_percentage]);

            return response()->json([
                'success' => true,
                'message' => 'Project completion velocity updated.',
                'completed_percentage' => $project->completed_percentage
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Modify functional Project Status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:Ongoing,Completed,Closed'
        ]);

        try {
            $project = CustomerProject::findOrFail($id);
            $project->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Project status updated to ' . $request->status,
                'status' => $project->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch Tasks belonging to specific project
     */
    public function fetchTasks($projectId): JsonResponse
    {
        try {
            // Leverages structure found in TaskController fetchByCustomerProject
            $tasks = Task::where('customer_project_id', $projectId)
                ->with(['status', 'priority', 'assignedUsers', 'creator', 'remarks.user'])
                ->orderBy('due_date', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Filtered Worklog manifests
     */
    public function fetchWorklogs(Request $request, $projectId): JsonResponse
    {
        try {
            $query = Worklog::where('customer_project_id', $projectId)
                ->with(['user', 'entryType', 'module']);

            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->filled('module_id')) {
                $query->where('module_id', $request->module_id);
            }

            if ($request->filled('start_date')) {
                $query->whereDate('work_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('work_date', '<=', $request->end_date);
            }

            $worklogs = $query->orderBy('work_date', 'desc')->get();

            return response()->json([
                'success' => true,
                'worklogs' => $worklogs
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store New Project-Level Remark (Timeline Feed)
     */
    public function storeRemark(Request $request): JsonResponse
    {
        $request->validate([
            'customer_project_id' => 'required|exists:customer_projects,id',
            'remark' => 'required|string'
        ]);

        try {
            // Replicating logic inside CustomerProjectRemarkController
            $remark = CustomerProjectRemark::create([
                'customer_project_id' => $request->customer_project_id,
                'remark' => $request->remark,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Remark saved successfully!',
                'data' => $remark->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
