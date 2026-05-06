@extends('layouts.app')

@section('title', 'Leave Approvals')
@section('page_title', 'Leave Approvals')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

  /* Filter Box */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }
  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }
  .filterBox .form-control-modern, .filterBox .form-select-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
    width: 100%;
  }
  .filterBox .form-control-modern option, .filterBox .form-select-modern option { color: #000; background: #fff; }
  .filterBox .form-control-modern:focus, .filterBox .form-select-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  /* Table Search */
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Table Styles */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
  
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; font-family: Montserrat; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; font-family: Montserrat; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  /* Pagination */
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-weight: 500; cursor: pointer; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; }
  
  .badge-status { font-weight: 600; font-size: 0.75rem; }
  .badge-pending { color: #d97706; }
  .badge-approved { color: #059669; }
  .badge-rejected { color: #dc2626; }
  
  .truncate-reason { cursor: pointer; color: inherit; text-decoration: none; }
  .truncate-reason:hover { color: #000; text-decoration: underline; }

  .btn-action { background: transparent !important; border: none !important; padding: 0.25rem; color: #6c757d; transition: all 0.2s ease; cursor: pointer; }
  .btn-action:hover { color: #434afa; transform: scale(1.1); }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Filter Box -->
    <div class="filterBox">
        <div>
            <label class="form-label-modern"><i class="bi bi-funnel"></i> Status</label>
            <select class="form-select-modern" id="filterStatus">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-calendar"></i> From Date</label>
            <input type="date" class="form-control-modern" id="filterFromDate">
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-calendar"></i> To Date</label>
            <input type="date" class="form-control-modern" id="filterToDate">
        </div>
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="leaveSearch" placeholder="Search by name, type, or reason..." />
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="approvalsTable">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Days</th>
                            <th>Leave Type</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="approvalsTableBody">
                        <tr><td colspan="8" class="text-center py-4 text-muted">Loading leave approvals...</td></tr>
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

<!-- Reason Modal -->
<div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 0px !important;">
            <div class="modal-header bg-primary text-white border-0" style="background: #434AFA !important; border-radius: 0px !important;">
                <h5 class="modal-title" style="font-size: 1rem;"><i class="bi bi-card-text me-2"></i>Complete Reason</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <p id="fullReasonText" class="mb-0" style="font-size: 14px; white-space: pre-wrap; color:#333;"></p>
            </div>
            <div class="modal-footer border-0 p-3 bg-light" style="border-top: 1px solid #f0f0f0 !important;">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Leave Modal -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Reject Leave Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="reject_leave_id">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">REASON FOR REJECTION <span class="text-danger">*</span></label>
                    <textarea class="form-control border-0 bg-light" id="reject_reason" rows="4" placeholder="Please provide a valid reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" onclick="submitRejection()">Confirm Rejection</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allApprovals = [];
let filteredApprovals = [];
let currentPage = 1;
let itemsPerPage = 10;

$(document).ready(function() {
    loadApprovals();
    
    $('#leaveSearch, #filterStatus, #filterFromDate, #filterToDate').on('keyup change', function() {
        applyFilters();
    });
});

function applyFilters() {
    const query = $('#leaveSearch').val().toLowerCase();
    const status = $('#filterStatus').val();
    const fromDate = $('#filterFromDate').val();
    const toDate = $('#filterToDate').val();

    filteredApprovals = allApprovals.filter(l => {
        let matchQuery = true;
        if(query) {
            matchQuery = (l.user?.name || '').toLowerCase().includes(query) ||
                         (l.leave_type?.name || '').toLowerCase().includes(query) ||
                         (l.reason || '').toLowerCase().includes(query) ||
                         (l.status || '').toLowerCase().includes(query);
        }
        
        let matchStatus = true;
        if(status) {
            matchStatus = (l.status === status);
        }

        let matchDate = true;
        if(fromDate && toDate) {
            matchDate = (l.start_date >= fromDate && l.start_date <= toDate) || (l.end_date >= fromDate && l.end_date <= toDate) || (l.start_date <= fromDate && l.end_date >= toDate);
        } else if(fromDate) {
            matchDate = (l.end_date >= fromDate);
        } else if(toDate) {
            matchDate = (l.start_date <= toDate);
        }

        return matchQuery && matchStatus && matchDate;
    });
    currentPage = 1;
    renderTable();
}

function showAlert(type, message) {
    let color = type === 'success' ? 'alert-success' : 'alert-danger';
    $('#alertBox').html(`<div class="alert ${color} alert-dismissible fade show border-0 shadow-sm" style="border-radius:0;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
}

function loadApprovals() {
    $.get('{{ route("leave.approvals.fetch") }}', function(response) {
        if (response.data) {
            allApprovals = response.data;
            applyFilters();
        }
    });
}

function showReason(reasonStr) {
    $('#fullReasonText').text(reasonStr);
    const modal = new bootstrap.Modal(document.getElementById('reasonModal'));
    modal.show();
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

function truncateString(str, num) {
    if (!str) return '';
    if (str.length <= num) return str;
    return str.slice(0, num) + '...';
}

function renderTable() {
    const total = filteredApprovals.length;
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    const pageData = filteredApprovals.slice(start, end);
    
    let html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="8" class="text-center py-4 text-muted">No pending approvals found.</td></tr>';
    } else {
        pageData.forEach(leave => {
            let badge = 'pending';
            if(leave.status === 'approved') badge = 'approved';
            if(leave.status === 'rejected') badge = 'rejected';

            let typeName = leave.leave_type ? leave.leave_type.name : '-';
            if (leave.is_rh) typeName = 'Restricted Holiday (RH)';
            if (leave.is_sl) typeName = 'Short Leave (SL)';
            if (leave.is_half_day) {
                let sessionName = leave.half_day_period === 'pre_lunch' ? 'Pre Lunch' : 'Post Lunch';
                typeName += ` <span class="badge bg-info text-dark" style="font-size:9px; padding:2px 4px; border-radius:3px;">${sessionName}</span>`;
            }
            
            let reasonHtml = '-';
            if (leave.reason) {
                const escapedReason = escapeHtml(leave.reason);
                if (leave.reason.length > 6) {
                    reasonHtml = `<span class="truncate-reason" onclick="showReason('${escapedReason.replace(/'/g, "\\'").replace(/"/g, "&quot;")}')">${escapeHtml(truncateString(leave.reason, 6))}</span>`;
                } else {
                    reasonHtml = escapedReason;
                }
            }

            html += `<tr>
                <td style="text-align:left;">${leave.user ? leave.user.name : 'Unknown User'}</td>
                <td>${new Date(leave.start_date).toLocaleDateString()}</td>
                <td>${new Date(leave.end_date).toLocaleDateString()}</td>
                <td>${leave.total_days}</td>
                <td>${typeName}</td>
                <td><span class="badge-status badge-${badge}">${(leave.status || 'unknown').toUpperCase()}</span></td>
                <td>${reasonHtml}</td>
                <td class="text-center">
                    ${leave.status === 'pending' ? `
                    <button class="btn-action text-success" title="Approve" onclick="performAction(${leave.id}, 'approve')"><i class="bi bi-check-lg"></i></button>
                    <button class="btn-action text-danger" title="Reject" onclick="performAction(${leave.id}, 'reject')"><i class="bi bi-x-lg"></i></button>
                    ` : '-'}
                </td>
            </tr>`;
        });
    }
    
    $('#approvalsTableBody').html(html);
    $('#leaveRangeInfo').text(`Showing ${total > 0 ? start + 1 : 0}-${end} of ${total} entries`);
    renderPagination(total);
}

function performAction(id, action) {
    if (action === 'reject') {
        $('#reject_leave_id').val(id);
        $('#reject_reason').val('');
        const modal = new bootstrap.Modal(document.getElementById('rejectLeaveModal'));
        modal.show();
        return;
    }

    if(!confirm(`Are you sure you want to ${action} this leave request?`)) return;
    
    $.ajax({
        url: `/leave/approvals/${id}/${action}`,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if(res.success) {
                showAlert('success', res.message);
                loadApprovals();
            } else {
                showAlert('error', res.message);
            }
        },
        error: function(xhr) {
             let msg = 'Error processing request.';
             if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
             showAlert('error', msg);
        }
    });
}

function submitRejection() {
    const id = $('#reject_leave_id').val();
    const reason = $('#reject_reason').val().trim();

    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }

    $.ajax({
        url: `/leave/approvals/${id}/reject`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            reason: reason
        },
        success: function(res) {
            if(res.success) {
                bootstrap.Modal.getInstance(document.getElementById('rejectLeaveModal')).hide();
                showAlert('success', res.message);
                loadApprovals();
            } else {
                showAlert('error', res.message);
            }
        },
        error: function(xhr) {
             let msg = 'Error rejecting leave.';
             if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
             showAlert('error', msg);
        }
    });
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
</script>
@endpush
