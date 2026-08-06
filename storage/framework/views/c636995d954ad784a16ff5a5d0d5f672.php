<?php $__env->startSection('title', 'Advance Approvals'); ?>
<?php $__env->startSection('page_title', 'Advance Approvals'); ?>

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
  .filterBox .form-label-modern { color: #fff; font-weight: 600; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem; font-size: 10px; }
  .filterBox .form-control-modern, .filterBox .form-select-modern { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 2px; padding: 0.35rem 0.5rem; background: rgba(255, 255, 255, 0.98); color: #000; font-size: 10px; width: 100%; }

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
  
  .btn-approve { background: #16a34a; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-approve:hover { background: #15803d; }
  .btn-reject { background: #dc2626; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-reject:hover { background: #b91c1c; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
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
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="advSearch" placeholder="Search by employee name..." />
        </div>
    </div>

    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="approvalsTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Start Month</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="approvalsTableBody">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Loading advance approvals...</td></tr>
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
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    let allAdvs = [];

    function fetchAdvances() {
        $('#approvalsTableBody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Loading advance approvals...</td></tr>');
        $.ajax({
            url: "<?php echo e(route('salary_advances.admin.fetch')); ?>",
            type: "POST",
            success: function(res) {
                allAdvs = res.data || [];
                renderTable();
            },
            error: function() {
                $('#approvalsTableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
            }
        });
    }

    function renderTable() {
        let searchTerm = $('#advSearch').val().toLowerCase();
        let statusFilter = $('#filterStatus').val();

        let filtered = allAdvs.filter(adv => {
            let empName = (adv.employee && adv.employee.name) ? adv.employee.name.toLowerCase() : '';
            return (!searchTerm || empName.includes(searchTerm)) && (!statusFilter || adv.status === statusFilter);
        });

        let html = '';
        if(filtered.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-4 text-muted">No advances found.</td></tr>';
        } else {
            filtered.forEach(adv => {
                let empName = (adv.employee && adv.employee.name) ? adv.employee.name : 'N/A';
                
                let badgeClass = 'badge-pending';
                if(adv.status === 'approved' || adv.status === 'completed') badgeClass = 'badge-active';
                if(adv.status === 'rejected') badgeClass = 'text-danger fw-bold';
                
                let actionHtml = '-';
                if(adv.status === 'pending') {
                    actionHtml = `
                        <button class="btn-approve me-1 update-status" data-id="${adv.id}" data-status="approved">Approve</button>
                        <button class="btn-reject update-status" data-id="${adv.id}" data-status="rejected">Reject</button>
                    `;
                }

                html += `
                    <tr>
                        <td><strong>${empName}</strong></td>
                        <td>$${parseFloat(adv.amount).toFixed(2)}</td>
                        <td>${adv.deduction_start_month}</td>
                        <td>${adv.reason || '-'}</td>
                        <td><span class="${badgeClass}">${adv.status.charAt(0).toUpperCase() + adv.status.slice(1)}</span></td>
                        <td>${actionHtml}</td>
                    </tr>
                `;
            });
        }
        $('#approvalsTableBody').html(html);
    }

    fetchAdvances();
    $('#advSearch').on('keyup', renderTable);
    $('#filterStatus').on('change', renderTable);

    $(document).on('click', '.update-status', function(e) {
        e.preventDefault();
        
        let status = $(this).data('status');
        let id = $(this).data('id');
        let actionWord = status === 'approved' ? 'approve' : 'reject';

        if(!confirm(`Are you sure you want to ${actionWord} this advance?`)) return;
        
        let btn = $(this);
        let ogText = btn.html();
        btn.html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);

        $.ajax({
            url: `/admin/salary-advances/${id}/status`,
            type: 'POST',
            data: { status: status },
            success: function(res) {
                if(res.success) {
                    showAlert('success', res.message);
                    fetchAdvances();
                } else {
                    showAlert('danger', res.message || 'Error occurred');
                    btn.html(ogText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                let msg = `Failed to ${actionWord} advance.`;
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/salary_advances/admin_index.blade.php ENDPATH**/ ?>