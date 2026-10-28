<?php

namespace App\Http\Controllers;

use App\Models\WhatsappTemplate;
use App\Models\WhatsappTemplateDocument;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Traits\TenantAwareStorage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class WhatsappTemplateController extends Controller
{
    use TenantAwareStorage;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = WhatsappTemplateDocument::orderBy('title', 'asc')->get();
        return view('setup.whatsapp-template', compact('documents'));
    }

    /**
     * Fetch templates with pagination and linked documents
     */
    public function fetch(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = WhatsappTemplate::with(['documents'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('text', 'like', '%' . $search . '%');
            });
        }

        $templates = $query->paginate($perPage);
        
        return response()->json($templates);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'integer|exists:whatsapp_templates_documents_list,id'
        ]);

        $template = WhatsappTemplate::create([
            'name' => $request->name,
            'text' => $request->text
        ]);

        if ($request->has('document_ids')) {
            $template->documents()->sync($request->document_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template created successfully!',
            'data' => $template->load('documents')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): JsonResponse
    {
        $template = WhatsappTemplate::with('documents')->findOrFail($id);
        return response()->json(['data' => $template]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'integer|exists:whatsapp_templates_documents_list,id'
        ]);

        $template = WhatsappTemplate::findOrFail($id);
        $template->update([
            'name' => $request->name,
            'text' => $request->text
        ]);

        $template->documents()->sync($request->input('document_ids', []));

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template updated successfully!',
            'data' => $template->load('documents')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $template = WhatsappTemplate::findOrFail($id);
        $template->documents()->detach();
        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp Template deleted successfully!'
        ]);
    }

    /**
     * Fetch list of all uploaded WhatsApp template documents (JSON)
     */
    public function listDocuments(): JsonResponse
    {
        $documents = WhatsappTemplateDocument::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Upload a new document specifically for WhatsApp templates
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $request->validate([
            'document_file' => 'required|file|max:25600', // up to 25MB
            'title' => 'nullable|string|max:255'
        ]);

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $title = $request->input('title') ?: pathinfo($originalName, PATHINFO_FILENAME);
        $mimeType = $file->getClientMimeType();
        $size = $file->getSize();

        // Store tenant file under tenants/{tenant_id}/whatsapp_documents/
        $filePath = $this->storeTenantFile($file, 'whatsapp_documents', 'public');

        if (!$filePath) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document to storage.'
            ], 500);
        }

        $document = WhatsappTemplateDocument::create([
            'title' => $title,
            'filename' => basename($filePath),
            'original_filename' => $originalName,
            'file_path' => $filePath,
            'file_extension' => $extension,
            'file_size' => $size,
            'mime_type' => $mimeType
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully!',
            'data' => $document
        ]);
    }

    /**
     * Delete an uploaded WhatsApp document
     */
    public function deleteDocument($id): JsonResponse
    {
        $doc = WhatsappTemplateDocument::findOrFail($id);
        
        // Delete physical file from storage
        $this->deleteTenantFile($doc->file_path, 'public');
        
        $doc->templates()->detach();
        $doc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully!'
        ]);
    }

    /**
     * Public direct download endpoint for customer WhatsApp document links.
     * Enforces Content-Disposition: attachment so the browser downloads instead of opening.
     */
    public function publicDownload($tenantId, $id)
    {
        // 1. Switch to tenant database connection if needed
        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            TenantDatabaseService::setDefaultConnection($tenant->id);
        }

        $doc = WhatsappTemplateDocument::find($id);

        if (!$doc) {
            abort(404, 'Document not found.');
        }

        // Verify file on disk
        if (!Storage::disk('public')->exists($doc->file_path)) {
            abort(404, 'Document file not found in storage.');
        }

        $diskPath = Storage::disk('public')->path($doc->file_path);
        $downloadFileName = $doc->original_filename ?: ($doc->title . ($doc->file_extension ? '.' . $doc->file_extension : ''));

        // Return forced attachment download
        return response()->download($diskPath, $downloadFileName, [
            'Content-Disposition' => 'attachment; filename="' . addslashes($downloadFileName) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
