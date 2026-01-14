@extends('layouts.app')

@section('title', 'Shifts')
@section('page_title', 'Shifts')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
<style>
  .page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
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
  <!-- Page Header -->
  <div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <h2>
        <i class="bi bi-clock-history"></i>
        Shift Management
      </h2>
      <button class="btn-modern btn-modern-primary" id="openShiftModal">
        <i class="bi bi-plus-circle"></i>
        Add New Shift
      </button>
    </div>
  </div>

  <!-- Main Card -->
  <div class="modern-card">
    <div class="modern-card-body">
      <div class="modern-table-wrapper">
        <table class="modern-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Start Time</th>
              <th>End Time</th>
              <th>Late (min)</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="shiftTableBody">
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading shifts...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="shiftModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shiftModalLabel">
          <i class="bi bi-clock-history"></i>
          Add Shift
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="shiftForm">
          <input type="hidden" id="shift_id">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label-modern">Shift Name <span class="text-danger">*</span></label>
              <input type="text" id="shift_name" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Start Time <span class="text-danger">*</span></label>
              <input type="time" id="shift_start_time" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">End Time <span class="text-danger">*</span></label>
              <input type="time" id="shift_end_time" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Late (minutes allowed)</label>
              <input type="number" min="0" id="shift_late_min" class="form-control form-control-modern" placeholder="e.g. 15">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Status</label>
              <select id="shift_is_active" class="form-select form-select-modern">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="shiftError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-primary" id="saveShift">
          <i class="bi bi-check-circle"></i>
          Save Shift
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const csrf = $('meta[name="csrf-token"]').attr('content');
  const listUrl = "{{ route('shifts.list') }}";
  const storeUrl = "{{ route('shifts.store') }}";

  function escapeHtml(text = '') {
    return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  function formatTime(value) {
    if (!value) return '-';
    return value.substring(0, 5); // HH:MM
  }

  function loadShifts() {
    $('#shiftTableBody').html(`
      <tr>
        <td colspan="6" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading shifts...</p>
        </td>
      </tr>
    `);

    $.get(listUrl).done(function(rows){
      if (!rows || rows.length === 0) {
        $('#shiftTableBody').html(`
          <tr>
            <td colspan="6" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Shifts Found</h5>
              <p>Get started by creating your first shift.</p>
            </td>
          </tr>
        `);
        return;
      }

      let html = '';
      rows.forEach(function(row, index){
        const statusClass = row.is_active ? 'badge-modern-success' : 'badge-modern-secondary';
        const statusText = row.is_active ? 'Active' : 'Inactive';
        html += `
          <tr style="animation-delay: ${index * 0.1}s;">
            <td><strong>${escapeHtml(row.name)}</strong></td>
            <td>${escapeHtml(formatTime(row.start_time))}</td>
            <td>${escapeHtml(formatTime(row.end_time))}</td>
            <td>${row.late_min != null ? escapeHtml(row.late_min) : '-'}</td>
            <td><span class="badge ${statusClass}">${statusText}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-shift" data-shift='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-shift" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#shiftTableBody').html(html);
    }).fail(function(){
      $('#shiftTableBody').html(`
        <tr>
          <td colspan="6" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load shifts. Please try again.
          </td>
        </tr>
      `);
    });
  }

  function openModal(data){
    $('#shiftForm')[0].reset();
    $('#shift_id').val(data && data.id ? data.id : '');
    $('#shiftModalLabel').html(data ?
      '<i class="bi bi-pencil-square"></i> Edit Shift' :
      '<i class="bi bi-plus-circle"></i> Add Shift'
    );
    $('#shiftError').addClass('d-none').text('');

    if (data) {
      $('#shift_name').val(data.name || '');
      $('#shift_start_time').val(formatTime(data.start_time));
      $('#shift_end_time').val(formatTime(data.end_time));
      $('#shift_late_min').val(data.late_min != null ? data.late_min : '');
      $('#shift_is_active').val(data.is_active ? '1' : '0');
    } else {
      $('#shift_late_min').val('');
      $('#shift_is_active').val('1');
    }

    new bootstrap.Modal(document.getElementById('shiftModal')).show();
  }

  function saveShift(){
    const id = $('#shift_id').val();
    const payload = {
      _token: csrf,
      name: $('#shift_name').val().trim(),
      start_time: $('#shift_start_time').val(),
      end_time: $('#shift_end_time').val(),
      late_min: $('#shift_late_min').val(),
      is_active: $('#shift_is_active').val(),
    };

    const method = id ? 'PUT' : 'POST';
    const url = id ? `{{ url('/shifts') }}/${id}` : storeUrl;

    const $btn = $('#saveShift');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('shiftModal')).hide();
        loadShifts();
        showNotification('success', id ? 'Shift updated successfully!' : 'Shift created successfully!');
      })
      .fail(function(xhr){
        let msg = 'Unable to save.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
        }
        $('#shiftError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Shift');
      });
  }

  function deleteShift(id){
    if (!confirm('Are you sure you want to delete this shift?')) return;

    $.ajax({
      url: `{{ url('/shifts') }}/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadShifts();
      showNotification('success', 'Shift deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete shift. Please try again.');
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

  // Event bindings
  $('#openShiftModal').on('click', function(){ openModal(null); });
  $('#saveShift').on('click', saveShift);
  $(document).on('click', '.edit-shift', function(){
    const data = $(this).data('shift');
    openModal(data);
  });
  $(document).on('click', '.delete-shift', function(){
    deleteShift($(this).data('id'));
  });

  $(document).ready(loadShifts);
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


