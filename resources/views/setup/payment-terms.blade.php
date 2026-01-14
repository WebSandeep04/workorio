@extends('layouts.app')

@section('title', 'Payment Terms Management')
@section('page_title', 'Payment Terms Management')

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
  
  .btn-action-warning {
    background: #ffc107 !important;
    color: white;
    border-radius: 4px;
  }

  .btn-action-success {
    background: #28a745 !important;
    color: white;
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
  
  .btn-modern-danger {
    background: #dc3545;
    color: white;
  }
  
  .btn-modern-danger:hover {
    background: #b02a37;
    color: white;
  }
  
  .badge-modern-success {
      background: #d1fae5;
      color: #065f46;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.75rem;
  }
  
  .badge-modern-secondary {
      background: #f3f4f6;
      color: #4b5563;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.75rem;
  }
  
  .badge-modern-primary {
     background: #e0e7ff;
     color: #3730a3;
     padding: 0.35rem 0.75rem;
     border-radius: 999px;
     font-weight: 600;
     font-size: 0.75rem;
  }
  
  .badge-modern-info {
     background: #e0f2fe;
     color: #0369a1;
     padding: 0.35rem 0.75rem;
     border-radius: 999px;
     font-weight: 600;
     font-size: 0.75rem;
  }
  
  .badge-modern-warning {
     background: #fef3c7;
     color: #92400e;
     padding: 0.35rem 0.75rem;
     border-radius: 999px;
     font-weight: 600;
     font-size: 0.75rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search payment terms..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createPaymentTermModal">
      <i class="bi bi-plus me-1"></i>Create Payment Terms
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="paymentTermsTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Advance %</th>
              <th>Design & Dev %</th>
              <th>Completion %</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading payment terms...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="paymentRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Payment Terms Modal -->
<div class="modal fade modal-modern" id="createPaymentTermModal" tabindex="-1" aria-labelledby="createPaymentTermModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createPaymentTermModalLabel">
          <i class="bi bi-plus text-white"></i>
          Create Payment Terms
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createPaymentTermForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <label for="name" class="form-label-modern">Payment Terms Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="name" name="name" required placeholder="Enter name">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label-modern">Description</label>
            <textarea class="form-control form-control-modern" id="description" name="description" rows="2" placeholder="Enter description"></textarea>
          </div>
          
          <h6 class="mb-3 text-primary"><strong>Payment Percentages (Must total 100%)</strong></h6>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="advance_percentage" class="form-label-modern">Advance on Project <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="advance_percentage" name="advance_percentage" min="0" max="100" value="50" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="design_dev_percentage" class="form-label-modern">Design & Dev Approval <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="design_dev_percentage" name="design_dev_percentage" min="0" max="100" value="30" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="completion_percentage" class="form-label-modern">Completion <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="completion_percentage" name="completion_percentage" min="0" max="100" value="20" required>
              </div>
            </div>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
            <input type="hidden" name="is_active" value="0">
            <label class="form-check-label" for="is_active">
              Active
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Create Payment Terms
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Payment Terms Modal -->
<div class="modal fade modal-modern" id="editPaymentTermModal" tabindex="-1" aria-labelledby="editPaymentTermModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="editPaymentTermModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Payment Terms
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPaymentTermForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_id" name="id">
          <div class="mb-3">
            <label for="edit_name" class="form-label-modern">Payment Terms Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label-modern">Description</label>
            <textarea class="form-control form-control-modern" id="edit_description" name="description" rows="2"></textarea>
          </div>
          
          <h6 class="mb-3 text-primary"><strong>Payment Percentages (Must total 100%)</strong></h6>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label for="edit_advance_percentage" class="form-label-modern">Advance on Project <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="edit_advance_percentage" name="advance_percentage" min="0" max="100" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="edit_design_dev_percentage" class="form-label-modern">Design & Dev Approval <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="edit_design_dev_percentage" name="design_dev_percentage" min="0" max="100" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="edit_completion_percentage" class="form-label-modern">Completion <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="edit_completion_percentage" name="completion_percentage" min="0" max="100" required>
              </div>
            </div>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active" value="1">
            <input type="hidden" name="is_active" value="0">
            <label class="form-check-label" for="edit_is_active">
              Active
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Update Payment Terms
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-modern" id="deletePaymentTermModal" tabindex="-1" aria-labelledby="deletePaymentTermModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="deletePaymentTermModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this payment terms? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-modern btn-modern-danger" id="confirmDelete">
          <i class="bi bi-trash"></i>
          Delete
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

// Build compact pagination
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
    // Current
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
    const $info = $('#paymentRangeInfo');
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

$(document).ready(function() {
    let searchTimeout;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadPaymentTerms();

    function loadPaymentTerms(page = 1) {
        let search = $('#search').val();
        
        $('#paymentTermsTable tbody').html(`
          <tr>
            <td colspan="7" class="loading-state">
              <i class="bi bi-arrow-repeat spin"></i>
              <p class="mt-2 mb-0">Loading payment terms...</p>
            </td>
          </tr>
        `);
        
        $.ajax({
            url: `{{ route("payment-terms.fetch") }}?page=${page}&search=${search}`,
            method: 'GET',
            success: function(response) {
                // If response is array (old format) or paginated object (new format)
                const dataList = response.data || response;
                const pagination = response.current_page ? response : null;

                if (!dataList || dataList.length === 0) {
                    $('#paymentTermsTable tbody').html(`
                      <tr>
                        <td colspan="7" class="empty-state">
                          <i class="bi bi-inbox"></i>
                          <h5>No Payment Terms Found</h5>
                          <p>Get started by creating your first payment terms.</p>
                        </td>
                      </tr>
                    `);
                    $('#paginationLinks').empty();
                    updateRangeInfo(0, 0, 0);
                    return;
                }
                
                const tbody = $('#paymentTermsTable tbody');
                tbody.empty();
                
                let rows = '';
                dataList.forEach(function(term, index) {
                    const statusBadge = term.is_active ? 
                        '<span class="badge badge-modern-success">Active</span>' : 
                        '<span class="badge badge-modern-secondary">Inactive</span>';
                    
                    rows += `
                        <tr style="animation-delay: ${index * 0.1}s;">
                            <td><strong>${term.name}</strong></td>
                            <td>${term.description || '-'}</td>
                            <td><span class="badge badge-modern-primary">${term.advance_percentage}%</span></td>
                            <td><span class="badge badge-modern-info">${term.design_dev_percentage}%</span></td>
                            <td><span class="badge badge-modern-warning">${term.completion_percentage}%</span></td>
                            <td>${statusBadge}</td>
                            <td>
                              <div class="d-flex gap-2 justify-content-center">
                                <button class="btn-action btn-action-edit" onclick="editPaymentTerm(${term.id})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action ${term.is_active ? 'btn-action-warning' : 'btn-action-success'}" onclick="toggleStatus(${term.id})" title="${term.is_active ? 'Deactivate' : 'Activate'}">
                                    <i class="bi bi-${term.is_active ? 'pause' : 'play'}"></i>
                                </button>
                                <button class="btn-action btn-action-delete" onclick="deletePaymentTerm(${term.id})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                              </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.html(rows);

                if (pagination) {
                    buildSimplePagination($('#paginationLinks'), pagination.current_page || 1, pagination.last_page || 1);
                    updateRangeInfo(pagination.from, pagination.to, pagination.total);
                } else {
                     $('#paginationLinks').empty();
                     updateRangeInfo(1, dataList.length, dataList.length);
                }
            },
            error: function() {
                $('#paymentTermsTable tbody').html(`
                  <tr>
                    <td colspan="7" class="text-danger text-center py-4">
                      <i class="bi bi-exclamation-triangle"></i>
                      Failed to load payment terms. Please try again.
                    </td>
                  </tr>
                `);
            }
        });
    }

    // Pagination click
    $(document).on('click', '#paginationLinks .page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadPaymentTerms(page);
        }
    });

    // Search input
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadPaymentTerms(1);
        }, 300);
    });

    // Close modals when clicking outside
    $(document).on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            $('.modal').modal('hide');
        }
    });

    $('#createPaymentTermForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');
        
        $.ajax({
            url: '{{ route("payment-terms.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#createPaymentTermModal').modal('hide');
                    $('#createPaymentTermForm')[0].reset();
                    loadPaymentTerms();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Validation failed:\n';
                    for (let field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                    showAlert('error', errorMessage);
                } else {
                    showAlert('error', 'An error occurred while creating the payment terms.');
                }
            },
            always: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Create Payment Terms');
            }
        });
    });

    $('#editPaymentTermForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
        
        const id = $('#edit_id').val();
        
        $.ajax({
            url: '{{ route("payment-terms.update", ":id") }}'.replace(':id', id),
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#editPaymentTermModal').modal('hide');
                    loadPaymentTerms();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'Validation failed:\n';
                    for (let field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                    showAlert('error', errorMessage);
                } else {
                    showAlert('error', 'An error occurred while updating the payment terms.');
                }
            },
            always: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Payment Terms');
            }
        });
    });

    $('#confirmDelete').on('click', function() {
        const id = $(this).data('id');
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');
        
        $.ajax({
            url: '{{ route("payment-terms.destroy", ":id") }}'.replace(':id', id),
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#deletePaymentTermModal').modal('hide');
                    loadPaymentTerms();
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'An error occurred while deleting the payment terms.');
            },
            always: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
            }
        });
    });
});

// Exposed functions for onclick events
window.editPaymentTerm = function(id) {
    $.ajax({
        url: '{{ route("payment-terms.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            $('#edit_id').val(response.id);
            $('#edit_name').val(response.name);
            $('#edit_description').val(response.description);
            $('#edit_advance_percentage').val(response.advance_percentage);
            $('#edit_design_dev_percentage').val(response.design_dev_percentage);
            $('#edit_completion_percentage').val(response.completion_percentage);
            $('#edit_is_active').prop('checked', response.is_active);
            
            $('#editPaymentTermModal').modal('show');
        },
        error: function() {
            showAlert('error', 'Failed to load payment terms details.');
        }
    });
};

window.deletePaymentTerm = function(id) {
    $('#confirmDelete').data('id', id);
    $('#deletePaymentTermModal').modal('show');
};

window.toggleStatus = function(id) {
    $.ajax({
        url: '{{ route("payment-terms.toggle-status", ":id") }}'.replace(':id', id),
        method: 'PATCH',
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                // Reload current page if possible, or just default load
                // Accessing closure variable from here is tricky, but simplest is just triggering a reload
                // For now, simple re-trigger of load logic:
                // We don't have direct access to 'loadPaymentTerms' as it's inside doc.ready...
                // Wait, I need to expose loadPaymentTerms or signal it.
                // Best practice: move loadPaymentTerms outside or trigger a custom event.
                // But since I'm rewriting the file, I'll just expose it or reload the page.
                
                // Better yet, I'll redefine loadPaymentTerms globally or access the button's context
                // Let's just reload the page content by triggering the search inputs logic again? 
                // Or just:
                location.reload(); // Simplest fallback given structure, but let's try to be better.
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'Failed to update status.');
        }
    });
};
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
