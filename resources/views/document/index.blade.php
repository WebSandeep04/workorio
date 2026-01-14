@extends('layouts.app')

@section('title', 'Document Management')
@section('page_title', 'Document Management')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <h4>Document Categories</h4>
            <p class="text-muted">Click on a category to manage documents</p>
        </div>
    </div>
    
    <div class="row mt-3">
        <!-- Add New Category Card - First -->
        @if($canManage)
        <div class="col-md-3 mb-3">
            <div class="card document-category-card" onclick="openAddCategoryModal()" style="border: 2px dashed #dee2e6;">
                <div class="card-body text-center">
                    <i class="bi bi-plus-circle text-muted" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title mt-2 text-muted">Add New Category</h5>
                    <p class="card-text text-muted">Create a new document category</p>
                </div>
            </div>
        </div>
        @endif
        
        @foreach($categories as $category)
        <div class="col-md-3 mb-3">
            <div class="card document-category-card">
                <div class="card-body text-center">
                    @if($canManage)
                    <i class="bi bi-gear-fill text-secondary category-settings-icon" 
                       onclick="event.stopPropagation(); openCategorySettingsModal({{ $category->id }}, '{{ $category->name }}')" 
                       style="position: absolute; top: 10px; right: 10px; cursor: pointer; font-size: 1.2rem; z-index: 10;"></i>
                    @endif
                    <div onclick="openDocumentCategory('{{ $category->slug }}')">
                        <i class="bi {{ $category->icon }} text-{{ $category->color }}" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-2">{{ $category->name }}</h5>
                        <p class="card-text text-muted">{{ $category->description }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.document-category-card {
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #dee2e6;
}

.document-category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}

.document-category-card .card-body {
    padding: 1.5rem;
    position: relative;
}

.category-settings-icon:hover {
    color: #0d6efd !important;
    transform: rotate(45deg);
    transition: all 0.3s ease;
}

.document-category-card[onclick*="openAddCategoryModal"] {
    border-style: dashed !important;
    border-width: 2px !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.document-category-card[onclick*="openAddCategoryModal"]:hover {
    border-color: #0d6efd !important;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.15);
}
</style>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="uploadDocumentForm" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Document Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File *</label>
                        <input type="file" class="form-control" id="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                        <div class="form-text">Maximum file size: 10MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Upload Document</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editDocumentForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDocumentModalLabel">Edit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="edit_document_id">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Document Title *</label>
                        <input type="text" class="form-control" id="edit_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select class="form-control" id="edit_category">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Update Document</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addCategoryForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="category_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_description" class="form-label">Description</label>
                        <textarea class="form-control" id="category_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category_icon" class="form-label">Icon</label>
                        <select class="form-control" id="category_icon" name="icon">
                            <option value="bi-folder">Folder</option>
                            <option value="bi-file-text">Document</option>
                            <option value="bi-file-earmark-text">Text Document</option>
                            <option value="bi-file-pdf">PDF</option>
                            <option value="bi-file-image">Image</option>
                            <option value="bi-file-slides">Presentation</option>
                            <option value="bi-file-spreadsheet">Spreadsheet</option>
                            <option value="bi-file-zip">Archive</option>
                            <option value="bi-file-music">Audio</option>
                            <option value="bi-file-play">Video</option>
                            <option value="bi-file-code">Code</option>
                            <option value="bi-file-binary">Binary</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_color" class="form-label">Color</label>
                        <select class="form-control" id="category_color" name="color">
                            <option value="primary">Primary (Blue)</option>
                            <option value="secondary">Secondary (Gray)</option>
                            <option value="success">Success (Green)</option>
                            <option value="danger">Danger (Red)</option>
                            <option value="warning">Warning (Yellow)</option>
                            <option value="info">Info (Cyan)</option>
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="category_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="category_sort_order" name="sort_order" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Create Category</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Category Settings Modal -->
<div class="modal fade" id="categorySettingsModal" tabindex="-1" aria-labelledby="categorySettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categorySettingsModalLabel">Category Settings: <span id="settingsCategoryName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Manage User Access</h6>
                    <p class="text-muted small">Select users who can access this category</p>
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-light sticky-top" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 10%;">Select</th>
                                <th style="width: 90%;">User</th>
                            </tr>
                        </thead>
                        <tbody id="usersList">
                            <tr>
                                <td colspan="2" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted mb-0">Loading users...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategorySettingsBtn">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function openDocumentCategory(category) {
    window.location.href = `/document/${category}`;
}

function openAddCategoryModal() {
    $('#addCategoryModal').modal('show');
}

let currentCategoryId = null;

function openCategorySettingsModal(categoryId, categoryName) {
    currentCategoryId = categoryId;
    $('#settingsCategoryName').text(categoryName);
    $('#categorySettingsModal').modal('show');
    
    // Fetch users
    loadUsersForCategory(categoryId);
}

function loadUsersForCategory(categoryId) {
    // Fetch users from the server
    $.ajax({
        url: '/document/users',
        type: 'GET',
        data: { category_id: categoryId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                renderUsersList(response.data);
            } else {
                $('#usersList').html('<p class="text-center text-danger py-4">Failed to load users</p>');
            }
        },
        error: function(xhr) {
            console.error('Error fetching users:', xhr);
            $('#usersList').html('<p class="text-center text-danger py-4">Error loading users</p>');
        }
    });
}

function renderUsersList(users) {
    if (users.length === 0) {
        $('#usersList').html('<tr><td colspan="2" class="text-center text-muted py-4">No users found</td></tr>');
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const checkedAttr = user.is_selected ? 'checked' : '';
        html += `
            <tr>
                <td class="text-center">
                    <input class="form-check-input user-select-checkbox" type="checkbox" value="${user.id}" id="user_${user.id}" ${checkedAttr}>
                </td>
                <td>
                    <div>
                        <strong>${user.name}</strong><br>
                        <small class="text-muted">${user.email}</small>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#usersList').html(html);
}

$(function () {
    // Initialize category cards
    $('.document-category-card').on('mouseenter', function() {
        $(this).addClass('shadow-lg');
    }).on('mouseleave', function() {
        $(this).removeClass('shadow-lg');
    });

    // Handle save category settings
    $('#saveCategorySettingsBtn').on('click', function() {
        const selectedUsers = [];
        
        // Collect selected users
        $('.user-select-checkbox:checked').each(function() {
            selectedUsers.push($(this).val());
        });
        
        // Disable button and show loading
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        
        // Make AJAX call to save settings
        $.ajax({
            url: '/document/category-access',
            type: 'POST',
            data: {
                category_id: currentCategoryId,
                user_ids: selectedUsers
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', `Category settings saved. ${selectedUsers.length} user(s) selected.`);
                    $('#categorySettingsModal').modal('hide');
                } else {
                    showAlert('error', response.message || 'Failed to save category settings');
                    $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response.message || 'Failed to save category settings');
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
            }
        });
    });

    // Handle add category form submission
    $('#addCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/document/categories',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#addCategoryModal').modal('hide');
                    $('#addCategoryForm')[0].reset();
                    // Reload the page to show the new category
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response.message || 'Failed to create category');
            }
        });
    });
});
</script>
@endpush