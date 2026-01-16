

<?php $__env->startSection('title', 'Sales Product'); ?>
<?php $__env->startSection('page_title', 'Sales Product'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-3">
  <div class="row g-3 align-items-stretch">
    <!-- Lead Details -->
    <div class="col-md-3 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background-color: #434afa;">
          <strong>Lead Details</strong>
        </div>
        <?php if($record): ?>
        <?php $first = $record; ?>
        <div class="card-body p-3 small text-light" style="background-color: #434afa;">
          <div class="d-flex flex-column gap-2">
            <div class="row g-0">
              <div class="col-5">Lead:</div>
              <div class="col-7 fw-bold"><?php echo e($first->leads_name ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">Contact Person:</div>
              <div class="col-7 fw-bold"><?php echo e($first->contact_person ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">Contact No:</div>
              <div class="col-7 fw-bold"><?php echo e($first->contact_number ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">Email:</div>
              <div class="col-7 fw-bold text-break"><?php echo e($first->email ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">State:</div>
              <div class="col-7 fw-bold"><?php echo e($first->state->state_name ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">City:</div>
              <div class="col-7 fw-bold"><?php echo e($first->city->city_name ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">Product:</div>
              <div class="col-7 fw-bold"><?php echo e($first->product->product_name ?? '--'); ?></div>
            </div>
            <div class="row g-0">
              <div class="col-5">Business Type:</div>
              <div class="col-7 fw-bold"><?php echo e($first->businessType->business_name ?? '--'); ?></div>
            </div>
          </div>

          <div id="latestQuoteBox" class="lq-box lq-inline" style="display:none;">
            <span class="lq-label me-1">Latest Quote</span>
            <span id="lq_version" class="badge rounded-pill bg-primary-subtle text-primary border border-primary me-1">v-</span>
            <a id="lq_link" href="#" target="_blank" class="btn btn-icon btn-primary" title="Open">
              <i class="bi bi-box-arrow-up-right"></i>
            </a>
            <a id="lq_rev" href="<?php echo e(route('quotation')); ?>" class="btn btn-icon btn-yellow ms-1" title="Revise">
              <i class="bi bi-arrow-repeat"></i>
            </a>
            <button id="lq_whatsapp" onclick="sendQuoteToWhatsApp()" class="btn btn-icon btn-success ms-1" title="Send to WhatsApp" style="background: linear-gradient(135deg,#25d366,#128c7e); color: white; border: 1px solid rgba(0,0,0,.08);">
              <i class="bi bi-whatsapp"></i>
            </button>
          </div>
          <input type="hidden" id="lq_entity_type" value="<?php echo e(isset($first->customer_id) && $first->customer_id ? 'customer' : 'prospect'); ?>">
          <input type="hidden" id="lq_entity_id" value="<?php echo e($first->customer_id ?? $first->prospectus_id); ?>">
        </div>
        <?php endif; ?>
        <div class="text-center pb-3 mt-auto" style="background-color: #434afa;">
          <button type="button" class="btn btn-sm btn-warning w-75" onclick="openEditProspectModal()">Edit</button>
        </div>
      </div>
    </div>

    <!-- Remark Form -->
    <div class="col-md-4 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background-color: #434afa;">
          <strong>Add/Edit Remark</strong>
        </div>
        <div class="card-body p-3 d-flex flex-column text-white" style="background-color: #434afa;">
          <form id="remarkForm" class="flex-grow-1 d-flex flex-column">
            <input type="hidden" name="sales_record_id" id="sales_record_id" value="<?php echo e($record->id); ?>">
            <input type="hidden" name="remark_id" id="remark_id" value="">

            <div class="mb-2">
              <label class="form-label">Date</label>
              <input type="text" name="remark_date" id="remark_date" class="form-control form-control-sm" placeholder="dd/mm/yyyy">
            </div>

            <div class="mb-2">
              <label class="form-label">Remark</label>
              <textarea name="remark" id="remark" class="form-control form-control-sm" rows="3" required></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Estimated Ticket Value</label>
              <input type="text" name="ticket_value" id="ticket_value" class="form-control form-control-sm" value="<?php echo e($first->ticket_value); ?>" placeholder="Enter value">
            </div>

            <div class="mb-2">
              <label class="form-label">Next Follow-Up Date</label>
              <input type="text" name="next_follow_up_date" id="next_follow_up_date" class="form-control form-control-sm" placeholder="dd/mm/yyyy"
                     value="<?php echo e(isset($first->next_follow_up_date) ? \Carbon\Carbon::parse($first->next_follow_up_date)->format('d/m/Y') : ''); ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="sales_status" id="sales_status" class="form-select form-select-sm">
                <option value="">Select Status</option>
                <!-- AJAX options -->
              </select>
            </div>

            <button type="submit" onclick="submitRemark(event)" class="btn btn-warning btn-sm w-100 mt-auto">Submit Remark</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-md-5 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background-color: #434afa;">
          <strong>Previous Remarks</strong>
        </div>
       <div class="card-body p-3 overflow-auto" style="max-height: 500px; background-color: #434afa;" id="remarkList">

          <ul class="list-group small" id="remarkList">
            <?php $allRemarks = $record->remarks()->orderBy('remark_date','desc')->get(); ?>

            <?php $__empty_1 = true; $__currentLoopData = $allRemarks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $remark): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <li class="list-group-item d-flex justify-content-between align-items-start py-2 px-3">
                <div>
                  <strong><?php echo e(\Carbon\Carbon::parse($remark->remark_date)->format('d/m/Y')); ?>:</strong>
                  <div><?php echo e(\Illuminate\Support\Str::limit($remark->remark, 100)); ?></div>
                </div>
                <button class="btn btn-sm btn-warning ms-2"
                        onclick="editRemark('<?php echo e($remark->id); ?>', '<?php echo e($remark->remark_date); ?>', `<?php echo e(addslashes($remark->remark)); ?>`)">
                  Edit
                </button>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <li class="list-group-item text-muted">No remarks found.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Prospectus Modal -->
<div class="modal fade" id="editProspectusModal" tabindex="-1" aria-labelledby="editProspectusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="editProspectusModalLabel">Edit Prospectus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="editProspectusForm">
          <input type="hidden" id="edit_prospectus_id" name="edit_prospectus_id">
          <div class="row g-3">

            <div class="col-md-6">
              <label for="edit_modalnewProspectusName" class="form-label">Prospect Name</label>
              <input type="text" class="form-control" id="edit_modalnewProspectusName" name="edit_modal_new_prospectus_name" placeholder="Enter Prospectus Name" required>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_contact_person" class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="edit_modal_contact_person" name="edit_modal_contact_person" placeholder="Enter Contact Person" required>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_contact_number" class="form-label">Contact Number</label>
              <input type="text" class="form-control" id="edit_modal_contact_number" name="edit_modal_contact_number" placeholder="Enter Contact Number" required>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_address" class="form-label">Address</label>
              <input type="text" class="form-control" id="edit_modal_address" name="edit_modal_address" placeholder="Enter Address" required>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_state" class="form-label">State</label>
              <select class="form-select" id="edit_modal_state" name="edit_modal_state" required>
                <option value="">Select State</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_city" class="form-label">City</label>
              <select class="form-select" id="edit_modal_city" name="edit_modal_city" required>
                <option value="">Select City</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_email" class="form-label">Email</label>
              <input type="email" class="form-control" id="edit_modal_email" name="edit_modal_email" placeholder="Enter Email" required>
            </div>

            <div class="col-md-6">
              <label for="edit_modal_business_type" class="form-label">Business Type</label>
              <select class="form-select" id="edit_modal_business_type" name="edit_modal_business_type" required>
                <option value="">Loading...</option>
              </select>
            </div>

          </div>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer justify-content-center" style="background-color: #434afa;">
        <button type="submit" onclick="updateProspect(event)" class="btn btn-warning fw-bold" form="editProspectusForm">Update Prospectus</button>
      </div>

    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  /* Targeting the scroll container */
  #remarkList {
    max-height: 450px;
    overflow-y: auto;
  }

  /* Scrollbar styling for Webkit browsers (Chrome, Edge, Safari) */
  #remarkList::-webkit-scrollbar {
    width: 8px;
  }

  #remarkList::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1); 
    border-radius: 4px;
  }

  #remarkList::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.4);
    border-radius: 4px;
  }

  /* Firefox Scrollbar */
  #remarkList {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.4) rgba(255, 255, 255, 0.1);
  }

  /* Latest quote compact UI */
  .lq-box {
    background: rgba(255,255,255,.92);
    color: #0f172a;
    border-radius: 8px;
    padding: 4px 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    border: 1px solid rgba(255,255,255,.4);
    display: inline-flex;
    align-items: center;
  }
  .lq-label {
    font-weight: 700;
    letter-spacing: .2px;
    font-size: 11px;
    text-transform: uppercase;
    opacity: .85;
  }
  .btn-chip {
    padding: 4px 10px;
    font-size: 12px;
    border-radius: 999px;
    line-height: 1.2;
  }
  .btn-icon {
    padding: 2px 8px;
    font-size: 12px;
    border-radius: 999px;
    line-height: 1.2;
  }
  .btn-chip.btn-primary {
    background: linear-gradient(135deg,#1d4ed8,#2563eb);
    border: none;
  }
  .btn-chip.btn-primary:hover { filter: brightness(1.05); }
  .btn-chip.btn-outline-light:hover { background: rgba(255,255,255,.12); }
  .lq-meta {
    font-size: 11px;
    opacity: .85;
  }
  .btn-yellow {
    background: linear-gradient(135deg,#f59e0b,#fbbf24);
    color: #111827;
    border: 1px solid rgba(0,0,0,.08);
  }
  .btn-yellow:hover { filter: brightness(1.05); }
</style>
<?php $__env->stopPush(); ?>




<?php $__env->startPush('scripts'); ?>
<script>
  // Store latest quote data globally
  var latestQuoteData = null;

  // Fetch latest quotation for this entity
  $(document).ready(function(){
      var type = $('#lq_entity_type').val();
      var id = $('#lq_entity_id').val();
      if (!type || !id) return;
      $.get("<?php echo e(route('quotation.latest')); ?>", { type: type, id: id })
        .done(function(resp){
            if (resp && resp.data) {
                var d = resp.data;
                latestQuoteData = d; // Store for WhatsApp function
                $('#lq_version').text('v' + (d.version||'-'));
                if (d.file_url) { $('#lq_link').attr('href', d.file_url); }
                // Link to open the revise page directly
                $('#lq_rev').attr('href', `<?php echo e(route('quotation.create')); ?>?quote=${encodeURIComponent(d.quotation_number)}&revise=1`);
                $('#latestQuoteBox').show();
            }
        })
        .fail(function(){ /* ignore */ });
  });

  // Send latest quote to WhatsApp
  function sendQuoteToWhatsApp() {
      if (!latestQuoteData || !latestQuoteData.file_url) {
          alert('Quotation file not available');
          return;
      }

      // Get contact number from lead details
      var contactNumber = '<?php echo e($first->contact_number ?? ''); ?>';
      if (!contactNumber || contactNumber === '') {
          alert('Contact number not available');
          return;
      }

      // Clean phone number (remove spaces, dashes, etc.)
      contactNumber = contactNumber.replace(/[^0-9+]/g, '');
      
      // Ensure phone number starts with country code (91 for India)
      if (!contactNumber.startsWith('+') && !contactNumber.startsWith('91')) {
          if (contactNumber.startsWith('0')) {
              contactNumber = '91' + contactNumber.substring(1);
          } else {
              contactNumber = '91' + contactNumber;
          }
      }

      // Remove + if present for WhatsApp URL
      contactNumber = contactNumber.replace(/^\+/, '');

      // Get quotation number and absolute file URL
      var quoteNumber = latestQuoteData.quotation_number || 'Quotation';
      var fileUrl = latestQuoteData.file_url;
      if (fileUrl.startsWith('/')) { fileUrl = window.location.origin + fileUrl; }

      // Try using the Web Share API with file attachment (mobile browsers)
      (async function(){
          try {
              const resp = await fetch(fileUrl, { mode: 'cors' });
              const blob = await resp.blob();
              const fileName = (quoteNumber || 'quotation') + '.pdf';
              const file = new File([blob], fileName, { type: 'application/pdf' });

              if (navigator.canShare && navigator.canShare({ files: [file] })) {
                  await navigator.share({
                      files: [file],
                      title: 'Quotation',
                      text: `Quotation ${quoteNumber}`
                  });
                  return; // done
              }
          } catch (e) {
              // fall through to WhatsApp Web
          }

          // Fallback: open WhatsApp Web with the link prefilled
          var message = encodeURIComponent(`Hello! Please find the quotation ${quoteNumber} below:\n\n${fileUrl}`);
          var whatsappUrl = `https://web.whatsapp.com/send?phone=${contactNumber}&text=${message}`;
          window.open(whatsappUrl, '_blank');
      })();
  }

   // Function to open edit prospect modal
   function openEditProspectModal() {
       let prospectId = "<?php echo e($first->prospectus_id); ?>";
       
       if (prospectId) {
           // Set the prospect ID
           $('#edit_prospectus_id').val(prospectId);
           
           // Load prospect data and populate modal
           $.ajax({
               url: '/fillprospectus/' + prospectId,
               type: 'GET',
               success: function (data) {
                   // Populate edit modal fields
                   $('#edit_modalnewProspectusName').val(data.prospectus_name);
                   $('#edit_modal_contact_person').val(data.contact_person);
                   $('#edit_modal_contact_number').val(data.contact_number);
                   $('#edit_modal_address').val(data.address);
                   $('#edit_modal_email').val(data.email);
                   $('#edit_modal_business_type').val(data.business_type_id);
                   $('#edit_modal_state').val(data.state_id);
                   
                   // Load cities for selected state
                   if (data.state_id) {
                       $.ajax({
                           url: "/city/" + data.state_id,
                           type: "GET",
                           dataType: "json",
                           success: function (cities) {
                               $('#edit_modal_city').empty().append('<option value="">Select City</option>');
                               $.each(cities, function (id, name) {
                                   $('#edit_modal_city').append(`<option value="${id}">${name}</option>`);
                               });
                               $('#edit_modal_city').val(data.city_id);
                           }
                       });
                   }
                   
                   // Open the modal
                   $('#editProspectusModal').modal('show');
               },
               error: function (xhr) {
                   alert("Failed to load prospect data!");
                   console.log(xhr.responseText);
               }
           });
       } else {
           alert('No prospect found for this record!');
       }
   }

   // Function to update prospect
   function updateProspect(e) {
       e.preventDefault();

       let prospectusId = $('#edit_prospectus_id').val();
       let newProspectusName = $('#edit_modalnewProspectusName').val();
       let contact_person = $('#edit_modal_contact_person').val();
       let contact_number = $('#edit_modal_contact_number').val();
       let address = $('#edit_modal_address').val();
       let state = $('#edit_modal_state').val();
       let city = $('#edit_modal_city').val();
       let email = $('#edit_modal_email').val();
       let business_type = $('#edit_modal_business_type').val();

       $.ajax({
           url: '/updateprospectus/' + prospectusId,
           type: 'POST',
           data: {
               _token: $('meta[name="csrf-token"]').attr('content'),
               prospectus_name: newProspectusName,
               contact_person: contact_person,
               contact_number: contact_number,
               address: address,
               state_id: state,
               city_id: city,
               email: email,
               business_type_id: business_type
           },
           success: function(response) {
               $('#editProspectusModal').modal('hide');
               $('#editProspectusForm')[0].reset();
               alert('Prospect updated successfully!');
               
               // Reload the page to show updated data
               location.reload();
           },
           error: function(xhr) {
               alert('Something went wrong!');
               console.log(xhr.responseText);
           }
       });
   }

   function editRemark(id, date, remark) {
    console.log("Edit Remark Clicked:");
    console.log("ID:", id);
    console.log("Original Date:", date);
    console.log("Remark Text:", remark);

    var formattedDate = formatDateToJNY(date);
    console.log("Formatted Date:", formattedDate);

    document.getElementById('remark_id').value = id;
    document.getElementById('remark_date').value = formattedDate;
    document.getElementById('remark').value = remark;

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

    function formatDateToJNY(date) {
        if (!date) return '';
        var str = String(date);
        // Normalize to YYYY-MM-DD by stripping time if present
        var dateOnly = str.split('T')[0].split(' ')[0];
        var parts = dateOnly.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        // If already like dd/mm/yyyy, return as is
        if (str.indexOf('/') > -1) return str;
        // Fallback: try Date parsing
        var d = new Date(str);
        if (!isNaN(d.getTime())) {
            var dd = String(d.getDate()).padStart(2, '0');
            var mm = String(d.getMonth() + 1).padStart(2, '0');
            var yyyy = d.getFullYear();
            return dd + '/' + mm + '/' + yyyy;
        }
        return str;
    }


    // get status

           $(document).ready(function () {
        const selectedStatusName = "<?php echo e($first->status->status_name); ?>"; // e.g., "Cold"

        $.ajax({
            url: "<?php echo e(route('getStatuses')); ?>",
            type: 'GET',
            success: function (data) {
                $('#sales_status').empty().append('<option value="">Select Status</option>');

                $.each(data, function (key, status) {
                    const selected = status.status_name === selectedStatusName ? 'selected' : '';
                    $('#sales_status').append(`<option value="${status.id}" ${selected}>${status.status_name}</option>`);
                });
            },
            error: function () {
                alert('Failed to load sales statuses.');
            }
        });
    });

    // convert date
    document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('remark_date');

    // If input is empty, set to today's date
    if (!dateInput.value) {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        const yyyy = today.getFullYear();
        dateInput.value = `${dd}/${mm}/${yyyy}`;
    }
});


// submit remark

function submitRemark(e) {
  e.preventDefault();

let remark_id = $('#remark_id').val();
let remark_date = $('#remark_date').val();
let remark = $('#remark').val();
let ticket_value = $('#ticket_value').val();
let next_follow_up_date = $('#next_follow_up_date').val();
let sales_status = $('#sales_status').val();
let sales_record_id = $('#sales_record_id').val();

 console.log("Form Data:");
  console.log("remark_id:", remark_id);
  console.log("remark_date:", remark_date);
  console.log("remark:", remark);
  console.log("ticket_value:", ticket_value);
  console.log("next_follow_up_date:", next_follow_up_date);
  console.log("sales_status:", sales_status);
  console.log("sales_record_id:", sales_record_id);

  $.ajax({
    url: '<?php echo e(route("saveremark")); ?>', 
    method: 'POST',
    data: {
      _token: '<?php echo e(csrf_token()); ?>', 
      remark_id: remark_id,
      sales_record_id :sales_record_id,
      remark_date: remark_date,
      remark: remark,
      ticket_value: ticket_value,
      next_follow_up_date: next_follow_up_date,
      sales_status: sales_status
    },
    success: function(response) {
      console.log("Response:", response);
      alert('Remark submitted successfully!');
      // Optionally reload or reset form
    },
    error: function(xhr) {
      console.error("Error:", xhr.responseText);
      alert('Something went wrong!');
    }
  });
}

// Load states for edit modal
$(document).ready(function () {
    $.ajax({
        url: "<?php echo e(route('state')); ?>",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#edit_modal_state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select State</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });
});

// Load business types for edit modal
$.ajax({
    url: "<?php echo e(route('getbusiness')); ?>",
    type: "GET",
    success: function (data) {
        $('#edit_modal_business_type').empty().append('<option value="">Select Business Type</option>');
        $.each(data, function (index, type) {
            $('#edit_modal_business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
        });
    },
    error: function () {
        $('#edit_modal_business_type').html('<option value="">Unable to load types</option>');
    }
});

// Edit modal cities
$('#edit_modal_state').on('change', function () {
    var stateId = $(this).val();

    if (stateId) {
        $.ajax({
            url: "/city/" + stateId,
            type: "GET",
            dataType: "json",
            success: function (cities) {
                $('#edit_modal_city').empty().append('<option value="">Select City</option>');
                $.each(cities, function (id, name) {
                    $('#edit_modal_city').append(`<option value="${id}">${name}</option>`);
                });
            },
            error: function () {
                alert('Could not fetch cities.');
            }
        });
    } else {
        $('#edit_modal_city').empty().append('<option value="">Select City</option>');
    }
});

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/remark.blade.php ENDPATH**/ ?>