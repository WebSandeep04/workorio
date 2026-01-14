@extends('layouts.app')

@section('title', 'Late Reasons')
@section('page_title', 'Late Reasons')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
<style>
  .page-header {
    background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    padding: 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(247, 151, 30, 0.3);
    animation: fadeInDown 0.6s ease-out;
  }
  .page-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
  <div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <h2>
        <i class="bi bi-clock-history"></i>
        Late Reasons Management
      </h2>
      <button class="btn-modern btn-modern-primary" id="openLateReasonModal">
        <i class="bi bi-plus-circle"></i>
        Add Late Reason
      </button>
    </div>
  </div>

  <div class="modern-card">
    <div class="modern-card-body">
      <div class="modern-table-wrapper">
        <table class="modern-table">
          <thead>
            <tr>
              <th>Reason</th>
              <th>Active</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="lateReasonTableBody">
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading late reasons...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-modern" id="lateReasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lateReasonModalLabel">
          <i class="bi bi-plus-circle"></i>
          Add Late Reason
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="lateReasonForm">
          <input type="hidden" id="late_reason_id">
          <div class="mb-3">
            <label class="form-label-modern">Reason <span class="text-danger">*</span></label>
            <input type="text" id="late_reason_text" class="form-control form-control-modern" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="late_reason_active" checked>
              <label class="form-check-label" for="late_reason_active">
                Active
              </label>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none" id="lateReasonError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-primary" id="saveLateReason">
          <i class="bi bi-check-circle"></i>
          Save
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const listUrl = "{{ route('late-reasons.list') }}";
  const storeUrl = "{{ route('late-reasons.store') }}";

  function escapeHtml(text = '') {
    return (text || '').toString()
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function loadLateReasons() {
    $('#lateReasonTableBody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading late reasons...</p>
        </td>
      </tr>
    `);

    $.get(listUrl)
      .done(function(rows){
        if (!rows || rows.length === 0) {
          $('#lateReasonTableBody').html(`
            <tr>
              <td colspan="4" class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Late Reasons Found</h5>
                <p>Get started by creating your first late reason.</p>
              </td>
            </tr>
          `);
          return;
        }

        let html = '';
        rows.forEach(function(row, index){
          const activeBadge = row.active
            ? '<span class="badge-modern badge-modern-success">Active</span>'
            : '<span class="badge-modern badge-modern-secondary">Inactive</span>';

          html += `
            <tr style="animation-delay: ${index * 0.1}s;">
              <td><strong>${escapeHtml(row.reason)}</strong></td>
              <td>${activeBadge}</td>
              <td>${row.created_at ? escapeHtml(row.created_at) : '-'}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn-action btn-action-edit edit-late-reason" data-reason='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-action-delete delete-late-reason" data-id="${row.id}" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        });
        $('#lateReasonTableBody').html(html);
      })
      .fail(function(){
        $('#lateReasonTableBody').html(`
          <tr>
            <td colspan="4" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i>
              Failed to load late reasons. Please try again.
            </td>
          </tr>
        `);
      });
  }

  function openModal(data) {
    $('#lateReasonForm')[0].reset();
    $('#late_reason_id').val(data && data.id ? data.id : '');
    $('#lateReasonModalLabel').html(data
      ? '<i class="bi bi-pencil-square"></i> Edit Late Reason'
      : '<i class="bi bi-plus-circle"></i> Add Late Reason'
    );
    $('#lateReasonError').addClass('d-none').text('');

    if (data) {
      $('#late_reason_text').val(data.reason || '');
      $('#late_reason_active').prop('checked', !!data.active);
    } else {
      $('#late_reason_active').prop('checked', true);
    }

    new bootstrap.Modal(document.getElementById('lateReasonModal')).show();
  }

  function saveLateReason() {
    const id = $('#late_reason_id').val();
    const payload = {
      _token: csrf,
      reason: ($('#late_reason_text').val() || '').trim(),
      active: $('#late_reason_active').is(':checked') ? 1 : 0,
    };

    if (!payload.reason) {
      $('#lateReasonError').removeClass('d-none').text('Reason is required');
      return;
    }

    const method = id ? 'PUT' : 'POST';
    const url = id ? `{{ url('/late-reasons') }}/${id}` : storeUrl;

    const $btn = $('#saveLateReason');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('lateReasonModal')).hide();
        loadLateReasons();
        showNotification('success', id ? 'Late reason updated successfully!' : 'Late reason created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#lateReasonError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save');
      });
  }

  function deleteLateReason(id) {
    if (!confirm('Are you sure you want to delete this late reason?')) return;

    $.ajax({
      url: `{{ url('/late-reasons') }}/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadLateReasons();
      showNotification('success', 'Late reason deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete late reason. Please try again.');
    });
  }

  function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
      <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    $('body').append(alertHtml);
    setTimeout(() => $('.alert').fadeOut(), 3000);
  }

  $('#openLateReasonModal').on('click', function(){ openModal(null); });
  $('#saveLateReason').on('click', saveLateReason);

  $(document).on('click', '.edit-late-reason', function(){
    const data = $(this).data('reason');
    openModal(data);
  });

  $(document).on('click', '.delete-late-reason', function(){
    deleteLateReason($(this).data('id'));
  });

  $(document).ready(loadLateReasons);
})();
</script>
<style>
  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush
