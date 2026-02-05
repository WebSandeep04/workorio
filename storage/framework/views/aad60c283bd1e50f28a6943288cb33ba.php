

<?php $__env->startSection('title', 'Asset Categories'); ?>
<?php $__env->startSection('page_title', 'Asset Categories'); ?>

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
      <input type="text" id="search" placeholder="Search asset categories..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createAssetCategoryModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="assetCategoriesTable">
          <thead>
            <tr>
              <th>Category Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="2" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading asset categories...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="assetCategoryRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade modal-modern" id="createAssetCategoryModal" tabindex="-1" aria-labelledby="createAssetCategoryModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createAssetCategoryModalLabel">
          <i class="bi bi-plus text-white"></i>
          Create Asset Category
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAssetCategoryForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label for="category_name" class="form-label-modern">Category Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="category_name" name="name" required placeholder="Enter category name">
          </div>
          
          <div class="mb-2">
             <div class="d-flex justify-content-between align-items-center mb-2">
                 <label class="form-label-modern mb-0">Custom Fields</label>
                 <button type="button" class="btn btn-sm btn-outline-primary" id="addCreateFieldBtn">
                     <i class="bi bi-plus"></i> Add Field
                 </button>
             </div>
             <div id="create_fields_container">
                 <!-- Fields will be added here -->
             </div>
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

<!-- Edit Modal -->
<div class="modal fade modal-modern" id="editAssetCategoryModal" tabindex="-1" aria-labelledby="editAssetCategoryModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="editAssetCategoryModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Asset Category
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAssetCategoryForm">
        <div class="modal-body pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_category_id">
          <div class="mb-3">
            <label for="edit_category_name" class="form-label-modern">Category Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_category_name" required>
          </div>
          
          <div class="mb-2">
             <div class="d-flex justify-content-between align-items-center mb-2">
                 <label class="form-label-modern mb-0">Custom Fields</label>
                 <button type="button" class="btn btn-sm btn-outline-primary" id="addEditFieldBtn">
                     <i class="bi bi-plus"></i> Add Field
                 </button>
             </div>
             <div id="edit_fields_container">
                 <!-- Fields will be added here -->
             </div>
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
    const $info = $('#assetCategoryRangeInfo');
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
  loadAssetCategories();

  function loadAssetCategories(page = 1) {
    let search = $('#search').val();
    
    $('#assetCategoriesTable tbody').html(`
      <tr>
        <td colspan="2" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading asset categories...</p>
        </td>
      </tr>
    `);
    
    $.get(`<?php echo e(route('asset-category.fetch')); ?>?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#assetCategoriesTable tbody').html(`
          <tr>
            <td colspan="2" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Categories Found</h5>
              <p>Get started by creating your first asset category.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, cat) {
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${cat.name}</strong></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <a href="/asset-category/${cat.id}/fields" class="btn-action btn-action-edit" title="Manage Fields" style="background:#6c757d !important;">
                  <i class="bi bi-list-check"></i>
                </a>
                <button class="btn-action btn-action-edit editBtn"
                  data-id="${cat.id}" data-name="${cat.name}" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${cat.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#assetCategoriesTable tbody').html(rows);

      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
    });
  }

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadAssetCategories(page);
    }
  });
  
  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadAssetCategories(1);
      }, 300);
  });
  
  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });

  // --- Custom Fields Logic ---
  
  function addFieldRow(containerId, field = null) {
      const $container = $('#' + containerId);
      const name = field ? field.name : '';
      const type = field ? field.type : 'text';
      // If options is an array, join it. Depending on how it's stored/returned.
      // PHP json_encode returns array.
      let options = '';
      if(field && field.options) {
        if(Array.isArray(field.options)) options = field.options.join(', ');
        else if(typeof field.options === 'string') {
            try {
                options = JSON.parse(field.options).join(', ');
            } catch(e) { options = field.options; }
        }
      }
      
      const id = field ? field.id : '';
      
      const rowHtml = `
          <div class="field-row card p-2 mb-2 bg-light border" style="border-style: dashed !important;">
              <input type="hidden" class="field-id" value="${id}">
              <div class="row g-2 align-items-center">
                  <div class="col-md-5">
                      <input type="text" class="form-control form-control-modern field-name" placeholder="Field Name" value="${name}" required>
                  </div>
                  <div class="col-md-3">
                      <select class="form-select form-control-modern field-type">
                          <option value="text" ${type === 'text' ? 'selected' : ''}>Text</option>
                          <option value="dropdown" ${type === 'dropdown' ? 'selected' : ''}>Dropdown</option>
                      </select>
                  </div>
                  <div class="col-md-3">
                       <input type="text" class="form-control form-control-modern field-options" placeholder="Options (a, b, c)" 
                       value="${options}" style="display: ${type === 'dropdown' ? 'block' : 'none'};" ${type === 'dropdown' ? 'required' : ''}>
                  </div>
                  <div class="col-md-1 text-end">
                      <button type="button" class="btn btn-sm btn-outline-danger remove-field-btn" style="border:none;"><i class="bi bi-trash"></i></button>
                  </div>
              </div>
          </div>
      `;
      $container.append(rowHtml);
  }

  $(document).on('change', '.field-type', function() {
      const type = $(this).val();
      const $row = $(this).closest('.field-row');
      const $options = $row.find('.field-options');
      if (type === 'dropdown') {
          $options.show();
          $options.attr('required', true);
          $options.attr('placeholder', 'Options (a, b, c)');
      } else {
          $options.hide();
          $options.removeAttr('required');
      }
  });

  $(document).on('click', '.remove-field-btn', function() {
      $(this).closest('.field-row').remove();
  });

  $('#addCreateFieldBtn').click(function() {
      addFieldRow('create_fields_container');
  });

  $('#addEditFieldBtn').click(function() {
      addFieldRow('edit_fields_container');
  });

  function getFieldsData(containerId) {
      const fields = [];
      $('#' + containerId + ' .field-row').each(function() {
          const id = $(this).find('.field-id').val();
          const name = $(this).find('.field-name').val();
          const type = $(this).find('.field-type').val();
          const optionsStr = $(this).find('.field-options').val();
          
          let field = { name, type };
          if (id) field.id = id;
          
          if (type === 'dropdown') {
              field.options = optionsStr.split(',').map(s => s.trim()).filter(s => s);
          }
          fields.push(field);
      });
      return fields;
  }

  $('#createAssetCategoryForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');
    
    const fields = getFieldsData('create_fields_container');

    $.ajax({
        url: "<?php echo e(route('asset-category.store')); ?>",
        type: "POST",
        data: {
             name: $('#category_name').val(),
             fields: fields,
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function () {
          $('#createAssetCategoryModal').modal('hide');
          $('#createAssetCategoryForm')[0].reset();
          $('#create_fields_container').empty();
          loadAssetCategories();
          showAlert('success', 'Asset category created successfully.');
        },
        error: function (xhr) {
            let msg = 'Failed to create asset category.';
            if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
            alert(msg);
        },
        complete: function() {
          $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit');
        }
    });
  });

  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    // Fetch details including fields
    // Set loading state if needed, or just wait
    // We don't have a loading spinner on the button itself in the table, but we can just open modal and show loading? 
    // Or better, fetch first then show modal.
    
    $.get(`/asset-category/${id}`, function(data) {
        $('#edit_category_id').val(data.id);
        $('#edit_category_name').val(data.name);
        
        $('#edit_fields_container').empty();
        if(data.fields && data.fields.length > 0) {
            data.fields.forEach(field => {
                addFieldRow('edit_fields_container', field);
            });
        }
        
        $('#editAssetCategoryModal').modal('show');
    }).fail(function() {
        showAlert('danger', 'Failed to fetch category details');
    });
  });

  $('#editAssetCategoryForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
    
    let id = $('#edit_category_id').val();
    const fields = getFieldsData('edit_fields_container');

    $.ajax({
      url: `/asset-category/${id}`,
      type: 'PUT',
      data: {
        name: $('#edit_category_name').val(),
        fields: fields,
        _token: '<?php echo e(csrf_token()); ?>'
      },
      success: function () {
        $('#editAssetCategoryModal').modal('hide');
        loadAssetCategories();
        showAlert('success', 'Asset category updated successfully.');
      },
      error: function(xhr) {
        let msg = 'Failed to update asset category.';
        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
        alert(msg);
      },
      complete: function() {
        $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
      }
    });
  });

  $(document).on('click', '.deleteBtn', function () {
    if (confirm('Are you sure you want to delete this asset category?')) {
      $.ajax({
        url: `/asset-category/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function () {
          loadAssetCategories();
          showAlert('success', 'Asset category deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete asset category.');
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/software-setup/asset-category/index.blade.php ENDPATH**/ ?>