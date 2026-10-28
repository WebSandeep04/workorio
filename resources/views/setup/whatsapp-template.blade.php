@extends('layouts.app')

@section('title', 'Whatsapp Template Management')
@section('page_title', 'Whatsapp Template Management')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .data-table-card .custom-table thead th {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  }

  /* Table Search & Buttons */
  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
  }

  .table-btn-outline {
    background: #fff;
    color: #434AFA;
    border: 1px solid #434AFA;
    box-shadow: none;
  }
  .table-btn-outline:hover {
    background: #434AFA;
    color: #fff;
  }

  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }

  /* Modern Card & Table */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 5px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    transform: translateY(-1px);
  }

  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem 0.5rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .btn-action-edit {
    color: white;
    background: #343AFA !important;
    border-radius: 4px;
  }

  .btn-action-delete {
    color: white;
    background: #dc3545 !important;
    border-radius: 4px;
  }

  .pagination .page-link {
    color: #434AFA;
    border: 1px solid #e0e0e0;
    padding: 0.25rem 0.5rem;
    font-size: 12px;
  }

  .pagination .page-item.active .page-link {
    background: #434AFA;
    border-color: #434AFA;
    color: white;
  }

  .loading-state, .empty-state {
    text-align: center;
    padding: 2rem;
  }

  /* Modal Styles */
  .modal-header {
      background: #434AFA !important;
      color: white;
  }
  
  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    font-size: 0.88rem;
    margin-bottom: 0.4rem;
    display: block;
  }
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.65rem;
    width: 100%;
  }

  .doc-checkbox-item {
    padding: 8px 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 6px;
    transition: all 0.2s;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .doc-checkbox-item:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
  }

  .doc-checkbox-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #434AFA;
    cursor: pointer;
  }

  .doc-badge {
    background: #e0e7ff;
    color: #3730a3;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-right: 4px;
    margin-bottom: 3px;
  }

  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search templates..." />
    </div>
    
    <!-- Manage / Upload Documents Button -->
    <button class="table-search-btn table-btn-outline" data-bs-toggle="modal" data-bs-target="#manageDocumentsModal">
      <i class="bi bi-folder2-open me-1"></i> WhatsApp Documents
    </button>

    <!-- Add Template Button -->
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#templateModal">
      <i class="bi bi-plus me-1"></i> Add Template
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="templateTable">
          <thead>
            <tr>
              <th style="width: 60px;">ID</th>
              <th style="width: 180px;">Name</th>
              <th style="max-width: 450px;">Template Text</th>
              <th style="width: 110px; text-align: center;">Attached</th>
              <th style="width: 110px; text-align: center; white-space: nowrap;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading templates...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta mt-2" id="templateRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Add / Edit Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateModalLabel">Add Whatsapp Template</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="templateForm">
        <div class="modal-body">
          <input type="hidden" id="templateId" name="id">
          
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="templateName" class="form-label-modern">Template Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="templateName" name="name" required placeholder="e.g. Service Introduction & Brochure">
            </div>

            <div class="col-md-12 mb-3">
              <label for="templateText" class="form-label-modern">Template Text <span class="text-danger">*</span></label>
              <textarea class="form-control form-control-modern" id="templateText" name="text" rows="4" required placeholder="Enter the message text..."></textarea>
              <small class="text-muted">Document links will be appended automatically after this text during message sending.</small>
            </div>

            <div class="col-md-12">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label-modern mb-0">Attached Documents (Optional)</label>
                <div class="d-flex align-items-center gap-2">
                  <input type="text" id="docSearchInput" placeholder="Filter documents..." class="form-control form-control-sm" style="max-width: 170px; font-size: 0.8rem;">
                  <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; white-space: nowrap;" id="quickUploadDocBtn">
                    <i class="bi bi-upload me-1"></i> Upload New
                  </button>
                </div>
              </div>
              <p class="small text-muted mb-2">Check the uploaded WhatsApp documents that should be selected by default when sending this template.</p>
              
              <div class="border rounded p-2" id="documentsListContainer" style="max-height: 220px; overflow-y: auto; background: #fff;">
                <!-- Populated dynamically via renderTemplateDocList() -->
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="saveTemplate" style="background: #434AFA; border: none; min-width: 130px;">Save Template</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Manage / Upload Documents Modal -->
<div class="modal fade" id="manageDocumentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center">
          <i class="bi bi-folder2-open me-2"></i> WhatsApp Documents Library
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Upload Form Card -->
        <div class="card mb-3 border-0 shadow-sm" style="background: #f8faff; border: 1px dashed #434AFA !important;">
          <div class="card-body p-3">
            <h6 class="fw-bold mb-2 text-primary" style="font-size: 0.9rem;"><i class="bi bi-cloud-arrow-up me-1"></i> Upload New Document</h6>
            <form id="uploadDocForm" enctype="multipart/form-data">
              <div class="row g-2 align-items-end">
                <div class="col-md-5">
                  <label class="form-label small fw-semibold text-muted mb-1">Document Title (Optional)</label>
                  <input type="text" class="form-control form-control-sm" name="title" id="uploadDocTitle" placeholder="e.g. Company Brochure 2026">
                </div>
                <div class="col-md-5">
                  <label class="form-label small fw-semibold text-muted mb-1">Choose File <span class="text-danger">*</span></label>
                  <input type="file" class="form-control form-control-sm" name="document_file" id="uploadDocFile" required>
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-primary btn-sm w-100" id="btnUploadDoc" style="background: #434AFA; border: none; height: 31px;">
                    <i class="bi bi-upload me-1"></i> Upload
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Document List Table -->
        <div class="table-responsive border rounded" style="max-height: 280px; overflow-y: auto;">
          <table class="table table-hover align-middle mb-0" id="manageDocsTable" style="font-size: 0.85rem;">
            <thead class="table-light sticky-top">
              <tr>
                <th>Title</th>
                <th>Format</th>
                <th>Size</th>
                <th>File Link</th>
                <th style="width: 50px; text-align: center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- Populated via loadUploadedDocs() -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Template Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>Are you sure you want to delete this template?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let deleteId = null;
let searchTimeout;
let allUploadedDocs = [];

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Load templates in the main data table
function loadTemplates(page = 1) {
    currentPage = page;
    const search = $('#search').val();
    
    $('#templateTable tbody').html('<tr><td colspan="5" class="loading-state"><i class="bi bi-arrow-repeat spin"></i></td></tr>');
    
    $.ajax({
        url: '{{ route("whatsapp-template.fetch") }}?page=' + page + '&search=' + encodeURIComponent(search),
        type: 'GET',
        success: function (data) {
            let html = '';
            if (data.data.length === 0) {
                html = '<tr><td colspan="5" class="empty-state">No Templates Found</td></tr>';
                $('#paginationLinks').empty();
            } else {
                data.data.forEach(function (temp) {
                    const docCount = (temp.documents && temp.documents.length) ? temp.documents.length : 0;
                    const docTitles = docCount > 0 ? temp.documents.map(d => d.title).join(', ') : 'No documents attached';
                    let docBadge = docCount > 0 
                        ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem; font-weight: 600;" title="${docTitles}">
                                <i class="bi bi-paperclip me-1"></i>${docCount} ${docCount === 1 ? 'Doc' : 'Docs'}
                           </span>`
                        : `<span class="text-muted small" style="font-size: 0.75rem;">0</span>`;

                    html += `
                        <tr>
                            <td>${temp.id}</td>
                            <td><strong>${temp.name}</strong></td>
                            <td style="max-width: 450px; white-space: normal; word-break: break-word;" title="${temp.text.replace(/"/g, '&quot;')}">${temp.text}</td>
                            <td style="text-align: center; white-space: nowrap;">${docBadge}</td>
                            <td style="text-align: center; white-space: nowrap;">
                                <div class="d-inline-flex align-items-center justify-content-center gap-1">
                                    <button class="btn-action btn-action-edit edit-btn" data-id="${temp.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                                    <button class="btn-action btn-action-delete delete-btn" data-id="${temp.id}" title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                buildPagination($('#paginationLinks'), data.current_page, data.last_page);
                $('#templateRangeInfo').text(`Showing ${data.from}-${data.to} from ${data.total} data`);
            }
            $('#templateTable tbody').html(html);
        }
    });
}

// Fetch all WhatsApp documents list
function fetchWhatsAppDocuments(callback) {
    $.ajax({
        url: '{{ route("whatsapp-template.documents.list") }}',
        type: 'GET',
        success: function(resp) {
            if (resp.success) {
                allUploadedDocs = resp.data;
                renderTemplateDocList();
                renderManageDocsTable();
                if (typeof callback === 'function') callback();
            }
        }
    });
}

// Render checkboxes in Add/Edit Template Modal
function renderTemplateDocList(selectedIds = []) {
    let container = $('#documentsListContainer');
    if (!allUploadedDocs || allUploadedDocs.length === 0) {
        container.html(`
            <div class="text-center py-3 text-muted small">
                <i class="bi bi-file-earmark-x fs-4 d-block mb-1"></i>
                No documents uploaded yet for WhatsApp templates.<br>
                Click <strong>"Upload New"</strong> above to upload documents.
            </div>
        `);
        return;
    }

    let html = '';
    allUploadedDocs.forEach(function(doc) {
        const isChecked = selectedIds.includes(doc.id) ? 'checked' : '';
        html += `
            <label class="doc-checkbox-item py-1 px-2 doc-filter-item">
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="document_ids[]" value="${doc.id}" class="me-2 doc-checkbox" id="modal_doc_${doc.id}" ${isChecked}>
                    <span class="doc-title-text" style="font-size: 0.85rem; font-weight: 500;">${doc.title}</span>
                </div>
                <div>
                    <span class="badge bg-light text-secondary border text-uppercase" style="font-size: 0.7rem;">${doc.file_extension || 'FILE'}</span>
                    <span class="text-muted ms-1" style="font-size: 0.75rem;">${doc.formatted_file_size}</span>
                </div>
            </label>
        `;
    });
    container.html(html);
}

// Render table inside Manage Documents Modal
function renderManageDocsTable() {
    let tbody = $('#manageDocsTable tbody');
    if (!allUploadedDocs || allUploadedDocs.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center py-3 text-muted">No documents uploaded yet.</td></tr>');
        return;
    }

    let html = '';
    allUploadedDocs.forEach(function(doc) {
        html += `
            <tr>
                <td class="fw-semibold">${doc.title}</td>
                <td><span class="badge bg-light text-dark border text-uppercase">${doc.file_extension || 'FILE'}</span></td>
                <td class="text-muted small">${doc.formatted_file_size}</td>
                <td>
                    <a href="${doc.file_url}" target="_blank" class="btn btn-link btn-sm text-primary p-0 text-decoration-none" style="font-size: 0.8rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> View / Download
                    </a>
                </td>
                <td style="text-align: center;">
                    <button class="btn btn-sm btn-outline-danger btn-delete-doc py-0 px-1" data-id="${doc.id}" title="Delete Document">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    tbody.html(html);
}

// Upload document form submit
$('#uploadDocForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let submitBtn = $('#btnUploadDoc');
    submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i>');

    $.ajax({
        url: '{{ route("whatsapp-template.documents.upload") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
            submitBtn.prop('disabled', false).html('<i class="bi bi-upload me-1"></i> Upload');
            if (resp.success) {
                $('#uploadDocForm')[0].reset();
                fetchWhatsAppDocuments();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Uploaded!',
                        text: 'Document uploaded successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    alert('Document uploaded successfully!');
                }
            }
        },
        error: function(err) {
            submitBtn.prop('disabled', false).html('<i class="bi bi-upload me-1"></i> Upload');
            let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Upload failed.';
            alert(msg);
        }
    });
});

// Delete uploaded document
$(document).on('click', '.btn-delete-doc', function() {
    const docId = $(this).data('id');
    if (!confirm('Are you sure you want to delete this document? It will also be detached from any templates using it.')) {
        return;
    }

    $.ajax({
        url: `{{ url('/whatsapp-template/documents') }}/${docId}`,
        type: 'DELETE',
        success: function(resp) {
            if (resp.success) {
                fetchWhatsAppDocuments();
                loadTemplates(currentPage);
            }
        }
    });
});

// Quick upload button in Template Modal opens Manage Documents Modal
$('#quickUploadDocBtn').on('click', function() {
    $('#manageDocumentsModal').modal('show');
});

function buildPagination($container, current, last) {
    $container.empty();
    $container.append(`<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${current - 1}">Prev</a></li>`);
    for(let i=1; i<=last; i++) {
        $container.append(`<li class="page-item ${i === current ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
    }
    $container.append(`<li class="page-item ${current === last ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${current + 1}">Next</a></li>`);
}

$(document).on('click', '.page-link', function(e) {
    e.preventDefault();
    loadTemplates($(this).data('page'));
});

$('#search').on('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => loadTemplates(1), 300);
});

// Document search inside modal
$('#docSearchInput').on('keyup', function() {
    const term = $(this).val().toLowerCase();
    $('.doc-filter-item').each(function() {
        const text = $(this).find('.doc-title-text').text().toLowerCase();
        if (text.includes(term)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$('#templateForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#templateId').val();
    const url = id ? `{{ route("whatsapp-template.update", ":id") }}`.replace(':id', id) : '{{ route("whatsapp-template.store") }}';
    
    $.ajax({
        url: url,
        type: id ? 'PUT' : 'POST',
        data: $(this).serialize(),
        success: function(resp) {
            if (resp.success) {
                $('#templateModal').modal('hide');
                loadTemplates(currentPage);
            }
        },
        error: function(err) {
            alert('Failed to save template. Please check the inputs.');
        }
    });
});

$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    
    $.get(`{{ route("whatsapp-template.edit", ":id") }}`.replace(':id', id), function(resp) {
        $('#templateId').val(resp.data.id);
        $('#templateName').val(resp.data.name);
        $('#templateText').val(resp.data.text);
        
        let docIds = [];
        if (resp.data.documents && resp.data.documents.length > 0) {
            docIds = resp.data.documents.map(d => d.id);
        }
        
        renderTemplateDocList(docIds);

        $('#templateModalLabel').text('Edit Whatsapp Template');
        $('#templateModal').modal('show');
    });
});

$(document).on('click', '.delete-btn', function() {
    deleteId = $(this).data('id');
    $('#deleteModal').modal('show');
});

$('#confirmDelete').on('click', function() {
    $.ajax({
        url: `{{ route("whatsapp-template.destroy", ":id") }}`.replace(':id', deleteId),
        type: 'DELETE',
        success: function(resp) {
            $('#deleteModal').modal('hide');
            loadTemplates(currentPage);
        }
    });
});

$('#templateModal').on('hidden.bs.modal', function() {
    $('#templateForm')[0].reset();
    $('#templateId').val('');
    renderTemplateDocList([]);
    $('#docSearchInput').val('');
    $('#templateModalLabel').text('Add Whatsapp Template');
});

$(document).ready(function() {
    fetchWhatsAppDocuments();
    loadTemplates();
});
</script>
@endpush
