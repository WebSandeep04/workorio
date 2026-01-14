

<?php $__env->startSection('title', 'Departments'); ?>
<?php $__env->startSection('page_title', 'Departments'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .page-header {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    padding: 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(79, 172, 254, 0.3);
    animation: fadeInDown 0.6s ease-out;
  }
  
  .page-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
  <!-- Page Header -->
  <div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <h2>
        <i class="bi bi-diagram-2"></i>
        Departments Management
      </h2>
      <button class="btn-modern btn-modern-success" id="openDepartmentModal">
        <i class="bi bi-plus-circle"></i>
        Add New Department
      </button>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="filter-section mb-4">
    <label class="form-label-modern mb-0">Filter by Branch:</label>
    <select id="filterBranch" class="form-select form-select-modern">
      <option value="">All Branches</option>
      <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <!-- Main Card -->
  <div class="modern-card">
    <div class="modern-card-body">
      <div class="modern-table-wrapper">
        <table class="modern-table">
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
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading departments...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="departmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="departmentModalLabel">
          <i class="bi bi-diagram-2"></i>
          Add Department
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="departmentForm">
          <input type="hidden" id="department_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-modern">Branch <span class="text-danger">*</span></label>
              <select id="department_branch_id" class="form-select form-select-modern" required></select>
            </div>
            <div class="col-md-3">
              <label class="form-label-modern">Code</label>
              <input type="text" id="department_code" class="form-control form-control-modern" placeholder="Auto-generated" readonly>
              <small class="text-muted">Auto-generated after save.</small>
            </div>
            <div class="col-md-3">
              <label class="form-label-modern">Status</label>
              <select id="department_status" class="form-select form-select-modern">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="department_name" class="form-control form-control-modern" required>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="department_notes" rows="3" class="form-control form-control-modern"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="departmentError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-success" id="saveDepartment">
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
(function () {
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const listUrl = "<?php echo e(route('departments.list')); ?>";
  const storeUrl = "<?php echo e(route('departments.store')); ?>";
  const branches = <?php echo json_encode($branches, 15, 512) ?>;

  function escapeHtml(text = '') {
    return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  function loadDepartments() {
    $('#departmentTableBody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading departments...</p>
        </td>
      </tr>
    `);
    
    $.get(listUrl, { branch_id: $('#filterBranch').val() })
      .done(function(rows){
        if (!rows || rows.length === 0) {
          $('#departmentTableBody').html(`
            <tr>
              <td colspan="5" class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Departments Found</h5>
                <p>Get started by creating your first department.</p>
              </td>
            </tr>
          `);
          return;
        }
        
        let html = '';
        rows.forEach(function(row, index){
          const statusClass = row.status === 'active' ? 'badge-modern-success' : 'badge-modern-secondary';
          html += `
            <tr style="animation-delay: ${index * 0.1}s;">
              <td><strong>${escapeHtml((row.branch && row.branch.name) || '-')}</strong></td>
              <td><strong class="text-gradient-primary">${escapeHtml(row.code)}</strong></td>
              <td><strong>${escapeHtml(row.name)}</strong></td>
              <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn-action btn-action-edit edit-dept" data-dept='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
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
        $('#departmentTableBody').html(html);
      })
      .fail(function(){
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

  function populateBranchSelect(selectId, selected) {
    const select = $(selectId);
    select.empty();
    select.append('<option value="">Select Branch</option>');
    branches.forEach(function(branch){
      select.append(`<option value="${branch.id}" ${selected == branch.id ? 'selected' : ''}>${escapeHtml(branch.name)}</option>`);
    });
  }

  function openModal(data) {
    $('#departmentForm')[0].reset();
    populateBranchSelect('#department_branch_id', data && data.branch_id ? data.branch_id : null);
    $('#department_id').val(data && data.id ? data.id : '');
    $('#departmentModalLabel').html(data ? 
      '<i class="bi bi-pencil-square"></i> Edit Department' : 
      '<i class="bi bi-plus-circle"></i> Add Department'
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
    
    new bootstrap.Modal('#departmentModal').show();
  }

  function saveDepartment() {
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
    
    const $btn = $('#saveDepartment');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('departmentModal')).hide();
        loadDepartments();
        showNotification('success', id ? 'Department updated successfully!' : 'Department created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#departmentError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Department');
      });
  }

  function deleteDepartment(id) {
    if (!confirm('Are you sure you want to delete this department?')) return;
    
    $.ajax({
      url: `<?php echo e(url('/departments')); ?>/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadDepartments();
      showNotification('success', 'Department deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete department. Please try again.');
    });
  }

  function showNotification(type, message) {
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

  // Event bindings
  $('#openDepartmentModal').on('click', function(){ openModal(null); });
  $('#saveDepartment').on('click', saveDepartment);
  $(document).on('click', '.edit-dept', function(){ 
    const data = $(this).data('dept');
    openModal(data); 
  });
  $(document).on('click', '.delete-dept', function(){ 
    deleteDepartment($(this).data('id')); 
  });
  $('#filterBranch').on('change', loadDepartments);
  
  $(document).ready(loadDepartments);
})();
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/master/departments.blade.php ENDPATH**/ ?>