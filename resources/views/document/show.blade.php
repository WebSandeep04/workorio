@extends('layouts.app')

@section('title', $categoryData->name . ' Documents')
@section('page_title', $categoryData->name . ' Documents')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="mb-3">
        <h4>{{ $categoryData->name }} Documents</h4>
        <p class="text-muted">{{ $categoryData->description }}</p>
    </div>

        <!-- Subcategories Section -->
    <div class="row mt-3">
        <!-- Add New Subcategory Card - First -->
        @if($canManage)
        <div class="col-md-3 mb-3">
            <div class="card document-category-card" onclick="openAddSubcategoryModal()" style="border: 2px dashed #dee2e6;">
                <div class="card-body text-center">
                    <i class="bi bi-plus-circle text-muted" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title mt-2 text-muted">Add Subcategory</h5>
                    <p class="card-text text-muted">Create a new subcategory</p>
                </div>
            </div>
        </div>
        @endif
        
                 @foreach($subcategories as $subcategory)
         <div class="col-md-3 mb-3">
             <div class="card document-category-card">
                 <div class="card-body text-center">
                     @if($canManage)
                     <i class="bi bi-gear-fill text-secondary subcategory-settings-icon" 
                        onclick="event.stopPropagation(); openSubcategorySettingsModal({{ $subcategory->id }}, '{{ $subcategory->name }}')" 
                        style="position: absolute; top: 10px; right: 10px; cursor: pointer; font-size: 1.2rem; z-index: 10;"></i>
                     @endif
                     <div onclick="openSubcategory('{{ $subcategory->slug }}')">
                        <i class="bi {{ $subcategory->icon }} text-{{ $subcategory->color }}" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-2">{{ $subcategory->name }}</h5>
                        <p class="card-text text-muted">{{ $subcategory->description }}</p>
                        <small class="text-muted">{{ $subcategory->documents_count ?? 0 }} documents</small>
                    </div>
                </div>
            </div>
        </div>
         @endforeach
    </div>

</div>


<!-- Add Subcategory Modal -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1" aria-labelledby="addSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addSubcategoryForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSubcategoryModalLabel">Add New Subcategory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $categoryData->id }}">
                    <div class="mb-3">
                        <label for="subcategory_name" class="form-label">Subcategory Name *</label>
                        <input type="text" class="form-control" id="subcategory_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="subcategory_description" class="form-label">Description</label>
                        <textarea class="form-control" id="subcategory_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="subcategory_icon" class="form-label">Icon</label>
                        <select class="form-control" id="subcategory_icon" name="icon">
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
                        <label for="subcategory_color" class="form-label">Color</label>
                        <select class="form-control" id="subcategory_color" name="color">
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
                        <label for="subcategory_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="subcategory_sort_order" name="sort_order" min="0" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn button w-100">Create Subcategory</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Subcategory Settings Modal -->
<div class="modal fade" id="subcategorySettingsModal" tabindex="-1" aria-labelledby="subcategorySettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subcategorySettingsModalLabel">Subcategory Settings: <span id="settingsSubcategoryName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Manage User Access</h6>
                    <p class="text-muted small">Select users who can access this subcategory</p>
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-light sticky-top" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 10%;">Select</th>
                                <th style="width: 90%;">User</th>
                            </tr>
                        </thead>
                        <tbody id="subcategoryUsersList">
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
                <button type="button" class="btn btn-primary" id="saveSubcategorySettingsBtn">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

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

.subcategory-settings-icon:hover {
    color: #0d6efd !important;
    transform: rotate(45deg);
    transition: all 0.3s ease;
}

.document-category-card[onclick*="openAddSubcategoryModal"] {
    border-style: dashed !important;
    border-width: 2px !important;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.document-category-card[onclick*="openAddSubcategoryModal"]:hover {
    border-color: #0d6efd !important;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 110, 253, 0.15);
}
</style>

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

function openSubcategory(subcategorySlug) {
    window.location.href = `/document/{{ $categoryData->slug }}/${subcategorySlug}`;
}

function openAddSubcategoryModal() {
    $('#addSubcategoryModal').modal('show');
}

$(function () {
    // Handle add subcategory form submission
    $('#addSubcategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/document/subcategories',
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
                    $('#addSubcategoryModal').modal('hide');
                    $('#addSubcategoryForm')[0].reset();
                    // Reload the page to show the new subcategory
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response.message || 'Failed to create subcategory');
            }
        });
    });
});

let currentSubcategoryId = null;

function openSubcategorySettingsModal(subcategoryId, subcategoryName) {
    currentSubcategoryId = subcategoryId;
    $('#settingsSubcategoryName').text(subcategoryName);
    $('#subcategorySettingsModal').modal('show');
    
    // Fetch users
    loadUsersForSubcategory(subcategoryId);
}

function loadUsersForSubcategory(subcategoryId) {
    // Fetch users from the server
    $.ajax({
        url: '/document/subcategory-users',
        type: 'GET',
        data: { subcategory_id: subcategoryId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                renderSubcategoryUsersList(response.data);
            } else {
                $('#subcategoryUsersList').html('<p class="text-center text-danger py-4">Failed to load users</p>');
            }
        },
        error: function(xhr) {
            console.error('Error fetching users:', xhr);
            $('#subcategoryUsersList').html('<p class="text-center text-danger py-4">Error loading users</p>');
        }
    });
}

function renderSubcategoryUsersList(users) {
    if (users.length === 0) {
        $('#subcategoryUsersList').html('<tr><td colspan="2" class="text-center text-muted py-4">No users found</td></tr>');
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const checkedAttr = user.is_selected ? 'checked' : '';
        html += `
            <tr>
                <td class="text-center">
                    <input class="form-check-input subcategory-user-select-checkbox" type="checkbox" value="${user.id}" id="subcategory_user_${user.id}" ${checkedAttr}>
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
    
    $('#subcategoryUsersList').html(html);
}

$(document).ready(function() {
    // Handle save subcategory settings
    $('#saveSubcategorySettingsBtn').on('click', function() {
        const selectedUsers = [];
        
        // Collect selected users
        $('.subcategory-user-select-checkbox:checked').each(function() {
            selectedUsers.push($(this).val());
        });
        
        // Disable button and show loading
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        
        // Make AJAX call to save settings
        $.ajax({
            url: '/document/subcategory-access',
            type: 'POST',
            data: {
                subcategory_id: currentSubcategoryId,
                user_ids: selectedUsers
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', `Subcategory settings saved. ${selectedUsers.length} user(s) selected.`);
                    $('#subcategorySettingsModal').modal('hide');
                } else {
                    showAlert('error', response.message || 'Failed to save subcategory settings');
                    $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response.message || 'Failed to save subcategory settings');
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
            }
        });
    });
});
</script>
@endpush
