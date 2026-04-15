

<?php $__env->startSection('title', 'Lead Remarks'); ?>
<?php $__env->startSection('page_title', 'Lead Remarks'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3 calling-remarks-page">
  <div class="row g-3">

    <!-- Calling Details -->
    <div class="col-lg-3">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Lead Details
        </div>
        <div class="card-body ui-body small">
          <div class="row g-2">
            <div class="col-4 text-muted fw-semibold">Name:</div>
            <div class="col-8 fw-bold"><?php echo e($calling->name ?? '--'); ?></div>
            
            <div class="col-4 text-muted fw-semibold">Company:</div>
            <div class="col-8 fw-bold text-primary"><?php echo e($calling->company_name ?? '--'); ?></div>

            <div class="col-4 text-muted fw-semibold">Contact:</div>
            <div class="col-8"><?php echo e($calling->contact_person ?? '--'); ?></div>

            <div class="col-4 text-muted fw-semibold">Email:</div>
            <div class="col-8 text-break"><?php echo e($calling->email ?? '--'); ?></div>
            
            <div class="col-4 text-muted fw-semibold">Phone:</div>
            <div class="col-8 fw-bold"><?php echo e($calling->phone ?? '--'); ?></div>
            
            <div class="col-4 text-muted fw-semibold">GST No:</div>
            <div class="col-8"><?php echo e($calling->gst_number ?? '--'); ?></div>

            <div class="col-4 text-muted fw-semibold">Legal St:</div>
            <div class="col-8"><?php echo e($calling->legal_status ?? '--'); ?></div>

            <div class="col-4 text-muted fw-semibold">Turnover:</div>
            <div class="col-8"><?php echo e($calling->turnover ?? '--'); ?></div>

            <div class="col-12"><hr class="my-2"></div>
            
            <div class="col-4 text-muted fw-semibold">Campaign:</div>
            <div class="col-8 text-primary fw-bold"><?php echo e($currentCampaign ? $currentCampaign->name : 'General'); ?></div>
            
            <div class="col-4 text-muted fw-semibold">Status:</div>
            <div class="col-8"><span id="display_pivot_status" class="badge bg-info text-dark"><?php echo e($pivotData->status_name ?? 'Not Set'); ?></span></div>
            
            <div class="col-4 text-muted fw-semibold">Next Date:</div>
            <div class="col-8 fw-bold text-danger" id="display_pivot_date"><?php echo e($pivotData->next_followup_date ?? '--'); ?></div>

            <div class="col-4 text-muted fw-semibold">State/City:</div>
            <div class="col-8"><?php echo e($calling->state ?? '--'); ?> / <?php echo e($calling->city ?? '--'); ?></div>

            <div class="col-12"><hr class="my-2"></div>
            
            <div class="col-12 text-muted fw-semibold mb-1">Address:</div>
            <div class="col-12 text-secondary small"><?php echo e($calling->address ?? '--'); ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Follow-up -->
    <div class="col-lg-4">
      <div class="card ui-card h-100">
        <div class="card-header ui-header d-flex justify-content-between align-items-center">
          <span id="form_mode_title">Add Follow-up</span>
          <button type="button" id="reset_form_btn" class="btn btn-sm btn-light py-0 px-2" style="font-size: 0.7rem; display: none;">Reset</button>
        </div>
        <div class="card-body ui-body">
          <form id="remarkForm" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="campaign_id" value="<?php echo e(request('campaign_id')); ?>">
            <input type="hidden" name="remark_id" id="remark_id" value="">

            <div class="mb-3">
              <label class="form-label-modern text-dark">Remark Description</label>
              <textarea name="remark" id="remark" class="form-control" rows="5" required placeholder="Type lead interaction details..."></textarea>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-12">
                    <label class="form-label-modern text-dark">Status</label>
                    <select name="calling_type_id" id="calling_type_id" class="form-select form-select-sm">
                        <option value="">Choose Status...</option>
                        <?php $__currentLoopData = $callingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e(($pivotData->calling_type_id ?? '') == $type->id ? 'selected' : ''); ?>>
                                <?php echo e($type->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-12 mt-2">
                    <div id="whatsapp_action_container" style="display: none;">
                        <button type="button" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center" id="whatsapp_btn" style="background-color: #434AFA; border: none; padding: 8px;">
                            <i class="bi bi-whatsapp me-2"></i> Send WhatsApp
                        </button>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-md-12">
                    <label class="form-label-modern text-dark">Next Date</label>
                    <input type="date" name="next_followup_date" id="next_followup_date" class="form-control form-control-sm" value="<?php echo e($pivotData->next_followup_date ?? ''); ?>">
                </div>
            </div>

            <button type="submit" id="submit_btn" class="btn btn-primary w-100 py-2" style="background: #434AFA; border: none; font-weight: 600;">
              Save Interaction
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-lg-5">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Interaction History
        </div>
        <div class="card-body ui-body remark-scroll">
          <div id="remarkList">
            <?php $__empty_1 = true; $__currentLoopData = $calling->remarks->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="remark-item mb-3 pb-2 border-bottom" data-id="<?php echo e($r->id); ?>">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="text-primary small" style="font-weight: 600;"><?php echo e(optional($r->created_at)->format('d M Y, h:i A')); ?></span>
                  <div>
                    <?php if($r->user_id == $currentUserId || (Auth::user() && Auth::user()->isAdmin())): ?>
                      <button type="button" class="btn btn-link btn-sm p-0 me-2 edit-remark-btn" style="text-decoration: none; font-size: 0.8rem; color: #434AFA;">
                        <i class="bi bi-pencil-square"></i> Edit
                      </button>
                    <?php endif; ?>
                    <span class="badge bg-light text-muted" style="font-size: 0.65rem;">UID: <?php echo e($r->user_id); ?></span>
                  </div>
                </div>
                <div class="remark-text" style="font-size: 0.9rem; color: #344054;">
                   <?php echo e($r->remark); ?>

                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="text-center py-5 no-history">
                  <i class="bi bi-chat-left-dots text-muted opacity-25" style="font-size: 4rem;"></i>
                  <p class="text-muted mt-2">No interaction history found for this lead.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<!-- WhatsApp Template Modal -->
<div class="modal fade" id="whatsappTemplateModal" tabindex="-1" aria-labelledby="whatsappTemplateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #434AFA;">
        <h5 class="modal-title" id="whatsappTemplateModalLabel">Select WhatsApp Template</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="list-group">
          <?php $__empty_1 = true; $__currentLoopData = $whatsappTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <button type="button" class="list-group-item list-group-item-action whatsapp-template-item" 
                    data-text="<?php echo e($template->text); ?>">
              <strong><?php echo e($template->name); ?></strong>
              <p class="mb-0 small text-muted"><?php echo e(\Illuminate\Support\Str::limit($template->text, 100)); ?></p>
            </button>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-3">
              <p class="text-muted">No templates found. Please add them to the database.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.calling-remarks-page { background: #f8f9fa; min-height: calc(100vh - 100px); padding-bottom: 3rem; }
.ui-card { border-radius: 12px; border: 1px solid #eef0f6; box-shadow: 0 4px 12px rgba(0,0,0,0.03); overflow: hidden; min-height: 500px; }
.ui-header { background: #434AFA; color: #fff; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.05em; padding: 14px 16px; border: none; }
.ui-body { padding: 1.5rem; }
.form-label-modern { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.4rem; display: block; font-family: Montserrat; color: #667085; }
.remark-scroll { max-height: 750px; overflow-y: auto; }
.remark-scroll::-webkit-scrollbar { width: 4px; }
.remark-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
.form-control, .form-select { border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.7rem; font-size: 0.85rem; }
.form-control:focus, .form-select:focus { border-color: #434AFA; box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.1); }
.remark-item:hover { background-color: #fcfdfe; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log("Remarks AJAX script initialized");

    const currentUserId = <?php echo e($currentUserId ?? 'null'); ?>;
    const isAdmin = <?php echo e((Auth::user() && Auth::user()->isAdmin()) ? 'true' : 'false'); ?>;

    // SweetAlert Toast Definition
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Global AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // AJAX Submission handler
    $('#remarkForm').on('submit', function(e) {
        e.preventDefault();
        console.log("Form submitted via AJAX");
        
        const $form = $(this);
        const $btn = $('#submit_btn');
        const remarkId = $('#remark_id').val();
        
        // Determine URL
        let url = "<?php echo e(route('calling.remarks.store', ['id' => $calling->id])); ?>";
        if (remarkId) {
            url = "<?php echo e(route('calling.remarks.update', ['id' => ':id'])); ?>".replace(':id', remarkId);
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        $.ajax({
            url: url,
            method: 'POST',
            data: $form.serialize(),
            success: function(resp) {
                console.log("AJAX Success:", resp);
                if (resp.success) {
                    Toast.fire({ icon: 'success', title: resp.message });
                    
                    // Update display labels in sidebar
                    if (resp.pivot) {
                        $('#display_pivot_status').text(resp.pivot.status);
                        $('#display_pivot_date').text(resp.pivot.next_date || '--');
                    }

                    if (!remarkId) {
                        // Append new remark to list
                        $('.no-history').remove();
                        
                        // Condition for showing edit button on the new item
                        let editBtnHtml = '';
                        if (resp.remark.user_id == currentUserId || isAdmin) {
                            editBtnHtml = `
                                <button type="button" class="btn btn-link btn-sm p-0 me-2 edit-remark-btn" style="text-decoration: none; font-size: 0.8rem; color: #434AFA;">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                            `;
                        }

                        const newHtml = `
                            <div class="remark-item mb-3 pb-2 border-bottom" data-id="${resp.remark.id}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-primary small" style="font-weight: 600;">${resp.remark.created_at}</span>
                                    <div>
                                        ${editBtnHtml}
                                        <span class="badge bg-light text-muted" style="font-size: 0.65rem;">UID: ${resp.remark.user_id}</span>
                                    </div>
                                </div>
                                <div class="remark-text" style="font-size: 0.9rem; color: #344054;">
                                    ${resp.remark.text}
                                </div>
                            </div>
                        `;
                        $('#remarkList').prepend(newHtml);
                    } else {
                        // Update existing remark in list
                        const $item = $(`.remark-item[data-id="${remarkId}"]`);
                        $item.find('.remark-text').text($('#remark').val());
                    }

                    resetForm();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            },
            error: function(xhr) {
                console.error("AJAX Error:", xhr);
                let msg = 'Something went wrong. Please check your input.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(remarkId ? 'Update Interaction' : 'Save Interaction');
            }
        });
    });

    // Populate form for Edit
    $(document).on('click', '.edit-remark-btn', function() {
        const $item = $(this).closest('.remark-item');
        const id = $item.data('id');
        const text = $item.find('.remark-text').text().trim();
        
        $('#remark_id').val(id);
        $('#remark').val(text);
        
        $('#form_mode_title').text('Update Follow-up');
        $('#submit_btn').text('Update Interaction');
        $('#reset_form_btn').show();
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Reset button
    $('#reset_form_btn').on('click', resetForm);

    function resetForm() {
        $('#remark_id').val('');
        $('#remark').val('');
        $('#form_mode_title').text('Add Follow-up');
        $('#submit_btn').text('Save Interaction');
        $('#reset_form_btn').hide();
    }

    // WhatsApp Logic
    const whatsappBtnContainer = $('#whatsapp_action_container');
    const statusSelect = $('#calling_type_id');
    const leadPhone = "<?php echo e($calling->phone); ?>";

    function checkStatusForWhatsapp() {
        const selectedText = statusSelect.find('option:selected').text().trim().toLowerCase();
        if (selectedText === 'sent details') {
            whatsappBtnContainer.fadeIn();
        } else {
            whatsappBtnContainer.fadeOut();
        }
    }

    // Initial check
    checkStatusForWhatsapp();

    // On status change
    statusSelect.on('change', function() {
        checkStatusForWhatsapp();
    });

    $('#whatsapp_btn').on('click', function() {
        $('#whatsappTemplateModal').modal('show');
    });

    $('.whatsapp-template-item').on('click', function() {
        const templateText = $(this).data('text');
        if (!leadPhone) {
            Swal.fire('Error', 'Lead phone number is missing.', 'error');
            return;
        }

        // Clean phone number (keep only digits)
        const cleanPhone = leadPhone.replace(/\D/g, '');
        
        // Ensure it has a country code, the user didn't specify, but I'll assume 91 for India if it's 10 digits
        let finalPhone = cleanPhone;
        if (cleanPhone.length === 10) {
            finalPhone = '91' + cleanPhone;
        }

        const encodedMsg = encodeURIComponent(templateText);
        const whatsappUrl = `https://wa.me/${finalPhone}?text=${encodedMsg}`;
        
        window.open(whatsappUrl, '_blank');
        $('#whatsappTemplateModal').modal('hide');
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/remarks.blade.php ENDPATH**/ ?>