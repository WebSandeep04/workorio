<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentCategory;
use App\Models\DocumentSubcategory;
use App\Models\Document;
use App\Models\User;
use App\Models\CategoryUserAccess;
use App\Models\SubcategoryUserAccess;
use App\Models\DocumentUserAccess;

class DocumentController extends Controller
{
    /**
     * Check if current user has document management permission
     */
    private function hasManagePermission()
    {
        $userId = session('user_id') ?? auth()->id() ?? 1;
        $user = User::find($userId);
        
        // Admin users (role_id = 1) always have manage permission
        if ($user && $user->role_id == 1) {
            return true;
        }
        
        // Check if user has documents.manage permission
        if ($user && $user->role) {
            $permissions = $user->role->permissions_data ?? [];
            if (is_string($permissions)) {
                $permissions = json_decode($permissions, true) ?? [];
            }
            return in_array('documents.manage', $permissions);
        }
        
        return false;
    }

    /**
     * Display the document management page
     */
    public function index()
    {
        $categories = DocumentCategory::active()->ordered()->get();
        $canManage = $this->hasManagePermission();
        return view('document.index', compact('categories', 'canManage'));
    }

    /**
     * Display all documents in a single view
     */
    public function viewAll()
    {
        $documents = Document::with(['category', 'subcategory', 'uploader'])
            ->active()
            ->latest()
            ->paginate(20);
        
        $categories = DocumentCategory::active()->withCount('documents')->orderBy('name')->get();
        
        return view('document.view-all', compact('documents', 'categories'));
    }

    /**
     * Display a specific document category/folder page
     */
    public function show($category)
    {
        $categoryData = DocumentCategory::where('slug', $category)->firstOrFail();
        
        // Get current user ID and role
        $userId = session('user_id') ?? auth()->id() ?? 1;
        $user = User::find($userId);
        $isAdmin = $user && $user->role_id == 1; // Admin users can see everything
        
        // Build query for subcategories
        $query = $categoryData->subcategories()
            ->active()
            ->ordered();
        
        // If not admin, filter by user access
        if (!$isAdmin) {
            $query->whereHas('subcategoryAccess', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        
        // Get subcategories with document count
        $subcategories = $query->withCount(['documents' => function($q) use ($isAdmin, $userId) {
            $q->where('is_active', true);
            
            // If not admin, only count documents user has access to
            if (!$isAdmin) {
                $q->whereHas('documentAccess', function($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
            }
        }])->get();
        
        $canManage = $this->hasManagePermission();
        return view('document.show', compact('categoryData', 'subcategories', 'canManage'));
    }

    /**
     * Display a specific subcategory page
     */
    public function showSubcategory($category, $subcategory)
    {
        $categoryData = DocumentCategory::where('slug', $category)->firstOrFail();
        $subcategoryData = $categoryData->subcategories()->where('slug', $subcategory)->firstOrFail();
        
        // Get current user ID and role
        $userId = session('user_id') ?? auth()->id() ?? 1;
        $user = User::find($userId);
        $isAdmin = $user && $user->role_id == 1; // Admin users can see everything
        
        // Build query for documents
        $query = $subcategoryData->documents()->active()->with('uploader');
        
        // If not admin, filter by user access
        if (!$isAdmin) {
            $query->whereHas('documentAccess', function($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        
        $documents = $query->latest()->get();
        
        $canManage = $this->hasManagePermission();
        return view('document.subcategory', compact('categoryData', 'subcategoryData', 'documents', 'canManage'));
    }

    /**
     * Fetch documents for the current user/tenant
     */
    public function fetch(Request $request)
    {
        try {
            $category = $request->get('category');
            $subcategory = $request->get('subcategory');
            
            // Get current user ID and role
            $userId = session('user_id') ?? auth()->id() ?? 1;
            $user = User::find($userId);
            $isAdmin = $user && $user->role_id == 1; // Admin users can see everything
            
            // Build query
            $query = Document::with(['category', 'subcategory', 'uploader'])
                ->where('is_active', true);
            
            // Filter by category if provided
            if ($category) {
                $categoryData = DocumentCategory::where('slug', $category)->first();
                if ($categoryData) {
                    $query->where('category_id', $categoryData->id);
                }
            }
            
            // Filter by subcategory if provided
            if ($subcategory) {
                $subcategoryData = DocumentSubcategory::where('slug', $subcategory)->first();
                if ($subcategoryData) {
                    $query->where('subcategory_id', $subcategoryData->id);
                }
            }
            
            // If not admin, filter by document access
            if (!$isAdmin) {
                $query->whereHas('documentAccess', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }
            
            // Get documents
            $documents = $query->latest()->get()->map(function($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'description' => $document->description,
                    'file_size' => $document->file_size,
                    'formatted_file_size' => $document->formatted_file_size,
                    'created_at' => $document->created_at->toISOString(),
                    'uploader_name' => $document->uploader->name ?? 'Unknown',
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $documents,
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly uploaded document
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|integer|exists:document_categories,id',
                'subcategory' => 'nullable|integer|exists:document_subcategories,id',
                'file' => 'required|file|max:10240', // Max 10MB
            ]);

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFilename = $file->getClientOriginalName();
                $filename = time() . '_' . $originalFilename;
                $path = $file->storeAs('documents', $filename, 'public');
                
                // Create document record
                $document = Document::create([
                    'category_id' => $request->category,
                    'subcategory_id' => $request->subcategory,
                    'title' => $request->title,
                    'description' => $request->description,
                    'filename' => $filename,
                    'original_filename' => $originalFilename,
                    'file_path' => $path,
                    'file_extension' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => 1,
                    'is_active' => true
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded.'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update document information
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Find the document
            $document = Document::where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            // Update document
            $document->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document
     */
    public function destroy($id)
    {
        try {
            // Find the document
            $document = Document::where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            // Delete the file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the document record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a document
     */
    public function download($id)
    {
        try {
            // Find the document
            $document = Document::where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                    'message' => 'File not found on server'
            ], 404);
            }

            // Get the file path
            $filePath = Storage::disk('public')->path($document->file_path);
            
            // Return file download response
            return response()->download($filePath, $document->original_filename, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $document->original_filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all document categories
     */
    public function getCategories()
    {
        try {
            $categories = DocumentCategory::active()->ordered()->get();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new document category
     */
    public function storeCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0'
            ]);

            $slug = \Str::slug($request->name);
            
            // Check if slug already exists
            $existingCategory = DocumentCategory::where('slug', $slug)->first();
            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'A category with this name already exists'
                ], 422);
            }

            $category = DocumentCategory::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $request->icon ?? 'bi-folder',
                'color' => $request->color ?? 'primary',
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a document category
     */
    public function updateCategory(Request $request, $id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            $slug = \Str::slug($request->name);
            
            // Check if slug already exists (excluding current category)
            $existingCategory = DocumentCategory::where('slug', $slug)->where('id', '!=', $id)->first();
            if ($existingCategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'A category with this name already exists'
                ], 422);
            }

            $category->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $request->icon,
                'color' => $request->color,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ?? $category->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document category
     */
    public function destroyCategory($id)
    {
        try {
            $category = DocumentCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new document subcategory
     */
    public function storeSubcategory(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|integer|exists:document_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0'
            ]);

            $slug = \Str::slug($request->name);
            
            // Check if slug already exists within the same category
            $existingSubcategory = DocumentSubcategory::where('category_id', $request->category_id)
                ->where('slug', $slug)
                ->first();
                
            if ($existingSubcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'A subcategory with this name already exists in this category'
                ], 422);
            }

            $subcategory = DocumentSubcategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $request->icon ?? 'bi-folder',
                'color' => $request->color ?? 'primary',
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory created successfully',
                'data' => $subcategory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a document subcategory
     */
    public function updateSubcategory(Request $request, $id)
    {
        try {
            $subcategory = DocumentSubcategory::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:100',
                'color' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean'
            ]);

            $slug = \Str::slug($request->name);
            
            // Check if slug already exists within the same category (excluding current subcategory)
            $existingSubcategory = DocumentSubcategory::where('category_id', $subcategory->category_id)
                ->where('slug', $slug)
                ->where('id', '!=', $id)
                ->first();
                
            if ($existingSubcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'A subcategory with this name already exists in this category'
                ], 422);
            }

            $subcategory->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'icon' => $request->icon,
                'color' => $request->color,
                'sort_order' => $request->sort_order,
                'is_active' => $request->is_active ?? $subcategory->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory updated successfully',
                'data' => $subcategory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a document subcategory
     */
    public function destroySubcategory($id)
    {
        try {
            $subcategory = DocumentSubcategory::findOrFail($id);
            
            // Check if subcategory has documents
            if ($subcategory->documents()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete subcategory that contains documents'
                ], 422);
            }
            
            $subcategory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subcategory deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subcategory',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get document details
     */
    public function showDocument($id)
    {
        try {
            $document = Document::with(['category', 'subcategory', 'uploader'])
                ->where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $document
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch users for category settings
     */
    public function getUsers(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            
            // Get all users
            $allUsers = User::select('id', 'name', 'email')->orderBy('name')->get();
            
            // Get selected users for this category
            $selectedUserIds = [];
            if ($categoryId) {
                $selectedUserIds = CategoryUserAccess::where('category_id', $categoryId)
                    ->pluck('user_id')
                    ->toArray();
            }
            
            // Map users with selection status
            $users = $allUsers->map(function($user) use ($selectedUserIds) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_selected' => in_array($user->id, $selectedUserIds)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save category user access
     */
    public function saveCategoryUserAccess(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:document_categories,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $categoryId = $request->category_id;
            $userIds = $request->user_ids;

            // Use transaction for data integrity
            DB::transaction(function() use ($categoryId, $userIds) {
                // Delete existing access for this category
                CategoryUserAccess::where('category_id', $categoryId)->delete();
                
                // Insert new access records
                $insertData = array_map(function($userId) use ($categoryId) {
                    return [
                        'category_id' => $categoryId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                if (!empty($insertData)) {
                    CategoryUserAccess::insert($insertData);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Category user access saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save category user access',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch users for subcategory settings
     */
    public function getSubcategoryUsers(Request $request)
    {
        try {
            $subcategoryId = $request->get('subcategory_id');
            
            \Log::info('getSubcategoryUsers called', ['subcategory_id' => $subcategoryId]);
            
            // Get all users
            $allUsers = User::select('id', 'name', 'email')->orderBy('name')->get();
            
            \Log::info('Users fetched', ['count' => $allUsers->count()]);
            
            // Get selected users for this subcategory
            $selectedUserIds = [];
            if ($subcategoryId) {
                $selectedUserIds = SubcategoryUserAccess::where('subcategory_id', $subcategoryId)
                    ->pluck('user_id')
                    ->toArray();
                    
                \Log::info('Selected users fetched', ['count' => count($selectedUserIds), 'user_ids' => $selectedUserIds]);
            }
            
            // Map users with selection status
            $users = $allUsers->map(function($user) use ($selectedUserIds) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_selected' => in_array($user->id, $selectedUserIds)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getSubcategoryUsers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save subcategory user access
     */
    public function saveSubcategoryUserAccess(Request $request)
    {
        try {
            $request->validate([
                'subcategory_id' => 'required|exists:document_subcategories,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $subcategoryId = $request->subcategory_id;
            $userIds = $request->user_ids;

            // Get subcategory to get category_id
            $subcategory = DocumentSubcategory::findOrFail($subcategoryId);
            $categoryId = $subcategory->category_id;

            // Use transaction for data integrity
            DB::transaction(function() use ($categoryId, $subcategoryId, $userIds) {
                // First, ensure all users have category access
                $categoryAccessData = array_map(function($userId) use ($categoryId) {
                    return [
                        'category_id' => $categoryId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                // Insert category access (ignore duplicates)
                if (!empty($categoryAccessData)) {
                    CategoryUserAccess::insertOrIgnore($categoryAccessData);
                }
                
                // Delete existing subcategory access
                SubcategoryUserAccess::where('subcategory_id', $subcategoryId)->delete();
                
                // Insert new subcategory access records
                $subcategoryAccessData = array_map(function($userId) use ($categoryId, $subcategoryId) {
                    return [
                        'category_id' => $categoryId,
                        'subcategory_id' => $subcategoryId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                if (!empty($subcategoryAccessData)) {
                    SubcategoryUserAccess::insert($subcategoryAccessData);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Subcategory user access saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save subcategory user access',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch users for document settings
     */
    public function getDocumentUsers(Request $request)
    {
        try {
            $documentId = $request->get('document_id');
            
            // Get all users
            $allUsers = User::select('id', 'name', 'email')->orderBy('name')->get();
            
            // Get selected users for this document
            $selectedUserIds = [];
            if ($documentId) {
                $selectedUserIds = DocumentUserAccess::where('document_id', $documentId)
                    ->pluck('user_id')
                    ->toArray();
            }
            
            // Map users with selection status
            $users = $allUsers->map(function($user) use ($selectedUserIds) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_selected' => in_array($user->id, $selectedUserIds)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getDocumentUsers', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save document user access
     */
    public function saveDocumentUserAccess(Request $request)
    {
        try {
            $request->validate([
                'document_id' => 'required|exists:documents,id',
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id'
            ]);

            $documentId = $request->document_id;
            $userIds = $request->user_ids;

            // Get document to get category_id and subcategory_id
            $document = Document::findOrFail($documentId);
            $categoryId = $document->category_id;
            $subcategoryId = $document->subcategory_id;

            // Use transaction for data integrity
            DB::transaction(function() use ($categoryId, $subcategoryId, $documentId, $userIds) {
                // First, ensure all users have category access
                $categoryAccessData = array_map(function($userId) use ($categoryId) {
                    return [
                        'category_id' => $categoryId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                // Insert category access (ignore duplicates)
                if (!empty($categoryAccessData)) {
                    CategoryUserAccess::insertOrIgnore($categoryAccessData);
                }
                
                // Second, ensure all users have subcategory access
                $subcategoryAccessData = array_map(function($userId) use ($categoryId, $subcategoryId) {
                    return [
                        'category_id' => $categoryId,
                        'subcategory_id' => $subcategoryId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                // Insert subcategory access (ignore duplicates)
                if (!empty($subcategoryAccessData)) {
                    SubcategoryUserAccess::insertOrIgnore($subcategoryAccessData);
                }
                
                // Delete existing document access
                DocumentUserAccess::where('document_id', $documentId)->delete();
                
                // Insert new document access records
                $documentAccessData = array_map(function($userId) use ($categoryId, $subcategoryId, $documentId) {
                    return [
                        'category_id' => $categoryId,
                        'subcategory_id' => $subcategoryId,
                        'document_id' => $documentId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $userIds);
                
                if (!empty($documentAccessData)) {
                    DocumentUserAccess::insert($documentAccessData);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Document user access saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save document user access',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show user's document access permissions
     */
    public function showUserAccess()
    {
        try {
            // Get current user (you may need to adjust this based on your authentication)
            $userId = session('user_id') ?? auth()->id() ?? 1; // Fallback to user ID 1 for testing
            
            $user = User::find($userId);
            
            if (!$user) {
                abort(404, 'User not found');
            }
            
            // Get all categories the user has access to
            $categories = DocumentCategory::whereHas('categoryAccess', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['subcategories.subcategoryAccess' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->with(['subcategories.documents' => function($query) use ($userId) {
                $query->whereHas('documentAccess', function($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });
            }])
            ->active()
            ->ordered()
            ->get();
            
            return view('document.user-access', compact('user', 'categories'));
            
        } catch (\Exception $e) {
            \Log::error('Error in showUserAccess', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('document.index')
                ->with('error', 'Failed to load your document access permissions.');
        }
    }

}
