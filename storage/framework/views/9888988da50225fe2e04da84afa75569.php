

<?php $__env->startSection('title', 'Financial Request Approval'); ?>
<?php $__env->startSection('page_title', 'Financial Request Approval'); ?>

<?php $__env->startPush('styles'); ?>
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
  }
  .filterBox .form-control-modern, .filterBox .form-select-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    font-size: 10px;
    width: 100%;
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
  
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; table-layout: auto; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; text-align: left; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-skipped { background: #e0e7ff; color: #3730a3; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  
  .btn-approve { background: #16a34a; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-approve:hover { background: #15803d; }
  .btn-reject { background: #dc2626; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-reject:hover { background: #b91c1c; }

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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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
            <label class="form-label-modern"><i class="bi bi-tags"></i> Type</label>
            <select class="form-select-modern" id="filterType">
                <option value="">All Types</option>
                <option value="loan">Loan</option>
                <option value="advance">Advance</option>
            </select>
        </div>
    </div>

    <div id="financialSummary" class="summary-cards d-none mb-3 mt-3"></div>

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
                <table class="table custom-table" id="approvalsTable">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Details</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="approvalsTableBody">
                        <tr><td colspan="7" class="text-center py-4 text-muted">Loading approvals...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let allRequests = [];

    function fetchLoans() {
        $('#approvalsTableBody').html('<tr><td colspan="7" class="text-center py-4 text-muted">Loading approvals...</td></tr>');
        
        $.ajax({
            url: "<?php echo e(route('loans.admin.fetch')); ?>",
            type: "POST",
            success: function(res) {
                allRequests = res.data || [];
                renderTable();
            },
            error: function(err) {
                $('#approvalsTableBody').html('<tr><td colspan="7" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
            }
        });
    }

    function renderTable() {
        let searchTerm = $('#loanSearch').val().toLowerCase();
        let statusFilter = $('#filterStatus').val();
        let typeFilter = $('#filterType').val();

        let filtered = allRequests.filter(req => {
            let matchSearch = true;
            let matchStatus = true;
            let matchType = true;

            if(searchTerm) {
                let empName = (req.employee && req.employee.name) ? req.employee.name.toLowerCase() : '';
                matchSearch = empName.includes(searchTerm);
            }

            if(statusFilter) {
                matchStatus = req.status === statusFilter;
            }

            if(typeFilter) {
                matchType = req.request_type === typeFilter;
            }

            return matchSearch && matchStatus && matchType;
        });

        let totalLoan = 0;
        let approvedLoan = 0;
        let rejectedLoan = 0;
        let totalAdv = 0;
        let approvedAdv = 0;
        let rejectedAdv = 0;

        // Compute metrics based on all requests (unfiltered by status) but applying search term if needed
        // Actually, it's better to just show global stats for the whole table
        allRequests.forEach(req => {
            let amt = parseFloat(req.amount) || 0;
            if (req.request_type === 'loan') {
                totalLoan += amt;
                if (req.status === 'approved') approvedLoan += amt;
                if (req.status === 'rejected') rejectedLoan += amt;
            } else {
                totalAdv += amt;
                if (req.status === 'approved') approvedAdv += amt;
                if (req.status === 'rejected') rejectedAdv += amt;
            }
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
                
                let typeBadge = req.request_type === 'loan' ? '<span class="badge bg-primary" style="font-size: 0.75rem;">Loan</span>' : '<span class="badge bg-info text-dark" style="font-size: 0.75rem;">Advance</span>';
                
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

                // Action Buttons
                let actionHtml = '-';
                if(req.status === 'pending') {
                    actionHtml = `
                        <button class="btn-approve me-1 update-status" data-id="${req.id}" data-type="${req.request_type}" data-status="approved">Approve</button>
                        <button class="btn-reject update-status" data-id="${req.id}" data-type="${req.request_type}" data-status="rejected">Reject</button>
                    `;
                }

                html += `
                    <tr>
                        <td style="vertical-align: top;">${typeBadge}</td>
                        <td style="vertical-align: top;"><strong>${empName}</strong></td>
                        <td style="vertical-align: top;">${parseFloat(req.amount).toFixed(2)}</td>
                        <td style="vertical-align: top;">${details}</td>
                        <td style="vertical-align: top;">${rem}</td>
                        <td style="vertical-align: top;"><span class="${badgeClass}">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span></td>
                        <td>${actionHtml}</td>
                    </tr>
                `;
            });
        }

        $('#approvalsTableBody').html(html);

        $('#financialSummary').html(`
            <!-- Loans -->
            <div class="summary-card">
                <div class="summary-card-icon icon-blue">
                    <i class="bi bi-bank"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total Loan</div>
                    <div class="summary-card-value">${totalLoan.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Approved Loan</div>
                    <div class="summary-card-value">${approvedLoan.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-red">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Rejected Loan</div>
                    <div class="summary-card-value">${rejectedLoan.toFixed(2)}</div>
                </div>
            </div>

            <!-- Advances -->
            <div class="summary-card">
                <div class="summary-card-icon icon-purple">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total Advance</div>
                    <div class="summary-card-value">${totalAdv.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-green">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Approved Advance</div>
                    <div class="summary-card-value">${approvedAdv.toFixed(2)}</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-red">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Rejected Advance</div>
                    <div class="summary-card-value">${rejectedAdv.toFixed(2)}</div>
                </div>
            </div>
        `).removeClass('d-none');
    }

    fetchLoans();

    $('#loanSearch').on('keyup', renderTable);
    $('#filterStatus').on('change', renderTable);
    $('#filterType').on('change', renderTable);

    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        
        let status = $(this).data('status');
        let id = $(this).data('id');
        let type = $(this).data('type');
        let actionWord = status === 'approved' ? 'approve' : 'reject';

        if(!confirm(`Are you sure you want to ${actionWord} this ${type}?`)) return;
        
        let btn = $(this);
        let ogText = btn.html();
        btn.html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);

        let url = type === 'loan' ? `/admin/loans/${id}/status` : `/admin/salary-advances/${id}/status`;

        $.ajax({
            url: url,
            type: 'POST',
            data: { status: status },
            success: function(res) {
                if(res.success) {
                    showAlert('success', res.message);
                    fetchLoans();
                } else {
                    showAlert('danger', res.message || 'Error occurred');
                    btn.html(ogText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                let msg = `Failed to ${actionWord} loan.`;
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showAlert('danger', msg);
                btn.html(ogText).prop('disabled', false);
            }
        });
    });

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/loans/admin_index.blade.php ENDPATH**/ ?>