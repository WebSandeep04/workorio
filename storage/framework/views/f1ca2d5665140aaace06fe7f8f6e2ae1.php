<?php $__env->startSection('title', 'Quotation Setup'); ?>
<?php $__env->startSection('page_title', 'Quotation Setup'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem 1rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Page Header */
  .page-header {
    background: #fff;
    padding: 1.5rem 2rem;
    border-radius: 5px;
    color: black;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    animation: fadeInDown 0.6s ease-out;
  }
  
  .page-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  /* Modern Card */
  .modern-card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid #f2f4f7;
  }

  /* Tabs */
  .tabs-container {
    display: flex;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    overflow-x: auto;
    padding: 0 1rem;
  }

  .tab-btn {
    padding: 1rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .tab-btn:hover {
    color: #434AFA;
    background: rgba(67, 74, 250, 0.05);
  }

  .tab-btn.active {
    color: #434AFA;
    border-bottom-color: #434AFA;
    background: #fff;
  }

  .tab-content {
    display: none;
    padding: 2rem;
    animation: fadeIn 0.4s ease-out;
  }

  .tab-content.active {
    display: block;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Forms */
  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
  }

  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    width: 100%;
    background-color: #fff;
  }

  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  
  textarea.form-control-modern {
    min-height: 100px;
    resize: vertical;
  }

  /* Buttons */
  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
    padding: 0.75rem 2rem;
    font-size: 1rem;
    box-shadow: 0 4px 6px rgba(67, 74, 250, 0.2);
  }
  
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(67, 74, 250, 0.3);
    color: white;
  }

  .save-container {
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
  }

  @media (max-width: 768px) {
    .tabs-container {
      padding: 0;
    }
    .tab-btn {
      padding: 0.75rem 1rem;
      font-size: 0.8rem;
    }
    .tab-content {
      padding: 1.5rem;
    }
    .save-container {
        justify-content: center;
    }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2>
                <i class="bi bi-gear-wide-connected"></i>
                Quotation Setup
            </h2>
        </div>
    </div>

    <div class="modern-card">
        <div class="tabs-container">
            <button class="tab-btn active" data-tab="company">
                <i class="bi bi-image"></i> Company Logo
            </button>
            <button class="tab-btn" data-tab="pattern">
                <i class="bi bi-palette"></i> Quotes Pattern
            </button>
            <button class="tab-btn" data-tab="terms">
                <i class="bi bi-file-earmark-check"></i> Terms & Conditions
            </button>
        </div>

        <form id="quotationSetupForm">
            <?php echo csrf_field(); ?>
            
            <!-- Company Logo Tab -->
            <div class="tab-content active" id="tab-company">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Company Logo</label>
                            <input type="file" class="form-control-modern" id="logo" name="logo" accept="image/*" onchange="previewLogo(this)">
                            <small class="text-muted mt-1 d-block">Recommended size: 200x200px. Max 2MB.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Logo Preview</label>
                            <div id="logoPreviewContainer" style="width: 120px; height: 120px; border: 2px dashed #e0e0e0; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #fafafa;">
                                <span class="text-muted small" id="noLogoText">No Logo</span>
                                <img id="logoPreview" src="" alt="Logo Preview" style="max-width: 100%; max-height: 100%; display: none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotes Pattern Tab -->
            <div class="tab-content" id="tab-pattern">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Quotation Template</label>
                            <select class="form-control-modern" id="template_name" name="template_name">
                                <option value="triserv">Triserv Pattern (Premium)</option>
                                <option value="uniqueac">Uniqueac Pattern</option>
                            </select>
                            <small class="text-muted mt-1 d-block">Choose the layout pattern for your quotation PDFs.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms & Conditions Tab -->
            <div class="tab-content" id="tab-terms">
                <h5 class="mb-3" style="color: #434AFA; font-weight: 600;">Default Terms and Conditions</h5>
                <p class="text-muted mb-4 small">Set the default terms that appear when creating a new quotation. You can still customize them for individual quotes.</p>
                
                <div class="form-group">
                    <label class="form-label-modern">Terms and Conditions</label>
                    <textarea class="form-control-modern" id="payment_terms" name="payment_terms" rows="12" placeholder="Enter terms separated by new lines..."></textarea>
                    <small class="text-muted">These will be pre-filled in the "Terms and Conditions" section of the quotation builder.</small>
                </div>
            </div>

            <div class="save-container">
                <button type="submit" class="btn-modern btn-modern-primary" id="saveBtn">
                    <i class="bi bi-check-circle"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Tab switching with animation reset
    $('.tab-btn').on('click', function() {
        const tabId = $(this).data('tab');
        
        $('.tab-btn').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        const $content = $('#tab-' + tabId);
        $content.addClass('active');
    });
    
    // Load existing settings
    loadSettings();
    
    // Form submission
    $('#quotationSetupForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings();
    });
});

function loadSettings() {
    $.get("<?php echo e(route('quotation.setup.fetch')); ?>")
        .done(function(response) {
            if (response.data) {
                const data = response.data;
                
                $('#payment_terms').val(data.payment_terms || '');
                
                // Logo Preview
                if (data.logo_path) {
                    $('#logoPreview').attr('src', '/storage/' + data.logo_path).show();
                    $('#noLogoText').hide();
                } else {
                    $('#logoPreview').hide();
                    $('#noLogoText').show();
                }
                
                // Set Pattern fields
                $('#template_name').val(data.template_name || 'triserv');
            }
        })
        .fail(function() {
            showAlert('error', 'Failed to load settings.');
        });
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result).show();
            $('#noLogoText').hide();
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function saveSettings() {
    const $btn = $('#saveBtn');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    const form = document.getElementById('quotationSetupForm');
    const formData = new FormData(form);

    $.ajax({
        url: "<?php echo e(route('quotation.setup.store')); ?>",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('success', response.message || 'Settings saved successfully');
            // Update preview if new logo uploaded
            if (response.logo_path) {
                $('#logoPreview').attr('src', '/storage/' + response.logo_path);
            }
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Failed to save settings';
            showAlert('error', message);
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Settings');
        }
    });
}

function showAlert(type, message) {
    // Remove existing alerts
    $('.alert-dismissible').remove();

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? '<i class="bi bi-check-circle me-2"></i>' : '<i class="bi bi-exclamation-circle me-2"></i>';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            ${icon}${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
<style>
  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/setup.blade.php ENDPATH**/ ?>