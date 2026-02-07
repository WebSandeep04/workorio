

<?php $__env->startSection('title', 'My Profile'); ?>
<?php $__env->startSection('page_title', 'My Profile'); ?>

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
    background: #434AFA;
    color: white;
    padding: 1rem 1.5rem;
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
    border-color: #434AFA;
    background-color: #fff;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }

  .form-control-modern[readonly] {
    background-color: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
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
  }
  
  .btn-primary-modern {
    background: #434AFA;
    color: white;
  }
  
  .btn-primary-modern:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
  }

  .profile-page-avatar {
    width: 100px;
    height: 100px;
    background: #e0f2fe;
    color: #434AFA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0 auto 1.5rem;
    border: 4px solid #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  /* Upload Link Styling */
  .upload-link {
    color: #434AFA;
    text-decoration: none;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    font-family: Montserrat;
    font-weight: 500;
  }

  .upload-link:hover {
    color: #3538d4;
    background: #f0f4ff;
    border-color: #434AFA;
    text-decoration: none;
  }

  .upload-link i {
    font-size: 0.75rem;
  }

  /* Existing Documents List Styling */
  .existing-documents-list {
    margin-top: 0.5rem;
  }

  .document-item-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.4rem;
    transition: all 0.2s ease;
  }

  .document-item-card:hover {
    border-color: #667eea;
    background: #f0f4ff;
  }

  .document-item-card .fw-semibold {
    font-size: 0.8rem;
    font-weight: 500;
  }

  .document-item-card .text-muted {
    font-size: 0.7rem;
  }

  .document-item-card .btn-group {
    gap: 0.2rem;
  }

  .document-item-card .btn {
    border-radius: 4px;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    line-height: 1.2;
  }

  .document-item-card .btn-outline-primary:hover {
    background: #434AFA;
    border-color: #434AFA;
    color: white;
  }

  .document-item-card .btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="row justify-content-center">
    <div class="col-lg-11">
      <div class="modern-card">
        <div class="modern-card-header">
          <i class="bi bi-person-circle"></i> Personal Information
        </div>
        <div class="modern-card-body">
            
          <div class="text-center mb-4">
            <div class="profile-page-avatar">
              <?php echo e(strtoupper(substr($employee->name, 0, 1))); ?>

            </div>
            <h4 class="mb-1"><?php echo e($employee->name); ?></h4>
            <p class="text-muted mb-0"><?php echo e($employee->designation ?? 'Employee'); ?></p>
            <p class="text-muted small"><?php echo e($employee->employee_code); ?></p>
          </div>

          <form id="profileForm">
            <?php echo csrf_field(); ?>
            
            <!-- Basic Details -->
            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Basic Details</h6>
            <div class="row mb-4">
              <div class="col-md-4 mb-3">
                <label class="form-label-modern">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control-modern" name="name" value="<?php echo e($employee->name); ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label-modern">Work Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control-modern" name="email" value="<?php echo e($employee->email); ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label-modern">Employee Code</label>
                <input type="text" class="form-control-modern" value="<?php echo e($employee->employee_code); ?>" readonly style="background-color: #f3f4f6;">
              </div>
              <div class="col-md-4 mb-3">
                  <label class="form-label-modern">Phone Number</label>
                  <input type="text" class="form-control-modern" name="phone" value="<?php echo e($employee->phone); ?>">
              </div>
              <div class="col-md-4 mb-3">
                  <label class="form-label-modern">Designation</label>
                  <input type="text" class="form-control-modern" name="designation" value="<?php echo e($employee->designation); ?>">
              </div>
              <div class="col-md-4 mb-3">
                  <label class="form-label-modern">Department</label>
                  <input type="text" class="form-control-modern" name="department" value="<?php echo e($employee->department); ?>">
              </div>
               <div class="col-md-4 mb-3">
                  <label class="form-label-modern">Date of Joining</label>
                  <input type="text" class="form-control-modern" value="<?php echo e($employee->date_of_joining); ?>" readonly style="background-color: #f3f4f6;">
              </div>
            </div>

            <!-- Personal Information -->
            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Personal Information</h6>
            <div class="row mb-4">
                 <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Personal Email</label>
                    <input type="email" class="form-control-modern" name="personal_email" value="<?php echo e($employee->personal_email); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Date of Birth</label>
                    <input type="date" class="form-control-modern" name="date_of_birth" value="<?php echo e($employee->date_of_birth); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Blood Group</label>
                    <input type="text" class="form-control-modern" name="blood_group" value="<?php echo e($employee->blood_group); ?>" placeholder="e.g. O+">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Marital Status</label>
                    <select class="form-control-modern" name="marital_status">
                        <option value="">Select Status</option>
                        <option value="Single" <?php echo e($employee->marital_status == 'Single' ? 'selected' : ''); ?>>Single</option>
                        <option value="Married" <?php echo e($employee->marital_status == 'Married' ? 'selected' : ''); ?>>Married</option>
                        <option value="Divorced" <?php echo e($employee->marital_status == 'Divorced' ? 'selected' : ''); ?>>Divorced</option>
                        <option value="Widowed" <?php echo e($employee->marital_status == 'Widowed' ? 'selected' : ''); ?>>Widowed</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Spouse Name</label>
                    <input type="text" class="form-control-modern" name="spouse_name" value="<?php echo e($employee->spouse_name); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Number of Dependents</label>
                    <input type="number" class="form-control-modern" name="number_of_dependents" value="<?php echo e($employee->number_of_dependents); ?>">
                </div>
            </div>

            <!-- Identity Documents -->
            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Identity Documents</h6>
            <div class="row mb-4">
                 <div class="col-md-3 mb-3">
                    <label class="form-label-modern">Aadhaar Number</label>
                    <input type="text" class="form-control-modern" name="aadhaar_number" value="<?php echo e($employee->aadhaar_number); ?>">
                    <div class="mt-1" id="aadhaar_upload_container">
                        <input type="file" id="profile_aadhaar_document" class="d-none" accept="image/*,.pdf">
                        <a href="#" class="upload-link" onclick="document.getElementById('profile_aadhaar_document').click(); return false;">
                            <i class="bi bi-upload me-1"></i>Upload Aadhaar
                        </a>
                        <div id="aadhaar_file_name" class="small text-muted mt-1" style="display:none;"></div>
                    </div>
                    <div id="aadhaar_document_list" class="mt-2"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">PAN Number</label>
                    <input type="text" class="form-control-modern" name="pan_number" value="<?php echo e($employee->pan_number); ?>">
                    <div class="mt-1" id="pan_upload_container">
                        <input type="file" id="profile_pan_document" class="d-none" accept="image/*,.pdf">
                        <a href="#" class="upload-link" onclick="document.getElementById('profile_pan_document').click(); return false;">
                            <i class="bi bi-upload me-1"></i>Upload PAN
                        </a>
                        <div id="pan_file_name" class="small text-muted mt-1" style="display:none;"></div>
                    </div>
                    <div id="pan_document_list" class="mt-2"></div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">Passport Number</label>
                    <input type="text" class="form-control-modern" name="passport_number" value="<?php echo e($employee->passport_number); ?>">
                </div>
                 <div class="col-md-3 mb-3">
                    <label class="form-label-modern">Passport Expiry</label>
                    <input type="date" class="form-control-modern" name="passport_expiry" value="<?php echo e($employee->passport_expiry); ?>">
                </div>
            </div>

            <!-- Education & Experience -->
            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Education & Experience</h6>
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Highest Qualification</label>
                     <input type="text" class="form-control-modern" name="highest_qualification" value="<?php echo e($employee->highest_qualification); ?>">
                </div>
                <div class="col-md-4 mb-3">
                     <label class="form-label-modern">Institution Name</label>
                     <input type="text" class="form-control-modern" name="institution_name" value="<?php echo e($employee->institution_name); ?>">
                </div>
                 <div class="col-md-4 mb-3">
                     <label class="form-label-modern">Previous Employer</label>
                     <input type="text" class="form-control-modern" name="previous_employer" value="<?php echo e($employee->previous_employer); ?>">
                </div>
                <div class="col-md-4 mb-3">
                     <label class="form-label-modern">Total Experience (Years)</label>
                     <input type="number" step="0.1" class="form-control-modern" name="experience_years" value="<?php echo e($employee->experience_years); ?>">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label-modern">Education Documents</label>
                    <div id="education_upload_container">
                        <input type="file" id="profile_education_document" class="d-none" accept="image/*,.pdf">
                        <a href="#" class="upload-link" onclick="document.getElementById('profile_education_document').click(); return false;">
                            <i class="bi bi-upload me-1"></i>Upload Education Documents
                        </a>
                        <div id="education_file_name" class="small text-muted mt-1" style="display:none;"></div>
                    </div>
                    <div id="education_document_list" class="mt-2"></div>
                </div>
            </div>

            <!-- Bank & Insurance -->
            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Bank & Insurance Details</h6>
            <div class="row mb-4">
                 <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Bank Name</label>
                     <input type="text" class="form-control-modern" name="bank_name" value="<?php echo e($employee->bank_name); ?>">
                </div>
                 <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Account Number</label>
                     <input type="text" class="form-control-modern" name="bank_account_number" value="<?php echo e($employee->bank_account_number); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">IFSC Code</label>
                     <input type="text" class="form-control-modern" name="ifsc_code" value="<?php echo e($employee->ifsc_code); ?>">
                </div>
                 <div class="col-md-4 mb-3">
                    <label class="form-label-modern">UAN Number</label>
                     <input type="text" class="form-control-modern" name="uan_number" value="<?php echo e($employee->uan_number); ?>">
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Address & Contact</h6>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label-modern">Address Line</label>
                    <input type="text" class="form-control-modern" name="address_line" value="<?php echo e($employee->address_line); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">City</label>
                    <input type="text" class="form-control-modern" name="city" value="<?php echo e($employee->city); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">State</label>
                    <input type="text" class="form-control-modern" name="state" value="<?php echo e($employee->state); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">Country</label>
                    <input type="text" class="form-control-modern" name="country" value="<?php echo e($employee->country); ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern">Postal Code</label>
                    <input type="text" class="form-control-modern" name="postal_code" value="<?php echo e($employee->postal_code); ?>">
                </div>
            </div>

            <h6 class="text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 1px; font-weight: 700;">Emergency Contact</h6>
             <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Contact Name</label>
                    <input type="text" class="form-control-modern" name="emergency_contact_name" value="<?php echo e($employee->emergency_contact_name); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Contact Phone</label>
                    <input type="text" class="form-control-modern" name="emergency_contact_phone" value="<?php echo e($employee->emergency_contact_phone); ?>">
                </div>
                 <div class="col-md-4 mb-3">
                    <label class="form-label-modern">Relation</label>
                    <input type="text" class="form-control-modern" name="emergency_contact_relation" value="<?php echo e($employee->emergency_contact_relation); ?>">
                </div>
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn-modern btn-primary-modern" id="saveBtn">
                <i class="bi bi-check2-circle"></i> Save Changes
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

    const csrf = $('meta[name="csrf-token"]').attr('content');
    const storageBase = "<?php echo e(asset('storage')); ?>";
    let existingDocuments = <?php echo json_encode($employee->documents ?? [], 15, 512) ?>;

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

    function escapeHtml(text = '') {
        return (text || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderDocuments() {
        // Clear document lists
        $('#aadhaar_document_list').html('');
        $('#pan_document_list').html('');
        $('#education_document_list').html('');
        
        // Show all upload links by default (can be toggled if we want to limit to 1 doc)
        $('#aadhaar_upload_container .upload-link').show();
        $('#pan_upload_container .upload-link').show();
        $('#education_upload_container .upload-link').show();

        const aadhaarDocs = existingDocuments.filter(doc => doc.document_type === 'Aadhaar');
        const panDocs = existingDocuments.filter(doc => doc.document_type === 'PAN');
        const educationDocs = existingDocuments.filter(doc => doc.document_type === 'Education');

         // 1. Render Aadhaar
        if (aadhaarDocs.length > 0) {
             // If Aadhaar exists, hide upload link
             $('#aadhaar_upload_container .upload-link').hide(); 
             
             let html = '<div class="existing-documents-list">';
             aadhaarDocs.forEach(function(doc) {
                 html += createDocumentCard(doc);
             });
             html += '</div>';
             $('#aadhaar_document_list').html(html);
        }

        // 2. Render PAN
        if (panDocs.length > 0) {
            // If PAN exists, hide upload link
            $('#pan_upload_container .upload-link').hide();

             let html = '<div class="existing-documents-list">';
             panDocs.forEach(function(doc) {
                 html += createDocumentCard(doc);
             });
             html += '</div>';
             $('#pan_document_list').html(html);
        }

        // 3. Render Education
        if (educationDocs.length > 0) {
             let html = '<div class="existing-documents-list">';
             educationDocs.forEach(function(doc) {
                 html += createDocumentCard(doc);
             });
             html += '</div>';
             $('#education_document_list').html(html);
        }
    }

    function createDocumentCard(doc) {
        const fileUrl = doc.file_path ? storageBase + '/' + doc.file_path : '#';
        // Note: delete route needs replacement
        return `
            <div class="document-item-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        <div>
                            <div class="fw-semibold small">${escapeHtml(doc.document_name)}</div>
                            <small class="text-muted">${doc.created_at ? new Date(doc.created_at).toLocaleDateString() : ''}</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                data-id="${doc.id}" 
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    $(document).ready(function() {
        renderDocuments();

        // Handle file input changes to show file name
        $('#profile_aadhaar_document, #profile_pan_document, #profile_education_document').on('change', function() {
            const file = this.files[0];
            const nameContainer = $(this).parent().find('div[id$="_file_name"]');
            if (file) {
                nameContainer.text('Selected: ' + file.name).show();
            } else {
                nameContainer.hide();
            }
        });

        // Delete document (delegated)
        $(document).on('click', '.delete-document', function() {
            if(!confirm('Are you sure you want to delete this document?')) return;
            
            const btn = $(this);
            const docId = btn.data('id');
            const originalIcon = btn.html();
            btn.html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: "<?php echo e(route('profile.documents.destroy', 'DOC_ID')); ?>".replace('DOC_ID', docId),
                method: "DELETE",
                data: { _token: csrf },
                success: function(response) {
                    if(response.success) {
                        // Remove from local list
                        existingDocuments = existingDocuments.filter(d => d.id != docId);
                        renderDocuments();
                        showAlert('success', 'Document deleted successfully.');
                    } else {
                        showAlert('error', response.message);
                        btn.html(originalIcon);
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to delete document.');
                    btn.html(originalIcon);
                }
            });
        });

        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#saveBtn');
            const originalContent = btn.html();
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
            
            $.ajax({
                url: "<?php echo e(route('profile.update')); ?>",
                method: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        // Profile updated, now check for documents
                        uploadDocuments(function() {
                            showAlert('success', 'Profile and documents updated successfully.');
                             // Refresh page after a short delay to reflect everything cleanly
                             setTimeout(function() {
                                location.reload();
                             }, 1000);
                        });
                    } else {
                        showAlert('error', response.message || 'Something went wrong');
                        btn.prop('disabled', false).html(originalContent);
                    }
                },
                error: function(xhr) {
                    let msg = 'Failed to update profile.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if(xhr.responseJSON && xhr.responseJSON.errors) {
                         const firstError = Object.values(xhr.responseJSON.errors)[0][0];
                         msg = firstError;
                    }
                    showAlert('error', msg);
                    btn.prop('disabled', false).html(originalContent);
                }
            });
        });

        function uploadDocuments(callback) {
            const documents = [];
            
            // Check Aadhaar
            const aadhaarFile = document.getElementById('profile_aadhaar_document').files[0];
            if (aadhaarFile) {
                documents.push({
                    file: aadhaarFile,
                    document_name: 'Aadhaar Card',
                    document_type: 'Aadhaar'
                });
            }
            
            // Check PAN
            const panFile = document.getElementById('profile_pan_document').files[0];
            if (panFile) {
                documents.push({
                    file: panFile,
                    document_name: 'PAN Card',
                    document_type: 'PAN'
                });
            }
            
            // Check Education
            const educationFile = document.getElementById('profile_education_document').files[0];
            if (educationFile) {
                documents.push({
                    file: educationFile,
                    document_name: 'Education Certificate',
                    document_type: 'Education'
                });
            }
            
            if (documents.length === 0) {
                if (callback) callback();
                return;
            }
            
            let uploadCount = 0;
            const totalDocs = documents.length;
            
            documents.forEach(function(doc) {
                const formData = new FormData();
                formData.append('file', doc.file);
                formData.append('document_name', doc.document_name);
                formData.append('document_type', doc.document_type);
                formData.append('_token', csrf);
                
                $.ajax({
                    url: "<?php echo e(route('profile.documents.store')); ?>",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function() {
                    uploadCount++;
                    if (uploadCount === totalDocs && callback) callback();
                }).fail(function() {
                    uploadCount++;
                    // Even if fail, we continue (could warn user, but for now simple flow)
                    showAlert('error', 'Failed to upload ' + doc.document_name);
                    if (uploadCount === totalDocs && callback) callback();
                });
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/profile/index.blade.php ENDPATH**/ ?>