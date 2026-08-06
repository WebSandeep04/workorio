

<?php $__env->startSection('title', 'Loan Installments Schedule'); ?>
<?php $__env->startSection('page_title', 'Loan Installments Schedule'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

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
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
  .icon-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
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
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?php echo e(route('loans.manage')); ?>" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Manage Loans</a>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Loan Amount</div>
                <div class="summary-card-value"><?php echo e(number_format($loan->amount, 2)); ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-purple">
                <i class="bi bi-list-ol"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total EMIs</div>
                <div class="summary-card-value"><?php echo e($loan->total_installments); ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Paid</div>
                <div class="summary-card-value"><?php echo e(number_format($loan->installments->where('status', 'paid')->sum('amount'), 2)); ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-red">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Skipped</div>
                <div class="summary-card-value"><?php echo e(number_format($loan->installments->where('status', 'skipped')->sum('amount'), 2)); ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-orange">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Remaining Balance</div>
                <div class="summary-card-value"><?php echo e(number_format($loan->remaining_balance, 2)); ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon <?php echo e($loan->status === 'approved' ? 'icon-green' : ($loan->status === 'rejected' ? 'icon-red' : 'icon-orange')); ?>">
                <i class="bi bi-info-circle"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Status</div>
                <div class="summary-card-value">
                    <span class="badge <?php echo e($loan->status === 'approved' ? 'bg-success' : ($loan->status === 'rejected' ? 'bg-danger' : 'bg-warning')); ?>" style="font-size: 0.8rem;">
                        <?php echo e(ucfirst($loan->status)); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $loan->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $iBadge = $inst->status === 'paid' ? 'badge-active' : ($inst->status === 'skipped' ? 'badge-skipped' : 'badge-pending');
                                $currentMonth = \Carbon\Carbon::now()->format('Y-m');
                            ?>
                            <tr>
                                <td><?php echo e($inst->installment_number); ?></td>
                                <td><?php echo e(strtolower(\Carbon\Carbon::parse($inst->due_month)->format('M-Y'))); ?></td>
                                <td><?php echo e(number_format($inst->amount, 2)); ?></td>
                                <td><span class="<?php echo e($iBadge); ?>"><?php echo e(ucfirst($inst->status)); ?></span></td>
                                <td>
                                    <?php if($inst->status === 'pending' && $inst->due_month === $currentMonth): ?>
                                        <button type="button" class="btn-skip open-skip-modal" data-id="<?php echo e($inst->id); ?>">Skip</button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.75rem;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No installments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Skip Modal -->
<div class="modal fade" id="skipModal" tabindex="-1" aria-labelledby="skipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="skipModalForm">
                <div class="modal-header" style="border-bottom: 1px solid #eceef3;">
                    <h5 class="modal-title" id="skipModalLabel" style="font-size: 0.9rem; font-weight: 600;">Skip Installment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="skipInstallmentId" name="installment_id">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600;">Action Strategy</label>
                        <select id="skipStrategySelect" name="skip_strategy" class="form-select" style="font-size: 0.85rem;" required>
                            <option value="add_to_next">Add to Next Month</option>
                            <option value="extend_period">Extend Loan Period</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #eceef3; padding: 0.5rem;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Confirm Skip</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.open-skip-modal', function() {
        let id = $(this).data('id');
        $('#skipInstallmentId').val(id);
        $('#skipStrategySelect').val('add_to_next');
        $('#skipModal').modal('show');
    });

    $(document).on('submit', '#skipModalForm', function(e) {
        e.preventDefault();
        
        let id = $('#skipInstallmentId').val();
        let strategy = $('#skipStrategySelect').val();
        
        let btn = $(this).find('button[type="submit"]');
        let ogText = btn.html();
        btn.html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);

        $.ajax({
            url: `/loans/installments/${id}/skip`,
            type: 'POST',
            data: { skip_strategy: strategy },
            success: function(res) {
                if(res.success) {
                    showAlert('success', res.message);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showAlert('danger', res.message || 'Error occurred');
                    btn.html(ogText).prop('disabled', false);
                }
            },
            error: function(xhr) {
                let msg = 'Failed to skip installment.';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/loans/manage_installments.blade.php ENDPATH**/ ?>