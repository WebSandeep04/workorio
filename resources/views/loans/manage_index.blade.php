@extends('layouts.app')

@section('title', 'Manage Financial Requests (Admin)')
@section('page_title', 'Manage Financial Requests (Admin)')

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
  
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; table-layout: auto; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.8rem; font-weight: 600; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; text-align: left; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-skipped { background: #e0e7ff; color: #3730a3; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  
  .btn-skip { background: #fb923c; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-skip:hover { background: #ea580c; }

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
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3 border-bottom-0" id="manageTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="loans-tab" data-bs-toggle="tab" data-bs-target="#loans" type="button" role="tab" style="color: #434AFA; font-weight: 600; font-size: 0.9rem;">Loans</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="advances-tab" data-bs-toggle="tab" data-bs-target="#advances" type="button" role="tab" style="color: #4b5563; font-weight: 600; font-size: 0.9rem;">Salary Advances</button>
      </li>
    </ul>

    <div id="financialSummary" class="summary-cards d-none mb-3"></div>

    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="loanSearch" placeholder="Search by employee name..." />
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="manageTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Details</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="manageTableBody">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Loading requests...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let allRequests = [];
    let activeTab = 'loan';

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        activeTab = e.target.id === 'loans-tab' ? 'loan' : 'advance';
        renderTable();
    });

    function fetchLoans() {
        $('#manageTableBody').html('<tr><td colspan="7" class="text-center py-4 text-muted">Loading requests...</td></tr>');
        
        $.ajax({
            url: "{{ route('loans.admin.fetch') }}",
            type: "POST",
            success: function(res) {
                allRequests = res.data || [];
                renderTable();
            },
            error: function(err) {
                $('#manageTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
            }
        });
    }

    function renderTable() {
        let searchTerm = $('#loanSearch').val().toLowerCase();

        let filtered = allRequests.filter(req => {
            // Filter by Active Tab
            if (req.request_type !== activeTab) return false;

            // Only show loans that are not pending/rejected (i.e. approved or completed)
            if (req.status === 'pending' || req.status === 'rejected') return false;

            let matchSearch = true;

            if(searchTerm) {
                let empName = (req.employee && req.employee.name) ? req.employee.name.toLowerCase() : '';
                matchSearch = empName.includes(searchTerm);
            }

            return matchSearch;
        });

        let totalAmount = 0;
        let totalRemaining = 0;
        let totalPaid = 0;
        
        filtered.forEach(req => {
            let amt = parseFloat(req.amount) || 0;
            let rem = parseFloat(req.remaining_balance) || 0;
            let paid = amt - rem;
            if(paid < 0) paid = 0;

            totalAmount += amt;
            totalRemaining += rem;
            totalPaid += paid;
        });

        let html = '';
        if(filtered.length === 0) {
            html = '<tr><td colspan="7" class="text-center py-4 text-muted">No requests found.</td></tr>';
        } else {
            filtered.forEach(req => {
                let empName = (req.employee && req.employee.name) ? req.employee.name : 'N/A';
                
                let badgeClass = 'badge-pending';
                if(req.status === 'approved') badgeClass = 'badge-active';
                if(req.status === 'rejected') badgeClass = 'text-danger fw-bold';

                let rem = req.remaining_balance !== undefined ? parseFloat(req.remaining_balance).toFixed(2) : '0.00';
                
                let details = '';
                if (req.request_type === 'loan') {
                    details = req.total_installments + ' EMIs';
                } else {
                    let formattedMonth = req.deduction_start_month;
                    if(formattedMonth && formattedMonth.includes('-')) {
                        let parts = formattedMonth.split('-');
                        let months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
                        if(parts.length >= 2) formattedMonth = months[parseInt(parts[1], 10) - 1] + '-' + parts[0];
                    }
                    details = 'Start: ' + (formattedMonth || 'N/A');
                }

                let actionHtml = '';
                if (req.request_type === 'loan') {
                    actionHtml = `
                        <a href="/admin/loans/${req.id}/installments" class="btn btn-sm" title="View Installments" style="background: #434AFA; color: white; border: none; font-size: 0.8rem; border-radius: 4px; text-decoration: none;">
                            <i class="bi bi-eye"></i> View
                        </a>`;
                } else {
                    actionHtml = `<span class="text-muted" style="font-size: 0.8rem;">N/A</span>`;
                }

                html += `
                    <tr>
                        <td style="vertical-align: middle;"><strong>${empName}</strong></td>
                        <td style="vertical-align: middle;">${parseFloat(req.amount).toFixed(2)}</td>
                        <td style="vertical-align: middle;">${details}</td>
                        <td style="vertical-align: middle;">${rem}</td>
                        <td style="vertical-align: middle;"><span class="${badgeClass}">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span></td>
                        <td style="vertical-align: middle;">${actionHtml}</td>
                    </tr>
                `;
            });
        }

        $('#manageTableBody').html(html);

        let typeLabel = activeTab === 'loan' ? 'Loan' : 'Adv.';
        let iconMain = activeTab === 'loan' ? 'icon-blue' : 'icon-purple';

        $('#financialSummary').html(`
            <div class="summary-card">
                <div class="summary-card-icon ${iconMain}">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total ${typeLabel}</div>
                    <div class="summary-card-value">${totalAmount.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Paid ${typeLabel}</div>
                    <div class="summary-card-value">${totalPaid.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-orange">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Rem. ${typeLabel}</div>
                    <div class="summary-card-value">${totalRemaining.toFixed(2)}</div>
                </div>
            </div>
        `).removeClass('d-none');
    }

    fetchLoans();

    $('#loanSearch').on('keyup', renderTable);

    function showAlert(type, msg) {
        let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        $('#alertBox').html(alertHtml);
        setTimeout(() => { $('#alertBox .alert').alert('close'); }, 5000);
    }
});
</script>
@endpush
