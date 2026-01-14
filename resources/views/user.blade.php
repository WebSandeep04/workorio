@extends('layouts.app')

@section('title', 'User Management')
@section('page_title', 'User Management')

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
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-select-modern {
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
  
  .badge-modern-info {
      background: #e0f2fe;
      color: #0369a1;
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
      <input type="text" id="search" placeholder="Search users..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createUserModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="usersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
              <th>Manager</th>
              <th>Worklog</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading users...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="userRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade modal-modern" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="editUserForm">
        <div class="modal-header">
          <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="editUserModalLabel">
            <i class="bi bi-pencil-square text-white"></i>
            Edit User
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-4 pb-4">
            <input type="hidden" id="edit_user_id">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="edit_name" class="form-label-modern">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-modern" id="edit_name" name="name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_email" class="form-label-modern">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-modern" id="edit_email" name="email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_role" class="form-label-modern">Role <span class="text-danger">*</span></label>
                    <select class="form-select form-select-modern" id="edit_role" name="role_id" required>
                        <!-- Load roles via JS -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_employee" class="form-label-modern">Employee</label>
                    <select class="form-select form-select-modern" id="edit_employee" name="employee_id">
                        <option value="">Select Employee (Optional)</option>
                        <!-- Load employees via JS -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="edit_manager" class="form-label-modern">Manager</label>
                    <select class="form-select form-select-modern" id="edit_manager" name="is_manager">
                        <option value="">Select Manager (Optional)</option>
                        <!-- Load users via JS -->
                    </select>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-12">
                     <label class="form-label-modern mb-3">Permissions</label>
                </div>
                <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_worklog" name="is_worklog">
                        <label class="form-check-label" for="edit_is_worklog">Worklog Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_sales" name="is_sales">
                        <label class="form-check-label" for="edit_is_sales">Sales Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_task" name="is_task">
                        <label class="form-check-label" for="edit_is_task">Task Access</label>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_indiaMart" name="is_indiaMart">
                        <label class="form-check-label" for="edit_is_indiaMart">IndiaMart Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_calander" name="is_calander">
                        <label class="form-check-label" for="edit_is_calander">Calendar Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="edit_is_login" name="is_login">
                        <label class="form-check-label" for="edit_is_login">Login Enabled</label>
                    </div>
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

<!-- Create User Modal -->
<div class="modal fade modal-modern" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form id="createUserForm">
        <div class="modal-header">
          <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createUserModalLabel">
            <i class="bi bi-plus text-white"></i>
            Create User
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-4 pb-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="create_name" class="form-label-modern">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-modern" id="create_name" name="name" required placeholder="Enter Name">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_email" class="form-label-modern">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control form-control-modern" id="create_email" name="email" required placeholder="Enter Email">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_password" class="form-label-modern">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control form-control-modern" id="create_password" name="password" required placeholder="Enter Password">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_role" class="form-label-modern">Role <span class="text-danger">*</span></label>
                    <select class="form-select form-select-modern" id="create_role" name="role_id" required>
                        <!-- Roles will be loaded via JS -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_employee" class="form-label-modern">Employee</label>
                    <select class="form-select form-select-modern" id="create_employee" name="employee_id">
                        <option value="">Select Employee (Optional)</option>
                        <!-- Load employees via JS -->
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="create_manager" class="form-label-modern">Manager</label>
                    <select class="form-select form-select-modern" id="create_manager" name="is_manager">
                        <option value="">Select Manager (Optional)</option>
                        <!-- Load users via JS -->
                    </select>
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-12">
                     <label class="form-label-modern mb-3">Permissions</label>
                </div>
                 <div class="col-md-6">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_worklog" name="is_worklog">
                        <label class="form-check-label" for="create_is_worklog">Worklog Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_sales" name="is_sales">
                        <label class="form-check-label" for="create_is_sales">Sales Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_task" name="is_task">
                        <label class="form-check-label" for="create_is_task">Task Access</label>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_indiaMart" name="is_indiaMart">
                        <label class="form-check-label" for="create_is_indiaMart">IndiaMart Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_calander" name="is_calander">
                        <label class="form-check-label" for="create_is_calander">Calendar Access</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="create_is_login" name="is_login" checked>
                        <label class="form-check-label" for="create_is_login">Login Enabled</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-success w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Create
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
    const $info = $('#userRangeInfo');
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
  loadUsers();

  function loadUsers(page = 1) {
    let search = $('#search').val();
    
    $('#usersTable tbody').html(`
      <tr>
        <td colspan="7" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading users...</p>
        </td>
      </tr>
    `);

    $.get(`{{ route('fetchuser') }}?page=${page}&search=${search}`, function (data) {
      if (!data.data || data.data.length === 0) {
        $('#usersTable tbody').html(`
          <tr>
             <td colspan="7" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Users Found</h5>
              <p>Get started by creating your first user.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }

      let rows = '';
      $.each(data.data, function (i, user) {
          const managerName = user.manager ? user.manager.name : 'None';
          const worklogStatus = user.is_worklog ? '<span class="badge badge-modern-success">Yes</span>' : '<span class="badge badge-modern-secondary">No</span>';
          
          rows += `
            <tr style="animation-delay: ${i * 0.1}s;">
              <td><strong>${user.id}</strong></td>
              <td><strong>${user.name}</strong></td>
              <td><span class="badge badge-modern-info">${user.role ? user.role.role_name : '-'}</span></td>
              <td>${user.email}</td>
              <td>${managerName}</td>
              <td>${worklogStatus}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center">
                  <button class="btn-action btn-action-edit" onclick='openEditModal(${JSON.stringify(user).replace(/'/g, "&#39;")})' title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action btn-action-delete" onclick="deleteUser(${user.id})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
      });
      $('#usersTable tbody').html(rows);

      // Compact pagination
      buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
      updateRangeInfo(data.from, data.to, data.total);
      
    }).fail(function () {
      $('#usersTable tbody').html(`
        <tr>
          <td colspan="7" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mt-2 mb-0">Failed to load users. Please try again.</p>
          </td>
        </tr>
      `);
    });
  }
  
  // Pagination click
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
      loadUsers(page);
    }
  });

  // Search input
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
          loadUsers(1);
      }, 300);
  });
  
  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });
});

function deleteUser(id) {
  if (confirm("Are you sure you want to delete this user?")) {
    $.ajax({
      url: `/user/delete/${id}`,
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function () {
        showAlert('success', 'User deleted successfully.');
        // We can reload the table instead of full page reload if we exposed a global load function,
        // but finding the function scope complexity -> simple reload for now or re-trigger click.
        location.reload(); 
      },
      error: function () {
        showAlert('error', 'Failed to delete user.');
      }
    });
  }
}

// edit modal
function openEditModal(user) {
  $('#edit_user_id').val(user.id);
  $('#edit_name').val(user.name);
  $('#edit_email').val(user.email);
  $('#edit_is_worklog').prop('checked', user.is_worklog == 1);
  $('#edit_is_sales').prop('checked', user.is_sales == 1);
  $('#edit_is_task').prop('checked', user.is_task == 1);
  $('#edit_is_indiaMart').prop('checked', user.is_indiaMart == 1);
  $('#edit_is_calander').prop('checked', user.is_calander == 1);
  $('#edit_is_login').prop('checked', user.is_login == 1);

  // Load roles dropdown
  $.ajax({
    url: '{{ route("fetchrole") }}',
    method: 'GET',
    success: function (roles) {
      let roleSelect = $('#edit_role');
      roleSelect.empty();

      roles.forEach(role => {
        if (role.role_name !== 'Super Admin' && role.role_name !== 'super_admin') {
          roleSelect.append(`<option value="${role.id}" ${user.role && user.role.id === role.id ? 'selected' : ''}>${role.role_name}</option>`);
        }
      });

      // Load employees dropdown
      $.ajax({
        url: '{{ route("user.fetch-employees") }}',
        method: 'GET',
        success: function (employees) {
          let employeeSelect = $('#edit_employee');
          employeeSelect.empty();
          employeeSelect.append('<option value="">Select Employee (Optional)</option>');

          employees.forEach(employee => {
            employeeSelect.append(`<option value="${employee.id}" ${user.employee_id == employee.id ? 'selected' : ''}>${employee.employee_code} - ${employee.name}</option>`);
          });
        },
        error: function () {
          console.log('Failed to load employees');
        }
      });

      // Load users for manager dropdown
      $.ajax({
        url: '{{ route("fetchUsersForManager") }}',
        method: 'GET',
        success: function (users) {
          let managerSelect = $('#edit_manager');
          managerSelect.empty();
          managerSelect.append('<option value="">Select Manager (Optional)</option>');

          users.forEach(managerUser => {
            if (managerUser.id != user.id) { // Don't allow self as manager
              managerSelect.append(`<option value="${managerUser.id}" ${user.is_manager == managerUser.id ? 'selected' : ''}>${managerUser.name}</option>`);
            }
          });

          $('#editUserModal').modal('show');
        }
      });
    }
  });
}

$('#editUserForm').submit(function (e) {
  e.preventDefault();
  const $btn = $(this).find('button[type="submit"]');
  $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');

  const userId = $('#edit_user_id').val();

  $.ajax({
    url: `/user/update/${userId}`,
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    data: {
      name: $('#edit_name').val(),
      email: $('#edit_email').val(),
      role_id: $('#edit_role').val(),
      employee_id: $('#edit_employee').val() || null,
      is_manager: $('#edit_manager').val() || null,
      is_worklog: $('#edit_is_worklog').is(':checked') ? 1 : 0,
      is_sales: $('#edit_is_sales').is(':checked') ? 1 : 0,
      is_task: $('#edit_is_task').is(':checked') ? 1 : 0,
      is_indiaMart: $('#edit_is_indiaMart').is(':checked') ? 1 : 0,
      is_calander: $('#edit_is_calander').is(':checked') ? 1 : 0,
      is_login: $('#edit_is_login').is(':checked') ? 1 : 0
    },
    success: function () {
      $('#editUserModal').modal('hide');
      showAlert('success', 'User updated successfully.');
      location.reload();
    },
    error: function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.message) {
        showAlert('error', 'Error: ' + xhr.responseJSON.message);
      } else {
        showAlert('error', 'Failed to update user.');
      }
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
    }
  });
});

$('#createUserModal').on('show.bs.modal', function () {
  // Reset form and checkbox
  $('#createUserForm')[0].reset();
  $('#create_is_worklog').prop('checked', false);
  $('#create_is_sales').prop('checked', false);
  $('#create_is_task').prop('checked', false);
  $('#create_is_indiaMart').prop('checked', false);
  $('#create_is_calander').prop('checked', false);
  $('#create_is_login').prop('checked', true);

  // Load roles dropdown
  $.ajax({
    url: '{{ route("fetchrole") }}',
    method: 'GET',
    success: function (roles) {
      let roleSelect = $('#create_role');
      roleSelect.empty();

      roles.forEach(role => {
        if (role.role_name !== 'Super Admin' && role.role_name !== 'super_admin') {
          roleSelect.append(`<option value="${role.id}">${role.role_name}</option>`);
        }
      });
    },
    error: function (xhr, status, error) {
      console.error('Error loading roles:', xhr.responseText);
    }
  });

  // Load employees dropdown
  $.ajax({
    url: '{{ route("user.fetch-employees") }}',
    method: 'GET',
    success: function (employees) {
      let employeeSelect = $('#create_employee');
      employeeSelect.empty();
      employeeSelect.append('<option value="">Select Employee (Optional)</option>');

      employees.forEach(employee => {
        employeeSelect.append(`<option value="${employee.id}">${employee.employee_code} - ${employee.name}</option>`);
      });
    }
  });

  // Load users for manager dropdown
  $.ajax({
    url: '{{ route("fetchUsersForManager") }}',
    method: 'GET',
    success: function (users) {
      let managerSelect = $('#create_manager');
      managerSelect.empty();
      managerSelect.append('<option value="">Select Manager (Optional)</option>');

      users.forEach(user => {
        managerSelect.append(`<option value="${user.id}">${user.name}</option>`);
      });
    }
  });
});

// Handle Create User Form submission
$('#createUserForm').submit(function (e) {
  e.preventDefault();
  const $btn = $(this).find('button[type="submit"]');
  $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');

  $.ajax({
    url: '{{ route("user.store") }}',
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    data: {
      name: $('#create_name').val(),
      email: $('#create_email').val(),
      password: $('#create_password').val(),
      role_id: $('#create_role').val(),
      employee_id: $('#create_employee').val() || null,
      is_manager: $('#create_manager').val() || null,
      is_worklog: $('#create_is_worklog').is(':checked') ? 1 : 0,
      is_sales: $('#create_is_sales').is(':checked') ? 1 : 0,
      is_task: $('#create_is_task').is(':checked') ? 1 : 0,
      is_indiaMart: $('#create_is_indiaMart').is(':checked') ? 1 : 0,
      is_calander: $('#create_is_calander').is(':checked') ? 1 : 0,
      is_login: $('#create_is_login').is(':checked') ? 1 : 0
    },
    success: function () {
      $('#createUserModal').modal('hide');
      showAlert('success', 'User created successfully.');
      location.reload();
    },
    error: function (xhr) {
      if (xhr.responseJSON && xhr.responseJSON.message) {
        showAlert('error', 'Error: ' + xhr.responseJSON.message);
      } else {
        showAlert('error', 'Failed to create user.');
      }
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Create');
    }
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
@endpush
