<?php $__env->startSection('title', 'Branches'); ?>
<?php $__env->startSection('page_title', 'Branches'); ?>

<?php $__env->startPush('styles'); ?>
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

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .spin {
    animation: spin 1s linear infinite;
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

  .badge-modern-success {
    background: #10B981;
    color: white;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 500;
  }

  .badge-modern-secondary {
    background: #6B7280;
    color: white;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    font-weight: 500;
  }
  .text-gradient-primary {
    background: linear-gradient(135deg, #434AFA, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search branches..." />
    </div>
    <button class="table-search-btn" id="openBranchModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="branchesTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Name</th>
              <th>Location</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="branchTableBody">
            <tr>
              <td colspan="6" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading branches...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="branchRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Modal -->
<div class="modal fade modal-modern" id="branchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="branchModalLabel">
          <i class="bi bi-diagram-3 text-white"></i>
          Add Branch
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <form id="branchForm">
          <input type="hidden" id="branch_id">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-modern">Code</label>
              <input type="text" id="branch_code" class="form-control form-control-modern" placeholder="Auto-generated" readonly>
            </div>
            <div class="col-md-8">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="branch_name" class="form-control form-control-modern" required placeholder="Enter branch name">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Location</label>
              <input type="text" id="branch_location" class="form-control form-control-modern" placeholder="Enter branch location">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Status</label>
              <select id="branch_status" class="form-control form-select-modern">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Contact Person</label>
              <input type="text" id="branch_contact_person" class="form-control form-control-modern" placeholder="Enter contact person name">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Contact Phone</label>
              <input type="text" id="branch_contact_phone" class="form-control form-control-modern" placeholder="Enter contact phone">
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="branch_notes" rows="3" class="form-control form-control-modern" placeholder="Enter additional notes"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="branchError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-modern-primary w-100 justify-content-center" id="saveBranch" style="background: #434AFA; color: white;">
          <i class="bi bi-check-circle"></i>
          Save Branch
        </button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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

function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#branchRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;
    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);
    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    $info.text(`Showing ${safeStart.toLocaleString('en-IN')}-${safeEnd.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

function escapeHtml(text = '') {
  return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/\'/g,'&#039;');
}

$(function () {
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const listUrl = "<?php echo e(route('branches.list')); ?>";
  const storeUrl = "<?php echo e(route('branches.store')); ?>";
  let searchTimeout;
  
  loadBranches();

  function loadBranches(page = 1) {
    let search = $('#search').val();
    
    $('#branchTableBody').html(`
      <tr>
        <td colspan="6" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading branches...</p>
        </td>
      </tr>
    `);
    
    $.get(`${listUrl}?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#branchTableBody').html(`
          <tr>
            <td colspan="6" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Branches Found</h5>
              <p>Get started by creating your first branch.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, row) {
        const statusClass = row.status === 'active' ? 'badge-modern-success' : 'badge-modern-secondary';
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong class="text-gradient-primary">${escapeHtml(row.code)}</strong></td>
            <td><strong>${escapeHtml(row.name)}</strong></td>
            <td>${escapeHtml(row.location || '-')}</td>
            <td>
              ${escapeHtml(row.contact_person || '-')}
              ${row.contact_phone ? `<br><small class="text-muted">${escapeHtml(row.contact_phone)}</small>` : ''}
            </td>
            <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-branch"
                  data-branch='${JSON.stringify(row).replace(/\'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-branch" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#branchTableBody').html(rows);

      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
    }).fail(function(){
      $('#branchTableBody').html(`
        <tr>
          <td colspan="6" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load branches. Please try again.
          </td>
        </tr>
      `);
    });
  }

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadBranches(page);
    }
  });
  
  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadBranches(1);
      }, 300);
  });
  
  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });

  function openModal(data){
    $('#branchForm')[0].reset();
    $('#branch_id').val(data && data.id ? data.id : '');
    $('#branchModalLabel').html(data ? 
      '<i class="bi bi-pencil-square text-white"></i> Edit Branch' : 
      '<i class="bi bi-diagram-3 text-white"></i> Add Branch'
    );
    $('#branchError').addClass('d-none').text('');
    
    if (data) {
      $('#branch_code').val(data.code || '');
      $('#branch_name').val(data.name || '');
      $('#branch_location').val(data.location || '');
      $('#branch_contact_person').val(data.contact_person || '');
      $('#branch_contact_phone').val(data.contact_phone || '');
      $('#branch_status').val(data.status || 'active');
      $('#branch_notes').val(data.notes || '');
    } else {
      $('#branch_status').val('active');
    }
    
    $('#branchModal').modal('show');
  }

  $('#openBranchModal').on('click', function(){ openModal(null); });
  
  $(document).on('click', '.edit-branch', function(){ 
    const data = $(this).data('branch');
    openModal(data); 
  });

  $('#saveBranch').on('click', function (e) {
    e.preventDefault();
    const id = $('#branch_id').val();
    const payload = {
      _token: csrf,
      code: $('#branch_code').val().trim(),
      name: $('#branch_name').val().trim(),
      location: $('#branch_location').val().trim(),
      contact_person: $('#branch_contact_person').val().trim(),
      contact_phone: $('#branch_contact_phone').val().trim(),
      status: $('#branch_status').val(),
      notes: $('#branch_notes').val().trim(),
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `<?php echo e(url('/branches')); ?>/${id}` : storeUrl;
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        $('#branchModal').modal('hide');
        loadBranches();
        showAlert('success', id ? 'Branch updated successfully!' : 'Branch created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#branchError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Branch');
      });
  });

  $(document).on('click', '.delete-branch', function () {
    if (confirm('Are you sure you want to delete this branch?')) {
      $.ajax({
        url: `<?php echo e(url('/branches')); ?>/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: csrf },
        success: function () {
          loadBranches();
          showAlert('success', 'Branch deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete branch.');
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/master/branches.blade.php ENDPATH**/ ?>