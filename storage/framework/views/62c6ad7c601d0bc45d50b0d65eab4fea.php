<?php $__env->startSection('title', 'Request Loan'); ?>
<?php $__env->startSection('page_title', 'Request Loan'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
  }
  .form-card {
    background: #fff;
    border-radius: 5px;
    border: 1px solid #e5e7eb;
    padding: 1.5rem;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
  }
  .form-label-modern { color: #434AFA; font-weight: 600; font-size: 0.85rem; }
  .form-control-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.9rem; width: 100%;}
  .form-control-modern:focus { border-color: #434AFA; box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1); outline: none;}
  .btn-submit { background: #434AFA; color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 4px; font-weight: 600;}
  .btn-submit:hover { background: #3538d4; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <div class="form-card mt-3">
        <form action="<?php echo e(route('loans.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label-modern">Employee <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-control-modern" required>
                        <option value="">Select Employee</option>
                        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($emp->id); ?>"><?php echo e($emp->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Loan Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" class="form-control-modern" required value="<?php echo e(old('amount')); ?>">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label-modern">Total Installments (Months) <span class="text-danger">*</span></label>
                    <input type="number" name="total_installments" class="form-control-modern" required value="<?php echo e(old('total_installments')); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Start Month <span class="text-danger">*</span></label>
                    <input type="month" name="start_month" class="form-control-modern" required value="<?php echo e(old('start_month')); ?>">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label-modern">Reason (Optional)</label>
                    <textarea name="reason" class="form-control-modern" rows="3"><?php echo e(old('reason')); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn-submit w-100">Submit Request</button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/loans/create.blade.php ENDPATH**/ ?>