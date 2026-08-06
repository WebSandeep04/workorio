

<?php $__env->startSection('title', 'My Salary Advances'); ?>
<?php $__env->startSection('page_title', 'My Salary Advances'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
  }
  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
  }
  .table-search-btn:hover { background: #3538d4; color: white; text-decoration: none;}
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
  }
  .table-responsive { padding: 0.5rem 0.75rem 1rem; overflow-x: auto; }
  .custom-table { border-spacing: 0; width: 100%; font-size: 0.85rem; }
  .custom-table thead th { background: #fff; font-size: 0.8rem; font-weight: 600; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; text-align: left; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-rejected { background: #fee2e2; color: #991b1b; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem;}

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 0.6rem 1rem;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 0.6rem 1rem;
      background: #fff;
  }

  .form-label-modern {
      color: #434AFA;
      font-weight: 600;
      margin-bottom: 0.2rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.75rem;
  }
  
  .form-control-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.8rem;
      width: 100%;
  }
  
  .form-control-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
      outline: none;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search salary advances..." />
    </div>
    <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#formModal" id="addBtn">
        <i class="bi bi-plus me-1"></i>Request Advance
    </button>
  </div>

  <?php if(session('success')): ?>
      <div class="alert alert-success mt-2"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <div class="data-table-card mt-3">
    <div class="table-responsive">
      <table class="table custom-table" id="mainTable">
        <thead>
          <tr>
            <th>Employee</th>
            <th>Amount</th>
            <th>Start Month</th>
            <th>Remaining Balance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="advancesTableBody">
          <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Request Advance Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="modalTitle">
          <i class="bi bi-plus text-white"></i> Request Advance
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo e(route('salary_advances.store')); ?>" method="POST" id="mainForm">
        <div class="modal-body bg-white pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="_method" id="formMethod" value="POST">
          <div id="formErrors" class="alert alert-danger d-none"></div>
          
          <div class="row g-3 mb-3">
              <div class="col-md-12">
                  <label class="form-label-modern">Employee <span class="text-danger">*</span></label>
                  <input type="text" class="form-control-modern bg-light" value="<?php echo e(session('user_name')); ?>" readonly>
                  <input type="hidden" name="employee_id" value="<?php echo e(\App\Models\User::find(session('user_id'))?->employee_id); ?>">
              </div>
          </div>

          <div class="row g-3 mb-3">
              <div class="col-md-6">
                  <label class="form-label-modern">Advance Amount <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" name="amount" class="form-control-modern" required value="<?php echo e(old('amount')); ?>">
              </div>
              <div class="col-md-6">
                  <label class="form-label-modern">Deduction Start Month <span class="text-danger">*</span></label>
                  <input type="month" name="deduction_start_month" class="form-control-modern bg-light" required value="<?php echo e(\Carbon\Carbon::now()->format('Y-m')); ?>" readonly>
              </div>
          </div>

          <div class="row g-3">
              <div class="col-md-12">
                  <label class="form-label-modern">Reason (Optional)</label>
                  <textarea name="reason" class="form-control-modern" rows="3"><?php echo e(old('reason')); ?></textarea>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn table-search-btn w-100 justify-content-center" id="saveBtn">
            <i class="bi bi-check-circle"></i> Submit Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
          <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            ${message}
          </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => $('.alert.alert-dismissible').fadeOut('slow', function() { $(this).remove(); }), 3000);
    }

    let defaultAction = "<?php echo e(route('salary_advances.store')); ?>";
    let allAdvances = [];

    function fetchAdvances() {
        $.ajax({
            url: "<?php echo e(route('salary_advances.fetch')); ?>",
            type: "POST",
            data: { _token: "<?php echo e(csrf_token()); ?>" },
            success: function(response) {
                allAdvances = response.data || [];
                renderTable();
            }
        });
    }

    function renderTable() {
        let searchTerm = $('#search').val().toLowerCase();
        let filtered = allAdvances.filter(adv => {
            let empName = (adv.employee && adv.employee.name) ? adv.employee.name.toLowerCase() : '';
            return !searchTerm || empName.includes(searchTerm);
        });

        let html = '';
        if(filtered.length === 0) {
            html = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4"></i><br>No salary advances found</td></tr>';
        } else {
            filtered.forEach(function(adv) {
                let empName = adv.employee ? adv.employee.name : 'N/A';
                let amount = parseFloat(adv.amount).toFixed(2);
                let rem = adv.remaining_balance !== undefined ? parseFloat(adv.remaining_balance).toFixed(2) : '0.00';
                
                let statusBadge = '';
                if(adv.status == 'approved' || adv.status == 'completed') statusBadge = `<span class="badge-active">${adv.status.charAt(0).toUpperCase() + adv.status.slice(1)}</span>`;
                else if(adv.status == 'rejected') statusBadge = `<span class="badge-rejected">${adv.status.charAt(0).toUpperCase() + adv.status.slice(1)}</span>`;
                else statusBadge = `<span class="badge-pending">${adv.status.charAt(0).toUpperCase() + adv.status.slice(1)}</span>`;

                let actions = '';
                if(adv.status === 'pending') {
                    actions = `
                        <button class="btn btn-sm btn-danger py-1 px-2 delete-btn" style="font-size: 11px;" data-id="${adv.id}"><i class="bi bi-trash"></i></button>
                    `;
                } else {
                    actions = `<span class="text-muted" style="font-size: 11px;">N/A</span>`;
                }
                
                html += `
                    <tr>
                        <td><strong>${empName}</strong></td>
                        <td>${amount}</td>
                        <td>${adv.deduction_start_month}</td>
                        <td>${rem}</td>
                        <td>${statusBadge}</td>
                        <td>${actions}</td>
                    </tr>
                `;
            });
        }
        $('#advancesTableBody').html(html);
    }

    $(document).ready(function() {
        fetchAdvances();

        $('#search').on('keyup', renderTable);

        $('#addBtn').on('click', function() {
            $('#mainForm').attr('action', defaultAction);
            $('#formMethod').val('POST');
            $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Request Advance');
            $('#mainForm')[0].reset();
            $('#formErrors').addClass('d-none').html('');
        });

        // Delete
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            if(confirm('Are you sure you want to delete this pending advance request?')) {
                $.ajax({
                    url: `/salary-advances/${id}`,
                    type: 'DELETE',
                    data: { _token: "<?php echo e(csrf_token()); ?>" },
                    success: function(response) {
                        if(response.success) {
                            showAlert('success', response.message);
                            fetchAdvances();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showAlert('error', 'Error deleting advance.');
                    }
                });
            }
        });

        $('#mainForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = $('#saveBtn');
            let formErrors = $('#formErrors');
            
            formErrors.addClass('d-none').html('');
            submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Submitting...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#formModal').modal('hide');
                        showAlert('success', response.message);
                        fetchAdvances();
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
                    
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        formErrors.removeClass('d-none').html(errorHtml);
                    } else if(xhr.status === 403) {
                        showAlert('error', xhr.responseJSON.message || 'Action forbidden.');
                    } else {
                        showAlert('error', 'An unexpected error occurred. Please try again.');
                    }
                }
            });
        });
        
        $('#formModal').on('hidden.bs.modal', function () {
            $('#mainForm').attr('action', defaultAction);
            $('#formMethod').val('POST');
            $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Request Advance');
            $('#mainForm')[0].reset();
            $('#formErrors').addClass('d-none').html('');
            $('#saveBtn').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/salary_advances/index.blade.php ENDPATH**/ ?>