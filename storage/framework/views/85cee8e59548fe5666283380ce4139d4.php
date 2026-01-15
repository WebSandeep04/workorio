

<?php $__env->startSection('title', 'Sales Product'); ?>
<?php $__env->startSection('page_title', 'Sales Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
  <div class="row g-4">

    <!-- Sales Summary Card -->
  <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Total Tenants
        </h6>
        <a href="<?php echo e(route('todayfollowupstable')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="totaltenant">
        0
      </div>
    </div>
  </div>
</div>


    <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Active Tenant
        </h6>
        <a href="<?php echo e(route('underprocesstable')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="underprocess">
        0
      </div>
    </div>
  </div>
</div>


   <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Inactive Tenant
        </h6>
        <a href="<?php echo e(route('todaycompletedtable')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaycompleted">
      0
      </div>
    </div>
  </div>
</div>


     <!-- <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today Pending
        </h6>
        <a href="<?php echo e(route('todaypendingtable')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaypending">
      0 
      </div>
    </div>
  </div>
</div>

    <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>Today New
        </h6>
        <a href="<?php echo e(route('todaynewtable')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="todaynew">
        0
      </div>
    </div>
  </div>
</div> -->

     <!-- <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i> Total Ticket
        </h6>
        <a href="#" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="estimateticket">
        0
      </div>
    </div>
  </div>
</div> -->

 <!-- <div class="col-md-6 col-lg-4">
  <div class="card shadow rounded-4 border-0">
    <div class="card-body squareCard d-flex justify-content-between align-items-center">
      <div>
        <h6 class="card-title text-light mb-1">
          <i class="bi bi-bar-chart-line me-1"></i>  My All Leads
        </h6>
        <a href="<?php echo e(route('followup')); ?>" class="btn btn-warning btn-sm mt-1">View Details</a>
      </div>
      <div class="display-6 fw-bold text-light" id="allleads">
        0
      </div>
    </div>
  </div>
</div> -->



    <!-- Charts Section -->
  <div class="row g-4">
    <!-- Pie Chart -->
    <div class="col-md-6">
      <div class="card shadow rounded-4 border-0 bg-transparent">
        <div class="card-body">
          <h6 class="card-title">Lead Distribution</h6>
          <div class="d-flex justify-content-center">
            <canvas id="pieChart" width="180" height="180" style="max-width: 100%;"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Bar Chart -->
    <div class="col-md-6">
      <div class="card shadow rounded-4 border-0 bg-transparent">
        <div class="card-body">
          <h6 class="card-title">Monthly Leads</h6>
          <canvas id="barChart" height="170"></canvas>
        </div>
      </div>
    </div>
  </div>
  
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .card-title i {
    margin-right: 8px;
  }
  .squareCard{
    background: linear-gradient(to right, #6a11cb, #2575fc);
     
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function () {
    $.ajax({
        url: '/totaltenant',
        method: 'GET',
        success: function (response) {
            $('#totaltenant').text(response.total_tenants);
        },
        error: function () {
            $('#totaltenant').text('Error fetching count');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/superadmindashboard.blade.php ENDPATH**/ ?>