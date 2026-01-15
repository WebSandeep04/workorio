    

<?php $__env->startSection('title', 'Tenant Management'); ?>
<?php $__env->startSection('page_title', 'Tenant Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="tenant-shell container-fluid px-3 px-md-5 py-4">
    <div class="row g-3 mb-4">
        <div class="col-xl-8">
            <div class="hero-tile gradient-tile shadow-sm h-100">
                <p class="hero-eyebrow">Control center</p>
                <h3 class="hero-title mb-2 text-white">Tenant Management</h3>
                <p class="hero-subtitle mb-0 text-white-50">Create sandbox environments, enable products, and regenerate secure access codes.</p>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="row g-3">
                <div class="col-6 col-xl-12">
                    <div class="metric-card tile shadow-sm h-100">
                        <span>Active menus</span>
                        <strong>8</strong>
                    </div>
                </div>
                <div class="col-6 col-xl-12">
                    <div class="metric-card tile shadow-sm h-100">
                        <span>Provisioned tenants</span>
                        <strong id="tenantCount">—</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch">
        <div class="col-lg-5 d-flex">
            <div class="card glass-card tile-card w-100 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Add New Tenant</h5>
                        <small class="text-muted">Provision a tenant and toggle modules in seconds.</small>
                    </div>
                    <span class="chip chip-primary">Wizard</span>
                </div>
                <div class="card-body p-4">
                    <form id="tenantForm" class="modern-form">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label" for="tenant_name">Tenant Name</label>
                            <input type="text" class="form-control form-control-modern" id="tenant_name" name="tenant_name" placeholder="Acme HQ" required>
                            <div class="invalid-feedback" id="tenant_name_error"></div>
                        </div>
                        <div class="menu-grid">
                            <label class="form-label">Enabled Menus</label>
                            <div class="menu-checkboxes">
                                <?php
                                    $menus = [
                                        'Setup' => 'is_setup_enabled',
                                        'Sales' => 'is_sales_enabled',
                                        'Worklog' => 'is_worklog_enabled',
                                        'Attendance' => 'is_attendance_enabled',
                                        'Subscription' => 'is_subscription_enabled',
                                        'Document Mgmt' => 'is_document_management_enabled',
                                        'Sales Setup' => 'is_sales_setup_enabled',
                                        'Work Setup' => 'is_work_setup_enabled',
                                        'User Setup' => 'is_user_setup_enabled',
                                        'Petty Cash' => 'is_petty_cash_enable',
                                    ];
                                ?>
                                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="modern-checkbox">
                                        <input type="checkbox" id="<?php echo e($id); ?>" name="<?php echo e($id); ?>" checked>
                                        <span><?php echo e($label); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-gradient w-100 mt-3 text-white">
                            <i class="bi bi-plus-circle me-2"></i>Provision Tenant
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-flex">
            <div class="card glass-card tile-card w-100 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Tenant List</h5>
                        <small class="text-muted">Manage modules, regenerate codes, and edit tenant metadata.</small>
                    </div>
                    <span class="chip chip-dark text-uppercase">Live</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive tenant-table-wrapper">
                        <table class="table tenant-table mb-0" id="tenantTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tenant Name</th>
                                    <th>Tenant Code</th>
                                    <th>Created</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenantTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Tenant Modal -->
<?php $__env->startPush('styles'); ?>
<style>
    .tenant-shell {
        background: #f5f7ff;
        border-radius: 28px;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .hero-tile {
        border-radius: 26px;
        padding: 1.8rem 2rem;
        color: #fff;
    }

    .gradient-tile {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
    }

    .hero-eyebrow {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        opacity: 0.75;
    }

    .hero-title {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .hero-subtitle {
        opacity: 0.85;
        font-size: 0.95rem;
    }

    .tile {
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 20px 35px rgba(15, 23, 42, 0.08);
    }

    .metric-card {
        background: #fff;
        border-radius: 18px;
        padding: 0.85rem 1rem;
        min-width: 140px;
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.1);
    }

    .metric-card span {
        display: block;
        font-size: 0.65rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.6;
    }

    .metric-card strong {
        font-size: 1.1rem;
    }

    .tile-card {
        border-radius: 24px;
        border: none;
        background: #ffffff;
        box-shadow: 0 25px 40px rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .chip {
        border-radius: 999px;
        padding: 0.25rem 0.85rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chip-dark {
        background: rgba(15, 23, 42, 0.08);
        color: #0f172a;
    }

    .chip-primary {
        background: rgba(59, 130, 246, 0.15);
        color: #2563eb;
    }

    .modern-form .form-control-modern {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0f172a;
    }

    .modern-form .form-control-modern:focus {
        background: #fff;
        border-color: #2563eb;
        color: #0f172a;
        box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.15);
    }

    .menu-grid {
        margin-top: 1rem;
    }

    .menu-checkboxes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.5rem;
    }

    .modern-checkbox {
        background: #eef2ff;
        border-radius: 12px;
        padding: 0.4rem 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
        border: 1px solid transparent;
    }

    .modern-checkbox input {
        accent-color: #22d3ee;
    }

    .modern-checkbox:hover {
        border-color: rgba(79, 70, 229, 0.35);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #22d3ee, #3b82f6);
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 14px;
        padding: 0.6rem 1rem;
        box-shadow: 0 20px 30px rgba(59, 130, 246, 0.25);
    }

    .tenant-table-wrapper {
        border-radius: 0 0 24px 24px;
        overflow: hidden;
    }

    .tenant-table {
        color: #0f172a;
    }

    .tenant-table thead {
        background: rgba(243, 244, 255, 0.8);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.72rem;
    }

    .tenant-table tbody tr {
        background: #fff;
    }

    .tenant-table tbody tr + tr {
        border-top: 1px solid rgba(15, 23, 42, 0.06);
    }

    .tenant-table .badge {
        background: rgba(99, 102, 241, 0.15);
        color: #4338ca;
        border-radius: 999px;
    }

    .tenant-table .action-btn {
        border: none;
        border-radius: 12px;
        padding: 0.4rem 0.55rem;
        color: #fff;
        margin: 0 0.2rem;
    }

    .action-btn.primary { background: rgba(59, 130, 246, 0.75); }
    .action-btn.warning { background: rgba(249, 115, 22, 0.8); }
    .action-btn.danger { background: rgba(239, 68, 68, 0.85); }

    @media (max-width: 992px) {
        .tenant-hero {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<!-- Edit Tenant Modal -->
<div class="modal fade" id="editTenantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tenant</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editTenantForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="edit_tenant_id" name="tenant_id">
                    <div class="form-group">
                        <label for="edit_tenant_name">Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_tenant_name" name="tenant_name" required>
                        <div class="invalid-feedback" id="edit_tenant_name_error"></div>
                    </div>
                    <div class="form-group">
                        <label>Enabled Menus</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_setup_enabled" name="is_setup_enabled">
                                <label class="form-check-label" for="edit_is_setup_enabled">Setup</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_sales_enabled" name="is_sales_enabled">
                                <label class="form-check-label" for="edit_is_sales_enabled">Sales</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_worklog_enabled" name="is_worklog_enabled">
                                <label class="form-check-label" for="edit_is_worklog_enabled">Worklog</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_attendance_enabled" name="is_attendance_enabled">
                                <label class="form-check-label" for="edit_is_attendance_enabled">Attendance</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_subscription_enabled" name="is_subscription_enabled">
                                <label class="form-check-label" for="edit_is_subscription_enabled">Subscription</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_document_management_enabled" name="is_document_management_enabled">
                                <label class="form-check-label" for="edit_is_document_management_enabled">Document Mgmt</label>
                            </div>
                            <div class="w-100"></div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_sales_setup_enabled" name="is_sales_setup_enabled">
                                <label class="form-check-label" for="edit_is_sales_setup_enabled">Sales Setup</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_work_setup_enabled" name="is_work_setup_enabled">
                                <label class="form-check-label" for="edit_is_work_setup_enabled">Work Setup</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_user_setup_enabled" name="is_user_setup_enabled">
                                <label class="form-check-label" for="edit_is_user_setup_enabled">User Setup</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="edit_is_petty_cash_enable" name="is_petty_cash_enable">
                                <label class="form-check-label" for="edit_is_petty_cash_enable">Petty Cash</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Load tenants on page load
    loadTenants();

    // Handle form submission
    $('#tenantForm').on('submit', function(e) {
        e.preventDefault();
        addTenant();
    });

    // Handle edit form submission
    $('#editTenantForm').on('submit', function(e) {
        e.preventDefault();
        updateTenant();
    });

    function loadTenants() {
        $.ajax({
            url: '<?php echo e(route("tenant.fetch")); ?>',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayTenants(response.data);
                } else {
                    showAlert('Error loading tenants', 'error');
                }
            },
            error: function() {
                showAlert('Error loading tenants', 'error');
            }
        });
    }

    function displayTenants(tenants) {
        let html = '';
        $('#tenantCount').text(tenants.length);
        if (tenants.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No tenants found</td></tr>';
        } else {
            tenants.forEach(function(tenant, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${tenant.tenant_name}</td>
                        <td>
                            <span class="badge badge-info">${tenant.tenant_code}</span>
                        </td>
                        <td>${new Date(tenant.created_at).toLocaleDateString()}</td>
                        <td class="text-center">
                            <button class="action-btn primary" onclick="editTenant(${tenant.id}, '${tenant.tenant_name}', ${tenant.is_setup_enabled ? 1 : 0}, ${tenant.is_sales_enabled ? 1 : 0}, ${tenant.is_worklog_enabled ? 1 : 0}, ${tenant.is_attendance_enabled ? 1 : 0}, ${tenant.is_subscription_enabled ? 1 : 0}, ${tenant.is_document_management_enabled ? 1 : 0}, ${tenant.is_sales_setup_enabled ? 1 : 0}, ${tenant.is_work_setup_enabled ? 1 : 0}, ${tenant.is_user_setup_enabled ? 1 : 0}, ${tenant.is_petty_cash_enable ? 1 : 0})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-btn warning" onclick="regenerateCode(${tenant.id})">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <button class="action-btn danger" onclick="deleteTenant(${tenant.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#tenantTableBody').html(html);
    }

    function addTenant() {
        const formData = new FormData($('#tenantForm')[0]);
        // Ensure checkboxes always submit explicit 1/0
        formData.set('is_setup_enabled', $('#is_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_sales_enabled', $('#is_sales_enabled').is(':checked') ? 1 : 0);
        formData.set('is_worklog_enabled', $('#is_worklog_enabled').is(':checked') ? 1 : 0);
        formData.set('is_attendance_enabled', $('#is_attendance_enabled').is(':checked') ? 1 : 0);
        formData.set('is_subscription_enabled', $('#is_subscription_enabled').is(':checked') ? 1 : 0);
        formData.set('is_document_management_enabled', $('#is_document_management_enabled').is(':checked') ? 1 : 0);
        formData.set('is_sales_setup_enabled', $('#is_sales_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_work_setup_enabled', $('#is_work_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_user_setup_enabled', $('#is_user_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_petty_cash_enable', $('#is_petty_cash_enable').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: '<?php echo e(route("tenant.store")); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#tenantForm')[0].reset();
                    loadTenants();
                } else {
                    showValidationErrors(response.errors);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors);
                } else {
                    showAlert('Error adding tenant', 'error');
                }
            }
        });
    }

    function updateTenant() {
        const tenantId = $('#edit_tenant_id').val();
        const formData = new FormData($('#editTenantForm')[0]);
        // Laravel-friendly method override and checkbox values
        formData.set('_method', 'PUT');
        formData.set('is_setup_enabled', $('#edit_is_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_sales_enabled', $('#edit_is_sales_enabled').is(':checked') ? 1 : 0);
        formData.set('is_worklog_enabled', $('#edit_is_worklog_enabled').is(':checked') ? 1 : 0);
        formData.set('is_attendance_enabled', $('#edit_is_attendance_enabled').is(':checked') ? 1 : 0);
        formData.set('is_subscription_enabled', $('#edit_is_subscription_enabled').is(':checked') ? 1 : 0);
        formData.set('is_document_management_enabled', $('#edit_is_document_management_enabled').is(':checked') ? 1 : 0);
        formData.set('is_sales_setup_enabled', $('#edit_is_sales_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_work_setup_enabled', $('#edit_is_work_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_user_setup_enabled', $('#edit_is_user_setup_enabled').is(':checked') ? 1 : 0);
        formData.set('is_petty_cash_enable', $('#edit_is_petty_cash_enable').is(':checked') ? 1 : 0);
        
        $.ajax({
            url: `/tenant/${tenantId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#editTenantModal').modal('hide');
                    loadTenants();
                } else {
                    showValidationErrors(response.errors, 'edit_');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors, 'edit_');
                } else {
                    showAlert('Error updating tenant', 'error');
                }
            }
        });
    }

    function deleteTenant(tenantId) {
        if (confirm('Are you sure you want to delete this tenant?')) {
            $.ajax({
                url: `/tenant/${tenantId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadTenants();
                    } else {
                        showAlert('Error deleting tenant', 'error');
                    }
                },
                error: function() {
                    showAlert('Error deleting tenant', 'error');
                }
            });
        }
    }

    function regenerateCode(tenantId) {
        if (confirm('Are you sure you want to regenerate the tenant code?')) {
            $.ajax({
                url: `/tenant/${tenantId}/regenerate-code`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        loadTenants();
                    } else {
                        showAlert('Error regenerating code', 'error');
                    }
                },
                error: function() {
                    showAlert('Error regenerating code', 'error');
                }
            });
        }
    }

    function showValidationErrors(errors, prefix = '') {
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Show new errors
        Object.keys(errors).forEach(function(key) {
            const fieldId = prefix + key;
            $(`#${fieldId}`).addClass('is-invalid');
            $(`#${fieldId}_error`).text(errors[key][0]);
        });
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of the container
        $('.tenant-shell').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
});

// Global functions for onclick handlers
function editTenant(tenantId, tenantName, setup, sales, worklog, attendance, subscription, documentMgmt, salesSetup, workSetup, userSetup, pettyCash) {
    $('#edit_tenant_id').val(tenantId);
    $('#edit_tenant_name').val(tenantName);
    $('#edit_is_setup_enabled').prop('checked', !!setup);
    $('#edit_is_sales_enabled').prop('checked', !!sales);
    $('#edit_is_worklog_enabled').prop('checked', !!worklog);
    $('#edit_is_attendance_enabled').prop('checked', !!attendance);
    $('#edit_is_subscription_enabled').prop('checked', !!subscription);
    $('#edit_is_document_management_enabled').prop('checked', !!documentMgmt);
    $('#edit_is_sales_setup_enabled').prop('checked', !!salesSetup);
    $('#edit_is_work_setup_enabled').prop('checked', !!workSetup);
    $('#edit_is_user_setup_enabled').prop('checked', !!userSetup);
    $('#edit_is_petty_cash_enable').prop('checked', !!pettyCash);
    $('#editTenantModal').modal('show');
}

function deleteTenant(tenantId) {
    if (confirm('Are you sure you want to delete this tenant?')) {
        $.ajax({
            url: `/tenant/${tenantId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadTenants();
                } else {
                    showAlert('Error deleting tenant', 'error');
                }
            },
            error: function() {
                showAlert('Error deleting tenant', 'error');
            }
        });
    }
}

function regenerateCode(tenantId) {
    if (confirm('Are you sure you want to regenerate the tenant code?')) {
        $.ajax({
            url: `/tenant/${tenantId}/regenerate-code`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    loadTenants();
                } else {
                    showAlert('Error regenerating code', 'error');
                }
            },
            error: function() {
                showAlert('Error regenerating code', 'error');
            }
        });
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/tenant.blade.php ENDPATH**/ ?>