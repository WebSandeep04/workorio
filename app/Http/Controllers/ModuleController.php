<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index()
    {
        return view('module.index');
    }

    public function fetchModules(Request $request)
    {
        $query = Module::with('service');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $modules = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($modules);
    }

    public function getModulesByService($serviceId)
    {
        $modules = Module::where('service_id', $serviceId)
            ->orderBy('name')
            ->get();

        return response()->json($modules);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_id' => 'required|exists:services,id']);

        $module = Module::create([
            'name' => $request->name,
            'description' => $request->description,
            'service_id' => $request->service_id]);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_id' => 'required|exists:services,id']);

        $module = Module::where('id', $id)
            ->firstOrFail();

        $module->update([
            'name' => $request->name,
            'description' => $request->description,
            'service_id' => $request->service_id]);

        return response()->json(['success' => true, 'module' => $module]);
    }

    public function destroy($id)
    {
        $module = Module::where('id', $id)
            ->firstOrFail();

        $module->delete();

        return response()->json(['success' => true]);
    }
}
