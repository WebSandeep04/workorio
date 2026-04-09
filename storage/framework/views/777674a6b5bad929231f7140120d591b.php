<?php $__env->startSection('title', 'Lead Lists'); ?>
<?php $__env->startSection('page_title', 'List Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .calling-page { padding: 0.5rem; background: #f7f8fc; }
    
    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
    .hero-metric-card {
        background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.75rem 1rem;
        box-shadow: 0px 4px 4px 0px #0000000A; display: flex; align-items: center; gap: 0.75rem;
    }
    .hero-metric-icon { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; }
    .icon-indigo { background: linear-gradient(135deg, #434AFA, #667eea); }
    .icon-teal { background: linear-gradient(135deg, #0ea5e9, #2dd4bf); }
    .hero-metric-icon i { color: #fff; font-size: 1.2rem; }

    .metric-label { display: block; font-size: 0.65rem; color: #64748b; text-transform: uppercase; font-weight: 700; font-family: Montserrat; }
    .metric-value { font-size: 1.2rem; font-weight: 700; color: #1e293b; font-family: Montserrat; }

    .data-table-card { border-radius: 8px; border: 1px solid #f1f5f9; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; font-family: Montserrat; }
    .custom-table thead th { background: #f8fafc; padding: 1rem; font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: #475569; border-bottom: 1px solid #e2e8f0; }
    .custom-table tbody td { padding: 1rem; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    
    .btn-create-list { background: #434AFA; color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-create-list:hover { background: #3339d6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); }

    .badge-records { background: #f1f5ff; color: #434afa; padding: 0.35rem 0.75rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; }
    .list-date { font-size: 0.75rem; color: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="font-family: Montserrat;">Your Imported Lists</h5>
            <p class="text-muted small mb-0">Manage lead segments uploaded via spreadsheets</p>
        </div>
        <a href="<?php echo e(route('calling.list.create')); ?>" class="btn-create-list">
            <i class="bi bi-plus-lg"></i> Import New List
        </a>
    </div>

    <!-- Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-indigo">
                <i class="bi bi-layers"></i>
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Lists</span>
                <span class="metric-value"><?php echo e($lists->total()); ?></span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-teal">
                <i class="bi bi-people"></i>
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Contacts</span>
                <span class="metric-value"><?php echo e(number_format($lists->sum('total_records'))); ?></span>
            </div>
        </div>
    </div>

    <div class="data-table-card">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th>List Name</th>
                    <th>Records Count</th>
                    <th>Ingestion Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?php echo e($list->id); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo e($list->name); ?></div>
                        </td>
                        <td><span class="badge-records"><?php echo e(number_format($list->total_records)); ?> Records</span></td>
                        <td>
                            <div class="list-date">
                                <i class="bi bi-clock me-1"></i> <?php echo e($list->created_at->format('d M Y, h:i A')); ?>

                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item small" href="#"><i class="bi bi-eye me-2"></i> View Records</a></li>
                                    <li><hr class="dropdown-header"></li>
                                    <li><a class="dropdown-item small text-danger" href="#"><i class="bi bi-trash me-2"></i> Delete List</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center p-5 text-muted">
                            <i class="bi bi-cloud-slash d-block fs-1 mb-3"></i>
                            No lead lists found. <a href="<?php echo e(route('calling.list.create')); ?>">Upload your first one now.</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if($lists->hasPages()): ?>
            <div class="p-4 bg-light border-top">
                <?php echo e($lists->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/list/index.blade.php ENDPATH**/ ?>