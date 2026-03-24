@extends('layouts.app')

@section('title', 'Leave Management')
@section('page_title', 'Leave Management')

@push('styles')
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
</style>
@endpush

@section('content')
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
                    
                    <div class="row g-2 mb-3" id="sl_time_div" style="display:none;">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-primary">From Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-primary">To Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time">
                        </div>
                        <div class="col-12 text-muted fw-bold" style="font-size:0.75rem;" id="shift_bounds_text"></div>
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
@endsection

@push('scripts')
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
        $('#sl_time_div').slideUp();
        $('#start_time, #end_time').removeAttr('required').val('');
        $('#start_date, #end_date').prop('readonly', false);
        $('#end_date').closest('.col-6').show();

        if (typeId === 'rh' && targetType && targetType.rh_list) {
            $('#rh_holiday_div').slideDown();
            let opts = '<option value="">Choose your pending RH...</option>';
            targetType.rh_list.forEach(h => {
                let cleanDate = h.holiday_date.substring(0, 10);
                opts += `<option value="${cleanDate}" data-name="${h.name}">${h.name} (${cleanDate})</option>`;
            });
            $('#rh_holiday_select').html(opts).attr('required', true);
            
            $('#start_date, #end_date').prop('readonly', true);
        } else if (typeId === 'sl' && targetType) {
            $('#sl_time_div').slideDown();
            $('#start_time, #end_time').attr('required', true);
            // Lock dates to single day
            $('#end_date').closest('.col-6').hide();
            $('#end_date').val($('#start_date').val());
            
            // Limit bounds
            let sStart = targetType.shift_start ? targetType.shift_start.substring(0,5) : '09:00';
            let sEnd = targetType.shift_end ? targetType.shift_end.substring(0,5) : '18:00';
            
            $('#start_time').attr('min', sStart).attr('max', sEnd);
            $('#end_time').attr('min', sStart).attr('max', sEnd);
            $('#shift_bounds_text').text(`Shift limits: ${sStart} to ${sEnd}`);
        }

        if(targetType) {
            $('#balanceAlert').fadeIn();
            $('#dynamicBalance').text(targetType.balance);
        } else {
            $('#balanceAlert').hide();
        }
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

    $('#start_time, #end_time').on('change', function() {
        let st = $('#start_time').val();
        let et = $('#end_time').val();
        if(st && et && st >= et) {
            showAlert('error', 'End time must be after Start time.');
            $('#submitBtn').prop('disabled', true);
        } else {
            $('#submitBtn').prop('disabled', false);
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
    $.get('{{ route("leave.fetch") }}', function(response) {
        if (response.data) {
            allLeaves = response.data;
            filteredLeaves = [...allLeaves];
            currentPage = 1;
            renderTable();
        }
    });
}

function loadLeaveTypes() {
    $.get('{{ route("leave.types") }}', function(response) {
        if (response.data) {
            allLeaveTypes = response.data;
            let opts = '<option value="">Select Leave Type</option>';
            let cardsHtml = '';
            
            response.data.forEach(t => {
                opts += `<option value="${t.id}">${t.name}</option>`;
                
                let ledgerBtn = (t.id === 'rh' || t.id === 'sl') 
                    ? '' 
                    : `<button class="details-btn" onclick="openLedger('${t.id}', '${t.name}')">Ledger</button>`;
                
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
    
    $.get('{{ route("leave.ledger") }}', { leave_type_id: leaveTypeId }, function(res) {
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
            
            let timeStr = '';
            if (leave.is_sl && leave.start_time) {
                 timeStr = `<br><span class="badge bg-light text-dark border mt-1"><i class="bi bi-clock me-1"></i>${leave.start_time.substring(0,5)} to ${leave.end_time.substring(0,5)}</span>`;
            }

            html += `<tr>
                <td><strong>${new Date(leave.start_date).toLocaleDateString()}</strong> ${leave.start_date !== leave.end_date ? `to <strong>${new Date(leave.end_date).toLocaleDateString()}</strong>` : ''} ${timeStr}</td>
                <td><span style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700;">${leave.total_days}</span></td>
                <td>${typeName}</td>
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
    $('#rh_holiday_div').hide();
    $('#rh_holiday_select').removeAttr('required');
    $('#sl_time_div').hide();
    $('#start_time, #end_time').removeAttr('required');
    $('#end_date').closest('.col-6').show();
    $('#start_date, #end_date').prop('readonly', false);
    
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
        _token: '{{ csrf_token() }}',
        start_date: $('#start_date').val(),
        end_date: $('#leave_type_id').val() === 'sl' ? $('#start_date').val() : $('#end_date').val(),
        leave_type_id: $('#leave_type_id').val(),
        start_time: $('#start_time').val(),
        end_time: $('#end_time').val(),
        reason: $('#reason').val()
    };
    if (currentLeaveId) data._method = 'PUT';
    
    const url = currentLeaveId ? `/leave/${currentLeaveId}` : '{{ route("leave.store") }}';
    
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
        data: { _token: '{{ csrf_token() }}' },
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
@endpush
