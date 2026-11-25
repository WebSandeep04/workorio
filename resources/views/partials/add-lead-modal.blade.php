@push('styles')
<style>
  /* Modern Nexora Modal Styling */
  #addLeadModal .modal-content,
  #addProspectusModal .modal-content {
    border-radius: 16px !important;
    border: 1px solid #E4E7EC;
    box-shadow: 0 20px 48px -12px rgba(15, 23, 42, 0.22);
    overflow: hidden;
    background: #FFFFFF;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  #addLeadModal .modal-header,
  #addProspectusModal .modal-header {
    background: #FFFFFF !important;
    color: #09090B;
    border-bottom: 1px solid #F1F5F9;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  #addLeadModal .modal-title,
  #addProspectusModal .modal-title {
    font-size: 15px;
    font-weight: 700;
    color: #09090B;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }

  #addLeadModal .modal-title i,
  #addProspectusModal .modal-title i {
    color: #434AFA;
    font-size: 16px;
  }

  #addLeadModal .btn-close,
  #addProspectusModal .btn-close {
    opacity: 0.55;
    transition: opacity 0.15s ease;
    filter: none;
  }

  #addLeadModal .btn-close:hover,
  #addProspectusModal .btn-close:hover {
    opacity: 1;
  }

  #addLeadModal .modal-body,
  #addProspectusModal .modal-body {
    padding: 20px 22px;
    background: #FFFFFF;
  }

  #addLeadModal .modal-footer,
  #addProspectusModal .modal-footer {
    border-top: 1px solid #F1F5F9;
    padding: 14px 22px;
    background: #F8FAFC;
  }

  #addLeadModal .form-label-modern,
  #addProspectusModal .form-label-modern {
    color: #3F3F46;
    font-weight: 600;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  #addLeadModal .form-control-modern,
  #addLeadModal .form-select-modern,
  #addProspectusModal .form-control-modern,
  #addProspectusModal .form-select-modern {
    height: 38px;
    border: 1px solid #D4D8E1;
    border-radius: 9px;
    padding: 6px 12px;
    background: #FFFFFF;
    color: #09090B;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    font-size: 13px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    width: 100%;
  }

  #addLeadModal textarea.form-control-modern {
    height: auto;
    min-height: 38px;
    resize: none;
  }

  #addLeadModal .form-control-modern:focus,
  #addLeadModal .form-select-modern:focus,
  #addProspectusModal .form-control-modern:focus,
  #addProspectusModal .form-select-modern:focus {
    outline: none;
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
  }

  #addLeadModal .btn-modern-primary,
  #addProspectusModal .btn-modern-primary {
    background: #434AFA;
    color: #FFFFFF;
    border: none;
    border-radius: 9px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.22);
    cursor: pointer;
  }

  #addLeadModal .btn-modern-primary:hover,
  #addProspectusModal .btn-modern-primary:hover {
    background: #3138D8;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(67, 74, 250, 0.3);
    color: #FFFFFF;
  }

  #addLeadModal .btn-modern-cancel,
  #addProspectusModal .btn-modern-cancel {
    height: 36px;
    padding: 0 16px;
    border-radius: 9px;
    border: 1px solid #D4D8E1;
    background: #FFFFFF;
    color: #3F3F46;
    font-weight: 600;
    font-size: 13px;
    transition: background 0.15s ease, border-color 0.15s ease;
  }

  #addLeadModal .btn-modern-cancel:hover,
  #addProspectusModal .btn-modern-cancel:hover {
    background: #F4F4F5;
    border-color: #CBD5E1;
  }

  .blur-active {
    filter: blur(2px);
    transition: filter 0.2s ease;
    pointer-events: none;
  }

  #addProspectusModal .modal-content {
    filter: none !important;
    pointer-events: auto;
  }

  .modal-backdrop.show {
    background-color: rgba(15, 23, 42, 0.55);
  }
</style>
@endpush

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1040px;">
    <div class="modal-content blur-target">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel">
          <i class="bi bi-person-plus-fill"></i> Add New Lead
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <form method="POST" action="" id="addLeadForm">
          <div class="row g-3">
            <!-- Row 1 -->
            <div class="col-md-4">
              <label for="add_lead_prospectus" class="form-label-modern">Prospect <span class="text-danger">*</span></label>
              <div class="d-flex align-items-center gap-2">
                <div class="flex-grow-1" style="min-width: 0;">
                  <select class="form-select form-select-modern" id="add_lead_prospectus" name="prospectus" required>
                    <option value="">Select Prospect</option>
                  </select>
                </div>
                <button class="btn btn-sm d-inline-flex align-items-center justify-content-center flex-shrink-0" type="button"
                  onclick="openProspectusModal()" title="Add New Prospect"
                  style="width: 38px; height: 38px; border-radius: 9px; border: 1px solid #C7D2FE; background: #EEF2FF; color: #434AFA;">
                  <i class="bi bi-plus-lg"></i>
                </button>
              </div>
            </div>

            <div class="col-md-4">
              <label for="add_lead_leadsName" class="form-label-modern">Lead Name</label>
              <input type="text" class="form-control form-control-modern" id="add_lead_leadsName" name="leads_name"
                placeholder="Enter Lead Name">
            </div>

            <div class="col-md-4">
              <label for="add_lead_contactPerson" class="form-label-modern">Contact Person</label>
              <input type="text" class="form-control form-control-modern" id="add_lead_contactPerson"
                name="contact_person" placeholder="Enter Contact Person Name">
            </div>

            <!-- Row 2 -->
            <div class="col-md-4">
              <label for="add_lead_contactNumber" class="form-label-modern">Contact Number</label>
              <input type="tel" class="form-control form-control-modern" id="add_lead_contactNumber"
                name="contact_number" placeholder="Enter Contact Number">
            </div>

            <div class="col-md-4">
              <label for="add_lead_email" class="form-label-modern">Email ID</label>
              <input type="email" class="form-control form-control-modern" id="add_lead_email" name="email"
                placeholder="Enter Email ID">
            </div>

            <div class="col-md-4">
              <label for="add_lead_website_link" class="form-label-modern">Website</label>
              <input type="url" class="form-control form-control-modern" id="add_lead_website_link"
                name="website_link" placeholder="Enter Website URL">
            </div>

            <!-- Row 3 -->
            <div class="col-md-4">
              <label for="add_lead_address" class="form-label-modern">Address</label>
              <input type="text" class="form-control form-control-modern" id="add_lead_address" name="address"
                placeholder="Enter Address">
            </div>

            <div class="col-md-4">
              <label for="add_lead_state" class="form-label-modern">State</label>
              <select class="form-select form-select-modern" id="add_lead_state" name="state">
                <option value="">Select State</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="add_lead_city" class="form-label-modern">City</label>
              <select class="form-select form-select-modern" id="add_lead_city" name="city">
                <option value="">Select City</option>
              </select>
            </div>

            <!-- Row 4 -->
            <div class="col-md-4">
              <label for="add_lead_business_type" class="form-label-modern">Business Type</label>
              <select class="form-select form-select-modern" id="add_lead_business_type" name="business_type">
                <option value="">Loading...</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="add_lead_lead_source" class="form-label-modern">Lead Sources</label>
              <select class="form-select form-select-modern" id="add_lead_lead_source" name="lead_source">
                <option value="">Loading...</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="add_lead_product_type" class="form-label-modern">Product Type</label>
              <select class="form-select form-select-modern" id="add_lead_product_type" name="product_type">
                <option value="">Loading...</option>
              </select>
            </div>

            <!-- Row 5 -->
            <div class="col-md-4">
              <label for="add_lead_sales_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
              <select class="form-select form-select-modern" id="add_lead_sales_status" name="sales_status" required>
                <option value="">Loading...</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="add_lead_next_follow_up" class="form-label-modern">Next Follow-up Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control form-control-modern" id="add_lead_next_follow_up"
                name="next_follow_up_date" required>
            </div>

            <div class="col-md-4">
              <label for="add_lead_remark" class="form-label-modern">Remark <span class="text-danger">*</span></label>
              <textarea class="form-control form-control-modern" id="add_lead_remark" name="remark"
                placeholder="Enter Remark" rows="1" required></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-modern-cancel" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" onclick="submitLead(event)" class="btn-modern-primary" form="addLeadForm">
          <i class="bi bi-check-circle"></i> Submit Lead
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Prospectus Modal -->
<div class="modal fade" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProspectusModalLabel">
          <i class="bi bi-building-add"></i> Add New Prospect
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body py-3">
        <form id="addProspectusForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="modalnewProspectusName" class="form-label-modern">Prospect Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="modalnewProspectusName" name="modal_new_prospectus_name" placeholder="Enter Prospect Name" required>
            </div>
            <div class="col-md-6">
              <label for="modal_contact_person" class="form-label-modern">Contact Person</label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_person" name="modal_contact_person" placeholder="Enter Contact Person">
            </div>
            <div class="col-md-6">
              <label for="modal_contact_number" class="form-label-modern">Contact Number</label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_number" name="modal_contact_number" placeholder="Enter Contact Number">
            </div>
            <div class="col-md-6">
              <label for="modal_address" class="form-label-modern">Address</label>
              <input type="text" class="form-control form-control-modern" id="modal_address" name="modal_address" placeholder="Enter Address">
            </div>
            <div class="col-md-6">
              <label for="modal_state" class="form-label-modern">State</label>
              <select class="form-select form-select-modern" id="modal_state" name="modal_state">
                <option value="">Select State</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_city" class="form-label-modern">City</label>
              <select class="form-select form-select-modern" id="modal_city" name="modal_city">
                <option value="">Select City</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_email" class="form-label-modern">Email</label>
              <input type="email" class="form-control form-control-modern" id="modal_email" name="modal_email" placeholder="Enter Email">
            </div>
            <div class="col-md-6">
              <label for="modal_website_link" class="form-label-modern">Website</label>
              <input type="url" class="form-control form-control-modern" id="modal_website_link" name="modal_website_link" placeholder="Enter Website URL">
            </div>
            <div class="col-md-12">
              <label for="modal_business_type" class="form-label-modern">Business Type</label>
              <select class="form-select form-select-modern" id="modal_business_type" name="modal_business_type">
                <option value="">Loading...</option>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-modern-cancel" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="submit" onclick="submitProspect(event)" class="btn-modern-primary" form="addProspectusForm">
          <i class="bi bi-check-circle"></i> Save Prospect
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// --- Add Lead Modal Logic ---
$(document).ready(function() {
    // Initial fetch for Add Lead modal dropdowns
    fetchAddLeadOptions();
});

let addLeadProspectsList = [];

function populateProspectDropdown(list, selectedVal = null, placeholder = 'Select Prospect') {
    let opts = `<option value="">${placeholder}</option>`;
    let currentVal = selectedVal !== null ? selectedVal : $('#add_lead_prospectus').val();
    let hasSelected = false;
    $.each(list, function(key, val) {
        if (!val || !val.id) return;
        let name = (val.prospectus_name || '').trim();
        if (!name) return;
        let isSelected = (currentVal && currentVal == val.id) ? 'selected' : '';
        if (isSelected) hasSelected = true;
        opts += `<option value="${val.id}" ${isSelected}>${name}</option>`;
    });
    $('#add_lead_prospectus').html(opts);
    if (selectedVal) {
        $('#add_lead_prospectus').val(selectedVal);
    } else if (currentVal && hasSelected) {
        $('#add_lead_prospectus').val(currentVal);
    }
}

function fetchAddLeadOptions() {
    // Prospect
    $.get("{{ route('getProspectus') }}", function(data) {
        addLeadProspectsList = data || [];
        populateProspectDropdown(addLeadProspectsList);
    });

    // States (Add Lead)
    $.get("{{ route('state') }}", function(data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#add_lead_state').html(opts);
    });

    // States (Prospect Modal)
    $.get("{{ route('state') }}", function(data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#modal_state').html(opts);
    });
    
    // Status
    $.get("{{ route('getStatuses') }}", function(data) {
         let opts = '<option value="">Select Status</option>';
         $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.status_name}</option>`; });
         $('#add_lead_sales_status').html(opts);
    });
    
    // Business Type (Add Lead)
    $.get("{{ route('getbusiness') }}", function(data) {
         let opts = '<option value="">Select Business Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
         $('#add_lead_business_type').html(opts);
    });

    // Business Type (Prospect Modal)
    $.get("{{ route('getbusiness') }}", function(data) {
         let opts = '<option value="">Select Business Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
         $('#modal_business_type').html(opts);
    });
    
    // Lead Source
    $.get("{{ route('getsource') }}", function(data) {
         let opts = '<option value="">Select Source</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.source_name}</option>`; });
         $('#add_lead_lead_source').html(opts);
    });
    
    // Product Type
    $.get("{{ route('getproduct') }}", function(data) {
         let opts = '<option value="">Select Product Type</option>';
         $.each(data, function(idx, val) { opts += `<option value="${val.id}">${val.product_name}</option>`; });
         $('#add_lead_product_type').html(opts);
    });
}

// Add Lead State Change -> Cities
$(document).on('change', '#add_lead_state', function() {
    let stateId = $(this).val();
    if(stateId) {
        $.get("/city/" + stateId, function(data) {
             let opts = '<option value="">Select City</option>';
             $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
             $('#add_lead_city').html(opts);
        });
    } else {
        $('#add_lead_city').html('<option value="">Select City</option>');
    }
});

// Prospect Modal State Change -> Cities
$(document).on('change', '#modal_state', function() {
    let stateId = $(this).val();
    if(stateId) {
        $.get("/city/" + stateId, function(data) {
             let opts = '<option value="">Select City</option>';
             $.each(data, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
             $('#modal_city').html(opts);
        });
    } else {
        $('#modal_city').html('<option value="">Select City</option>');
    }
});

// Prospectus Change -> Fill Data
$(document).on('change', '#add_lead_prospectus', function() {
    let id = $(this).val();
    if(id) {
        $.get('/fillprospectus/' + id, function(data) {
            $('#add_lead_contactPerson').val(data.contact_person || '');
            $('#add_lead_contactNumber').val(data.contact_number || '');
            $('#add_lead_address').val(data.address || '');
            $('#add_lead_leadsName').val(data.prospectus_name || '');
            $('#add_lead_email').val(data.email || '');
            $('#add_lead_website_link').val(data.website_link || '');
            $('#add_lead_business_type').val(data.business_type_id || '');
            $('#add_lead_state').val(data.state_id || ''); 
            
             if(data.state_id){
                 $.get("/city/" + data.state_id, function(cities) {
                     let opts = '<option value="">Select City</option>';
                     $.each(cities, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
                     $('#add_lead_city').html(opts).val(data.city_id || '');
                 });
             }
        });
    }
});

function submitLead(e) {
    e.preventDefault();
    let $btn = $(e.currentTarget);
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');

    let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_id: $('#add_lead_prospectus').val(),
        leads_name: $('#add_lead_leadsName').val(),
        contact_person: $('#add_lead_contactPerson').val(),
        contact_number: $('#add_lead_contactNumber').val(),
        status_id: $('#add_lead_sales_status').val(),
        address: $('#add_lead_address').val(),
        state_id: $('#add_lead_state').val(),
        city_id: $('#add_lead_city').val(),
        email: $('#add_lead_email').val(),
        website_link: $('#add_lead_website_link').val(),
        next_follow_up_date: $('#add_lead_next_follow_up').val(),
        business_type_id: $('#add_lead_business_type').val(),
        remark: $('#add_lead_remark').val(),
        lead_source_id: $('#add_lead_lead_source').val(),
        products_id: $('#add_lead_product_type').val()
    };
    
    $.post('/savelead', data)
     .done(function(response) {
         $btn.prop('disabled', false).html(originalText);
         showAlert('success', 'Sales record submitted successfully!');
         $('#addLeadForm')[0].reset();
         populateProspectDropdown(addLeadProspectsList);
         $('#addLeadModal').modal('hide');
         if (typeof loadMyLeads === 'function') loadMyLeads(); 
         if (typeof loadSummaryStats === 'function') loadSummaryStats(); 
         if (typeof loadStatusCounts === 'function') loadStatusCounts();
         
         // Mark as lead in WhatsApp Inbox if applicable
         if ($('#modalSenderNumber').length > 0 && $('#modalSenderNumber').text() !== '-') {
             let senderNumber = $('#modalSenderNumber').text();
             $.post('/whatsapp-inbox/mark-as-lead', {
                 _token: $('meta[name="csrf-token"]').attr('content'),
                 sender_number: senderNumber
             }).done(function() {
                 if (typeof loadMessages === 'function') {
                     loadMessages();
                     $('#btnConvertToLead').hide();
                 }
             });
         }
     })
     .fail(function(xhr) {
         $btn.prop('disabled', false).html(originalText);
         console.error(xhr.responseText);
         showAlert('error', 'Failed to submit sales record!');
     });
}

function submitProspect(e) {
    e.preventDefault();
    let $btn = $(e.currentTarget);
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

    let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_name: $('#modalnewProspectusName').val(),
        contact_person: $('#modal_contact_person').val(),
        contact_number: $('#modal_contact_number').val(),
        address: $('#modal_address').val(),
        state_id: $('#modal_state').val(),
        city_id: $('#modal_city').val(),
        email: $('#modal_email').val(),
        website_link: $('#modal_website_link').val(),
        business_type_id: $('#modal_business_type').val()
    };

    $.post('/prospectus', data)
    .done(function(response) {
        $btn.prop('disabled', false).html(originalText);
        $('#addProspectusModal').modal('hide');
        $('#addProspectusForm')[0].reset();
        showAlert('success', 'Prospect added successfully.');
        // Refresh Prospect Dropdown in Add Lead Modal and auto-select newly created prospect
         $.get("{{ route('getProspectus') }}", function(data) {
            addLeadProspectsList = data || [];
            let createdProspect = null;
            if (response.id) {
                createdProspect = addLeadProspectsList.find(p => p.id == response.id);
            } else if (data.prospectus_name) {
                createdProspect = addLeadProspectsList.find(p => p.prospectus_name === data.prospectus_name);
            }
            let selectedId = createdProspect ? createdProspect.id : (addLeadProspectsList[addLeadProspectsList.length - 1]?.id || null);
            populateProspectDropdown(addLeadProspectsList, selectedId);
            if (selectedId) {
                $('#add_lead_prospectus').val(selectedId).trigger('change');
            }
        });
    })
    .fail(function(xhr) {
        $btn.prop('disabled', false).html(originalText);
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error === 'duplicate') {
            let p = xhr.responseJSON.prospect;
            let detailHtml = `
                <div class="text-start mt-2 p-2 border rounded" style="background:#f8f9fa; font-size: 0.88rem; line-height: 1.6;">
                    <div><strong>Prospect Name:</strong> ${p.prospectus_name || '-'}</div>
                    <div><strong>Contact Person:</strong> ${p.contact_person || '-'}</div>
                    <div><strong>Contact Number:</strong> ${p.contact_number || '-'}</div>
                    ${p.email ? `<div><strong>Email:</strong> ${p.email}</div>` : ''}
                    ${p.address ? `<div><strong>Address:</strong> ${p.address}</div>` : ''}
                    ${p.city ? `<div><strong>City / State:</strong> ${p.city}${p.state ? ', ' + p.state : ''}</div>` : ''}
                    ${p.business_type ? `<div><strong>Business Type:</strong> ${p.business_type}</div>` : ''}
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Prospect Already Exists!',
                    html: `<p class="mb-2 text-danger fw-semibold">${xhr.responseJSON.message}</p>${detailHtml}`,
                    confirmButtonText: 'Use This Existing Prospect',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#434afa',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#addProspectusModal').modal('hide');
                        $('#addProspectusForm')[0].reset();
                        // Select this existing prospect in the lead form
                        $.get("{{ route('getProspectus') }}", function(data) {
                            addLeadProspectsList = data || [];
                            populateProspectDropdown(addLeadProspectsList, p.id);
                            $('#add_lead_prospectus').val(p.id).trigger('change');
                        });
                    }
                });
            } else {
                alert(`${xhr.responseJSON.message}\nName: ${p.prospectus_name}\nPerson: ${p.contact_person}\nPhone: ${p.contact_number}`);
            }
        } else {
            showAlert('error', 'Something went wrong while adding prospect!');
            console.log(xhr.responseText);
        }
    });
}

function openProspectusModal() {
    $('#addLeadModal .blur-target').addClass('blur-active');
    
    // Prefill from Add Lead form if values are present
    let leadContactPerson = $('#add_lead_contactPerson').val();
    let leadContactNumber = $('#add_lead_contactNumber').val();
    let leadAddress = $('#add_lead_address').val();
    let leadEmail = $('#add_lead_email').val();
    let leadWebsite = $('#add_lead_website_link').val();
    let leadBusinessType = $('#add_lead_business_type').val();
    let leadState = $('#add_lead_state').val();
    let leadCity = $('#add_lead_city').val();
    let leadName = $('#add_lead_leadsName').val();

    if (leadContactPerson && !$('#modal_contact_person').val()) {
        $('#modal_contact_person').val(leadContactPerson);
    }
    if (leadContactNumber && !$('#modal_contact_number').val()) {
        $('#modal_contact_number').val(leadContactNumber);
    }
    if (leadAddress && !$('#modal_address').val()) {
        $('#modal_address').val(leadAddress);
    }
    if (leadEmail && !$('#modal_email').val()) {
        $('#modal_email').val(leadEmail);
    }
    if (leadWebsite && !$('#modal_website_link').val()) {
        $('#modal_website_link').val(leadWebsite);
    }
    if (leadBusinessType && !$('#modal_business_type').val()) {
        $('#modal_business_type').val(leadBusinessType);
    }
    if (leadState && !$('#modal_state').val()) {
        $('#modal_state').val(leadState).trigger('change');
        if (leadCity) {
            setTimeout(function() {
                $('#modal_city').val(leadCity);
            }, 300);
        }
    }
    if (leadName && !$('#modalnewProspectusName').val()) {
        $('#modalnewProspectusName').val(leadName);
    } else if (leadContactPerson && !$('#modalnewProspectusName').val()) {
        $('#modalnewProspectusName').val(leadContactPerson);
    }

    $('#addProspectusModal').modal('show');
}

$(document).ready(function () {
    $('#addProspectusModal').on('hidden.bs.modal', function () {
        $('#addLeadModal .blur-target').removeClass('blur-active');
    });
});

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    const alertHtml = `
      <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    $('body').append(alertHtml);
    setTimeout(() => $('.alert').fadeOut(), 3000);
}
</script>
@endpush
