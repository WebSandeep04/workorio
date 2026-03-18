<?php $__env->startSection('title', 'Leave Management'); ?>
<?php $__env->startSection('page_title', 'Leave Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

  /* Table Search & Add Button */
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .table-search-btn { padding: 0.35rem 1rem; background: #434afa; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; }

  /* Table Styles */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
  .custom-table { border-spacing: 0; width: 100%; min-width: 800px; font-size: 0.85rem; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; }
  
  /* Pagination */
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-weight: 500; cursor: pointer; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; }
  
  /* Modals */
  .modal-content { border-radius: 0; border: none; }
  .modal-header { border-radius: 0; background-color: #434afa; color: white; }
  .form-control, .form-select { border-radius: 0; }
  .btn { border-radius: 0; }
  .badge-status { font-weight: 600; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; }
  .badge-pending { background: #fff3cd; color: #856404; }
  .badge-approved { background: #d4edda; color: #155724; }
  .badge-cancelled { background: #f8d7da; color: #721c24; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Search and Add -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="leaveSearch" placeholder="Search leaves..." />
        </div>
        <button type="button" class="table-search-btn" onclick="openCreateModal()">
          <i class="bi bi-plus me-1"></i>Apply Leave
        </button>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="leavesTable">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Days</th>
                            <th>Leave Type</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="leavesTableBody">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Loading leaves...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="table-range-meta" id="leaveRangeInfo" style="font-size:0.75rem; color:#6b7280;">Showing 0-0 of 0 entries</div>
    <div class="d-flex justify-content-center mt-2">
        <ul class="pagination" id="pagination"></ul>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Apply Leave</h5>
                <button type="button" class="btn-close" style="filter: invert(1);" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="leaveForm">
                <div class="modal-body">
                    <div id="balanceAlert" class="alert alert-info py-2 fw-bold" style="font-size:0.85rem; display:none;">
                        <i class="bi bi-wallet2 me-1"></i> Balance: <span id="dynamicBalance"></span> Days
                    </div>
                
                    <input type="hidden" id="leaveId" name="id">
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required onchange="calculateDays()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required onchange="calculateDays()">
                        </div>
                    </div>
                    
                    <div class="text-end mb-2 fw-bold text-primary" style="font-size:0.8rem;" id="calcDaysDisplay">
                        Total: 1 Day
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Optional reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="submitBtn" style="background:#434afa; border-color:#434afa;">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel/Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Cancel Leave Request</h5>
                <button type="button" class="btn-close" style="filter: invert(1);" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this leave application? If it was already approved, the balance will be instantly refunded to your ledger.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Yes, Cancel Leave</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let allLeaves = [];
let filteredLeaves = [];
let allLeaveTypes = [];
let currentPage = 1;
let itemsPerPage = 8;
let currentLeaveId = null;
let deleteLeaveId = null;

$(document).ready(function() {
    loadLeaveTypes();
    loadLeaves();
    
    $('#leaveSearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        filteredLeaves = allLeaves.filter(l => {
            return (l.leave_type?.name || '').toLowerCase().includes(query) ||
                   (l.reason || '').toLowerCase().includes(query) ||
                   (l.status || '').toLowerCase().includes(query);
        });
        currentPage = 1;
        renderTable();
    });
    
    $('#leaveForm').on('submit', function(e) { e.preventDefault(); submitForm(); });
    $('#confirmDeleteBtn').on('click', function() { deleteLeave(); });
    
    $('#leave_type_id').on('change', function() {
        let typeId = $(this).val();
        let targetType = allLeaveTypes.find(t => t.id == typeId);
        if(targetType) {
            $('#balanceAlert').fadeIn();
            $('#dynamicBalance').text(targetType.balance);
        } else {
            $('#balanceAlert').hide();
        }
    });
});

function calculateDays() {
    let s = $('#start_date').val();
    let e = $('#end_date').val();
    
    if(s && e) {
        let sd = new Date(s);
        let ed = new Date(e);
        if(ed >= sd) {
            let diffTime = Math.abs(ed - sd);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            $('#calcDaysDisplay').text('Total: ' + diffDays + ' Day' + (diffDays>1?'s':''));
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#calcDaysDisplay').text('Invalid Range');
            $('#submitBtn').prop('disabled', true);
        }
    }
}

function showAlert(type, message) {
    let color = type === 'success' ? 'alert-success' : 'alert-danger';
    $('#alertBox').html(`<div class="alert ${color} alert-dismissible fade show border-0 shadow-sm" style="border-radius:0;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
}

function loadLeaves() {
    $.get('<?php echo e(route("leave.fetch")); ?>', function(response) {
        if (response.data) {
            allLeaves = response.data;
            filteredLeaves = [...allLeaves];
            currentPage = 1;
            renderTable();
        }
    });
}

function loadLeaveTypes() {
    $.get('<?php echo e(route("leave.types")); ?>', function(response) {
        if (response.data) {
            allLeaveTypes = response.data;
            let opts = '<option value="">Select Leave Type</option>';
            response.data.forEach(t => opts += `<option value="${t.id}">${t.name}</option>`);
            $('#leave_type_id').html(opts);
        }
    });
}

function renderTable() {
    const total = filteredLeaves.length;
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    const pageData = filteredLeaves.slice(start, end);
    
    let html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="6" class="text-center py-4 text-muted">No applications found.</td></tr>';
    } else {
        pageData.forEach(leave => {
            let badge = 'pending';
            if(leave.status === 'approved') badge = 'approved';
            if(leave.status === 'cancelled') badge = 'cancelled';
            if(leave.status === 'rejected') badge = 'cancelled';

            html += `<tr>
                <td><strong>${new Date(leave.start_date).toLocaleDateString()}</strong> to <strong>${new Date(leave.end_date).toLocaleDateString()}</strong></td>
                <td><span style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700;">${leave.total_days}</span></td>
                <td>${leave.leave_type ? leave.leave_type.name : '-'}</td>
                <td><span class="badge-status badge-${badge}">${(leave.status || 'unknown').toUpperCase()}</span></td>
                <td>${leave.reason || '-'}</td>
                <td class="text-center">
                    ${leave.status === 'pending' || leave.status === 'approved' ? `
                    <button class="btn btn-sm btn-link delete-btn p-0 text-danger" onclick="openDeleteModal(${leave.id})" title="Cancel Leave"><i class="bi bi-x-circle-fill"></i></button>
                    ` : '-'}
                </td>
            </tr>`;
        });
    }
    
    $('#leavesTableBody').html(html);
    $('#leaveRangeInfo').text(`Showing ${total > 0 ? start + 1 : 0}-${end} of ${total} entries`);
    
    // Base pagination logic simply rendering buttons
    renderPagination(total);
}

function renderPagination(total) {
    const totalPages = Math.ceil(total / itemsPerPage);
    let pagHtml = '';
    if (totalPages > 1) {
        for(let i=1; i<=totalPages; i++) {
             pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
        }
    }
    $('#pagination').html(pagHtml);
}

function changePage(p) { currentPage = p; renderTable(); }

function openCreateModal() {
    currentLeaveId = null;
    $('#leaveModalLabel').text('Apply Leave');
    $('#submitBtn').text('Submit Application');
    $('#leaveForm')[0].reset();
    $('#balanceAlert').hide();
    
    const today = new Date().toISOString().split('T')[0];
    $('#start_date').val(today);
    $('#end_date').val(today);
    calculateDays();
    $('#leaveModal').modal('show');
}

function openDeleteModal(id) {
    deleteLeaveId = id;
    $('#deleteModal').modal('show');
}

function submitForm() {
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        leave_type_id: $('#leave_type_id').val(),
        reason: $('#reason').val()
    };
    if (currentLeaveId) data._method = 'PUT';
    
    const url = currentLeaveId ? `/leave/${currentLeaveId}` : '<?php echo e(route("leave.store")); ?>';
    
    let btn = $('#submitBtn');
    btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url: url, type: 'POST', data: data,
        success: function(res) {
            if (res.success) {
                $('#leaveModal').modal('hide');
                showAlert('success', res.message);
                loadLeaves();
                loadLeaveTypes(); // Refresh balances
            }
        },
        error: function(xhr) {
             let msg = 'Error processing request.';
             if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
             showAlert('error', msg);
        },
        complete: function() { btn.prop('disabled', false).text('Submit Application'); }
    });
}

function deleteLeave() {
    let btn = $('#confirmDeleteBtn');
    btn.prop('disabled', true).text('Cancelling...');
    
    $.ajax({
        url: `/leave/${deleteLeaveId}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function(res) {
             if (res.success) {
                 $('#deleteModal').modal('hide');
                 showAlert('success', res.message);
                 loadLeaves();
                 loadLeaveTypes(); // Refresh balances
             }
        },
        error: function(xhr) { 
             let msg = 'Error archiving request.';
             if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
             showAlert('error', msg);
        },
        complete: function() { btn.prop('disabled', false).text('Yes, Cancel Leave'); }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/leave/index.blade.php ENDPATH**/ ?>