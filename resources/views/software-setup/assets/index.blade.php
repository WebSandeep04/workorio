@extends('layouts.app')

@section('title', 'Open Assets')
@section('page_title', 'Open Assets')

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
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="table-search" style="margin-bottom:0; width: auto; flex-grow: 1;">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="search" placeholder="Search assets (ID, Category, etc.)..." />
        </div>
      </div>
      <div class="ms-2">
           <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createAssetModal">
              <i class="bi bi-plus me-1"></i>Add Asset
            </button>
      </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="assetsTable">
          <thead>
            <tr>
              <th>Asset ID</th>
              <th>Remark</th>
              <th>Category</th>
              <th>Supplier</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading assets...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="assetRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade modal-modern" id="createAssetModal" tabindex="-1" aria-labelledby="createAssetModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createAssetModalLabel">
          <i class="bi bi-box-seam text-white"></i>
          Create Asset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAssetForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="row g-3">
              <div class="col-md-6">
                <label for="asset_type_id" class="form-label-modern">Asset Type</label>
                <select class="form-select form-control-modern" id="asset_type_id" name="asset_type_id">
                    <option value="">Select Type</option>
                    @foreach($assetTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="asset_category_id" class="form-label-modern">Category <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="asset_category_id" name="asset_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="asset_id" class="form-label-modern">Asset ID / Tag <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="asset_id" name="asset_id" required placeholder="e.g. LP-001">
              </div>
              <div class="col-md-6">
                <label for="remark" class="form-label-modern">Remark</label>
                <textarea class="form-control form-control-modern" id="remark" name="remark" rows="1" placeholder="Enter remark"></textarea>
              </div>
              <div class="col-md-6">
                 <label for="supplier_id" class="form-label-modern">Supplier</label>
                 <select class="form-select form-control-modern" id="supplier_id" name="supplier_id">
                     <option value="">Select Supplier</option>
                     @foreach($suppliers as $supplier)
                         <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                     @endforeach
                 </select>
               </div>
              <div class="col-md-6">
                <label for="status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="status" name="status" required>
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->name }}">{{ $status->name }}</option>
                    @endforeach
                </select>
              </div>
          </div>
          
          <div class="mt-4" id="customFieldsSection" style="display:none;">
               <h6 style="color: #434AFA; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Category Specific Fields</h6>
               <div id="create_custom_fields_container" class="row g-3">
                   <!-- Custom Fields -->
               </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center">
            <i class="bi bi-check-circle"></i>
            Save Asset
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade modal-modern" id="editAssetModal" tabindex="-1" aria-labelledby="editAssetModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Asset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAssetForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_id">
          
          <div class="row g-3">
              <div class="col-md-6">
                <label for="edit_asset_type_id" class="form-label-modern">Asset Type</label>
                <select class="form-select form-control-modern" id="edit_asset_type_id" name="asset_type_id">
                    <option value="">Select Type</option>
                    @foreach($assetTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="edit_asset_category_id" class="form-label-modern">Category <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="edit_asset_category_id" name="asset_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="edit_asset_id" class="form-label-modern">Asset ID / Tag <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="edit_asset_id" name="asset_id" required>
              </div>
              <div class="col-md-6">
                <label for="edit_remark" class="form-label-modern">Remark</label>
                <textarea class="form-control form-control-modern" id="edit_remark" name="remark" rows="1"></textarea>
              </div>
              <div class="col-md-6">
                  <label for="edit_supplier_id" class="form-label-modern">Supplier</label>
                  <select class="form-select form-control-modern" id="edit_supplier_id" name="supplier_id">
                      <option value="">Select Supplier</option>
                      @foreach($suppliers as $supplier)
                          <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                      @endforeach
                  </select>
              </div>
              <div class="col-md-6">
                <label for="edit_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="edit_status" name="status" required>
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->name }}">{{ $status->name }}</option>
                    @endforeach
                </select>
              </div>
          </div>

          <div class="mt-4" id="editCustomFieldsSection" style="display:none;">
               <h6 style="color: #434AFA; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Category Specific Fields</h6>
               <div id="edit_custom_fields_container" class="row g-3">
                   <!-- Custom Fields -->
               </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center">
            <i class="bi bi-check-circle"></i>
            Update Asset
          </button>
        </div>
      </form>
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

// Pagination & Range
function updateRangeInfo(from, to, total) {
    const $info = $('#assetRangeInfo');
    if (!$info.length) return;
    const totalValue = Number(total) || 0;
    const startValue = Number(from) || 0;
    const endValue = Number(to) || 0;
    $info.text(`Showing ${startValue}-${endValue} from ${totalValue} data`);
}

function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}"><i class="bi bi-chevron-left"></i> Previous</a>
        </li>
        <li class="page-item active"><span class="page-link">${current} / ${last}</span></li>
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">Next <i class="bi bi-chevron-right"></i></a>
        </li>
    `);
}

$(function () {
  let searchTimeout;
  loadAssets();

  // Load Assets Table
  function loadAssets(page = 1) {
    let search = $('#search').val();
    
    $('#assetsTable tbody').html(`
      <tr><td colspan="4" class="loading-state"><i class="bi bi-arrow-repeat spin"></i><p class="mt-2 mb-0">Loading assets...</p></td></tr>
    `);
    
    $.get(`{{ route('assets.fetch') }}?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#assetsTable tbody').html(`
          <tr>
            <td colspan="4" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Assets Found</h5>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, asset) {
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${asset.asset_id}</strong></td>
            <td>${asset.remark ? (asset.remark.length > 50 ? asset.remark.substring(0, 50) + '...' : asset.remark) : '-'}</td>
            <td>${asset.category ? asset.category.name : '-'}</td>
            <td>${asset.supplier ? asset.supplier.name : '-'}</td>
            <td><span class="badge bg-light text-dark border">${asset.status}</span></td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit editBtn" data-id="${asset.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${asset.id}" title="Delete"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#assetsTable tbody').html(rows);
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
    });
  }

  // Pagination Actions
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadAssets(page);
  });
  
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadAssets(1), 300);
  });




  // Render Custom Fields
  function renderCustomFields(categoryId, containerId, sectionId, existingData = null) {
      const $container = $('#' + containerId);
      const $section = $('#' + sectionId);
      
      $container.html('<div class="col-12 text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Loading fields...</div>');
      $section.show();
      
      $.get(`/asset-category/${categoryId}`, function(data) {
           $container.empty();
           if(data.fields && data.fields.length > 0) {
               data.fields.forEach(field => {
                   let value = '';
                   if (existingData && existingData[field.name]) {
                       value = existingData[field.name];
                   }
                   
                   let inputHtml = '';
                   if (field.type === 'dropdown') {
                       let optionsHtml = '<option value="">Select option</option>';
                       if (field.options && field.options.length) {
                           field.options.forEach(opt => {
                               const selected = value == opt ? 'selected' : '';
                               optionsHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                           });
                       }
                       inputHtml = `
                           <select class="form-select form-control-modern" name="custom_fields[${field.name}]">
                               ${optionsHtml}
                           </select>
                       `;
                   } else {
                       inputHtml = `<input type="text" class="form-control form-control-modern" name="custom_fields[${field.name}]" value="${value}">`;
                   }
                   
                   const fieldHtml = `
                       <div class="col-md-6">
                           <label class="form-label-modern">${field.name}</label>
                           ${inputHtml}
                       </div>
                   `;
                   $container.append(fieldHtml);
               });
           } else {
               $container.html('<div class="col-12 text-muted small">No custom fields for this category.</div>');
               // Optionally hide section if truly empty
           }
      });
  }

  // Create Modal Category Change
  $('#asset_category_id').change(function() {
      const catId = $(this).val();
      if(catId) {
          renderCustomFields(catId, 'create_custom_fields_container', 'customFieldsSection');
      } else {
          $('#customFieldsSection').hide();
          $('#create_custom_fields_container').empty();
      }
  });

  // Edit Modal Category Change (if user changes category during edit)
  $('#edit_asset_category_id').change(function() {
      const catId = $(this).val();
      if(catId) {
          // Warning: this clears existing custom values if category changes!
          renderCustomFields(catId, 'edit_custom_fields_container', 'editCustomFieldsSection');
      } else {
          $('#editCustomFieldsSection').hide();
          $('#edit_custom_fields_container').empty();
      }
  });

  // Store Asset
  $('#createAssetForm').submit(function(e) {
      e.preventDefault();
      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
      
      // Collect form data (including custom fields automatically via name="custom_fields[key]")
      // Serialize handles nested arrays properly in format custom_fields[key]=val
      
      $.ajax({
          url: "{{ route('assets.store') }}",
          type: "POST",
          data: $(this).serialize(),
          success: function() {
              $('#createAssetModal').modal('hide');
              $('#createAssetForm')[0].reset();
              $('#customFieldsSection').hide();
              loadAssets();
              showAlert('success', 'Asset created successfully');
          },
          error: function(xhr) {
              let msg = 'Error creating asset';
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
              showAlert('danger', msg);
          },
          complete: function() {
              $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Asset');
          }
      });
  });

   // Edit Button Click
   $(document).on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get(`/assets/${id}`, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_asset_id').val(data.asset_id);
            $('#edit_remark').val(data.remark);
            $('#edit_asset_category_id').val(data.asset_category_id);
            $('#edit_asset_type_id').val(data.asset_type_id);
            
            $('#edit_status').val(data.status);
            $('#edit_supplier_id').val(data.supplier_id);
            
            // Load fields with existing data
            renderCustomFields(data.asset_category_id, 'edit_custom_fields_container', 'editCustomFieldsSection', data.custom_fields_data);
            
            $('#editAssetModal').modal('show');
        });
   });
  
  // Update Asset
  $('#editAssetForm').submit(function(e) {
      e.preventDefault();
      const id = $('#edit_id').val();
      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
      
      $.ajax({
          url: `/assets/${id}`,
          type: "PUT",
          data: $(this).serialize(),
          success: function() {
              $('#editAssetModal').modal('hide');
              loadAssets();
              showAlert('success', 'Asset updated successfully');
          },
          error: function(xhr) {
               let msg = 'Error updating asset';
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
              showAlert('danger', msg);
          },
          complete: function() {
              $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Asset');
          }
      });
  });

  // Delete Asset
  $(document).on('click', '.deleteBtn', function() {
      if(confirm('Are you sure you want to delete this asset?')) {
          const id = $(this).data('id');
          $.ajax({
              url: `/assets/${id}`,
              type: 'DELETE',
              data: { _token: '{{ csrf_token() }}' },
              success: function() {
                  loadAssets();
                  showAlert('success', 'Asset deleted successfully');
              },
              error: function() {
                  showAlert('danger', 'Error deleting asset');
              }
          });
      }
  });

});
</script>
@endpush
