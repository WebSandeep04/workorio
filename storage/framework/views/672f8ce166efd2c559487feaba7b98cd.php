<?php $__env->startSection('title', 'Manage Employee Shifts'); ?>
<?php $__env->startSection('page_title', 'Manage Shifts'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/select/1.3.4/css/select.bootstrap5.min.css" rel="stylesheet">
<style>
    .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border: none; margin-bottom: 1rem; }
    .card-header { background-color: #f8f9fa; border-bottom: 1px solid #eee; font-weight: 600; }
    .history-timeline { position: relative; padding: 20px 0; }
    .timeline-item { padding-left: 30px; position: relative; margin-bottom: 20px; }
    .timeline-item::before { content: ''; position: absolute; left: 0; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #0d6efd; z-index: 2; }
    .timeline-item::after { content: ''; position: absolute; left: 5px; top: 10px; width: 2px; height: calc(100% + 15px); background: #dee2e6; z-index: 1; }
    .timeline-item:last-child::after { display: none; }
    .timeline-date { font-weight: bold; color: #495057; font-size: 0.9rem; margin-bottom: 2px; }
    .timeline-content { background: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #e9ecef; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-3">
    <!-- Assignment Panel -->
    <div class="card mb-4">
        <div class="card-header">Assign Shifts</div>
        <div class="card-body">
            <form id="assignShiftForm">
                <?php echo csrf_field(); ?>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Selected Employees</label>
                        <input type="text" class="form-control bg-light" id="selectedEmployeesText" readonly placeholder="No employees selected">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">New Shift <span class="text-danger">*</span></label>
                        <select id="new_shift_id" name="shift_id" class="form-select" required>
                            <option value="">Select Shift</option>
                            <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($shift->id); ?>"><?php echo e($shift->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Effective Date <span class="text-danger">*</span></label>
                        <input type="date" id="shift_effective_date" name="shift_effective_date" class="form-control" min="<?php echo e(\Carbon\Carbon::today()->format('Y-m-d')); ?>" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" id="btnAssign">Apply Shift</button>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">Check the boxes in the table below to select employees for bulk assignment.</small>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-header">Employee Roster</div>
        <div class="card-body">
            <table id="employeesTable" class="table table-striped table-bordered w-100 align-middle">
                <thead>
                    <tr>
                        <th style="width: 30px;"><input type="checkbox" id="selectAll"></th>
                        <th>Emp Code</th>
                        <th>Name</th>
                        <th>Current Shift</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Default to today
    $('#shift_effective_date').val(new Date().toISOString().split('T')[0]);

    // Initialize DataTable
    var table = $('#employeesTable').DataTable({
        ajax: {
            url: "<?php echo e(route('employee-shifts.list')); ?>",
            dataSrc: ""
        },
        columns: [
            { 
                data: null,
                defaultContent: '',
                orderable: false,
                className: 'select-checkbox',
                render: function() { return ''; }
            },
            { data: 'employee_code' },
            { data: 'name' },
            { data: 'current_shift', render: function(data) {
                return `<span class="badge bg-secondary">${data}</span>`;
            }},
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-info text-white view-history-btn" data-id="${data}" data-name="${row.name}" title="View Shift History"><i class="fas fa-history"></i> History</button>`;
                }
            }
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        order: [[2, 'asc']]
    });

    // Update selection text
    table.on('select deselect', function () {
        var count = table.rows({ selected: true }).count();
        if (count === 0) {
            $('#selectedEmployeesText').val('No employees selected');
        } else if (count === 1) {
            var rowData = table.rows({ selected: true }).data()[0];
            $('#selectedEmployeesText').val(rowData.name);
        } else {
            $('#selectedEmployeesText').val(count + ' employees selected');
        }
    });

    // Select All
    $('#selectAll').on('click', function() {
        if(this.checked){
            table.rows({search:'applied'}).select();
        } else {
            table.rows({search:'applied'}).deselect();
        }
    });

    // Assign Shifts Form Submit
    $('#assignShiftForm').on('submit', function(e) {
        e.preventDefault();
        var selectedRows = table.rows({ selected: true }).data();
        if (selectedRows.length === 0) {
            Swal.fire('Error', 'Please select at least one employee.', 'error');
            return;
        }

        var employeeIds = [];
        for (var i = 0; i < selectedRows.length; i++) {
            employeeIds.push(selectedRows[i].id);
        }

        var shiftId = $('#new_shift_id').val();
        var effectiveDate = $('#shift_effective_date').val();
        var btn = $('#btnAssign');

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: "<?php echo e(route('employee-shifts.assign')); ?>",
            type: "POST",
            data: {
                _token: "<?php echo e(csrf_token()); ?>",
                employee_ids: employeeIds,
                shift_id: shiftId,
                shift_effective_date: effectiveDate
            },
            success: function(res) {
                Swal.fire('Success', res.message, 'success');
                table.ajax.reload();
                table.rows().deselect();
                btn.prop('disabled', false).text('Apply Shift');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Failed to assign shifts.', 'error');
                btn.prop('disabled', false).text('Apply Shift');
            }
        });
    });

    // View History
    $('#employeesTable tbody').on('click', '.view-history-btn', function(e) {
        e.stopPropagation(); // prevent row selection
        var empId = $(this).data('id');
        window.location.href = "/employee-shifts/" + empId + "/history";
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/employees/shifts.blade.php ENDPATH**/ ?>