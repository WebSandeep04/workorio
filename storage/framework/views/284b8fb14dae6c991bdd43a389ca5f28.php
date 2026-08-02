<?php $__env->startSection('title', 'Shift History - ' . $employee->name); ?>
<?php $__env->startSection('page_title', 'Shift History'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: none; margin-bottom: 1rem; }
    .card-header { background-color: #f8f9fa; border-bottom: 1px solid #eee; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
    .history-timeline { position: relative; padding: 20px 0; }
    .timeline-item { padding-left: 30px; position: relative; margin-bottom: 20px; }
    .timeline-item::before { content: ''; position: absolute; left: 0; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #0d6efd; z-index: 2; }
    .timeline-item::after { content: ''; position: absolute; left: 5px; top: 10px; width: 2px; height: calc(100% + 15px); background: #dee2e6; z-index: 1; }
    .timeline-item:last-child::after { display: none; }
    .timeline-date { font-weight: bold; color: #495057; font-size: 0.9rem; margin-bottom: 2px; }
    .timeline-content { background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; }
    
    .current-badge {
        font-size: 0.75em;
        padding: 0.25em 0.6em;
        background-color: #198754;
        color: white;
        border-radius: 0.25rem;
        margin-left: 10px;
        vertical-align: middle;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-3">
    
    <div class="card mb-4">
        <div class="card-header">
            <span><?php echo e($employee->name); ?> (<?php echo e($employee->employee_code); ?>)</span>
            <a href="<?php echo e(route('employee-shifts.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Shifts</a>
        </div>
        <div class="card-body">
            <?php if($history->isEmpty()): ?>
                <div class="alert alert-info">No shift history found for this employee.</div>
            <?php else: ?>
                <div class="history-timeline">
                    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isCurrent = false;
                            $today = \Carbon\Carbon::today();
                            $effectiveDate = \Carbon\Carbon::parse($record->effective_from)->startOfDay();
                            
                            // It's the "Current" shift if it's effective as of today AND it's the first one matching that condition in our desc-ordered list.
                            if ($index === 0 && $effectiveDate->lte($today)) {
                                $isCurrent = true;
                            } else {
                                // If the first record is in the future, the NEXT one in the past is current
                                if ($index > 0) {
                                    $prevRecord = $history[$index - 1];
                                    $prevEffective = \Carbon\Carbon::parse($prevRecord->effective_from)->startOfDay();
                                    if ($prevEffective->gt($today) && $effectiveDate->lte($today)) {
                                        $isCurrent = true;
                                    }
                                }
                            }
                        ?>
                        
                        <div class="timeline-item">
                            <div class="timeline-date">
                                Effective From: <?php echo e(\Carbon\Carbon::parse($record->effective_from)->format('M d, Y')); ?>

                            </div>
                            <div class="timeline-content">
                                <h5>
                                    <?php echo e($record->shift ? $record->shift->name : 'Unknown Shift'); ?>

                                    <?php if($isCurrent): ?>
                                        <span class="current-badge">Active</span>
                                    <?php endif; ?>
                                </h5>
                                
                                <div class="text-muted small mt-2">
                                    <i class="bi bi-clock-history"></i> Assigned on: <?php echo e($record->created_at->format('M d, Y h:i A')); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/employees/shift_history.blade.php ENDPATH**/ ?>