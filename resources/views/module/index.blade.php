@extends('layouts.app')

@section('title', 'Module Management')
@section('page_title', 'Module Management')

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

  .form-select-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    background-color: #fff;
  }
  
  .form-select-modern:focus {
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
  
  .badge-modern-info {
    background-color: rgba(67, 74, 250, 0.1);
    color: #434AFA;
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    border-radius: 4px;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search modules..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createModuleModal">
      <i class="bi bi-plus me-1"></i>Create Module
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="moduleTable">
          <thead>
            <tr>
              <th>Module Name</th>
              <th>Service</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading modules...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="moduleRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Module Modal -->
<div class="modal fade modal-modern" id="createModuleModal" tabindex="-1" aria-labelledby="createModuleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createModuleModalLabel">
          <i class="bi bi-plus-circle text-white"></i>
          Create Module
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createModuleForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label-modern">Module Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="name" name="name" required placeholder="Enter module name">
          </div>
          <div class="mb-3">
            <label for="service_id" class="form-label-modern">Service <span class="text-danger">*</span></label>
            <select class="form-select form-select-modern" id="service_id" name="service_id" required>
              <option value="">Select Service</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label-modern">Description</label>
            <textarea class="form-control form-control-modern" id="description" name="description" rows="3" placeholder="Enter description"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Create Module
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade modal-modern" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="editModuleModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Module
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editModuleForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_module_id">
          <div class="mb-3">
            <label for="edit_name" class="form-label-modern">Module Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_name" required>
          </div>
          <div class="mb-3">
            <label for="edit_service_id" class="form-label-modern">Service <span class="text-danger">*</span></label>
            <select class="form-select form-select-modern" id="edit_service_id" required>
              <option value="">Select Service</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label-modern">Description</label>
            <textarea class="form-control form-control-modern" id="edit_description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Update Module
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-modern" id="deleteModuleModal" tabindex="-1" aria-labelledby="deleteModuleModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="deleteModuleModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this module?</p>
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
function showAlert(type, message) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const alertHtml = `
    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  $('body').append(alertHtml);
  setTimeout(() => $('.alert').fadeOut(), 3000);
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
    const $info = $('#moduleRangeInfo');
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

$(function () {
  let searchTimeout;
  loadModules();
  loadProjects();

  function loadModules(page = 1) {
    let search = $('#search').val();

    $('#moduleTable tbody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading modules...</p>
        </td>
      </tr>
    `);
    
    $.get(`{{ route('module.fetch') }}?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#moduleTable tbody').html(`
          <tr>
            <td colspan="4" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Modules Found</h5>
              <p>Get started by creating your first module.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, module) {
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${module.name}</strong></td>
            <td>${module.service ? module.service.name : '-'}</td>
            <td>${module.description || '-'}</td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit editBtn" data-id="${module.id}" 
                        data-name="${module.name}" data-project-id="${module.service_id}" 
                        data-description="${module.description || ''}" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${module.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#moduleTable tbody').html(rows);

      // Simple pagination
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);

    }).fail(function() {
      $('#moduleTable tbody').html(`
        <tr>
          <td colspan="4" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load modules. Please try again.
          </td>
        </tr>
      `);
    });
  }

  function loadProjects() {
    $.get("{{ route('service.fetch') }}", function (data) {
      let options = '<option value="">Select Service</option>';
      let projects = data.data ? data.data : data; // Handle both paginated and non-paginated

      $.each(projects, function (i, project) {
        options += `<option value="${project.id}">${project.name}</option>`;
      });
      $('#service_id, #edit_service_id').html(options);
    });
  }

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadModules(page);
    }
  });

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadModules(1);
      }, 300);
  });

  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });

  $('#createModuleForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');
    
    $.post("{{ route('module.store') }}", {
      name: $('#name').val(),
      service_id: $('#service_id').val(),
      description: $('#description').val(),
      _token: '{{ csrf_token() }}'
    }, function (response) {
      if (response.success) {
        $('#createModuleModal').modal('hide');
        $('#createModuleForm')[0].reset();
        loadModules();
        showAlert('success', 'Module created successfully.');
      }
    }).fail(function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.errors) {
        const errors = Object.values(xhr.responseJSON.errors).join("\n");
        showAlert('error', errors);
      } else {
        showAlert('error', 'Error creating module.');
      }
    }).always(function() {
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Create Module');
    });
  });

  $(document).on('click', '.editBtn', function () {
    $('#edit_module_id').val($(this).data('id'));
    $('#edit_name').val($(this).data('name'));
    $('#edit_service_id').val($(this).data('project-id'));
    $('#edit_description').val($(this).data('description'));
    $('#editModuleModal').modal('show');
  });

  $('#editModuleForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
    
    let id = $('#edit_module_id').val();
    $.ajax({
      url: `/module/${id}`,
      type: 'PUT',
      data: {
        name: $('#edit_name').val(),
        service_id: $('#edit_service_id').val(),
        description: $('#edit_description').val(),
        _token: '{{ csrf_token() }}'
      },
      success: function (response) {
        if (response.success) {
          $('#editModuleModal').modal('hide');
          loadModules();
          showAlert('success', 'Module updated successfully.');
        }
      },
      error: function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = Object.values(xhr.responseJSON.errors).join("\n");
          showAlert('error', errors);
        } else {
          showAlert('error', 'Error updating module.');
        }
      },
      always: function() {
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Module');
      }
    });
  });

  $(document).on('click', '.deleteBtn', function () {
    $('#confirmDelete').data('id', $(this).data('id'));
    $('#deleteModuleModal').modal('show');
  });

  $('#confirmDelete').click(function() {
      const id = $(this).data('id');
      const $btn = $(this);
      $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');

      $.ajax({
        url: `/module/${id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function (response) {
          if (response.success) {
            $('#deleteModuleModal').modal('hide');
            loadModules();
            showAlert('success', 'Module deleted successfully.');
          }
        },
        error: function () {
          showAlert('error', 'Error deleting module.');
        },
        always: function() {
          $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
        }
      });
  });
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
