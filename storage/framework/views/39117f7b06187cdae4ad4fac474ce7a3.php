<?php $__env->startSection('title', 'Salary Components'); ?>
<?php $__env->startSection('page_title', 'Salary Components'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
  .table-search-btn { padding: 0.35rem 1rem; background: #434AFA; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); text-decoration: none; display: inline-flex; align-items: center; }
  .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; text-decoration: none; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .modern-card-body { padding: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-responsive { border-radius: 5px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; scrollbar-color: #434AFA #e4e7ec; }
  .data-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-responsive::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb { background: #434AFA; border-radius: 999px; }
  .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }
  .btn-action { background: transparent !important; border: none !important; padding: 0.25rem 0.5rem; color: #6c757d; transition: all 0.2s ease; cursor: pointer; }
  .btn-action-edit { color: white; background: #343AFA !important; border-radius: 4px; }
  .btn-action-delete { color: white; background: #343AFA !important; border-radius: 4px; }
  .btn-action i { font-size: 0.8rem; }
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }
  .loading-state, .empty-state { text-align: center; padding: 1rem; color: #667eea; font-size: 10px; }
  .empty-state { color: #6c757d; }
  .loading-state i, .empty-state i { font-size: 1.5rem; margin-bottom: 0.5rem; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .spin { animation: spin 1s linear infinite; }
  .modal-content { border-radius: 0px !important; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
  .modal-header { border-radius: 0px !important; background: #434AFA !important; color: white; border-bottom: none; padding: 1rem 1.5rem; }
  .modal-footer { border-top: 1px solid #f0f0f0; padding: 1rem 1.5rem; background: #fff; }
  .form-label-modern { color: #434AFA; font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem; font-size: 0.9rem; }
  .form-control-modern, .form-select-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.75rem 1rem; transition: all 0.3s ease; font-size: 0.95rem; }
  .form-control-modern:focus, .form-select-modern:focus { border-color: #434AFA; box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1); outline: none; }
  .btn-modern { padding: 0.6rem 1.5rem; border-radius: 4px; font-weight: 600; transition: all 0.3s ease; border: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; }
  .btn-modern-primary { background: #434AFA; color: white; }
  .btn-modern-primary:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); color: white; }
  .badge-modern-success { background: #10B981; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .badge-modern-secondary { background: #6B7280; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .badge-modern-info { background: #3B82F6; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .badge-modern-warning { background: #F59E0B; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .text-gradient-primary { background: linear-gradient(135deg, #434AFA, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search components..." />
    </div>
    <button class="table-search-btn" id="openComponentModal">
      <i class="bi bi-plus me-1"></i>Add Component
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="componentsTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Calculation Type</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="componentTableBody">
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading components...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="componentRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Modal -->
<div class="modal fade modal-modern" id="componentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="componentModalLabel">
          <i class="bi bi-cash-stack text-white"></i>
          Add Component
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <form id="componentForm">
          <input type="hidden" id="component_id">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="component_name" class="form-control form-control-modern" required placeholder="e.g. Basic Salary">
            </div>
            <div class="col-12">
              <label class="form-label-modern">Type <span class="text-danger">*</span></label>
              <select id="component_type" class="form-control form-select-modern">
                <option value="earning">Earning</option>
                <option value="deduction">Deduction</option>
                <option value="employer_contribution">Employer Contribution</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Calculation Type <span class="text-danger">*</span></label>
              <select id="component_calculation_type" class="form-control form-select-modern">
                <option value="fixed">Fixed Amount</option>
                <option value="percentage">Percentage (of Gross)</option>
                <option value="formula">Custom Formula</option>
                <option value="rule">Rule Based</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Status</label>
              <select id="component_status" class="form-control form-select-modern">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="componentError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-modern-primary w-100 justify-content-center" id="saveComponent" style="background: #434AFA; color: white;">
          <i class="bi bi-check-circle"></i>
          Save Component
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
    const $info = $('#componentRangeInfo');
    if (!$info.length) return;
    const safeTotal = Number.isFinite(Number(total)) ? Number(total) : 0;
    const safeStart = safeTotal === 0 ? 0 : Number(from);
    const safeEnd = safeTotal === 0 ? 0 : Number(to);
    $info.text(`Showing ${safeStart.toLocaleString('en-IN')}-${safeEnd.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

function escapeHtml(text = '') {
  return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/\'/g,'&#039;');
}

$(function () {
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const baseUrl = "<?php echo e(route('payroll.components.index')); ?>";
  let searchTimeout;
  
  loadComponents();

  function loadComponents(page = 1) {
    let search = $('#search').val();
    
    $('#componentTableBody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading components...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
      url: baseUrl,
      type: 'GET',
      data: { page: page, search: search },
      success: function (data) {
        if (!data.data || data.data.length === 0) {
          $('#componentTableBody').html(`
            <tr>
              <td colspan="5" class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Components Found</h5>
                <p>Get started by creating your first salary component.</p>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }
        
        let rows = '';
        $.each(data.data, function (i, row) {
          const statusClass = row.is_active ? 'badge-modern-success' : 'badge-modern-secondary';
          const typeClass = row.type === 'earning' ? 'badge-modern-success' : (row.type === 'deduction' ? 'badge-modern-warning' : 'badge-modern-info');
          rows += `
            <tr style="animation-delay: ${i * 0.1}s;">
              <td><strong>${escapeHtml(row.name)}</strong></td>
              <td><span class="badge ${typeClass}">${escapeHtml(row.type.replace('_', ' ').toUpperCase())}</span></td>
              <td><span class="badge badge-modern-secondary">${escapeHtml(row.calculation_type.toUpperCase())}</span></td>
              <td><span class="badge ${statusClass}">${row.is_active ? 'Active' : 'Inactive'}</span></td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn-action btn-action-edit edit-component"
                    data-component='${JSON.stringify(row).replace(/\'/g, "&#39;")}' title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-action-delete delete-component" data-id="${row.id}" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        });
        $('#componentTableBody').html(rows);
        buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
        updateRangeInfo(data.from, data.to, data.total);
      },
      error: function() {
        $('#componentTableBody').html(`
          <tr>
            <td colspan="5" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i>
              Failed to load components. Please try again.
            </td>
          </tr>
        `);
      }
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadComponents(page);
  });
  
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadComponents(1), 300);
  });

  function openModal(data){
    $('#componentForm')[0].reset();
    $('#component_id').val(data && data.id ? data.id : '');
    $('#componentModalLabel').html(data ? 
      '<i class="bi bi-pencil-square text-white"></i> Edit Component' : 
      '<i class="bi bi-cash-stack text-white"></i> Add Component'
    );
    $('#componentError').addClass('d-none').text('');
    
    if (data) {
      $('#component_name').val(data.name || '');
      $('#component_type').val(data.type || 'earning');
      $('#component_calculation_type').val(data.calculation_type || 'fixed');
      $('#component_status').val(data.is_active ? '1' : '0');
    }
    
    $('#componentModal').modal('show');
  }

  $('#openComponentModal').on('click', function(){ openModal(null); });
  
  $(document).on('click', '.edit-component', function(){ 
    const data = $(this).data('component');
    openModal(data); 
  });

  $('#saveComponent').on('click', function (e) {
    e.preventDefault();
    const id = $('#component_id').val();
    const payload = {
      _token: csrf,
      name: $('#component_name').val().trim(),
      type: $('#component_type').val(),
      calculation_type: $('#component_calculation_type').val(),
      is_active: $('#component_status').val()
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        $('#componentModal').modal('hide');
        loadComponents();
        showAlert('success', id ? 'Component updated successfully!' : 'Component created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#componentError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Component');
      });
  });

  $(document).on('click', '.delete-component', function () {
    if (confirm('Are you sure you want to delete this component?')) {
      $.ajax({
        url: `${baseUrl}/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: csrf },
        success: function () {
          loadComponents();
          showAlert('success', 'Component deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete component.');
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/components.blade.php ENDPATH**/ ?>