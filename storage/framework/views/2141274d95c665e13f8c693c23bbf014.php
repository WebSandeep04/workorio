<?php $__env->startSection('title', 'Monthly Attendance Review'); ?>
<?php $__env->startSection('page_title', 'Monthly Attendance Review'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
  .table-search-btn { padding: 0.35rem 1rem; background: #434AFA; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .filter-dropdown { background: #fff; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; font-size: 0.85rem; outline: none; color: #111827; min-width: 120px; }
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
  .btn-action-lock { color: white; background: #F59E0B !important; border-radius: 4px; font-size: 0.75rem; padding: 0.35rem 0.7rem; }
  .btn-action-unlock { color: white; background: #6B7280 !important; border-radius: 4px; font-size: 0.75rem; padding: 0.35rem 0.7rem; }
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search by Employee Name or Code..." />
    </div>
    
    <select id="filterMonth" class="filter-dropdown">
      <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($num); ?>" <?php echo e(date('n') == $num ? 'selected' : ''); ?>><?php echo e($name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    
    <select id="filterYear" class="filter-dropdown">
      <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($yr); ?>" <?php echo e(date('Y') == $yr ? 'selected' : ''); ?>><?php echo e($yr); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
      
    <button id="btnSync" class="btn btn-primary" style="margin-left: 10px; border-radius: 8px;">
      <i class="bi bi-arrow-repeat me-1"></i> Sync Data
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="attendanceTable">
          <thead>
            <tr>
              <th style="min-width: 200px;">Employee</th>
              <th>Work Days</th>
              <th>Total Present</th>
              <th>Full Day</th>
              <th>Half Day</th>
              <th>Sunday Work</th>
              <th>Holiday Work</th>
              <th>Leave</th>
              <th>Unpaid Leave</th>
              <th>Absent</th>
              <th>Less Shift Hr</th>
              <th>More Shift Hr</th>
              <th>Late Count</th>
              <th>Payable Days</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="attendanceTableBody">
            <tr>
              <td colspan="16" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading attendance summaries...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="attendanceRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
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
    const $info = $('#attendanceRangeInfo');
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
  const baseUrl = "<?php echo e(route('payroll.attendance.review')); ?>";
  const syncUrl = "<?php echo e(route('payroll.attendance.sync')); ?>";
  let searchTimeout;
  
  loadAttendance();

  function loadAttendance(page = 1) {
    let search = $('#search').val();
    let month = $('#filterMonth').val();
    let year = $('#filterYear').val();
    
    $('#attendanceTableBody').html(`
      <tr>
        <td colspan="16" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading attendance summaries...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
      url: baseUrl,
      type: 'GET',
      data: { page: page, search: search, month: month, year: year },
      success: function (data) {
        if (!data.data || data.data.length === 0) {
          $('#attendanceTableBody').html(`
            <tr>
              <td colspan="16" class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h5>No Attendance Records Found</h5>
                <p>No monthly summaries available for the selected period.</p>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }
        
        let rows = '';
        $.each(data.data, function (i, row) {
          const emp = row.employee || {};
          const statusBadge = row.is_locked 
            ? '<span class="badge badge-modern-success"><i class="bi bi-lock-fill"></i> Locked</span>' 
            : '<span class="badge badge-modern-secondary"><i class="bi bi-unlock-fill"></i> Open</span>';
            
          const payableDays = parseFloat(row.days_worked || 0) + parseFloat(row.days_on_leave || 0) + parseFloat(row.total_holidays || 0) + parseFloat(row.total_weekly_offs || 0);

          let actionBtn = '';
          if (row.is_locked) {
            actionBtn = `<button class="btn-action btn-action-unlock toggle-lock" data-id="${row.id}" data-action="unlock"><i class="bi bi-unlock-fill me-1"></i> Unlock</button>`;
          } else {
            actionBtn = `<button class="btn-action btn-action-lock toggle-lock" data-id="${row.id}" data-action="lock"><i class="bi bi-lock-fill me-1"></i> Lock</button>`;
          }

          rows += `
            <tr style="animation-delay: ${i * 0.05}s;">
              <td>
                <div class="d-flex align-items-center">
                  <div class="fw-bold text-dark">${escapeHtml(emp.name)}</div>
                  <div class="ms-2 text-muted" style="font-size: 0.75rem;">${escapeHtml(emp.employee_code)}</div>
                </div>
              </td>
              <td>${row.total_working_days || 0}</td>
              <td>${row.total_present_combined || 0}</td>
              <td>${row.total_present || 0}</td>
              <td>${row.total_halfday || 0}</td>
              <td>${row.total_weekly_offs_worked || 0}</td>
              <td>${row.total_holidays_worked || 0}</td>
              <td>${row.days_on_leave || 0}</td>
              <td>${row.total_unpaid_leaves || 0}</td>
              <td><span class="text-danger fw-bold">${row.days_absent || 0}</span></td>
              <td>${row.total_less_8_30 || 0}</td>
              <td>${row.total_more_8_30 || 0}</td>
              <td>${row.late_count || 0}</td>
              <td><strong>${payableDays}</strong></td>
              <td>${statusBadge}</td>
              <td>${actionBtn}</td>
            </tr>
          `;
        });
        $('#attendanceTableBody').html(rows);
        buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
        updateRangeInfo(data.from, data.to, data.total);
      },
      error: function() {
        $('#attendanceTableBody').html(`
          <tr>
            <td colspan="16" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i> Failed to load attendance.
            </td>
          </tr>
        `);
      }
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadAttendance(page);
  });
  
  $('#search, #filterMonth, #filterYear').on('change keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadAttendance(1), 300);
  });

  $('#btnSync').on('click', function() {
    const btn = $(this);
    const originalHtml = btn.html();
    const month = $('#filterMonth').val();
    const year = $('#filterYear').val();

    btn.html('<i class="bi bi-arrow-repeat spin me-1"></i> Syncing...').prop('disabled', true);
    
    $.ajax({
      url: syncUrl,
      type: 'POST',
      data: { month: month, year: year },
      headers: { 'X-CSRF-TOKEN': csrf },
      success: function(res) {
        btn.html(originalHtml).prop('disabled', false);
        if (res.success) {
          showAlert('success', res.message);
          loadAttendance(1);
        } else {
          showAlert('error', res.message || 'Sync failed.');
        }
      },
      error: function(xhr) {
        btn.html(originalHtml).prop('disabled', false);
        showAlert('error', xhr.responseJSON?.message || 'Failed to sync data.');
      }
    });
  });

  $(document).on('click', '.toggle-lock', function () {
    const id = $(this).data('id');
    const action = $(this).data('action');
    const url = action === 'lock' ? "<?php echo e(route('payroll.attendance.lock')); ?>" : "<?php echo e(route('payroll.attendance.unlock')); ?>";
    const confirmMsg = action === 'lock' 
        ? 'Locking this attendance will prevent further modifications and prepare it for payroll. Proceed?' 
        : 'Unlocking will allow changes but may disrupt active payroll processing. Proceed?';

    if (confirm(confirmMsg)) {
      const $btn = $(this);
      const originalHtml = $btn.html();
      $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i>');

      $.ajax({
        url: url,
        type: 'POST',
        data: { _token: csrf, id: id },
        success: function (res) {
          loadAttendance();
          showAlert('success', res.message);
        },
        error: function(xhr) {
          showAlert('error', 'Failed to update lock status.');
          $btn.prop('disabled', false).html(originalHtml);
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/attendance_review.blade.php ENDPATH**/ ?>