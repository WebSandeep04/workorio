
@push('styles')
<style>


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
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern, .form-select-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern:focus, .form-select-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
      outline: none;
  }
  
  .btn-modern {
      padding: 0.4rem 1.2rem;
      border-radius: 4px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
  }
  
  .btn-modern-primary {
      background: #434AFA;
      color: white;
  }
  
  .btn-modern-primary:hover {
      background: #3538d4;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
      color: white;
  }

  .blur-active {
    filter: blur(2px);
    transition: filter 0.3s ease;
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
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content blur-target">
      <div class="modal-header">
        <h5 class="modal-title" id="addLeadModalLabel" style="font-size: 1rem;">
            <i class="bi bi-person-plus-fill text-white me-2"></i>Add New Lead
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-2">
        <form method="POST" action="" id="addLeadForm">
          <div class="row g-2">
            <!-- Column 1 -->
            <div class="col-md-6">
              <div class="mb-2">
                <label for="add_lead_prospectus" class="form-label-modern">Prospectus <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <select class="form-select form-select-modern" id="add_lead_prospectus" name="prospectus" required>
                      <option value="">Select Prospectus</option>
                    </select>
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="openProspectusModal()">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
              </div>

              <div class="mb-2">
                <label for="add_lead_leadsName" class="form-label-modern">Lead Name</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_leadsName" name="leads_name" placeholder="Enter Lead Name">
              </div>

              <div class="mb-2">
                <label for="add_lead_contactPerson" class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_contactPerson" name="contact_person" placeholder="Enter Contact Person Name">
              </div>

              <div class="mb-2">
                <label for="add_lead_contactNumber" class="form-label-modern">Contact Number</label>
                <input type="tel" class="form-control form-control-modern" id="add_lead_contactNumber" name="contact_number" placeholder="Enter Contact Number">
              </div>

              <div class="mb-2">
                <label for="add_lead_sales_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-select-modern" id="add_lead_sales_status" name="sales_status" required>
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_address" class="form-label-modern">Address</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_address" name="address" placeholder="Enter Address">
              </div>

              <div class="mb-2">
                <label for="add_lead_state" class="form-label-modern">State</label>
                <select class="form-select form-select-modern" id="add_lead_state" name="state">
                  <option value="">Select State</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_city" class="form-label-modern">City</label>
                <select class="form-select form-select-modern" id="add_lead_city" name="city">
                  <option value="">Select City</option>
                </select>
              </div>
            </div>
            
            <!-- Column 2 -->
            <div class="col-md-6">
              <div class="mb-2">
                <label for="add_lead_email" class="form-label-modern">Email ID</label>
                <input type="email" class="form-control form-control-modern" id="add_lead_email" name="email" placeholder="Enter Email ID">
              </div>

              <div class="mb-2">
                <label for="add_lead_website_link" class="form-label-modern">Website</label>
                <input type="url" class="form-control form-control-modern" id="add_lead_website_link" name="website_link" placeholder="Enter Website URL">
              </div>
              
              <div class="mb-2">
                <label for="add_lead_next_follow_up" class="form-label-modern">Next Follow-up Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-modern" id="add_lead_next_follow_up" name="next_follow_up_date" required>
              </div>

              <div class="mb-2">
                <label for="add_lead_business_type" class="form-label-modern">Business Type</label>
                <select class="form-select form-select-modern" id="add_lead_business_type" name="business_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_lead_source" class="form-label-modern">Lead Sources</label>
                <select class="form-select form-select-modern" id="add_lead_lead_source" name="lead_source">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_product_type" class="form-label-modern">Product Type</label>
                <select class="form-select form-select-modern" id="add_lead_product_type" name="product_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="add_lead_remark" class="form-label-modern">Remark <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-modern" id="add_lead_remark" name="remark" placeholder="Enter Remark" rows="2" required></textarea>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
         <button type="submit" onclick="submitLead(event)" class="btn-modern btn-modern-primary w-100 justify-content-center" form="addLeadForm">
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
        <h5 class="modal-title" id="addProspectusModalLabel" style="font-size: 1rem;">
            <i class="bi bi-building-add text-white me-2"></i>Add New Prospectus
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body py-2">
        <form id="addProspectusForm">
          <div class="row g-2">
            <div class="col-md-6">
              <label for="modalnewProspectusName" class="form-label-modern">Prospect Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="modalnewProspectusName" name="modal_new_prospectus_name" placeholder="Enter Prospectus Name" required>
            </div>
            <div class="col-md-6">
              <label for="modal_contact_person" class="form-label-modern">Contact Person <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_person" name="modal_contact_person" placeholder="Enter Contact Person" required>
            </div>
            <div class="col-md-6">
              <label for="modal_contact_number" class="form-label-modern">Contact Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="modal_contact_number" name="modal_contact_number" placeholder="Enter Contact Number" required>
            </div>
            <div class="col-md-6">
              <label for="modal_address" class="form-label-modern">Address <span class="text-danger">*</span></label>
              <input type="text" class="form-control form-control-modern" id="modal_address" name="modal_address" placeholder="Enter Address" required>
            </div>
            <div class="col-md-6">
              <label for="modal_state" class="form-label-modern">State <span class="text-danger">*</span></label>
              <select class="form-select form-select-modern" id="modal_state" name="modal_state" required>
                <option value="">Select State</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_city" class="form-label-modern">City <span class="text-danger">*</span></label>
              <select class="form-select form-select-modern" id="modal_city" name="modal_city" required>
                <option value="">Select City</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="modal_email" class="form-label-modern">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control form-control-modern" id="modal_email" name="modal_email" placeholder="Enter Email" required>
            </div>
            <div class="col-md-6">
              <label for="modal_website_link" class="form-label-modern">Website</label>
              <input type="url" class="form-control form-control-modern" id="modal_website_link" name="modal_website_link" placeholder="Enter Website URL">
            </div>
            <div class="col-md-6">
              <label for="modal_business_type" class="form-label-modern">Business Type <span class="text-danger">*</span></label>
              <select class="form-select form-select-modern" id="modal_business_type" name="modal_business_type" required>
                <option value="">Loading...</option>
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" onclick="submitProspect(event)" class="btn-modern btn-modern-primary w-100 justify-content-center" form="addProspectusForm">
            <i class="bi bi-check-circle"></i> Save Prospect
        </button>
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

function fetchAddLeadOptions() {
    // Prospectus
    $.get("{{ route('getProspectus') }}", function(data) {
        let opts = '<option value="">Select Prospectus</option>';
        $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
        $('#add_lead_prospectus').html(opts);
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
            $('#add_lead_contactPerson').val(data.contact_person);
            $('#add_lead_contactNumber').val(data.contact_number);
            $('#add_lead_address').val(data.address);
            $('#add_lead_leadsName').val(data.prospectus_name);
            $('#add_lead_email').val(data.email);
            $('#add_lead_website_link').val(data.website_link);
            $('#add_lead_business_type').val(data.business_type_id);
            $('#add_lead_state').val(data.state_id); 
            
             if(data.state_id){
                 $.get("/city/" + data.state_id, function(cities) {
                     let opts = '<option value="">Select City</option>';
                     $.each(cities, function(id, name) { opts += `<option value="${id}">${name}</option>`; });
                     $('#add_lead_city').html(opts).val(data.city_id);
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
                     loadMessages(); // refresh contact list to show Converted badge
                     $('#btnConvertToLead').hide(); // Hide the button
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
        // Refresh Prospectus Dropdown in Add Lead Modal
         $.get("{{ route('getProspectus') }}", function(data) {
            let opts = '<option value="">Select Prospectus</option>';
            $.each(data, function(key, val) { opts += `<option value="${val.id}">${val.prospectus_name}</option>`; });
            $('#add_lead_prospectus').html(opts);
        });
    })
    .fail(function(xhr) {
        $btn.prop('disabled', false).html(originalText);
        showAlert('error', 'Something went wrong while adding prospect!');
        console.log(xhr.responseText);
    });
}
function openProspectusModal() {
   $('#addLeadModal .blur-target').addClass('blur-active');
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
