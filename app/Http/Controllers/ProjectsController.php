<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\CustomerProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\TenantAwareStorage;

class ProjectsController extends Controller
{
    use TenantAwareStorage;
    public function index()
    {
        // Summary Stats
        $totalProjects = CustomerProject::count();
        $activeProjects = CustomerProject::where('status', 'Ongoing')->count();
        $completedProjects = CustomerProject::where('status', 'Completed')->count();
        $pendingProjects = CustomerProject::where('status', 'Closed')->count(); // Mapping to Closed for now as Pending is not in enum

        return view('projects.project-tracking', compact('totalProjects', 'activeProjects', 'completedProjects', 'pendingProjects'));
    }

    public function show($id)
    {
        $project = CustomerProject::with(['customer', 'service', 'assignedUsers', 'remarks' => function($q) {
            $q->with('user')->latest();
        }, 'latestRemark' => function($q) {
            $q->with('user');
        }])->findOrFail($id);
        $worklogs = \App\Models\Worklog::where('customer_project_id', $id)
                    ->with(['user', 'entryType', 'module'])
                    ->orderBy('work_date', 'desc')
                    ->get();
        // Since we are loading modules for filter, we need to know all possible modules for this service
        $modules = $project->service ? $project->service->modules : collect();

        return view('projects.project-details', compact('project', 'worklogs', 'modules'));
    }

    public function fetch(Request $request)
    {
        $query = CustomerProject::with(['customer', 'service', 'assignedUsers'])->where('status', 'Ongoing');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('service', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by Status
        if ($request->filled('project_status')) {
            $query->where('status', $request->project_status);
        }
        
        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by Service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Filter by Starred
        if ($request->boolean('is_starred')) {
            $query->where('is_favourite', 1);
        } else {
            $query->where('is_favourite', 0);
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(50);

        return response()->json($projects);
    }
    
    public function toggleFavourite($id)
    {
        $project = CustomerProject::findOrFail($id);
        $project->is_favourite = !$project->is_favourite;
        $project->save();

        return response()->json([
            'success' => true,
            'is_favourite' => $project->is_favourite
        ]);
    }

    // Fetch projects for a specific customer (for dropdowns)
    public function fetchByCustomer($customerId)
    {
        $projects = CustomerProject::where('customer_id', $customerId)
                                   ->where('status', 'Ongoing')
                                   ->orderBy('project_name')
                                   ->get(['id', 'project_name', 'status']);
        return response()->json($projects);
    }

    public function fetchCustomers(Request $request)
    {
        $query = Customer::withCount('customerProjects');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->orderBy('name')->paginate(50);

        return response()->json($customers);
    }

    public function getOptions()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $services = Service::orderBy('name')->get(['id', 'name']);
        return response()->json([
            'customers' => $customers,
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'project_name' => 'required|string|max:255',
            'status' => 'required|string|in:Ongoing,Completed,Closed',
            'completed_percentage' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'sow_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('sow_document')) {
            // Use tenant-aware storage with isolation
            $data['sow_path'] = $this->storeTenantFile($request->file('sow_document'), 'customer-projects/sow');
        }
        if ($request->has('project_status') && !$request->has('status')) {
            $data['status'] = $request->project_status;
        }
        $project = CustomerProject::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data' => $project->load(['customer', 'service'])
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'project_name' => 'required|string|max:255',
            'project_status' => 'required|integer|in:0,1,2',
            'completed_percentage' => 'nullable|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'sow_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->all();
        if ($request->has('project_status') && !$request->has('status')) {
            $data['status'] = $request->project_status;
        }
        $project = CustomerProject::findOrFail($id);
        
        if ($request->hasFile('sow_document')) {
            if ($project->sow_path && Storage::disk('public')->exists($project->sow_path)) {
                Storage::disk('public')->delete($project->sow_path);
            }
            $data['sow_path'] = $request->file('sow_document')->store('customer-projects/sow', 'public');
        }

        $project->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->load(['customer', 'service'])
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'completed_percentage' => 'required|integer|min:0|max:100',
        ]);

        $project = CustomerProject::findOrFail($id);
        $project->update(['completed_percentage' => $request->completed_percentage]);

        return response()->json([
            'success' => true,
            'message' => 'Progress updated',
            'data' => $project
        ]);
    }

    public function destroy($id)
    {
        $project = CustomerProject::findOrFail($id);
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:Ongoing,Completed,Closed'
        ]);

        $project = CustomerProject::findOrFail($id);
        $project->update(['status' => $request->project_status]);

        return response()->json([
            'success' => true,
            'message' => 'Project status updated successfully'
        ]);
    }

    public function fetchWorklogs(Request $request, $projectId)
    {
        $query = \App\Models\Worklog::where('customer_project_id', $projectId)
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
            'worklogs' => $worklogs
        ]);
    }
}
