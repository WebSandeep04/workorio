<?php $__env->startSection('title', 'Payroll Settings'); ?>
<?php $__env->startSection('page_title', 'Payroll Settings'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .modern-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
  }
  .modern-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
  }
  .modern-card-header h5 {
    margin: 0;
    font-weight: 600;
    color: #111827;
    font-family: Montserrat;
  }
  .modern-card-body {
    padding: 1.5rem;
  }
  .form-label-modern {
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }
  .form-control-modern, .form-select-modern {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    transition: all 0.2s;
  }
  .form-control-modern:focus, .form-select-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  .btn-modern-primary {
    background: #434AFA;
    color: white;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s;
  }
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
  }
  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
  }
  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
  }
  .slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }
  input:checked + .slider {
    background-color: #434AFA;
  }
  input:checked + .slider:before {
    transform: translateX(26px);
  }
  .setting-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f3f4f6;
  }
  .setting-row:last-child {
    border-bottom: none;
  }
  .setting-info h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #374151;
  }
  .setting-info p {
    margin: 0;
    font-size: 0.85rem;
    color: #6b7280;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-3 py-3">
  
  <form id="payrollSettingsForm">
    <?php echo csrf_field(); ?>
    <div class="row">
      <!-- General Settings -->
      <div class="col-md-6">
        <div class="modern-card">
          <div class="modern-card-header">
            <h5><i class="bi bi-calendar3 me-2"></i>Salary Cycle</h5>
          </div>
          <div class="modern-card-body">
            <div class="mb-3">
              <label class="form-label-modern">Salary Cycle Start Date (1-31)</label>
              <input type="number" name="salary_cycle_start" id="salary_cycle_start" class="form-control form-control-modern" min="1" max="31" value="<?php echo e($settings->salary_cycle_start ?? 1); ?>" required>
              <small class="text-muted d-block mt-1">Day of the month the payroll cycle begins.</small>
            </div>
            
            <div class="mb-3">
              <label class="form-label-modern">Salary Cycle End Date (1-31)</label>
              <input type="number" name="salary_cycle_end" id="salary_cycle_end" class="form-control form-control-modern" min="1" max="31" value="<?php echo e($settings->salary_cycle_end ?? 31); ?>" required>
              <small class="text-muted d-block mt-1">Day of the month the payroll cycle ends. (Use 31 to automatically mean the last day of any month)</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Module Toggles -->
      <div class="col-md-6">
        <div class="modern-card">
          <div class="modern-card-header">
            <h5><i class="bi bi-sliders me-2"></i>Statutory & Calculation Rules</h5>
          </div>
          <div class="modern-card-body p-0 px-3">
            
            <div class="setting-row">
              <div class="setting-info">
                <h6>Attendance Based Payroll</h6>
                <p>Calculate salaries based on locked monthly attendance summaries.</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="attendance_based" id="attendance_based" value="1" <?php echo e(($settings->attendance_based ?? true) ? 'checked' : ''); ?>>
                <span class="slider"></span>
              </label>
            </div>

            <div class="setting-row">
              <div class="setting-info">
                <h6>Enable PF (Provident Fund)</h6>
                <p>Calculate and deduct PF during payroll processing.</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="pf_enabled" id="pf_enabled" value="1" <?php echo e(($settings->pf_enabled ?? false) ? 'checked' : ''); ?>>
                <span class="slider"></span>
              </label>
            </div>

            <div class="setting-row">
              <div class="setting-info">
                <h6>Enable ESI (Employee State Insurance)</h6>
                <p>Calculate and deduct ESI during payroll processing.</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="esi_enabled" id="esi_enabled" value="1" <?php echo e(($settings->esi_enabled ?? false) ? 'checked' : ''); ?>>
                <span class="slider"></span>
              </label>
            </div>

            <div class="setting-row">
              <div class="setting-info">
                <h6>Enable PT (Professional Tax)</h6>
                <p>Calculate and deduct PT during payroll processing.</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="pt_enabled" id="pt_enabled" value="1" <?php echo e(($settings->pt_enabled ?? false) ? 'checked' : ''); ?>>
                <span class="slider"></span>
              </label>
            </div>

            <div class="setting-row">
              <div class="setting-info">
                <h6>Enable TDS (Tax Deducted at Source)</h6>
                <p>Calculate and deduct TDS during payroll processing.</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" name="tds_enabled" id="tds_enabled" value="1" <?php echo e(($settings->tds_enabled ?? false) ? 'checked' : ''); ?>>
                <span class="slider"></span>
              </label>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end mb-4">
      <button type="submit" class="btn-modern-primary" id="saveBtn">
        <i class="bi bi-save me-2"></i>Save Settings
      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
  $('#payrollSettingsForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#saveBtn');
    btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-2"></i>Saving...');

    // Collect Data
    const formData = {
      _token: '<?php echo e(csrf_token()); ?>',
      salary_cycle_start: $('#salary_cycle_start').val(),
      salary_cycle_end: $('#salary_cycle_end').val(),
      attendance_based: $('#attendance_based').is(':checked') ? 1 : 0,
      pf_enabled: $('#pf_enabled').is(':checked') ? 1 : 0,
      esi_enabled: $('#esi_enabled').is(':checked') ? 1 : 0,
      pt_enabled: $('#pt_enabled').is(':checked') ? 1 : 0,
      tds_enabled: $('#tds_enabled').is(':checked') ? 1 : 0,
    };

    $.ajax({
      url: '<?php echo e(route("payroll.settings.store")); ?>',
      type: 'POST',
      data: formData,
      success: function(res) {
        if(res.success) {
          if(typeof showAlert === 'function') {
             showAlert('success', res.message);
          } else {
             alert(res.message);
          }
        }
      },
      error: function(xhr) {
        let msg = 'Failed to save settings.';
        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        if(typeof showAlert === 'function') {
           showAlert('error', msg);
        } else {
           alert(msg);
        }
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="bi bi-save me-2"></i>Save Settings');
      }
    });
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/payroll/settings.blade.php ENDPATH**/ ?>