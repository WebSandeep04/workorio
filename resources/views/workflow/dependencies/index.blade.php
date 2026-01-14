#workflowDependencyModal .form-select {
    padding: 0.3rem 0.5rem;
}
@extends('layouts.app')

@section('title', 'Workflow Dependencies')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
            <h6 class="mb-0"><i class="bi bi-diagram-3-fill me-2"></i>Workflow Dependencies</h6>
            <button class="btn btn-sm btn-light" id="openWorkflowDependencyModal" type="button">
                <i class="bi bi-plus-lg"></i> Add Dependency
            </button>
        </div>
        <div class="card-body">
            <div id="workflowDependencyAlert"></div>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered align-middle text-center workflow-dependency-table" id="workflowDependencyTable">
                    <thead class="table-secondary">
                        <tr>
                            <th scope="col">Code</th>
                            <th scope="col">Dependency Name</th>
                            <th scope="col">Allows Lag?</th>
                            <th scope="col">Description</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider" id="workflowDependencyTableBody">
                        <tr class="text-muted">
                            <td colspan="5" class="py-3">No dependency types yet. Click "Add Dependency" to seed your workflow rules.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="workflowDependencyModal" tabindex="-1" aria-labelledby="workflowDependencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="workflowDependencyForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="workflowDependencyModalLabel">Add Workflow Dependency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="workflowDependencyId">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="dependencyCode" class="form-label small mb-1">Code *</label>
                            <input type="text" class="form-control form-control-sm text-uppercase" id="dependencyCode" maxlength="10" placeholder="FS" required>
                        </div>
                        <div class="col-md-5">
                            <label for="dependencyName" class="form-label small mb-1">Dependency Name *</label>
                            <input type="text" class="form-control form-control-sm" id="dependencyName" placeholder="Finish to Start" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Allows Lag?</label>
                            <select class="form-select form-select-sm" id="dependencyAllowsLag">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="dependencyDescription" class="form-label small mb-1">Description</label>
                            <textarea class="form-control form-control-sm" id="dependencyDescription" rows="3" placeholder="Explain when this dependency is applied."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save Dependency</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="workflowDependencyViewModal" tabindex="-1" aria-labelledby="workflowDependencyViewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workflowDependencyViewModalLabel">Dependency Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                <dl class="row mb-0">
                    <dt class="col-4">Code</dt>
                    <dd class="col-8" id="dependencyViewCode">—</dd>
                    <dt class="col-4">Name</dt>
                    <dd class="col-8" id="dependencyViewName">—</dd>
                    <dt class="col-4">Allows Lag?</dt>
                    <dd class="col-8" id="dependencyViewAllowsLag">—</dd>
                    <dt class="col-4">Description</dt>
                    <dd class="col-8" id="dependencyViewDescription" style="white-space: pre-wrap;">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function($){
    const modalEl = document.getElementById('workflowDependencyModal');
    const dependencyModal = new bootstrap.Modal(modalEl);
    const viewModalEl = document.getElementById('workflowDependencyViewModal');
    const dependencyViewModal = new bootstrap.Modal(viewModalEl);
    const $form = $('#workflowDependencyForm');
    const $alertBox = $('#workflowDependencyAlert');
    const $tableBody = $('#workflowDependencyTableBody');
    const emptyStateRow = '<tr class="text-muted"><td colspan="5" class="py-3">No dependency types yet. Click "Add Dependency" to seed your workflow rules.</td></tr>';
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        $alertBox.html(alertHtml);
    }

    function escapeHtml(value) {
        return $('<div/>').text(value ?? '').html();
    }

    function resetForm() {
        $('#workflowDependencyId').val('');
        $('#dependencyCode').val('');
        $('#dependencyName').val('');
        $('#dependencyAllowsLag').val('0');
        $('#dependencyDescription').val('');
        $('#dependencyDescription').val('');
        $('#workflowDependencyModalLabel').text('Add Workflow Dependency');
        $form.removeClass('was-validated');
    }

    function renderRows(dependencies) {
        if (!dependencies || dependencies.length === 0) {
            $tableBody.html(emptyStateRow);
            return;
        }

        const rows = dependencies.map(function(dependency) {
            const descRaw = dependency.description ? dependency.description : '';
            const descShort = descRaw.length > 10 ? escapeHtml(descRaw.substring(0, 10)) + '…' : escapeHtml(descRaw);
            return `
                <tr data-id="${dependency.id}">
                    <td class="text-uppercase fw-semibold">${escapeHtml(dependency.code)}</td>
                    <td class="text-start">${escapeHtml(dependency.name)}</td>
                    <td>${dependency.allows_lag ? '<span class="badge bg-success-subtle text-success">Yes</span>' : '<span class="badge bg-secondary-subtle text-secondary">No</span>'}</td>
                    <td class="text-start">
                        ${descRaw
                            ? `<button type="button" class="btn btn-link btn-sm p-0 workflow-dependency-view" data-id="${dependency.id}">${descShort}</button>`
                            : '<span class="text-muted">—</span>'}
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2 workflow-dependency-edit" data-id="${dependency.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger workflow-dependency-delete" data-id="${dependency.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        $tableBody.html(rows);

        dependencies.forEach(function(dependency){
            const $row = $tableBody.find(`tr[data-id="${dependency.id}"]`);
            $row.data('dependency', dependency);
        });
    }

    function loadDependencies() {
        $.get("{{ route('workflow-dependencies.fetch') }}")
            .done(function(response){
                if (response.success) {
                    renderRows(response.data);
                } else {
                    showAlert('warning', response.message || 'Unable to load dependencies.');
                }
            })
            .fail(function(){
                showAlert('danger', 'Failed to load dependencies.');
            });
    }

    function submitDependency(id) {
        const method = id ? 'PUT' : 'POST';
        const url = id
            ? "{{ url('/workflow/dependencies') }}/" + id
            : "{{ route('workflow-dependencies.store') }}";

        const payload = {
            code: $('#dependencyCode').val().trim(),
            name: $('#dependencyName').val().trim(),
            allows_lag: $('#dependencyAllowsLag').val() === '1' ? 1 : 0,
            description: $('#dependencyDescription').val().trim(),
            _token: csrfToken,
        };

        if (!payload.code || !payload.name) {
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
                dependencyModal.hide();
                showAlert('success', response.message || 'Dependency saved.');
                resetForm();
                loadDependencies();
            } else {
                showAlert('warning', response.message || 'Unable to save dependency.');
            }
        })
        .fail(function(xhr){
            const message = xhr.responseJSON?.message || 'Failed to save dependency.';
            showAlert('danger', message);
        });
    }

    function deleteDependency(id) {
        $.ajax({
            url: "{{ url('/workflow/dependencies') }}/" + id,
            method: 'DELETE',
            data: { _token: csrfToken },
        })
        .done(function(response){
            if (response.success) {
                showAlert('success', response.message || 'Dependency deleted.');
                loadDependencies();
            } else {
                showAlert('warning', response.message || 'Unable to delete dependency.');
            }
        })
        .fail(function(){
            showAlert('danger', 'Failed to delete dependency.');
        });
    }

    $('#workflowDependencyModal').on('hidden.bs.modal', resetForm);

    $('#openWorkflowDependencyModal').on('click', function(){
        resetForm();
        dependencyModal.show();
    });

    $form.on('submit', function(event){
        event.preventDefault();
        const id = $('#workflowDependencyId').val();
        submitDependency(id);
    });

    $(document).on('click', '.workflow-dependency-edit', function(){
        const $row = $(this).closest('tr');
        const dependency = $row.data('dependency') || {};

        $('#workflowDependencyId').val(dependency.id || '');
        $('#dependencyCode').val(dependency.code || '');
        $('#dependencyName').val(dependency.name || '');
        $('#dependencyAllowsLag').val(dependency.allows_lag ? '1' : '0');
        $('#dependencyDescription').val(dependency.description || '');
        $('#workflowDependencyModalLabel').text('Edit Workflow Dependency');

        dependencyModal.show();
    });

    $(document).on('click', '.workflow-dependency-view', function(){
        const $row = $(this).closest('tr');
        const dependency = $row.data('dependency') || {};

        $('#dependencyViewCode').text(dependency.code || '—');
        $('#dependencyViewName').text(dependency.name || '—');
        $('#dependencyViewAllowsLag').text(dependency.allows_lag ? 'Yes' : 'No');
        $('#dependencyViewDescription').text(dependency.description || '—');

        dependencyViewModal.show();
    });

    $(document).on('click', '.workflow-dependency-delete', function(){
        const id = $(this).data('id');
        if (!id) return;
        if (confirm('Are you sure you want to delete this dependency?')) {
            deleteDependency(id);
        }
    });

    loadDependencies();
})(jQuery);
</script>
@endpush

@push('styles')
<style>
.workflow-dependency-table thead th,
.workflow-dependency-table tbody td {
    padding: 0.35rem 0.5rem;
    font-size: 0.82rem;
}

.workflow-dependency-table tbody td:first-child {
    font-weight: 600;
}

.workflow-dependency-table .btn {
    padding: 0.2rem 0.35rem;
    font-size: 0.75rem;
}

#workflowDependencyModal .form-control,
#workflowDependencyModal .form-select,
#workflowDependencyModal textarea {
    font-size: 0.85rem;
    padding: 0.3rem 0.5rem;
}
</style>
@endpush

