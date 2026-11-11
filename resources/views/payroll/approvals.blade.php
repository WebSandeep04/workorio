@extends('layouts.app')

@section('title', 'Payroll Approvals')
@section('page_title', 'Payroll Approvals')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }

  .summary-card {
    background: #ffffff;
    border: 1px solid #eef0f3;
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px rgba(0, 0, 0, 0.04);
  }

  .summary-card.active-card {
    border: 2px solid #434AFA;
    background-color: #f8f9ff;
  }

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
    font-size: 1.15rem;
    color: white;
  }

  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
  .icon-rose { background: linear-gradient(135deg, #ef4444, #f87171); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #4b5563;
  }

  .summary-card-value {
    font-size: 1.15rem;
    font-weight: 700;
    line-height: 1.2;
  }

  /* Filter Box */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 4px;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.25);
  }

  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 11px;
  }

  .filterBox .form-control-modern, .filterBox .form-select-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 11px;
    width: 100%;
  }

  .filterBox .form-control-modern:focus, .filterBox .form-select-modern:focus {
    outline: none;
    background: #fff;
    border-color: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
  }

  /* Table search */
  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
  }

  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Data Table Card */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

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
    font-family: Montserrat;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    white-space: nowrap;
  }

  .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    white-space: nowrap;
  }

  .custom-table tbody tr:hover { background: #f8f9ff; }

  /* Badges */
  .badge-status { font-weight: 600; font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 4px; }
  .badge-pending { background: #fef3c7; color: #d97706; }
  .badge-approved { background: #d1fae5; color: #059669; }
  .badge-rejected { background: #fee2e2; color: #dc2626; }

  /* Action Buttons */
  .btn-approve {
    background: #10B981;
    color: white;
    border: none;
    padding: 0.3rem 0.75rem;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  .btn-approve:hover { background: #059669; color: white; }

  .btn-reject {
    background: #EF4444;
    color: white;
    border: none;
    padding: 0.3rem 0.75rem;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
  }
  .btn-reject:hover { background: #DC2626; color: white; }

  .btn-view-details {
    background: #434AFA;
    color: white;
    border: none;
    padding: 0.3rem 0.75rem;
    border-radius: 3px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .btn-view-details:hover { background: #3538d4; color: white; }

  /* Pagination */
  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
  }
  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">

  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card active-card" data-status="all" onclick="filterByStatus('all', this)">
      <div class="summary-card-icon icon-blue">
        <i class="bi bi-stack"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Batches</div>
        <div class="summary-card-value text-dark" id="countTotal">0</div>
      </div>
    </div>

    <div class="summary-card" data-status="pending" onclick="filterByStatus('pending', this)">
      <div class="summary-card-icon icon-amber">
        <i class="bi bi-hourglass-split"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Pending Approval</div>
        <div class="summary-card-value text-warning" id="countPending">0</div>
      </div>
    </div>

    <div class="summary-card" data-status="approved" onclick="filterByStatus('approved', this)">
      <div class="summary-card-icon icon-green">
        <i class="bi bi-check-circle"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Approved</div>
        <div class="summary-card-value text-success" id="countApproved">0</div>
      </div>
    </div>

    <div class="summary-card" data-status="rejected" onclick="filterByStatus('rejected', this)">
      <div class="summary-card-icon icon-rose">
        <i class="bi bi-x-circle"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Rejected</div>
        <div class="summary-card-value text-danger" id="countRejected">0</div>
      </div>
    </div>
  </div>

  <!-- Filter Box -->
  <div class="filterBox">
    <div class="d-flex flex-column">
      <label class="form-label-modern"><i class="bi bi-calendar-event"></i> Year</label>
      <select class="form-select-modern" id="filterYear">
        <option value="">All Years</option>
        @foreach($years as $yr)
          <option value="{{ $yr }}" {{ $yr == date('Y') ? 'selected' : '' }}>{{ $yr }}</option>
        @endforeach
      </select>
    </div>

    <div class="d-flex flex-column">
      <label class="form-label-modern"><i class="bi bi-calendar-month"></i> Month</label>
      <select class="form-select-modern" id="filterMonth">
        <option value="">All Months</option>
        @for($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
        @endfor
      </select>
    </div>

    <div class="d-flex flex-column">
      <label class="form-label-modern"><i class="bi bi-filter-circle"></i> Status</label>
      <select class="form-select-modern" id="filterStatus">
        <option value="all">All Status</option>
        <option value="pending">Pending Approval</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>
  </div>

  <!-- Table Card -->
  <div class="modern-card data-table-card">
    <div class="modern-card-body p-0">
      <div class="table-scroll">
        <table class="table custom-table" id="approvalsTable">
          <thead>
            <tr>
              <th>Period</th>
              <th>Employees Processed</th>
              <th>Gross Salary</th>
              <th>Total Deductions</th>
              <th>Net Salary</th>
              <th>Status</th>
              <th>Approved By / On</th>
              <th>Rejection Reason</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="approvalsTableBody">
            <tr><td colspan="9" class="text-center py-4 text-muted">Loading payroll approvals...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-2 px-1">
    <div class="text-muted small" id="rangeInfo">Showing 0-0 from 0 data</div>
    <ul class="pagination mb-0" id="paginationLinks"></ul>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-x-circle me-1"></i> Reject Payroll Batch</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="rejectPayrollId">
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Reason for Rejection <span class="text-danger">*</span></label>
          <textarea class="form-control" id="rejectReason" rows="3" placeholder="Provide reason for rejecting this payroll..."></textarea>
          <div class="invalid-feedback d-none" id="rejectError">Please provide a reason.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmRejectBtn">Confirm Rejection</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
let currentPage = 1;

function showAlert(type, message) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const alertHtml = `
    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;
  $('body').append(alertHtml);
  setTimeout(() => { $('.alert').alert('close'); }, 4000);
}

function formatCurrency(amount) {
  return '₹' + parseFloat(amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fetchApprovals(page = 1) {
  currentPage = page;
  const year = $('#filterYear').val();
  const month = $('#filterMonth').val();
  const status = $('#filterStatus').val();

  $('#approvalsTableBody').html(`
    <tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-arrow-repeat spin me-2"></i>Loading payroll approvals...</td></tr>
  `);

  $.ajax({
    url: "{{ route('payroll.approvals.fetch') }}",
    type: "GET",
    data: { page, year, month, status },
    success: function(res) {
      if (!res.success) return;

      // Update counters
      if (res.counts) {
        $('#countTotal').text(res.counts.total || 0);
        $('#countPending').text(res.counts.pending || 0);
        $('#countApproved').text(res.counts.approved || 0);
        $('#countRejected').text(res.counts.rejected || 0);
      }

      if (!res.data || res.data.length === 0) {
        $('#approvalsTableBody').html(`
          <tr><td colspan="9" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No payroll batches found for selected criteria.</td></tr>
        `);
        $('#paginationLinks').empty();
        $('#rangeInfo').text('Showing 0-0 from 0 data');
        return;
      }

      let rows = '';
      res.data.forEach(p => {
        const rawStatus = (p.status || 'Draft').toLowerCase();
        let statusBadge = '<span class="badge-status badge-pending">Draft / Pending</span>';
        if (rawStatus === 'approved' || rawStatus === 'finalized') {
          statusBadge = '<span class="badge-status badge-approved">Approved</span>';
        } else if (rawStatus === 'rejected') {
          statusBadge = '<span class="badge-status badge-rejected">Rejected</span>';
        }

        let approvedInfo = '-';
        if (p.approved_at) {
          const approverName = p.approver ? p.approver.name : 'Admin';
          const approvedDate = new Date(p.approved_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
          approvedInfo = `<div class="fw-bold">${approverName}</div><div class="text-muted" style="font-size:11px;">${approvedDate}</div>`;
        }

        let actions = `
          <a href="{{ url('payroll/process') }}/${p.id}" class="btn-view-details me-1" title="View Full Report">
            <i class="bi bi-eye"></i> View
          </a>
        `;

        if (rawStatus !== 'approved' && rawStatus !== 'finalized') {
          actions += `
            <button class="btn-approve me-1" onclick="approvePayroll(${p.id}, '${monthNames[p.month]} ${p.year}', this)">
              <i class="bi bi-check-lg"></i> Approve
            </button>
            <button class="btn-reject" onclick="openRejectModal(${p.id})">
              <i class="bi bi-x-lg"></i> Reject
            </button>
          `;
        }

        rows += `
          <tr>
            <td><strong>${monthNames[p.month] || 'Month ' + p.month} ${p.year}</strong></td>
            <td>${p.details_count || 0} Employees</td>
            <td class="fw-semibold">${formatCurrency(p.total_gross)}</td>
            <td class="text-danger fw-semibold">${formatCurrency(p.total_deductions)}</td>
            <td class="text-success fw-bold">${formatCurrency(p.total_net)}</td>
            <td>${statusBadge}</td>
            <td>${approvedInfo}</td>
            <td class="text-danger small" style="max-width: 180px; overflow: hidden; text-overflow: ellipsis;">${p.rejection_reason || '-'}</td>
            <td class="text-center">${actions}</td>
          </tr>
        `;
      });

      $('#approvalsTableBody').html(rows);
      $('#rangeInfo').text(`Showing ${res.from || 0}-${res.to || 0} from ${res.total || 0} data`);
      renderPagination(res.current_page, res.last_page);
    },
    error: function() {
      $('#approvalsTableBody').html(`
        <tr><td colspan="9" class="text-center text-danger py-4">Failed to load payroll approvals.</td></tr>
      `);
    }
  });
}

function filterByStatus(status, element) {
  $('.summary-card').removeClass('active-card');
  $(element).addClass('active-card');
  $('#filterStatus').val(status);
  fetchApprovals(1);
}

function renderPagination(current, last) {
  let html = '';
  if (last <= 1) {
    $('#paginationLinks').html('');
    return;
  }

  html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
    <a class="page-link" href="#" onclick="fetchApprovals(${current - 1}); return false;">Previous</a>
  </li>`;

  for (let i = 1; i <= last; i++) {
    html += `<li class="page-item ${i === current ? 'active' : ''}">
      <a class="page-link" href="#" onclick="fetchApprovals(${i}); return false;">${i}</a>
    </li>`;
  }

  html += `<li class="page-item ${current === last ? 'disabled' : ''}">
    <a class="page-link" href="#" onclick="fetchApprovals(${current + 1}); return false;">Next</a>
  </li>`;

  $('#paginationLinks').html(html);
}

function approvePayroll(id, period, btnEl) {
  if (!confirm(`Are you sure you want to approve payroll for ${period}?`)) {
    return;
  }

  const $btn = $(btnEl);
  $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i>');

  $.ajax({
    url: `{{ url('payroll/approvals') }}/${id}/approve`,
    type: 'POST',
    data: { _token: '{{ csrf_token() }}' },
    success: function(res) {
      if (res.success) {
        showAlert('success', res.message);
        fetchApprovals(currentPage);
      } else {
        showAlert('error', res.message);
        $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Approve');
      }
    },
    error: function(xhr) {
      const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error approving payroll.';
      showAlert('error', msg);
      $btn.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Approve');
    }
  });
}

function openRejectModal(id) {
  $('#rejectPayrollId').val(id);
  $('#rejectReason').val('');
  $('#rejectError').addClass('d-none');
  $('#rejectModal').modal('show');
}

$('#confirmRejectBtn').on('click', function() {
  const id = $('#rejectPayrollId').val();
  const reason = $('#rejectReason').val().trim();

  if (!reason) {
    $('#rejectError').removeClass('d-none');
    return;
  }

  const $btn = $(this);
  $btn.prop('disabled', true).text('Rejecting...');

  $.ajax({
    url: `{{ url('payroll/approvals') }}/${id}/reject`,
    type: 'POST',
    data: { _token: '{{ csrf_token() }}', reason },
    success: function(res) {
      $('#rejectModal').modal('hide');
      $btn.prop('disabled', false).text('Confirm Rejection');
      if (res.success) {
        showAlert('success', res.message);
        fetchApprovals(currentPage);
      } else {
        showAlert('error', res.message);
      }
    },
    error: function(xhr) {
      $btn.prop('disabled', false).text('Confirm Rejection');
      const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error rejecting payroll.';
      showAlert('error', msg);
    }
  });
});

$(document).ready(function() {
  $('#filterYear, #filterMonth, #filterStatus').on('change', function() {
    fetchApprovals(1);
  });

  fetchApprovals(1);
});
</script>
@endpush

