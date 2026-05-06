<?php $__env->startSection('title', 'Change Password'); ?>
<?php $__env->startSection('page_title', 'Change Password'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .modern-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    border: 1px solid #f2f4f7;
    margin-bottom: 1.5rem;
    overflow: hidden;
  }

  .modern-card-header {
    background: #434aFA;
    color: white;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .modern-card-body {
    padding: 2rem;
  }

  .form-label-modern {
    color: #4b5563;
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    display: block;
  }
  
  .form-control-modern {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    width: 100%;
    transition: all 0.2s ease;
    background-color: #f9fafb;
  }
  
  .form-control-modern:focus {
    border-color: #434aFA;
    background-color: #fff;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }

  .btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    background: #434aFA;
    color: white;
  }
  
  .btn-modern:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="modern-card">
        <div class="modern-card-header">
          <i class="bi bi-shield-lock"></i> Change Password
        </div>
        <div class="modern-card-body">
          <form id="changePasswordForm">
            <?php echo csrf_field(); ?>
            
            <div class="mb-4">
              <label class="form-label-modern">Current Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control-modern" name="current_password" required placeholder="Enter your current password">
            </div>

            <div class="mb-4">
              <label class="form-label-modern">New Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control-modern" name="new_password" required placeholder="Enter new password (min. 6 characters)">
            </div>

            <div class="mb-4">
              <label class="form-label-modern">Confirm New Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control-modern" name="new_password_confirmation" required placeholder="Confirm your new password">
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn-modern" id="changePasswordBtn">
                <i class="bi bi-key"></i> Update Password
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? '<i class="bi bi-check-circle me-2"></i>' : '<i class="bi bi-exclamation-triangle me-2"></i>';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed shadow-sm border-0" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 320px; border-radius: 8px;">
                <div class="d-flex align-items-center">
                    ${icon}
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => $('.alert').fadeOut(), 3000);
    }

    $(document).ready(function() {
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#changePasswordBtn');
            const originalContent = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');
            
            $.ajax({
                url: "<?php echo e(route('profile.change-password.post')); ?>",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#changePasswordForm')[0].reset();
                    } else {
                        showAlert('error', response.message || 'Something went wrong');
                    }
                    btn.prop('disabled', false).html(originalContent);
                },
                error: function(xhr) {
                    let msg = 'Failed to update password.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                        msg = firstError;
                    }
                    showAlert('error', msg);
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/profile/change-password.blade.php ENDPATH**/ ?>