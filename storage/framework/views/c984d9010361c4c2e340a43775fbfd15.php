

<?php $__env->startSection('title', 'Employment Types'); ?>
<?php $__env->startSection('page_title', 'Employment Types'); ?>

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
  }
  .table-search-btn:hover { background: #3538d4; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
  }
  .table-responsive { padding: 0.5rem 0.75rem 1rem; overflow-x: auto; }
  .custom-table { border-spacing: 0; width: 100%; font-size: 0.85rem; }
  .custom-table thead th { background: #fff; font-size: 0.65rem; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .btn-action { background: transparent; border: none; padding: 0.25rem 0.5rem; }
  .btn-action-edit { color: white; background: #343AFA; border-radius: 4px; }
  .btn-action-delete { color: white; background: #343AFA; border-radius: 4px; }
  
  .modal-content { border-radius: 0px !important; border: none; }
  .modal-header { background: #434AFA; color: white; border-bottom: none; }
  .modal-footer { border-top: 1px solid #f0f0f0; background: #fff; }
  .form-label-modern { color: #434AFA; font-weight: 600; font-size: 0.85rem; }
  .form-control-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.9rem; }
  
  .matrix-box { border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 4px; margin-bottom: 0.5rem; background:#f8fafc;}
  .matrix-header { font-weight: 600; font-size: 0.9rem; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; }
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-inactive { background: #fee2e2; color: #991b1b; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem;}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search employment types..." />
    </div>
    <button class="table-search-btn" id="addBtn">
        <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="data-table-card">
    <div class="table-responsive">
      <table class="table custom-table" id="mainTable">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="4" class="text-center text-muted py-3">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Unified Form Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="modalTitle">
          Create Employment Type
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="mainForm">
        <div class="modal-body bg-white pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_id" name="id">
          
          <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label-modern">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="name" name="name" required placeholder="Full-Time, Probation, etc.">
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Status</label>
                <select id="status" name="status" class="form-control form-control-modern">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label-modern">Max Loan % of Gross Salary</label>
                <input type="number" step="0.01" class="form-control form-control-modern" id="max_loan_percentage" name="max_loan_percentage" placeholder="e.g., 50.00">
              </div>
          </div>

          <!-- The Integrated Leave Matrix -->
          <h6 class="form-label-modern border-bottom pb-2 mb-3"><i class="bi bi-airplane"></i> Assign Leave Policies</h6>
          
          <?php if(isset($leaveTypes) && $leaveTypes->isNotEmpty()): ?>
             <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leave): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <div class="matrix-box">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input matrix-toggle" type="checkbox" id="leave_toggle_<?php echo e($leave->id); ?>" name="rules[<?php echo e($leave->id); ?>][enabled]" value="1">
                        <label class="form-check-label matrix-header" for="leave_toggle_<?php echo e($leave->id); ?>">
                            <?php if($leave->color_code): ?>
                                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?php echo e($leave->color_code); ?>;"></span>
                            <?php endif; ?>
                            <?php echo e($leave->name); ?>

                        </label>
                    </div>

                    <div class="row g-2 matrix-config" id="matrix_config_<?php echo e($leave->id); ?>" style="display:none; opacity:0.6;">
                        <div class="col-md-2">
                            <label style="font-size:0.75rem;">Type</label>
                            <select class="form-control form-control-sm matrix-type" name="rules[<?php echo e($leave->id); ?>][generation_type]">
                                <option value="prefill">Prefill (Upfront)</option>
                                <option value="accrual">Accrual (Earned)</option>
                                <option value="unlimited">Unlimited</option>
                            </select>
                        </div>
                        <div class="col-md-2 matrix-val-col">
                            <label style="font-size:0.75rem;" class="matrix-val-label">Days to give</label>
                            <input type="number" step="any" min="0" class="form-control form-control-sm matrix-val-input" name="rules[<?php echo e($leave->id); ?>][value]" value="0">
                        </div>
                        <div class="col-md-2 matrix-max-use-col">
                            <label style="font-size:0.75rem;">Max use per month</label>
                            <input type="number" step="1" min="0" class="form-control form-control-sm" name="rules[<?php echo e($leave->id); ?>][max_use_per_month]" value="0">
                        </div>
                        <div class="col-md-2 matrix-elig-col" style="display:none;">
                            <label style="font-size:0.75rem;">Eligibility (Days)</label>
                            <input type="number" step="1" min="0" class="form-control form-control-sm" name="rules[<?php echo e($leave->id); ?>][eligibility_days]" value="0">
                        </div>
                        <div class="col-md-2 matrix-hd-col" style="display:none;">
                            <label style="font-size:0.75rem;">Half Day Val</label>
                            <input type="number" step="0.1" min="0" max="1" class="form-control form-control-sm" name="rules[<?php echo e($leave->id); ?>][halfday_count_value]" value="1.0">
                        </div>
                        <div class="col-md-2 matrix-cf-col">
                            <label style="font-size:0.75rem;">Carry Forward</label>
                            <select class="form-control form-control-sm matrix-cf" name="rules[<?php echo e($leave->id); ?>][carry_forward_allowed]">
                                <option value="0">No, Lapse</option>
                                <option value="1">Yes, Rollover</option>
                            </select>
                        </div>
                        <div class="col-md-2 cf-lapse-col" style="display:none;">
                            <label style="font-size:0.75rem;">Lapse Type</label>
                            <select class="form-control form-control-sm matrix-lapse" name="rules[<?php echo e($leave->id); ?>][lapse_type]">
                                <option value="yearly" selected>Yearly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        <div class="col-md-2 cf-max-col" style="display:none;">
                            <label style="font-size:0.75rem;">Max Rollover</label>
                            <input type="number" class="form-control form-control-sm max-cf-val" name="rules[<?php echo e($leave->id); ?>][max_carry_forward]" value="0">
                        </div>
                    </div>
                 </div>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
             <div class="alert alert-warning py-2 mb-0" style="font-size:0.85rem;">No Active Leave Types found. Create leave types first.</div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn table-search-btn w-100 justify-content-center" id="saveBtn">
            <i class="bi bi-check-circle"></i> Save Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function() {
    let rawData = [];
    
    // UI Matrix Toggles logic
    $('.matrix-toggle').on('change', function() {
        let div = $(this).closest('.matrix-box').find('.matrix-config');
        if($(this).is(':checked')) {
            div.slideDown().css('opacity', 1);
            div.find('.matrix-cf').trigger('change');
        } else {
            div.slideUp().css('opacity', 0.6);
        }
    });

    $('.matrix-type').on('change', function() {
        let parent = $(this).closest('.matrix-config');
        let label = parent.find('.matrix-val-label');
        let valCol = parent.find('.matrix-val-col');
        let cfCol = parent.find('.matrix-cf-col');
        let maxCol = parent.find('.cf-max-col');

        let val = $(this).val();
        let maxUseCol = parent.find('.matrix-max-use-col');
        let eligCol = parent.find('.matrix-elig-col');
        let hdCol = parent.find('.matrix-hd-col');

        if (val === 'unlimited') {
            valCol.hide().find('input').prop('disabled', true).val('0');
            maxUseCol.hide().find('input').prop('disabled', true).val('0');
            eligCol.hide().find('input').prop('disabled', true).val('0');
            hdCol.hide().find('input').prop('disabled', true).val('1.0');
            cfCol.hide().find('select').prop('disabled', true).val('0');
            maxCol.hide().find('input').prop('disabled', true).val('0');
            parent.find('.cf-lapse-col').hide().find('select').prop('disabled', true);
        } else {
            valCol.show().find('input').prop('disabled', false);
            cfCol.show().find('select').prop('disabled', false);
            parent.find('.cf-lapse-col').find('select').prop('disabled', false);
            
            if (val === 'accrual') {
                label.text('Valid Days Reqd.');
                maxUseCol.hide().find('input').prop('disabled', true).val('0');
                eligCol.show().find('input').prop('disabled', false);
                hdCol.show().find('input').prop('disabled', false);
            } else {
                label.text('Base Days Given');
                maxUseCol.show().find('input').prop('disabled', false);
                eligCol.hide().find('input').prop('disabled', true).val('0');
                hdCol.hide().find('input').prop('disabled', true).val('1.0');
            }
            
            if (cfCol.find('select').val() === '1') {
                maxCol.show().find('input').prop('disabled', false);
                parent.find('.cf-lapse-col').hide();
            } else {
                maxCol.hide();
                parent.find('.cf-lapse-col').show();
            }
        }
    });

    $('.matrix-cf').on('change', function() {
        let parent = $(this).closest('.matrix-config');
        let maxCol = parent.find('.cf-max-col');
        let lapseCol = parent.find('.cf-lapse-col');
        
        let genType = parent.find('.matrix-type').val();
        if (genType === 'unlimited') {
            maxCol.hide();
            lapseCol.hide();
            return;
        }

        if ($(this).val() === '1') {
            maxCol.fadeIn();
            lapseCol.fadeOut();
        } else {
            maxCol.fadeOut();
            parent.find('.max-cf-val').val(0);
            lapseCol.fadeIn();
        }
    });

    function loadData() {
        let search = $('#search').val().toLowerCase();
        
        $.get("<?php echo e(route('employment-types.list')); ?>", function(res) {
            rawData = res;
            renderTable(search);
        });
    }

    function renderTable(search = '') {
        let tbody = $('#mainTable tbody');
        tbody.empty();

        let filtered = rawData.filter(item => {
            return (item.name || '').toLowerCase().includes(search) || (item.code || '').toLowerCase().includes(search);
        });

        if(filtered.length === 0) {
            tbody.html(`<tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4"></i><br>No matching records</td></tr>`);
            return;
        }

        $.each(filtered, function(i, row) {
            let statusBadge = row.status === 'active' ? 'badge-active' : 'badge-inactive';
            
            let html = `
                <tr>
                    <td><strong style="color: #434AFA;">${row.code || '-'}</strong></td>
                    <td><strong>${row.name}</strong></td>
                    <td><span class="${statusBadge}">${row.status || 'inactive'}</span></td>
                    <td class="text-center">
                        <button class="btn-action btn-action-edit editBtn" data-id="${row.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn-action btn-action-delete deleteBtn" data-id="${row.id}" title="Delete"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    $('#search').on('keyup', function() {
        renderTable($(this).val().toLowerCase());
    });

    $('#addBtn').on('click', function() {
        $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Create Employment Type');
        $('#mainForm')[0].reset();
        $('#edit_id').val('');
        $('#max_loan_percentage').val('');
        
        // Reset matrix
        $('.matrix-toggle').prop('checked', false).trigger('change');
        $('.matrix-type').val('prefill').trigger('change');
        $('.matrix-cf').val('0').trigger('change');
        
        $('#formModal').modal('show');
    });

    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        let row = rawData.find(r => r.id == id);
        
        $('#modalTitle').html('<i class="bi bi-pencil-square text-white"></i> Edit Employment Type');
        $('#mainForm')[0].reset();
        
        // Reset matrix first
        $('.matrix-toggle').prop('checked', false).trigger('change');
        $('.matrix-type').val('prefill').trigger('change');
        $('.matrix-cf').val('0').trigger('change');

        // Fill basic
        $('#edit_id').val(row.id);
        $('#name').val(row.name);
        $('#status').val(row.status);
        $('#max_loan_percentage').val(row.max_loan_percentage);


        // Fill Rules
        if(row.leave_rules && row.leave_rules.length > 0) {
            row.leave_rules.forEach(rule => {
                let lid = rule.leave_type_id;
                $(`#leave_toggle_${lid}`).prop('checked', true).trigger('change');
                $(`select[name="rules[${lid}][generation_type]"]`).val(rule.generation_type).trigger('change');
                $(`input[name="rules[${lid}][value]"]`).val(rule.value);
                $(`input[name="rules[${lid}][max_use_per_month]"]`).val(rule.max_use_per_month || 0);
                $(`input[name="rules[${lid}][eligibility_days]"]`).val(rule.eligibility_days || 0);
                $(`input[name="rules[${lid}][halfday_count_value]"]`).val(rule.halfday_count_value || 1.0);
                let cfVal = rule.carry_forward_allowed ? 1 : 0;
                $(`select[name="rules[${lid}][carry_forward_allowed]"]`).val(cfVal).trigger('change');
                if (cfVal == 0) {
                    $(`select[name="rules[${lid}][lapse_type]"]`).val(rule.lapse_type || 'yearly');
                }
                $(`input[name="rules[${lid}][max_carry_forward]"]`).val(rule.max_carry_forward);
            });
        }

        $('#formModal').modal('show');
    });

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = id ? `/employment-types/${id}` : "<?php echo e(route('employment-types.store')); ?>";
        let type = id ? 'PUT' : 'POST';
        
        let $btn = $('#saveBtn');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res) {
                $('#formModal').modal('hide');
                loadData();
                alert(res.message);
            },
            error: function(xhr) {
                let msg = 'An error occurred';
                if(xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Settings');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        if(confirm('Are you absolutely sure you want to delete this?')) {
            $.ajax({
                url: `/employment-types/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '<?php echo e(csrf_token()); ?>' },
                success: function() {
                    loadData();
                }
            });
        }
    });

    // Initial load
    loadData();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/master/employment-types.blade.php ENDPATH**/ ?>