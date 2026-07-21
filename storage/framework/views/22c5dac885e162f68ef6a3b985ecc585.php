<?php $__env->startSection('title', 'Payroll Processing'); ?>
<?php $__env->startSection('page_title', 'Payroll Processing'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .filter-dropdown { background: #fff; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; font-size: 0.85rem; outline: none; color: #111827; min-width: 120px; flex: 1; }
  .table-search-btn { padding: 0.35rem 1rem; background: #434AFA; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; }
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
  .btn-action-delete { color: white; background: #DC2626 !important; border-radius: 4px; font-size: 0.75rem; padding: 0.35rem 0.7rem; }
  .btn-action-view { color: white; background: #343AFA !important; border-radius: 4px; font-size: 0.75rem; padding: 0.35rem 0.7rem; }
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }
  .loading-state, .empty-state { text-align: center; padding: 1rem; color: #667eea; font-size: 10px; }
  .empty-state { color: #6c757d; }
  .loading-state i, .empty-state i { font-size: 1.5rem; margin-bottom: 0.5rem; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .spin { animation: spin 1s linear infinite; }
  .badge-modern-success { background: #10B981; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .badge-modern-secondary { background: #6B7280; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  .badge-modern-warning { background: #F59E0B; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; }
  
  .modal-content { border-radius: 0px !important; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden; }
  .modal-header { border-radius: 0px !important; background: #434AFA !important; color: white; border-bottom: none; padding: 1rem 1.5rem; }
  .modal-footer { border-top: 1px solid #f0f0f0; padding: 1rem 1.5rem; background: #fff; }
  .form-label-modern { color: #434AFA; font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.25rem; font-size: 0.9rem; }
  .form-select-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.75rem 1rem; transition: all 0.3s ease; font-size: 0.95rem; }
  .form-select-modern:focus { border-color: #434AFA; box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1); outline: none; }
  .btn-modern { padding: 0.6rem 1.5rem; border-radius: 4px; font-weight: 600; transition: all 0.3s ease; border: none; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; }
  .btn-modern-primary { background: #434AFA; color: white; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <select id="filterYear" class="filter-dropdown">
      <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($yr); ?>" <?php echo e(date('Y') == $yr ? 'selected' : ''); ?>><?php echo e($yr); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    
    <button class="table-search-btn" id="openGenerateModal">
      <i class="bi bi-play-circle me-1"></i>Generate Payroll
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="payrollTable">
          <thead>
            <tr>
              <th>Period</th>
              <th>Employees Processed</th>
              <th>Status</th>
              <th>Generated On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="payrollTableBody">
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading payroll history...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="payrollRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Generate Modal -->
<div class="modal fade modal-modern" id="generateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
          <i class="bi bi-play-circle text-white"></i>
          Generate Payroll
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <form id="generateForm">
          <div class="row g-3">
            <div class="col-12">
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> This will calculate salary for all employees with <strong>locked</strong> attendance for the selected period.
              </div>
            </div>
            <div class="col-6">
              <label class="form-label-modern">Month <span class="text-danger">*</span></label>
              <select id="gen_month" class="form-control form-select-modern">
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($num); ?>" <?php echo e(date('n') == $num ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label-modern">Year <span class="text-danger">*</span></label>
              <select id="gen_year" class="form-control form-select-modern">
                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($yr); ?>" <?php echo e(date('Y') == $yr ? 'selected' : ''); ?>><?php echo e($yr); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="generateError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-modern-primary w-100 justify-content-center" id="btnGenerate">
          <i class="bi bi-lightning-charge"></i>
          Run Payroll Engine
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
    const $info = $('#payrollRangeInfo');
    if (!$info.length) return;
    const safeTotal = Number.isFinite(Number(total)) ? Number(total) : 0;
    const safeStart = safeTotal === 0 ? 0 : Number(from);
    const safeEnd = safeTotal === 0 ? 0 : Number(to);
    $info.text(`Showing ${safeStart.toLocaleString('en-IN')}-${safeEnd.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

$(function () {
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const baseUrl = "<?php echo e(route('payroll.process.index')); ?>";
  
  const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  
  loadPayrolls();

  function loadPayrolls(page = 1) {
    let year = $('#filterYear').val();
    
    $('#payrollTableBody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading payroll history...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
      url: baseUrl,
      type: 'GET',
      data: { page: page, year: year },
      success: function (data) {
        if (!data.data || data.data.length === 0) {
          $('#payrollTableBody').html(`
            <tr>
              <td colspan="5" class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Payrolls Found</h5>
                <p>No payrolls have been generated for ${year}.</p>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }
        
        let rows = '';
        $.each(data.data, function (i, row) {
          const statusBadge = row.status === 'Finalized' 
            ? '<span class="badge badge-modern-success"><i class="bi bi-check-circle"></i> Finalized</span>' 
            : '<span class="badge badge-modern-warning"><i class="bi bi-pencil"></i> Draft</span>';
            
          const createdDate = new Date(row.created_at).toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'});

          rows += `
            <tr style="animation-delay: ${i * 0.05}s;">
              <td><strong>${monthNames[row.month]} ${row.year}</strong></td>
              <td>${row.details_count || 0} Employees</td>
              <td>${statusBadge}</td>
              <td><small class="text-muted">${createdDate}</small></td>
              <td>
                <div class="d-flex gap-2 justify-content-start">
                  <a href="${baseUrl}/${row.id}" class="btn-action btn-action-view" title="View Details"><i class="bi bi-eye me-1"></i> View Details</a>
                  ${row.status !== 'Finalized' ? `<button class="btn-action btn-action-delete void-payroll" data-id="${row.id}" title="Void Payroll"><i class="bi bi-trash me-1"></i> Void</button>` : ''}
                </div>
              </td>
            </tr>
          `;
        });
        $('#payrollTableBody').html(rows);
        buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
        updateRangeInfo(data.from, data.to, data.total);
      },
      error: function() {
        $('#payrollTableBody').html(`
          <tr>
            <td colspan="5" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i> Failed to load payrolls.
            </td>
          </tr>
        `);
      }
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadPayrolls(page);
  });
  
  $('#filterYear').on('change', function() {
      loadPayrolls(1);
  });

  $('#openGenerateModal').on('click', function() {
      $('#generateError').addClass('d-none').text('');
      $('#generateModal').modal('show');
  });

  $('#btnGenerate').on('click', function () {
    const payload = {
      _token: csrf,
      month: $('#gen_month').val(),
      year: $('#gen_year').val()
    };
    
    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Processing...');
    $('#generateError').addClass('d-none');
    
    $.ajax({
        url: "<?php echo e(route('payroll.process.generate')); ?>",
        type: 'POST',
        data: payload
    })
    .done(function(res){
        if (res.success) {
            $('#generateModal').modal('hide');
            loadPayrolls();
            showAlert('success', res.message);
        } else {
            $('#generateError').removeClass('d-none').text(res.message);
        }
    })
    .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Processing failed. Check if structures and components are set up correctly.';
        $('#generateError').removeClass('d-none').text(msg);
    })
    .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-lightning-charge"></i> Run Payroll Engine');
    });
  });

  $(document).on('click', '.void-payroll', function () {
    if (confirm('Are you sure you want to void this draft payroll? This will delete all generated payslips for this period.')) {
      $.ajax({
        url: `/payroll/process/${$(this).data('id')}/void`,
        type: 'DELETE',
        data: { _token: csrf },
        success: function (res) {
          loadPayrolls();
          showAlert('success', res.message);
        },
        error: function(xhr) {
          const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to void payroll.';
          showAlert('error', msg);
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/process.blade.php ENDPATH**/ ?>