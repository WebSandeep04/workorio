<?php $__env->startSection('title', 'Salary Report'); ?>

<?php $__env->startSection('content'); ?>
<style>
  .modern-card {
      background: #fff;
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.5rem;
  }
  .modern-card .card-header {
      background: #fff;
      border-bottom: 1px solid #f1f5f9;
      padding: 1.25rem 1.5rem;
      border-radius: 12px 12px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }
  .modern-card .card-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1e293b;
      margin: 0;
  }
  .custom-table th {
      background: #f8fafc;
      color: #475569;
      font-weight: 600;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      padding: 1rem;
      border-bottom: 2px solid #e2e8f0;
  }
  .custom-table td {
      padding: 1rem;
      color: #334155;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
  }
</style>

<div class="container-fluid px-2 mt-2">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark" style="font-size: 1.25rem;">Salary Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?php echo e(url('/dashboard')); ?>" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Salary Report</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card">
        <div class="card-body p-4">
            <form id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold" style="font-size: 0.85rem;">Month</label>
                    <select name="month" id="month" class="form-select form-select-sm" style="height: 38px;">
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($num); ?>" <?php echo e($num == date('n') ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold" style="font-size: 0.85rem;">Year</label>
                    <select name="year" id="year" class="form-select form-select-sm" style="height: 38px;">
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($yr); ?>" <?php echo e($yr == date('Y') ? 'selected' : ''); ?>><?php echo e($yr); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" id="generateBtn" class="btn btn-primary w-100" style="height: 38px;">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="modern-card" id="tableCard" style="display: none;">
        <div class="card-header">
            <h5 class="card-title mb-0">Salary & Attendance Summary</h5>
            <a href="#" id="exportBtn" class="btn btn-sm btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto;">
                <table class="table custom-table mb-0" id="reportTable">
                    <thead id="reportTableHead" style="position: sticky; top: 0; z-index: 1;">
                        <!-- Headers populated dynamically -->
                    </thead>
                    <tbody id="reportTableBody">
                        <!-- Data will be populated here by AJAX -->
                    </tbody>
                </table>
            </div>
            
            <div id="noDataMessage" class="text-center p-5" style="display: none;">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">No attendance or salary data found for the selected month.</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#exportBtn').on('click', function(e) {
        e.preventDefault();
        const month = $('#month').val();
        const year = $('#year').val();
        window.location.href = "<?php echo e(route('payroll.report.export')); ?>?month=" + month + "&year=" + year;
    });

    $('#generateBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        const month = $('#month').val();
        const year = $('#year').val();
        
        btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: "<?php echo e(route('payroll.report.fetch')); ?>",
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                month: month,
                year: year
            },
            success: function(response) {
                btn.html(originalText);
                btn.prop('disabled', false);
                
                $('#tableCard').show();
                const thead = $('#reportTableHead');
                const tbody = $('#reportTableBody');
                thead.empty();
                tbody.empty();
                
                if (response.success && response.data.length > 0) {
                    $('#reportTable').show();
                    $('#noDataMessage').hide();
                    
                    // Build Headers dynamically
                    let cols = response.columns || [];
                    let headHtml = `<tr>
                        <th style="min-width: 120px;">Emp Code</th>
                        <th style="min-width: 150px;">Employee Name</th>`;
                    
                    cols.forEach(c => {
                        headHtml += `<th class="text-end" style="min-width: 100px;">${c}</th>`;
                    });
                    
                    headHtml += `
                        <th class="text-center" style="min-width: 100px;">Working Days</th>
                        <th class="text-center" style="min-width: 100px;">Total Present</th>
                        <th class="text-center" style="min-width: 80px;">Full Day</th>
                        <th class="text-center" style="min-width: 80px;">Half Day</th>
                        <th class="text-center" style="min-width: 100px;">Sunday Work</th>
                        <th class="text-center" style="min-width: 100px;">Holiday Work</th>
                        <th class="text-center" style="min-width: 80px;">Leave</th>
                        <th class="text-center" style="min-width: 100px;">Unpaid Leave</th>
                        <th class="text-center" style="min-width: 80px;">Absent</th>
                        <th class="text-center" style="min-width: 120px;">Total Weekly Off</th>
                        <th class="text-center" style="min-width: 100px;">Total Holidays</th>
                        <th class="text-center" style="min-width: 150px;">Total Deduction Days</th>
                        <th class="text-end" style="min-width: 150px;">Deduction Amount</th>
                        <th class="text-center" style="min-width: 120px;">Working Days</th>
                        <th class="text-end" style="min-width: 120px;">Paid Salary</th>
                    </tr>`;
                    thead.append(headHtml);
                    
                    // Build Rows
                    response.data.forEach(function(row) {
                        const salaryClass = row.paid_salary === 'Not Generated' ? 'text-muted' : 'fw-bold text-success';
                        const salaryText = row.paid_salary === 'Not Generated' ? 'Not Generated' : '₹' + row.paid_salary;
                            
                        let tr = `<tr>
                            <td><span class="fw-medium">${row.employee_code || '-'}</span></td>
                            <td>${row.employee_name}</td>`;
                        
                        cols.forEach(c => {
                            let val = row.components[c] !== undefined ? row.components[c] : 0;
                            tr += `<td class="text-end text-muted">₹${val}</td>`;
                        });
                            
                        tr += `<td class="text-center">${row.total_working_days}</td>
                            <td class="text-center">${row.total_present_combined}</td>
                            <td class="text-center">${row.total_present}</td>
                            <td class="text-center">${row.total_halfday}</td>
                            <td class="text-center">${row.sunday_work}</td>
                            <td class="text-center">${row.holiday_work}</td>
                            <td class="text-center">${row.leave}</td>
                            <td class="text-center">${row.unpaid_leave}</td>
                            <td class="text-center text-danger">${row.absent}</td>
                            <td class="text-center">${row.total_weekly_off}</td>
                            <td class="text-center">${row.total_holidays}</td>
                            <td class="text-center text-danger">${row.total_deduction_days}</td>
                            <td class="text-end text-danger">₹${row.deduction_amount}</td>
                            <td class="text-center fw-medium">${row.payable_days}</td>
                            <td class="text-end ${salaryClass}">${salaryText}</td>
                        </tr>`;
                        tbody.append(tr);
                    });
                } else {
                    $('#reportTable').hide();
                    $('#noDataMessage').show();
                }
            },
            error: function(err) {
                btn.html(originalText);
                btn.prop('disabled', false);
                console.error(err);
                alert('Failed to fetch data. Please try again.');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/report.blade.php ENDPATH**/ ?>