

<?php $__env->startSection('title', 'Suppliers'); ?>
<?php $__env->startSection('page_title', 'Suppliers'); ?>

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
  .modal-lg {
      max-width: 800px;
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
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    background: #fdfdfd;
  }
  
  .form-control-modern::placeholder {
    color: #aaa;
    font-size: 0.85rem;
  }
  
  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
    background: #fff;
  }
  
  .form-section-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
    padding-bottom: 0.25rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .form-section-title i {
      color: #434AFA;
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
      <input type="text" id="search" placeholder="Search suppliers..." />
    </div>
    <button class="table-search-btn" onclick="openCreateModal()">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="suppliersTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Contact Person</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>City</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading suppliers...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="supplierRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create/Edit Modal Reuse -->
<div class="modal fade modal-modern" id="supplierModal" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="supplierModalLabel">
          <i class="bi bi-plus text-white"></i>
          <span>Create Supplier</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="supplierForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="supplier_id" name="id">
        <div class="modal-body pt-4 pb-4">
            
            <!-- Basic Info -->
            <div class="form-section-title">
                <i class="bi bi-person-badge"></i> Basic Information
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label-modern">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-modern" name="name" required placeholder="Business Name">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Contact Person</label>
                    <input type="text" class="form-control form-control-modern" name="contact_person" placeholder="Primary Contact">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Email</label>
                    <input type="email" class="form-control form-control-modern" name="email" placeholder="email@example.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Mobile</label>
                    <input type="text" class="form-control form-control-modern" name="mobile" placeholder="Mobile Number">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Phone</label>
                    <input type="text" class="form-control form-control-modern" name="phone" placeholder="Landline">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Website</label>
                    <input type="text" class="form-control form-control-modern" name="website" placeholder="https://">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Status</label>
                    <select class="form-control form-control-modern" name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Address Info -->
            <div class="form-section-title">
                <i class="bi bi-geo-alt"></i> Address Information
            </div>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label-modern">Address Line 1</label>
                    <input type="text" class="form-control form-control-modern" name="address_line1" placeholder="Building, Street">
                </div>
                <div class="col-12">
                    <label class="form-label-modern">Address Line 2</label>
                    <input type="text" class="form-control form-control-modern" name="address_line2" placeholder="Suite, Unit, etc.">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">City</label>
                    <input type="text" class="form-control form-control-modern" name="city">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">State</label>
                    <input type="text" class="form-control form-control-modern" name="state">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Country</label>
                    <input type="text" class="form-control form-control-modern" name="country">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Pincode</label>
                    <input type="text" class="form-control form-control-modern" name="pincode">
                </div>
            </div>

            <!-- Financial Info -->
            <div class="form-section-title">
                <i class="bi bi-receipt"></i> Financial Information
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label-modern">GST Number</label>
                    <input type="text" class="form-control form-control-modern" name="gst_number">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">PAN Number</label>
                    <input type="text" class="form-control form-control-modern" name="pan_number">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Bank Name</label>
                    <input type="text" class="form-control form-control-modern" name="bank_name">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Account Number</label>
                    <input type="text" class="form-control form-control-modern" name="account_number">
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">IFSC Code</label>
                    <input type="text" class="form-control form-control-modern" name="ifsc_code">
                </div>
                 <div class="col-md-6">
                    <label class="form-label-modern">Branch Name</label>
                    <input type="text" class="form-control form-control-modern" name="branch_name">
                </div>
            </div>

            <!-- Remarks -->
            <div class="form-section-title">
                <i class="bi bi-pencil"></i> Other
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label-modern">Remarks</label>
                    <textarea class="form-control form-control-modern" name="remarks" rows="2"></textarea>
                </div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            <span id="submitBtnText">Submit</span>
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

// Build compact pagination
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
    const $info = $('#supplierRangeInfo');
    if (!$info.length) return;
    const totalValue = Number(total) || 0;
    const safeStart = totalValue === 0 ? 0 : (from || 1);
    const safeEnd = totalValue === 0 ? 0 : (to || safeStart);
    $info.text(`Showing ${safeStart}-${safeEnd} from ${totalValue} data`);
}

$(function () {
  let searchTimeout;
  loadSuppliers();

  function loadSuppliers(page = 1) {
    let search = $('#search').val();
    
    $('#suppliersTable tbody').html(`
      <tr>
        <td colspan="7" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading suppliers...</p>
        </td>
      </tr>
    `);
    
    $.get(`<?php echo e(route('supplier.fetch')); ?>?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#suppliersTable tbody').html(`
          <tr>
            <td colspan="7" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Suppliers Found</h5>
              <p>Get started by creating your first supplier.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, s) {
        // null safe
        const contact = s.contact_person || '-';
        const mobile = s.mobile || '-';
        const email = s.email || '-';
        const city = s.city || '-';
        const statusClass = s.status === 'Active' ? 'text-success' : 'text-danger';

        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${s.name}</strong></td>
            <td>${contact}</td>
            <td>${mobile}</td>
            <td>${email}</td>
            <td>${city}</td>
            <td><span class="badge bg-light ${statusClass}">${s.status}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit editBtn" data-data='${JSON.stringify(s)}' title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${s.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#suppliersTable tbody').html(rows);
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
    });
  }

  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadSuppliers(page);
  });
  
  // Search
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadSuppliers(1), 300);
  });

  // Open Create
  window.openCreateModal = function() {
      $('#supplierForm')[0].reset();
      $('#supplier_id').val('');
      $('#supplierModalLabel span').text('Create Supplier');
      $('#submitBtnText').text('Submit');
      $('#supplierModal').modal('show');
  };

  // Open Edit
  $(document).on('click', '.editBtn', function() {
      const data = $(this).data('data');
      $('#supplierForm')[0].reset();
      $('#supplier_id').val(data.id);
      
      // Populate fields
      $.each(data, function(key, value) {
         $(`[name="${key}"]`).val(value); 
      });

      $('#supplierModalLabel span').text('Edit Supplier');
      $('#submitBtnText').text('Update');
      $('#supplierModal').modal('show');
  });

  // Submit Form
  $('#supplierForm').submit(function(e) {
      e.preventDefault();
      const id = $('#supplier_id').val();
      const url = id ? `/supplier/${id}` : `<?php echo e(route('supplier.store')); ?>`;
      const method = id ? 'PUT' : 'POST';
      const $btn = $(this).find('button[type="submit"]');

      $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Processing...');

      $.ajax({
          url: url,
          type: method,
          data: $(this).serialize(),
          success: function() {
              $('#supplierModal').modal('hide');
              loadSuppliers();
              showAlert('success', id ? 'Supplier updated.' : 'Supplier created.');
          },
          error: function(xhr) {
              let msg = 'Operation failed.';
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
              alert(msg);
          },
          complete: function() {
              $btn.prop('disabled', false).html(`<i class="bi bi-check-circle"></i> ${id ? 'Update' : 'Submit'}`);
          }
      });
  });

  // Delete
  $(document).on('click', '.deleteBtn', function () {
    if (confirm('Are you sure you want to delete this supplier?')) {
      $.ajax({
        url: `/supplier/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function () {
          loadSuppliers();
          showAlert('success', 'Supplier deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete supplier.');
        }
      });
    }
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/software-setup/supplier/index.blade.php ENDPATH**/ ?>