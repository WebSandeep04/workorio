

<?php $__env->startSection('title', $department->name . ' - Expense History'); ?>
<?php $__env->startSection('page_title', $department->name . ' - Expense History'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .page-header {
      padding: 1rem 1.5rem;
      background: white;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .table-responsive {
    border-radius: 18px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

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
    position: sticky;
    top: 0;
    z-index: 5;
    font-family: Montserrat;
  }

  .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    vertical-align: middle;
  }

  .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .badge-approved {
    background-color: #d1fae5;
    color: #065f46;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <!-- <div class="page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo e(route('petty-cash.department-summary')); ?>" class="btn btn-light btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="m-0 fw-bold"><?php echo e($department->name); ?> - Expense History</h4>
        </div>
        <div>
            <span class="badge bg-primary fs-6">Total: ₹<?php echo e(number_format($expenses->sum('price'), 2)); ?></span>
        </div>
    </div> -->

    <div class="px-3">
        <div class="data-table-card">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Expense Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($expense->created_at->format('d M Y')); ?></td>
                            <td><?php echo e($expense->expense ? $expense->expense->name : 'N/A'); ?></td>
                            <td class="fw-bold">₹<?php echo e(number_format($expense->price, 2)); ?></td>
                            <td>
                                <?php if($expense->is_approved): ?>
                                    <span class="badge badge-approved">Approved</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($expense->attachment): ?>
                                    <a href="<?php echo e(asset('storage/' . $expense->attachment)); ?>" target="_blank" class="text-primary">
                                        <i class="bi bi-paperclip fs-5"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No expenses found for this wallet.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/pettycash/department-expenses.blade.php ENDPATH**/ ?>