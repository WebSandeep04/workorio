@extends('layouts.app')

@section('title', 'Employment Types')
@section('page_title', 'Employment Types')

@push('styles')
<style>
  .page-header {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    padding: 2rem;
    border-radius: 20px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 40px rgba(255, 236, 210, 0.3);
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
        <i class="bi bi-briefcase"></i>
        Employment Types Management
      </h2>
      <button class="btn-modern btn-modern-warning" id="openEmploymentTypeModal">
        <i class="bi bi-plus-circle"></i>
        Add Employment Type
      </button>
    </div>
  </div>

  <div class="modern-card">
    <div class="modern-card-body">
      <div class="modern-table-wrapper">
        <table class="modern-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="employmentTypeTableBody">
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading employment types...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-modern" id="employmentTypeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employmentTypeModalLabel">
          <i class="bi bi-briefcase"></i>
          Add Employment Type
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="employmentTypeForm">
          <input type="hidden" id="employment_type_id">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-modern">Code</label>
              <input type="text" id="employment_type_code" class="form-control form-control-modern" placeholder="Auto-generated" readonly>
              <small class="text-muted">Auto-generated after save.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="employment_type_name" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-4">
              <label class="form-label-modern">Status</label>
              <select id="employment_type_status" class="form-select form-select-modern">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="employment_type_notes" rows="3" class="form-control form-control-modern"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="employmentTypeError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-warning" id="saveEmploymentType">
          <i class="bi bi-check-circle"></i>
          Save Employment Type
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
  const listUrl = "{{ route('employment-types.list') }}";
  const storeUrl = "{{ route('employment-types.store') }}";

  function escapeHtml(text = '') {
    return (text || '').toString()
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function loadEmploymentTypes() {
    $('#employmentTypeTableBody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading employment types...</p>
        </td>
      </tr>
    `);
    
    $.get(listUrl).done(function(rows){
      if (!rows || rows.length === 0) {
        $('#employmentTypeTableBody').html(`
          <tr>
            <td colspan="4" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Employment Types Found</h5>
              <p>Get started by creating your first employment type.</p>
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
            <td><strong>${escapeHtml(row.name)}</strong></td>
            <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-employment-type" data-employment-type='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-employment-type" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#employmentTypeTableBody').html(html);
    }).fail(function(){
      $('#employmentTypeTableBody').html(`
        <tr>
          <td colspan="4" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load employment types. Please try again.
          </td>
        </tr>
      `);
    });
  }

  function openModal(data) {
    $('#employmentTypeForm')[0].reset();
    $('#employment_type_id').val(data && data.id ? data.id : '');
    $('#employmentTypeModalLabel').html(data ? 
      '<i class="bi bi-pencil-square"></i> Edit Employment Type' : 
      '<i class="bi bi-plus-circle"></i> Add Employment Type'
    );
    $('#employmentTypeError').addClass('d-none').text('');
    
    if (data) {
      $('#employment_type_code').val(data.code || '');
      $('#employment_type_name').val(data.name || '');
      $('#employment_type_status').val(data.status || 'active');
      $('#employment_type_notes').val(data.notes || '');
    } else {
      $('#employment_type_status').val('active');
    }
    
    new bootstrap.Modal('#employmentTypeModal').show();
  }

  function saveEmploymentType() {
    const id = $('#employment_type_id').val();
    const payload = {
      _token: csrf,
      code: $('#employment_type_code').val().trim(),
      name: $('#employment_type_name').val().trim(),
      status: $('#employment_type_status').val(),
      notes: $('#employment_type_notes').val().trim(),
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `{{ url('/employment-types') }}/${id}` : storeUrl;
    
    const $btn = $('#saveEmploymentType');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('employmentTypeModal')).hide();
        loadEmploymentTypes();
        showNotification('success', id ? 'Employment type updated successfully!' : 'Employment type created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#employmentTypeError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Employment Type');
      });
  }

  function deleteEmploymentType(id) {
    if (!confirm('Are you sure you want to delete this employment type?')) return;
    
    $.ajax({
      url: `{{ url('/employment-types') }}/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadEmploymentTypes();
      showNotification('success', 'Employment type deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete employment type. Please try again.');
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

  $('#openEmploymentTypeModal').on('click', function(){ openModal(null); });
  $('#saveEmploymentType').on('click', saveEmploymentType);
  $(document).on('click', '.edit-employment-type', function(){ 
    openModal($(this).data('employment-type')); 
  });
  $(document).on('click', '.delete-employment-type', function(){ 
    deleteEmploymentType($(this).data('id')); 
  });
  
  $(document).ready(loadEmploymentTypes);
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
