@extends('layouts.app')

@section('title', 'Leave Approvals')
@section('page_title', 'Leave Approvals')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

  /* Table Search */
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

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
  
  .badge-status { font-weight: 600; padding: 0.25rem 0.6rem; border-radius: 12px; font-size: 0.75rem; }
  .badge-pending { background: #fff3cd; color: #856404; }
  .badge-approved { background: #d4edda; color: #155724; }
  .badge-rejected { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
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
                            <th>Period</th>
                            <th>Days</th>
                            <th>Leave Type</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="approvalsTableBody">
                        <tr><td colspan="7" class="text-center py-4 text-muted">Loading leave approvals...</td></tr>
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
    
    $('#leaveSearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        filteredApprovals = allApprovals.filter(l => {
            return (l.user?.name || '').toLowerCase().includes(query) ||
                   (l.leave_type?.name || '').toLowerCase().includes(query) ||
                   (l.reason || '').toLowerCase().includes(query) ||
                   (l.status || '').toLowerCase().includes(query);
        });
        currentPage = 1;
        renderTable();
    });
});

function showAlert(type, message) {
    let color = type === 'success' ? 'alert-success' : 'alert-danger';
    $('#alertBox').html(`<div class="alert ${color} alert-dismissible fade show border-0 shadow-sm" style="border-radius:0;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
}

function loadApprovals() {
    $.get('{{ route("leave.approvals.fetch") }}', function(response) {
        if (response.data) {
            allApprovals = response.data;
            filteredApprovals = [...allApprovals];
            currentPage = 1;
            renderTable();
        }
    });
}

function renderTable() {
    const total = filteredApprovals.length;
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    const pageData = filteredApprovals.slice(start, end);
    
    let html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="7" class="text-center py-4 text-muted">No pending approvals found.</td></tr>';
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
                typeName += ` <span class="badge bg-info" style="font-size:9px; padding:2px 4px; border-radius:3px;">${sessionName}</span>`;
            }
            
            let timeStr = '';
            if (leave.is_sl && leave.start_time) {
                 timeStr = `<br><span class="badge bg-light text-dark border mt-1"><i class="bi bi-clock me-1"></i>${leave.start_time.substring(0,5)} to ${leave.end_time.substring(0,5)}</span>`;
            }



            html += `<tr>
                <td><strong>${leave.user ? leave.user.name : 'Unknown User'}</strong></td>
                <td><strong>${new Date(leave.start_date).toLocaleDateString()}</strong> ${leave.start_date !== leave.end_date ? `to <strong>${new Date(leave.end_date).toLocaleDateString()}</strong>` : ''} ${timeStr}</td>
                <td><span style="background:#e2e8f0; padding:2px 6px; border-radius:4px; font-weight:700;">${leave.total_days}</span></td>
                <td>${typeName}</td>
                <td><span class="badge-status badge-${badge}">${(leave.status || 'unknown').toUpperCase()}</span></td>
                <td>${leave.reason || '-'}</td>
                <td class="text-center">
                    ${leave.status === 'pending' ? `
                    <button class="btn btn-sm btn-success me-1 px-2 py-1" onclick="performAction(${leave.id}, 'approve')" title="Approve"><i class="bi bi-check-lg"></i></button>
                    <button class="btn btn-sm btn-danger px-2 py-1" onclick="performAction(${leave.id}, 'reject')" title="Reject"><i class="bi bi-x-lg"></i></button>
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
