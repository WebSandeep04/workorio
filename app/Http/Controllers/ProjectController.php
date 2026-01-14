<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        return view('project.index');
    }

    public function fetchProjects(Request $request)
    {
        $query = Service::with('modules');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string']);

        $project = Service::create([
            'name' => $request->name,
            'description' => $request->description]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string']);

        $project = Service::where('id', $id)
            ->firstOrFail();

        $project->update([
            'name' => $request->name,
            'description' => $request->description]);

        return response()->json(['success' => true, 'project' => $project]);
    }

    public function destroy($id)
    {
        $project = Service::where('id', $id)
            ->firstOrFail();

        $project->delete();

        return response()->json(['success' => true]);
    }
}
