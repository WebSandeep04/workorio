@extends('layouts.app')
@section('title', 'My Tasks')
@section('page_title', 'My Tasks')
@section('content')
@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .row-overdue,
  .row-overdue td {
      background: #f72323ff !important;
      box-shadow: inset 0 0 0 9999px #f02525ff !important;
      color: #fff !important;
  }
  
  .row-overdue td a {
      color: #fff !important;
  }

  /* Summary Cards (Optional but good for consistency) */
  /* Summary Cards */
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
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 55px;
    height: 65px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 20px;
    height: 20px;
    object-fit: contain;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #64748b;
    line-height: 1.1;
    font-family: Montserrat, sans-serif;
  }

  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    color: #0f172a;
    font-family: Montserrat, sans-serif;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  /* Filter Box Styling */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  .filterBox .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: #fff;
  }

  @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-left: 0;
    }

    .summary-cards {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.5rem;
    }

    .data-table-card .custom-table tbody td {
      font-size: 0.75rem
    }
    
    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-field {
        width: 100%;
    }
    
    .table-search-btn {
      width: auto;
    }
  }

  /* Table Search & Add Button */
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
    background: #434afa;
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

  .table-search-btn:active {
    transform: translateY(0);
    background: #2d30b8;
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

  /* Table Styles */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
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

  .data-table-card .table-scroll {
    width: 100%;
    overflow-x: auto;
    padding: 0.5rem 0.75rem 1rem;
    background: transparent;
  }
  
  .data-table-card .table-scroll::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-scroll::-webkit-scrollbar-thumb { background: #434aFA; border-radius: 999px; }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 800px;
    background: transparent;
    font-size: 0.85rem;
    table-layout: auto;
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
    border-right: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }
  .data-table-card .custom-table thead th:last-child { border-right: none; }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #0f172a;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    vertical-align: middle;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    transform: translateY(-1px);
    box-shadow: 0px 2px 5px rgba(0,0,0,0.02);
  }
  
  /* Kanban Styles - Keeping the existing ones but ensuring boundaries */
  .kanban-board {
      display: flex;
      justify-content: space-between;
      gap: 2px;
      overflow-x: auto;
      overflow-y: hidden;
      padding: 8px;
      height: 450px;
      background: linear-gradient(135deg, #f8fafc 0%, #e8f0f8 100%);
      border-radius: 10px;
      border: 2px solid #e2e8f0;
  }
  .kanban-column {
      min-width: 180px;
      max-width: 180px;
      background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
      border-radius: 10px;
      padding: 6px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      border: 2px solid #d1d9e6;
      transition: all 0.3s ease-in-out;
      position: relative;
      max-height: calc(100vh - 270px);
      overflow-y: auto;
  }
  .kanban-column:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
      border-color: #0d6efd;
  }
  .kanban-column-header {
      background: linear-gradient(135deg, #0d6efd 0%, #1e90ff 100%);
      color: white;
      padding: 6px 10px;
      border-radius: 6px;
      margin-bottom: 8px;
      font-weight: 700;
      text-align: center;
      position: sticky;
      top: 0;
      z-index: 10;
      font-size: 0.75rem;
      box-shadow: 0 2px 8px rgba(13, 110, 253, 0.4);
      text-shadow: 0 1px 3px rgba(0,0,0,0.2);
      border: 2px solid rgba(255,255,255,0.3);
      letter-spacing: 0.2px;
  }
  .kanban-column-header .badge {
      background-color: rgba(255,255,255,0.3) !important;
      color: white !important;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,0.5);
      font-size: 0.65rem;
      margin-left: 4px;
  }
  .kanban-column-content {
      flex: 1;
      min-height: auto;
      padding: 2px;
      overflow-y: auto;
    }
  .kanban-card {
      background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
      border-radius: 8px;
      padding: 6px 8px;
      padding-left: 12px;
      margin-bottom: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      cursor: move;
      transition: all 0.3s ease-in-out;
      border: 1.5px solid #e2e8f0;
      border-left: 3px solid #0d6efd;
  }
  .kanban-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      transform: translateY(-1px) scale(1.01);
      border-color: #0d6efd;
      border-left-width: 4px;
      background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
  }
  .view-toggle-btn.active {
      background-color: #0d6efd !important;
      color: white !important;
  }

  /* Badge styling consistency */
  .badge {
    font-weight: 600;
    padding: 0.35em 0.6em;
    font-size: 0.65rem;
    border-radius: 4px;
    letter-spacing: 0.3px;
  }

  .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
</style>
@endpush
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="summary-card card-1">
        <div class="summary-card-icon icon-sunrise">
          <img src="{{ asset('img/icons/call.png') }}" alt="Total Tasks">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Tasks</div>
          <div class="summary-card-value" id="totalTasks">0</div>
        </div>
      </div>
      <div class="summary-card card-2">
        <div class="summary-card-icon icon-amber">
          <img src="{{ asset('img/icons/underprocess.png') }}" alt="In Progress">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">In Progress</div>
          <div class="summary-card-value" id="inProgressTasks">0</div>
        </div>
      </div>
      <div class="summary-card card-3">
        <div class="summary-card-icon icon-emerald">
          <img src="{{ asset('img/icons/tick.png') }}" alt="Completed">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Completed</div>
          <div class="summary-card-value" id="completedTasks">0</div>
        </div>
      </div>
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-rose">
          <img src="{{ asset('img/icons/pending.png') }}" alt="Pending">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Pending</div>
          <div class="summary-card-value" id="pendingTasks">0</div>
        </div>
      </div>
      <div class="summary-card card-5">
        <div class="summary-card-icon icon-sky">
          <img src="{{ asset('img/icons/new.png') }}" alt="Today's Tasks">
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Today's Tasks</div>
          <div class="summary-card-value" id="todayTasks">0</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filterBox mb-2">
      <div class="mb-2">
        <label for="filter_customer" class="form-label-modern">
          <i class="bi bi-building"></i> Customer
        </label>
        <select id="filter_customer" class="form-select form-select-sm form-control-modern">
          <option value="">All Customers</option>
        </select>
      </div>
      
      <div class="mb-2">
        <label for="filter_status" class="form-label-modern">
          <i class="bi bi-check-circle"></i> Status
        </label>
        <select id="filter_status" class="form-select form-select-sm form-control-modern">
          <option value="">All Statuses</option>
          <option value="done">Done</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="filter_priority" class="form-label-modern">
          <i class="bi bi-flag"></i> Priority
        </label>
        <select id="filter_priority" class="form-select form-select-sm form-control-modern">
          <option value="">All Priorities</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="filter_type" class="form-label-modern">
          <i class="bi bi-tag"></i> Type
        </label>
        <select id="filter_type" class="form-select form-select-sm form-control-modern">
          <option value="">All Types</option>
          <option value="task">Task</option>
          <option value="qc">QC</option>
          <option value="cp">Critical Path</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="filter_date_from" class="form-label-modern">
          <i class="bi bi-calendar"></i> Date From
        </label>
        <input type="date" id="filter_date_from" class="form-control form-control-sm form-control-modern">
      </div>

      <div class="mb-2">
        <label for="filter_date_to" class="form-label-modern">
          <i class="bi bi-calendar"></i> Date To
        </label>
        <input type="date" id="filter_date_to" class="form-control form-control-sm form-control-modern">
      </div>

      <div class="d-flex align-items-end mb-2">
        <button type="button" class="btn btn-sm btn-light w-100 fw-bold" onclick="clearFilters()" title="Reset Filters" style="font-size: 0.75rem;">
          <i class="bi bi-arrow-counterclockwise"></i> Reset
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="table-search mb-2">
      <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search tasks..." />
      </div>
      <button type="button" onclick="handleExport()" class="table-search-btn" style="background: #434afa; border:none; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);">
        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
      </button>
    </div>

    <!-- Main Content -->
    <div class="card shadow-sm table-container" style="border-radius: 2px; border: 1px solid #eee;">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-2" style="border-bottom: 1px solid #eee;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list-task text-primary"></i>
                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">My Tasks</h6>
            </div>
            
            <div class="d-flex gap-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary view-toggle-btn active" data-view="table">
                        <i class="bi bi-table"></i> Table
                    </button>
                    <button type="button" class="btn btn-outline-primary view-toggle-btn" data-view="kanban">
                        <i class="bi bi-columns-gap"></i> Kanban
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- Table View -->
            <div id="tableView" class="view-container">
                <div class="modern-card data-table-card">
                    <div class="modern-card-body">
                        <div class="table-scroll">
                            <table class="table custom-table">
                                <thead>
                            <tr>
                                <th scope="col" style="width: 40px;"><input type="checkbox" id="selectAllTasks" class="form-check-input"></th>
                                <th scope="col" style="width: 15%;">Customer</th>
                                <th scope="col" style="width: 20%;">Task</th>
                                <th scope="col" style="width: 5%;">Type</th>
                                <th scope="col" style="width: 8%;">Priority</th>
                                <th scope="col" style="width: 10%;">Status</th>
                                <th scope="col" style="width: 5%;">Remarks</th>
                                <th scope="col" style="width: 7%;">Due</th>
                                <th scope="col" style="width: 10%;">Created</th>
                            </tr>
                        </thead>
                        <tbody id="taskTableBody">
                            <tr>
                                <td colspan="10" class="text-center p-4 text-muted">Loading tasks...</td>
                            </tr>
                        </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Kanban View -->
            <div id="kanbanView" class="view-container p-3" style="display: none;">
                <div id="kanbanBoard" class="kanban-board">
                    <!-- Kanban columns will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Task Modal -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" style="background: #434AFA;">
        <h5 class="modal-title fw-bold" id="viewTaskModalLabel">
            <i class="bi bi-card-text me-2"></i>Task Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Meta Info Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Assigned To</small>
                        <p id="view_assigned_to" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 bg-light text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Customer</small>
                        <p id="view_customer" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3 bg-light text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Created By</small>
                        <p id="view_created_by" class="mb-0 fw-semibold text-dark"></p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="bg-light border-2 opacity-50 my-4">

        <!-- Task Content -->
        <div class="mb-4">
            <small class="text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.7rem;">Task Name</small>
            <h5 id="view_task_name" class="fw-bold text-dark mb-0"></h5>
        </div>
        
        <div class="bg-light rounded-3 p-3 border border-light">
            <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-file-text me-2"></i>Description</h6>
            <p id="view_task_description" class="mb-0 text-secondary" style="white-space: pre-wrap; font-size: 0.9rem; line-height: 1.6;"></p>
        </div>

        <!-- Images Section -->
        <div id="view_task_images_container" class="mt-4" style="display: none;">
            <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-images me-2"></i>Attachments</h6>
            <div id="view_task_images" class="d-flex flex-wrap gap-2"></div>
        </div>
      </div>
      <div class="modal-footer bg-light border-0 py-2">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let selectedTaskIds = new Set();
$(document).ready(function() {
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        return (text || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    console.log('My Tasks page loaded');
    
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Load dropdowns using the shared routes
    function loadDropdowns() {
        // Customers
        $.get("{{ route('task.customers') }}", function(data) {
            let options = '<option value="">All Customers</option>';
            if (Array.isArray(data) && data.length) {
                $.each(data, function(i, customer) {
                    options += `<option value="${customer.id}">${customer.name}</option>`;
                });
            }
            $('#filter_customer').html(options);
        });

        // Statuses
        $.get("{{ route('task.statuses') }}", function(data) {
            let options = '<option value="">All Statuses</option><option value="done">Done</option>';
            if (data && data.length > 0) {
                $.each(data, function(i, status) {
                    options += `<option value="${status.id}">${status.name}</option>`;
                });
            }
            $('#filter_status').html(options);
        });

        // Priorities
        $.get("{{ route('task.priorities') }}", function(data) {
            let options = '<option value="">All Priorities</option>';
            if (Array.isArray(data) && data.length) {
                $.each(data, function(i, priority) {
                    options += `<option value="${priority.id}">${priority.name}</option>`;
                });
            }
            $('#filter_priority').html(options);
        });
    }

    loadDropdowns();

    // Apply client-side filters
    function applyFilters(data) {
        if (!Array.isArray(data)) return [];

        let filtered = data.slice();
        const filterCustomer = $('#filter_customer').val();
        const filterStatus = $('#filter_status').val();
        const filterPriority = $('#filter_priority').val();
        const filterType = $('#filter_type').val();
        const filterDateFrom = $('#filter_date_from').val();
        const filterDateTo = $('#filter_date_to').val();

        if (filterCustomer) {
            filtered = filtered.filter(task => String(task.customer_id) === String(filterCustomer));
        }

        if (filterStatus) {
            if (filterStatus === 'done') {
                filtered = filtered.filter(task => {
                    const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
                    return task.is_done == 1 || task.is_done === true || statusName === 'done' || statusName.includes('completed');
                });
            } else {
                filtered = filtered.filter(task => {
                    const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
                    const isTaskDone = task.is_done == 1 || task.is_done === true || statusName === 'done' || statusName.includes('completed');
                    return task.task_status_id == filterStatus && !isTaskDone;
                });
            }
        } else {
            // Default: Hide done/completed tasks
            filtered = filtered.filter(task => {
                const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
                const isTaskDone = task.is_done == 1 || task.is_done === true || statusName === 'done' || statusName.includes('completed');
                return !isTaskDone;
            });
        }

        if (filterPriority) {
            filtered = filtered.filter(task => {
                const priorityId = task.task_priority_id ?? (task.priority ? task.priority.id : null);
                return String(priorityId || '') === String(filterPriority);
            });
        }

        if (filterType) {
            filtered = filtered.filter(task => (task.task_type || 'task') === filterType);
        }

        if (filterDateFrom) {
            const fromDate = new Date(filterDateFrom);
            filtered = filtered.filter(task => {
                if (!task.created_at) return false;
                return new Date(task.created_at) >= fromDate;
            });
        }

        if (filterDateTo) {
            const toDate = new Date(filterDateTo);
            toDate.setHours(23,59,59,999);
            filtered = filtered.filter(task => {
                if (!task.created_at) return false;
                return new Date(task.created_at) <= toDate;
            });
        }

        const searchTerm = $('#search').val().toLowerCase();
        if (searchTerm) {
            filtered = filtered.filter(task => {
                const taskName = (task.task_name || '').toLowerCase();
                const taskDesc = (task.task || '').toLowerCase();
                const customerName = (task.customer?.name || '').toLowerCase();
                
                return taskName.includes(searchTerm) || 
                       taskDesc.includes(searchTerm) || 
                       customerName.includes(searchTerm);
            });
        }

        return filtered;
    }

    function isDateOverdue(dateString) {
      if (!dateString) return false;
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      
      let due;
      if (typeof dateString === 'string' && dateString.length >= 10) {
         const parts = dateString.substring(0, 10).split('-');
         if (parts.length === 3) {
             due = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
         } else {
             due = new Date(dateString);
             due.setHours(0,0,0,0);
         }
      } else {
         due = new Date(dateString);
         due.setHours(0,0,0,0);
      }
      return due < today;
    }

    // Load tasks
    function loadTasks() {
        console.log('Loading my tasks...');
        $.ajax({
            url: "{{ route('my-tasks.fetch') }}",
            type: "GET",
            dataType: 'json',
            success: function(data) {
                console.log('Tasks loaded:', data);
                window.allTasks = data; // Store for modal access

                // Calculate and update stats
                const total = data.length;
                const inProgress = data.filter(t => {
                   const statusName = (t.status && t.status.name) ? t.status.name.toLowerCase() : '';
                   const isTaskDone = t.is_done || statusName === 'done' || statusName.includes('completed');
                   return !isTaskDone && (statusName === 'in progress');
                }).length;
                const completed = data.filter(t => {
                   const statusName = (t.status && t.status.name) ? t.status.name.toLowerCase() : '';
                   return t.is_done || statusName === 'done' || statusName.includes('completed');
                }).length;
                const pending = data.filter(t => {
                   const statusName = (t.status && t.status.name) ? t.status.name.toLowerCase() : '';
                   const isTaskDone = t.is_done || statusName === 'done' || statusName.includes('completed');
                   return !isTaskDone && (statusName === 'pending' || !statusName);
                }).length;
                
                const now = new Date();
                const today = data.filter(t => {
                    if (!t.created_at) return false;
                    const d = new Date(t.created_at);
                    return d.getDate() === now.getDate() && 
                           d.getMonth() === now.getMonth() && 
                           d.getFullYear() === now.getFullYear();
                }).length;

                $('#totalTasks').text(total);
                $('#inProgressTasks').text(inProgress);
                $('#completedTasks').text(completed);
                $('#pendingTasks').text(pending);
                $('#todayTasks').text(today);
                
                data = applyFilters(data);
                
                let overdueCount = 0;
                let html = '';
                if (!data || data.length === 0) {
                    html = '<tr><td colspan="10" class="text-center p-4 text-muted">No tasks found</td></tr>';
                } else {
                    $.each(data, function(index, task) {
                        // Check Overdue
                        let isOverdue = false;
                        const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
                        const isTaskCompleted = task.is_done || statusName === 'done' || statusName.includes('completed') || statusName.includes('complete');
                        
                        if (task.due_date && !isTaskCompleted) {
                            if (isDateOverdue(task.due_date)) {
                                isOverdue = true;
                                overdueCount++;
                            }
                        }
                        const rowClass = '';

                        // Status Badge/Dropdown logic
                        let statusDisplay = '';
                        if (task.is_done) {
                             statusDisplay = '<span class="fw-bold text-success">Done</span>';
                        } else {
                            // Inline status dropdown
                            statusDisplay = `
                                <select class="form-select form-select-sm" style="width: auto; min-width: 100px; font-size: 0.75rem; padding: 0.15rem 0.4rem;" onchange="updateTaskStatus(${task.id}, this.value)">
                                    <option value="pending" ${task.status && task.status.name === 'Pending' ? 'selected' : ''}>Pending</option>
                                    <option value="in progress" ${task.status && task.status.name === 'In Progress' ? 'selected' : ''}>In Progress</option>
                                    <option value="completed" ${task.status && task.status.name === 'Completed' ? 'selected' : ''}>Completed</option>
                                    <option value="cancelled" ${task.status && task.status.name === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            `;
                        }

                        const createdDate = task.created_at ? new Date(task.created_at).toLocaleDateString('en-GB') : 'N/A';
                        const rawName = (task.task_name || '').toString();
                        
                        // Task Type badge
                        const taskType = task.task_type || 'task';
                        let typeColor = '#0d6efd';
                        if (taskType === 'qc') typeColor = '#0dcaf0';
                        else if (taskType === 'cp') typeColor = '#dc3545';
                        const typeBadge = `<span class="fw-bold" style="color: ${typeColor}">${taskType.toUpperCase()}</span>`;

                        // Priority
                        let priorityBadge = '<span class="fw-bold text-secondary">None</span>';
                        if (task.priority) {
                            const pColor = task.priority.color || '#6c757d';
                            priorityBadge = `<span class="fw-bold" style="color: ${pColor}">${task.priority.name}</span>`;
                        }
                        
                        // Remarks
                        const remarksCount = task.remarks ? task.remarks.length : 0;
                        const remarksBtn = `
                            <button type="button" class="btn btn-sm btn-outline-secondary position-relative" onclick="showRemarksModal(${task.id})" title="Remarks">
                                <i class="bi bi-chat-left-text"></i>
                                ${remarksCount > 0 ? `<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.55rem; padding: 2px 4px;">${remarksCount}</span>` : ''}
                            </button>
                        `;

                        let dueDateRaw = task.due_date ? new Date(task.due_date).toLocaleDateString('en-GB') : '-';
                        let dueDate = isOverdue 
                            ? `<span class="text-danger fw-bold" title="Overdue">${dueDateRaw}</span>` 
                            : dueDateRaw;
                        
                        const isChecked = selectedTaskIds.has(task.id) ? 'checked' : '';
                        html += `
                            <tr class="${rowClass}">
                                <td><input type="checkbox" class="task-checkbox form-check-input" value="${task.id}" ${isChecked}></td>
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.customer ? task.customer.name : 'N/A'}">
                                        ${(task.customer ? task.customer.name : 'N/A').length > 7 ? (task.customer ? task.customer.name : 'N/A').substring(0, 7) + '...' : (task.customer ? task.customer.name : 'N/A')}
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-decoration-none" style="color: #212529;" title="${escapeHtml(rawName)}">
                                        ${(task.task_name || 'N/A').length > 7 ? (task.task_name || 'N/A').substring(0, 7) + '...' : (task.task_name || 'N/A')}
                                    </a>
                                </td>
                                <td>${typeBadge}</td>
                                <td>${priorityBadge}</td>
                                <td>${statusDisplay}</td>
                                <td>${remarksBtn}</td>
                                <td>${dueDate}</td>
                                <td><small>${createdDate}</small></td>
                            </tr>
                        `;
                    });
                }
                
                console.log('Total overdue visible on My Tasks page:', overdueCount);
                $('#taskTableBody').html(html);
                
                // Refresh kanban if visible
                if ($('#kanbanView').is(':visible')) {
                    renderKanban();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading tasks:', xhr.responseText, status, error);
                $('#taskTableBody').html('<tr><td colspan="10" class="text-center text-danger">Error loading tasks</td></tr>');
            }
        });
    }

    // Filter Listeners
    $('#filter_customer, #filter_status, #filter_priority, #filter_type, #filter_date_from, #filter_date_to').on('change', function() {
        selectedTaskIds.clear();
        $('#selectAllTasks').prop('checked', false);
        loadTasks();
        updateExportUrl();
    });

    function updateExportUrl() {
        // No longer needed
    }

    function handleExport() {
        const selectedIds = Array.from(selectedTaskIds);
        
        const baseUrl = "{{ route('task.export') }}";
        const params = new URLSearchParams({
            type: 'assigned',
            customer_id: $('#filter_customer').val() || '',
            status: $('#filter_status').val() || '',
            priority: $('#filter_priority').val() || '',
            task_type: $('#filter_type').val() || '',
            date_from: $('#filter_date_from').val() || '',
            date_to: $('#filter_date_to').val() || '',
            search: $('#search').val() || ''
        });
        
        if (selectedIds.length > 0) {
            params.append('ids', selectedIds.join(','));
        }
        
        window.location.href = `${baseUrl}?${params.toString()}`;
    }

    // Select All functionality
    $(document).on('change', '#selectAllTasks', function() {
        const isChecked = $(this).prop('checked');
        // allTasks is stored on window during loadTasks
        const filtered = applyFilters(window.allTasks || []);
        if (isChecked) {
            filtered.forEach(t => selectedTaskIds.add(t.id));
        } else {
            selectedTaskIds.clear();
        }
        $('.task-checkbox').prop('checked', isChecked);
    });

    // Individual checkbox change
    $(document).on('change', '.task-checkbox', function() {
        const id = parseInt($(this).val());
        if ($(this).prop('checked')) {
            selectedTaskIds.add(id);
        } else {
            selectedTaskIds.delete(id);
            $('#selectAllTasks').prop('checked', false);
        }
    });

    let searchTimeout;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadTasks();
            updateExportUrl();
        }, 300);
    });

    window.clearFilters = function() {
        $('#filter_customer').val('');
        $('#filter_status').val('');
        $('#filter_priority').val('');
        $('#filter_type').val('');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        $('#search').val('');
        loadTasks();
        updateExportUrl();
    };

    // View Toggle
    $('.view-toggle-btn').on('click', function(){
        const view = $(this).data('view');
        $('.view-toggle-btn').removeClass('active');
        $(this).addClass('active');
        
        if (view === 'table') {
            $('#tableView').show();
            $('#kanbanView').hide();
        } else {
            $('#tableView').hide();
            $('#kanbanView').show();
            renderKanban();
        }
    });

    // Render Kanban Board
    function renderKanban() {
        $.ajax({
            url: "{{ route('my-tasks.fetch') }}",
            type: "GET",
            dataType: 'json',
            success: function(data) {
                // Apply same filters to kanban
                data = applyFilters(data);

                $.get("{{ route('task.statuses') }}", function(statuses) {
                    const kanbanBoard = $('#kanbanBoard');
                    kanbanBoard.empty();
                    
                    statuses.forEach(function(status) {
                        const statusTasks = data.filter(function(task) {
                            return task.task_status_id == status.id;
                        });
                        
                        const statusColor = status.color || '#0d6efd';
                        const statusColor2 = status.color ? adjustColor(status.color, 20) : '#1e90ff';
                        
                        const columnHtml = `
                            <div class="kanban-column" data-status-id="${status.id}" style="border-top: 4px solid ${statusColor};">
                                <div class="kanban-column-header" style="background: linear-gradient(135deg, ${statusColor} 0%, ${statusColor2} 100%); border-color: ${statusColor};">
                                    ${escapeHtml(status.name)} <span class="badge bg-light text-dark">${statusTasks.length}</span>
                                </div>
                                <div class="kanban-column-content" ondrop="dropMyTask(event)" ondragover="allowDrop(event)">
                                    ${renderKanbanCards(statusTasks, statusColor)}
                                </div>
                            </div>
                        `;
                        kanbanBoard.append(columnHtml);
                    });
                    
                    initializeDragAndDrop();
                });
            },
            error: function(xhr) {
                console.log(xhr);
            }
        });
    }

    function adjustColor(color, percent) {
        if (!color || !color.startsWith('#')) return '#1e90ff';
        const num = parseInt(color.replace("#",""), 16);
        const amt = Math.round(2.55 * percent);
        const R = Math.min(255, Math.max(0, (num >> 16) + amt));
        const G = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + amt));
        const B = Math.min(255, Math.max(0, (num & 0x0000FF) + amt));
        return "#" + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
    }

    function renderKanbanCards(tasks, statusColor) {
        if (!tasks || tasks.length === 0) {
            return '<div class="kanban-empty-state">No tasks</div>';
        }
        let html = '';
        tasks.forEach(function(task) {
            const taskName = (task.task_name || '').toString();
            html += `
                <div class="kanban-card" draggable="true" data-task-id="${task.id}" data-status-id="${task.task_status_id}" style="border-left-color: ${statusColor || '#0d6efd'};">
                    <div class="kanban-card-header">${escapeHtml(taskName)}</div>
                </div>
            `;
        });
        return html;
    }

    // Drag and Drop
    let draggedElement = null;
    function initializeDragAndDrop() {
        $('.kanban-card').off('dragstart dragend');
        $('.kanban-card').on('dragstart', function(e) {
            draggedElement = this;
            $(this).addClass('dragging');
            e.originalEvent.dataTransfer.effectAllowed = 'move';
        });
        $('.kanban-card').on('dragend', function(e) {
            $(this).removeClass('dragging');
            draggedElement = null;
        });
    }

    window.allowDrop = function(ev) {
        ev.preventDefault();
        ev.dataTransfer.dropEffect = 'move';
    }

    window.dropMyTask = function(ev) {
        ev.preventDefault();
        if (!draggedElement) return;
        
        const taskId = $(draggedElement).data('task-id');
        const newStatusId = $(ev.target).closest('.kanban-column').data('status-id');
        const oldStatusId = $(draggedElement).data('status-id');
        
        if (newStatusId && newStatusId != oldStatusId) {
            updateTaskStatus(taskId, newStatusId);
        }
    }

    // Update Status with Alert
    const originalUpdateTaskStatus = window.updateTaskStatus;
    window.updateTaskStatus = function(taskId, newStatus) {
        console.log('Updating task status:', taskId, newStatus);
        $.ajax({
            type: 'POST',
            url: '/task/' + taskId + '/update-status', 
            data: {
                status: newStatus,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showTaskAlert('success', 'Task updated');
                    loadTasks();
                } else {
                    showTaskAlert('error', 'Update failed');
                }
            },
            error: function(xhr) {
                 if (!isNaN(newStatus)) {
                     $.ajax({
                        url: '/task/' + taskId + '/update-status-id',
                        type: 'POST',
                        data: {
                            task_status_id: newStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.success) {
                                showTaskAlert('success', 'Task updated');
                                loadTasks();
                            }
                        }
                     });
                 } else {
                     showTaskAlert('error', 'Error updating status');
                 }
            }
        });
    };

    function showTaskAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(function() { $('.alert').fadeOut(); }, 3000);
    }
    
    // Initial Load
    loadTasks();
    
    // Modal Helpers (Full Text & Remarks)
    $(document).on('click', '.task-name-link', function(e){
        e.preventDefault();
        showFullTextModal('Task Name', $(this).data('full') || '');
    });
    $(document).on('click', '.task-desc-link', function(e){
        e.preventDefault();
        showFullTextModal('Task Description', $(this).data('full') || '');
    });

    function showFullTextModal(title, text) {
        let modalEl = document.getElementById('myTasksFullTextModal');
        if (!modalEl) {
             const html = `
            <div class="modal fade" id="myTasksFullTextModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="myTasksFullTextModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <pre id="myTasksFullTextBody" class="mb-0" style="white-space: pre-wrap; word-break: break-word;"></pre>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            modalEl = document.getElementById('myTasksFullTextModal');
        }
        $('#myTasksFullTextModalLabel').text(title);
        $('#myTasksFullTextBody').text(text);
        new bootstrap.Modal(modalEl).show();
    }

    window.showRemarksModal = function(taskId) {
        // Reuse existing logic
        let task = null;
        $.ajax({
            url: "{{ route('my-tasks.fetch') }}",
            type: "GET",
            dataType: 'json',
            success: function(data) {
                task = data.find(t => t.id == taskId);
                if (task) {
                    let modalEl = document.getElementById('taskRemarksModal');
                    if (!modalEl) {
                        const html = `
                        <div class="modal fade" id="taskRemarksModal" tabindex="-1">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Remarks</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                              </div>
                              <div class="modal-body">
                                <div id="remarksList" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;"></div>
                                <div class="border-top pt-3">
                                  <h6>Add New Remark</h6>
                                  <textarea id="newRemarkText" class="form-control" rows="3"></textarea>
                                  <button type="button" class="btn btn-primary btn-sm mt-2" id="saveRemarkBtn"><i class="bi bi-save"></i> Add</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>`;
                        document.body.insertAdjacentHTML('beforeend', html);
                        modalEl = document.getElementById('taskRemarksModal');
                    }
                    displayRemarks(task.remarks || []);
                    $('#saveRemarkBtn').attr('data-task-id', taskId);
                    new bootstrap.Modal(modalEl).show();
                }
            }
        });
    };

    function displayRemarks(remarks) {
        const list = $('#remarksList');
        if (!remarks || !remarks.length) {
            list.html('<p class="text-muted text-center">No remarks.</p>');
            return;
        }
        let html = '<div class="list-group">';
        remarks.forEach(r => {
             html += `<div class="list-group-item">
                <p class="mb-1">${escapeHtml(r.remark)}</p>
                <small class="text-muted">${r.user ? r.user.name : ''} - ${new Date(r.created_at).toLocaleString()}</small>
             </div>`;
        });
        html += '</div>';
        list.html(html);
    }

    $(document).on('click', '#saveRemarkBtn', function() {
        const taskId = $(this).attr('data-task-id');
        const text = $('#newRemarkText').val().trim();
        if (!text) return;
        $.post("{{ route('task.remark.save') }}", {
            task_id: taskId,
            remark: text,
            _token: '{{ csrf_token() }}'
        }, function(res) {
            if (res.success) {
                $('#newRemarkText').val('');
                showTaskAlert('success', 'Remark added');
                $.get("{{ route('my-tasks.fetch') }}", function(data) {
                    const t = data.find(i => i.id == taskId);
                    if (t) displayRemarks(t.remarks);
                });
                loadTasks();
            }
        });
    });

    window.handleViewImageError = function(img, taskId, imageId, imagePath) {
        if (img.dataset.retried) return;
        img.dataset.retried = true;

        // If currently using route URL, try storage URL, and vice versa
        if (img.src.includes('/task/')) {
            img.src = `/storage/${imagePath}`;
        } else {
            img.src = `/task/${taskId}/image/${imageId}`;
        }
    };

    window.viewTaskDetails = function(id) {
      const task = window.allTasks.find(t => t.id === id);
      if (task) {
        // Assigned users logic
        let assignedTo = 'N/A';
        if (task.assigned_users && task.assigned_users.length > 0) {
            assignedTo = task.assigned_users.map(u => u.name).join(', ');
        } else if (task.user) {
            assignedTo = task.user.name;
        }

        $('#view_assigned_to').text(assignedTo);
        $('#view_customer').text(task.customer?.name || 'N/A');
        $('#view_created_by').text(task.creator?.name || 'N/A');
        $('#view_task_name').text(task.task_name || 'N/A');
        $('#view_task_description').text(task.task || 'No description provided.');

        // Images logic
        const imagesContainer = $('#view_task_images_container');
        const imagesDiv = $('#view_task_images');
        imagesDiv.empty();

        if (task.images && task.images.length > 0) {
            task.images.forEach(function(img) {
                let imageUrl = '';
                
                // Prioritize the secure route if ID exists to avoid 403 on storage links
                if (img.id) {
                     imageUrl = `/task/${task.id}/image/${img.id}`;
                } else if (img.url) {
                    imageUrl = img.url;
                } else if (img.image_path) {
                    imageUrl = `/storage/${img.image_path}`;
                }

                if (imageUrl) {
                    const isDoc = !!img.original_name && img.original_name.match(/\.(pdf|doc|docx|xls|xlsx|csv|txt|zip)$/i);
                    const ext = (img.original_name || '').split('.').pop().toLowerCase();
                    const isKnownDoc = isDoc || ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip'].includes(ext);

                    if (isKnownDoc) {
                        imagesDiv.append(`
                            <a href="${imageUrl}" target="_blank" class="d-flex flex-column align-items-center justify-content-center border rounded p-2 text-decoration-none bg-light" style="width: 80px; height: 80px;" title="${img.original_name || 'Document'}">
                                <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                <small class="text-truncate w-100 text-center text-dark mt-1" style="font-size: 0.65rem;">${img.original_name || 'Doc'}</small>
                            </a>
                        `);
                    } else {
                        imagesDiv.append(`
                            <a href="${imageUrl}" target="_blank" class="d-block border rounded overflow-hidden position-relative" style="width: 80px; height: 80px;" title="${img.original_name || 'Image'}">
                                <img src="${imageUrl}" class="w-100 h-100" style="object-fit: cover;" alt="Task Image"
                                    onerror="handleViewImageError(this, ${task.id}, ${img.id || 'null'}, '${img.image_path || ''}')">
                                <div class="position-absolute bottom-0 start-0 w-100 text-center text-truncate text-white bg-dark bg-opacity-50" style="font-size: 0.55rem; padding: 1px;">
                                    ${img.original_name || 'Image'}
                                </div>
                            </a>
                        `);
                    }
                }
            });
            imagesContainer.show();
        } else {
            imagesContainer.hide();
        }

        $('#viewTaskModal').modal('show');
      }
    };

});
</script>
@endpush
