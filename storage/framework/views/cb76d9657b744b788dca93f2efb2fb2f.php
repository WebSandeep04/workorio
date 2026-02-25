


<?php $__env->startSection('template_styles'); ?>
<style>
    /* Add unique CSS for this client format here */
    .header { border-bottom: 5px solid <?php echo e($settings->primary_color); ?>; }
    /* ... other custom styles ... */
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    
    <h1>UNIQUEAC QUOTATION</h1>
    <p>Quote No: <?php echo e($quote->quotation_number); ?></p>
    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('quotation.templates.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/uniqueac.blade.php ENDPATH**/ ?>