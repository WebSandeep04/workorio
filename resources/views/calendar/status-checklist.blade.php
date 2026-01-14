@extends('layouts.app')

@section('title', 'Status-Checklist Links')
@section('page_title', 'Status-Checklist Links')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  
  /* Table Search & Buttons */
  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }
  
  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }
  
  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }
  
  /* Modern Card & Table */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }
  
  .modern-card-body {
    padding: 0.5rem;
  }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }
  
  .data-table-card .modern-card-body {
    padding: 0;
  }
  
  .data-table-card .table-responsive {
    border-radius: 5px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }
  
  .data-table-card .table-responsive {
    scrollbar-color: #434AFA #e4e7ec;
  }
  
  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
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
  
  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }
  
  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }
  
  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }
  
  /* Range Info */
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
  
  /* Buttons in Table */
  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem 0.5rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  .btn-action-edit {
    color: white;
    background: #343AFA !important;
    border-radius: 4px;
  }
  
  /* Pagination */
  .pagination .page-link {
    color: #667eea;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }
  
  .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
  }
  
  .pagination .page-link:hover {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    transform: translateY(-1px);
  }

  .loading-state, .empty-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
    font-size: 10px;
  }
  
  .empty-state {
    color: #6c757d;
  }
  
  .loading-state i, .empty-state i {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
  }

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 1rem 1.5rem;
  }
  
  .modal-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: white;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 1rem 1.5rem;
      background: #fff;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
  }
  
  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .btn-modern-danger {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-danger:hover {
    background: #3538d4;
    color: white;
  }

  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .badge-modern-info {
    font-weight: 500;
    padding: 4px 8px;
    font-size: 0.75rem;
    background-color: rgba(67, 74, 250, 0.1);
    color: #434AFA;
    border-radius: 4px;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search statuses..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="statusChecklistTable">
          <thead>
            <tr>
              <th>Status</th>
              <th>Linked Checklists</th>
              <th style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody id="statusChecklistTableBody">
            <tr>
              <td colspan="3" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading data...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="statusRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Manage Checklists Modal -->
<div class="modal fade modal-modern" id="statusChecklistModal" tabindex="-1" aria-labelledby="statusChecklistModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusChecklistModalLabel">
          <i class="bi bi-list-check text-white"></i>
          Manage Checklists for: <span id="modal_status_name_display" class="fw-normal text-white-50"></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <input type="hidden" id="modal_status_id" />
        <div class="mb-3">
          <label class="form-label-modern fw-bold mb-3">Select Checklists:</label>
          <div id="modal_checklists" class="row g-3"></div>
        </div>
        <div class="alert alert-danger d-none mt-2" id="statusChecklistError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn-modern btn-modern-danger" id="saveStatusChecklist">
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
function showAlert(type, message) {
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

function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#statusRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    const formattedStart = safeStart.toLocaleString('en-IN');
    const formattedEnd = safeEnd.toLocaleString('en-IN');
    const formattedTotal = safeTotal.toLocaleString('en-IN');

    $info.text(`Showing ${formattedStart}-${formattedEnd} from ${formattedTotal} data`);
}

(function(){
  let checklists = [];
  let relationships = {};
  let searchTimeout;

  function loadData(page = 1){
    let search = $('#search').val();

    $('#statusChecklistTableBody').html(`
      <tr>
        <td colspan="3" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading data...</p>
        </td>
      </tr>
    `);
    
    $.get(`{{ route('calendar-status-checklist.fetch') }}?page=${page}&search=${search}`).then(function(data){
      // Global store for the modal
      checklists = data.checklists || [];
      relationships = data.relationships || {};
      
      const statuses = data.statuses.data || [];
      
      if (statuses.length === 0) {
        $('#statusChecklistTableBody').html(`
          <tr>
            <td colspan="3" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Statuses Found</h5>
              <p>No statuses available matching your search.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }

      const rows = statuses.map((st, index) => {
        const linked = (relationships[st.id] || []).map(function(cid){
            const c = checklists.find(x => x.id == cid);
            return c ? c.name : null;
        }).filter(Boolean).join(', ');

        return `
          <tr style="animation-delay: ${index * 0.1}s;">
            <td>${escapeHtml(st.name)}</td>
            <td>${linked ? `<span class="badge badge-modern-info" style="white-space: normal;">${linked}</span>` : '<span class="text-muted small">None</span>'}</td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit edit-status-checklist" data-id="${st.id}" data-name="${escapeHtml(st.name)}" title="Manage Checklists">
                  <i class="bi bi-pencil"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');

      $('#statusChecklistTableBody').html(rows);

      buildSimplePagination($('#paginationLinks'), data.statuses.current_page || 1, data.statuses.last_page || 1);
      updateRangeInfo(data.statuses.from, data.statuses.to, data.statuses.total);

    }).fail(function(){
      $('#statusChecklistTableBody').html(`
        <tr>
            <td colspan="3" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i>
              Failed to load data. Please try again.
            </td>
        </tr>
      `);
    });
  }

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadData(1);
      }, 300);
  });

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadData(page);
    }
  });

  function openModal(statusId, statusName) {
    $('#modal_status_id').val(statusId);
    $('#modal_status_name_display').text(statusName);

    let checkHtml = '';
    if(checklists.length === 0){
      checkHtml = '<div class="col-12 text-muted text-center py-3">No checklists available in the system.</div>';
    } else {
      checklists.forEach(function(c){
        const isChecked = (relationships[statusId] || []).includes(c.id);
        checkHtml += `<div class="col-md-6 col-lg-6">
          <div class="form-check p-2" style="border: 1px solid #eee; border-radius: 4px; transition: background 0.2s;">
            <input class="form-check-input checklist-checkbox me-2" type="checkbox" value="${c.id}" id="c_${c.id}" ${isChecked ? 'checked' : ''} style="cursor: pointer;">
            <label class="form-check-label w-100" for="c_${c.id}" style="cursor: pointer;">${escapeHtml(c.name)}</label>
          </div>
        </div>`;
      });
    }
    $('#modal_checklists').html(checkHtml);
    $('#statusChecklistError').addClass('d-none').text('');
    new bootstrap.Modal(document.getElementById('statusChecklistModal')).show();
  }

  $(document).on('click', '.edit-status-checklist', function(){
    const statusId = $(this).data('id');
    const statusName = $(this).data('name');
    openModal(statusId, statusName);
  });

  $('#saveStatusChecklist').on('click', function(){
    const $btn = $(this);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
    
    const statusId = $('#modal_status_id').val();
    if(!statusId){
      $('#statusChecklistError').removeClass('d-none').text('Invalid status');
      $btn.prop('disabled', false).html(originalHtml);
      return;
    }
    const selected = $('.checklist-checkbox:checked').map(function(){ return $(this).val(); }).get();
    
    $.ajax({
      url: "{{ route('calendar-status-checklist.update') }}",
      method: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        status_id: statusId,
        checklist_ids: selected
      }
    }).done(function(resp){
      if(resp && resp.success){
        showAlert('success', 'Checklists updated successfully!');
        bootstrap.Modal.getInstance(document.getElementById('statusChecklistModal')).hide();
        loadData(1); // Reloading page 1 is safest to refresh relationships
      } else {
        $('#statusChecklistError').removeClass('d-none').text(resp.message || 'Failed to save');
      }
    }).fail(function(xhr){
      $('#statusChecklistError').removeClass('d-none').text(xhr.responseJSON?.message || 'Failed to save');
    }).always(function() {
      $btn.prop('disabled', false).html(originalHtml);
    });
  });

  $(document).ready(function(){
      loadData();
  });
})();

function escapeHtml(text = '') {
  return text
    .toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
</script>
@endpush
