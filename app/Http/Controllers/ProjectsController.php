<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\CustomerProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    public function index()
    {
        // Summary Stats
        $totalProjects = CustomerProject::count();
        $activeProjects = CustomerProject::where('status', 'in_progress')->count();
        $completedProjects = CustomerProject::where('status', 'completed')->count();
        $pendingProjects = CustomerProject::where('status', 'pending')->count();

        return view('projects.project-tracking', compact('totalProjects', 'activeProjects', 'completedProjects', 'pendingProjects'));
    }

    public function show($id)
    {
        $project = CustomerProject::with('customer', 'service')->findOrFail($id);
        return view('projects.project-details', compact('project'));
    }

    public function fetch(Request $request)
    {
        $query = CustomerProject::with(['customer', 'service', 'assignedUsers']);

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
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(50);

        return response()->json($projects);
    }
    
    // Fetch projects for a specific customer (for dropdowns)
    public function fetchByCustomer($customerId)
    {
        $projects = CustomerProject::where('customer_id', $customerId)
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
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $project = CustomerProject::create($validated);

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
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $project = CustomerProject::findOrFail($id);
        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data' => $project->load(['customer', 'service'])
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
}
