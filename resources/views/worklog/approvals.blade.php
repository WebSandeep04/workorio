@extends('layouts.app')

@section('title', 'Worklog Approvals')
@section('page_title', 'Worklog Approvals')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header text-white" style="background-color: #434afa; padding: 0.75rem 1rem;">
                    <h5 class="mb-0">Pending Worklog Approvals</h5>
                </div>
                <div class="card-body">
                    <div id="groupedApprovals">
                        <!-- Grouped approvals will be loaded via jQuery -->
                    </div>
                </div>
                    
                    <!-- No Data Message -->
                    <div id="noDataMessage" class="text-center py-4" style="display: none;">
                        <i class="bi bi-check-circle fs-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No pending approvals</h5>
                        <p class="text-muted">All worklog entries have been reviewed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

$(function () {
    loadPendingApprovals();
});

function loadPendingApprovals() {
    $.get("{{ route('worklog.pending-approvals') }}", function (data) {
        if (data.length === 0) {
            $('#groupedApprovals').hide();
            $('#noDataMessage').show();
        } else {
            $('#groupedApprovals').show();
            $('#noDataMessage').hide();
            
            let html = '';
            $.each(data, function (i, group) {
                const totalTime = group.entries.reduce((total, entry) => {
                    return total + (parseInt(entry.hours) * 60 + parseInt(entry.minutes));
                }, 0);
                const totalHours = Math.floor(totalTime / 60);
                const totalMinutes = totalTime % 60;
                const timeDisplay = `${totalHours}h ${totalMinutes}m`;
                
                html += `
                <div class="card mb-3" style="border: 1px solid #e0e0e0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #434afa; padding: 0.75rem 1rem;">
                        <h6 class="mb-0">
                            <i class="bi bi-person-circle me-2"></i>
                            ${group.user_name} - ${group.work_date.split('T')[0]}
                        </h6>
                        <div>
                            <span class="badge bg-white text-dark me-2 shadow-sm" style="padding: 0.5rem 0.8rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">${group.entries.length} entries</span>
                            <span class="badge bg-white text-dark shadow-sm" style="padding: 0.5rem 0.8rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">Total: ${timeDisplay}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Entry Type</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Project</th>
                                        <th scope="col">Module</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                $.each(group.entries, function (j, worklog) {
                    const timeDisplay = `${worklog.hours}h ${worklog.minutes}m`;
                    
                    html += `<tr>
                        <td>${worklog.entry_type ? worklog.entry_type.name : 'N/A'}</td>
                        <td>${worklog.customer ? worklog.customer.name : 'N/A'}</td>
                        <td>${worklog.project ? worklog.project.name : 'N/A'}</td>
                        <td>${worklog.module ? worklog.module.name : 'N/A'}</td>
                        <td>${timeDisplay}</td>
                        <td>
                            <div class="text-start">
                                <small>${worklog.description}</small>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm text-white approveBtn" data-id="${worklog.id}" title="Approve" style="background-color: #434afa; border:none; border-radius: 4px; padding: 0.25rem 0.5rem;">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-danger rejectBtn" data-id="${worklog.id}" title="Reject" style="border:none; border-radius: 4px; padding: 0.25rem 0.5rem;">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Group Actions:</small>
                            <div>
                                <button class="btn btn-sm text-white approveGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Approve All" style="background-color: #434afa; border:none; border-radius: 4px; padding: 0.4rem 1rem; margin-right: 0.5rem;">
                                    <i class="bi bi-check-circle"></i> Approve All
                                </button>
                                <button class="btn btn-sm btn-danger rejectGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Reject All" style="border:none; border-radius: 4px; padding: 0.4rem 1rem;">
                                    <i class="bi bi-x-circle"></i> Reject All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            $('#groupedApprovals').html(html);
        }
    }).fail(function () {
        showAlert('error', 'Error loading pending approvals.');
    });
}

// Open modal helpers
function openApprovalModal({worklogId=null, userName=null, workDate=null, isGroup=false}) {
    const modal = $('#approvalModal');
    modal.find('input[name="worklog_id"]').val(worklogId || '');
    modal.find('input[name="user_name"]').val(userName || '');
    modal.find('input[name="work_date"]').val(workDate || '');
    modal.find('input[name="is_group"]').val(isGroup ? '1' : '0');
    modal.modal('show');
}

function openRejectionModal({worklogId=null, userName=null, workDate=null, isGroup=false}) {
    const modal = $('#rejectionModal');
    modal.find('input[name="worklog_id"]').val(worklogId || '');
    modal.find('input[name="user_name"]').val(userName || '');
    modal.find('input[name="work_date"]').val(workDate || '');
    modal.find('input[name="is_group"]').val(isGroup ? '1' : '0');
    modal.modal('show');
}

// Approve worklog
$(document).on('click', '.approveBtn', function () {
    const worklogId = $(this).data('id');
    openApprovalModal({worklogId});
});

// Reject worklog
$(document).on('click', '.rejectBtn', function () {
    const worklogId = $(this).data('id');
    openRejectionModal({worklogId});
});

// Approve group
$(document).on('click', '.approveGroupBtn', function () {
    const userName = $(this).data('user');
    const workDate = $(this).data('date');
    openApprovalModal({userName, workDate, isGroup: true});
});

// Reject group
$(document).on('click', '.rejectGroupBtn', function () {
    const userName = $(this).data('user');
    const workDate = $(this).data('date');
    openRejectionModal({userName, workDate, isGroup: true});
});

// Submit approval
$(document).on('submit', '#approvalForm', function(e) {
    e.preventDefault();
    const isGroup = $(this).find('input[name="is_group"]').val() === '1';
    const payload = $(this).serialize();
    if (isGroup) {
        $.post("{{ route('worklog.approve-group') }}", payload, function(response) {
            if (response.success) { loadPendingApprovals(); showAlert('success', response.message); $('#approvalModal').modal('hide'); }
        }).fail(function(xhr){ showAlert('error', xhr.responseJSON?.message || 'Approval failed'); });
    } else {
        const id = $(this).find('input[name="worklog_id"]').val();
        $.post(`/worklog/${id}/approve`, payload, function(response) {
            if (response.success) { loadPendingApprovals(); showAlert('success', response.message); $('#approvalModal').modal('hide'); }
        }).fail(function(xhr){ showAlert('error', xhr.responseJSON?.message || 'Approval failed'); });
    }
});

// Submit rejection
$(document).on('submit', '#rejectionForm', function(e) {
    e.preventDefault();
    const isGroup = $(this).find('input[name="is_group"]').val() === '1';
    const payload = $(this).serialize();
    if (isGroup) {
        $.post("{{ route('worklog.reject-group') }}", payload, function(response) {
            if (response.success) { loadPendingApprovals(); showAlert('success', response.message); $('#rejectionModal').modal('hide'); }
        }).fail(function(xhr){ showAlert('error', xhr.responseJSON?.message || 'Rejection failed'); });
    } else {
        const id = $(this).find('input[name="worklog_id"]').val();
        $.post(`/worklog/${id}/reject`, payload, function(response) {
            if (response.success) { loadPendingApprovals(); showAlert('success', response.message); $('#rejectionModal').modal('hide'); }
        }).fail(function(xhr){ showAlert('error', xhr.responseJSON?.message || 'Rejection failed'); });
    }
});
</script>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="approvalForm" class="modal-content">
      <div class="modal-header text-white" style="background-color: #434afa;">
        <h5 class="modal-title">Approve Worklog</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="worklog_id">
        <input type="hidden" name="user_name">
        <input type="hidden" name="work_date">
        <input type="hidden" name="is_group" value="0">
        <div class="mb-3">
          <label class="form-label">Rating *</label>
          <select class="form-select" name="rating" required>
            <option value="">Select</option>
            <option value="below">Below Expectation</option>
            <option value="met">Met Expectation</option>
            <option value="exceeded">Exceeded Expectation</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Remark *</label>
          <textarea class="form-control" name="remark" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="submit" class="btn text-white" style="background-color: #434afa; border:none; border-radius: 0; padding: 0.5rem 1.5rem;">Approve</button>
      </div>
    </form>
  </div>
  </div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="rejectionForm" class="modal-content">
      <div class="modal-header text-white" style="background-color: #434afa;">
        <h5 class="modal-title">Reject Worklog</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="worklog_id">
        <input type="hidden" name="user_name">
        <input type="hidden" name="work_date">
        <input type="hidden" name="is_group" value="0">
        <div class="mb-3">
          <label class="form-label">Reason *</label>
          <textarea class="form-control" name="remark" rows="3" required></textarea>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="submit" class="btn text-white" style="background-color: #434afa; border:none; border-radius: 0; padding: 0.5rem 1.5rem;">Reject</button>
      </div>
    </form>
  </div>
</div>
@endpush
