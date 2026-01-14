@extends('layouts.app')

@section('title', $subcategoryData->name . ' Documents')
@section('page_title', $subcategoryData->name . ' Documents')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('document.index') }}">Documents</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('document.show', $categoryData->slug) }}">{{ $categoryData->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $subcategoryData->name }}</li>
                </ol>
            </nav>
            <h4>{{ $subcategoryData->name }} Documents</h4>
            <p class="text-muted">{{ $subcategoryData->description }}</p>
        </div>
        @if($canManage)
        <button class="btn button" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-cloud-upload"></i> Upload New Document
        </button>
        @endif
    </div>
        
    <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered align-middle text-center border shadow-sm rounded" id="documentTable">
            <thead class="table-secondary">
                <tr>
                    @if($canManage)
                    <th scope="col" style="width: 5%;">Settings</th>
                    @endif
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">File Size</th>
                    <th scope="col">Upload Date</th>
                    <th scope="col">Uploaded By</th>
                    @if($canManage)
                    <th scope="col">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody id="documentTableBody">
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        Loading documents...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentModalLabel">Edit Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDocumentForm">
                @csrf
                <input type="hidden" id="edit_document_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Document Title *</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn button">Update Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the document "<strong id="deleteDocumentTitle"></strong>"?</p>
                <p class="text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentModalLabel">Upload {{ $subcategoryData->name }} Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Document Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
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
                    <input type="hidden" name="category" value="{{ $categoryData->id }}">
                    <input type="hidden" name="subcategory" value="{{ $subcategoryData->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn button">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Show custom alert using the alert container
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

let currentDocumentId = null;
let deleteDocumentId = null;

$(document).ready(function() {
    console.log('Documents page loaded');
    loadDocuments();
    
    // Handle upload form submission
    $('#uploadDocumentForm').on('submit', function(e) {
        e.preventDefault();
        uploadDocument();
    });
    
    // Handle edit form submission
    $('#editDocumentForm').on('submit', function(e) {
        e.preventDefault();
        updateDocument();
    });
    
    // Handle delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        deleteDocument();
    });
    
    // Handle edit and delete button clicks using event delegation
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const description = $(this).data('description');
        openEditModal(id, title, description);
    });
    
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        openDeleteModal(id, title);
    });
});

function loadDocuments() {
    $.ajax({
        url: '{{ route("document.fetch") }}',
        method: 'GET',
        data: {
            category: '{{ $categoryData->slug }}',
            subcategory: '{{ $subcategoryData->slug }}'
        },
        success: function(response) {
            if (response.success && response.data) {
                displayDocuments(response.data);
            } else {
                showAlert('error', 'Failed to load documents');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr);
            showAlert('error', 'Failed to load documents. Please try again.');
        }
    });
}

function displayDocuments(documents) {
    const tbody = $('#documentTableBody');
    
    if (documents.length === 0) {
        const colspan = @if($canManage) 7 @else 6 @endif;
        tbody.html(`
            <tr>
                <td colspan="${colspan}" class="text-center text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    No documents found.@if($canManage) Upload your first document!@endif
                </td>
            </tr>
        `);
        return;
    }
    
    let html = '';
    documents.forEach(function(document) {
        const uploadDate = new Date(document.created_at).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
        
        html += `<tr>`;
        
        @if($canManage)
        html += `
                <td class="text-center">
                    <i class="bi bi-gear-fill text-secondary document-settings-icon" 
                       onclick="openDocumentSettingsModal(${document.id}, '${document.title.replace(/'/g, "\\'")}')" 
                       style="cursor: pointer; font-size: 1.2rem;" 
                       title="Document Settings"></i>
                </td>
        `;
        @endif
        
        html += `
                <td>${document.title}</td>
                <td>${document.description || '-'}</td>
                <td>${document.formatted_file_size || formatFileSize(document.file_size)}</td>
                <td>${uploadDate}</td>
                <td>${document.uploader_name || 'Unknown'}</td>
        `;
        
        @if($canManage)
        html += `
                <td>
                    <button class="btn btn-sm btn-primary edit-btn" data-id="${document.id}" data-title="${document.title}" data-description="${document.description || ''}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <a href="/document/${document.id}/download" class="btn btn-sm btn-success" target="_blank">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${document.id}" data-title="${document.title}">
                        <i class="bi bi-trash3-fill me-1"></i> Delete
                    </button>
                </td>
        `;
        @else
        html += `
                <td>
                    <a href="/document/${document.id}/download" class="btn btn-sm btn-success" target="_blank">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </td>
        `;
        @endif
        
        html += `</tr>`;
    });
    
    tbody.html(html);
}

function uploadDocument() {
    const formData = new FormData($('#uploadDocumentForm')[0]);
    
    $.ajax({
        url: '{{ route("document.store") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#uploadDocumentModal').modal('hide');
                    }
                } catch (error) {
                    $('#uploadDocumentModal').modal('hide');
                }
                $('#uploadDocumentForm')[0].reset();
                loadDocuments();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('error', response.message || 'Failed to upload document');
        }
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function openEditModal(id, title, description) {
    currentDocumentId = id;
    $('#edit_document_id').val(id);
    $('#edit_title').val(title);
    $('#edit_description').val(description || '');
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('editDocumentModal'));
        modal.show();
    } catch (error) {
        $('#editDocumentModal').modal('show');
    }
}

function openDeleteModal(id, title) {
    deleteDocumentId = id;
    $('#deleteDocumentTitle').text(title);
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } catch (error) {
        $('#deleteModal').modal('show');
    }
}

function updateDocument() {
    const formData = {
        title: $('#edit_title').val(),
        description: $('#edit_description').val(),
        _token: '{{ csrf_token() }}'
    };
    
    $.ajax({
        url: `/document/${currentDocumentId}`,
        method: 'PUT',
        data: formData,
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editDocumentModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#editDocumentModal').modal('hide');
                    }
                } catch (error) {
                    $('#editDocumentModal').modal('hide');
                }
                loadDocuments();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('error', response.message || 'Failed to update document');
        }
    });
}

function deleteDocument() {
    $.ajax({
        url: `/document/${deleteDocumentId}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    if (modal) {
                        modal.hide();
                    } else {
                        $('#deleteModal').modal('hide');
                    }
                } catch (error) {
                    $('#deleteModal').modal('hide');
                }
                loadDocuments();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('error', response.message || 'Failed to delete document');
        }
    });
}

// Document Settings Modal (using existing currentDocumentId variable)
const documentSettingsModal = `
<div class="modal fade" id="documentSettingsModal" tabindex="-1" aria-labelledby="documentSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentSettingsModalLabel">Document Settings: <span id="settingsDocumentName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Manage User Access</h6>
                    <p class="text-muted small">Select users who can access this document</p>
                </div>
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead class="table-light sticky-top" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 10%;">Select</th>
                                <th style="width: 90%;">User</th>
                            </tr>
                        </thead>
                        <tbody id="documentUsersList">
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
                <button type="button" class="btn btn-primary" id="saveDocumentSettingsBtn">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</div>
`;

// Append modal to body
$('body').append(documentSettingsModal);

function openDocumentSettingsModal(documentId, documentName) {
    currentDocumentId = documentId;
    $('#settingsDocumentName').text(documentName);
    $('#documentSettingsModal').modal('show');
    
    // Fetch users
    loadUsersForDocument(documentId);
}

function loadUsersForDocument(documentId) {
    $.ajax({
        url: '/document/document-users',
        type: 'GET',
        data: { document_id: documentId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                renderDocumentUsersList(response.data);
            } else {
                $('#documentUsersList').html('<tr><td colspan="2" class="text-center text-danger py-4">Failed to load users</td></tr>');
            }
        },
        error: function(xhr) {
            console.error('Error fetching users:', xhr);
            $('#documentUsersList').html('<tr><td colspan="2" class="text-center text-danger py-4">Error loading users</td></tr>');
        }
    });
}

function renderDocumentUsersList(users) {
    if (users.length === 0) {
        $('#documentUsersList').html('<tr><td colspan="2" class="text-center text-muted py-4">No users found</td></tr>');
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const checkedAttr = user.is_selected ? 'checked' : '';
        html += `
            <tr>
                <td class="text-center">
                    <input class="form-check-input document-user-select-checkbox" type="checkbox" value="${user.id}" id="document_user_${user.id}" ${checkedAttr}>
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
    
    $('#documentUsersList').html(html);
}

$(document).ready(function() {
    // Handle save document settings
    $('#saveDocumentSettingsBtn').on('click', function() {
        const selectedUsers = [];
        
        // Collect selected users
        $('.document-user-select-checkbox:checked').each(function() {
            selectedUsers.push($(this).val());
        });
        
        // Disable button and show loading
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
        
        // Make AJAX call to save settings
        $.ajax({
            url: '/document/document-access',
            type: 'POST',
            data: {
                document_id: currentDocumentId,
                user_ids: selectedUsers
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', `Document settings saved. ${selectedUsers.length} user(s) selected.`);
                    $('#documentSettingsModal').modal('hide');
                } else {
                    showAlert('error', response.message || 'Failed to save document settings');
                    $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showAlert('error', response.message || 'Failed to save document settings');
                $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Settings');
            }
        });
    });
});

// CSS for settings icon hover effect
$('head').append(`
<style>
.document-settings-icon:hover {
    color: #0d6efd !important;
    transform: rotate(45deg);
    transition: all 0.3s ease;
}
</style>
`);
</script>
@endpush

