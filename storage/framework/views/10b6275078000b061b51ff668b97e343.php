

<?php $__env->startSection('title', 'Employees'); ?>
<?php $__env->startSection('page_title', 'Employees'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .compact-table {
        font-size: 0.75rem;
        font-family: Montserrat;
    }
    .compact-table thead th {
        padding: 0.3rem 0.2rem;
        font-size: 0.7rem;
        font-family: Montserrat;
        font-weight: 600;
    }
    .compact-table tbody td {
        padding: 0.25rem 0.2rem;
        vertical-align: middle;
    }
    .compact-table .btn-sm {
        padding: 0.1rem 0.25rem;
        font-size: 0.65rem;
    }
    .compact-table .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.3rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: white; color: black;">
            <h5 class="mb-0" style="font-family: Montserrat;"><i class="bi bi-people me-2"></i>Employee Master</h5>
            <button type="button" style="background: #434AFA; color: white; border: 1px solid white; font-size: 15px; padding: 6px; font-family: Montserrat;" id="openEmployeeModal">
                Add <i class="bi bi-plus"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="alert alert-danger d-none" id="employeeTableError"></div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center shadow-sm compact-table">
                    <thead class="table-secondary">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Department (Text)</th>
                            <th>Designation</th>
                            <th>Designation (Text)</th>
                            <th>Employment Type</th>
                            <th>Employment Type (Text)</th>
                            <th>Shift</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Docs</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <tr><td colspan="18" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    let employeeCache = {};
    const csrf = $('meta[name="csrf-token"]').attr('content');
    const listUrl = "<?php echo e(route('employees.list')); ?>";
    const storeUrl = "<?php echo e(route('employees.store')); ?>";
    const storageBase = "<?php echo e(asset('storage')); ?>";
    const deptOptionsUrl = "<?php echo e(route('departments.options')); ?>";
    const cityOptionsUrl = "<?php echo e(route('cities.options')); ?>";
    const branchOptions = <?php echo json_encode($branches, 15, 512) ?>;
    const designationOptions = <?php echo json_encode($designations, 15, 512) ?>;
    const employmentTypeOptions = <?php echo json_encode($employmentTypes, 15, 512) ?>;
    const shiftOptions = <?php echo json_encode($shifts, 15, 512) ?>;
    const stateOptions = <?php echo json_encode($states, 15, 512) ?>;
    const countryOptions = <?php echo json_encode($countries, 15, 512) ?>;

    function escapeHtml(text = '') {
        return (text || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function loadEmployees() {
        $('#employeeTableBody').html('<tr><td colspan="18" class="text-center">Loading...</td></tr>');
        $.get(listUrl)
            .done(function (rows) {
                employeeCache = {};
                if (!rows || rows.length === 0) {
                    $('#employeeTableBody').html('<tr><td colspan="18" class="text-center">No employees found</td></tr>');
                    return;
                }
                let html = '';
                rows.forEach(function (row) {
                    employeeCache[row.id] = row;
                    html += `<tr>
                        <td>${escapeHtml(row.employee_code)}</td>
                        <td>${escapeHtml(row.name)}</td>
                        <td>${escapeHtml(row.email || '')}</td>
                        <td>${escapeHtml(row.phone || '')}</td>
                        <td>${escapeHtml((row.branch && row.branch.name) || '')}</td>
                        <td>${escapeHtml((row.department_relation && row.department_relation.name) || '')}</td>
                        <td>${escapeHtml(row.department || '')}</td>
                        <td>${escapeHtml((row.designation_relation && row.designation_relation.title) || '')}</td>
                        <td>${escapeHtml(row.designation || '')}</td>
                        <td>${escapeHtml((row.employment_type_relation && row.employment_type_relation.name) || '')}</td>
                        <td>${escapeHtml(row.employment_type || '')}</td>
                        <td>${escapeHtml((row.shift_relation && row.shift_relation.name) || '')}</td>
                        <td>${escapeHtml((row.country_relation && row.country_relation.name) || row.country || '')}</td>
                        <td>${escapeHtml((row.state_relation && row.state_relation.state_name) || row.state || '')}</td>
                        <td>${escapeHtml((row.city_relation && row.city_relation.city_name) || row.city || '')}</td>
                        <td>${row.date_of_joining || ''}</td>
                        <td><span class="badge ${row.status === 'active' ? 'bg-success' : 'bg-secondary'}">${escapeHtml(row.status || '')}</span></td>
                        <td class="text-center">${row.documents_count || 0}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary edit-employee" data-id="${row.id}"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-danger delete-employee" data-id="${row.id}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                $('#employeeTableBody').html(html);
            })
            .fail(function () {
                $('#employeeTableBody').html('<tr><td colspan="18" class="text-center text-danger">Failed to load employees</td></tr>');
            });
    }

    function collectEmployeeForm() {
        const branchId = $('#employee_branch').val();
        const departmentSelect = $('#employee_department_select');
        const designationSelect = $('#employee_designation_select');
        const employmentTypeSelect = $('#employee_employment_type_select');
        const shiftSelect = $('#employee_shift_select');
        const stateSelect = $('#employee_state_select');
        const citySelect = $('#employee_city_select');
        const countrySelect = $('#employee_country_select');

        const departmentName = departmentSelect.val() ? departmentSelect.find('option:selected').text() : '';
        const designationName = designationSelect.val() ? designationSelect.find('option:selected').text() : '';
        const employmentTypeName = employmentTypeSelect.val() ? employmentTypeSelect.find('option:selected').text() : '';
        const stateName = stateSelect.val() ? stateSelect.find('option:selected').text() : '';
        const cityName = citySelect.val() ? citySelect.find('option:selected').text() : '';
        const countryName = countrySelect.val() ? countrySelect.find('option:selected').text() : '';

        return {
            employee_code: $('#employee_code').val().trim(),
            name: $('#employee_name').val().trim(),
            email: $('#employee_email').val().trim(),
            phone: $('#employee_phone').val().trim(),
            branch_id: branchId,
            department_id: departmentSelect.val(),
            designation_id: designationSelect.val(),
            employment_type_id: employmentTypeSelect.val(),
            shift_id: shiftSelect.val(),
            state_id: stateSelect.val(),
            city_id: citySelect.val(),
            country_id: countrySelect.val(),
            designation: designationName,
            employment_type: employmentTypeName,
            department: departmentName,
            state: stateName,
            city: cityName,
            country: countryName,
            date_of_birth: $('#employee_dob').val(),
            date_of_joining: $('#employee_doj').val(),
            personal_email: $('#employee_personal_email').val().trim(),
            blood_group: $('#employee_blood_group').val().trim(),
            marital_status: $('#employee_marital_status').val(),
            spouse_name: $('#employee_spouse_name').val().trim(),
            number_of_dependents: $('#employee_dependents').val(),
            passport_number: $('#employee_passport_number').val().trim(),
            passport_expiry: $('#employee_passport_expiry').val(),
            aadhaar_number: $('#employee_aadhaar_number').val().trim(),
            pan_number: $('#employee_pan_number').val().trim(),
            highest_qualification: $('#employee_highest_qualification').val().trim(),
            institution_name: $('#employee_institution_name').val().trim(),
            field_of_study: $('#employee_field_of_study').val().trim(),
            graduation_year: $('#employee_graduation_year').val().trim(),
            grade: $('#employee_grade').val().trim(),
            previous_employer: $('#employee_previous_employer').val().trim(),
            previous_job_title: $('#employee_previous_job_title').val().trim(),
            experience_years: $('#employee_experience_years').val(),
            skills: $('#employee_skills').val().trim(),
            bank_name: $('#employee_bank_name').val().trim(),
            bank_account_number: $('#employee_bank_account').val().trim(),
            ifsc_code: $('#employee_ifsc').val().trim(),
            uan_number: $('#employee_uan').val().trim(),
            pf_number: $('#employee_pf').val().trim(),
            esi_number: $('#employee_esi').val().trim(),
            insurance_provider: $('#employee_insurance_provider').val().trim(),
            insurance_policy_number: $('#employee_insurance_policy').val().trim(),
            insurance_valid_till: $('#employee_insurance_valid').val(),
            medical_conditions: $('#employee_medical_conditions').val().trim(),
            allergies: $('#employee_allergies').val().trim(),
            status: $('#employee_status').val().trim(),
            work_location: $('#employee_work_location').val().trim(),
            address_line: $('#employee_address').val().trim(),
            postal_code: $('#employee_postal').val().trim(),
            emergency_contact_name: $('#employee_emergency_name').val().trim(),
            emergency_contact_relation: $('#employee_emergency_relation').val().trim(),
            emergency_contact_phone: $('#employee_emergency_phone').val().trim(),
            notes: $('#employee_notes').val().trim(),
        };
    }

    function populateBranchOptions(selected) {
        const select = $('#employee_branch');
        select.empty().append('<option value="">Select Branch</option>');
        branchOptions.forEach(function (branch) {
            select.append(`<option value="${branch.id}" ${selected && Number(selected) === Number(branch.id) ? 'selected' : ''}>${escapeHtml(branch.name)}</option>`);
        });
    }

    function loadDepartmentOptions(branchId, selectedId) {
        const select = $('#employee_department_select');
        select.empty().append('<option value="">Select Department</option>');
        if (!branchId) {
            return;
        }
        $.get(deptOptionsUrl, { branch_id: branchId })
            .done(function (rows) {
                rows.forEach(function (dept) {
                    select.append(`<option value="${dept.id}" ${selectedId && Number(selectedId) === Number(dept.id) ? 'selected' : ''}>${escapeHtml(dept.name)}</option>`);
                });
            })
            .fail(function () {
                select.append('<option value="">Unable to load</option>');
            });
    }

    function populateDesignationOptions(selectedId) {
        const select = $('#employee_designation_select');
        select.empty().append('<option value="">Select Designation</option>');
        designationOptions.forEach(function (designation) {
            select.append(`<option value="${designation.id}" ${selectedId && Number(selectedId) === Number(designation.id) ? 'selected' : ''}>${escapeHtml(designation.title)}</option>`);
        });
    }

    function populateEmploymentTypeOptions(selectedId) {
        const select = $('#employee_employment_type_select');
        select.empty().append('<option value="">Select Employment Type</option>');
        employmentTypeOptions.forEach(function (type) {
            select.append(`<option value="${type.id}" ${selectedId && Number(selectedId) === Number(type.id) ? 'selected' : ''}>${escapeHtml(type.name)}</option>`);
        });
    }

    function populateShiftOptions(selectedId) {
        const select = $('#employee_shift_select');
        select.empty().append('<option value="">Select Shift</option>');
        shiftOptions.forEach(function (shift) {
            select.append(`<option value="${shift.id}" ${selectedId && Number(selectedId) === Number(shift.id) ? 'selected' : ''}>${escapeHtml(shift.name)}</option>`);
        });
    }

    function populateStateOptions(selectedId) {
        const select = $('#employee_state_select');
        select.empty().append('<option value="">Select State</option>');
        stateOptions.forEach(function (state) {
            select.append(`<option value="${state.id}" ${selectedId && Number(selectedId) === Number(state.id) ? 'selected' : ''}>${escapeHtml(state.state_name)}</option>`);
        });
    }

    function loadCityOptions(stateId, selectedId) {
        const select = $('#employee_city_select');
        select.empty().append('<option value="">Select City</option>');
        if (!stateId) {
            return;
        }
        $.get(cityOptionsUrl, { state_id: stateId })
            .done(function (rows) {
                rows.forEach(function (city) {
                    select.append(`<option value="${city.id}" ${selectedId && Number(selectedId) === Number(city.id) ? 'selected' : ''}>${escapeHtml(city.city_name)}</option>`);
                });
            })
            .fail(function () {
                select.append('<option value="">Unable to load</option>');
            });
    }

    function populateCountryOptions(selectedId) {
        const select = $('#employee_country_select');
        select.empty().append('<option value="">Select Country</option>');
        countryOptions.forEach(function (country) {
            select.append(`<option value="${country.id}" ${selectedId && Number(selectedId) === Number(country.id) ? 'selected' : ''}>${escapeHtml(country.name)}</option>`);
        });
    }

    function openEmployeeModal(data) {
        $('#employeeForm')[0].reset();
        // Reset file inputs
        $('#employee_aadhaar_document').val('');
        $('#employee_pan_document').val('');
        $('#employee_education_document').val('');
        
        var entityId = data && data.id ? data.id : '';
        $('#employee_id').val(entityId);
        $('#employeeModalLabel').text(data ? 'Edit Employee' : 'Add Employee');
        $('#employeeError').addClass('d-none').text('');
        var branchId = data ? (data.branch_id || (data.branch ? data.branch.id : '')) : '';
        var deptId = data ? (data.department_id || (data.department_relation ? data.department_relation.id : '')) : '';
        var designationId = data ? (data.designation_id || (data.designation_relation ? data.designation_relation.id : '')) : '';
        var employmentTypeId = data ? (data.employment_type_id || (data.employment_type_relation ? data.employment_type_relation.id : '')) : '';
        var shiftId = data ? (data.shift_id || (data.shift_relation ? data.shift_relation.id : '')) : '';
        var stateId = data ? (data.state_id || (data.state_relation ? data.state_relation.id : '')) : '';
        var cityId = data ? (data.city_id || (data.city_relation ? data.city_relation.id : '')) : '';
        var countryId = data ? (data.country_id || (data.country_relation ? data.country_relation.id : '')) : '';
        populateBranchOptions(branchId);
        loadDepartmentOptions(branchId, deptId);
        populateDesignationOptions(designationId);
        populateEmploymentTypeOptions(employmentTypeId);
        populateShiftOptions(shiftId);
        populateStateOptions(stateId);
        loadCityOptions(stateId, cityId);
        populateCountryOptions(countryId);
        if (data) {
            $('#employee_code').val(data.employee_code || '');
            $('#employee_name').val(data.name || '');
            $('#employee_email').val(data.email || '');
            $('#employee_phone').val(data.phone || '');
            $('#employee_designation_select').val(designationId || '');
            $('#employee_employment_type_select').val(employmentTypeId || '');
            $('#employee_shift_select').val(shiftId || '');
            $('#employee_state_select').val(stateId || '');
            $('#employee_country_select').val(countryId || '');
            $('#employee_personal_email').val(data.personal_email || '');
            $('#employee_doj').val(data.date_of_joining || '');
            $('#employee_dob').val(data.date_of_birth || '');
            $('#employee_status').val(data.status || 'active');
            $('#employee_work_location').val(data.work_location || '');
            $('#employee_address').val(data.address_line || '');
            $('#employee_blood_group').val(data.blood_group || '');
            $('#employee_marital_status').val(data.marital_status || '');
            $('#employee_spouse_name').val(data.spouse_name || '');
            $('#employee_dependents').val(data.number_of_dependents || '');
            $('#employee_passport_number').val(data.passport_number || '');
            $('#employee_passport_expiry').val(data.passport_expiry || '');
            $('#employee_aadhaar_number').val(data.aadhaar_number || '');
            $('#employee_pan_number').val(data.pan_number || '');
            $('#employee_highest_qualification').val(data.highest_qualification || '');
            $('#employee_institution_name').val(data.institution_name || '');
            $('#employee_field_of_study').val(data.field_of_study || '');
            $('#employee_graduation_year').val(data.graduation_year || '');
            $('#employee_grade').val(data.grade || '');
            $('#employee_previous_employer').val(data.previous_employer || '');
            $('#employee_previous_job_title').val(data.previous_job_title || '');
            $('#employee_experience_years').val(data.experience_years || '');
            $('#employee_skills').val(data.skills || '');
            $('#employee_bank_name').val(data.bank_name || '');
            $('#employee_bank_account').val(data.bank_account_number || '');
            $('#employee_ifsc').val(data.ifsc_code || '');
            $('#employee_uan').val(data.uan_number || '');
            $('#employee_pf').val(data.pf_number || '');
            $('#employee_esi').val(data.esi_number || '');
            $('#employee_insurance_provider').val(data.insurance_provider || '');
            $('#employee_insurance_policy').val(data.insurance_policy_number || '');
            $('#employee_insurance_valid').val(data.insurance_valid_till || '');
            $('#employee_medical_conditions').val(data.medical_conditions || '');
            $('#employee_allergies').val(data.allergies || '');
            $('#employee_postal').val(data.postal_code || '');
            $('#employee_emergency_name').val(data.emergency_contact_name || '');
            $('#employee_emergency_relation').val(data.emergency_contact_relation || '');
            $('#employee_emergency_phone').val(data.emergency_contact_phone || '');
            $('#employee_notes').val(data.notes || '');
            
            // Load and display existing documents
            loadEmployeeDocumentsForModal(data.id, data.documents || []);
        } else {
            $('#employee_status').val('active');
            // Clear document lists for new employee and show upload links
            $('#aadhaar_document_list').html('');
            $('#pan_document_list').html('');
            $('#education_document_list').html('');
            $('#aadhaar_upload_container').show();
            $('#pan_upload_container').show();
            $('#education_upload_container').show();
        }
        new bootstrap.Modal('#employeeModal').show();
    }

    function loadEmployeeDocumentsForModal(employeeId, documents) {
        // Clear existing document lists
        $('#aadhaar_document_list').html('');
        $('#pan_document_list').html('');
        $('#education_document_list').html('');
        
        // Show all upload links by default
        $('#aadhaar_upload_container').show();
        $('#pan_upload_container').show();
        $('#education_upload_container').show();
        
        if (!documents || documents.length === 0) {
            return;
        }
        
        // Group documents by type
        const aadhaarDocs = documents.filter(doc => doc.document_type === 'Aadhaar');
        const panDocs = documents.filter(doc => doc.document_type === 'PAN');
        const educationDocs = documents.filter(doc => doc.document_type === 'Education');
        
        // Hide upload link if document exists for Aadhaar
        if (aadhaarDocs.length > 0) {
            $('#aadhaar_upload_container').hide();
        }
        
        // Display Aadhaar documents
        if (aadhaarDocs.length > 0) {
            let html = '<div class="existing-documents-list">';
            aadhaarDocs.forEach(function(doc) {
                const fileUrl = doc.file_path ? storageBase + '/' + doc.file_path : '#';
                html += `
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
                                <button type="button" class="btn btn-sm btn-outline-danger delete-document-inline" 
                                        data-employee-id="${employeeId}" 
                                        data-document-id="${doc.id}" 
                                        data-document-type="Aadhaar"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#aadhaar_document_list').html(html);
        }
        
        // Display PAN documents
        if (panDocs.length > 0) {
            let html = '<div class="existing-documents-list">';
            panDocs.forEach(function(doc) {
                const fileUrl = doc.file_path ? storageBase + '/' + doc.file_path : '#';
                html += `
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
                                <button type="button" class="btn btn-sm btn-outline-danger delete-document-inline" 
                                        data-employee-id="${employeeId}" 
                                        data-document-id="${doc.id}" 
                                        data-document-type="PAN"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#pan_document_list').html(html);
        }
        
        // Hide upload link if document exists for PAN
        if (panDocs.length > 0) {
            $('#pan_upload_container').hide();
        }
        
        // Display Education documents
        if (educationDocs.length > 0) {
            let html = '<div class="existing-documents-list">';
            educationDocs.forEach(function(doc) {
                const fileUrl = doc.file_path ? storageBase + '/' + doc.file_path : '#';
                html += `
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
                                <button type="button" class="btn btn-sm btn-outline-danger delete-document-inline" 
                                        data-employee-id="${employeeId}" 
                                        data-document-id="${doc.id}" 
                                        data-document-type="Education"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $('#education_document_list').html(html);
        }
        
        // Hide upload link if document exists for Education
        if (educationDocs.length > 0) {
            $('#education_upload_container').hide();
        }
    }

    function saveEmployee() {
        const id = $('#employee_id').val();
        const payload = collectEmployeeForm();
        payload._token = csrf;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `<?php echo e(url('/employees')); ?>/${id}` : storeUrl;

        $('#saveEmployeeBtn').prop('disabled', true).text('Saving...');
        $.ajax({ url, method, data: payload })
            .done(function (response) {
                const employeeId = response.employee ? response.employee.id : id;
                
                // Upload documents if files are selected
                uploadEmployeeDocuments(employeeId, function() {
                    // Reload employee data with documents to update the modal display
                    $.get(`<?php echo e(url('/employees')); ?>/${employeeId}`)
                        .done(function(employeeData) {
                            employeeCache[employeeId] = employeeData;
                            if (employeeData.documents) {
                                loadEmployeeDocumentsForModal(employeeId, employeeData.documents);
                            }
                        });
                    bootstrap.Modal.getInstance(document.getElementById('employeeModal')).hide();
                    loadEmployees();
                    $('#saveEmployeeBtn').prop('disabled', false).text('Save');
                });
            })
            .fail(function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save employee.';
                $('#employeeError').removeClass('d-none').text(msg);
                $('#saveEmployeeBtn').prop('disabled', false).text('Save');
            });
    }

    function uploadEmployeeDocuments(employeeId, callback) {
        const documents = [];
        
        // Check for Aadhaar document
        const aadhaarFile = document.getElementById('employee_aadhaar_document').files[0];
        if (aadhaarFile) {
            documents.push({
                file: aadhaarFile,
                document_name: 'Aadhaar Card',
                document_type: 'Aadhaar'
            });
        }
        
        // Check for PAN document
        const panFile = document.getElementById('employee_pan_document').files[0];
        if (panFile) {
            documents.push({
                file: panFile,
                document_name: 'PAN Card',
                document_type: 'PAN'
            });
        }
        
        // Check for Education document
        const educationFile = document.getElementById('employee_education_document').files[0];
        if (educationFile) {
            documents.push({
                file: educationFile,
                document_name: 'Education Certificate',
                document_type: 'Education'
            });
        }
        
        // If no documents to upload, just call callback
        if (documents.length === 0) {
            if (callback) callback();
            return;
        }
        
        // Upload each document
        let uploadCount = 0;
        const totalDocs = documents.length;
        
        documents.forEach(function(doc) {
            const formData = new FormData();
            formData.append('file', doc.file);
            formData.append('document_name', doc.document_name);
            formData.append('document_type', doc.document_type);
            formData.append('_token', csrf);
            
            $.ajax({
                url: `<?php echo e(url('/employees')); ?>/${employeeId}/documents`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function() {
                // Hide upload link for the uploaded document type
                if (doc.document_type === 'Aadhaar') {
                    $('#aadhaar_upload_container').hide();
                } else if (doc.document_type === 'PAN') {
                    $('#pan_upload_container').hide();
                } else if (doc.document_type === 'Education') {
                    $('#education_upload_container').hide();
                }
                
                uploadCount++;
                if (uploadCount === totalDocs && callback) {
                    callback();
                }
            }).fail(function() {
                uploadCount++;
                if (uploadCount === totalDocs && callback) {
                    callback();
                }
            });
        });
    }

    function deleteEmployee(id) {
        if (!confirm('Delete this employee?')) return;
        $.ajax({
            url: `<?php echo e(url('/employees')); ?>/${id}`,
            method: 'DELETE',
            data: { _token: csrf },
        }).done(loadEmployees)
            .fail(() => alert('Failed to delete employee.'));
    }

    function openDocumentModal(employeeId, name) {
        $('#document_employee_id').val(employeeId);
        $('#documentEmployeeName').text(name || '');
        $('#docError').addClass('d-none').text('');
        $('#docListBody').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
        new bootstrap.Modal('#documentModal').show();
        loadDocuments(employeeId);
    }

    function loadDocuments(employeeId) {
        $.get(`<?php echo e(url('/employees')); ?>/${employeeId}/documents`)
            .done(function (rows) {
                if (!rows || rows.length === 0) {
                    $('#docListBody').html('<tr><td colspan="5" class="text-center">No documents</td></tr>');
                    return;
                }
                let html = '';
                rows.forEach(function (row) {
                    html += `<tr>
                        <td>${escapeHtml(row.document_name)}</td>
                        <td>${escapeHtml(row.document_type || '')}</td>
                        <td>${row.issued_at || ''}</td>
                        <td>${row.expires_at || ''}</td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-outline-primary" href="${row.file_path ? storageBase + '/' + row.file_path : '#'}" target="_blank"><i class="bi bi-box-arrow-up-right"></i></a>
                            <button class="btn btn-sm btn-danger delete-document" data-employee="${row.employee_id}" data-id="${row.id}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
                $('#docListBody').html(html);
            })
            .fail(function () {
                $('#docListBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load documents</td></tr>');
            });
    }

    function uploadDocument() {
        const employeeId = $('#document_employee_id').val();
        const formData = new FormData(document.getElementById('documentForm'));
        formData.append('_token', csrf);

        $('#uploadDocumentBtn').prop('disabled', true).text('Uploading...');
        $.ajax({
            url: `<?php echo e(url('/employees')); ?>/${employeeId}/documents`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function () {
            $('#documentForm')[0].reset();
            loadDocuments(employeeId);
            loadEmployees();
        }).fail(function (xhr) {
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to upload document.';
            $('#docError').removeClass('d-none').text(msg);
        }).always(function () {
            $('#uploadDocumentBtn').prop('disabled', false).text('Upload');
        });
    }

    function deleteDocument(employeeId, documentId) {
        if (!confirm('Delete this document?')) return;
        $.ajax({
            url: `<?php echo e(url('/employees')); ?>/${employeeId}/documents/${documentId}`,
            method: 'DELETE',
            data: { _token: csrf },
        }).done(function () {
            loadDocuments(employeeId);
            loadEmployees();
        }).fail(function () {
            alert('Failed to delete document.');
        });
    }

    // Event bindings
    $('#openEmployeeModal').on('click', function () { openEmployeeModal(null); });
    $('#saveEmployeeBtn').on('click', saveEmployee);
    $('#employee_branch').on('change', function () {
        loadDepartmentOptions($(this).val(), null);
    });

    $('#employee_state_select').on('change', function () {
        loadCityOptions($(this).val(), null);
    });

    $(document).on('click', '.edit-employee', function () {
        const id = $(this).data('id');
        openEmployeeModal(employeeCache[id]);
    });

    $(document).on('click', '.delete-employee', function () {
        deleteEmployee($(this).data('id'));
    });

    $(document).on('click', '.docs-employee', function () {
        openDocumentModal($(this).data('id'), $(this).data('name'));
    });

    $('#uploadDocumentBtn').on('click', uploadDocument);

    $(document).on('click', '.delete-document', function () {
        deleteDocument($(this).data('employee'), $(this).data('id'));
    });

    $(document).on('click', '.delete-document-inline', function () {
        const employeeId = $(this).data('employee-id');
        const documentId = $(this).data('document-id');
        const documentType = $(this).data('document-type');
        
        if (!confirm(`Are you sure you want to delete this ${documentType} document?`)) {
            return;
        }
        
        $.ajax({
            url: `<?php echo e(url('/employees')); ?>/${employeeId}/documents/${documentId}`,
            method: 'DELETE',
            data: { _token: csrf },
        }).done(function() {
            // Reload documents for the modal
            const employeeData = employeeCache[employeeId];
            if (employeeData) {
                $.get(`<?php echo e(url('/employees')); ?>/${employeeId}/documents`)
                    .done(function(docs) {
                        // Filter documents by type to show/hide upload links
                        const filteredDocs = docs.filter(doc => ['Aadhaar', 'PAN', 'Education'].includes(doc.document_type));
                        loadEmployeeDocumentsForModal(employeeId, filteredDocs);
                        // Also update the cache
                        employeeData.documents = filteredDocs;
                        employeeCache[employeeId] = employeeData;
                    });
            }
            loadEmployees(); // Refresh the main table
        }).fail(function() {
            alert('Failed to delete document.');
        });
    });

    $(document).ready(loadEmployees);
})();
</script>

<style>
/* Employee Modal Styling */
.employee-modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.employee-modal-header {
    background: white;
    color: #1f2937;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.75rem 1.25rem;
    border-radius: 10px 10px 0 0;
}

.employee-modal-header .modal-title {
    font-weight: 600;
    font-size: 1rem;
    font-family: Montserrat;
    color: #1f2937;
}

.modal-body {
    padding: 1rem 1.25rem;
}

/* Slim Form Styling */
.modal-body .row {
    margin-bottom: 0.5rem;
}

.modal-body .form-label {
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: #495057;
}

.modal-body .form-control-sm,
.modal-body .form-select-sm {
    font-size: 0.85rem;
    padding: 0.35rem 0.65rem;
    border-radius: 5px;
    border: 1px solid #ced4da;
}

.modal-body textarea.form-control-sm {
    font-size: 0.85rem;
    padding: 0.35rem 0.65rem;
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

/* Form Section Headers */
.modal-body h6.fw-bold.text-primary {
    color: #434AFA !important;
    font-size: 0.9rem;
    font-weight: 600;
    padding-bottom: 0.35rem;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 0.75rem;
    margin-top: 0.5rem;
    font-family: Montserrat;
}

.modal-body hr {
    margin: 0.75rem 0;
    opacity: 0.3;
    border-color: #e5e7eb;
}

/* Form Controls Focus */
.modal-body .form-control-sm:focus,
.modal-body .form-select-sm:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 0.15rem rgba(67, 74, 250, 0.2);
    outline: none;
}

/* Modal Footer */
.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 0.75rem 1.25rem;
    background: #f8f9fa;
    border-radius: 0 0 10px 10px;
}

.modal-footer .btn {
    border-radius: 5px;
    padding: 0.4rem 1.25rem;
    font-weight: 500;
    font-size: 0.85rem;
}

.modal-footer .btn-primary {
    background: #434AFA;
    border: none;
    font-family: Montserrat;
    font-weight: 500;
}

.modal-footer .btn-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(67, 74, 250, 0.25);
}

.modal-footer .btn-secondary {
    font-family: Montserrat;
    font-weight: 500;
}

/* Scrollbar Styling */
.modal-dialog-scrollable .modal-body {
    scrollbar-width: thin;
    scrollbar-color: #667eea #f1f1f1;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 10px;
}

.modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
    background: #3538d4;
}

.modal-dialog-scrollable .modal-body {
    scrollbar-color: #434AFA #f1f1f1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .employee-modal-content {
        margin: 0.5rem;
    }
    
    .document-item-card {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .document-item-card .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }
}
</style>
<?php $__env->stopPush(); ?>


<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header employee-modal-header">
                <h5 class="modal-title mb-0" id="employeeModalLabel" style="font-family: Montserrat;"><i class="bi bi-person-plus me-2"></i>Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="employeeForm">
                    <input type="hidden" id="employee_id">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Employee Code</label>
                            <input type="text" id="employee_code" class="form-control form-control-sm" placeholder="Auto-generated" readonly>
                            <small class="text-muted">Auto-generated after save.</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" id="employee_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="employee_email" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="employee_phone" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <select id="employee_branch" class="form-select form-select-sm">
                                <option value="">Select Branch</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select id="employee_department_select" class="form-select form-select-sm">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <select id="employee_designation_select" class="form-select form-select-sm">
                                <option value="">Select Designation</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select id="employee_employment_type_select" class="form-select form-select-sm">
                                <option value="">Select Employment Type</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Shift</label>
                            <select id="employee_shift_select" class="form-select form-select-sm">
                                <option value="">Select Shift</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Personal Information</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" id="employee_dob" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Personal Email</label>
                            <input type="email" id="employee_personal_email" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Joining</label>
                            <input type="date" id="employee_doj" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select id="employee_status" class="form-select form-select-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="probation">Probation</option>
                                <option value="released">Released</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Work Location</label>
                            <input type="text" id="employee_work_location" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Blood Group</label>
                            <input type="text" id="employee_blood_group" class="form-control form-control-sm" placeholder="e.g. O+">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marital Status</label>
                            <select id="employee_marital_status" class="form-select form-select-sm">
                                <option value="">Select</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Spouse Name</label>
                            <input type="text" id="employee_spouse_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Number of Dependents</label>
                            <input type="number" min="0" id="employee_dependents" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Identification Documents</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Passport Number</label>
                            <input type="text" id="employee_passport_number" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Passport Expiry</label>
                            <input type="date" id="employee_passport_expiry" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Aadhaar Number</label>
                            <input type="text" id="employee_aadhaar_number" class="form-control form-control-sm">
                            <div class="mt-1" id="aadhaar_upload_container">
                                <input type="file" id="employee_aadhaar_document" class="d-none" accept="image/*,.pdf">
                                <a href="#" class="upload-link" onclick="document.getElementById('employee_aadhaar_document').click(); return false;">
                                    <i class="bi bi-upload me-1"></i>Upload Aadhaar
                                </a>
                            </div>
                            <div id="aadhaar_document_list" class="mt-2"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" id="employee_pan_number" class="form-control form-control-sm">
                            <div class="mt-1" id="pan_upload_container">
                                <input type="file" id="employee_pan_document" class="d-none" accept="image/*,.pdf">
                                <a href="#" class="upload-link" onclick="document.getElementById('employee_pan_document').click(); return false;">
                                    <i class="bi bi-upload me-1"></i>Upload PAN
                                </a>
                            </div>
                            <div id="pan_document_list" class="mt-2"></div>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Education Details</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Highest Qualification</label>
                            <input type="text" id="employee_highest_qualification" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Institution Name</label>
                            <input type="text" id="employee_institution_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field of Study</label>
                            <input type="text" id="employee_field_of_study" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Graduation Year</label>
                            <input type="text" id="employee_graduation_year" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Grade / Percentage</label>
                            <input type="text" id="employee_grade" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">&nbsp;</label>
                            <div id="education_upload_container">
                                <input type="file" id="employee_education_document" class="d-none" accept="image/*,.pdf">
                                <a href="#" class="upload-link" onclick="document.getElementById('employee_education_document').click(); return false;">
                                    <i class="bi bi-upload me-1"></i>Upload Education Documents
                                </a>
                            </div>
                            <div id="education_document_list" class="mt-2"></div>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Previous Employment & Skills</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Previous Employer</label>
                            <input type="text" id="employee_previous_employer" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Previous Job Title</label>
                            <input type="text" id="employee_previous_job_title" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" step="0.1" min="0" id="employee_experience_years" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Key Skills</label>
                            <textarea id="employee_skills" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Banking & Payroll</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Name</label>
                            <input type="text" id="employee_bank_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account Number</label>
                            <input type="text" id="employee_bank_account" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" id="employee_ifsc" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">UAN Number</label>
                            <input type="text" id="employee_uan" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PF Number</label>
                            <input type="text" id="employee_pf" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ESI Number</label>
                            <input type="text" id="employee_esi" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2">Health & Insurance</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Insurance Provider</label>
                            <input type="text" id="employee_insurance_provider" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Policy Number</label>
                            <input type="text" id="employee_insurance_policy" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valid Till</label>
                            <input type="date" id="employee_insurance_valid" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions</label>
                            <textarea id="employee_medical_conditions" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Allergies</label>
                            <textarea id="employee_allergies" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" id="employee_address" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">State</label>
                            <select id="employee_state_select" class="form-select form-select-sm">
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">City</label>
                            <select id="employee_city_select" class="form-select form-select-sm">
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Country</label>
                            <select id="employee_country_select" class="form-select form-select-sm">
                                <option value="">Select Country</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Postal Code</label>
                            <input type="text" id="employee_postal" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" id="employee_emergency_name" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Relationship</label>
                            <input type="text" id="employee_emergency_relation" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Phone</label>
                            <input type="text" id="employee_emergency_phone" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea id="employee_notes" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </form>
                <div class="alert alert-danger d-none mt-3" id="employeeError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="saveEmployeeBtn">Save</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0">Documents for <span id="documentEmployeeName"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="document_employee_id">
                <form id="documentForm" class="border rounded p-3 mb-3 bg-light">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Document Name <span class="text-danger">*</span></label>
                            <input type="text" name="document_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Document Type</label>
                            <input type="text" name="document_type" class="form-control form-control-sm" placeholder="Offer Letter / ID Card">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Issued At</label>
                            <input type="date" name="issued_at" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expires At</label>
                            <input type="date" name="expires_at" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-sm btn-primary" id="uploadDocumentBtn"><i class="bi bi-upload me-1"></i>Upload</button>
                </div>
                <div class="alert alert-danger d-none" id="docError"></div>
                <div class="table-responsive border rounded">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-secondary text-center">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Issued</th>
                                <th>Expires</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="docListBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 </div>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Dont Delete\laravel\leadmanagement (akrati ui work)\resources\views/employees/index.blade.php ENDPATH**/ ?>