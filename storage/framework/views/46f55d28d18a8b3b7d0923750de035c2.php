

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
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; scrollbar-color: #434afa #e4e7ec; }
  .table-scroll::-webkit-scrollbar { height: 8px; }
  .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .table-scroll::-webkit-scrollbar-thumb { background: #434afa; border-radius: 999px; }
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

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }
  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .summary-card:hover { transform: translateY(-2px); box-shadow: 0px 8px 8px 0px #0000000A; }
  .summary-card-content { display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; }
  .summary-card-label { font-size: 10px; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; color: #000; line-height: 1.2; font-family: Montserrat; }
  .summary-card-value { font-size: 1.1rem; font-weight: 700; margin: 0; line-height: 1; color: #101828; font-family: Montserrat; }
  
  .details-btn {
      font-size: 10px;
      padding: 0.2rem 0.5rem;
      border-radius: 4px;
      background: #434afa;
      color: white;
      text-decoration: none;
      border: none;
      cursor: pointer;
  }
  .details-btn:hover { background: #3538d4; color: white;}
  .stats-row { display: flex; gap: 0.5rem; font-size: 10px; margin-top: 4px; }
  .text-xs { font-size: 10px; }
  .x-small { font-size: 0.7rem; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Summary Cards Container -->
    <div class="summary-cards" id="leaveSummaryCards">
        <!-- Cards loaded dynamically -->
    </div>
    
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

                    <div id="half_day_toggle_div" style="display:none;" class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" onchange="toggleHalfDay(this.checked)">
                            <label class="form-check-label fw-bold text-primary" for="is_half_day">Is Half Day?</label>
                        </div>
                    </div>

                    <div id="half_day_options" style="display:none;" class="mb-3 p-2 bg-light border">
                        <label class="form-label small fw-bold text-primary d-block">Select Half Day Session <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="half_day_period" id="pre_lunch" value="pre_lunch" autocomplete="off" checked>
                                <label class="btn btn-outline-primary w-100 py-2" for="pre_lunch">
                                    <i class="bi bi-brightness-high d-block mb-1"></i>
                                    Pre Lunch
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="half_day_period" id="post_lunch" value="post_lunch" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 py-2" for="post_lunch">
                                    <i class="bi bi-sunset d-block mb-1"></i>
                                    Post Lunch
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="rh_holiday_div" style="display:none;">
                        <label class="form-label small fw-bold text-primary">Select Restricted Holiday <span class="text-danger">*</span></label>
                        <select class="form-control" id="rh_holiday_select">
                            <option value="">Choose your pending RH...</option>
                        </select>
                    </div>
                    
                    <!-- SL Info -->
                    <div class="mb-3" id="sl_type_div" style="display:none;">
                        <input type="hidden" name="sl_period" id="sl_period_hidden" value="evening">
                        <label class="form-label small fw-bold text-primary d-block">Short Leave Information</label>
                        <div class="alert alert-warning py-2 border-0 shadow-sm" style="border-radius:0;">
                            <i class="bi bi-info-circle me-1"></i> Short Leave is available only for the <strong>Evening</strong> window.
                            <div class="mt-1 fw-bold text-dark" id="sl_evening_window_info" style="font-size:0.85rem;">Window: ...</div>
                        </div>
                        <div class="text-muted fw-bold mt-2" style="font-size:0.75rem;" id="shift_bounds_text"></div>
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

<!-- Ledger Modal -->
<div class="modal fade" id="ledgerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #434afa;">
                <h5 class="modal-title text-white">Leave Ledger: <span id="ledgerLeaveTypeName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-scroll">
                    <table class="table custom-table mb-0" id="ledgerTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="ledgerTableBody">
                        </tbody>
                    </table>
                </div>
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
        
        // Reset blocks
        $('#rh_holiday_div').slideUp();
        $('#rh_holiday_select').removeAttr('required').val('');
        $('#sl_type_div').slideUp();
        $('#half_day_toggle_div').slideUp();
        $('#half_day_options').slideUp();
        $('#is_half_day').prop('checked', false);
        $('#end_date').closest('.col-6').show();
        $('#start_date, #end_date').prop('readonly', false);

        if (!targetType) {
            $('#balanceAlert').hide();
            return;
        }

        // Show Balance
        $('#balanceAlert').fadeIn();
        $('#dynamicBalance').text(targetType.balance);

        // Logic based on dynamic flags
        if (targetType.allow_half_day) {
            $('#half_day_toggle_div').slideDown();
        }

        if (targetType.is_restricted && targetType.rh_list) {
            $('#rh_holiday_div').slideDown();
            let opts = '<option value="">Choose your pending RH...</option>';
            targetType.rh_list.forEach(h => {
                let cleanDate = h.holiday_date.substring(0, 10);
                opts += `<option value="${cleanDate}" data-name="${h.name}">${h.name} (${cleanDate})</option>`;
            });
            $('#rh_holiday_select').html(opts).attr('required', true);
            $('#start_date, #end_date').prop('readonly', true);
        } else if (targetType.is_short_leave) {
            $('#sl_type_div').slideDown();
            // Lock dates to single day
            $('#end_date').closest('.col-6').hide();
            $('#end_date').val($('#start_date').val());
            
            // Limit bounds display
            let sStartStr = targetType.shift_start ? targetType.shift_start.substring(0,5) : '09:00';
            let sEndStr = targetType.shift_end ? targetType.shift_end.substring(0,5) : '18:00';
            $('#shift_bounds_text').text(`Shift limits: ${sStartStr} to ${sEndStr}`);

            // Calc windows
            let endLimit = parseInt(targetType.end_limit_hours || 0);
            
            function subHours(timeStr, hours) {
                let parts = timeStr.split(':');
                let h = parseInt(parts[0]) - hours;
                if(h < 0) h = 0;
                return (h < 10 ? '0'+h : h) + ':' + parts[1];
            }

            let eveningStart = subHours(sEndStr, endLimit);
            $('#sl_evening_window_info').text(`Window: ${eveningStart} - ${sEndStr}`);
        }

        calculateDays();
    });

    $('#rh_holiday_select').on('change', function() {
        let val = $(this).val();
        let name = $(this).find(':selected').data('name');
        if (val) {
            $('#start_date').val(val);
            $('#end_date').val(val);
            // Optional: you can prepend the name to the reason if you want
            if (!$('#reason').val().includes('Restricted Holiday:')) {
                $('#reason').val('Restricted Holiday: ' + name);
            }
            calculateDays();
        } else {
            $('#start_date').val('');
            $('#end_date').val('');
            $('#reason').val('');
            $('#calcDaysDisplay').text('Total: 0 Days');
            $('#submitBtn').prop('disabled', true);
        }
    });

    $('#start_date').on('change', function() {
        if ($('#leave_type_id').val() === 'sl') {
            $('#end_date').val($(this).val());
        }
    });


});

function calculateDays() {
    let s = $('#start_date').val();
    let e = $('#end_date').val();
    let typeId = $('#leave_type_id').val();
    let targetType = allLeaveTypes.find(t => t.id == typeId);
    let isHalf = $('#is_half_day').is(':checked');
    
    if(s && e) {
        if (isHalf && targetType) {
            let weight = targetType.half_day_weight || 0.5;
            $('#calcDaysDisplay').text('Total: ' + weight + ' Day');
            $('#submitBtn').prop('disabled', false);
            return;
        }

        let sd = new Date(s);
        let ed = new Date(e);
        if(ed >= sd) {
            let diffTime = Math.abs(ed - sd);
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            // If it's a special type with a weight (like Short Leave)
            if (targetType && targetType.is_short_leave) {
                let weight = (targetType.full_day_weight || 0.25) * diffDays;
                $('#calcDaysDisplay').text('Total: ' + weight + ' Day' + (weight!=1?'s':''));
            } else {
                $('#calcDaysDisplay').text('Total: ' + diffDays + ' Day' + (diffDays>1?'s':''));
            }
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#calcDaysDisplay').text('Invalid Range');
            $('#submitBtn').prop('disabled', true);
        }
    }
}

function toggleHalfDay(active) {
    if (active) {
        $('#half_day_options').slideDown();
        $('#end_date').val($('#start_date').val());
        $('#end_date').closest('.col-6').hide();
    } else {
        $('#half_day_options').slideUp();
        $('#end_date').closest('.col-6').show();
    }
    calculateDays();
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
            let cardsHtml = '';
            
            response.data.forEach(t => {
                opts += `<option value="${t.id}">${t.name}</option>`;
                
                let ledgerBtn = (t.id === 'rh' || t.id === 'sl' || t.id === 'hd') 
                    ? '' 
                    : `<button class="details-btn" onclick="openLedger('${t.id}', '${t.name}')">Ledger</button>`;

                
                if (t.id !== 'hd') {
                    cardsHtml += `
                        <div class="summary-card">
                            <div class="summary-card-content" style="padding-right:0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="summary-card-label">${t.name}</div>
                                    ${ledgerBtn}
                                </div>
                                <div class="summary-card-value text-primary mt-1">${t.balance} <span style="font-size:10px; color:#6b7280; font-weight:500;">Balance</span></div>
                                <div class="stats-row text-muted fw-bold">
                                    <span>Allowed: ${t.total_allowed}</span> | 
                                    <span class="text-warning">Pending: ${t.pending}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }

            });
            $('#leave_type_id').html(opts);
            $('#leaveSummaryCards').html(cardsHtml);
        }
    });
}

function openLedger(leaveTypeId, leaveTypeName) {
    if (leaveTypeId === 'rh') {
        showAlert('warning', 'There is no detailed ledger for Restricted Holidays (RH). The balance is simply deduced from your allowance based on your active RH leave requests.');
        return;
    }
    if (leaveTypeId === 'sl') {
        showAlert('warning', 'Short Leaves have a fixed monthly quota, no accrued ledger is maintained.');
        return;
    }

    $('#ledgerLeaveTypeName').text(leaveTypeName);
    $('#ledgerTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted">Loading ledger...</td></tr>');
    $('#ledgerModal').modal('show');
    
    $.get('<?php echo e(route("leave.ledger")); ?>', { leave_type_id: leaveTypeId }, function(res) {
        if(res.success && res.data.length > 0) {
            let html = '';
            res.data.forEach(tx => {
                let badgeClass = tx.transaction_type === 'credit' ? 'success' : (tx.transaction_type === 'debit' ? 'danger' : 'secondary');
                let op = tx.transaction_type === 'credit' ? '+' : '';
                html += `
                    <tr>
                        <td>${new Date(tx.created_at).toLocaleDateString()}</td>
                        <td><span class="badge bg-${badgeClass}">${tx.transaction_type.toUpperCase()}</span></td>
                        <td class="text-${badgeClass} fw-bold">${op}${tx.amount}</td>
                        <td class="fw-bold">${tx.balance_after}</td>
                        <td><span class="text-xs">${tx.remarks || '-'}</span></td>
                    </tr>
                `;
            });
            $('#ledgerTableBody').html(html);
        } else {
            $('#ledgerTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted">No ledger records found.</td></tr>');
        }
    }).fail(function() {
        $('#ledgerTableBody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load ledger.</td></tr>');
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

            let typeName = leave.leave_type ? leave.leave_type.name : '-';
            if (leave.is_rh) typeName = 'Restricted Holiday (RH)';
            if (leave.is_sl) typeName = 'Short Leave (SL)';
            if (leave.is_half_day) {
                let sessionName = leave.half_day_period === 'pre_lunch' ? 'Pre Lunch' : 'Post Lunch';
                typeName += ` <span class="badge bg-info x-small">${sessionName}</span>`;
            }
            
            let timeStr = '';
            if (leave.is_sl && leave.start_time) {
                 timeStr = `<br><span class="badge bg-light text-dark border mt-1"><i class="bi bi-clock me-1"></i>${leave.start_time.substring(0,5)} to ${leave.end_time.substring(0,5)}</span>`;
            }

            let overlapWarning = '';
            if (leave.has_attendance_overlap && leave.status === 'approved') {
                overlapWarning = ` <i class="bi bi-exclamation-triangle-fill text-warning" style="cursor:help;" title="Attendance detected during this leave!"></i>`;
            }

            let actions = '-';
            if (leave.status === 'pending') {
                actions = `<button class="btn btn-sm btn-link delete-btn p-0 text-danger" onclick="openDeleteModal(${leave.id})" title="Cancel Leave"><i class="bi bi-x-circle-fill"></i></button>`;
            } else if (leave.status === 'approved') {
                let cancelBtn = `<button class="btn btn-sm btn-link delete-btn p-0 text-danger" onclick="openDeleteModal(${leave.id})" title="Cancel Leave"><i class="bi bi-x-circle-fill"></i></button>`;
                let resumeBtn = '';
                if (leave.has_attendance_overlap) {
                    resumeBtn = `<button class="btn btn-sm btn-warning py-0 px-2 ms-2" style="font-size:10px; font-weight:700; border-radius:4px;" onclick="resumeEarly(${leave.id}, '${leave.start_date}')">Resume Work</button>`;
                }
                actions = `<div class="d-flex align-items-center justify-content-center">${cancelBtn} ${resumeBtn}</div>`;
            }

            html += `<tr>
                <td><strong>${new Date(leave.start_date).toLocaleDateString()}</strong> ${leave.start_date !== leave.end_date ? `to <strong>${new Date(leave.end_date).toLocaleDateString()}</strong>` : ''} ${timeStr}</td>
                <td><span style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700;">${leave.total_days}</span></td>
                <td>${typeName}</td>
                <td><span class="badge-status badge-${badge}">${(leave.status || 'unknown').toUpperCase()}</span>${overlapWarning}</td>
                <td>${leave.reason || '-'}</td>
                <td class="text-center">${actions}</td>
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
    $('#rh_holiday_div').hide();
    $('#rh_holiday_select').removeAttr('required');
    $('#end_date').closest('.col-6').show();
    $('#start_date, #end_date').prop('readonly', false);
    $('#is_half_day').prop('checked', false);
    $('#half_day_options').hide();
    $('#sl_type_div').hide();
    
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = tomorrow.toISOString().split('T')[0];
    
    $('#start_date').attr('min', tomorrowStr).val(tomorrowStr);
    $('#end_date').attr('min', tomorrowStr).val(tomorrowStr);
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
        is_half_day: $('#is_half_day').is(':checked') ? 1 : 0,
        half_day_period: $('#is_half_day').is(':checked') ? $('input[name="half_day_period"]:checked').val() : null,
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

function resumeEarly(id, startDate) {
    const today = new Date().toISOString().split('T')[0];
    let resumeDate = prompt("Enter the date employee resumed work (YYYY-MM-DD):", today);
    
    if (!resumeDate) return;

    if (confirm(`Confirm early return on ${resumeDate}? This will update the leave end date and refund any unused balance.`)) {
        $.post(`/leave/${id}/curtail`, {
            _token: '<?php echo e(csrf_token()); ?>',
            resume_date: resumeDate
        }, function(res) {
            if (res.success) {
                showAlert('success', res.message);
                loadLeaves();
                loadLeaveTypes();
            } else {
                showAlert('error', res.message);
            }
        }).fail(function(xhr) {
            let msg = xhr.responseJSON ? xhr.responseJSON.message : "Error processing resumption.";
            showAlert('error', msg);
        });
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/leave/index.blade.php ENDPATH**/ ?>