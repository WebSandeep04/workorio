@extends('layouts.app')

@push('styles')
<style>
  /* Base Styles */
  body {
    background-color: #fca5a5; /* Use layout background */
    font-family: 'Montserrat', sans-serif;
  }

  .container-fluid {
    max-width: 1400px;
  }

  /* Table Search & Actions */
  .table-search {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0px; 
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

  /* Dashboard Cards CSS */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.4rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    width: 100%;
    min-height: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  
  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }
  
  .summary-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    line-height: 1.1;
  }

  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    color: #101828;
    line-height: 1;
  }

  /* Filter Box CSS */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 5px;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
  }

  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 10px;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    font-size: 10px;
    width: 100%;
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
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <i class="bi bi-box-seam text-white"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Assets</div>
        <div class="summary-card-value" id="totalAssets">0</div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-emerald">
        <i class="bi bi-check-circle text-white"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Available</div>
        <div class="summary-card-value" id="availableAssets">0</div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-sky">
        <i class="bi bi-person-check text-white"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Assigned</div>
        <div class="summary-card-value" id="assignedAssets">0</div>
      </div>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-rose">
        <i class="bi bi-exclamation-triangle text-white"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Return Due</div>
        <div class="summary-card-value" id="returnDue">0</div>
      </div>
    </div>
  </div>

   <!-- Filters -->
   <div class="filterBox d-flex justify-content-between align-items-center mb-4 gap-3">
       <div class="d-flex flex-column flex-grow-1">
           <label class="form-label-modern">Employee</label>
           <select class="form-control-modern w-100 arrow-white" id="filter_employee_id">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                     <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                 @endforeach
           </select>
       </div>
       <div class="d-flex flex-column flex-grow-1">
           <label class="form-label-modern">Category</label>
           <select class="form-control-modern w-100 arrow-white" id="filter_category_id">
               <option value="">All Categories</option>
               @foreach($categories as $category)
                   <option value="{{ $category->id }}">{{ $category->name }}</option>
               @endforeach
           </select>
       </div>
       <div class="d-flex flex-column flex-grow-1">
           <label class="form-label-modern">From Date</label>
           <input type="date" class="form-control-modern w-100" id="filter_from_date">
       </div>
       <div class="d-flex flex-column flex-grow-1">
           <label class="form-label-modern">To Date</label>
           <input type="date" class="form-control-modern w-100" id="filter_to_date">
       </div>
   </div>

  <!-- View Mode Switch -->
  <div class="d-flex mb-2">
      <div class="btn-group" role="group" aria-label="View Toggle">
          <input type="radio" class="btn-check" name="viewMode" id="viewModeAssignments" value="assignments" checked>
          <label class="btn btn-outline-primary" for="viewModeAssignments" style="padding: 0.15rem 0.5rem; font-size: 0.75rem;">Assignments</label>

          <input type="radio" class="btn-check" name="viewMode" id="viewModeAssets" value="assets">
          <label class="btn btn-outline-primary" for="viewModeAssets" style="padding: 0.15rem 0.5rem; font-size: 0.75rem;">Assets List</label>

          <input type="radio" class="btn-check" name="viewMode" id="viewModeUser" value="user_view">
          <label class="btn btn-outline-primary" for="viewModeUser" style="padding: 0.15rem 0.5rem; font-size: 0.75rem;">By User</label>
      </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search assignments..." />
    </div>
    <div class="ms-auto d-flex gap-2">
         <!-- <a href="{{ route('assets.index') }}" class="table-search-btn text-decoration-none" target="_blank">
             <i class="bi bi-folder2-open me-1"></i> Open Assets
         </a> -->
         <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createAssetModal">
             <i class="bi bi-plus-lg me-1"></i> Add Asset
         </button>
         <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
           <i class="bi bi-person-plus me-1"></i> Assign Asset
         </button>
    </div>
  </div>

  <h4 class="mb-2" style="font-size: 10px; font-weight: bold; color: #434AFA; margin-left: 2px;" id="listTitle">ASSET ASSIGNMENT LIST</h4>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <!-- Assignments Table -->
      <div class="table-responsive" id="assignmentsTableContainer">
        <table class="table custom-table" id="assignmentsTable">
          <thead>
            <tr>
              <th>Asset Name</th>
              <th>Asset ID</th>
              <th>Assigned To</th>
              <th>Assigned Date</th>
              <th>Exp. Return Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading assignments...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Assets List Table -->
      <div class="table-responsive" id="assetsListTableContainer" style="display:none;">
        <table class="table custom-table" id="assetsListTable">
          <thead>
            <tr>
              <th>Asset ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
             <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading assets...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Assignments By User Table -->
      <div class="table-responsive" id="assignmentsByUserTableContainer" style="display:none;">
        <table class="table custom-table" id="assignmentsByUserTable">
          <thead>
            <tr>
              <th>Employee Name</th>
              <th>Employee Code</th>
              <th class="text-center">Asset Count (Assigned)</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
             <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading user data...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="assignmentRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade modal-modern" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createAssignmentModalLabel">
          <i class="bi bi-box-seam text-white"></i>
          Assign Asset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAssignmentForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="row g-3">
              <div class="col-md-6">
                <label for="category_id" class="form-label-modern">Asset Category <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="asset_id" class="form-label-modern">Asset <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="asset_id" name="asset_id" required disabled>
                    <option value="">Select Asset</option>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="employee_id" class="form-label-modern">Employee <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="employee_id" name="employee_id" required>
                    <option value="">Select Employee</option>
                     @foreach($employees as $employee)
                          <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_code }})</option>
                      @endforeach
                </select>
              </div>
              
              <div class="col-md-6">
                  <label for="assigned_date" class="form-label-modern">Assigned Date <span class="text-danger">*</span></label>
                  <input type="date" class="form-control form-control-modern" id="assigned_date" name="assigned_date" required value="{{ date('Y-m-d') }}">
              </div>

              <div class="col-md-6">
                  <label for="return_date" class="form-label-modern">Expected Return Date</label>
                  <input type="date" class="form-control form-control-modern" id="return_date" name="return_date">
              </div>
              
               <div class="col-12">
                  <label for="description" class="form-label-modern">Description</label>
                  <textarea class="form-control form-control-modern" id="description" name="description" rows="2"></textarea>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center">
            <i class="bi bi-check-circle"></i>
            Assign Asset
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Create Asset Modal (Added) -->
<div class="modal fade modal-modern" id="createAssetModal" tabindex="-1" aria-labelledby="createAssetModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createAssetModalLabel">
          <i class="bi bi-box-seam text-white"></i>
          Add New Asset
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createAssetForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="row g-3">
              <div class="col-md-6">
                <label for="create_asset_tag" class="form-label-modern">Asset ID / Tag <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="create_asset_tag" name="asset_id" required placeholder="e.g. LP-001">
              </div>
              <div class="col-md-6">
                <label for="create_asset_name" class="form-label-modern">Asset Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="create_asset_name" name="name" required placeholder="e.g. Dell Latitude">
              </div>
              <div class="col-md-6">
                <label for="create_asset_category_id" class="form-label-modern">Category <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="create_asset_category_id" name="asset_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="create_asset_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="create_asset_status" name="status" required>
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->name }}">{{ $status->name }}</option>
                    @endforeach
                </select>
              </div>
          </div>
          
          <div class="mt-4" id="new_asset_custom_fields_section" style="display:none;">
               <h6 style="color: #434AFA; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Category Specific Fields</h6>
               <div id="new_asset_custom_fields_container" class="row g-3">
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

<!-- Edit Asset Modal -->
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
          <input type="hidden" id="edit_asset_pk">
          
          <div class="row g-3">
              <div class="col-md-6">
                <label for="edit_asset_tag" class="form-label-modern">Asset ID / Tag <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="edit_asset_tag" name="asset_id" required>
              </div>
              <div class="col-md-6">
                <label for="edit_asset_name_field" class="form-label-modern">Asset Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="edit_asset_name_field" name="name" required>
              </div>
              <div class="col-md-6">
                <label for="edit_asset_cat_id" class="form-label-modern">Category <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="edit_asset_cat_id" name="asset_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label for="edit_asset_status_field" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-control-modern" id="edit_asset_status_field" name="status" required>
                    <option value="">Select Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->name }}">{{ $status->name }}</option>
                    @endforeach
                </select>
              </div>
          </div>

          <div class="mt-4" id="edit_asset_custom_fields_section" style="display:none;">
               <h6 style="color: #434AFA; border-bottom: 2px solid #f0f0f0; padding-bottom: 0.5rem; margin-bottom: 1rem;">Category Specific Fields</h6>
               <div id="edit_asset_custom_fields_container" class="row g-3">
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

<!-- Edit Modal -->
<div class="modal fade modal-modern" id="editAssignmentModal" tabindex="-1" aria-labelledby="editAssignmentModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Assignment
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAssignmentForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_id">
          
          <div class="row g-3">
               <div class="col-md-6">
                  <label class="form-label-modern">Asset</label>
                  <input type="text" class="form-control form-control-modern" id="edit_asset_name" disabled readonly>
              </div>
              
              <div class="col-md-6">
                  <label for="edit_employee_id" class="form-label-modern">Employee <span class="text-danger">*</span></label>
                  <select class="form-select form-control-modern" id="edit_employee_id" name="employee_id" required>
                      <option value="">Select Employee</option>
                       @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->employee_code }})</option>
                        @endforeach
                  </select>
              </div>
              
              <div class="col-md-6">
                   <label for="edit_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                   <select class="form-select form-control-modern" id="edit_status" name="status" required>
                       <option value="assigned">Assigned</option>
                       <option value="returned">Returned</option>
                   </select>
              </div>

              <div class="col-md-6">
                  <label for="edit_return_date" class="form-label-modern">Expected / Actual Return Date</label>
                  <input type="date" class="form-control form-control-modern" id="edit_return_date" name="return_date">
              </div>

               <div class="col-12">
                  <label for="edit_description" class="form-label-modern">Description</label>
                  <textarea class="form-control form-control-modern" id="edit_description" name="description" rows="2"></textarea>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center">
            <i class="bi bi-check-circle"></i>
            Update Assignment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- User Assets Modal -->
<div class="modal fade modal-modern" id="userAssetsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assigned Assets: <span id="userAssetsModalName"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
          <div class="table-responsive">
            <table class="table custom-table mb-0" id="userAssetsModalTable">
              <thead>
                <tr>
                  <th>Asset Name</th>
                  <th>Asset ID</th>
                  <th>Category</th>
                  <th>Assigned Date</th>
                  <th>Return Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                  <!-- Content -->
              </tbody>
            </table>
          </div>
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

// Pagination & Range
function updateRangeInfo(from, to, total) {
    const $info = $('#assignmentRangeInfo');
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
  loadAssignments();
  loadStats();

  $('#filter_from_date, #filter_to_date, #filter_category_id, #filter_employee_id').change(function() {
      loadAssignments();
      loadStats();
  });



  function loadStats() {
      // Build query params for stats too if we want stats to filter? 
      // User said "add filter from to date filter by users filter by category" - usually filters apply to stats too in dashboards.
      // But user demand "cards to show total assets... whose due date passed".
      // Usually "Total Assets" is global. "Return Due" might be global.
      // But if I filter by Category, Total Assets should probably respect that.
      // I'll pass filters to stats endpoint too.
      
      let queryParams = `?from_date=${$('#filter_from_date').val()}`;
      queryParams += `&to_date=${$('#filter_to_date').val()}`;
      queryParams += `&category_id=${$('#filter_category_id').val()}`;
      queryParams += `&employee_id=${$('#filter_employee_id').val()}`;

      $.get(`{{ route('asset-management.stats') }}${queryParams}`, function(data) {
          $('#totalAssets').text(data.total_assets);
          $('#availableAssets').text(data.available_assets);
          $('#assignedAssets').text(data.assigned_assets);
          $('#returnDue').text(data.return_due); // Using updated ID
      });
  }

  // Load Assignments Table
  function loadAssignments(page = 1) {
    let search = $('#search').val();
    
    $('#assignmentsTable tbody').html(`
      <tr><td colspan="7" class="loading-state"><i class="bi bi-arrow-repeat spin"></i><p class="mt-2 mb-0">Loading assignments...</p></td></tr>
    `);
    
    let queryParams = `page=${page}&search=${search}`;
    queryParams += `&from_date=${$('#filter_from_date').val()}`;
    queryParams += `&to_date=${$('#filter_to_date').val()}`;
    queryParams += `&category_id=${$('#filter_category_id').val()}`;
    queryParams += `&employee_id=${$('#filter_employee_id').val()}`;

    $.get(`{{ route('asset-management.fetch') }}?${queryParams}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#assignmentsTable tbody').html(`
          <tr>
            <td colspan="7" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Assignments Found</h5>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data.data, function (i, assignment) {
        let statusBadge = assignment.status === 'assigned' 
                ? '<span class="badge bg-primary">Assigned</span>' 
                : '<span class="badge bg-success">Returned</span>';
        
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td>${assignment.asset ? assignment.asset.name : '-'}</td>
            <td><strong>${assignment.asset ? assignment.asset.asset_id : '-'}</strong></td>
            <td>${assignment.employee ? assignment.employee.name : '-'}</td>
            <td>${assignment.assigned_date}</td>
            <td>${assignment.return_date ? assignment.return_date : '-'}</td>
            <td>${statusBadge}</td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit editBtn" data-id="${assignment.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${assignment.id}" title="Delete"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#assignmentsTable tbody').html(rows);
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
    });
  }

  // Pagination Actions
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadAssignments(page);
  });
  
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadAssignments(1), 300);
  });
  
  // Category Change Handler - Load Assets
  $('#category_id').change(function() {
      const categoryId = $(this).val();
      const $assetSelect = $('#asset_id');
      
      $assetSelect.html('<option value="">Loading...</option>').prop('disabled', true);
      
      if(categoryId) {
          $.get("{{ route('asset-management.get-assets') }}", { category_id: categoryId }, function(data) {
             let options = '<option value="">Select Asset</option>';
             if(data.length > 0) {
                 data.forEach(asset => {
                     options += `<option value="${asset.id}">${asset.name} (${asset.asset_id})</option>`;
                 });
                 $assetSelect.html(options).prop('disabled', false);
             } else {
                 $assetSelect.html('<option value="">No available assets</option>').prop('disabled', true);
             }
          });
      } else {
           $assetSelect.html('<option value="">Select Asset</option>').prop('disabled', true);
      }
  });

  // Store Assignment
  $('#createAssignmentForm').submit(function(e) {
      e.preventDefault();
      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Assigning...');
      
      $.ajax({
          url: "{{ route('asset-management.store') }}",
          type: "POST",
          data: $(this).serialize(),
          success: function() {
              $('#createAssignmentModal').modal('hide');
              $('#createAssignmentForm')[0].reset();
              loadAssignments();
              showAlert('success', 'Asset assigned successfully');
          },
          error: function(xhr) {
              let msg = 'Error assigning asset';
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
              showAlert('danger', msg);
          },
          complete: function() {
              $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Assign Asset');
          }
      });
  });

   // Edit Button Click
   $(document).on('click', '.editBtn', function() {
        const id = $(this).data('id');
        $.get(`/asset-management/${id}`, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_asset_name').val(data.asset ? data.asset.name : 'Unknown');
            $('#edit_employee_id').val(data.employee_id);
            $('#edit_status').val(data.status);
            $('#edit_return_date').val(data.return_date || '');
            $('#edit_description').val(data.description || '');
            
            if(data.status === 'returned') {
                 // Logic if already returned
            }
            
            $('#editAssignmentModal').modal('show');
        });
  });

   function loadAssignmentsByUser(page = 1) {
      let search = $('#search').val();
      let categoryId = $('#filter_category_id').val();
      let employeeId = $('#filter_employee_id').val();
      let fromDate = $('#filter_from_date').val();
      let toDate = $('#filter_to_date').val();

      $('#assignmentsByUserTable tbody').html('<tr><td colspan="4" class="loading-state"><i class="bi bi-arrow-repeat spin"></i><p class="mt-2 mb-0">Loading user data...</p></td></tr>');
      
      let q = `page=${page}&search=${search}&group_by=user`;
      if(categoryId) q += `&category_id=${categoryId}`;
      if(employeeId) q += `&employee_id=${employeeId}`;
      if(fromDate) q += `&from_date=${fromDate}`;
      if(toDate) q += `&to_date=${toDate}`;

      // Use asset-management/fetch URL directly or verify route name
      $.get(`asset-management/fetch?${q}`, function(data) {
           if (!data.data || data.data.length === 0) {
              $('#assignmentsByUserTable tbody').html('<tr><td colspan="4" class="empty-state"><i class="bi bi-inbox"></i><h5>No Assignments Found</h5></td></tr>');
              if(page === 1) $('#paginationLinks').empty();
              return;
           }
           let rows = '';
           $.each(data.data, function(i, item) {
               const empName = item.employee ? item.employee.name : 'Unknown';
               const empCode = item.employee ? (item.employee.employee_code || '-') : '-';
               const count = item.asset_count;
               
               rows += `
                   <tr style="animation-delay: ${i * 0.1}s;">
                       <td>
                           <div class="d-flex align-items-center">
                               <div class="avatar-circle me-2 bg-primary text-white" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:0.8rem;">${empName.charAt(0)}</div>
                               <div><div class="fw-bold">${empName}</div></div>
                           </div>
                       </td>
                       <td>${empCode}</td>
                       <td class="text-center"><span class="badge bg-primary rounded-pill">${count}</span></td>
                       <td class="text-center">
                           <button class="btn btn-sm btn-outline-primary viewUserAssetsBtn" data-id="${item.employee_id}" data-name="${empName}">
                               <i class="bi bi-eye me-1"></i> View Details
                           </button>
                       </td>
                   </tr>
               `;
           });
           $('#assignmentsByUserTable tbody').html(rows);
           buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      });
   }
   
   $(document).on('click', '.viewUserAssetsBtn', function() {
       const empId = $(this).data('id');
       const empName = $(this).data('name');
       $('#userAssetsModalName').text(empName);
       $('#userAssetsModalTable tbody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>');
       $('#userAssetsModal').modal('show');
       
       $.get(`asset-management/fetch?employee_id=${empId}&per_page=100`, function(data) {
             let rows = '';
             if(!data.data || data.data.length === 0) {
                 rows = '<tr><td colspan="6" class="text-center text-muted">No assignments found.</td></tr>';
             } else {
                 $.each(data.data, function(i, assign) {
                     const assetName = assign.asset ? assign.asset.name : '-';
                     const assetCode = assign.asset ? assign.asset.asset_id : '-';
                     const cat = assign.asset && assign.asset.category ? assign.asset.category.name : '-';
                     
                     rows += `
                        <tr>
                            <td>${assetName}</td>
                            <td>${assetCode}</td>
                            <td>${cat}</td>
                            <td>${assign.assigned_date}</td>
                            <td>${assign.return_date || '-'}</td>
                            <td><span class="badge bg-light text-dark border">${assign.status}</span></td>
                        </tr>
                     `;
                 });
             }
             $('#userAssetsModalTable tbody').html(rows);
       });
   });
   
   $('#edit_status').change(function() {
      if($(this).val() === 'returned') {
           // If returning, maybe force a date check? 
           // For now, keeping it simple as per user request to just show the field.
      }
   });
  
  // Update Assignment
  $('#editAssignmentForm').submit(function(e) {
      e.preventDefault();
      const id = $('#edit_id').val();
      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
      
      $.ajax({
          url: `/asset-management/${id}`,
          type: "PUT",
          data: $(this).serialize(),
          success: function() {
              $('#editAssignmentModal').modal('hide');
              loadAssignments();
              showAlert('success', 'Assignment updated successfully');
          },
          error: function(xhr) {
               let msg = 'Error updating assignment';
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              if(xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join("\n");
              showAlert('danger', msg);
          },
          complete: function() {
              $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Assignment');
          }
      });
  });

  // Delete Assignment
  $(document).on('click', '.deleteBtn', function() {
      if(confirm('Are you sure you want to delete this assignment record? This will mark the asset as available again.')) {
          const id = $(this).data('id');
          $.ajax({
              url: `/asset-management/${id}`,
              type: "DELETE",
              data: {
                  _token: '{{ csrf_token() }}'
              },
              success: function() {
                  loadAssignments();
                  showAlert('success', 'Assignment deleted successfully');
              },
              error: function() {
                  showAlert('danger', 'Error deleting assignment');
              }
          });
      }
  });

  // --- Add Asset Logic ---
  function renderNewAssetCustomFields(categoryId, containerId, sectionId) {
      const $container = $('#' + containerId);
      const $section = $('#' + sectionId);
      
      $container.html('<div class="col-12 text-center text-muted"><span class="spinner-border spinner-border-sm"></span> Loading fields...</div>');
      $section.show();
      
      $.get(`/asset-category/${categoryId}`, function(data) {
           $container.empty();
           if(data.fields && data.fields.length > 0) {
               data.fields.forEach(field => {
                   let inputHtml = '';
                   if (field.type === 'dropdown') {
                       let optionsHtml = '<option value="">Select option</option>';
                       if (field.options && field.options.length) {
                           field.options.forEach(opt => {
                               optionsHtml += `<option value="${opt}">${opt}</option>`;
                           });
                       }
                       inputHtml = `
                           <select class="form-select form-control-modern" name="custom_fields[${field.name}]">
                               ${optionsHtml}
                           </select>
                       `;
                   } else {
                       inputHtml = `<input type="text" class="form-control form-control-modern" name="custom_fields[${field.name}]">`;
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
           }
      });
  }

  $('#create_asset_category_id').change(function() {
      const catId = $(this).val();
      if(catId) {
          renderNewAssetCustomFields(catId, 'new_asset_custom_fields_container', 'new_asset_custom_fields_section');
      } else {
          $('#new_asset_custom_fields_section').hide();
          $('#new_asset_custom_fields_container').empty();
      }
  });

  $('#createAssetForm').submit(function(e) {
      e.preventDefault();
      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
      
      $.ajax({
          url: "{{ route('assets.store') }}",
          type: "POST",
          data: $(this).serialize(),
          success: function() {
              $('#createAssetModal').modal('hide');
              $('#createAssetForm')[0].reset();
              $('#new_asset_custom_fields_section').hide();
              showAlert('success', 'Asset created successfully. You can now assign it.');
              loadStats(); 
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

  // --- View Switching & Assets List Logic ---
  $('input[name="viewMode"]').change(function() {
      const mode = $(this).val();
      $('#assignmentsTableContainer, #assetsListTableContainer, #assignmentsByUserTableContainer').hide();
      
      if(mode === 'assignments') {
          $('#assignmentsTableContainer').show();
          $('#listTitle').text('ASSET ASSIGNMENT LIST');
          $('.table-search-btn[data-bs-target="#createAssignmentModal"]').show();
          loadAssignments();
      } else if (mode === 'user_view') {
          $('#assignmentsByUserTableContainer').show();
          $('#listTitle').text('ASSIGNMENTS BY USER');
          $('.table-search-btn[data-bs-target="#createAssignmentModal"]').show();
          loadAssignmentsByUser();
      } else {
          $('#assetsListTableContainer').show();
          $('#listTitle').text('ALL ASSETS LIST');
          $('.table-search-btn[data-bs-target="#createAssignmentModal"]').hide(); 
          loadAssetsList();
      }
  });

  $('#filter_from_date, #filter_to_date, #filter_category_id, #filter_employee_id').off('change').change(function() {
      const mode = $('input[name="viewMode"]:checked').val();
      if(mode === 'assignments') loadAssignments();
      else if (mode === 'user_view') loadAssignmentsByUser();
      else loadAssetsList();
      loadStats();
  });

  $('#search').off('keyup').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
          const mode = $('input[name="viewMode"]:checked').val();
          if(mode === 'assignments') loadAssignments(1);
          else if (mode === 'user_view') loadAssignmentsByUser(1);
          else loadAssetsList(1);
      }, 300);
  });

  $(document).off('click', '#paginationLinks .page-link').on('click', '#paginationLinks .page-link', function(e) {
       e.preventDefault();
       const page = $(this).data('page');
       if(!page) return;
       const mode = $('input[name="viewMode"]:checked').val();
       if(mode === 'assignments') loadAssignments(page);
       else if (mode === 'user_view') loadAssignmentsByUser(page);
       else loadAssetsList(page);
   });

  function loadAssetsList(page = 1) {
      let search = $('#search').val();
      let categoryId = $('#filter_category_id').val();
      
      $('#assetsListTable tbody').html('<tr><td colspan="5" class="loading-state"><i class="bi bi-arrow-repeat spin"></i><p class="mt-2 mb-0">Loading assets...</p></td></tr>');
      
      let q = `page=${page}&search=${search}`;
      if(categoryId) q += `&category_id=${categoryId}`; 

      $.get(`{{ route('assets.fetch') }}?${q}`, function(data) {
           if (!data.data || data.data.length === 0) {
              $('#assetsListTable tbody').html('<tr><td colspan="5" class="empty-state"><i class="bi bi-inbox"></i><h5>No Assets Found</h5></td></tr>');
              if(page === 1) $('#paginationLinks').empty();
              return;
           }
           let rows = '';
           $.each(data.data, function(i, asset) {
               rows += `
                   <tr style="animation-delay: ${i * 0.1}s;">
                       <td><strong>${asset.asset_id}</strong></td>
                       <td>${asset.name}</td>
                       <td>${asset.category ? asset.category.name : '-'}</td>
                       <td><span class="badge bg-light text-dark border">${asset.status}</span></td>
                       <td>
                           <div class="d-flex gap-2 justify-content-center">
                               <button class="btn-action btn-action-edit editAssetBtn" data-id="${asset.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                               <button class="btn-action btn-action-delete deleteAssetBtn" data-id="${asset.id}" title="Delete"><i class="bi bi-trash"></i></button>
                           </div>
                       </td>
                   </tr>
               `;
           });
           $('#assetsListTable tbody').html(rows);
           buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      });
  }

  $(document).on('click', '.editAssetBtn', function() {
      const id = $(this).data('id');
      $.get(`/assets/${id}`, function(data) {
          $('#edit_asset_pk').val(data.id);
          $('#edit_asset_tag').val(data.asset_id);
          $('#edit_asset_name_field').val(data.name);
          $('#edit_asset_cat_id').val(data.asset_category_id);
          $('#edit_asset_status_field').val(data.status);
          renderEditAssetCustomFields(data.asset_category_id, 'edit_asset_custom_fields_container', 'edit_asset_custom_fields_section', data.custom_fields_data);
          $('#editAssetModal').modal('show');
      });
  });
  
  function renderEditAssetCustomFields(categoryId, containerId, sectionId, existingData = null) {
      const $container = $('#' + containerId);
      const $section = $('#' + sectionId);
      $container.html('<div class="col-12 text-center text-muted">Loading fields...</div>');
      $section.show();
      $.get(`/asset-category/${categoryId}`, function(data) {
           $container.empty();
           if(data.fields && data.fields.length > 0) {
               data.fields.forEach(field => {
                   let value = '';
                   if (existingData && existingData[field.name]) value = existingData[field.name];
                   
                   let inputHtml = '';
                   if (field.type === 'dropdown') {
                       let optionsHtml = '<option value="">Select option</option>';
                       if (field.options) {
                           field.options.forEach(opt => {
                               const selected = value == opt ? 'selected' : '';
                               optionsHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                           });
                       }
                       inputHtml = `<select class="form-select form-control-modern" name="custom_fields[${field.name}]">${optionsHtml}</select>`;
                   } else {
                       inputHtml = `<input type="text" class="form-control form-control-modern" name="custom_fields[${field.name}]" value="${value}">`;
                   }
                   $container.append(`<div class="col-md-6"><label class="form-label-modern">${field.name}</label>${inputHtml}</div>`);
               });
           } else {
               $container.html('<div class="col-12 text-muted small">No custom fields.</div>');
           }
      });
  }
  
  $('#edit_asset_cat_id').change(function() {
      const catId = $(this).val();
      if(catId) renderEditAssetCustomFields(catId, 'edit_asset_custom_fields_container', 'edit_asset_custom_fields_section');
      else { $('#edit_asset_custom_fields_section').hide(); $('#edit_asset_custom_fields_container').empty(); }
  });
  
  $('#editAssetForm').submit(function(e) {
      e.preventDefault();
      const id = $('#edit_asset_pk').val();
       const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).html('Updating...');
      $.ajax({
          url: `/assets/${id}`,
          type: "PUT",
          data: $(this).serialize(),
          success: function() {
              $('#editAssetModal').modal('hide');
              loadAssetsList();
              showAlert('success', 'Asset updated.');
          },
          error: function(xhr) { 
              let msg = 'Error updating asset'; 
              if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              showAlert('danger', msg); 
          },
          complete: function() { $btn.prop('disabled', false).html('Update Asset'); }
      });
  });

  $(document).on('click', '.deleteAssetBtn', function() {
      if(confirm('Are you sure you want to delete this asset?')) {
          const id = $(this).data('id');
          $.ajax({
              url: `/assets/${id}`,
              type: 'DELETE',
              data: { _token: '{{ csrf_token() }}' },
              success: function() {
                  loadAssetsList();
                  showAlert('success', 'Asset deleted.');
              },
              error: function() { showAlert('danger', 'Error deleting asset'); }
          });
      }
  });

});
</script>
@endpush
