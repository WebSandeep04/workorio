@extends('layouts.app')

@section('title', 'Calling Types Management')
@section('page_title', 'Calling Types Management')

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
    color: #667eea;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
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
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-control-modern:focus {
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
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
    color: white;
  }

  .btn-modern-danger {
    background: #dc3545;
    color: white;
  }
  
  .btn-modern-danger:hover {
    background: #b02a37;
    color: white;
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
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search calling type..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#callingTypeModal">
      <i class="bi bi-plus me-1"></i>Add Calling Type
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="callingTypeTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading calling types...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="callingTypeRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Calling Type Modal -->
<div class="modal fade modal-modern" id="callingTypeModal" tabindex="-1" aria-labelledby="callingTypeModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="callingTypeModalLabel">
          <i class="bi bi-plus-circle text-white"></i>
          Add Calling Type
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="callingTypeForm">
        <div class="modal-body pt-4 pb-4">
          <input type="hidden" id="callingTypeId" name="id">
          <div class="mb-2">
            <label for="callingTypeName" class="form-label-modern">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="callingTypeName" name="name" required placeholder="Enter calling type name">
            <div class="invalid-feedback"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" id="saveCallingType">
            <i class="bi bi-check-circle"></i>
            Save
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
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="deleteModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this calling type?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-modern btn-modern-danger" id="confirmDelete">
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
let currentPage = 1;
let deleteId = null;
let searchTimeout;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Format date function
function formatDateOnly(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const t = str.indexOf('T');
    if (t > 0) return str.slice(0, t);
    const d = new Date(str);
    if (!isNaN(d.getTime())) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    return str.length >= 10 ? str.slice(0, 10) : str;
}

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
    const $info = $('#callingTypeRangeInfo');
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

// Load calling types with pagination
function loadCallingTypes(page = 1) {
    currentPage = page;
    const search = $('#search').val();
    
    $('#callingTypeTable tbody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading calling types...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
        url: '{{ route("calling-type.fetch") }}?page=' + page + '&search=' + encodeURIComponent(search),
        type: 'GET',
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = `
                  <tr>
                    <td colspan="4" class="empty-state">
                      <i class="bi bi-inbox"></i>
                      <h5>No Calling Types Found</h5>
                      <p>Get started by creating your first calling type.</p>
                    </td>
                  </tr>
                `;
                $('#paginationLinks').empty();
                updateRangeInfo(0, 0, 0);
            } else {
                data.data.forEach(function (callingType, index) {
                    html += `
                        <tr style="animation-delay: ${index * 0.1}s;">
                            <td><strong>${callingType.id}</strong></td>
                            <td><strong>${callingType.name}</strong></td>
                            <td>${formatDateOnly(callingType.created_at)}</td>
                            <td>
                              <div class="d-flex gap-2 justify-content-center">
                                <button class="btn-action btn-action-edit edit-btn" data-id="${callingType.id}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action btn-action-delete delete-btn" data-id="${callingType.id}" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                              </div>
                            </td>
                        </tr>
                    `;
                });
                buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
                updateRangeInfo(data.from, data.to, data.total);
            }

            $('#callingTypeTable tbody').html(html);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#callingTypeTable tbody').html(`
              <tr>
                <td colspan="4" class="text-danger text-center py-4">
                  <i class="bi bi-exclamation-triangle"></i>
                  Failed to load calling types. Please try again.
                </td>
              </tr>
            `);
            showAlert('error', 'Something went wrong while loading calling types.');
        }
    });
}

// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    $('body').append(alertHtml);
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 3000);
}

// Pagination click handlers
$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        loadCallingTypes(page);
    }
});

// Search input
$('#search').on('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        loadCallingTypes(1);
    }, 300);
});

// Add/Edit Form Submit
$('#callingTypeForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        name: $('#callingTypeName').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
    };

    const id = $('#callingTypeId').val();
    const url = id ? `{{ route("calling-type.update", ":id") }}`.replace(':id', id) : '{{ route("calling-type.store") }}';
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    const $btn = $('#saveCallingType');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    $.ajax({
        url: url,
        type: id ? 'PUT' : 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                $('#callingTypeModal').modal('hide');
                $('#callingTypeForm')[0].reset();
                $('#callingTypeId').val('');
                
                loadCallingTypes(currentPage);
                showAlert('success', response.message);
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                for (let field in errors) {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(errors[field][0]);
                }
            } else {
                showAlert('error', 'Something went wrong. Please try again.');
            }
        },
        always: function() {
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save');
        }
    });
});

// Edit Button Click
$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    
    $.ajax({
        url: `{{ route("calling-type.edit", ":id") }}`.replace(':id', id),
        type: 'GET',
        success: function(response) {
            $('#callingTypeId').val(response.data.id);
            $('#callingTypeName').val(response.data.name);
            $('#callingTypeModalLabel').html('<i class="bi bi-pencil-square text-white"></i> Edit Calling Type');
            $('#callingTypeModal').modal('show');
        },
        error: function() {
            showAlert('error', 'Failed to load calling type data.');
        }
    });
});

// Delete Button Click
$(document).on('click', '.delete-btn', function() {
    deleteId = $(this).data('id');
    $('#deleteModal').modal('show');
});

// Confirm Delete
$('#confirmDelete').on('click', function() {
    if (deleteId) {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');
        
        $.ajax({
            url: `{{ route("calling-type.destroy", ":id") }}`.replace(':id', deleteId),
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    loadCallingTypes(currentPage);
                    showAlert('success', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to delete calling type.');
            },
            always: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
            }
        });
    }
});

// Reset form when modal is hidden
$('#callingTypeModal').on('hidden.bs.modal', function() {
    $('#callingTypeForm')[0].reset();
    $('#callingTypeId').val('');
    $('#callingTypeModalLabel').html('<i class="bi bi-plus-circle text-white"></i> Add Calling Type');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
});

// Close modals when clicking outside
$(document).on('click', function (e) {
    if ($(e.target).hasClass('modal')) {
        $('.modal').modal('hide');
    }
});

// Initialize on page load
$(document).ready(function() {
    loadCallingTypes();
});
</script>
<style>
  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush
