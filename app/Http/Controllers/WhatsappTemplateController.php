<?php

namespace App\Http\Controllers;

use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsappTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setup.whatsapp-template');
    }

    /**
     * Fetch templates with pagination
     */
    public function fetch(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = WhatsappTemplate::orderBy('created_at', 'desc');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('text', 'like', '%' . $search . '%');
        }

        $templates = $query->paginate($perPage);
        
        return response()->json($templates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string'
        ]);

        $template = WhatsappTemplate::create([
            'name' => $request->name,
            'text' => $request->text
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template created successfully!',
            'data' => $template
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        $template = WhatsappTemplate::findOrFail($id);
        return response()->json(['data' => $template]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string'
        ]);

        $template = WhatsappTemplate::findOrFail($id);
        $template->update([
            'name' => $request->name,
            'text' => $request->text
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template updated successfully!',
            'data' => $template
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $template = WhatsappTemplate::findOrFail($id);
        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template deleted successfully!'
        ]);
    }
}
