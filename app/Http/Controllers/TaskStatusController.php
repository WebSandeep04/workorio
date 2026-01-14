<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;

class TaskStatusController extends Controller
{
    /**
     * Display the task status management page
     */
    public function index()
    {
        return view('task-status.index');
    }

    /**
     * Fetch all task statuses
     */
    public function fetch(Request $request)
    {
        $query = TaskStatus::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $statuses = $query->orderBy('order')
            ->paginate(10);

        return response()->json($statuses);
    }

    /**
     * Store a new task status
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer'
        ]);

        $status = TaskStatus::create([
            'name' => $request->name,
            'color' => $request->color ?? '#6c757d',
            'order' => $request->order ?? 0
        ]);

        return response()->json([
            'message' => 'Task status created successfully',
            'status' => $status
        ]);
    }

    /**
     * Update a task status
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer'
        ]);

        $status = TaskStatus::findOrFail($id);
        $status->update([
            'name' => $request->name,
            'color' => $request->color ?? '#6c757d',
            'order' => $request->order ?? 0
        ]);

        return response()->json([
            'message' => 'Task status updated successfully',
            'status' => $status
        ]);
    }

    /**
     * Delete a task status
     */
    public function destroy($id)
    {
        $status = TaskStatus::findOrFail($id);
        
        // Check if status is being used by any tasks
        if ($status->tasks()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete this status as it is being used by tasks'
            ], 422);
        }

        $status->delete();

        return response()->json(['message' => 'Task status deleted successfully']);
    }
}
