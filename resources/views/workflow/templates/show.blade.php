@extends('layouts.app')

@section('title', 'Workflow Template Tasks')

@section('content')
<div class="container mt-2">
    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <a href="{{ route('workflow-templates.index') }}" class="btn btn-sm btn-outline-secondary" title="Back to Templates">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div> -->

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs" id="workflowTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dependencies-tab" data-bs-toggle="tab" data-bs-target="#dependencies" type="button" role="tab" aria-controls="dependencies" aria-selected="true">
                        <i class="bi bi-diagram-3"></i> Dependencies
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="all-tasks-tab" data-bs-toggle="tab" data-bs-target="#all-tasks" type="button" role="tab" aria-controls="all-tasks" aria-selected="false">
                        <i class="bi bi-list-task"></i> All Tasks
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false">
                        <i class="bi bi-graph-up"></i> Summary
                    </button>
                </li>
            </ul>
            <a href="{{ route('workflow-templates.index') }}" class="btn btn-sm btn-outline-secondary" title="Back to Templates">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-muted">Define how tasks depend on each other</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" id="refreshDependencies" type="button">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                    <button class="btn btn-sm btn-primary" id="openTaskDependencyModal" type="button">
                        <i class="bi bi-plus-lg"></i> Add Dependency
                    </button>
                </div>
            </div>
            <div id="taskDependencyAlert" class="mb-3"></div>

            <div class="tab-content" id="workflowTabContent">
                <div class="tab-pane fade show active" id="dependencies" role="tabpanel" aria-labelledby="dependencies-tab">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle task-dependency-table" id="taskDependencyTable">
                            <thead class="table-primary-subtle">
                                <tr>
                                    <th scope="col" class="text-uppercase small">Predecessor</th>
                                    <th scope="col" class="text-uppercase small">Successor</th>
                                    <th scope="col" class="text-uppercase small">Type</th>
                                    <th scope="col" class="text-uppercase small text-center">Lag (days)</th>
                                    <th scope="col" class="text-uppercase small">Notes</th>
                                    <th scope="col" class="text-uppercase small text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="taskDependencyTableBody" class="table-group-divider">
                                <tr class="text-muted">
                                    <td colspan="6" class="py-4 text-center">
                                        <i class="bi bi-diagram-3 display-6 d-block mb-2"></i>
                                        Start by adding dependencies to organise your workflow.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="all-tasks" role="tabpanel" aria-labelledby="all-tasks-tab">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-primary-subtle">
                                <tr>
                                    <th scope="col" class="text-uppercase small">#</th>
                    <th scope="col" class="text-uppercase small">Task</th>
                                    <th scope="col" class="text-uppercase small">Owner</th>
                                    <th scope="col" class="text-uppercase small text-center">Position</th>
                                    <th scope="col" class="text-uppercase small text-center">Duration (days)</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                @forelse ($template->tasks as $index => $task)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-start">{{ $task->name }}</td>
                                        <td class="text-start">{{ $task->owner_name ?? '—' }}</td>
                                        <td class="text-center">{{ $task->position ?? '—' }}</td>
                                        <td class="text-center">{{ $task->duration_days !== null ? $task->duration_days : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr class="text-muted">
                                        <td colspan="5" class="py-4 text-center">
                                            <i class="bi bi-list-task display-6 d-block mb-2"></i>
                                            No tasks added to this template yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-icon bg-primary text-white">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Total Dependencies</div>
                                    <div class="summary-value" id="summaryTotal">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-icon bg-success text-white">
                                    <i class="bi bi-link-45deg"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Lag Enabled</div>
                                    <div class="summary-value" id="summaryLagEnabled">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <div class="summary-icon bg-warning text-white">
                                    <i class="bi bi-patch-question"></i>
                                </div>
                                <div>
                                    <div class="summary-label">Unconnected Tasks</div>
                                    <div class="summary-value" id="summaryUncovered">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6 class="text-muted text-uppercase small">Coverage Detail</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle" id="coverageTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Task</th>
                                        <th scope="col" class="text-center">Has Predecessor</th>
                                        <th scope="col" class="text-center">Has Successor</th>
                                    </tr>
                                </thead>
                                <tbody id="coverageTableBody" class="table-group-divider">
                                    <tr class="text-muted"><td colspan="3" class="py-3 text-center">Loading coverage…</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskDependencyModal" tabindex="-1" aria-labelledby="taskDependencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="taskDependencyForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskDependencyModalLabel">Add Dependency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="taskDependencyId">
                    <div class="mb-2">
                        <label class="form-label small mb-1" for="dependencyPredecessor">Predecessor Task *</label>
                        <select class="form-select form-select-sm" id="dependencyPredecessor" required></select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small mb-1" for="dependencySuccessor">Successor Task *</label>
                        <select class="form-select form-select-sm" id="dependencySuccessor" required></select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small mb-1" for="dependencyType">Dependency Type *</label>
                        <select class="form-select form-select-sm" id="dependencyType" required></select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small mb-1" for="dependencyLagDays">Lag Days</label>
                        <input type="number" class="form-control form-control-sm" id="dependencyLagDays" placeholder="0" disabled>
                        <div class="form-text" id="dependencyLagHint"></div>
                    </div>
                    <div>
                        <label class="form-label small mb-1" for="dependencyNotes">Notes</label>
                        <textarea class="form-control form-control-sm" id="dependencyNotes" rows="2" placeholder="Optional context"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save Dependency</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="taskDependencyViewModal" tabindex="-1" aria-labelledby="taskDependencyViewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDependencyViewModalLabel">Dependency Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body small">
                <dl class="row mb-0">
                    <dt class="col-4">Predecessor</dt>
                    <dd class="col-8" id="taskDependencyViewPredecessor">—</dd>
                    <dt class="col-4">Successor</dt>
                    <dd class="col-8" id="taskDependencyViewSuccessor">—</dd>
                    <dt class="col-4">Type</dt>
                    <dd class="col-8" id="taskDependencyViewType">—</dd>
                    <dt class="col-4">Lag (days)</dt>
                    <dd class="col-8" id="taskDependencyViewLag">—</dd>
                    <dt class="col-4">Notes</dt>
                    <dd class="col-8" id="taskDependencyViewNotes" style="white-space: pre-wrap;">—</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function($){
    const templateId = {{ $template->id }};
    const tasks = @json($template->tasks->map(fn($task) => ['id' => $task->id, 'name' => $task->name]));

    const dependencyTypes = new Map();
    let dependencyCache = [];

    const dependencyModalEl = document.getElementById('taskDependencyModal');
    const dependencyModal = new bootstrap.Modal(dependencyModalEl);
    const dependencyViewModalEl = document.getElementById('taskDependencyViewModal');
    const dependencyViewModal = new bootstrap.Modal(dependencyViewModalEl);

    const $dependencyForm = $('#taskDependencyForm');
    const $dependencyAlert = $('#taskDependencyAlert');
    const $dependencyTableBody = $('#taskDependencyTableBody');
    const $coverageTableBody = $('#coverageTableBody');

    const $dependencyId = $('#taskDependencyId');
    const $predecessorSelect = $('#dependencyPredecessor');
    const $successorSelect = $('#dependencySuccessor');
    const $typeSelect = $('#dependencyType');
    const $lagInput = $('#dependencyLagDays');
    const $lagHint = $('#dependencyLagHint');
    const $notesInput = $('#dependencyNotes');
    const $summaryTotal = $('#summaryTotal');
    const $summaryLagEnabled = $('#summaryLagEnabled');
    const $summaryUncovered = $('#summaryUncovered');

    function escapeHtml(value) {
        return $('<div/>').text(value ?? '').html();
    }

    function showDependencyAlert(type, message) {
        $dependencyAlert.html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
    }

    function resetDependencyForm() {
        $dependencyId.val('');
        $predecessorSelect.val('');
        $successorSelect.val('');
        $typeSelect.val('');
        toggleLagInput(null);
        $lagInput.val('');
        $notesInput.val('');
        $('#taskDependencyModalLabel').text('Add Dependency');
        $dependencyForm.removeClass('was-validated');
    }

    function toggleLagInput(typeId) {
        if (!typeId || !dependencyTypes.has(typeId)) {
            $lagInput.prop('disabled', true).val('');
            $lagHint.text('Select a dependency type to configure lag.');
            return;
        }
        const type = dependencyTypes.get(typeId);
        if (type.allows_lag) {
            $lagInput.prop('disabled', false);
            $lagHint.text('Optional. Positive values create a lag before the successor starts.');
        } else {
            $lagInput.prop('disabled', true).val('');
            $lagHint.text('This dependency type does not allow lag.');
        }
    }

    function formatTaskLabel(taskName, ownerName) {
        const parts = [];
        if (taskName) {
            parts.push(escapeHtml(taskName));
        }
        if (ownerName) {
            parts.push(escapeHtml(ownerName));
        }
        return parts.length > 0 ? parts.join(' — ') : '—';
    }

    function renderDependencyRows(dependencies) {
        dependencyCache = dependencies || [];
        if (dependencyCache.length === 0) {
            $dependencyTableBody.html('<tr class="text-muted"><td colspan="6" class="py-4 text-center"><i class="bi bi-diagram-3 display-6 d-block mb-2"></i>No dependencies yet.</td></tr>');
            updateSummary();
            updateCoverage();
            return;
        }

        const rows = dependencyCache.map(dep => {
            const notesRaw = dep.notes ? dep.notes : '';
            const notesShort = notesRaw.length > 12 ? `${escapeHtml(notesRaw.substring(0, 12))}…` : escapeHtml(notesRaw);
            const typeLabel = [dep.dependency_type_code, dep.dependency_type_name].filter(Boolean).join(' - ');
            const lag = dep.lag_days !== null && dep.lag_days !== undefined ? dep.lag_days : '—';
            const predecessorLabel = formatTaskLabel(dep.predecessor_name, dep.predecessor_owner_name);
            const successorLabel = formatTaskLabel(dep.successor_name, dep.successor_owner_name);
            return `
                <tr data-id="${dep.id}">
                    <td class="text-start">${predecessorLabel}</td>
                    <td class="text-start">${successorLabel}</td>
                    <td class="text-start">${escapeHtml(typeLabel)}</td>
                    <td class="text-center">${lag}</td>
                    <td class="text-start">
                        ${dep.notes ? `<button type="button" class="btn btn-link btn-sm p-0 task-dependency-view" data-id="${dep.id}">${notesShort}</button>` : '<span class="text-muted">—</span>'}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2 task-dependency-edit" data-id="${dep.id}"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger task-dependency-delete" data-id="${dep.id}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
        }).join('');

        $dependencyTableBody.html(rows);

        dependencyCache.forEach(dep => {
            $dependencyTableBody.find(`tr[data-id="${dep.id}"]`).data('dependency', dep);
        });

        updateSummary();
        updateCoverage();
    }

    function updateSummary() {
        const total = dependencyCache.length;
        const lagEnabled = dependencyCache.filter(dep => dep.allows_lag && dep.lag_days !== null && dep.lag_days !== undefined).length;
        $summaryTotal.text(total);
        $summaryLagEnabled.text(lagEnabled);

        const predecessorSet = new Set(dependencyCache.map(dep => dep.successor_task_id));
        const successorSet = new Set(dependencyCache.map(dep => dep.predecessor_task_id));
        const uncovered = tasks.filter(task => !predecessorSet.has(task.id) && !successorSet.has(task.id)).length;
        $summaryUncovered.text(uncovered);
    }

    function updateCoverage() {
        if (!tasks || tasks.length === 0) {
            $coverageTableBody.html('<tr class="text-muted"><td colspan="3" class="py-3 text-center">No tasks to analyse. Add tasks first.</td></tr>');
            return;
        }
        const rows = tasks.map(task => {
            const hasPred = dependencyCache.some(dep => dep.successor_task_id === task.id);
            const hasSucc = dependencyCache.some(dep => dep.predecessor_task_id === task.id);
            return `
                <tr>
                    <td class="text-start">${escapeHtml(task.name)}</td>
                    <td class="text-center">${hasPred ? '<span class="badge bg-success-subtle text-success">Yes</span>' : '<span class="badge bg-secondary-subtle text-secondary">No</span>'}</td>
                    <td class="text-center">${hasSucc ? '<span class="badge bg-success-subtle text-success">Yes</span>' : '<span class="badge bg-secondary-subtle text-secondary">No</span>'}</td>
                </tr>`;
        }).join('');
        $coverageTableBody.html(rows);
    }

    function loadDependencyTypes() {
        return $.get("{{ route('workflow-dependency-types.index') }}")
            .done(response => {
                if (response.success) {
                    dependencyTypes.clear();
                    const options = ['<option value="">Select type</option>'];
                    (response.data || []).forEach(type => {
                        dependencyTypes.set(String(type.id), {
                            id: String(type.id),
                            code: type.code,
                            name: type.name,
                            allows_lag: !!type.allows_lag,
                        });
                        options.push(`<option value="${type.id}">${escapeHtml(type.code)} - ${escapeHtml(type.name)}</option>`);
                    });
                    $typeSelect.html(options.join(''));
                } else {
                    showDependencyAlert('warning', response.message || 'Unable to load dependency types.');
                }
            })
            .fail(() => showDependencyAlert('danger', 'Failed to load dependency types.'));
    }

    function loadTaskDependencies() {
        $.get(`{{ url('/workflow/templates') }}/${templateId}/task-dependencies`)
            .done(response => {
                if (response.success) {
                    renderDependencyRows(response.data || []);
                } else {
                    showDependencyAlert('warning', response.message || 'Unable to load dependencies.');
                }
            })
            .fail(() => showDependencyAlert('danger', 'Failed to load dependencies.'));
    }

    function populateTaskSelects() {
        const options = ['<option value="">Select task</option>'];
        tasks.forEach(task => {
            options.push(`<option value="${task.id}">${escapeHtml(task.name)}</option>`);
        });
        $predecessorSelect.html(options.join(''));
        $successorSelect.html(options.join(''));
    }

    $typeSelect.on('change', function(){
        toggleLagInput($(this).val());
    });

    $('#openTaskDependencyModal').on('click', function(){
        resetDependencyForm();
        dependencyModal.show();
    });

    $('#refreshDependencies').on('click', function(){
        loadTaskDependencies();
    });

    $dependencyForm.on('submit', function(event){
        event.preventDefault();
        if (!$dependencyForm[0].checkValidity()) {
            $dependencyForm.addClass('was-validated');
            return;
        }

        const id = $dependencyId.val();
        const method = id ? 'PUT' : 'POST';
        const url = id
            ? `{{ url('/workflow/templates') }}/${templateId}/task-dependencies/${id}`
            : `{{ url('/workflow/templates') }}/${templateId}/task-dependencies`;

        const payload = {
            dependency_type_id: $typeSelect.val(),
            predecessor_task_id: $predecessorSelect.val(),
            successor_task_id: $successorSelect.val(),
            lag_days: $lagInput.prop('disabled') ? null : ($lagInput.val() || null),
            notes: (() => { const value = $notesInput.val().trim(); return value === '' ? null : value; })(),
            _token: $('meta[name="csrf-token"]').attr('content'),
        };

        $.ajax({ url, method, data: payload })
            .done(response => {
                if (response.success) {
                    dependencyModal.hide();
                    showDependencyAlert('success', response.message || 'Dependency saved.');
                    resetDependencyForm();
                    loadTaskDependencies();
                } else {
                    showDependencyAlert('warning', response.message || 'Unable to save dependency.');
                }
            })
            .fail(xhr => {
                const message = xhr.responseJSON?.message || 'Failed to save dependency.';
                showDependencyAlert('danger', message);
            });
    });

    $(document).on('click', '.task-dependency-edit', function(){
        const dep = $(this).closest('tr').data('dependency') || {};
        $dependencyId.val(dep.id || '');
        $predecessorSelect.val(String(dep.predecessor_task_id || ''));
        $successorSelect.val(String(dep.successor_task_id || ''));
        $typeSelect.val(String(dep.dependency_type_id || ''));
        toggleLagInput($typeSelect.val());
        $lagInput.val(dep.lag_days ?? '');
        $notesInput.val(dep.notes || '');
        $('#taskDependencyModalLabel').text('Edit Dependency');
        dependencyModal.show();
    });

    $(document).on('click', '.task-dependency-view', function(){
        const dep = $(this).closest('tr').data('dependency') || {};
        $('#taskDependencyViewPredecessor').html(formatTaskLabel(dep.predecessor_name, dep.predecessor_owner_name));
        $('#taskDependencyViewSuccessor').html(formatTaskLabel(dep.successor_name, dep.successor_owner_name));
        const typeLabel = [dep.dependency_type_code, dep.dependency_type_name].filter(Boolean).join(' - ');
        $('#taskDependencyViewType').text(typeLabel || '—');
        $('#taskDependencyViewLag').text(dep.lag_days ?? '—');
        $('#taskDependencyViewNotes').text(dep.notes || '—');
        dependencyViewModal.show();
    });

    $(document).on('click', '.task-dependency-delete', function(){
        const id = $(this).data('id');
        if (!id) return;
        if (!confirm('Are you sure you want to delete this dependency?')) {
            return;
        }
        $.ajax({
            url: `{{ url('/workflow/templates') }}/${templateId}/task-dependencies/${id}`,
            method: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
        })
        .done(response => {
            if (response.success) {
                showDependencyAlert('success', response.message || 'Dependency removed.');
                loadTaskDependencies();
            } else {
                showDependencyAlert('warning', response.message || 'Unable to delete dependency.');
            }
        })
        .fail(() => showDependencyAlert('danger', 'Failed to delete dependency.'));
    });

    function populateTaskSelects(){
        const options = ['<option value="">Select task</option>'];
        tasks.forEach(task => {
            options.push(`
                <option value="${task.id}">${escapeHtml(task.name)}</option>
            `);
        });
        $predecessorSelect.html(options.join(''));
        $successorSelect.html(options.join(''));
    }

    populateTaskSelects();
    loadDependencyTypes().always(loadTaskDependencies);
})(jQuery);
</script>
@endpush

@push('styles')
<style>
.task-dependency-table thead th,
.task-dependency-table tbody td {
    padding: 0.45rem 0.6rem;
    font-size: 0.85rem;
}

.task-dependency-table .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
}

#taskDependencyModal .form-select,
#taskDependencyModal .form-control {
    font-size: 0.85rem;
    padding: 0.3rem 0.5rem;
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
    padding: 0.85rem 1.1rem;
    border: 1px solid rgba(0,0,0,0.05);
}

.summary-card .summary-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    font-size: 1rem;
}

.summary-card .summary-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.15rem;
    color: #6c757d;
}

.summary-card .summary-value {
    font-size: 1.3rem;
    font-weight: 600;
}

.rule-card {
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 0.75rem;
    padding: 1rem;
    background: #ffffff;
    height: 100%;
}

.rule-card h6 {
    font-size: 0.95rem;
    margin-bottom: 0.4rem;
}

.rule-card i {
    color: #0d6efd;
    margin-right: 0.35rem;
}
</style>
@endpush


