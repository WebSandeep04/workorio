@extends('layouts.app')

@section('title', 'Entry Types Management')
@section('page_title', 'Entry Types Management')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
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
  
  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }
  
  .data-table-card .table-responsive {
    scrollbar-color: #434AFA #e4e7ec;
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
    white-space: nowrap;
    font-family: Montserrat;
  }
  
  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }
  
  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }
  
  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }
  
  /* Range Info */
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
  
  /* Buttons in Table */
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
    background: #343AFA !important;
    border-radius: 4px;
  }
  
  .btn-action i {
    font-size: 0.8rem;
  }
  
  /* Pagination */
  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }
  
  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }
  
  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }

  .loading-state, .empty-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
    font-size: 10px;
  }
  
  .empty-state {
    color: #6c757d;
  }
  
  .loading-state i, .empty-state i {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
  }

  @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-right: 0;
    }
  
    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-btn {
      width: auto;
      padding: 0.35rem 0.75rem;
    }
  
    .table-search-field {
        width: 100%;
    }
  }

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 1rem 1.5rem;
  }
  
  .modal-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 1rem 1.5rem;
      background: #fff;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
  }
  
  .form-control-modern, .form-select-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-control-modern:focus, .form-select-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  
  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .btn-modern-danger {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-danger:hover {
    background: #3538d4;
    color: white;
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
      <input type="text" id="search" placeholder="Search entry types..." />
    </div>
    <button class="table-search-btn" onclick="openCreateModal()">
      <i class="bi bi-plus me-1"></i>New Type
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="entryTypesTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Working Hours</th>
              <th>Description</th>
              <th>Created At</th>
              <th style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody id="entryTypesTableBody">
            <tr>
              <td colspan="6" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading entry types...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="entryTypeRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
  
  <div id="alertContainer"></div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade modal-modern" id="entryTypeModal" tabindex="-1" aria-labelledby="entryTypeModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="entryTypeModalLabel">
          <i class="bi bi-plus-circle text-white"></i>
          Create New Entry Type
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="entryTypeForm">
        <div class="modal-body pt-4 pb-4">
          <input type="hidden" id="entryTypeId" name="id">
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label-modern">Entry Type Name <span class="text-danger">*</span></label>
                <input type="text" 
                       class="form-control form-control-modern" 
                       id="name" 
                       name="name" 
                       placeholder="e.g., Full Day, Half Day"
                       required>
                <div class="invalid-feedback" id="nameError"></div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="mb-3">
                <label for="working_hours" class="form-label-modern">Working Hours <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="number" 
                         class="form-control form-control-modern" 
                         id="working_hours" 
                         name="working_hours" 
                         min="0" 
                         max="24" 
                         placeholder="8"
                         required>
                  <span class="input-group-text bg-light border-start-0">hours</span>
                </div>
                <div class="invalid-feedback" id="working_hoursError"></div>
                <small class="form-text text-muted">
                  Max 24 hours.
                </small>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label-modern">Description</label>
            <textarea class="form-control form-control-modern" 
                      id="description" 
                      name="description" 
                      rows="3" 
                      placeholder="Optional description..."></textarea>
            <div class="invalid-feedback" id="descriptionError"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-danger w-100 justify-content-center" id="submitBtn">
            <i class="bi bi-check-circle"></i>
            <span>Create Entry Type</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-modern" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete <strong id="deleteEntryTypeName"></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-modern btn-modern-danger" id="confirmDeleteBtn" style="background: #dc3545; color: white;">
          <i class="bi bi-trash"></i>
          Delete
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let currentEntryTypeId = null;
let deleteEntryTypeId = null;
let searchTimeout;

// Build compact pagination
function buildSimplePagination($container, current, last) {
    $container.empty();
    // Prev
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    // Current
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    // Next
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#entryTypeRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    const formattedStart = safeStart.toLocaleString('en-IN');
    const formattedEnd = safeEnd.toLocaleString('en-IN');
    const formattedTotal = safeTotal.toLocaleString('en-IN');

    $info.text(`Showing ${formattedStart}-${formattedEnd} from ${formattedTotal} data`);
}

$(document).ready(function() {
    loadEntryTypes();
    
    $('#entryTypeForm').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        deleteEntryType();
    });
    
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const hours = $(this).data('hours');
        const description = $(this).data('description');
        openEditModal(id, name, hours, description);
    });
    
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        openDeleteModal(id, name);
    });
    
    $('#working_hours').on('input', function() {
        let value = parseInt($(this).val());
        if (value < 0) $(this).val(0);
        if (value > 24) $(this).val(24);
    });
    
    /* Search */
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadEntryTypes(1);
        }, 300);
    });
    
    /* Pagination Click */
    $(document).on('click', '#paginationLinks .page-link', function (e) {
      e.preventDefault();
      const page = $(this).data('page');
      if (page) {
        loadEntryTypes(page);
      }
    });
});

function loadEntryTypes(page = 1) {
    let search = $('#search').val() || '';
    
    $('#entryTypesTableBody').html(`
      <tr>
        <td colspan="6" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading entry types...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
        url: `{{ route("entry-type.fetch") }}?page=${page}&search=${search}`,
        method: 'GET',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                displayEntryTypes(response.data);
                buildSimplePagination($('#paginationLinks'), response.current_page || 1, response.last_page || 1);
                updateRangeInfo(response.from, response.to, response.total);
            } else {
                 $('#entryTypesTableBody').html(`
                    <tr>
                        <td colspan="6" class="empty-state">
                          <i class="bi bi-inbox"></i>
                          <h5>No Entry Types Found</h5>
                          <p>Get started by creating your first entry type.</p>
                        </td>
                    </tr>
                `);
                $('#paginationLinks').empty();
                updateRangeInfo(0, 0, 0);
            }
        },
        error: function(xhr) {
            let errorMessage = 'Failed to load entry types. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            }
            $('#entryTypesTableBody').html(`
              <tr>
                <td colspan="6" class="text-danger text-center py-4">
                  <i class="bi bi-exclamation-triangle"></i>
                  ${errorMessage}
                </td>
              </tr>
            `);
            showAlert('error', errorMessage);
        }
    });
}

function displayEntryTypes(entryTypes) {
    const tbody = $('#entryTypesTableBody');
    let html = '';
    entryTypes.forEach(function(entryType, index) {
        const description = entryType.description ? escapeHtml(entryType.description) : '<span class="text-muted">No description</span>';
        html += `
            <tr style="animation-delay: ${index * 0.1}s;">
                <td><strong>#${entryType.id}</strong></td>
                <td><strong>${escapeHtml(entryType.name)}</strong></td>
                <td>${entryType.working_hours} hours</td>
                <td class="text-wrap" style="max-width: 250px;">${description}</td>
                <td>${formatDate(entryType.created_at)}</td>
                <td>
                  <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn-action btn-action-edit edit-btn" 
                            data-id="${entryType.id}" 
                            data-name="${escapeHtml(entryType.name)}" 
                            data-hours="${entryType.working_hours}" 
                            data-description="${escapeHtml(entryType.description || '')}"
                            title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn-action btn-action-delete delete-btn" 
                            data-id="${entryType.id}" 
                            data-name="${escapeHtml(entryType.name)}"
                            title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
            </tr>
        `;
    });
    tbody.html(html);
}

function openCreateModal() {
    currentEntryTypeId = null;
    $('#entryTypeModalLabel').html('<i class="bi bi-plus-circle text-white"></i> Create New Entry Type');
    $('#submitBtn').html('<i class="bi bi-check-circle"></i> <span>Create Entry Type</span>');
    $('#entryTypeForm')[0].reset();
    clearErrors();
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('entryTypeModal'));
        modal.show();
    } catch (error) {
        $('#entryTypeModal').modal('show');
    }
}

function openEditModal(id, name, workingHours, description) {
    currentEntryTypeId = id;
    $('#entryTypeModalLabel').html('<i class="bi bi-pencil-square text-white"></i> Edit Entry Type');
    $('#submitBtn').html('<i class="bi bi-check-circle"></i> <span>Update Entry Type</span>');
    
    $('#entryTypeId').val(id);
    $('#name').val(name);
    $('#working_hours').val(workingHours);
    $('#description').val(description);
    clearErrors();
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('entryTypeModal'));
        modal.show();
    } catch (error) {
        $('#entryTypeModal').modal('show');
    }
}

function openDeleteModal(id, name) {
    deleteEntryTypeId = id;
    $('#deleteEntryTypeName').text(name);
    
    try {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } catch (error) {
        $('#deleteModal').modal('show');
    }
}

function submitForm() {
    const $btn = $('#submitBtn');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> <span>Processing...</span>');
    
    const formData = {
        name: $('#name').val(),
        working_hours: $('#working_hours').val(),
        description: $('#description').val(),
        _token: '{{ csrf_token() }}'
    };
    
    const url = currentEntryTypeId 
        ? `/entry-type/${currentEntryTypeId}`
        : '{{ route("entry-type.store") }}';
    
    const method = currentEntryTypeId ? 'PUT' : 'POST';
    
    if (method === 'PUT') {
        formData._method = 'PUT';
    }
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#entryTypeModal').modal('hide');
                loadEntryTypes();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                displayErrors(xhr.responseJSON.errors);
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        },
        always: function() {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

function deleteEntryType() {
    const $btn = $('#confirmDeleteBtn');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> <span>Deleting...</span>');
    
    $.ajax({
        url: `/entry-type/${deleteEntryTypeId}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                $('#deleteModal').modal('hide');
                loadEntryTypes();
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                showAlert('error', xhr.responseJSON.message);
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        },
        always: function() {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

function displayErrors(errors) {
    clearErrors();
    
    Object.keys(errors).forEach(function(field) {
        const errorElement = $(`#${field}Error`);
        const inputElement = $(`#${field}`);
        
        if (errorElement.length && inputElement.length) {
            errorElement.text(errors[field][0]);
            inputElement.addClass('is-invalid');
        }
    });
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

function showAlert(type, message) {
    // Reusing the same alert function styling as customer project for consistency
    const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    setTimeout(() => $('.alert').fadeOut(), 3000);
}

function escapeHtml(text) {
  if (text == null) return '';
  return text.toString()
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    if (!dateString) return '-';
    // Use substring for YYYY-MM-DD
    return dateString.substring(0, 10);
}
</script>
@endpush
