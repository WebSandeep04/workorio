@extends('layouts.app')

@section('title', 'Workflow Templates')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
            <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>Workflow Templates</h6>
            <button class="btn btn-sm btn-light" id="openWorkflowTemplateModal" type="button">
                <i class="bi bi-plus-lg"></i> Create Template
            </button>
        </div>
        <div class="card-body">
            <div id="workflowTemplateAlert"></div>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered align-middle text-center" id="workflowTemplateTable">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col">Template Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider" id="workflowTemplateTableBody">
                        <tr class="text-muted">
                            <td colspan="3" class="py-3">No templates yet. Click "Create Template" to add your first workflow.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="workflowTemplateModal" tabindex="-1" aria-labelledby="workflowTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="workflowTemplateForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workflowTemplateModalLabel">Create Workflow Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="templateId">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="templateName" class="form-label small mb-1">Template Name *</label>
                            <input type="text" class="form-control form-control-sm" id="templateName" name="template_name" required>
                        </div>
                        <div class="col-12">
                            <label for="templateDescription" class="form-label small mb-1">Description</label>
                            <textarea class="form-control form-control-sm" id="templateDescription" name="description" rows="3" placeholder="Summarize the purpose, key milestones, and when to use this template."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small mb-0">Tasks</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="workflowTemplateAddTask">
                                    <i class="bi bi-plus-lg"></i> Add Task
                                </button>
                            </div>
                            <div class="border rounded p-2 bg-light-subtle" id="workflowTemplateTasksWrapper" style="max-height: 260px; overflow-y: auto;">
                                <div class="text-muted small text-center py-2" id="workflowTemplateTasksEmpty">No tasks added yet. Use "Add Task" to define workflow steps.</div>
                                <div id="workflowTemplateTasksContainer" class="d-flex flex-column gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save Template</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="workflowTemplateDuplicateModal" tabindex="-1" aria-labelledby="workflowTemplateDuplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="workflowTemplateDuplicateForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workflowTemplateDuplicateModalLabel">Copy Workflow Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="duplicateTemplateName" class="form-label small mb-1">New Template Name *</label>
                        <input type="text" class="form-control form-control-sm" id="duplicateTemplateName" required>
                        <div class="invalid-feedback">Please enter a name for the copied template.</div>
                    </div>
                    <div class="mb-0">
                        <label for="duplicateTemplateDescription" class="form-label small mb-1">Description</label>
                        <textarea class="form-control form-control-sm" id="duplicateTemplateDescription" rows="3" placeholder="Optional description for the copied template."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Create Copy</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function($){
    const modalEl = document.getElementById('workflowTemplateModal');
    const templateModal = new bootstrap.Modal(modalEl);
    const duplicateModalEl = document.getElementById('workflowTemplateDuplicateModal');
    const duplicateModal = new bootstrap.Modal(duplicateModalEl);
    const $form = $('#workflowTemplateForm');
    const $duplicateForm = $('#workflowTemplateDuplicateForm');
    const $alertBox = $('#workflowTemplateAlert');
    const $tableBody = $('#workflowTemplateTableBody');
    const emptyStateRow = '<tr class="text-muted"><td colspan="3" class="py-3">No templates yet. Click "Create Template" to add your first workflow.</td></tr>';
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const $tasksContainer = $('#workflowTemplateTasksContainer');
    const $tasksEmpty = $('#workflowTemplateTasksEmpty');
    const showTemplateBaseUrl = "{{ url('/workflow/templates') }}/";
    const duplicateTemplateBaseUrl = "{{ url('/workflow/templates') }}/";
    const $duplicateName = $('#duplicateTemplateName');
    const $duplicateDescription = $('#duplicateTemplateDescription');
    let workflowTemplateUsers = [];
    let usersLoaded = false;
    let duplicateTemplateId = null;

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        $alertBox.html(alertHtml);
    }

    function resetForm() {
        $('#templateId').val('');
        $('#templateName').val('');
        $('#templateDescription').val('');
        $('#workflowTemplateModalLabel').text('Create Workflow Template');
        $form.removeClass('was-validated');
        renderTaskRows([]);
    }

    function escapeHtml(value) {
        return $('<div/>').text(value ?? '').html();
    }

    function getUserNameById(id) {
        const user = workflowTemplateUsers.find(function(u){
            return String(u.id) === String(id);
        });
        return user ? user.name : null;
    }

    function renderRows(templates) {
        if (!templates || templates.length === 0) {
            $tableBody.html(emptyStateRow);
            return;
        }

        const rows = templates.map(function(template, index){
            const desc = (template.description && template.description.trim().length > 0)
                ? escapeHtml(template.description).replace(/\n/g, '<br>')
                : '<span class="text-muted">No description</span>';

            return `
                <tr data-id="${template.id}" data-template-index="${index}">
                    <td class="text-start">${escapeHtml(template.name)}</td>
                    <td class="text-start">${desc}</td>
                    <td>
                        <a href="${showTemplateBaseUrl}${template.id}/tasks" class="btn btn-sm btn-outline-secondary me-2" title="View Tasks">
                            <i class="bi bi-list-task"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-info me-2 workflow-template-copy" data-id="${template.id}" title="Duplicate Template">
                            <i class="bi bi-files"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2 workflow-template-edit" data-id="${template.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger workflow-template-delete" data-id="${template.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        $tableBody.html(rows);

        templates.forEach(function(template, index){
            const $row = $tableBody.find(`tr[data-template-index="${index}"]`);
            $row.data('template', template);
        });
    }

    function updateTaskEmptyState() {
        const hasTasks = $tasksContainer.find('.workflow-task-row').length > 0;
        if (hasTasks) {
            $tasksEmpty.addClass('d-none');
        } else {
            $tasksEmpty.removeClass('d-none');
        }
    }

    function normalizeDurationForInput(value) {
        if (!value) {
            return '';
        }
        const number = parseInt(value, 10);
        return Number.isFinite(number) && number >= 0 ? String(number) : '';
    }

    function createTaskRow(task) {
        const $row = $(`
            <div class="workflow-task-row border rounded bg-white p-2">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <input type="hidden" class="workflow-task-id">
                        <input type="text" class="form-control form-control-sm workflow-task-name" placeholder="Task name *">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm workflow-task-owner">
                            <option value="">Select owner</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" min="0" class="form-control form-control-sm workflow-task-duration" placeholder="Days" title="Estimated duration in days">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger workflow-task-remove" title="Remove task">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);

        const ownerId = task?.owner_id ? String(task.owner_id) : '';
        const ownerName = task?.owner_name || null;
        const $ownerSelect = $row.find('.workflow-task-owner');

        if (workflowTemplateUsers.length === 0) {
            $ownerSelect.prop('disabled', true).append('<option value="">No users available</option>');
        } else {
            workflowTemplateUsers.forEach(function(user){
                $ownerSelect.append(
                    $('<option>')
                        .val(String(user.id))
                        .text(user.name)
                );
            });
        }

        if (ownerId) {
            const exists = workflowTemplateUsers.some(function(user){
                return String(user.id) === ownerId;
            });
            if (!exists && ownerName) {
                $ownerSelect.append(
                    $('<option>')
                        .val(ownerId)
                        .text(ownerName + ' (inactive)')
                );
            }
            $ownerSelect.val(ownerId);
        }

        $row.find('.workflow-task-id').val(task?.id ? String(task.id) : '');
        $row.find('.workflow-task-name').val(task?.name ?? '');
        $row.find('.workflow-task-duration').val(normalizeDurationForInput(task?.duration_days ?? ''));

        $tasksContainer.append($row);
        updateTaskEmptyState();
    }

    function renderTaskRows(tasks) {
        $tasksContainer.empty();
        (tasks || []).forEach(function(task){
            createTaskRow(task);
        });
        updateTaskEmptyState();
    }

    function collectTasks() {
        const tasks = [];
        let errorMessage = null;

        $tasksContainer.find('.workflow-task-row').each(function(){
            const $row = $(this);
            const $nameInput = $row.find('.workflow-task-name');
            const $ownerSelect = $row.find('.workflow-task-owner');
            const $durationInput = $row.find('.workflow-task-duration');
            const $idInput = $row.find('.workflow-task-id');
            const name = $nameInput.val().trim();
            const ownerId = $ownerSelect.val();
            const durationValue = $durationInput.val();

            $nameInput.removeClass('is-invalid');

            if (!name && !ownerId && !durationValue) {
                return;
            }

            if (!name) {
                $nameInput.addClass('is-invalid');
                errorMessage = 'Each task must include a name.';
                return false;
            }

            let ownerName = null;
            if (ownerId) {
                ownerName = getUserNameById(ownerId) || $ownerSelect.find('option:selected').text();
            }

            const durationDays = durationValue === '' ? null : parseInt(durationValue, 10);

            tasks.push({
                id: $idInput.val() || null,
                name: name,
                owner_id: ownerId || null,
                owner_name: ownerName || null,
                duration_days: Number.isInteger(durationDays) && durationDays >= 0 ? durationDays : null,
            });
        });

        return { tasks, errorMessage };
    }

    function loadTemplates() {
        $.get("{{ route('workflow-templates.fetch') }}")
            .done(function(response){
                if (response.success) {
                    renderRows(response.data);
                } else {
                    showAlert('warning', response.message || 'Unable to load templates.');
                }
            })
            .fail(function(){
                showAlert('danger', 'Failed to load workflow templates.');
            });
    }

    function submitTemplate(id, tasks) {
        const method = id ? 'PUT' : 'POST';
        const url = id
            ? "{{ url('/workflow/templates') }}/" + id
            : "{{ route('workflow-templates.store') }}";

        const payload = {
            name: $('#templateName').val().trim(),
            description: $('#templateDescription').val().trim(),
            _token: csrfToken,
        };

        if (Array.isArray(tasks)) {
            payload.tasks = JSON.stringify(tasks);
        }

        if (!payload.name) {
            $form.addClass('was-validated');
            return;
        }

        $.ajax({
            url: url,
            method: method,
            data: payload,
        })
        .done(function(response){
            if (response.success) {
                templateModal.hide();
                showAlert('success', response.message || 'Template saved.');
                resetForm();
                loadTemplates();
            } else {
                showAlert('warning', response.message || 'Unable to save template.');
            }
        })
        .fail(function(xhr){
            const message = xhr.responseJSON?.message || 'Failed to save template.';
            showAlert('danger', message);
        });
    }

    function deleteTemplate(id) {
        $.ajax({
            url: "{{ url('/workflow/templates') }}/" + id,
            method: 'DELETE',
            data: {
                _token: csrfToken,
            },
        })
        .done(function(response){
            if (response.success) {
                showAlert('success', response.message || 'Template deleted.');
                loadTemplates();
            } else {
                showAlert('warning', response.message || 'Unable to delete template.');
            }
        })
        .fail(function(){
            showAlert('danger', 'Failed to delete template.');
        });
    }

    function loadUsers() {
        return $.get("{{ route('workflow-templates.users') }}")
            .done(function(response){
                if (response.success) {
                    workflowTemplateUsers = response.data || [];
                    usersLoaded = true;
                } else {
                    showAlert('warning', response.message || 'Unable to load users.');
                }
            })
            .fail(function(){
                showAlert('danger', 'Failed to load users for task owners.');
            });
    }

    function ensureUsersLoaded(callback) {
        if (usersLoaded) {
            callback();
            return;
        }
        loadUsers().always(callback);
    }

    // Event bindings
    $('#workflowTemplateModal').on('hidden.bs.modal', resetForm);

    $('#openWorkflowTemplateModal').on('click', function(){
        ensureUsersLoaded(function(){
            resetForm();
            $('#workflowTemplateModalLabel').text('Create Workflow Template');
            templateModal.show();
        });
    });

    $form.on('submit', function(event){
        event.preventDefault();
        const id = $('#templateId').val();
        const collected = collectTasks();

        if (collected.errorMessage) {
            showAlert('danger', collected.errorMessage);
            return;
        }

        submitTemplate(id, collected.tasks);
    });

    $(document).on('click', '.workflow-template-edit', function(){
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        const template = $row.data('template') || {};

        ensureUsersLoaded(function(){
            $('#templateId').val(id);
            $('#templateName').val(template.name || '');
            $('#templateDescription').val(template.description || '');
            renderTaskRows(template.tasks || []);

            $('#workflowTemplateModalLabel').text('Edit Workflow Template');
            templateModal.show();
        });
    });

    $(document).on('click', '.workflow-template-delete', function(){
        const id = $(this).data('id');
        if (!id) return;
        if (confirm('Are you sure you want to delete this template?')) {
            deleteTemplate(id);
        }
    });

    $(document).on('click', '.workflow-template-copy', function(){
        const $row = $(this).closest('tr');
        const template = $row.data('template') || {};
        duplicateTemplateId = template.id || null;

        if (!duplicateTemplateId) {
            showAlert('danger', 'Unable to copy this template right now.');
            return;
        }

        const suggestedName = template.name ? `${template.name} (Copy)` : '';
        $duplicateName.val(suggestedName);
        $duplicateDescription.val(template.description || '');
        $duplicateName.removeClass('is-invalid');
        duplicateModal.show();
    });

    $duplicateForm.on('submit', function(event){
        event.preventDefault();
        if (!duplicateTemplateId) {
            showAlert('danger', 'Unable to copy this template right now.');
            return;
        }

        const name = $duplicateName.val().trim();
        const description = $duplicateDescription.val().trim();

        if (!name) {
            $duplicateName.addClass('is-invalid');
            return;
        }

        $.ajax({
            url: duplicateTemplateBaseUrl + duplicateTemplateId + '/duplicate',
            method: 'POST',
            data: {
                _token: csrfToken,
                name: name,
                description: description,
            },
        })
        .done(function(response){
            if (response.success) {
                duplicateModal.hide();
                showAlert('success', response.message || 'Template copied successfully.');
                loadTemplates();
            } else {
                showAlert('warning', response.message || 'Unable to copy template.');
            }
        })
        .fail(function(xhr){
            const message = xhr.responseJSON?.message || 'Failed to copy template.';
            showAlert('danger', message);
        });
    });

    duplicateModalEl.addEventListener('hidden.bs.modal', function () {
        duplicateTemplateId = null;
        $duplicateName.val('');
        $duplicateDescription.val('');
        $duplicateName.removeClass('is-invalid');
    });

    $duplicateName.on('input', function(){
        $(this).removeClass('is-invalid');
    });

    $('#workflowTemplateAddTask').on('click', function(){
        ensureUsersLoaded(function(){
            createTaskRow();
        });
    });

    $(document).on('click', '.workflow-task-remove', function(){
        $(this).closest('.workflow-task-row').remove();
        updateTaskEmptyState();
    });

    $(document).on('input', '.workflow-task-name', function(){
        $(this).removeClass('is-invalid');
    });

    // Initial load
    loadUsers().always(function(){
        loadTemplates();
    });
})(jQuery);
</script>
@endpush
