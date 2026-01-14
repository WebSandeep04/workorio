@extends('layouts.app')

@section('title', 'Designations')
@section('page_title', 'Designations')

@push('styles')
<style>
  .page-header {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(240, 147, 251, 0.3);
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
        <i class="bi bi-badge-ad"></i>
        Designations Management
      </h2>
      <button class="btn-modern btn-modern-warning" id="openDesignationModal">
        <i class="bi bi-plus-circle"></i>
        Add New Designation
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
              <th>Code</th>
              <th>Title</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="designationTableBody">
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading designations...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="designationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="designationModalLabel">
          <i class="bi bi-badge-ad"></i>
          Add Designation
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="designationForm">
          <input type="hidden" id="designation_id">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-modern">Code</label>
              <input type="text" id="designation_code" class="form-control form-control-modern" placeholder="Auto-generated" readonly>
              <small class="text-muted">Auto-generated after save.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label-modern">Title <span class="text-danger">*</span></label>
              <input type="text" id="designation_title" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-4">
              <label class="form-label-modern">Status</label>
              <select id="designation_status" class="form-select form-select-modern">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="designation_notes" rows="3" class="form-control form-control-modern"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="designationError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-warning" id="saveDesignation">
          <i class="bi bi-check-circle"></i>
          Save Designation
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
  const listUrl = "{{ route('designations.list') }}";
  const storeUrl = "{{ route('designations.store') }}";

  function escapeHtml(text = '') {
    return (text || '').toString()
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function loadDesignations() {
    $('#designationTableBody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading designations...</p>
        </td>
      </tr>
    `);
    
    $.get(listUrl).done(function(rows){
      if (!rows || rows.length === 0) {
        $('#designationTableBody').html(`
          <tr>
            <td colspan="4" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Designations Found</h5>
              <p>Get started by creating your first designation.</p>
            </td>
          </tr>
        `);
        return;
      }
      
      let html = '';
      rows.forEach(function(row, index){
        const statusClass = row.status === 'active' ? 'badge-modern-success' : 'badge-modern-secondary';
        html += `
          <tr style="animation-delay: ${index * 0.1}s;">
            <td><strong class="text-gradient-primary">${escapeHtml(row.code || '')}</strong></td>
            <td><strong>${escapeHtml(row.title)}</strong></td>
            <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-designation" data-designation='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-designation" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#designationTableBody').html(html);
    }).fail(function(){
      $('#designationTableBody').html(`
        <tr>
          <td colspan="4" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load designations. Please try again.
          </td>
        </tr>
      `);
    });
  }

  function openModal(data) {
    $('#designationForm')[0].reset();
    $('#designation_id').val(data && data.id ? data.id : '');
    $('#designationModalLabel').html(data ? 
      '<i class="bi bi-pencil-square"></i> Edit Designation' : 
      '<i class="bi bi-plus-circle"></i> Add Designation'
    );
    $('#designationError').addClass('d-none').text('');
    
    if (data) {
      $('#designation_code').val(data.code || '');
      $('#designation_title').val(data.title || '');
      $('#designation_status').val(data.status || 'active');
      $('#designation_notes').val(data.notes || '');
    } else {
      $('#designation_status').val('active');
    }
    
    new bootstrap.Modal('#designationModal').show();
  }

  function saveDesignation() {
    const id = $('#designation_id').val();
    const payload = {
      _token: csrf,
      code: $('#designation_code').val().trim(),
      title: $('#designation_title').val().trim(),
      status: $('#designation_status').val(),
      notes: $('#designation_notes').val().trim(),
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `{{ url('/designations') }}/${id}` : storeUrl;
    
    const $btn = $('#saveDesignation');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('designationModal')).hide();
        loadDesignations();
        showNotification('success', id ? 'Designation updated successfully!' : 'Designation created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#designationError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Designation');
      });
  }

  function deleteDesignation(id) {
    if (!confirm('Are you sure you want to delete this designation?')) return;
    
    $.ajax({
      url: `{{ url('/designations') }}/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadDesignations();
      showNotification('success', 'Designation deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete designation. Please try again.');
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
  $('#openDesignationModal').on('click', function(){ openModal(null); });
  $('#saveDesignation').on('click', saveDesignation);
  $(document).on('click', '.edit-designation', function(){ 
    openModal($(this).data('designation')); 
  });
  $(document).on('click', '.delete-designation', function(){ 
    deleteDesignation($(this).data('id')); 
  });
  
  $(document).ready(loadDesignations);
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
