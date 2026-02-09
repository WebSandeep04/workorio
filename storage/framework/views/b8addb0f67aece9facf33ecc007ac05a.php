

<?php $__env->startSection('title', 'Attendance Approval'); ?>
<?php $__env->startSection('page_title', 'Attendance Approval'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Table Header - no uppercase, specific shadow */
  .data-table-card .custom-table thead th {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    text-transform: none !important; /* REQUESTED CHANGE: Remove uppercase */
    font-size: 0.75rem !important; /* Slightly larger for readability if mixed case */
    letter-spacing: normal !important;
  }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.4rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
    font-size: 1.25rem;
  }

  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: space-between;
    flex-wrap: wrap; /* Allow wrapping on small screens */
  }

  .table-search-field {
    flex: 1;
    min-width: 200px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
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
  
  /* Date Filter Input Style */
  .date-filter-input {
      background: #f4f5f7;
      border: 1px solid #e5e7eb;
      border-radius: 2px;
      padding: 0.35rem 0.5rem;
      font-size: 0.85rem;
      color: #111827;
      font-family: Montserrat;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }
  .date-filter-input:focus {
      outline: none;
      border-color: #434afa;
  }

  .btn-custom-primary {
    background-color: #434afa;
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    padding: 0.35rem 1rem;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
  }
  
  .btn-custom-primary:hover {
     background-color: #3538d4;
     color: white;
     box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
  }
  
  .btn-custom-primary:disabled {
      background-color: #a0a3f5;
      cursor: not-allowed;
      box-shadow: none;
  }

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

  .data-table-card .table-responsive {
    border-radius: 18px;
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
    /* text-transform: uppercase; REMOVED per request */
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
  }

  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .btn-action:hover {
    color: #434afa;
    transform: scale(1.1);
  }

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
  
  .spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .badge-emergency {
      background-color: #ef4444;
      color: white;
      font-size: 0.65rem;
      padding: 2px 6px;
      border-radius: 4px;
      margin-left: 5px;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">

  <!-- Summary Cards -->
  <div class="summary-cards">
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-amber">
          <i class="bi bi-person-check fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Pending Approvals</div>
          <div class="summary-card-value text-dark" id="stat_pending_count">0</div>
        </div>
      </div>
  </div>

  <!-- Actions & Search -->
  <div class="table-search mb-2">
    <!-- Date Filter -->
    <div class="d-flex align-items-center gap-2">
        <input type="date" id="filterDate" class="date-filter-input" value="<?php echo e(\Carbon\Carbon::today()->toDateString()); ?>" title="Filter by Date" onchange="fetchAttendance(1)">
    </div>

    <div class="table-search-field mx-2">
      <i class="bi bi-search"></i>
      <input type="text" id="searchInput" placeholder="Search employee..." />
    </div>
    
    <div class="d-flex gap-2">
      <button class="btn btn-custom-primary btn-sm" id="btnBulkApprove" style="display: none;" onclick="bulkApprove()">
        <i class="bi bi-check-all me-1"></i> Approve Selected (<span id="selectedCount">0</span>)
      </button>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetAndRefresh()" style="font-size: 0.85rem; border-radius: 2px;" title="Reset Filters & Refresh">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="attendanceTable">
          <thead>
            <tr>
              <th width="40"><input type="checkbox" id="checkAll"></th>
              <th>Date</th>
              <th>Employee</th>
              <th>In</th>
              <th>Out</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="text-center py-4">
                <i class="bi bi-arrow-repeat spin"></i> Loading data...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 d-flex justify-content-center">
    <div id="paginationLinks"></div>
  </div>
</div>

<!-- Edit Time Modal -->
<div class="modal fade" id="editTimeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="modal-header bg-primary text-white p-2">
        <h6 class="modal-title ms-2">Edit Punch Times</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editTimeForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="edit_attendance_id">
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label small fw-bold">Punch In Time</label>
            <input type="time" class="form-control form-control-sm" name="in_time" id="edit_in_time" required>
          </div>
          <div class="mb-1">
            <label class="small fw-bold">Punch Out Time</label>
            <input type="time" class="form-control form-control-sm" name="out_time" id="edit_out_time">
            <small class="text-muted" style="font-size: 0.65rem;">Leave empty if not left yet.</small>
          </div>
        </div>
        <div class="modal-footer p-2 d-flex justify-content-center border-0">
          <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary px-3">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {

    let currentPage = 1;

    window.fetchAttendance = function(page = 1) {
        currentPage = page;
        let searchTerm = $('#searchInput').val();
        let dateFilter = $('#filterDate').val();
        
        // Disable bulk btn
        $('#checkAll').prop('checked', false);
        $('#selectedCount').text(0);
        $('#btnBulkApprove').hide(); 
        // toggleBulkButton(); // Remove this as we manually hid it above for immediate effect

        // Show loading state
        $('#attendanceTable tbody').html(`
           <tr>
               <td colspan="7" class="text-center py-4 text-muted">
                   <i class="bi bi-arrow-repeat spin" style="font-size: 1.2rem;"></i> Loading records...
               </td>
           </tr>
        `);

        $.ajax({
            url: "<?php echo e(route('attendance.approval.fetch')); ?>",
            type: 'GET',
            data: { 
                page: page, 
                search: searchTerm,
                date: dateFilter
            },
            success: function(response) {
                let rows = '';
                let data = response.data;
                $('#stat_pending_count').text(response.total || 0);
                
                if (data.length > 0) {
                    data.forEach(function(item) {
                        let emergencyBadge = item.is_emergency ? '<span class="badge-emergency">Provisional</span>' : '';
                        
                        // Helper for badges
                        let getBadge = (type) => {
                            if (!type) return '';
                            let cls = (type === 'field') ? 'bg-info' : 'bg-primary';
                            return `<span class="badge ${cls} me-1" style="font-size: 0.6rem; text-transform: capitalize;">${type}</span>`;
                        };

                        let inEntry = `${getBadge(item.in_type)} ${item.in_time}`;
                        let outEntry = `${getBadge(item.out_type)} ${item.out_time}`;

                        rows += `
                            <tr>
                                <td><input type="checkbox" class="row-checkbox" value="${item.id}"></td>
                                <td>${item.date}</td>
                                <td><span class="fw-bold">${item.user_name}</span> ${emergencyBadge}</td>
                                <td>${inEntry}</td>
                                <td>${outEntry}</td>
                                <td><span class="badge bg-warning text-dark" style="font-size: 0.7rem;">${item.status}</span></td>
                                <td>
                                    <button class="btn-action text-success" title="Approve" onclick="approveAttendance(${item.id})">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button class="btn-action text-primary ms-1" title="Edit Times" onclick="editTimes(${item.id}, '${item.in_time_raw}', '${item.out_time_raw}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="7" class="text-center py-4 text-muted">No pending approvals found</td></tr>`;
                }
                
                $('#attendanceTable tbody').html(rows);
                
                // Render Pagination
                let links = '';
                if (response.links) {
                  let linkHtml = '';
                  response.links.forEach(link => {
                    if (link.url) {
                         let activeClass = link.active ? 'active' : '';
                         // Replace HTML entities for better rendering
                         let label = String(link.label).replace('&laquo;', '«').replace('&raquo;', '»');
                         linkHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchAttendance(${link.url.split('page=')[1]})">${label}</a></li>`;
                    } else {
                         let label = String(link.label).replace('&laquo;', '«').replace('&raquo;', '»');
                         linkHtml += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
                    }
                  });
                  $('#paginationLinks').html(`<ul class="pagination pagination-sm">${linkHtml}</ul>`);
                }
            },
            error: function(xhr) {
                console.error('Error fetching data', xhr);
                $('#attendanceTable tbody').html(`<tr><td colspan="7" class="text-center py-4 text-danger">Error loading data</td></tr>`);
            }
        });
    }

    // CHECKBOX LOGIC
    $('#checkAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkButton();
    });

    $(document).on('change', '.row-checkbox', function() {
        toggleBulkButton();
        // Update header checkbox
        let allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
        $('#checkAll').prop('checked', allChecked);
    });

    function toggleBulkButton() {
        let count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#btnBulkApprove').fadeIn(200);
        } else {
            $('#btnBulkApprove').fadeOut(200);
        }
    }

    // ACTIONS
    window.approveAttendance = function(id) {
        if (!confirm('Are you sure you want to approve this attendance?')) return;

        $.ajax({
            url: "/attendance/approve/" + id,
            type: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>' },
            success: function(response) {
                if (response.success) {
                    fetchAttendance(currentPage); 
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error approving attendance');
            }
        });
    }

    // Edit Times Modal Helper
    window.editTimes = function(id, inRaw, outRaw) {
        $('#edit_attendance_id').val(id);
        $('#edit_in_time').val(inRaw);
        $('#edit_out_time').val(outRaw);
        $('#editTimeModal').modal('show');
    }

    // Submit Time Edit
    $('#editTimeForm').submit(function(e) {
        e.preventDefault();
        let id = $('#edit_attendance_id').val();
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();

        submitBtn.text('Updating...').prop('disabled', true);

        $.ajax({
            url: "/attendance/update-times/" + id,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                submitBtn.text(originalText).prop('disabled', false);
                if (response.success) {
                    $('#editTimeModal').modal('hide');
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                let msg = 'Error updating times';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg += ': ' + xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });

    window.bulkApprove = function() {
        let ids = [];
        $('.row-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        if (!confirm(`Are you sure you want to approve ${ids.length} records?`)) return;

        $.ajax({
            url: "<?php echo e(route('attendance.approve-bulk')); ?>",
            type: "POST",
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                ids: ids
            },
            success: function(response) {
                if (response.success) {
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error processing bulk approval');
            }
        });
    }

    // Search Debounce
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetchAttendance(1);
        }, 500);
    });
    
    // Date input change
    $('#filterDate').on('change', function() {
        console.log('Date Filter Changed:', $(this).val());
        fetchAttendance(1);
    });

    // Reset and Refresh
    window.resetAndRefresh = function() {
        // Set date filter to today's date
        let today = new Date().toISOString().split('T')[0];
        $('#filterDate').val(today);
        $('#searchInput').val(''); // Clear search input
        fetchAttendance(1);
    }

    // Initial Load
    fetchAttendance();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/attendance/approval.blade.php ENDPATH**/ ?>