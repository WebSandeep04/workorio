<?php $__env->startSection('title', 'Salary Structures'); ?>
<?php $__env->startSection('page_title', 'Salary Structures'); ?>

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
  
  .component-item { border: 1px solid #e5e7eb; border-radius: 6px; padding: 0.75rem; margin-bottom: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; background: #f9fafb; transition: all 0.2s; }
  .component-item.active { border-color: #434AFA; background: #f0f4ff; }
  .component-header { display: flex; align-items: center; justify-content: space-between; }
  .component-config { display: none; }
  .component-item.active .component-config { display: block; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search structures..." />
    </div>
    <button class="table-search-btn" id="openStructureModal">
      <i class="bi bi-plus me-1"></i>Add Structure
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="structuresTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
              <th>Components Included</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="structureTableBody">
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading structures...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="structureRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Modal -->
<div class="modal fade modal-modern" id="structureModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="structureModalLabel">
          <i class="bi bi-diagram-3 text-white"></i>
          Add Structure
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <form id="structureForm">
          <input type="hidden" id="structure_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label-modern">Structure Name <span class="text-danger">*</span></label>
              <input type="text" id="structure_name" class="form-control form-control-modern" required placeholder="e.g. Standard Developer Package">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Salary Type <span class="text-danger">*</span></label>
              <select id="salary_type" class="form-control form-select-modern">
                <option value="structured">Structured (Based on Base Pay)</option>
                <option value="fixed">Fixed</option>
              </select>
            </div>
            
            <div class="col-12 mt-4">
              <label class="form-label-modern mb-3"><i class="bi bi-list-check"></i> Assign Components</label>
              <div class="components-list" style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                <?php $__empty_1 = true; $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <div class="component-item" data-id="<?php echo e($component->id); ?>" data-calc-type="<?php echo e($component->calculation_type); ?>">
                    <div class="component-header">
                      <div class="form-check">
                        <input class="form-check-input component-checkbox" type="checkbox" id="comp_<?php echo e($component->id); ?>" value="<?php echo e($component->id); ?>">
                        <label class="form-check-label fw-bold ms-1" for="comp_<?php echo e($component->id); ?>">
                          <?php echo e($component->name); ?> 
                          <span class="badge badge-modern-info ms-2" style="font-size: 0.65rem;"><?php echo e(strtoupper($component->type)); ?></span>
                          <span class="badge badge-modern-secondary" style="font-size: 0.65rem;"><?php echo e(strtoupper($component->calculation_type)); ?></span>
                        </label>
                      </div>
                    </div>
                    <div class="component-config mt-2 ps-4">
                      <?php if($component->calculation_type === 'fixed' || $component->calculation_type === 'percentage'): ?>
                        <div class="input-group input-group-sm w-50">
                          <span class="input-group-text"><?php echo e($component->calculation_type === 'percentage' ? '%' : '₹'); ?></span>
                          <input type="number" step="0.01" class="form-control comp-val" placeholder="Value">
                        </div>
                      <?php elseif($component->calculation_type === 'formula'): ?>
                        <input type="text" class="form-control form-control-sm comp-formula" placeholder="e.g. basic * 0.4">
                      <?php else: ?>
                        <small class="text-muted">Calculated via rule configuration.</small>
                      <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <div class="alert alert-warning">No active components found. Please create salary components first.</div>
                <?php endif; ?>
              </div>
            </div>
            
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="structureError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-modern-primary w-100 justify-content-center" id="saveStructure">
          <i class="bi bi-check-circle"></i>
          Save Structure
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
    const $info = $('#structureRangeInfo');
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
  const baseUrl = "<?php echo e(route('payroll.structures.index')); ?>";
  let searchTimeout;
  
  loadStructures();

  $('.component-checkbox').on('change', function() {
      if($(this).is(':checked')) {
          $(this).closest('.component-item').addClass('active');
      } else {
          $(this).closest('.component-item').removeClass('active');
          $(this).closest('.component-item').find('input[type="number"], input[type="text"]').val('');
      }
  });

  function loadStructures(page = 1) {
    let search = $('#search').val();
    
    $('#structureTableBody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading structures...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
      url: baseUrl,
      type: 'GET',
      data: { page: page, search: search },
      success: function (data) {
        if (!data.data || data.data.length === 0) {
          $('#structureTableBody').html(`
            <tr>
              <td colspan="4" class="empty-state">
                <i class="bi bi-diagram-3"></i>
                <h5>No Structures Found</h5>
                <p>Get started by creating your first salary structure.</p>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }
        
        let rows = '';
        $.each(data.data, function (i, row) {
          const typeClass = row.salary_type === 'structured' ? 'badge-modern-success' : 'badge-modern-info';
          let componentsList = '';
          if (row.components && row.components.length > 0) {
              componentsList = row.components.map(c => `<span class="badge bg-light text-dark border me-1">${escapeHtml(c.name)}</span>`).join('');
          } else {
              componentsList = '<small class="text-muted">No components assigned</small>';
          }

          rows += `
            <tr style="animation-delay: ${i * 0.1}s;">
              <td><strong>${escapeHtml(row.name)}</strong></td>
              <td><span class="badge ${typeClass}">${escapeHtml(row.salary_type.toUpperCase())}</span></td>
              <td style="white-space: normal; max-width: 300px;">${componentsList}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn-action btn-action-edit edit-structure"
                    data-structure='${JSON.stringify(row).replace(/\'/g, "&#39;")}' title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-action-delete delete-structure" data-id="${row.id}" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        });
        $('#structureTableBody').html(rows);
        buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
        updateRangeInfo(data.from, data.to, data.total);
      },
      error: function() {
        $('#structureTableBody').html(`
          <tr>
            <td colspan="4" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i>
              Failed to load structures.
            </td>
          </tr>
        `);
      }
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadStructures(page);
  });
  
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadStructures(1), 300);
  });

  function openModal(data){
    $('#structureForm')[0].reset();
    $('#structure_id').val(data && data.id ? data.id : '');
    $('#structureModalLabel').html(data ? 
      '<i class="bi bi-pencil-square text-white"></i> Edit Structure' : 
      '<i class="bi bi-diagram-3 text-white"></i> Add Structure'
    );
    $('#structureError').addClass('d-none').text('');
    
    $('.component-item').removeClass('active');
    $('.component-checkbox').prop('checked', false);
    
    if (data) {
      $('#structure_name').val(data.name || '');
      $('#salary_type').val(data.salary_type || 'structured');
      
      if (data.components && data.components.length > 0) {
          data.components.forEach(comp => {
              const pivot = comp.pivot;
              const $item = $(`.component-item[data-id="${comp.id}"]`);
              if ($item.length) {
                  $item.find('.component-checkbox').prop('checked', true);
                  $item.addClass('active');
                  if (comp.calculation_type === 'fixed' || comp.calculation_type === 'percentage') {
                      $item.find('.comp-val').val(pivot.value);
                  } else if (comp.calculation_type === 'formula') {
                      $item.find('.comp-formula').val(pivot.formula);
                  }
              }
          });
      }
    }
    
    $('#structureModal').modal('show');
  }

  $('#openStructureModal').on('click', function(){ openModal(null); });
  
  $(document).on('click', '.edit-structure', function(){ 
    const data = $(this).data('structure');
    openModal(data); 
  });

  $('#saveStructure').on('click', function (e) {
    e.preventDefault();
    const id = $('#structure_id').val();
    
    let components = [];
    $('.component-item.active').each(function() {
        let compId = $(this).data('id');
        let calcType = $(this).data('calc-type');
        
        let value = null;
        let formula = null;
        
        if (calcType === 'fixed' || calcType === 'percentage') {
            value = $(this).find('.comp-val').val() || 0;
        } else if (calcType === 'formula') {
            formula = $(this).find('.comp-formula').val() || '';
        }
        
        components.push({ id: compId, value: value, formula: formula });
    });

    const payload = {
      _token: csrf,
      name: $('#structure_name').val().trim(),
      salary_type: $('#salary_type').val(),
      components: components
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        $('#structureModal').modal('hide');
        loadStructures();
        showAlert('success', id ? 'Structure updated successfully!' : 'Structure created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#structureError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Structure');
      });
  });

  $(document).on('click', '.delete-structure', function () {
    if (confirm('Are you sure you want to delete this structure?')) {
      $.ajax({
        url: `${baseUrl}/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: csrf },
        success: function () {
          loadStructures();
          showAlert('success', 'Structure deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete structure.');
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/structures.blade.php ENDPATH**/ ?>