<?php $__env->startSection('title', 'Departments'); ?>
<?php $__env->startSection('page_title', 'Departments'); ?>

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
      <input type="text" id="search" placeholder="Search departments..." />
    </div>
    
    <div style="width: 200px;">
      <select id="filterBranch" class="form-select form-select-modern p-2" style="font-size: 0.85rem; height: auto;">
        <option value="">All Branches</option>
        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <button class="table-search-btn" id="openDepartmentModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="departmentsTable">
          <thead>
            <tr>
              <th>Branch</th>
              <th>Code</th>
              <th>Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="departmentTableBody">
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading departments...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="departmentRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<!-- Modal -->
<div class="modal fade modal-modern" id="departmentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="departmentModalLabel">
          <i class="bi bi-diagram-3 text-white"></i>
          Add Department
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <form id="departmentForm">
          <input type="hidden" id="department_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-modern">Branch <span class="text-danger">*</span></label>
              <select id="department_branch_id" class="form-select form-select-modern w-100" required></select>
            </div>
            <div class="col-md-3">
              <label class="form-label-modern">Code</label>
              <input type="text" id="department_code" class="form-control form-control-modern w-100" placeholder="Auto-generated" readonly>
            </div>
            <div class="col-md-3">
              <label class="form-label-modern">Status</label>
              <select id="department_status" class="form-select form-select-modern w-100">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="department_name" class="form-control form-control-modern w-100" required placeholder="Enter department name">
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="department_notes" rows="3" class="form-control form-control-modern w-100" placeholder="Enter additional notes"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="departmentError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-modern-primary w-100 justify-content-center" id="saveDepartment" style="background: #434AFA; color: white;">
          <i class="bi bi-check-circle"></i>
          Save Department
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

function updateRangeInfo(from, to, total) {
    const $info = $('#departmentRangeInfo');
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
  const listUrl = "<?php echo e(route('departments.list')); ?>";
  const storeUrl = "<?php echo e(route('departments.store')); ?>";
  const branches = <?php echo json_encode($branches, 15, 512) ?>;
  let searchTimeout;
  
  loadDepartments();

  function loadDepartments() {
    let search = $('#search').val() || '';
    let branch_id = $('#filterBranch').val() || '';
    
    $('#departmentTableBody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading departments...</p>
        </td>
      </tr>
    `);
    
    $.get(`${listUrl}?search=${search}&branch_id=${branch_id}`, function (data) {
      if (!data || data.length === 0) {
        $('#departmentTableBody').html(`
          <tr>
            <td colspan="5" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Departments Found</h5>
              <p>Get started by creating your first department.</p>
            </td>
          </tr>
        `);
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data, function (i, row) {
        const statusClass = row.status === 'active' ? 'badge-modern-success' : 'badge-modern-secondary';
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${escapeHtml(row.branch ? row.branch.name : '-')}</strong></td>
            <td><strong class="text-gradient-primary">${escapeHtml(row.code)}</strong></td>
            <td><strong>${escapeHtml(row.name)}</strong></td>
            <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-dept"
                  data-dept='${JSON.stringify(row).replace(/\'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-dept" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#departmentTableBody').html(rows);
      updateRangeInfo(1, data.length, data.length);
    }).fail(function(){
      $('#departmentTableBody').html(`
        <tr>
          <td colspan="5" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load departments. Please try again.
          </td>
        </tr>
      `);
    });
  }

  // Filter input
  $('#filterBranch').on('change', function() {
      loadDepartments();
  });

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadDepartments();
      }, 300);
  });

  function populateBranchSelect(selectId, selected) {
    const select = $(selectId);
    select.empty();
    select.append('<option value="">Select Branch</option>');
    branches.forEach(function(branch){
      select.append(`<option value="${branch.id}" ${selected == branch.id ? 'selected' : ''}>${escapeHtml(branch.name)}</option>`);
    });
  }

  function openModal(data){
    $('#departmentForm')[0].reset();
    populateBranchSelect('#department_branch_id', data && data.branch_id ? data.branch_id : null);
    $('#department_id').val(data && data.id ? data.id : '');
    $('#departmentModalLabel').html(data ? 
      '<i class="bi bi-pencil-square text-white"></i> Edit Department' : 
      '<i class="bi bi-diagram-3 text-white"></i> Add Department'
    );
    $('#departmentError').addClass('d-none').text('');
    
    if (data) {
      $('#department_code').val(data.code || '');
      $('#department_name').val(data.name || '');
      $('#department_status').val(data.status || 'active');
      $('#department_notes').val(data.notes || '');
    } else {
      $('#department_status').val('active');
    }
    
    $('#departmentModal').modal('show');
  }

  $('#openDepartmentModal').on('click', function(){ openModal(null); });
  
  $(document).on('click', '.edit-dept', function(){ 
    const data = $(this).data('dept');
    openModal(data); 
  });

  $('#saveDepartment').on('click', function (e) {
    e.preventDefault();
    const id = $('#department_id').val();
    const payload = {
      _token: csrf,
      branch_id: $('#department_branch_id').val(),
      code: $('#department_code').val().trim(),
      name: $('#department_name').val().trim(),
      status: $('#department_status').val(),
      notes: $('#department_notes').val().trim(),
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `<?php echo e(url('/departments')); ?>/${id}` : storeUrl;
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        $('#departmentModal').modal('hide');
        loadDepartments();
        showAlert('success', id ? 'Department updated successfully!' : 'Department created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#departmentError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Department');
      });
  });

  $(document).on('click', '.delete-dept', function () {
    if (confirm('Are you sure you want to delete this department?')) {
      $.ajax({
        url: `<?php echo e(url('/departments')); ?>/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: csrf },
        success: function () {
          loadDepartments();
          showAlert('success', 'Department deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete department.');
        }
      });
    }
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/master/departments.blade.php ENDPATH**/ ?>