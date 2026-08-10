<?php $__env->startSection('title', 'Create WhatsApp Campaign'); ?>
<?php $__env->startSection('page_title', 'Create WhatsApp Campaign'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">New Campaign</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('whatsapp-campaigns.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="name" class="form-label">Campaign Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="<?php echo e(route('whatsapp-campaigns.index')); ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/whatsapp_campaigns/create.blade.php ENDPATH**/ ?>