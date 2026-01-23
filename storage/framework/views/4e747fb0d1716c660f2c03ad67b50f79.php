

<?php $__env->startSection('title', 'Worklog Approvals'); ?>
<?php $__env->startSection('page_title', 'Worklog Approvals'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    margin-bottom: 1rem;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .card-header-modern {
    background-color: #434afa !important;
    color: white;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .group-title {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
  }

  .badge-stat {
    background: white;
    color: #434afa;
    padding: 0.4rem 0.7rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  @media (max-width: 768px) {
    .card-header-modern {
      flex-direction: column;
      align-items: flex-start;
    }
    
    .table-responsive {
      border: 0;
    }

    .group-actions {
      flex-direction: column;
      width: 100%;
      gap: 0.5rem;
    }

    .group-actions button {
      width: 100%;
      margin: 0 !important;
    }
  }

  .modal-backdrop.show {
    background-color: rgba(15, 23, 42, 0.55);
  }

  .modal-content {
    border: none;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  }

  .modal-header {
    border-bottom: 1px solid #f1f3f5;
  }

  .modal-footer {
    border-top: 1px solid #f1f3f5;
  }

  /* Custom Alert Styles */
  .custom-alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 5px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .custom-alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
  .custom-alert-error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
  .custom-alert-close { background: transparent; border: none; font-size: 1.25rem; font-weight: bold; cursor: pointer; color: inherit; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <div id="groupedApprovals">
                <!-- Grouped approvals will be loaded via jQuery -->
            </div>
            
            <!-- No Data Message -->
            <div id="noDataMessage" class="text-center py-5" style="display: none;">
                <div class="mb-3">
                    <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted">No pending approvals</h5>
                <p class="text-muted small">All worklog entries have been reviewed.</p>
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

$(function () {
    loadPendingApprovals();
});

function loadPendingApprovals() {
    $.get("<?php echo e(route('worklog.pending-approvals')); ?>", function (data) {
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
                <div class="data-table-card mb-4 shadow-sm">
                    <div class="card-header-modern">
                        <h6 class="group-title">
                            <i class="bi bi-person-circle me-2"></i>
                            <span class="fw-bold">${group.user_name}</span> 
                            <span class="mx-1 text-white-50">|</span> 
                            <span class="small">${group.work_date.split('T')[0]}</span>
                        </h6>
                        <div class="d-flex gap-2">
                            <span class="badge-stat">${group.entries.length} Entries</span>
                            <span class="badge-stat">Total: ${timeDisplay}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Entry Type</th>
                                    <th>Customer</th>
                                    <th>Project</th>
                                    <th>Module</th>
                                    <th>Time</th>
                                    <th>Description</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                $.each(group.entries, function (j, worklog) {
                    const entryTime = `${worklog.hours}h ${worklog.minutes}m`;
                    
                    html += `<tr>
                        <td><strong>${worklog.entry_type ? worklog.entry_type.name : 'N/A'}</strong></td>
                        <td>${worklog.customer ? worklog.customer.name : 'N/A'}</td>
                        <td>${worklog.project ? worklog.project.name : 'N/A'}</td>
                        <td>${worklog.module ? worklog.module.name : 'N/A'}</td>
                        <td><span class="badge bg-light text-dark">${entryTime}</span></td>
                        <td>
                            <div class="text-wrap" style="max-width: 250px; font-size: 0.8rem; line-height: 1.4; white-space: normal;">
                                ${worklog.description}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm text-white approveBtn" data-id="${worklog.id}" title="Approve" style="background-color: #434afa; border:none; border-radius: 4px; padding: 0.35rem 0.6rem;">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-danger rejectBtn" data-id="${worklog.id}" title="Reject" style="border:none; border-radius: 4px; padding: 0.35rem 0.6rem;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-light p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center group-actions">
                            <span class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Bulk Actions for this Date:</span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm text-white approveGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Approve All" style="background-color: #434afa; border:none; border-radius: 4px; padding: 0.5rem 1.25rem;">
                                    <i class="bi bi-check-all me-1"></i> Approve All
                                </button>
                                <button class="btn btn-sm btn-danger rejectGroupBtn" data-user="${group.user_name}" data-date="${group.work_date}" title="Reject All" style="border:none; border-radius: 4px; padding: 0.5rem 1.25rem;">
                                    <i class="bi bi-x-circle me-1"></i> Reject All
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
        $.post("<?php echo e(route('worklog.approve-group')); ?>", payload, function(response) {
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
        $.post("<?php echo e(route('worklog.reject-group')); ?>", payload, function(response) {
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
      <div class="modal-header text-white" style="background-color: #434afa; border-radius: 8px 8px 0 0;">
        <h5 class="modal-title">Approve Worklog</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
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
        <button type="submit" class="btn text-white" style="background-color: #434afa; border:none; border-radius: 4px; padding: 0.5rem 1.5rem; box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);">Approve</button>
      </div>
    </form>
  </div>
  </div>

<!-- Rejection Modal -->
<div class="modal fade" id="rejectionModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="rejectionForm" class="modal-content">
      <div class="modal-header text-white" style="background-color: #434afa; border-radius: 8px 8px 0 0;">
        <h5 class="modal-title">Reject Worklog</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        <input type="hidden" name="worklog_id">
        <input type="hidden" name="user_name">
        <input type="hidden" name="work_date">
        <input type="hidden" name="is_group" value="0">
        <div class="mb-3">
          <label class="form-label fw-bold small text-uppercase">Reason for Rejection *</label>
          <textarea class="form-control" name="remark" rows="3" required placeholder="Describe why this worklog is being rejected..."></textarea>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="submit" class="btn btn-danger" style="border:none; border-radius: 4px; padding: 0.5rem 1.5rem; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">Reject</button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/worklog/approvals.blade.php ENDPATH**/ ?>