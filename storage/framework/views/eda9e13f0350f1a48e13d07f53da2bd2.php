

<?php $__env->startSection('title', 'Calendar Clients'); ?>
<?php $__env->startSection('page_title', 'Calendar Clients'); ?>

<?php $__env->startPush('styles'); ?>
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
  
  .btn-action-delete {
    color: white;
    background: #343AFA !important;
    border-radius: 4px;
  }
  
  .btn-action i {
    font-size: 0.8rem;
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
  
  .badge-modern-info {
    font-weight: 500;
    padding: 4px 8px;
    font-size: 0.75rem;
    background-color: rgba(67, 74, 250, 0.1);
    color: #434AFA;
    border-radius: 4px;
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
  
  .form-control-modern, .form-select-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-control-modern:focus, .form-select-modern:focus {
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search clients..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createClientModal">
      <i class="bi bi-plus me-1"></i>Create Client
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="clientsTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Social Handles</th>
              <th>Active</th>
              <th style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody id="clientsTableBody">
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading clients...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="clientRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade modal-modern" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createClientModalLabel">
          <i class="bi bi-plus-circle text-white"></i>
          Create Client
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createClientForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label for="client_name" class="form-label-modern">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="client_name" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="client_active" checked>
              <label class="form-check-label" for="client_active">Active</label>
            </div>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label-modern mb-2">Social Handles</label>
            <div id="create_social_handles" class="row g-2">
              <!-- Dynamically loaded -->
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

<!-- Edit Client Modal -->
<div class="modal fade modal-modern" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editClientModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Client
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editClientForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_client_id">
          <div class="mb-3">
            <label for="edit_client_name" class="form-label-modern">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_client_name" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="edit_client_active">
              <label class="form-check-label" for="edit_client_active">Active</label>
            </div>
          </div>
          <hr>
          <div class="mb-3">
            <label class="form-label-modern mb-2">Social Handles</label>
            <div id="edit_social_handles" class="row g-2">
              <!-- Dynamically loaded -->
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

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-modern" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteClientModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this client?</p>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
    const $info = $('#clientRangeInfo');
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
  let socialHandles = [];
  let relationships = {};
  let searchTimeout;

  function loadClients(page = 1) {
    let search = $('#search').val();

    $('#clientsTableBody').html(`
      <tr>
        <td colspan="4" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading clients...</p>
        </td>
      </tr>
    `);
    
    $.get(`<?php echo e(route('calendar-clients.fetch')); ?>?page=${page}&search=${search}`)
      .done(function (data) {
        // Correctly handle the JSON structure being returned
        socialHandles = data.social_handles || [];
        relationships = data.relationships || {};
        const clientsData = data.clients;

        if (!clientsData.data || clientsData.data.length === 0) {
          $('#clientsTableBody').html(`
            <tr>
              <td colspan="4" class="empty-state">
                <i class="bi bi-inbox"></i>
                <h5>No Clients Found</h5>
                <p>Get started by creating your first client.</p>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }

        const rows = clientsData.data.map((r, index) => {
          const linkedIds = relationships[r.id] || [];
          const linkedNames = linkedIds.map(function(id){ 
            const h = socialHandles.find(sh => sh.id == id); 
            return h ? h.name : null; 
          }).filter(Boolean).join(', ');
          
          const activeBadge = r.is_active ? 
            '<span class="badge" style="background: #10b981; color: white;">Yes</span>' : 
            '<span class="badge" style="background: #6b7280; color: white;">No</span>';

          return `
            <tr style="animation-delay: ${index * 0.1}s;">
              <td>${escapeHtml(r.name)}</td>
              <td>${linkedNames ? `<span style="color: #6c757d;">${escapeHtml(linkedNames)}</span>` : '<span class="text-muted small">None</span>'}</td>
              <td>${activeBadge}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button 
                    class="btn-action btn-action-edit editBtn"
                    data-id="${r.id}"
                    data-name="${escapeHtml(r.name)}"
                    data-active="${r.is_active}"
                    title="Edit"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-action-delete deleteBtn" data-id="${r.id}" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        }).join('');

        $('#clientsTableBody').html(rows);
        
        buildSimplePagination($('#paginationLinks'), clientsData.current_page || 1, clientsData.last_page || 1);
        updateRangeInfo(clientsData.from, clientsData.to, clientsData.total);
      })
      .fail(function () {
        $('#clientsTableBody').html(`
          <tr>
            <td colspan="4" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i>
              Failed to load clients. Please try again.
            </td>
          </tr>
        `);
      });
  }

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadClients(1);
      }, 300);
  });

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadClients(page);
    }
  });

  function renderSocialHandles(containerId, selectedIds = []) {
    let html = '';
    if(socialHandles.length === 0){
      html = '<div class="col-12 text-muted small">No social handles available.</div>';
    } else {
      socialHandles.forEach(function(sh){
        const isChecked = selectedIds.includes(sh.id);
        html += `<div class="col-md-6">
          <div class="form-check p-2 active-check-wrapper" style="border: 1px solid #eee; border-radius: 4px; transition: background 0.2s;">
            <input class="form-check-input client-sh me-2" type="checkbox" value="${sh.id}" id="${containerId}_sh_${sh.id}" ${isChecked ? 'checked' : ''} style="cursor: pointer;">
            <label class="form-check-label w-100" for="${containerId}_sh_${sh.id}" style="cursor: pointer; user-select: none;">${escapeHtml(sh.name)}</label>
          </div>
        </div>`;
      });
    }
    $('#'+containerId).html(html);
  }

  // Pre-render social handles on modal show (Create)
  $('#createClientModal').on('show.bs.modal', function () {
    renderSocialHandles('create_social_handles');
  });

  $('#createClientForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#createSubmitBtn');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    let payload = {
      name: $('#client_name').val(),
      is_active: $('#client_active').is(':checked') ? 1 : 0,
      _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // Collect all checked social handles
    payload.social_handle_ids = $('#create_social_handles .client-sh:checked').map(function(){ return $(this).val(); }).get();

    $.post("<?php echo e(route('calendar-clients.store')); ?>", payload)
      .done(function (response) {
        $('#createClientModal').modal('hide');
        $('#createClientForm')[0].reset();
        loadClients(); // Reload to refresh data and relationships
        showAlert('success', 'Client created successfully.');
      })
      .fail(function (xhr) {
        const errors = xhr.responseJSON?.errors;
        const message = errors ? Object.values(errors).flat().join(', ') : 'Failed to create client.';
        showAlert('error', message);
      })
      .always(function () {
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit');
      });
  });

  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const isActive = $(this).data('active') == 1;

    $('#edit_client_id').val(id);
    $('#edit_client_name').val(name);
    $('#edit_client_active').prop('checked', isActive);
    
    // Get relationships for this client
    const linkedIds = relationships[id] || [];
    renderSocialHandles('edit_social_handles', linkedIds);
    
    $('#editClientModal').modal('show');
  });

  $('#editClientForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#editSubmitBtn');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');

    const id = $('#edit_client_id').val();
    let payload = {
      name: $('#edit_client_name').val(),
      is_active: $('#edit_client_active').is(':checked') ? 1 : 0,
      _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // Collect checked social handles
    payload.social_handle_ids = $('#edit_social_handles .client-sh:checked').map(function(){ return $(this).val(); }).get();

    $.ajax({
      url: `/calendar/clients/${id}`,
      type: 'PUT',
      data: payload
    })
      .done(function (response) {
        $('#editClientModal').modal('hide');
        loadClients();
        showAlert('success', 'Client updated successfully.');
      })
      .fail(function (xhr) {
        const errors = xhr.responseJSON?.errors;
        const message = errors ? Object.values(errors).flat().join(', ') : 'Failed to update client.';
        showAlert('error', message);
      })
      .always(function () {
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
      });
  });

  $(document).on('click', '.deleteBtn', function () {
    $('#confirmDeleteBtn').data('id', $(this).data('id'));
    $('#deleteClientModal').modal('show');
  });

  $('#confirmDeleteBtn').click(function() {
      const id = $(this).data('id');
      const $btn = $(this);
      $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');

      $.ajax({
        url: `/calendar/clients/${id}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' }
      })
        .done(function (response) {
          $('#deleteClientModal').modal('hide');
          loadClients();
          showAlert('success', 'Client deleted successfully.');
        })
        .fail(function (xhr) {
          showAlert('error', 'Failed to delete client.');
        })
        .always(function() {
            $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
        });
  });

  $(document).ready(function(){
      loadClients();
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/calendar/clients.blade.php ENDPATH**/ ?>