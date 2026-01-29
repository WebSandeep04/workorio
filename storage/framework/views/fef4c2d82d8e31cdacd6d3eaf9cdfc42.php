<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3 calling-remarks-page">
  <div class="row g-3">

    <!-- Calling Details -->
    <div class="col-lg-3">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Calling Details
        </div>
        <div class="card-body ui-body small">
          <p><strong>Name :</strong> <?php echo e($calling->name ?? '--'); ?></p>
          <p><strong>Email :</strong> <?php echo e($calling->email ?? '--'); ?></p>
          <p><strong>Phone :</strong> <?php echo e($calling->phone ?? '--'); ?></p>
          <p><strong>State :</strong> <?php echo e(optional($calling->state)->state_name ?? '--'); ?></p>
          <p><strong>City :</strong> <?php echo e(optional($calling->city)->city_name ?? '--'); ?></p>
          <p><strong>Address :</strong> <?php echo e($calling->address ?? '--'); ?></p>
          <p><strong>Calling Type :</strong> <?php echo e(optional($calling->callingType)->name ?? 'No Type'); ?></p>
          <p><strong>Next Follow-up :</strong> <?php echo e($calling->next_follow_up_date ?? '--'); ?></p>
        </div>
      </div>
    </div>

    <!-- Add Follow-up -->
    <div class="col-lg-4">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Add Follow-up
        </div>
        <div class="card-body ui-body">
          <form method="POST" action="<?php echo e(route('calling.remarks.store', ['calling' => $calling->id])); ?>">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="remark_id" id="remark_id">

            <div class="mb-2">
              <label class="form-label">Date</label>
              <input type="date" name="remark_date" id="remark_date"
                class="form-control form-control-sm"
                value="<?php echo e(now()->toDateString()); ?>">
            </div>

            <div class="mb-2">
              <label class="form-label">Remarks</label>
              <textarea name="remark" id="remark"
                class="form-control form-control-sm"
                rows="6" required></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Priority</label>
              <select name="calling_type_id" id="calling_type_id"
                class="form-control form-control-sm">
                <option value="">Choose opt...</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Next Followup Date</label>
              <input type="date" name="next_follow_up_date"
                id="next_follow_up_date"
                class="form-control form-control-sm"
                value="<?php echo e($defaultNextFollowUp); ?>">
            </div>

            <button class="btn btn-primary btn-sm w-100" style="background: #434AFA !important;">
              Save Remark
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-lg-5">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Previous Remark
        </div>
        <div class="card-body ui-body remark-scroll" id="callingRemarkList">
          <?php $__empty_1 = true; $__currentLoopData = $calling->remarks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="remark-item">
              <div class="remark-date">
                <?php echo e(optional($r->created_at)->format('d/m/Y')); ?>

              </div>
              <div class="remark-text">
                <?php echo e($r->remark); ?>

              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-muted small">No remarks found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Page background */
.calling-remarks-page {
  background: #f2f2f2;
}

/* Card */
.ui-card {
  border-radius: 6px;
  border: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Header */
.ui-header {
  background: #434AFA;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  padding: 10px 14px;
}

.form-label{
  font-weight: 700;
}

/* Body */
.ui-body {
  background: #ffffff;
  font-size: 16px;
}

/* Remarks Scroll */
.remark-scroll {
  max-height: 520px;
  overflow-y: auto;
}

/* Remark Item */
.remark-item {
  border-bottom: 1px solid #eee;
  padding: 8px 0;
}

.remark-date {
  font-size: 15px;
  font-weight: 400;
  color: #333;
}

.remark-text {
  font-size: 16px;
  color: black;
  font-weight: 700;
}

/* Scrollbar */
.remark-scroll::-webkit-scrollbar {
  width: 6px;
}
.remark-scroll::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.form-control{
  background: #DFDFDF;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {
  loadCallingTypeOptions();

  function loadCallingTypeOptions() {
    $.get('<?php echo e(route("getcallingtypes")); ?>', function (callingTypes) {
      let $select = $('#calling_type_id');
      $select.empty().append('<option value="">Choose opt...</option>');

      callingTypes.forEach(type => {
        $select.append(`<option value="${type.id}">${type.name}</option>`);
      });

      let defaultType = <?php echo e($defaultCallingType ?? 'null'); ?>;
      if (defaultType) $select.val(defaultType);
    });
  }
});

function fillRemark(id, date, nextDate, text) {
  $('#remark_id').val(id);
  $('#remark_date').val(date);
  $('#next_follow_up_date').val(nextDate);
  $('#remark').val(text);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/calling/remarks.blade.php ENDPATH**/ ?>