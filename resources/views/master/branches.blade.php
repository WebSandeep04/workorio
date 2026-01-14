@extends('layouts.app')

@section('title', 'Branches')
@section('page_title', 'Branches')

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
        <i class="bi bi-diagram-3"></i>
        Branches Management
      </h2>
      <button class="btn-modern btn-modern-primary" id="openBranchModal">
        <i class="bi bi-plus-circle"></i>
        Add New Branch
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
              <th>Name</th>
              <th>Location</th>
              <th>Contact</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="branchTableBody">
            <tr>
              <td colspan="6" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading branches...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modern Modal -->
<div class="modal fade modal-modern" id="branchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="branchModalLabel">
          <i class="bi bi-diagram-3"></i>
          Add Branch
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="branchForm">
          <input type="hidden" id="branch_id">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-modern">Code</label>
              <input type="text" id="branch_code" class="form-control form-control-modern" placeholder="Auto-generated" readonly>
              <small class="text-muted">Auto-generated after save.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label-modern">Name <span class="text-danger">*</span></label>
              <input type="text" id="branch_name" class="form-control form-control-modern" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Location</label>
              <input type="text" id="branch_location" class="form-control form-control-modern">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Status</label>
              <select id="branch_status" class="form-select form-select-modern">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Contact Person</label>
              <input type="text" id="branch_contact_person" class="form-control form-control-modern">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern">Contact Phone</label>
              <input type="text" id="branch_contact_phone" class="form-control form-control-modern">
            </div>
            <div class="col-12">
              <label class="form-label-modern">Notes</label>
              <textarea id="branch_notes" rows="3" class="form-control form-control-modern"></textarea>
            </div>
          </div>
        </form>
        <div class="alert alert-danger d-none mt-3" id="branchError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-primary" id="saveBranch">
          <i class="bi bi-check-circle"></i>
          Save Branch
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
  const listUrl = "{{ route('branches.list') }}";
  const storeUrl = "{{ route('branches.store') }}";

  function escapeHtml(text = '') {
    return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
  }

  function loadBranches() {
    $('#branchTableBody').html(`
      <tr>
        <td colspan="6" class="loading-state">
          <i class="bi bi-arrow-repeat"></i>
          <p class="mt-2 mb-0">Loading branches...</p>
        </td>
      </tr>
    `);
    
    $.get(listUrl).done(function(rows){
      if (!rows || rows.length === 0) {
        $('#branchTableBody').html(`
          <tr>
            <td colspan="6" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Branches Found</h5>
              <p>Get started by creating your first branch.</p>
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
            <td><strong class="text-gradient-primary">${escapeHtml(row.code)}</strong></td>
            <td><strong>${escapeHtml(row.name)}</strong></td>
            <td>${escapeHtml(row.location || '-')}</td>
            <td>
              ${escapeHtml(row.contact_person || '-')}
              ${row.contact_phone ? `<br><small class="text-muted">${escapeHtml(row.contact_phone)}</small>` : ''}
            </td>
            <td><span class="badge ${statusClass}">${escapeHtml(row.status || '')}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-branch" data-branch='${JSON.stringify(row).replace(/'/g, "&#39;")}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete delete-branch" data-id="${row.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#branchTableBody').html(html);
    }).fail(function(){
      $('#branchTableBody').html(`
        <tr>
          <td colspan="6" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            Failed to load branches. Please try again.
          </td>
        </tr>
      `);
    });
  }

  function openModal(data){
    $('#branchForm')[0].reset();
    $('#branch_id').val(data && data.id ? data.id : '');
    $('#branchModalLabel').html(data ? 
      '<i class="bi bi-pencil-square"></i> Edit Branch' : 
      '<i class="bi bi-plus-circle"></i> Add Branch'
    );
    $('#branchError').addClass('d-none').text('');
    
    if (data) {
      $('#branch_code').val(data.code || '');
      $('#branch_name').val(data.name || '');
      $('#branch_location').val(data.location || '');
      $('#branch_contact_person').val(data.contact_person || '');
      $('#branch_contact_phone').val(data.contact_phone || '');
      $('#branch_status').val(data.status || 'active');
      $('#branch_notes').val(data.notes || '');
    } else {
      $('#branch_status').val('active');
    }
    
    new bootstrap.Modal(document.getElementById('branchModal')).show();
  }

  function saveBranch(){
    const id = $('#branch_id').val();
    const payload = {
      _token: csrf,
      code: $('#branch_code').val().trim(),
      name: $('#branch_name').val().trim(),
      location: $('#branch_location').val().trim(),
      contact_person: $('#branch_contact_person').val().trim(),
      contact_phone: $('#branch_contact_phone').val().trim(),
      status: $('#branch_status').val(),
      notes: $('#branch_notes').val().trim(),
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `{{ url('/branches') }}/${id}` : storeUrl;
    
    const $btn = $('#saveBranch');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    $.ajax({ url, method, data: payload })
      .done(function(){
        bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
        loadBranches();
        showNotification('success', id ? 'Branch updated successfully!' : 'Branch created successfully!');
      })
      .fail(function(xhr){
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
        $('#branchError').removeClass('d-none').text(msg);
      })
      .always(function(){
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Branch');
      });
  }

  function deleteBranch(id){
    if (!confirm('Are you sure you want to delete this branch?')) return;
    
    $.ajax({
      url: `{{ url('/branches') }}/${id}`,
      method: 'DELETE',
      data: { _token: csrf },
    })
    .done(function(){
      loadBranches();
      showNotification('success', 'Branch deleted successfully!');
    })
    .fail(function(){
      showNotification('error', 'Failed to delete branch. Please try again.');
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
  $('#openBranchModal').on('click', function(){ openModal(null); });
  $('#saveBranch').on('click', saveBranch);
  $(document).on('click', '.edit-branch', function(){ 
    const data = $(this).data('branch');
    openModal(data); 
  });
  $(document).on('click', '.delete-branch', function(){ 
    deleteBranch($(this).data('id')); 
  });
  
  $(document).ready(loadBranches);
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
