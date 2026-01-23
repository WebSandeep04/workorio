@extends('layouts.app')

@section('title', 'Checklist')
@section('page_title', 'Checklist')

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
  
  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }
  
  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
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

  .btn-action-manage {
      color: white;
      background: #343AFA !important;
      border-radius: 4px;
  }
  
  .btn-action-delete {
    color: white;
    background: #343AFA !important;
    border-radius: 4px;
  }
  
  /* Pagination */
  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }
  
  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }
  
  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
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
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
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
      background: #434AFA; /* Unified color */
      color: white;
  }
  .btn-modern-danger:hover {
      background: #3538d4;
  }

  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  @media (max-width: 767px){
      .container-fluid{
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        margin-right: 0;
      }
    
      .table-search {
        flex-direction: row;
        gap: 0.5rem;
      }
      
      .table-search-btn {
        width: auto;
        padding: 0.35rem 0.75rem;
      }
    
      .table-search-field {
          width: 100%;
      }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search checklists..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createChecklistModal">
      <i class="bi bi-plus me-1"></i>Create Checklist
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="checklistTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Active</th>
              <th style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody id="checklistTableBody">
            <tr>
              <td colspan="3" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading checklists...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="checklistRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Checklist Modal -->
<div class="modal fade modal-modern" id="createChecklistModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-plus-circle text-white"></i>
          Create Checklist
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createChecklistForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="mb-3">
            <label class="form-label-modern">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="create_checklist_name" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="create_checklist_active" checked>
              <label class="form-check-label" for="create_checklist_active">Active</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-danger w-100 justify-content-center" id="createSubmitBtn">
            <i class="bi bi-check-circle"></i>
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Checklist Modal -->
<div class="modal fade modal-modern" id="editChecklistModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Checklist
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editChecklistForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_checklist_id">
          <div class="mb-3">
            <label class="form-label-modern">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_checklist_name" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="edit_checklist_active">
              <label class="form-check-label" for="edit_checklist_active">Active</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-danger w-100 justify-content-center" id="editSubmitBtn">
            <i class="bi bi-check-circle"></i>
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Checklist Modal -->
<div class="modal fade modal-modern" id="deleteChecklistModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this checklist?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-modern btn-modern-danger" id="confirmDeleteBtn" style="background: #dc3545; color: white;">
          <i class="bi bi-trash"></i>
          Delete
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Manage Options Modal (Keeping it simple but consistent with styling) -->
<div class="modal fade modal-modern" id="optionsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="optionsModalLabel">
          <i class="bi bi-list-ul text-white"></i>
          Manage Options
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <input type="hidden" id="options_checklist_id" />
        <div class="d-flex gap-2 mb-3">
            <input type="text" id="option_name" class="form-control form-control-modern" placeholder="New Option Name" />
            <input type="number" id="option_sort" class="form-control form-control-modern" placeholder="Sort" style="max-width:100px" value="0" />
            <div class="form-check d-flex align-items-center">
                <input class="form-check-input" type="checkbox" id="option_active" checked>
                <label class="form-check-label ms-1" for="option_active">Active</label>
            </div>
            <button class="btn btn-modern" style="background: #434AFA; color: white;" id="addOption">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>
        <div class="alert alert-danger d-none mb-3" id="optionError"></div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
             <table class="table custom-table" id="optionsTable">
                <thead>
                    <tr>
                        <th style="background: #f8f9fa;">Name</th>
                        <th style="background: #f8f9fa; width: 100px;">Sort</th>
                        <th style="background: #f8f9fa; width: 80px;">Active</th>
                        <th style="background: #f8f9fa; width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="optionsTableBody">
                    <tr><td colspan="4" class="text-center text-muted">No options found.</td></tr>
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    const $info = $('#checklistRangeInfo');
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
    let searchTimeout;

    function loadChecklists(page = 1){
        let search = $('#search').val();

        $('#checklistTableBody').html(`
          <tr>
            <td colspan="3" class="loading-state">
              <i class="bi bi-arrow-repeat spin"></i>
              <p class="mt-2 mb-0">Loading checklists...</p>
            </td>
          </tr>
        `);

        $.get(`{{ route('checklist.fetch') }}?page=${page}&search=${search}`).done(function(data){
            if(!data.data || data.data.length === 0){
                $('#checklistTableBody').html(`
                  <tr>
                    <td colspan="3" class="empty-state">
                      <i class="bi bi-inbox"></i>
                      <h5>No Checklists Found</h5>
                      <p>Get started by creating your first checklist.</p>
                    </td>
                  </tr>
                `);
                $('#paginationLinks').empty();
                updateRangeInfo(0,0,0);
                return;
            }

            const rows = data.data.map((r, index) => {
                const activeBadge = r.is_active ? 
                    '<span class="badge" style="background: #10b981; color: white;">Yes</span>' : 
                    '<span class="badge" style="background: #6b7280; color: white;">No</span>';
                
                return `
                <tr style="animation-delay: ${index * 0.1}s;">
                    <td><strong>${escapeHtml(r.name)}</strong></td>
                    <td>${activeBadge}</td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn-action btn-action-edit edit-checklist" data-id="${r.id}" data-name="${escapeHtml(r.name)}" data-active="${r.is_active}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-action btn-action-manage manage-options" data-id="${r.id}" data-name="${escapeHtml(r.name)}" title="Manage Options">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button class="btn-action btn-action-delete del-checklist" data-id="${r.id}" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            
            $('#checklistTableBody').html(rows);
            buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
            updateRangeInfo(data.from, data.to, data.total);
        }).fail(function(){
            $('#checklistTableBody').html(`
                <tr>
                    <td colspan="3" class="text-danger text-center py-4">
                        <i class="bi bi-exclamation-triangle"></i> Failed to load checklists.
                    </td>
                </tr>
            `);
        });
    }

    // Search input
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadChecklists(1);
        }, 300);
    });

    // Pagination click
    $(document).on('click', '#paginationLinks .page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadChecklists(page);
        }
    });

    $('#createChecklistForm').on('submit', function(e){
        e.preventDefault();
        const $btn = $('#createSubmitBtn');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');
        
        const payload = {
            _token: '{{ csrf_token() }}',
            name: $('#create_checklist_name').val(),
            is_active: $('#create_checklist_active').is(':checked') ? 1 : 0
        };

        $.post("{{ route('checklist.store') }}", payload).done(function(){
            $('#createChecklistModal').modal('hide');
            $('#createChecklistForm')[0].reset();
            loadChecklists();
            showAlert('success', 'Checklist created successfully.');
        }).fail(function(xhr){
            showAlert('error', xhr.responseJSON?.message || 'Create failed');
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit');
        });
    });

    $(document).on('click', '.edit-checklist', function(){
        const id = $(this).data('id');
        const name = $(this).data('name');
        const isActive = $(this).data('active') == 1;

        $('#edit_checklist_id').val(id);
        $('#edit_checklist_name').val(name);
        $('#edit_checklist_active').prop('checked', isActive);
        $('#editChecklistModal').modal('show');
    });

    $('#editChecklistForm').on('submit', function(e){
        e.preventDefault();
        const $btn = $('#editSubmitBtn');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
        
        const id = $('#edit_checklist_id').val();
         const payload = {
            _token: '{{ csrf_token() }}',
            name: $('#edit_checklist_name').val(),
            is_active: $('#edit_checklist_active').is(':checked') ? 1 : 0
        };

        $.ajax({ url:`/checklist/${id}`, method:'PUT', data: payload }).done(function(){
            $('#editChecklistModal').modal('hide');
            loadChecklists();
            showAlert('success', 'Checklist updated successfully.');
        }).fail(function(xhr){
             showAlert('error', xhr.responseJSON?.message || 'Update failed');
        }).always(function(){
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
        });
    });

    $(document).on('click', '.del-checklist', function(){
        $('#confirmDeleteBtn').data('id', $(this).data('id'));
        $('#deleteChecklistModal').modal('show');
    });
    
    $('#confirmDeleteBtn').click(function(){
        const id = $(this).data('id');
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');

        $.ajax({ url: `/checklist/${id}`, method:'DELETE', data:{ _token: '{{ csrf_token() }}' } })
            .done(function(){
                $('#deleteChecklistModal').modal('hide');
                loadChecklists();
                showAlert('success', 'Checklist deleted.');
            })
            .fail(function(){
                showAlert('error', 'Delete failed');
            })
            .always(function(){
                $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
            });
    });

    // --- Options Logic (kept mostly inline but styled) ---
    function loadOptions(checklistId){
        $('#optionsTableBody').html('<tr><td colspan="4" class="text-center p-3"><i class="bi bi-arrow-repeat spin text-primary"></i> Loading...</td></tr>');
        $.get("{{ route('checklist.options.fetch') }}", { checklist_id: checklistId }).done(function(rows){
            let html='';
            if(!rows || rows.length===0){ html='<tr><td colspan="4" class="text-center text-muted p-3">No options found. Add one above.</td></tr>'; }
            else {
                rows.forEach(function(r){
                    html += `<tr data-id="${r.id}">
                        <td><input class="form-control form-control-modern opt-name" value="${escapeHtml(r.name)}"></td>
                        <td><input type="number" class="form-control form-control-modern opt-sort" value="${r.sort_order}"></td>
                        <td class="text-center"><input type="checkbox" class="form-check-input opt-active" ${r.is_active? 'checked':''} style="cursor: pointer;"></td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn-action btn-action-edit save-option" title="Save Changes"><i class="bi bi-check2"></i></button>
                                <button class="btn-action btn-action-delete del-option" title="Delete Option"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
            }
            $('#optionsTableBody').html(html);
        });
    }

    $(document).on('click', '.manage-options', function(){
        const checklistId = $(this).data('id');
        const checklistName = $(this).data('name');
        $('#options_checklist_id').val(checklistId);
        $('#optionsModalLabel').html('<i class="bi bi-list-ul text-white"></i> Manage Options - ' + escapeHtml(checklistName));
        $('#option_name').val('');
        $('#option_sort').val('0');
        $('#option_active').prop('checked', true);
        $('#optionError').addClass('d-none').text('');
        loadOptions(checklistId);
        $('#optionsModal').modal('show');
    });

    $('#addOption').on('click', function(){
        const checklistId = $('#options_checklist_id').val();
        const payload = {
            _token: '{{ csrf_token() }}',
            checklist_id: checklistId,
            name: ($('#option_name').val()||'').trim(),
            sort_order: parseInt($('#option_sort').val()||'0', 10),
            is_active: $('#option_active').is(':checked') ? 1 : 0,
        };
        if (!payload.name) { $('#optionError').removeClass('d-none').text('Option name is required'); return; }
        
        const $btn = $(this);
        $btn.prop('disabled', true);
        
        $.post("{{ route('checklist.options.store') }}", payload).done(function(){
            $('#option_name').val('');
            $('#option_sort').val('0');
            $('#option_active').prop('checked', true);
            $('#optionError').addClass('d-none');
            loadOptions(checklistId);
        }).fail(function(xhr){
            $('#optionError').removeClass('d-none').text(xhr.responseJSON?.message || 'Save failed');
        }).always(function(){
             $btn.prop('disabled', false);
        });
    });

    $(document).on('click', '.save-option', function(){
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        const $btn = $(this);
        
        const payload = {
            _token: '{{ csrf_token() }}',
            name: (tr.find('.opt-name').val()||'').trim(),
            sort_order: parseInt(tr.find('.opt-sort').val()||'0', 10),
            is_active: tr.find('.opt-active').is(':checked') ? 1 : 0,
        };
        if (!payload.name) { showAlert('error', 'Option name is required'); return; }
        
        $btn.html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);
        
        $.ajax({ url:`/checklist/options/${id}`, method:'PUT', data: payload })
            .done(function(){ 
                showAlert('success', 'Option updated');
            })
            .fail(function(){ 
                showAlert('error', 'Update failed'); 
            })
            .always(function(){
                 $btn.html('<i class="bi bi-check2"></i>').prop('disabled', false);
            });
    });

    $(document).on('click', '.del-option', function(){
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        const checklistId = $('#options_checklist_id').val();
        if(!confirm('Delete option?')) return;
        
        const $btn = $(this);
        $btn.html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);

        $.ajax({ url:`/checklist/options/${id}`, method:'DELETE', data:{ _token: '{{ csrf_token() }}' } })
            .done(function(){ loadOptions(checklistId); })
            .fail(function(){ showAlert('error', 'Delete failed'); $btn.html('<i class="bi bi-trash"></i>').prop('disabled', false); });
    });

    $(document).ready(loadChecklists);
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
