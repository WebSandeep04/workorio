

<?php $__env->startSection('title', 'Countries'); ?>
<?php $__env->startSection('page_title', 'Countries'); ?>

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

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .spin {
    animation: spin 1s linear infinite;
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
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
    color: white;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search countries..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createCountryModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="countriesTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Country Name</th>
              <th>Status</th>
              <th>Notes</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading countries...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="countryRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Country Modal -->
<div class="modal fade modal-modern" id="createCountryModal" tabindex="-1" aria-labelledby="createCountryModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createCountryModalLabel">
          <i class="bi bi-plus text-white"></i>
          Create Country
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createCountryForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <div class="mb-2">
            <label for="country_name" class="form-label-modern">Country Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="country_name" name="country_name" required placeholder="Enter country name">
          </div>
          <div class="mb-2">
            <label for="country_status" class="form-label-modern">Status</label>
            <select id="country_status" name="country_status" class="form-select form-select-modern">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="mb-2">
            <label for="country_notes" class="form-label-modern">Notes</label>
            <textarea id="country_notes" name="country_notes" class="form-control form-control-modern" rows="3" placeholder="Optional notes"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Country Modal -->
<div class="modal fade modal-modern" id="editCountryModal" tabindex="-1" aria-labelledby="editCountryModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="editCountryModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Country
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editCountryForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_country_id">
          <div class="mb-2">
            <label class="form-label-modern">Country Code</label>
            <input type="text" class="form-control form-control-modern" id="edit_country_code" readonly>
          </div>
          <div class="mb-2">
            <label for="edit_country_name" class="form-label-modern">Country Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_country_name" required>
          </div>
          <div class="mb-2">
            <label for="edit_country_status" class="form-label-modern">Status</label>
            <select id="edit_country_status" class="form-select form-select-modern">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="mb-2">
            <label for="edit_country_notes" class="form-label-modern">Notes</label>
            <textarea id="edit_country_notes" class="form-control form-control-modern" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Update
          </button>
        </div>
      </form>
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

function escapeHtml(text = '') {
  return text
    .toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// Build compact pagination: "Previous [current / last] Next"
function buildSimplePagination($container, current, last) {
    $container.empty();
    // Prev
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    // Current (disabled as display only)
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    // Next
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#countryRangeInfo');
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

$(function () {
  let searchTimeout;
  loadCountries();

  function loadCountries(page = 1) {
    let search = $('#search').val();
    
    $('#countriesTable tbody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading countries...</p>
        </td>
      </tr>
    `);

    $.get(`<?php echo e(route('countries.list')); ?>?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#countriesTable tbody').html(`
          <tr>
            <td colspan="5" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Countries Found</h5>
              <p>Create your first country to get started.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }

      let rows = '';
      $.each(data.data, function (i, country) {
        const status = (country.status || 'inactive').toLowerCase();
        const statusClass = status === 'active' ? 'bg-success' : 'bg-secondary';
        const statusLabel = status.charAt(0).toUpperCase() + status.slice(1);
        const notes = country.notes ? escapeHtml(country.notes) : '<span class="text-muted">—</span>';
        const code = escapeHtml(country.code || '—');

        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${code}</strong></td>
            <td>${escapeHtml(country.name)}</td>
            <td><span class="badge ${statusClass}">${statusLabel}</span></td>
            <td>${notes}</td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button 
                  class="btn-action btn-action-edit editBtn"
                  data-id="${country.id}"
                  data-code="${escapeHtml(country.code || '')}"
                  data-name="${escapeHtml(country.name)}"
                  data-status="${escapeHtml(status)}"
                  data-notes="${escapeHtml(country.notes || '')}"
                  title="Edit"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${country.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#countriesTable tbody').html(rows);

      // Compact pagination
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
      
    }).fail(function () {
      $('#countriesTable tbody').html(`
        <tr>
          <td colspan="5" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mt-2 mb-0">Unable to load countries. Please try again.</p>
          </td>
        </tr>
      `);
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadCountries(page);
    }
  });

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadCountries(1);
      }, 300);
  });
  
  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });

  $('#createCountryForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');

    $.post("<?php echo e(route('countries.store')); ?>", {
      name: $('#country_name').val(),
      status: $('#country_status').val(),
      notes: $('#country_notes').val(),
      _token: '<?php echo e(csrf_token()); ?>'
    }, function () {
      $('#createCountryModal').modal('hide');
      $('#createCountryForm')[0].reset();
      $('#country_status').val('active');
      loadCountries();
      showAlert('success', 'Country created successfully.');
    }).fail(function (xhr) {
      alert(Object.values(xhr.responseJSON.errors || {}).join("\\n") || 'Failed to create country.');
    }).always(function () {
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit');
    });
  });

  $(document).on('click', '.editBtn', function () {
    $('#edit_country_id').val($(this).data('id'));
    $('#edit_country_code').val($(this).data('code') || '—');
    $('#edit_country_name').val($(this).data('name'));
    $('#edit_country_status').val($(this).data('status'));
    $('#edit_country_notes').val($(this).data('notes'));
    $('#editCountryModal').modal('show');
  });

  $('#editCountryForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');

    const id = $('#edit_country_id').val();
    $.ajax({
      url: `/countries/${id}`,
      type: 'PUT',
      data: {
        name: $('#edit_country_name').val(),
        status: $('#edit_country_status').val(),
        notes: $('#edit_country_notes').val(),
        _token: '<?php echo e(csrf_token()); ?>'
      },
      success: function () {
        $('#editCountryModal').modal('hide');
        loadCountries();
        showAlert('success', 'Country updated successfully.');
      },
      error: function (xhr) {
        alert(Object.values(xhr.responseJSON.errors || {}).join("\\n") || 'Failed to update country.');
      },
      complete: function () {
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
      }
    });
  });

  $(document).on('click', '.deleteBtn', function () {
    if (!confirm('Are you sure you want to delete this country?')) {
      return;
    }
    $.ajax({
      url: `/countries/${$(this).data('id')}`,
      type: 'DELETE',
      data: { _token: '<?php echo e(csrf_token()); ?>' },
      success: function (response) {
        if (response.success) {
          loadCountries();
          showAlert('success', 'Country deleted successfully.');
        } else {
          showAlert('error', response.message || 'Failed to delete country.');
        }
      },
      error: function (xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.message
          ? xhr.responseJSON.message
          : 'Failed to delete country.';
        showAlert('error', message);
      }
    });
  });
});
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/countries.blade.php ENDPATH**/ ?>