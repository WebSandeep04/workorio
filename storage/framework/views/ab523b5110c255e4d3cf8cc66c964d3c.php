<?php $__env->startSection('title', 'Whatsapp Template Management'); ?>
<?php $__env->startSection('page_title', 'Whatsapp Template Management'); ?>

<?php $__env->startPush('styles'); ?>
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
    white-space: nowrap;
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
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    display: block;
  }
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem;
    width: 100%;
  }

  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search templates..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#templateModal">
      <i class="bi bi-plus me-1"></i>Add Template
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="templateTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Template Text</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="loading-state">
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

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateModalLabel">Add Whatsapp Template</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="templateForm">
        <div class="modal-body">
          <input type="hidden" id="templateId" name="id">
          <div class="mb-3">
            <label for="templateName" class="form-label-modern">Template Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="templateName" name="name" required placeholder="e.g. Welcome Message">
          </div>
          <div class="mb-3">
            <label for="templateText" class="form-label-modern">Template Text <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-modern" id="templateText" name="text" rows="5" required placeholder="Enter the message text..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100" id="saveTemplate" style="background: #434AFA; border: none;">Save Template</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;
let deleteId = null;
let searchTimeout;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadTemplates(page = 1) {
    currentPage = page;
    const search = $('#search').val();
    
    $('#templateTable tbody').html('<tr><td colspan="4" class="loading-state"><i class="bi bi-arrow-repeat spin"></i></td></tr>');
    
    $.ajax({
        url: '<?php echo e(route("whatsapp-template.fetch")); ?>?page=' + page + '&search=' + encodeURIComponent(search),
        type: 'GET',
        success: function (data) {
            let html = '';
            if (data.data.length === 0) {
                html = '<tr><td colspan="4" class="empty-state">No Templates Found</td></tr>';
                $('#paginationLinks').empty();
            } else {
                data.data.forEach(function (temp) {
                    html += `
                        <tr>
                            <td>${temp.id}</td>
                            <td><strong>${temp.name}</strong></td>
                            <td class="text-truncate" style="max-width: 300px;">${temp.text}</td>
                            <td>
                                <button class="btn-action btn-action-edit edit-btn" data-id="${temp.id}"><i class="bi bi-pencil"></i></button>
                                <button class="btn-action btn-action-delete delete-btn" data-id="${temp.id}"><i class="bi bi-trash"></i></button>
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

$('#templateForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#templateId').val();
    const url = id ? `<?php echo e(route("whatsapp-template.update", ":id")); ?>`.replace(':id', id) : '<?php echo e(route("whatsapp-template.store")); ?>';
    
    $.ajax({
        url: url,
        type: id ? 'PUT' : 'POST',
        data: $(this).serialize(),
        success: function(resp) {
            if (resp.success) {
                $('#templateModal').modal('hide');
                loadTemplates(currentPage);
            }
        }
    });
});

$(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    $.get(`<?php echo e(route("whatsapp-template.edit", ":id")); ?>`.replace(':id', id), function(resp) {
        $('#templateId').val(resp.data.id);
        $('#templateName').val(resp.data.name);
        $('#templateText').val(resp.data.text);
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
        url: `<?php echo e(route("whatsapp-template.destroy", ":id")); ?>`.replace(':id', deleteId),
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
    $('#templateModalLabel').text('Add Whatsapp Template');
});

$(document).ready(function() {
    loadTemplates();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/setup/whatsapp-template.blade.php ENDPATH**/ ?>