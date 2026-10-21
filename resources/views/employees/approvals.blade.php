@extends('layouts.app')

@section('title', 'Employee Approvals')
@section('page_title', 'Employee Approvals')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

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
    cursor: pointer;
  }
  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }
  .summary-card.active-tab {
    border: 2px solid #434AFA;
  }
  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
  }
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
  .icon-green { background: linear-gradient(135deg, #34d399, #10b981); color: white; }
  .icon-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
  .icon-red { background: linear-gradient(135deg, #fb7185, #f43f5e); color: white; }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }
  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    line-height: 1.2;
  }
  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    color: #101828;
  }

  /* Table Search */
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Table Styles */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
  
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; table-layout: auto; font-family: Montserrat; }
  .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; text-align: left; vertical-align: middle; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; font-weight: 600; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; font-weight: 600; }
  .badge-rejected { background: #fee2e2; color: #b91c1c; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; font-weight: 600; }
  
  .btn-approve { background: #16a34a; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; transition: all 0.2s ease; }
  .btn-approve:hover { background: #15803d; color: white; }
  .btn-approve:disabled, .btn-reject:disabled { opacity: 0.65; cursor: not-allowed; }
  .btn-reject { background: #dc2626; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 600; transition: all 0.2s ease; }
  .btn-reject:hover { background: #b91c1c; color: white; }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .spin { display: inline-block; animation: spin 1s linear infinite; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>

    <!-- Summary Cards -->
    <div class="summary-cards mb-3 mt-1">
        <div class="summary-card" data-status="all" onclick="setFilterStatus('all', this)">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-people"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Employees</div>
                <div class="summary-card-value" id="countTotal">0</div>
            </div>
        </div>

        <div class="summary-card active-tab" data-status="pending" onclick="setFilterStatus('pending', this)">
            <div class="summary-card-icon icon-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Pending Approval</div>
                <div class="summary-card-value" id="countPending">0</div>
            </div>
        </div>

        <div class="summary-card" data-status="approved" onclick="setFilterStatus('approved', this)">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Approved</div>
                <div class="summary-card-value" id="countApproved">0</div>
            </div>
        </div>

        <div class="summary-card" data-status="rejected" onclick="setFilterStatus('rejected', this)">
            <div class="summary-card-icon icon-red">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Rejected</div>
                <div class="summary-card-value" id="countRejected">0</div>
            </div>
        </div>
    </div>

    <!-- Table Search & Status Filter Row -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="employeeSearch" placeholder="Search by employee name..." />
        </div>
        <div style="min-width: 170px;">
            <select class="form-select form-select-sm" id="filterStatus" style="font-size: 0.82rem; height: 35px; border: 1px solid #e5e7eb; border-radius: 2px;">
                <option value="all">All Status</option>
                <option value="pending" selected>Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="approvalsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Employee</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Approved By / On</th>
                            <th>Rejection Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="approvalsTableBody">
                        <tr><td colspan="10" class="text-center py-4 text-muted">Loading approvals...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title fw-bold"><i class="bi bi-x-circle me-1"></i>Reject Employee</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rejectEmployeeId">
                <p class="mb-2">Please specify the reason for rejecting <strong id="rejectEmployeeName"></strong>:</p>
                <textarea id="rejectReason" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmReject" onclick="submitRejection()">Confirm Reject</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allEmployees = [];
let currentFilter = 'pending';
const csrfToken = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    $('#filterStatus').val('pending');
    loadApprovals();

    $('#employeeSearch').on('keyup', filterLocal);
    $('#filterStatus').on('change', function() {
        setFilterStatus($(this).val());
    });
});

function setFilterStatus(status, el) {
    currentFilter = status;
    $('#filterStatus').val(status);
    $('.summary-card').removeClass('active-tab');
    if (el) {
        $(el).addClass('active-tab');
    } else {
        $(`.summary-card[data-status="${status}"]`).addClass('active-tab');
    }
    loadApprovals();
}

function loadApprovals() {
    $('#approvalsTableBody').html('<tr><td colspan="10" class="text-center py-4 text-muted">Loading approvals...</td></tr>');

    $.get("{{ route('employees.approvals.fetch') }}", { status: currentFilter }, function(res) {
        if (res.success) {
            allEmployees = res.data || [];
            if (res.counts) {
                $('#countTotal').text(res.counts.total || 0);
                $('#countPending').text(res.counts.pending || 0);
                $('#countApproved').text(res.counts.approved || 0);
                $('#countRejected').text(res.counts.rejected || 0);
            }
            renderTable(allEmployees);
        } else {
            $('#approvalsTableBody').html('<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
        }
    }).fail(function() {
        $('#approvalsTableBody').html('<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
    });
}

function filterLocal() {
    const q = ($('#employeeSearch').val() || '').toLowerCase();
    const filtered = allEmployees.filter(emp => {
        return (emp.name && emp.name.toLowerCase().includes(q)) ||
               (emp.employee_code && emp.employee_code.toLowerCase().includes(q)) ||
               (emp.email && emp.email.toLowerCase().includes(q)) ||
               (emp.phone && emp.phone.toLowerCase().includes(q));
    });
    renderTable(filtered);
}

function renderTable(rows) {
    if (!rows || rows.length === 0) {
        $('#approvalsTableBody').html('<tr><td colspan="10" class="text-center py-4 text-muted">No requests found.</td></tr>');
        return;
    }

    let html = '';
    rows.forEach(emp => {
        const appStatus = (emp.approval_status || 'pending').toLowerCase();
        let badgeHtml = '';
        if (appStatus === 'approved') {
            badgeHtml = '<span class="badge-active">Approved</span>';
        } else if (appStatus === 'rejected') {
            badgeHtml = '<span class="badge-rejected">Rejected</span>';
        } else {
            badgeHtml = '<span class="badge-pending">Pending</span>';
        }

        let approvedInfo = '-';
        if (emp.approval_status === 'approved') {
            const approverName = emp.approver ? emp.approver.name : 'Admin';
            const dateStr = emp.approved_at ? new Date(emp.approved_at).toLocaleDateString() : '';
            approvedInfo = `<div class="fw-bold">${approverName}</div><div class="text-muted" style="font-size:10px;">${dateStr}</div>`;
        }

        let actionBtns = '-';
        if (appStatus !== 'approved') {
            actionBtns = `
                <button class="btn-approve me-1" id="btn-approve-${emp.id}" onclick="approveEmployee(${emp.id}, '${escapeHtml(emp.name)}', this)">Approve</button>
                <button class="btn-reject" id="btn-reject-${emp.id}" onclick="openRejectModal(${emp.id}, '${escapeHtml(emp.name)}')">Reject</button>
            `;
        }

        html += `
            <tr>
                <td style="vertical-align: middle;"><strong>${escapeHtml(emp.employee_code || '')}</strong></td>
                <td style="vertical-align: middle;">
                    <strong>${escapeHtml(emp.name || '')}</strong>
                    <div class="text-muted" style="font-size:11px;">${escapeHtml(emp.email || '')}</div>
                </td>
                <td style="vertical-align: middle;">${escapeHtml(emp.phone || '-')}</td>
                <td style="vertical-align: middle;">${escapeHtml((emp.department_relation && emp.department_relation.name) || emp.department || '-')}</td>
                <td style="vertical-align: middle;">${escapeHtml((emp.designation_relation && emp.designation_relation.title) || emp.designation || '-')}</td>
                <td style="vertical-align: middle;">${emp.date_of_joining ? emp.date_of_joining.toString().substring(0, 10) : '-'}</td>
                <td style="vertical-align: middle;">${badgeHtml}</td>
                <td style="vertical-align: middle;">${approvedInfo}</td>
                <td style="vertical-align: middle;" class="text-danger small">${escapeHtml(emp.rejection_reason || '-')}</td>
                <td style="vertical-align: middle;">${actionBtns}</td>
            </tr>
        `;
    });

    $('#approvalsTableBody').html(html);
}

function approveEmployee(id, name, btnEl) {
    const $btn = $(btnEl);
    const $rejectBtn = $(`#btn-reject-${id}`);
    const originalText = $btn.html();

    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-1"></i>Approving...');
    $rejectBtn.prop('disabled', true);

    $.post(`{{ url('/employees') }}/${id}/approve`, { _token: csrfToken }, function(res) {
        if (res.success) {
            showAlert('success', res.message);
            loadApprovals();
        } else {
            showAlert('danger', res.message || 'Approval failed.');
            $btn.prop('disabled', false).html(originalText);
            $rejectBtn.prop('disabled', false);
        }
    }).fail(function(xhr) {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error approving employee.';
        showAlert('danger', msg);
        $btn.prop('disabled', false).html(originalText);
        $rejectBtn.prop('disabled', false);
    });
}

function openRejectModal(id, name) {
    $('#rejectEmployeeId').val(id);
    $('#rejectEmployeeName').text(name);
    $('#rejectReason').val('');
    $('#btnConfirmReject').prop('disabled', false).html('Confirm Reject');
    new bootstrap.Modal('#rejectModal').show();
}

function submitRejection() {
    const id = $('#rejectEmployeeId').val();
    const reason = $('#rejectReason').val().trim();
    const $submitBtn = $('#btnConfirmReject');
    const originalText = $submitBtn.html();

    if (!reason) {
        alert('Please provide a rejection reason.');
        return;
    }

    $submitBtn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-1"></i>Rejecting...');

    $.post(`{{ url('/employees') }}/${id}/reject`, { _token: csrfToken, reason: reason }, function(res) {
        if (res.success) {
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            showAlert('success', res.message);
            loadApprovals();
        } else {
            showAlert('danger', res.message || 'Rejection failed.');
            $submitBtn.prop('disabled', false).html(originalText);
        }
    }).fail(function(xhr) {
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error rejecting employee.';
        showAlert('danger', msg);
        $submitBtn.prop('disabled', false).html(originalText);
    });
}

function showAlert(type, msg) {
    let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`;
    $('#alertBox').html(alertHtml);
    setTimeout(() => { $('#alertBox .alert').alert('close'); }, 5000);
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
@endpush
