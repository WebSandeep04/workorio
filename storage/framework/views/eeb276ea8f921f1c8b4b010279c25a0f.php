

<?php $__env->startSection('title', 'Worklog'); ?>
<?php $__env->startSection('page_title', 'Worklog'); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    .container{
        font-family: Montserrat;
        font-weight: 600;
    }

    .form-control{
        background: #DFDFDF;
        font-size: 14px;
        border-radius: 3px;
    }
    .button2{
        background: #434AFA;
    }

    .card-header{
        height: 60px;
        background: #434AfA;
        color: white;
    }
    .card{
        border-radius:0px !important;
    }
    .card-header:first-child{
        border-radius:0px !important;
    }

    .sessionMain{
        background:#DFDFDF !important;
        padding:8px;
    }
    #sessionEntries{
        background:#DFDFDF;
    }

    .summarybox{
        display: flex;
        justify-content: space-between;
    }
    .submitBtn{
        display: flex;
        align-items: center;
    }

    .time{
        margin-left: 2px;
    }

    @media (max-width: 767px){
        .container{
             padding: 0.5rem;
             width: 100% !important;
        }
        .summarybox {
            flex-direction: column;
            gap: 1rem;
        }
        .sumary > div {
             margin-bottom: 0.5rem;
        }
        .submitBtn button {
             width: 100%;
        }
        .submitBtn {
            width: 100%;
            display: block;
        }
        .button2 {
            width: 100%;
            display: block;
            margin-top: 0.5rem;
        }
    }

</style>
<?php $__env->stopPush(); ?>
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" style="margin-top: 9px;">Worklog</h5>
                </div>
                <div class="card-body">
                    <form id="worklogForm">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="work_date" name="work_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="entry_type_id" class="form-label">Entry Type *</label>
                                    <select class="form-control" id="entry_type_id" name="entry_type_id" required>
                                        <option value="">Select Entry Type</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select class="form-control" id="customer_id" name="customer_id" required>
                                        <option value="">Select Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_name" class="form-label">Project *</label>
                                    <select class="form-control" id="project_name">
                                        <option value="">Select Project</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_id" class="form-label">Service *</label>
                                    <select class="form-control" id="service_id" name="service_id" required>
                                        <option value="">Select Service</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="module_id" class="form-label">Module *</label>
                                    <select class="form-control" id="module_id" name="module_id" required>
                                        <option value="">Select Module</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Time Spent *</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number" class="form-control" id="hours" name="hours" min="0" max="24" placeholder="Hours" required>
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control" id="minutes" name="minutes" min="0" max="59" placeholder="Minutes" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="">
                            <button type="submit" class="btn ms-auto button2 d-block btn-primary">Add Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: white;">
                    <h5 class="mb-0" style ="color: black;">Status</h5>
                    <div>
                        <button class="btn btn-sm button2 text-white" onclick="clearSession()">Clear All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sessionMain">
                           <div id="sessionEntries">
                                <p class="text-muted text-center">No entries in session</p>
                            </div>
                    </div>
                    <div id="sessionSummary" class="mt-3 summarybox" style="display: none;">
                    <div class="sumary">
                            <div class="d-flex justify-content-between">
                            <p>Total Time:</p>
                            <span id="totalTime" class="time">0h 0m</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>Expected Time:</p>
                            <span id="expectedTime" class="time">0h 0m</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>Status:</p>
                            <span id="timeStatus" class="badge">-</span>
                        </div>
                    </div>
                        
                        <div class="submitBtn">
                            <button class="btn btn-primary button2 "onclick="submitWorklog()" id="submitBtn" disabled>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function showAlert(type, message) {
    let colorClass = 'custom-alert-' + type;
    $('#alertBox').html(`
        <div class="custom-alert ${colorClass}">
            ${message}
            <button class="custom-alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `);
    setTimeout(() => $('.custom-alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

let selectedEntryType = null;

$(function () {
    // Set default date to today
    $('#work_date').val(new Date().toISOString().split('T')[0]);
    
    loadEntryTypes();
    loadCustomers();
    loadProjects();
    loadSessionEntries();



    function loadEntryTypes() {
        $.get("<?php echo e(route('worklog.entry-types')); ?>", function (data) {
            let options = '<option value="">Select Entry Type</option>';
            $.each(data, function (i, entryType) {
                options += `<option value="${entryType.id}" data-hours="${entryType.working_hours}">${entryType.name} (${entryType.working_hours}h)</option>`;
            });
            $('#entry_type_id').html(options);
        });
    }

    function loadCustomers() {
        $.get("<?php echo e(route('worklog.customers')); ?>", function (data) {
            let options = '<option value="">Select Customer</option>';
            $.each(data, function (i, customer) {
                options += `<option value="${customer.id}">${customer.name}</option>`;
            });
            $('#customer_id').html(options);
        });
    }

    function loadProjects() {
        $('#project_name').html('<option value="">Select Project</option>');
        $('#service_id').html('<option value="">Select Service</option>');
    }

    function loadSessionEntries() {
        $.get("<?php echo e(route('worklog.session-entries')); ?>", function (data) {
            displaySessionEntries(data);
        });
    }

    function displaySessionEntries(entries) {
        if (entries.length === 0) {
            $('#sessionEntries').html('<p class="text-muted text-center">No entries in session</p>');
            $('#sessionSummary').hide();
            return;
        }

        let html = '';
        let totalMinutes = 0;
        
        $.each(entries, function (i, entry) {
            totalMinutes += entry.total_minutes;
            html += `<div class="border rounded p-2 mb-2" style='background:white';>
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p>${entry.customer_name || 'N/A'}</p> - ${entry.service_name || entry.project_name || 'N/A'}<br>
                        <small class="text-muted">${entry.module_name}</small><br>
                        <small>${entry.hours}h ${entry.minutes}m</small>
                    </div>
                    <button class="btn btn-sm" style= "color: #434AFA"onclick="removeFromSession('${entry.id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="mt-1">
                    <small>${entry.description}</small>
                </div>
            </div>`;
        });

        $('#sessionEntries').html(html);
        
        // Update summary
        updateSessionSummary(entries, totalMinutes);
    }

    function updateSessionSummary(entries, totalMinutes) {
        if (entries.length === 0) return;
        
        const entryTypeId = entries[0].entry_type_id;
        const entryType = $('#entry_type_id option[value="' + entryTypeId + '"]');
        const expectedHours = parseInt(entryType.data('hours')) || 0;
        const expectedMinutes = expectedHours * 60;
        
        const totalHours = Math.floor(totalMinutes / 60);
        const totalMins = totalMinutes % 60;
        
        $('#totalTime').text(`${totalHours}h ${totalMins}m`);
        $('#expectedTime').text(`${expectedHours}h 0m`);
        
        let statusClass = 'bg-warning';
        let statusText = 'Incomplete';
        
        if (totalMinutes >= expectedMinutes) {
            statusClass = 'bg-success';
            statusText = 'Complete';
        } else {
            statusClass = 'bg-warning';
            statusText = 'Incomplete';
        }
        
        // Always enable submit button - let server-side validation handle the timing check
        $('#submitBtn').prop('disabled', false);
        
        $('#timeStatus').removeClass().addClass('badge ' + statusClass).text(statusText);
        $('#sessionSummary').show();
    }

    // Entry type change handler
    $('#entry_type_id').change(function() {
        selectedEntryType = $(this).val();
        if (selectedEntryType) {
            loadSessionEntries();
        }
    });

    // Date change handler for validation
    $('#work_date').change(function() {
        const selectedDate = $(this).val();
        if (selectedDate) {
            $.post("<?php echo e(route('worklog.check-date')); ?>", {
                date: selectedDate,
                _token: '<?php echo e(csrf_token()); ?>'
            }, function(response) {
                if (!response.valid) {
                    showAlert('error', response.message);
                    $('#work_date').val('');
                }
            }).fail(function() {
                showAlert('error', 'Error validating date.');
            });
        }
    });

    // Customer change handler - load projects
    $('#customer_id').change(function() {
        let customerId = $(this).val();
        if (customerId) {
            $.get(`/worklog/projects-only/customer/${customerId}`, function (data) {
                let options = '<option value="">Select Project</option>';
                $.each(data, function (i, row) {
                    options += `<option value="${row.name}">${row.name}</option>`;
                });
                $('#project_name').html(options);
                $('#service_id').html('<option value="">Select Service</option>');
                $('#module_id').html('<option value="">Select Module</option>');
            });
        } else {
            $('#project_name').html('<option value="">Select Project</option>');
            $('#service_id').html('<option value="">Select Service</option>');
            $('#module_id').html('<option value="">Select Module</option>');
        }
    });

    // Project change handler - load services list by customer+project
    $('#project_name').change(function() {
        let customerId = $('#customer_id').val();
        let projectName = $(this).val();
        if (customerId && projectName) {
            $.get(`/worklog/services/customer/${customerId}?project_name=${encodeURIComponent(projectName)}`)
                .done(function (data) {
                    let options = '<option value="">Select Service</option>';
                    if (data && data.length > 0) {
                        $.each(data, function (i, svc) {
                            options += `<option value="${svc.id}">${svc.name}</option>`;
                        });
                    } else {
                        options += '<option value="" disabled>No services available for this project</option>';
                    }
                    $('#service_id').html(options);
                    // store selected project_name for submission mapping
                    $('#service_id').data('selectedProjectName', projectName);
                    $('#module_id').html('<option value="">Select Module</option>');
                })
                .fail(function (xhr, status, error) {
                    console.error('Error loading services:', error, xhr.responseText);
                    $('#service_id').html('<option value="">Error loading services</option>');
                $('#module_id').html('<option value="">Select Module</option>');
            });
        } else {
            $('#service_id').html('<option value="">Select Service</option>');
            $('#module_id').html('<option value="">Select Module</option>');
        }
    });

    // Service change handler - load modules
    $('#service_id').change(function() {
        let serviceId = $(this).val();
        if (serviceId) {
            $.get(`/worklog/modules/${serviceId}`)
                .done(function (data) {
                let options = '<option value="">Select Module</option>';
                $.each(data, function (i, module) {
                    options += `<option value="${module.id}">${module.name}</option>`;
                });
                $('#module_id').html(options);
                })
                .fail(function (xhr, status, error) {
                    console.error('Error loading modules:', error);
                    $('#module_id').html('<option value="">Error loading modules</option>');
            });
        } else {
            $('#module_id').html('<option value="">Select Module</option>');
        }
    });

    // Form submission
    $('#worklogForm').submit(function (e) {
        e.preventDefault();
        
        $.post("<?php echo e(route('worklog.add-to-session')); ?>", {
            work_date: $('#work_date').val(),
            entry_type_id: $('#entry_type_id').val(),
            customer_id: $('#customer_id').val(),
            service_id: $('#service_id').val(),
            module_id: $('#module_id').val(),
            hours: $('#hours').val(),
            minutes: $('#minutes').val(),
            description: $('#description').val(),
            _token: '<?php echo e(csrf_token()); ?>'
        }, function (response) {
            if (response.success) {
                $('#worklogForm')[0].reset();
                $('#work_date').val(new Date().toISOString().split('T')[0]);
                // Set the entry type dropdown to match the session
                $('#entry_type_id').val(response.entry.entry_type_id);
                loadSessionEntries();
                showAlert('success', response.message);
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                showAlert('error', xhr.responseJSON.message);
            } else {
                showAlert('error', 'Error adding entry to session.');
            }
        });
    });


});

function removeFromSession(entryId) {
    $.post("<?php echo e(route('worklog.remove-from-session')); ?>", {
        entry_id: entryId,
        _token: '<?php echo e(csrf_token()); ?>'
    }, function (response) {
        if (response.success) {
            loadSessionEntries();
            // If no entries left, reset entry type dropdown
            if (response.total_entries === 0) {
                $('#entry_type_id').val('');
            }
            showAlert('success', response.message);
        }
    }).fail(function (xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            showAlert('error', xhr.responseJSON.message);
        } else {
            showAlert('error', 'Error removing entry from session.');
        }
    });
}

function clearSession() {
    if (confirm('Are you sure you want to clear all session entries?')) {
        $.post("<?php echo e(route('worklog.clear-session')); ?>", {
            _token: '<?php echo e(csrf_token()); ?>'
        }, function (response) {
            if (response.success) {
                $('#entry_type_id').val(''); // Reset entry type dropdown
                loadSessionEntries();
                showAlert('success', response.message);
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                showAlert('error', xhr.responseJSON.message);
            } else {
                showAlert('error', 'Error clearing session.');
            }
        });
    }
}

function loadSessionEntries() {
    $.get("<?php echo e(route('worklog.session-entries')); ?>", function (data) {
        displaySessionEntries(data);
        // Set entry type dropdown if there are entries
        if (data.length > 0) {
            $('#entry_type_id').val(data[0].entry_type_id);
        }
    });
}

function displaySessionEntries(entries) {
    if (entries.length === 0) {
        $('#sessionEntries').html('<p class="text-muted text-center">No entries in session</p>');
        $('#sessionSummary').hide();
        return;
    }

    let html = '';
    let totalMinutes = 0;
    
    $.each(entries, function (i, entry) {
        totalMinutes += entry.total_minutes;
        html += `<div class="border rounded p-2 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <strong>${entry.customer_name || 'N/A'}</strong> - ${entry.service_name || entry.project_name || 'N/A'}<br>
                    <small class="text-muted">${entry.module_name}</small><br>
                    <small>${entry.hours}h ${entry.minutes}m</small>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromSession('${entry.id}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mt-1">
                <small>${entry.description}</small>
            </div>
        </div>`;
    });

    $('#sessionEntries').html(html);
    
    // Update summary
    updateSessionSummary(entries, totalMinutes);
}


function submitWorklog() {
    const workDate = $('#work_date').val();
    
    if (!workDate) {
        showAlert('error', 'Please select date.');
        return;
    }
    
    // Get entry type from session entries
    $.get("<?php echo e(route('worklog.session-entries')); ?>", function (entries) {
        console.log('Session entries:', entries); // Debug log
        
        if (entries.length === 0) {
            showAlert('error', 'No entries in session to submit.');
            return;
        }
        
        const entryTypeId = entries[0].entry_type_id;
        console.log('Entry type ID:', entryTypeId); // Debug log
        
        if (!entryTypeId) {
            showAlert('error', 'Entry type not found in session.');
            return;
        }
        
        // Submit the worklog
        $.post("<?php echo e(route('worklog.submit')); ?>", {
            work_date: workDate,
            entry_type_id: entryTypeId,
            _token: '<?php echo e(csrf_token()); ?>'
        }, function (response) {
            if (response.success) {
                // Clear frontend form
                $('#worklogForm')[0].reset();
                $('#work_date').val(new Date().toISOString().split('T')[0]);
                $('#entry_type_id').val('');
                $('#customer_id').val('');
                $('#service_id').html('<option value="">Select Service</option>');
                $('#module_id').html('<option value="">Select Module</option>');
                $('#hours').val('');
                $('#minutes').val('');
                $('#description').val('');
                
                // Clear session entries display
                $('#sessionEntries').html('<p class="text-muted text-center">No entries in session</p>');
                $('#sessionSummary').hide();
                

                
                // Show success message
                showAlert('success', 'Worklog submitted successfully! Session cleared.');
            }
        }).fail(function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                showAlert('error', xhr.responseJSON.message);
            } else {
                showAlert('error', 'Error submitting worklog.');
            }
        });
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/worklog/index.blade.php ENDPATH**/ ?>