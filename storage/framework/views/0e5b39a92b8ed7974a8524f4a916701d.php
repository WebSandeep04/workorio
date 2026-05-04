<?php $__env->startSection('title', 'Tenant Management'); ?>
<?php $__env->startSection('page_title', 'Tenant Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 py-2">
    <div class="row g-2">
        <!-- Add Tenant Form (Slim) -->
        <div class="col-lg-6">
            <div class="card border bg-white rounded-1 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2 pb-1 px-3">
                    <span class="fw-bold text-dark small">Add New Tenant</span>
                </div>
                <div class="card-body p-2 pt-0">
                    <form id="tenantForm" class="slim-form">
                        <?php echo csrf_field(); ?>
                        <div class="mb-2">
                            <label class="form-label mb-1 fw-bold text-dark" for="tenant_name" style="font-size: 0.78rem;">Tenant Name</label>
                            <input type="text" class="form-control form-control-sm border rounded-1" id="tenant_name" name="tenant_name" placeholder="Acme HQ" required style="font-size: 0.82rem;">
                            <div class="invalid-feedback d-block small mt-1" id="tenant_name_error"></div>
                        </div>

                        <!-- 1. Enabled Menus / Modules -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Modules & Menus</span>
                                <label class="text-primary cursor-pointer d-flex align-items-center" style="font-size: 0.72rem;">
                                    <input type="checkbox" class="select-all-menus me-1" checked> Select All
                                </label>
                            </div>
                            <div class="slim-scrollable-area pe-1" style="max-height: 220px;">
                                <?php
                                    $menusWithMeta = [
                                        'Sales' => ['is_sales_enabled', 'All Data, My Leads, Team Leads, Assigned Leads, Follow Up, Quotations, Payments, Lead Form'],
                                        'Worklog' => ['is_worklog_enabled', 'Timesheet, Timesheet History, Missing Entries, Approvals'],
                                        'Attendance' => ['is_attendance_enabled', 'Mark Attendance, Attendance History, Leave Requests'],
                                        'Subscription' => ['is_subscription_enabled', 'Subscriptions & Renewals'],
                                        'Document Mgmt' => ['is_document_management_enabled', 'Manage Documents, My Documents'],
                                        'Petty Cash' => ['is_petty_cash_enable', 'Petty Cash Ledger & Receipts'],
                                        'Contact Mgmt' => ['is_contact_management', 'Contact Management'],
                                        'Asset Mgmt' => ['is_asset_management_enable', 'Asset Management'],
                                        'Email Marketing' => ['is_email_marketing_enable', 'Email Marketing Campaigns'],
                                        'Tele Calling' => ['is_tally_calling_enabled', 'All Calls, List, Campaign, Lock Calling, My/Team Calls'],
                                        'Lead Generation' => ['is_leadgen_enabled', 'My Gen Leads'],
                                        'Projects' => ['is_projects_enabled', 'Projects Index & Details'],
                                        'Tracking' => ['is_tracking_enabled', 'Employee Tracking & Field Location'],
                                        'Workflow' => ['is_workflow_enabled', 'Critical Path, Templates, Dependencies'],
                                        'Calendar' => ['is_social_media_calendar_enabled', 'Social Media Calendar, Manage Calendar'],
                                        'Master' => ['is_master_enabled', 'Master Employees Directory'],
                                        'Task & Reminders' => ['is_task_reminders_enabled', 'All Tasks, Task (Assigned by me), My Tasks'],
                                        'Reports' => ['is_reports_enabled', 'Attendance Report, Timesheet Report'],
                                        'Approvals' => ['is_approval_enabled', 'Pending Approvals for Timesheets, Attendance, Task, Leave, Petty Cash']
                                    ];
                                ?>

                                <div class="row g-1 menu-checkboxes">
                                    <?php $__currentLoopData = $menusWithMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-2 mb-2 p-1 ms-1">
                                            <input type="checkbox" class="form-check-input flex-shrink-0" style="margin-top: 2px;" id="<?php echo e($meta[0]); ?>" name="<?php echo e($meta[0]); ?>" checked>
                                            <div class="lh-sm">
                                                <label class="form-check-label fw-bold text-dark small" for="<?php echo e($meta[0]); ?>"><?php echo e($label); ?></label>
                                                <div class="text-muted mt-1" style="font-size: 0.72rem; line-height: 1.25;">Unlocks: <?php echo e($meta[1]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Setup Dimensions -->
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Setup Dimensions</span>
                                <label class="text-primary cursor-pointer d-flex align-items-center" style="font-size: 0.72rem;">
                                    <input type="checkbox" class="select-all-setup me-1" checked> Select All
                                </label>
                            </div>
                            <div class="slim-scrollable-area pe-1" style="max-height: 220px;">
                                <?php
                                    $setupWithMeta = [
                                        'Core Setup' => ['is_core_setup_enabled', 'State, City, Countries'],
                                        'User Setup' => ['is_user_setup_enabled', 'User Management, Role Master'],
                                        'Master Setup' => ['is_master_setup_enabled', 'Branches, Shift, Departments, Designations, Employment/Leave/Late Reasons, Places'],
                                        'Sales Setup' => ['is_sales_setup_enabled', 'Sales Status, Lead Source, Product, Business Type, Quotation Setup'],
                                        'Tally Calling Setup' => ['is_tally_calling_setup_enabled', 'Calling Types, WhatsApp Template'],
                                        'Lead Gen Setup' => ['is_leadgen_setup_enabled', 'My Gen Leads setup configurations'],
                                        'Petty Cash Setup' => ['is_petty_cash_setup_enabled', 'Expenses, Opening Balance'],
                                        'Work Setup' => ['is_work_setup_enabled', 'Customer, Entry Types'],
                                        'Projects Setup' => ['is_projects_setup_enabled', 'Project Services, Module, Open Project'],
                                        'Attendance Setup' => ['is_attendance_setup_enabled', 'Holidays Setup'],
                                        'Task Setup' => ['is_task_setup_enabled', 'Task Status'],
                                        'Subscription Setup' => ['is_subscription_setup_enabled', 'Subscription Status'],
                                        'Calendar Setup' => ['is_calendar_setup_enabled', 'Events, Missed Reasons, Calendar Status & Checklist, Social Handles, Clients'],
                                        'Asset Management Setup' => ['is_asset_management_setup_enabled', 'Asset Types, Categories, Statuses, Suppliers, Open Assets'],
                                        'Tracking Setup' => ['is_tracking_setup_enabled', 'Employee Tracking & Field Location Setup'],
                                        'Workflow Setup' => ['is_workflow_setup_enabled', 'Critical Path & Dependencies Setup'],
                                        'Reports Setup' => ['is_reports_setup_enabled', 'Attendance & Timesheet Report Configuration'],
                                        'Document Setup' => ['is_document_setup_enabled', 'Document Management Settings'],
                                        'Contact Management Setup' => ['is_contact_management_setup_enabled', 'Contact Management Options'],
                                        'Email Marketing Setup' => ['is_email_marketing_setup_enabled', 'Email Marketing Campaign Settings']
                                    ];
                                ?>

                                <div class="row g-1 checkbox-grid">
                                    <?php $__currentLoopData = $setupWithMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-2 mb-2 p-1 ms-1">
                                            <input type="checkbox" class="form-check-input flex-shrink-0" style="margin-top: 2px;" id="<?php echo e($meta[0]); ?>" name="<?php echo e($meta[0]); ?>" checked>
                                            <div class="lh-sm">
                                                <label class="form-check-label fw-bold text-dark small" for="<?php echo e($meta[0]); ?>"><?php echo e($label); ?></label>
                                                <div class="text-muted mt-1" style="font-size: 0.72rem; line-height: 1.25;">Unlocks: <?php echo e($meta[1]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 py-1 fw-bold mt-2" style="font-size: 0.82rem;">
                            <i class="bi bi-plus-circle me-1"></i>Provision Sandbox
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tenant Table -->
        <div class="col-lg-6">
            <div class="card border bg-white rounded-1 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2 pb-1 px-3">
                    <span class="fw-bold text-dark small"><i class="bi bi-list-ul me-1"></i>Registered Sandbox Instances</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-1 py-0" style="font-size: 0.7rem;">Active</span>
                </div>
                <div class="card-body p-0 flex-fill">
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle text-nowrap compact-table" id="tenantTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-1 text-muted" style="font-size: 0.75rem;">#</th>
                                    <th class="py-1 text-muted" style="font-size: 0.75rem;">Tenant Name</th>
                                    <th class="py-1 text-muted" style="font-size: 0.75rem;">Code</th>
                                    <th class="py-1 text-muted" style="font-size: 0.75rem;">Provisioned</th>
                                    <th class="text-center py-1 text-muted pe-3" style="font-size: 0.75rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tenantTableBody">
                                <tr><td colspan="5" class="text-center py-3 text-muted" style="font-size: 0.75rem;"><i class="bi bi-arrow-repeat spin d-inline-block me-1"></i>Loading sandbox list...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Super Clean Edit Modal -->
<div class="modal fade" id="editTenantModal" tabindex="-1" role="dialog" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border rounded-1 shadow overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white py-2 px-3 d-flex align-items-center justify-content-between">
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.85rem;"><i class="bi bi-pencil me-1"></i>Edit Tenant Sandbox</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTenantForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-2 bg-white">
                    <input type="hidden" id="edit_tenant_id" name="tenant_id">
                    <div class="mb-2">
                        <label class="form-label mb-1 fw-bold" for="edit_tenant_name" style="font-size: 0.78rem;">Tenant Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm border" id="edit_tenant_name" name="tenant_name" required style="font-size: 0.82rem;">
                        <div class="invalid-feedback d-block small mt-1" id="edit_tenant_name_error"></div>
                    </div>

                    <div class="row g-2">
                        <!-- Left pane: Edit Active Modules -->
                        <div class="col-md-6 border-end">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Modules & Menus</span>
                                <label class="text-primary cursor-pointer d-flex align-items-center" style="font-size: 0.72rem;">
                                    <input type="checkbox" class="select-all-edit-menus me-1"> Select All
                                </label>
                            </div>
                            <div class="slim-scrollable-area pe-1" style="max-height: 220px;">
                                <div class="row g-1 edit-menu-checkboxes">
                                    <?php $__currentLoopData = $menusWithMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-2 mb-2 p-1 ms-1">
                                            <input type="checkbox" class="form-check-input flex-shrink-0" style="margin-top: 2px;" id="edit_<?php echo e($meta[0]); ?>" name="<?php echo e($meta[0]); ?>">
                                            <div class="lh-sm">
                                                <label class="form-check-label fw-bold text-dark small" for="edit_<?php echo e($meta[0]); ?>"><?php echo e($label); ?></label>
                                                <div class="text-muted mt-1" style="font-size: 0.72rem; line-height: 1.25;">Unlocks: <?php echo e($meta[1]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right pane: Edit Setup Features -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                                <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.72rem;">Setup Dimensions</span>
                                <label class="text-primary cursor-pointer d-flex align-items-center" style="font-size: 0.72rem;">
                                    <input type="checkbox" class="select-all-edit-setup me-1"> Select All
                                </label>
                            </div>
                            <div class="slim-scrollable-area pe-1" style="max-height: 220px;">
                                <div class="row g-1 edit-setup-checkboxes">
                                    <?php $__currentLoopData = $setupWithMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-2 mb-2 p-1 ms-1">
                                            <input type="checkbox" class="form-check-input flex-shrink-0" style="margin-top: 2px;" id="edit_<?php echo e($meta[0]); ?>" name="<?php echo e($meta[0]); ?>">
                                            <div class="lh-sm">
                                                <label class="form-check-label fw-bold text-dark small" for="edit_<?php echo e($meta[0]); ?>"><?php echo e($label); ?></label>
                                                <div class="text-muted mt-1" style="font-size: 0.72rem; line-height: 1.25;">Unlocks: <?php echo e($meta[1]); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-1 bg-light d-flex justify-content-end gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-2" data-bs-dismiss="modal" style="font-size: 0.8rem;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-2 fw-bold border-0" style="font-size: 0.8rem;">Update Sandbox</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .slim-scrollable-area {
        overflow-y: auto;
    }
    .slim-scrollable-area::-webkit-scrollbar {
        width: 4px;
    }
    .slim-scrollable-area::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }
    .compact-table th, .compact-table td {
        font-size: 0.8rem;
        padding: 0.35rem 0.5rem !important;
    }
    .action-btn {
        border: none;
        border-radius: 3px;
        padding: 0.18rem 0.35rem;
        color: #fff;
        margin: 0 0.05rem;
        font-size: 0.75rem;
    }
    .action-btn.primary { background: #3b82f6; }
    .action-btn.warning { background: #f59e0b; }
    .action-btn.danger { background: #ef4444; }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    loadTenants();

    $('#tenantForm').on('submit', function(e) {
        e.preventDefault();
        addTenant();
    });

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
        window.tenantsData = tenants;
        let html = '';
        $('#tenantCount').text(tenants.length);
        if (tenants.length === 0) {
            html = '<tr><td colspan="5" class="text-center py-2 text-muted" style="font-size:0.75rem;">No active sandbox instances</td></tr>';
        } else {
            tenants.forEach(function(tenant, index) {
                html += `
                    <tr>
                        <td class="ps-3 text-secondary" style="font-size:0.78rem;">${index + 1}</td>
                        <td class="fw-bold text-dark" style="font-size:0.8rem;">${tenant.tenant_name}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info-subtle px-1 py-0 rounded" style="font-size:0.7rem;">${tenant.tenant_code}</span>
                        </td>
                        <td style="font-size:0.78rem;">${new Date(tenant.created_at).toLocaleDateString()}</td>
                        <td class="text-center pe-3">
                            <button class="action-btn primary" onclick="editTenant(${index})" title="Edit Sandbox">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-btn warning" onclick="regenerateCode(${tenant.id})" title="Regenerate Code">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                            <button class="action-btn danger" onclick="deleteTenant(${tenant.id})" title="Delete Sandbox">
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
        $('#tenantForm input[type="checkbox"]').each(function() {
            formData.set($(this).attr('name'), $(this).is(':checked') ? 1 : 0);
        });
        
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
                    $('.select-all-menus').prop('checked', true);
                    $('.select-all-setup').prop('checked', true);
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
        formData.set('_method', 'PUT');
        $('#editTenantForm input[type="checkbox"]').each(function() {
            formData.set($(this).attr('name'), $(this).is(':checked') ? 1 : 0);
        });
        
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
        if (confirm('Are you completely sure you want to delete this tenant? All data will be permanent cleared.')) {
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
        if (confirm('Are you sure you want to recreate the tenant code?')) {
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
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        Object.keys(errors).forEach(function(key) {
            const fieldId = prefix + key;
            $(`#${fieldId}`).addClass('is-invalid');
            $(`#${fieldId}_error`).text(errors[key][0]);
        });
    }

    function showAlert(message, type) {
        const alertClass = type === 'success' ? 'alert-success text-success border-success-subtle' : 'alert-danger text-danger border-danger-subtle';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show border rounded-1 py-1 px-2 mb-2" role="alert" style="font-size:0.75rem;">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-1"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding:0.35rem 0.6rem;"></button>
            </div>
        `;
        $('.alert').remove();
        $('.container-fluid').first().prepend(alertHtml);
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Select All logic
    $('.select-all-menus').on('change', function() {
        $('.menu-checkboxes input[type="checkbox"]').prop('checked', $(this).is(':checked'));
    });

    $('.select-all-setup').on('change', function() {
        $('.checkbox-grid input[type="checkbox"]').prop('checked', $(this).is(':checked'));
    });

    $('.select-all-edit-menus').on('change', function() {
        $('.edit-menu-checkboxes input[type="checkbox"]').prop('checked', $(this).is(':checked'));
    });

    $('.select-all-edit-setup').on('change', function() {
        $('.edit-setup-checkboxes input[type="checkbox"]').prop('checked', $(this).is(':checked'));
    });
});

function updateSelectAll(containerSelector, selectAllSelector) {
    const total = $(`${containerSelector} input[type="checkbox"]`).length;
    const checked = $(`${containerSelector} input[type="checkbox"]:checked`).length;
    if (total === 0) return;
    $(selectAllSelector).prop('checked', total === checked);
}

$(document).on('change', '.menu-checkboxes input[type="checkbox"]', function() {
    updateSelectAll('.menu-checkboxes', '.select-all-menus');
});

$(document).on('change', '.checkbox-grid input[type="checkbox"]', function() {
    updateSelectAll('.checkbox-grid', '.select-all-setup');
});

$(document).on('change', '.edit-menu-checkboxes input[type="checkbox"]', function() {
    updateSelectAll('.edit-menu-checkboxes', '.select-all-edit-menus');
});

$(document).on('change', '.edit-setup-checkboxes input[type="checkbox"]', function() {
    updateSelectAll('.edit-setup-checkboxes', '.select-all-edit-setup');
});

function editTenant(index) {
    const tenant = window.tenantsData[index];
    if(!tenant) return;

    $('#edit_tenant_id').val(tenant.id);
    $('#edit_tenant_name').val(tenant.tenant_name);

    $('#editTenantForm input[type="checkbox"]').each(function() {
        const key = $(this).attr('name');
        if(tenant[key] !== undefined) {
            $(this).prop('checked', !!tenant[key]);
        } else {
            $(this).prop('checked', false);
        }
    });
    
    updateSelectAll('.edit-menu-checkboxes', '.select-all-edit-menus');
    updateSelectAll('.edit-setup-checkboxes', '.select-all-edit-setup');
    
    $('#editTenantModal').modal('show');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/tenant.blade.php ENDPATH**/ ?>