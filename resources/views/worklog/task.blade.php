@extends('layouts.app')
@section('title', 'My Created Tasks')
@section('page_title', 'My Created Tasks')

@push('styles')
<style>
  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards,
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
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

  .summary-card,
  .status-card {
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
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card::before,
  .status-card::before {
    display: none;
  }

  .summary-card:hover,
  .status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card.card-1,
  .summary-card.card-2,
  .summary-card.card-3,
  .summary-card.card-4,
  .summary-card.card-5 {
    background: #fff;
  }

  .summary-card-label,
  .status-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value,
  .status-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

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

  .filterBox .form-control-modern option {
    color: #000;
    background: #fff;
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

  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

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

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .table-responsive {
    border-radius: 18px;
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

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background: transparent;
    font-size: 0.85rem;
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
    padding: 0.4rem 0.5rem;
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
    padding: 0.25rem 0.5rem;
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
    background: #434AFA;
    border-color: #434AFA;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }

  .loading-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
    font-size: 10px;
  }

  .loading-state i {
    font-size: 1rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 1rem;
    color: #6c757d;
    font-size: 10px;
  }

  .empty-state i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
  }

  .action-btn {
    padding: 0.15rem 0.35rem;
    font-size: 0.7rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-right: 0.25rem;
    line-height: 1;
  }

  .action-btn.btn-primary {
    background: #434afa;
    color: white;
  }

  .action-btn.btn-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
  }

  .action-btn.btn-danger {
    background: #ef4444;
    color: white;
  }

  .action-btn.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
  }

  /* Poke button polish */
  .btn-poke { 
    background: linear-gradient(135deg,#ffc107,#ff9f1a);
    border: none;
    color: #212529;
    padding: 0.15rem 0.35rem;
    font-size: 0.7rem;
    border-radius: 4px;
    margin-right: 0.25rem;
    line-height: 1;
  }
  .btn-poke:hover { filter: brightness(0.95); }
  .poke-sent-badge {
    display:inline-block; margin-left:6px; padding:1px 6px; border-radius:10px; font-size:10px;
    background:#e7f1ff; color:#0d6efd; border:1px solid #cfe2ff;
  }

  .assign-users-grid {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: #fff;
  }
  .assign-users-grid .form-check {
    margin-bottom: 6px;
  }
  .assign-users-grid .form-check-input {
    width: 1rem;
    height: 1rem;
  }

  /* Compact modal forms */
  .form-compact .form-label { font-size: 0.85rem; margin-bottom: 0.2rem; }
  .form-compact .form-control,
  .form-compact .form-select { padding: 0.35rem 0.5rem; font-size: 0.875rem; }
  .form-compact .form-check-label { font-size: 0.875rem; }
  .form-compact .section-title { font-size: 0.8rem; color:#6c757d; margin: 0.2rem 0 0.4rem; font-weight: 600; }
  .form-compact .help-text { font-size: 0.75rem; color:#6c757d; }
  .modal-body.form-compact { padding-top: 0.75rem; }

  /* Slim colorful form accents */
  .form-accent {
    margin-bottom: 10px;
  }
  .chip-toggle {
    display:inline-flex; align-items:center; gap:8px; padding:4px 10px; 
     color:#1d4ed8; font-weight:600; font-size:12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  }
  .chip-toggle .form-check-input { margin-left:8px; width:36px; height:18px; }
  .chip-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .chip-row .title { font-weight:700; letter-spacing:.2px; color: #0f172a; font-size:0.9rem; }

    .chip-title{
        border-bottom: none !important;
        font-weight: 700;
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

  .form-control{
        background: #DFDFDF;
        font-size: 14px;
        border-radius: 3px;
    }

    label{
        font-weight: 700;
    }

    .file-upload-box {
  border: 3px dashed #434AFA;
  border-radius: 12px;
  background-color: #f3f4f6;
  cursor: pointer;
  transition: 0.3s;
}

.file-upload-box:hover {
  border: 3px solid blue;
}

.upload-icon {
  font-size: 42px;
  color: #434AFA;
}

.task-type-wrapper {
  display: inline-flex;
  align-items: right;
  border: 1px solid #434AFA;
  border-radius: 3px;
  overflow: hidden;
  font-size: 14px;
}

.task-type-title {
  background: #434AFA;
  color: #fff;
  padding: 6px 14px;
  font-weight: 500;
  white-space: nowrap;
}

.task-type-option {
  display: flex;
  align-items: right;
  gap: 6px;
  padding: 6px 14px;
  cursor: pointer;
  border-left: 1px solid #434AFA;
  background: #fff;
  color: #000;
  white-space: nowrap;
}

/* hide default radio */
.task-type-option input {
  accent-color: #4c6fff;
  cursor: pointer;
}

/* active (selected) state */
.task-type-option:has(input:checked) {
  background: #eef2ff;
  font-weight: 500;
}

.subHeader{
    display: flex !important;
    gap: 10px !important;
}

.btn-close{
    margin-top: 2px !important;
}

.select{
    background-color: #434AFA;
}

</style>
@endpush

@section('content')
<div class="container-fluid px-2">
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
      <label for="filter_user" class="form-label-modern">
        <i class="bi bi-person"></i> Assigned To
      </label>
      <select id="filter_user" class="form-control-modern">
        <option value="">All Users</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_status" class="form-label-modern">
        <i class="bi bi-tag"></i> Status
      </label>
      <select id="filter_status" class="form-control-modern">
        <option value="">All Statuses</option>
        <option value="done">Done</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_priority" class="form-label-modern">
        <i class="bi bi-exclamation-circle"></i> Priority
      </label>
      <select id="filter_priority" class="form-control-modern">
        <option value="">All Priorities</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_type" class="form-label-modern">
        <i class="bi bi-list-task"></i> Type
      </label>
      <select id="filter_type" class="form-control-modern">
        <option value="">All Types</option>
        <option value="task">Task</option>
        <option value="qc">QC</option>
        <option value="cp">Critical Path</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_date_from" class="form-label-modern">
        <i class="bi bi-calendar-event"></i> Created From
      </label>
      <input type="date" id="filter_date_from" class="form-control-modern">
    </div>
    <div class="mb-2">
      <label for="filter_date_to" class="form-label-modern">
        <i class="bi bi-calendar-check"></i> Created To
      </label>
      <input type="date" id="filter_date_to" class="form-control-modern">
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search tasks..." />
    </div>
    <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createTaskModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="data-table-card">
    <div class="table-responsive">
      <table class="table custom-table" id="taskstable">
        <thead>
          <tr>
            <th>Assigned To</th>
            <th>Customer</th>
            <th>Task Name</th>
            <th>Type</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="12" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Loading tasks...</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="table-range-meta" id="tasksRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"> 
                 <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                <div>
                    <div class="subHeader">
                        <div class="mb-2">
                            <label class="form-label" id="label_task_type">
                            </label>

                            <div class="task-type-wrapper">
                                <span class="task-type-title">Select opt...</span>

                                <label class="task-type-option">
                                <input type="radio" name="task_type" value="task" checked>
                                Task
                                </label>

                                <label class="task-type-option">
                                <input type="radio" name="task_type" value="qc">
                                Qc
                                </label>

                                <label class="task-type-option">
                                <input type="radio" name="task_type" value="cp">
                                CP
                                </label>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <form id="taskForm">
                <div class="modal-body form-compact">
                    @csrf
                    
                    <!-- Recurring (Top, colorful) -->
                    <div class="form-accent mb-2" id="recurrenceSection">
                        <div class="chip-row">
                            <div class="chip-title">Recurring</div>
                            <label class="chip-toggle">
                                Enable
                                <input class="form-check-input" type="checkbox" id="is_recurring">
                            </label>
                        </div>
                        <div id="recurrencePanel" class="mt-2" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Repeat</label>
                                    <select id="recurrence_type" class="form-select form-select-sm">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Every</label>
                                    <input type="number" min="1" value="1" id="recurrence_interval" class="form-control form-control-sm" placeholder="Interval">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">End date</label>
                                    <input type="date" id="recurrence_end_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div id="recurrence_weekly" class="mt-2" style="display:none;">
                                <label class="form-label">On days</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="mon" id="dow_mon"><label class="form-check-label" for="dow_mon">Mon</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="tue" id="dow_tue"><label class="form-check-label" for="dow_tue">Tue</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="wed" id="dow_wed"><label class="form-check-label" for="dow_wed">Wed</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="thu" id="dow_thu"><label class="form-check-label" for="dow_thu">Thu</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="fri" id="dow_fri"><label class="form-check-label" for="dow_fri">Fri</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sat" id="dow_sat"><label class="form-check-label" for="dow_sat">Sat</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sun" id="dow_sun"><label class="form-check-label" for="dow_sun">Sun</label></div>
                                </div>
                            </div>
                            <div id="recurrence_monthly" class="mt-2" style="display:none;">
                                <label class="form-label">On day of month</label>
                                <input type="number" id="recurrence_day_of_month" class="form-control form-control-sm" min="1" max="31" placeholder="1-31">
                            </div>
                            <div id="recurrence_yearly" class="mt-2" style="display:none;">
                                <label class="form-label">In months</label>
                                <div class="row g-1">
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="m_1"><label class="form-check-label" for="m_1">Jan</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="2" id="m_2"><label class="form-check-label" for="m_2">Feb</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="3" id="m_3"><label class="form-check-label" for="m_3">Mar</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="4" id="m_4"><label class="form-check-label" for="m_4">Apr</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="5" id="m_5"><label class="form-check-label" for="m_5">May</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="6" id="m_6"><label class="form-check-label" for="m_6">Jun</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="7" id="m_7"><label class="form-check-label" for="m_7">Jul</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="8" id="m_8"><label class="form-check-label" for="m_8">Aug</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="9" id="m_9"><label class="form-check-label" for="m_9">Sep</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="10" id="m_10"><label class="form-check-label" for="m_10">Oct</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="11" id="m_11"><label class="form-check-label" for="m_11">Nov</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="12" id="m_12"><label class="form-check-label" for="m_12">Dec</label></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Customer Select -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label" id="label_customer">Clients</label>
                                <select name="customer_id" id="customer_id" class="form-select form-select-sm" required>
                                    <option value="" class="select">Select Customer</option>
                                </select>
                            </div>
                        </div>

                        <!-- User Select -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" id="label_user">Assign To</label>
                                <div id="assignUsersContainer" class="assign-users-grid" data-input-name="user_ids[]"></div>
                                <small class="text-muted">Select one or more users to assign this task/QC.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Task Name -->
                    <div class="row">
                        <div class="mb-3 col-md-8">
                            <label for="task_name" class="form-label" id="label_task_name">Task Name</label>
                            <input type="text" name="task_name" id="task_name" class="form-control form-control-sm" required placeholder="Enter task name...">
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="due_date" class="form-label" id="label_due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class = "row">
                        <!-- Task Description -->
                        <div class="mb-3 col-8">
                            <label for="task" class="form-label" id="label_task_desc">Description</label>
                            <textarea name="task" id="task" class="form-control form-control-sm" rows="4" required placeholder="Enter..."></textarea>
                            
                            <div class="row mt-3">
                                <!-- Task Status Select -->
                                <div class="mb-3 col-4">
                                    <label for="task_status_id" class="form-label" id="label_task_status">Status</label>
                                    <select name="task_status_id" id="task_status_id" class="form-select form-select-sm" required>
                                        <option value="">Select Status</option>
                                    </select>
                                </div>

                                <!-- Task Priority Select -->
                                <div class="mb-3 col-4">
                                    <label for="task_priority_id" class="form-label" id="label_task_priority">Priority</label>
                                    <select name="task_priority_id" id="task_priority_id" class="form-select form-select-sm">
                                        <option value="">Select Priority</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                            <!-- Image Upload with Add More -->
                        <div class="mb-3 col-4">
                            <div class="file-upload-box text-center p-4">

                                <!-- Upload Icon -->
                                <div class="upload-icon mb-3">
                                <i class="bi bi-cloud-arrow-up" style ="color: #434AFA;"></i>
                                </div>

                                <!-- File Input -->
                                <input
                                type="file" name="images[]" id="task_images" class="d-none" multiple accept="image/*" style= "background: #DfDfDf;"
                                >

                                <!-- Browse Button -->
                                <button
                                type="button"
                                class="btn btn-sm btn-primary mb-2" style= "background: #434AFA;"
                                onclick="document.getElementById('task_images').click()"
                                >
                                Browse Files
                                </button>

                                <!-- Helper Text -->
                                <p style="color: black;" >Drop or Paste Here</p>

                            </div>

                            <!-- Preview -->
                            <div id="imagePreview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                            <div id="selectedImagesList" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class = "p-4">
                    <button type="submit" class="btn btn-primary" style = "background: #434AFa;" id="createTaskSubmitBtn">
                        Submit
                    </button>
                </div>

            </form>
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

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <div>
                    <div class="subHeader">
                        <div class="mb-2">
                            <div class="task-type-wrapper">
                                <span class="task-type-title">Select opt...</span>

                                <label class="task-type-option" for="edit_task_type_task">
                                <input type="radio" name="edit_task_type" id="edit_task_type_task" value="task" checked>
                                Task
                                </label>

                                <label class="task-type-option" for="edit_task_type_qc">
                                <input type="radio" name="edit_task_type" id="edit_task_type_qc" value="qc">
                                Qc
                                </label>

                                <label class="task-type-option" for="edit_task_type_cp">
                                <input type="radio" name="edit_task_type" id="edit_task_type_cp" value="cp">
                                CP
                                </label>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <form id="editTaskForm">
                <div class="modal-body form-compact">
                    <input type="hidden" id="edit_task_id">
                    
                    <!-- Recurring (Top of Edit) -->
                    <div class="form-accent mb-2" id="editRecurrenceSection">
                        <div class="chip-row">
                            <div class="chip-title">Recurring</div>
                            <label class="chip-toggle">
                                Enable
                                <input class="form-check-input" type="checkbox" id="edit_is_recurring">
                            </label>
                        </div>
                        <div id="edit_recurrencePanel" class="mt-2" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Repeat</label>
                                    <select id="edit_recurrence_type" class="form-select form-select-sm">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Every</label>
                                    <input type="number" min="1" value="1" id="edit_recurrence_interval" class="form-control form-control-sm" placeholder="Interval">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">End date</label>
                                    <input type="date" id="edit_recurrence_end_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div id="edit_recurrence_weekly" class="mt-2" style="display:none;">
                                <label class="form-label">On days</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="mon" id="edit_dow_mon"><label class="form-check-label" for="edit_dow_mon">Mon</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="tue" id="edit_dow_tue"><label class="form-check-label" for="edit_dow_tue">Tue</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="wed" id="edit_dow_wed"><label class="form-check-label" for="edit_dow_wed">Wed</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="thu" id="edit_dow_thu"><label class="form-check-label" for="edit_dow_thu">Thu</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="fri" id="edit_dow_fri"><label class="form-check-label" for="edit_dow_fri">Fri</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sat" id="edit_dow_sat"><label class="form-check-label" for="edit_dow_sat">Sat</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" value="sun" id="edit_dow_sun"><label class="form-check-label" for="edit_dow_sun">Sun</label></div>
                                </div>
                            </div>
                            <div id="edit_recurrence_monthly" class="mt-2" style="display:none;">
                                <label class="form-label">On day of month</label>
                                <input type="number" id="edit_recurrence_day_of_month" class="form-control form-control-sm" min="1" max="31" placeholder="1-31">
                            </div>
                            <div id="edit_recurrence_yearly" class="mt-2" style="display:none;">
                                <label class="form-label">In months</label>
                                <div class="row g-1">
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="edit_m_1"><label class="form-check-label" for="edit_m_1">Jan</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="2" id="edit_m_2"><label class="form-check-label" for="edit_m_2">Feb</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="3" id="edit_m_3"><label class="form-check-label" for="edit_m_3">Mar</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="4" id="edit_m_4"><label class="form-check-label" for="edit_m_4">Apr</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="5" id="edit_m_5"><label class="form-check-label" for="edit_m_5">May</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="6" id="edit_m_6"><label class="form-check-label" for="edit_m_6">Jun</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="7" id="edit_m_7"><label class="form-check-label" for="edit_m_7">Jul</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="8" id="edit_m_8"><label class="form-check-label" for="edit_m_8">Aug</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="9" id="edit_m_9"><label class="form-check-label" for="edit_m_9">Sep</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="10" id="edit_m_10"><label class="form-check-label" for="edit_m_10">Oct</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="11" id="edit_m_11"><label class="form-check-label" for="edit_m_11">Nov</label></div></div>
                                    <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" value="12" id="edit_m_12"><label class="form-check-label" for="edit_m_12">Dec</label></div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="edit_customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" id="edit_customer_id" class="form-select form-select-sm" required>
                                <option value="">Select Customer</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Assign Users <span class="text-danger">*</span></label>
                            <div id="editAssignUsersContainer" class="assign-users-grid" data-input-name="user_ids[]"></div>
                            <small class="text-muted">Select one or more assignees for this task.</small>
                        </div>
                    </div>

                  <div class="row">
                      <div class="col-md-8">
                            <label for="edit_task_name" class="form-label">Task Name</label>
                            <input type="text" name="task_name" id="edit_task_name" class="form-control form-control-sm" required placeholder="Enter task name...">
                        </div>

                        <div class="col-md-4">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" class="form-control form-control-sm">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="mt-3 col-md-8">
                            <label for="edit_task" class="form-label">Task Description</label>
                            <textarea name="task" id="edit_task" class="form-control form-control-sm" rows="3" required placeholder="Enter task details..."></textarea>

                            <div class="row">
                                <div class="mt-3 col-md-4">
                                    <label for="edit_task_status_id" class="form-label">Task Status <span class="text-danger">*</span></label>
                                    <select name="task_status_id" id="edit_task_status_id" class="form-select form-select-sm" required>
                                        <option value="">Select Status</option>
                                    </select>
                                </div>

                                <div class="mt-3 col-md-4">
                                    <label for="edit_task_priority_id" class="form-label">Task Priority</label>
                                    <select name="task_priority_id" id="edit_task_priority_id" class="form-select form-select-sm">
                                        <option value="">Select Priority</option>
                                    </select>
                                </div>
                           </div>
                        </div>
                        <!-- Image Upload for Edit -->

                        <div class="mt-3 col-4">
                            <div class="file-upload-box text-center p-4">

                                <!-- Upload Icon -->
                                <div class="upload-icon mb-3">
                                <i class="bi bi-cloud-arrow-up" style ="color: #434AFA;"></i>
                                </div>

                                <!-- File Input -->
                                <input
                                type="file" name="images[]" id="edit_task_images" class="d-none" multiple accept="image/*" style= "background: #DfDfDf;"
                                >

                                <!-- Browse Button -->
                                <button
                                type="button"
                                class="btn btn-sm btn-primary mb-2" style= "background: #434AFA;"
                                onclick="document.getElementById('edit_task_images').click()"
                                >
                                Browse Files
                                </button>

                                <!-- Helper Text -->
                                <p style="color: black;" >Drop or Paste Here</p>

                            </div>

                            <!-- Preview -->
                            <div id="editImagePreview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                            <div id="existingImages" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class = "p-4">
                    <button type="submit" class="btn btn-sm btn-primary" style = "background: #434AFa;">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $dueDateRow = $('#due_date').closest('.mb-3');
    const $editDueDateRow = $('#edit_due_date').closest('.mb-3');

    // Toggle filter panel
    $('#toggleFiltersBtn').on('click', function () {
        let $panel = $('.filterScroll');
        if ($panel.is(':visible')) {
            $panel.slideUp('fast');
            $(this).text('Show Filters ▼');
        } else {
            $panel.slideDown('fast');
            $(this).text('Hide Filters ▲');
        }
    });

    console.log('Task page loaded');
    
    // Update labels based on selected Task Type (Task/QC)
    function updateCreateLabels() {
        const type = $('input[name="task_type"]:checked').val() || 'task';
        const isQC = (type === 'qc');
        const isCP = (type === 'cp');

        const titleMap = {
            task: 'Create New Task',
            qc: 'Create New QC',
            cp: 'Create Critical Path Task'
        };

        const nameLabelMap = {
            task: 'Task Name ',
            qc: 'QC Name ',
            cp: 'CP Task Name '
        };

        const descLabelMap = {
            task: 'Task Description ',
            qc: 'QC Description ',
            cp: 'CP Task Description '
        };

        const statusLabelMap = {
            task: 'Task Status ',
            qc: 'QC Status ',
            cp: 'CP Task Status '
        };

        const priorityLabelMap = {
            task: 'Task Priority',
            qc: 'QC Priority',
            cp: 'CP Task Priority'
        };
        
        // Modal title
        $('#createTaskModalLabel').text(titleMap[type] || titleMap.task);
        
        // Field labels
        $('#label_task_name').contents().first()[0].textContent = nameLabelMap[type] || nameLabelMap.task;
        $('#label_task_desc').contents().first()[0].textContent = descLabelMap[type] || descLabelMap.task;
        $('#label_task_status').contents().first()[0].textContent = statusLabelMap[type] || statusLabelMap.task;
        $('#label_task_priority').contents().first()[0].textContent = priorityLabelMap[type] || priorityLabelMap.task;
        
        // Submit button
        const submitLabel = type === 'qc' ? 'Create QC' : (type === 'cp' ? 'Create CP Task' : 'Submit');
        $('#createTaskSubmitBtn').html(`${submitLabel}`);

        // Recurrence visibility
        if (isQC || isCP) {
            $('#recurrenceSection').hide();
            $('#is_recurring').prop('checked', false).trigger('change');
            $('#is_recurring').prop('disabled', true);
            $('#recurrencePanel').hide();
        } else {
            $('#recurrenceSection').show();
            $('#is_recurring').prop('disabled', false);
            if ($('#is_recurring').is(':checked')) {
                $('#recurrencePanel').show();
            }
        }

        // Due date visibility: hide for QC, show for others
        if (isQC) {
            $dueDateRow.hide();
            $('#due_date').val('');
        } else {
            $dueDateRow.show();
        }
    }
    
    // Bind change listeners for Task/QC radios (create modal)
    $(document).on('change', 'input[name="task_type"]', updateCreateLabels);

    // Update edit modal recurrence visibility
    window.updateEditTypeState = function() {
        const type = $('input[name="edit_task_type"]:checked').val() || 'task';
        const isQC = (type === 'qc');
        const isCP = (type === 'cp');

        if (isQC || isCP) {
            $('#editRecurrenceSection').hide();
            $('#edit_is_recurring').prop('checked', false).trigger('change');
            $('#edit_is_recurring').prop('disabled', true);
            $('#edit_recurrencePanel').hide();
            const $editDueDateRow = $('#edit_due_date').closest('.mb-3');
            $editDueDateRow.hide();
            $('#edit_due_date').val('');
        } else {
            $('#editRecurrenceSection').show();
            $('#edit_is_recurring').prop('disabled', false);
            if ($('#edit_is_recurring').is(':checked')) {
                $('#edit_recurrencePanel').show();
            }
            const $editDueDateRow = $('#edit_due_date').closest('.mb-3');
            $editDueDateRow.show();
        }
    };

    $(document).on('change', 'input[name="edit_task_type"]', updateEditTypeState);
    
    // Ensure correct labels when modal opens
    $('#createTaskModal').on('shown.bs.modal', function () {
        updateCreateLabels();
    });
    
    // Initialize labels on page load as well
    updateCreateLabels();
    
    // Setup CSRF token for all AJAX requests
    // Simple HTML escape used for clickable truncated fields
    function escapeHtml(text) {
        return (text || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderAssignedUsers(task) {
        const assigned = Array.isArray(task.assigned_users) ? task.assigned_users : [];
        if (assigned.length) {
            return assigned.map(user => `<span class="badge bg-light text-dark border me-1">${escapeHtml(user.name || 'User')}</span>`).join('');
        }
        if (task.user && task.user.name) {
            return `<span class="badge bg-light text-dark border">${escapeHtml(task.user.name)}</span>`;
        }
        return 'N/A';
    }

    // Modal open for task name/desc
    $(document).on('click', '.task-name-link', function(e){
        e.preventDefault();
        const full = $(this).data('full') || '';
        showFullTextModal('Task Name', full);
    });
    $(document).on('click', '.task-desc-link', function(e){
        e.preventDefault();
        const full = $(this).data('full') || '';
        showFullTextModal('Task Description', full);
    });

    function showFullTextModal(title, text) {
        let modalEl = document.getElementById('fullTaskTextModal');
        if (!modalEl) {
            const html = `
            <div class="modal fade" id="fullTaskTextModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="fullTaskTextModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <pre id="fullTaskTextBody" class="mb-0" style="white-space: pre-wrap; word-break: break-word;"></pre>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            modalEl = document.getElementById('fullTaskTextModal');
        }
        $('#fullTaskTextModalLabel').text(title);
        $('#fullTaskTextBody').text(text);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    let allUsers = [];

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    function renderUserCheckboxes(containerSelector, selectedIds = []) {
        const container = $(containerSelector);
        if (!container.length) return;
        const inputName = container.data('input-name') || 'user_ids[]';
        if (!allUsers.length) {
            container.html('<div class="text-muted small">No users available.</div>');
            return;
        }
        const selectedSet = new Set((selectedIds || []).map(String));
        const html = allUsers.map(user => {
            const id = String(user.id);
            const checked = selectedSet.has(id) ? 'checked' : '';
            return `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${id}" id="${containerSelector.replace('#','')}_${id}" name="${inputName}" ${checked}>
                    <label class="form-check-label" for="${containerSelector.replace('#','')}_${id}">${user.name}</label>
                </div>`;
        }).join('');
        container.html(html);
    }

    // Load users dropdown / checkboxes
    $.get("{{ route('task.users') }}", function(data) {
        console.log('Users loaded:', data);
        allUsers = Array.isArray(data) ? data : [];
        renderUserCheckboxes('#assignUsersContainer');
        renderUserCheckboxes('#editAssignUsersContainer');

        let filterOptions = '<option value="">All Users</option>';
        let creatorOptions = '<option value="">All Creators</option>';
        if (allUsers.length) {
            $.each(allUsers, function(i, user) {
                filterOptions += `<option value="${user.id}">${user.name}</option>`;
                creatorOptions += `<option value="${user.id}">${user.name}</option>`;
            });
        } else {
            $('#assignUsersContainer').html('<div class="text-muted small">No users available.</div>');
        }
        $('#filter_user').html(filterOptions);
        $('#filter_creator').html(creatorOptions);
    }).fail(function(xhr, status, error) {
        console.error('Error loading users:', xhr.responseText, status, error);
        $('#assignUsersContainer').html('<div class="text-danger small">Unable to load users</div>');
    });

    // Load customers dropdown
    $.get("{{ route('task.customers') }}", function(data) {
        console.log('Customers loaded:', data);
        let options = '<option value="">Select Customer</option>';
        let filterOptions = '<option value="">All Customers</option>';
        if (data && data.length > 0) {
            $.each(data, function(i, customer) {
                options += `<option value="${customer.id}">${customer.name}</option>`;
                filterOptions += `<option value="${customer.id}">${customer.name}</option>`;
            });
        } else {
            options += '<option value="">No customers found</option>';
        }
        $('#customer_id').html(options);
        $('#edit_customer_id').html(options); // Populate edit modal
        $('#filter_customer').html(filterOptions);
    }).fail(function(xhr, status, error) {
        console.error('Error loading customers:', xhr.responseText, status, error);
        $('#customer_id').html('<option value="">Unable to load customers</option>');
    });

    // Load task statuses dropdown
    $.get("{{ route('task.statuses') }}", function(data) {
        console.log('Task statuses loaded:', data);
        let options = '<option value="">Select Status</option>';
        let filterOptions = '<option value="">All Statuses</option><option value="done">Done</option>';
        if (data && data.length > 0) {
            $.each(data, function(i, status) {
                options += `<option value="${status.id}">${status.name}</option>`;
                filterOptions += `<option value="${status.id}">${status.name}</option>`;
            });
        } else {
            options += '<option value="">No statuses found</option>';
        }
        $('#task_status_id').html(options);
        $('#edit_task_status_id').html(options); // Populate edit modal
        $('#filter_status').html(filterOptions);

        // Set default to Pending if present
        const pendingOpt = $('#task_status_id option').filter(function(){ return $(this).text().trim().toLowerCase() === 'pending'; }).first();
        if (pendingOpt.length) {
            $('#task_status_id').val(pendingOpt.val());
        }
    }).fail(function(xhr, status, error) {
        console.error('Error loading task statuses:', xhr.responseText, status, error);
        $('#task_status_id').html('<option value="">Unable to load statuses</option>');
    });

    // Load task priorities dropdown
    $.get("{{ route('task.priorities') }}", function(data) {
        console.log('Task priorities loaded:', data);
        let options = '<option value="">Select Priority</option>';
        let filterOptions = '<option value="">All Priorities</option>';
        if (data && data.length > 0) {
            $.each(data, function(i, priority) {
                options += `<option value="${priority.id}">${priority.name}</option>`;
                filterOptions += `<option value="${priority.id}">${priority.name}</option>`;
            });
        } else {
            options += '<option value="">No priorities found</option>';
        }
        $('#task_priority_id').html(options);
        $('#edit_task_priority_id').html(options); // Populate edit modal
        $('#filter_priority').html(filterOptions);

        // Set default to High if present
        const highOpt = $('#task_priority_id option').filter(function(){ return $(this).text().trim().toLowerCase() === 'high'; }).first();
        if (highOpt.length) {
            $('#task_priority_id').val(highOpt.val());
        }
    }).fail(function(xhr, status, error) {
        console.error('Error loading task priorities:', xhr.responseText, status, error);
        $('#task_priority_id').html('<option value="">Unable to load priorities</option>');
    });

    // Task loading and rendering functions
    let currentPage = 1;
    let allTasks = [];

    // Load summary stats
    function loadSummaryStats() {
      if (!allTasks || allTasks.length === 0) return;
      
      const total = allTasks.length;
      const inProgress = allTasks.filter(t => {
        const statusName = (t.status?.name || '').toLowerCase();
        return statusName.includes('progress') || statusName.includes('ongoing');
      }).length;
      const completed = allTasks.filter(t => t.is_done || (t.status?.name || '').toLowerCase().includes('complete')).length;
      const pending = allTasks.filter(t => (t.status?.name || '').toLowerCase().includes('pending')).length;
      const today = allTasks.filter(t => {
        if (!t.created_at) return false;
        const createdDate = new Date(t.created_at).toDateString();
        const todayDate = new Date().toDateString();
        return createdDate === todayDate;
      }).length;

      $('#totalTasks').text(total);
      $('#inProgressTasks').text(inProgress);
      $('#completedTasks').text(completed);
      $('#pendingTasks').text(pending);
      $('#todayTasks').text(today);
    }

    function loadTasks(page = 1) {
      $('#taskstable tbody').html(`
        <tr>
          <td colspan="12" class="loading-state">
            <i class="bi bi-arrow-repeat"></i>
            <p class="mt-2 mb-0">Loading tasks...</p>
          </td>
        </tr>
      `);

      $.ajax({
        url: "{{ route('task.fetch') }}",
        type: "GET",
        dataType: 'json',
        success: function(data) {
          allTasks = data || [];
          
          // Apply filters
          let filteredTasks = applyFilters(allTasks);
          
          // Update summary stats
          loadSummaryStats();
          
          // Render table
          renderTasksTable(filteredTasks, page);
        },
        error: function(xhr, status, error) {
          console.error('Error loading tasks:', xhr.responseText, status, error);
          $('#taskstable tbody').html(`
            <tr>
              <td colspan="12" class="text-danger text-center py-4">
                <i class="bi bi-exclamation-triangle"></i>
                <p class="mt-2">Failed to load tasks. Please try again.</p>
              </td>
            </tr>
          `);
        }
      });
    }

    function applyFilters(tasks) {
      let filtered = [...tasks];
      
      const filterUser = $('#filter_user').val();
      const filterStatus = $('#filter_status').val();
      const filterPriority = $('#filter_priority').val();
      const filterType = $('#filter_type').val();
      const filterDateFrom = $('#filter_date_from').val();
      const filterDateTo = $('#filter_date_to').val();
      const searchTerm = $('#search').val().toLowerCase();
      
      if (filterUser) {
        filtered = filtered.filter(task => {
          const assigned = Array.isArray(task.assigned_users) ? task.assigned_users : [];
          if (assigned.some(user => String(user.id) === String(filterUser))) {
            return true;
          }
          return String(task.user_id || '') === String(filterUser);
        });
      }
      
      if (filterStatus) {
        if (filterStatus === 'done') {
          filtered = filtered.filter(task => task.is_done == 1 || task.is_done == true);
        } else {
          filtered = filtered.filter(task => task.task_status_id == filterStatus && (!task.is_done || task.is_done == 0 || task.is_done == false));
        }
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
      
      if (searchTerm) {
        filtered = filtered.filter(task => {
          const taskName = (task.task_name || '').toLowerCase();
          const taskDesc = (task.task || '').toLowerCase();
          const customerName = (task.customer?.name || '').toLowerCase();
          const creatorName = (task.creator?.name || '').toLowerCase();
          
          return taskName.includes(searchTerm) || 
                 taskDesc.includes(searchTerm) || 
                 customerName.includes(searchTerm) ||
                 creatorName.includes(searchTerm);
        });
      }
      
      return filtered;
    }

    function isDateOverdue(dateString) {
      if (!dateString) return false;
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      
      let due;
      // Handle YYYY-MM-DD string as local date
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

    function renderTasksTable(tasks, page = 1) {
      const perPage = 10;
      const start = (page - 1) * perPage;
      const end = start + perPage;
      const paginatedTasks = tasks.slice(start, end);
      
      let overdueCount = 0;
      let html = '';
      
      if (paginatedTasks.length === 0) {
        html = `<tr>
          <td colspan="12" class="empty-state">
            <i class="bi bi-inbox"></i>
            <h5>No Tasks Found</h5>
            <p>No tasks available at the moment.</p>
          </td>
        </tr>`;
      } else {
        paginatedTasks.forEach(function(task) {
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

          // Status text
          let statusHtml = '';
          if (task.is_done) {
            statusHtml = '<span class="fw-bold text-success">Done</span>';
          } else if (task.status) {
            let statusColor = task.status.color || '#6c757d';
            statusHtml = `<span class="fw-bold" style="color: ${statusColor}">${task.status.name}</span>`;
          } else {
            statusHtml = '<span class="fw-bold text-warning">Pending</span>';
          }
          
          // Priority text
          let priorityHtml = 'N/A';
          if (task.priority) {
            let priorityColor = task.priority.color || '#6c757d';
            priorityHtml = `<span class="fw-bold" style="color: ${priorityColor}">${task.priority.name}</span>`;
          }
          
          // Assigned users
          let assignedTo = 'N/A';
          if (task.assigned_users && task.assigned_users.length > 0) {
            assignedTo = task.assigned_users.map(u => u.name).join(', ');
          } else if (task.user) {
            assignedTo = task.user.name;
          }
          
          // Type color
          let typeBadge = task.task_type || 'task';
          let typeColor = '#6c757d';
          if (typeBadge === 'qc') typeColor = '#0dcaf0';
          else if (typeBadge === 'cp') typeColor = '#dc3545';
          else typeColor = '#0d6efd';
          
          // Due date
          let dueDateRaw = task.due_date ? new Date(task.due_date).toLocaleDateString('en-GB') : 'N/A';
          let dueDate = isOverdue 
              ? `<span class="text-danger fw-bold" title="Overdue">${dueDateRaw}</span>` 
              : dueDateRaw;
          
          // Created at
          let createdAt = task.created_at ? new Date(task.created_at).toLocaleDateString('en-GB') : 'N/A';
          
          // Done toggle button
          let doneBtn = task.is_done 
            ? `<button class="btn btn-sm btn-secondary action-btn" onclick="toggleDone(${task.id})" title="Mark as Pending"><i class="bi bi-x-circle"></i></button>`
            : `<button class="btn btn-sm btn-success action-btn" onclick="toggleDone(${task.id})" title="Mark as Done"><i class="bi bi-check-circle"></i></button>`;
          
          html += `
            <tr class="${rowClass}">
              <td>
                <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${assignedTo}">
                  ${assignedTo.length > 7 ? assignedTo.substring(0, 7) + '...' : assignedTo}
                </a>
              </td>
              <td>
                <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.customer?.name || 'N/A'}">
                  ${(task.customer?.name || 'N/A').length > 7 ? (task.customer?.name || 'N/A').substring(0, 7) + '...' : (task.customer?.name || 'N/A')}
                </a>
              </td>
              <td>
                <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-decoration-none" style="color: #212529;" title="${task.task_name || ''}">
                  ${(task.task_name || 'N/A').length > 7 ? (task.task_name || 'N/A').substring(0, 7) + '...' : (task.task_name || 'N/A')}
                </a>
              </td>
              <td><span class="fw-bold" style="color: ${typeColor}">${typeBadge.toUpperCase()}</span></td>
              <td>${priorityHtml}</td>
              <td>${statusHtml}</td>
              <td>${dueDate}</td>
              <td>
                <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.creator?.name || 'N/A'}">
                  ${(task.creator?.name || 'N/A').length > 7 ? (task.creator?.name || 'N/A').substring(0, 7) + '...' : (task.creator?.name || 'N/A')}
                </a>
              </td>
              <td>${createdAt}</td>
              <td>
                <button class="btn btn-sm btn-primary action-btn" onclick="editTask(${task.id})" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                ${doneBtn}
                <button class="btn btn-sm btn-poke" onclick="pokeTask(this, ${task.id})" title="Poke Assigned User">
                  <i class="bi bi-bell"></i>
                </button>
                <button class="btn btn-sm btn-danger action-btn" onclick="deleteTask(${task.id})" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          `;
        });
      }
      
      console.log('Total overdue tasks on this page:', overdueCount);
      
      // Debug: Total overdue in entire filtered list
      const totalOverdue = tasks.filter(t => {
           const sName = (t.status && t.status.name) ? t.status.name.toLowerCase() : '';
           const isDone = t.is_done || sName === 'done' || sName.includes('completed') || sName.includes('complete');
           return t.due_date && !isDone && isDateOverdue(t.due_date);
      }).length;
      console.log('Total overdue in entire filtered list:', totalOverdue);

      $('#taskstable tbody').html(html);
      
      // Render pagination
      const totalPages = Math.ceil(tasks.length / perPage);
      renderPagination(page, totalPages);
      
      // Update range info
      const from = tasks.length > 0 ? start + 1 : 0;
      const to = Math.min(end, tasks.length);
      updateRangeInfo(from, to, tasks.length);
    }

    function renderPagination(current, last) {
      let pagination = $('#paginationLinks');
      pagination.empty();
      
      if (last <= 1) return;
      
      pagination.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${current - 1}">
            <i class="bi bi-chevron-left"></i> Previous
          </a>
        </li>
      `);
      
      pagination.append(`
        <li class="page-item active">
          <span class="page-link">${current} / ${last}</span>
        </li>
      `);
      
      pagination.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${current + 1}">
            Next <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      `);
    }

    function updateRangeInfo(from, to, total) {
      const $info = $('#tasksRangeInfo');
      if (!$info.length) return;
      
      const totalValue = Number(total) || 0;
      const safeStart = totalValue === 0 ? 0 : (Number(from) || 1);
      const safeEnd = totalValue === 0 ? 0 : (Number(to) || safeStart);
      
      $info.text(`Showing ${safeStart}-${safeEnd} from ${totalValue} data`);
    }

    // Event handlers
    $(document).on('click', '#paginationLinks .page-link', function(e) {
      e.preventDefault();
      const page = $(this).data('page');
      if (page && page > 0) {
        currentPage = page;
        const filtered = applyFilters(allTasks);
        renderTasksTable(filtered, page);
      }
    });

    $(document).on('change', '#filter_user, #filter_status, #filter_priority, #filter_type, #filter_date_from, #filter_date_to', function() {
      currentPage = 1;
      const filtered = applyFilters(allTasks);
      renderTasksTable(filtered, currentPage);
    });

    let searchTimeout;
    $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        currentPage = 1;
        const filtered = applyFilters(allTasks);
        renderTasksTable(filtered, currentPage);
      }, 300);
    });

    // Load tasks on page load
    loadTasks();

    // Ensure defaults when modal opens
    $('#createTaskModal').on('shown.bs.modal', function(){
        const pendingOpt = $('#task_status_id option').filter(function(){ return $(this).text().trim().toLowerCase() === 'pending'; }).first();
        if (pendingOpt.length) $('#task_status_id').val(pendingOpt.val());
        const highOpt = $('#task_priority_id option').filter(function(){ return $(this).text().trim().toLowerCase() === 'high'; }).first();
        if (highOpt.length) $('#task_priority_id').val(highOpt.val());
    });

    // Store selected images
    let selectedImages = [];
    let selectedEditImages = [];

    // Image preview functionality - add to selected images
    $('#task_images').on('change', function(e) {
        const files = e.target.files;
        if (files.length > 0) {
            Array.from(files).forEach((file) => {
                if (file.type.startsWith('image/')) {
                    // Check if file already exists
                    const exists = selectedImages.some(img => img.name === file.name && img.size === file.size);
                    if (!exists) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            selectedImages.push({
                                file: file,
                                name: file.name,
                                size: file.size,
                                preview: e.target.result
                            });
                            updateImagePreview();
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
            // Clear the input so user can select more
            $(this).val('');
        }
    });

    // Update image preview display
    function updateImagePreview() {
        const preview = $('#imagePreview');
        const selectedList = $('#selectedImagesList');
        
        if (selectedImages.length === 0) {
            preview.html('<small class="text-muted">No images selected yet</small>');
            selectedList.empty();
            return;
        }
        
        preview.html(`<small class="text-muted d-block mb-2">Selected images (${selectedImages.length}):</small>`);
        selectedList.empty();
        
        selectedImages.forEach((img, index) => {
            preview.append(`
                <div class="d-inline-block me-2 mb-2 position-relative" id="img-preview-${index}">
                    <img src="${img.preview}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute" style="top: 0; right: 0; padding: 2px 5px; font-size: 0.7rem;" onclick="removeImage(${index})" title="Remove">
                        <i class="bi bi-x"></i>
                    </button>
                    <small class="d-block text-truncate" style="max-width: 80px;" title="${img.name}">${img.name}</small>
                </div>
            `);
        });
    }

    // Handle pasted images utility
    function filesFromClipboard(event) {
        const items = (event.clipboardData || event.originalEvent?.clipboardData)?.items || [];
        const files = [];
        for (let i = 0; i < items.length; i++) {
            const it = items[i];
            if (it.kind === 'file') {
                const file = it.getAsFile();
                if (file && file.type && file.type.startsWith('image/')) {
                    files.push(file);
                }
            }
        }
        return files;
    }

    function addPastedFilesToSelected(files) {
        if (!files || files.length === 0) return;
        files.forEach((file) => {
            const exists = selectedImages.some(img => img.name === file.name && img.size === file.size && img.lastModified === file.lastModified);
            if (!exists) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    selectedImages.push({ file, name: file.name, size: file.size, preview: e.target.result });
                    updateImagePreview();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function addPastedFilesToSelectedEdit(files) {
        if (!files || files.length === 0) return;
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                selectedEditImages.push({ file, name: file.name, size: file.size, preview: e.target.result });
                // Render immediately into edit preview
                $('#editImagePreview').append(`
                    <div class="d-inline-block me-2 mb-2" style="position: relative;">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
    }

    // Paste on create image preview
    $('#imagePreview').on('paste', function(event) {
        const files = filesFromClipboard(event);
        if (files.length > 0) {
            event.preventDefault();
            addPastedFilesToSelected(files);
        }
    });

    // Paste on edit image preview
    $('#editImagePreview').on('paste', function(event) {
        const files = filesFromClipboard(event);
        if (files.length > 0) {
            event.preventDefault();
            addPastedFilesToSelectedEdit(files);
        }
    });

    // Remove image from selection
    window.removeImage = function(index) {
        selectedImages.splice(index, 1);
        updateImagePreview();
    };

    // Add More Images button - trigger file input again
    $('#addMoreImagesBtn').on('click', function() {
        $('#task_images').click();
    });

    // Handle form submission with file uploads
    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Submitting task form');
        const formData = new FormData(this);
        
        // Add all selected images
        selectedImages.forEach((img, index) => {
            formData.append('images[]', img.file);
        });
        
        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Add task_type from radio button
        const taskType = $('input[name="task_type"]:checked').val();
        formData.set('task_type', taskType || 'task');

        // Recurrence fields
        const isRecurring = $('#is_recurring').is(':checked');
        formData.append('is_recurring', isRecurring ? 1 : 0);
        if (isRecurring) {
            formData.append('recurrence_type', $('#recurrence_type').val());
            formData.append('recurrence_interval', $('#recurrence_interval').val() || 1);
            const dows = [];
            ['mon','tue','wed','thu','fri','sat','sun'].forEach(function(k){
                if ($('#dow_'+k).is(':checked')) dows.push(k);
            });
            if (dows.length) { dows.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
            const dom = $('#recurrence_day_of_month').val();
            if (dom) formData.append('recurrence_day_of_month', dom);
            const months = [];
            for (let i=1;i<=12;i++){ if ($('#m_'+i).is(':checked')) months.push(i); }
            if (months.length) { months.forEach(v => formData.append('recurrence_months[]', v)); }
            const endDate = $('#recurrence_end_date').val();
            if (endDate) formData.append('recurrence_end_date', endDate);
        }
        
        $.ajax({
            url: "{{ route('task.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Task created:', response);
                alert(response.message || 'Task created successfully!');
                $('#createTaskModal').modal('hide');
                $('#taskForm')[0].reset();
                selectedImages = [];
                updateImagePreview();
                loadTasks();
            },
            error: function(xhr, status, error) {
                console.error('Error creating task:', xhr.responseText, status, error);
                var errorMsg = 'Failed to create task';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                alert('Error: ' + errorMsg);
            }
        });
    });

    // Reset form when modal is closed
    $('#createTaskModal').on('hidden.bs.modal', function() {
        $('#taskForm')[0].reset();
        selectedImages = [];
        updateImagePreview();
    });

    // Recurrence UI logic
    $('#is_recurring').on('change', function(){
        $('#recurrencePanel').toggle(this.checked);
    });
    $('#recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#recurrence_weekly, #recurrence_monthly, #recurrence_yearly').hide();
        if (t === 'weekly') $('#recurrence_weekly').show();
        if (t === 'monthly') $('#recurrence_monthly').show();
        if (t === 'yearly') $('#recurrence_yearly').show();
    }).trigger('change');

    // Edit recurrence UI logic
    $('#edit_is_recurring').on('change', function(){
        $('#edit_recurrencePanel').toggle(this.checked);
    });
    $('#edit_recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#edit_recurrence_weekly, #edit_recurrence_monthly, #edit_recurrence_yearly').hide();
        if (t === 'weekly') $('#edit_recurrence_weekly').show();
        if (t === 'monthly') $('#edit_recurrence_monthly').show();
        if (t === 'yearly') $('#edit_recurrence_yearly').show();
    });

    // Handle image load errors gracefully
    window.handleImageError = function(imageId, taskId, imageIdNum) {
        const img = document.getElementById(imageId);
        const placeholder = document.getElementById(imageId + '-placeholder');
        
        if (img) {
            img.style.display = 'none';
            // Try alternative URL if available
            if (imageIdNum && img.src.includes('/storage/')) {
                const altUrl = `/task/${taskId}/image/${imageIdNum}`;
                img.src = altUrl;
                img.style.display = 'block';
                img.onerror = function() {
                    this.style.display = 'none';
                    if (placeholder) placeholder.style.display = 'block';
                };
            } else {
                if (placeholder) placeholder.style.display = 'block';
            }
        }
    };

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
      const task = allTasks.find(t => t.id === id);
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
                    imagesDiv.append(`
                        <a href="${imageUrl}" target="_blank" class="d-block border rounded overflow-hidden" style="width: 80px; height: 80px;">
                            <img src="${imageUrl}" class="w-100 h-100" style="object-fit: cover;" alt="Task Image"
                                onerror="handleViewImageError(this, ${task.id}, ${img.id || 'null'}, '${img.image_path || ''}')">
                        </a>
                    `);
                }
            });
            imagesContainer.show();
        } else {
            imagesContainer.hide();
        }

        $('#viewTaskModal').modal('show');
      }
    };

    // Delete task image
    window.deleteTaskImage = function(taskId, imageId, containerId) {
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        $.ajax({
            url: `/task/${taskId}/image/${imageId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Remove image container from DOM
                const container = document.getElementById(containerId);
                if (container) {
                    container.style.transition = 'opacity 0.3s';
                    container.style.opacity = '0';
                    setTimeout(() => {
                        container.remove();
                    }, 300);
                }
                alert('Image deleted successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error deleting image:', xhr.responseText, status, error);
                alert('Error: Failed to delete image');
            }
        });
    };

    // Load tasks with filters
    window.loadTasks = function() {
        console.log('Loading tasks...');
        $.ajax({
            url: "{{ route('task.fetch') }}",
            type: "GET",
            dataType: 'json',
            success: function(data) {
                console.log('Tasks loaded:', data);
                
                // Handle error response
                if (data.error) {
                    console.error('Error from server:', data.message);
                    $('#taskTableBody').html(`<tr><td colspan="12" class="text-center text-danger">Error: ${data.message || 'Failed to load tasks'}</td></tr>`);
                    return;
                }
                
                // Ensure data is an array
                if (!Array.isArray(data)) {
                    console.error('Invalid data format:', data);
                    $('#taskTableBody').html('<tr><td colspan="12" class="text-center text-danger">Invalid data format received</td></tr>');
                    return;
                }
                
                // Apply filters
                let filterUser = $('#filter_user').val();
                let filterCreator = $('#filter_creator').val();
                let filterCustomer = $('#filter_customer').val();
                let filterStatus = $('#filter_status').val();
                let filterPriority = $('#filter_priority').val();
                let filterType = $('#filter_type').val();
                let filterDateFrom = $('#filter_date_from').val();
                let filterDateTo = $('#filter_date_to').val();
                
                if (filterUser) {
                    data = data.filter(task => {
                        const assigned = Array.isArray(task.assigned_users) ? task.assigned_users : [];
                        if (assigned.some(user => String(user.id) === String(filterUser))) {
                            return true;
                        }
                        return String(task.user_id || '') === String(filterUser);
                    });
                }

                if (filterCreator) {
                    data = data.filter(task => {
                        const creatorId = task.created_by ?? (task.creator ? task.creator.id : null);
                        return String(creatorId) === String(filterCreator);
                    });
                }

                if (filterCustomer) {
                    data = data.filter(task => task.customer_id == filterCustomer);
                }
                
                if (filterStatus) {
                    if (filterStatus === 'done') {
                        data = data.filter(task => task.is_done == 1 || task.is_done == true);
                    } else {
                        data = data.filter(task => task.task_status_id == filterStatus && (!task.is_done || task.is_done == 0 || task.is_done == false));
                    }
                }

                if (filterPriority) {
                    data = data.filter(task => {
                        const priorityId = task.task_priority_id ?? (task.priority ? task.priority.id : null);
                        return String(priorityId || '') === String(filterPriority);
                    });
                }

                if (filterType) {
                    data = data.filter(task => (task.task_type || 'task') === filterType);
                }

                if (filterDateFrom) {
                    const fromDate = new Date(filterDateFrom);
                    data = data.filter(task => {
                        if (!task.created_at) return false;
                        return new Date(task.created_at) >= fromDate;
                    });
                }

                if (filterDateTo) {
                    const toDate = new Date(filterDateTo);
                    toDate.setHours(23,59,59,999);
                    data = data.filter(task => {
                        if (!task.created_at) return false;
                        return new Date(task.created_at) <= toDate;
                    });
                }
                
                let html = '';
                if (!data || data.length === 0) {
                    html = '<tr><td colspan="12" class="text-center">No tasks found</td></tr>';
                } else {
                    $.each(data, function(index, task) {
                        // Prioritize is_done field over task_status
                        let statusBadge = '';
                        if (task.is_done) {
                            // If marked as done, always show Done regardless of status
                            statusBadge = '<span class="badge bg-success">Done</span>';
                        } else if (task.status) {
                            // If not done, show the task status with color
                            let statusColor = task.status.color || '#6c757d';
                            statusBadge = `<span class="badge" style="background-color: ${statusColor}">${task.status.name}</span>`;
                        } else {
                            // Fallback if no status is set
                            statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
                        }
                        
                        let doneButton = task.is_done
                            ? `<button class="btn btn-sm btn-secondary" onclick="toggleDone(${task.id})" title="Mark as Pending"><i class="bi bi-x-circle"></i></button>`
                            : `<button class="btn btn-sm btn-success" onclick="toggleDone(${task.id})" title="Mark as Done"><i class="bi bi-check-circle"></i></button>`;

                        // Priority badge
                        let priorityBadge = '';
                        if (task.priority) {
                            let priorityColor = task.priority.color || '#6c757d';
                            priorityBadge = `<span class="badge" style="background-color: ${priorityColor}">${task.priority.name}</span>`;
                        } else {
                            priorityBadge = '<span class="badge bg-secondary">None</span>';
                        }

                        // Format date to show only date without time
                        let createdDate = task.created_at ? new Date(task.created_at).toLocaleDateString('en-GB') : 'N/A';
                        let dueDateFormatted = task.due_date ? new Date(task.due_date).toLocaleDateString('en-GB') : '-';
                        
                        // Assigned users text
                        let assignedToText = 'N/A';
                        if (task.assigned_users && task.assigned_users.length > 0) {
                            assignedToText = task.assigned_users.map(u => u.name).join(', ');
                        } else if (task.user) {
                            assignedToText = task.user.name;
                        }

                        const rawName = (task.task_name || '').toString();
                        const shortName = rawName.length > 7 ? rawName.substring(0, 7) + '...' : rawName;
                        
                        // Task Type badge
                        const taskType = task.task_type || 'task';
                        let typeBadge = '<span class="badge bg-primary">Task</span>';
                        if (taskType === 'qc') {
                            typeBadge = '<span class="badge bg-info">QC</span>';
                        } else if (taskType === 'cp') {
                            typeBadge = '<span class="badge bg-warning text-dark">CP</span>';
                        }
                        
                        html += `
                            <tr class="${task.is_done ? 'table-success' : ''}">
                                
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${assignedToText}">
                                        ${assignedToText.length > 7 ? assignedToText.substring(0, 7) + '...' : assignedToText}
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.customer ? task.customer.name : 'N/A'}">
                                        ${(task.customer ? task.customer.name : 'N/A').length > 7 ? (task.customer ? task.customer.name : 'N/A').substring(0, 7) + '...' : (task.customer ? task.customer.name : 'N/A')}
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-primary fw-bold text-decoration-none" title="${escapeHtml(rawName)}">
                                        ${escapeHtml(shortName || 'N/A')}
                                    </a>
                                </td>
                                <td>${typeBadge}</td>
                                <td>${priorityBadge}</td>
                                <td>${statusBadge}</td>
                                <td>${dueDateFormatted}</td>
                                <td>
                                    <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none" title="${task.creator ? task.creator.name : 'N/A'}">
                                        ${(task.creator ? task.creator.name : 'N/A').length > 7 ? (task.creator ? task.creator.name : 'N/A').substring(0, 7) + '...' : (task.creator ? task.creator.name : 'N/A')}
                                    </a>
                                </td>
                                <td>${createdDate}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editTask(${task.id})" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    ${doneButton}
                                    <button class="btn btn-sm btn-poke" onclick="pokeTask(this, ${task.id})" title="Poke Assigned User">
                                        <i class="bi bi-bell"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#taskTableBody').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error loading tasks:', xhr.responseText, status, error);
                let errorMsg = 'Error loading tasks';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                $('#taskTableBody').html(`<tr><td colspan="12" class="text-center text-danger">${errorMsg}</td></tr>`);
            }
        });
    }

    // Edit task function
    window.editTask = function(id) {
        console.log('Editing task:', id);

        // Reset image handling variables
        selectedEditImages = [];
        $('#edit_task_images').val('');
        $('#editImagePreview').empty();
        $('#existingImages').empty();

        // Load dropdowns first - mimicking all-tasks.blade.php
        function loadEditModalDropdowns() {
            // Load users for edit modal
            $.get("{{ route('task.users') }}", function(data) {
                let options = '';
                if (data && data.length > 0) {
                    $.each(data, function(i, user) {
                        options += `<option value="${user.id}">${user.name}</option>`;
                    });
                } else {
                    options = '<option value="" disabled>No users found</option>';
                }
                const container = $('#editAssignUsersContainer');
                // If it is a checkbox container
                if (container.length) {
                   allUsers = Array.isArray(data) ? data : [];
                   // We don't render here, we render when we have the task data to know who is selected
                }
                // If there is a select for users somewhere? No, task blade uses assign-users-grid div.
                // But loadEditModalDropdowns in all-tasks does not seem to handle users checkboxes async for options? 
                // Ah, in all-tasks it loads users via task data? 
                // In task.blade.php:
                // We rely on 'allUsers' being loaded on page load for renderUserCheckboxes.
                // But let's re-fetch 'allUsers' just in case.
                allUsers = data;
            });

            // Load customers for edit modal
            $.get("{{ route('task.customers') }}", function(data) {
                let options = '<option value="">Select Customer</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, customer) {
                        options += `<option value="${customer.id}">${customer.name}</option>`;
                    });
                }
                $('#edit_customer_id').html(options);
            });

            // Load task statuses for edit modal
            $.get("{{ route('task.statuses') }}", function(data) {
                let options = '<option value="">Select Status</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, status) {
                        options += `<option value="${status.id}">${status.name}</option>`;
                    });
                }
                $('#edit_task_status_id').html(options);
            });

            // Load task priorities for edit modal
            $.get("{{ route('task.priorities') }}", function(data) {
                let options = '<option value="">Select Priority</option>';
                if (data && data.length > 0) {
                    $.each(data, function(i, priority) {
                        options += `<option value="${priority.id}">${priority.name}</option>`;
                    });
                }
                $('#edit_task_priority_id').html(options);
            });
        }
        loadEditModalDropdowns();
        
        // Fetch task data
        $.get("{{ route('task.fetch') }}", function(data) {
            let task = data.find(t => t.id === id);
            if (task) {
                $('#edit_task_id').val(task.id);
                
                // Wait a bit for dropdowns to load, then set values
                setTimeout(function() {
                    const editAssignees = Array.isArray(task.assigned_users) && task.assigned_users.length
                        ? task.assigned_users.map(user => String(user.id))
                        : (task.user_id ? [String(task.user_id)] : []);
                    
                    // Ensure users are rendered before checking
                    renderUserCheckboxes('#editAssignUsersContainer', editAssignees);

                    $('#edit_customer_id').val(task.customer_id);
                    $('#edit_task_name').val(task.task_name);
                    $('#edit_task').val(task.task);
                    $('#edit_task_status_id').val(task.task_status_id);
                    $('#edit_task_priority_id').val(task.task_priority_id);
                    $('#edit_due_date').val(task.due_date ? task.due_date.substring(0, 10) : '');
                    
                    // Set task type
                    const taskType = task.task_type || 'task';
                    $(`input[name="edit_task_type"][value="${taskType}"]`).prop('checked', true);
                    updateEditTypeState();

                    // Prefill recurrence
                    const isRec = !!task.is_recurring;
                    $('#edit_is_recurring').prop('checked', isRec);
                    $('#edit_recurrencePanel').toggle(isRec);
                    $('#edit_recurrence_type').val(task.recurrence_type || 'daily');
                    $('#edit_recurrence_interval').val(task.recurrence_interval || 1);
                    $('#edit_recurrence_end_date').val(task.recurrence_end_date || '');
                    // Reset weekday/month checks
                    ['mon','tue','wed','thu','fri','sat','sun'].forEach(k => $('#edit_dow_'+k).prop('checked', false));
                    if (Array.isArray(task.recurrence_days_of_week)) {
                        task.recurrence_days_of_week.forEach(k => $('#edit_dow_'+k).prop('checked', true));
                    }
                    $('#edit_recurrence_day_of_month').val(task.recurrence_day_of_month || '');
                    for (let i=1;i<=12;i++){ $('#edit_m_'+i).prop('checked', false); }
                    if (Array.isArray(task.recurrence_months)) {
                        task.recurrence_months.forEach(m => $('#edit_m_'+m).prop('checked', true));
                    }
                    // Ensure the right sub-panel shows
                    $('#edit_recurrence_type').trigger('change');
                    
                    // Display existing images
                    const existingImagesDiv = $('#existingImages');
                    existingImagesDiv.empty();
                    if (task.images && task.images.length > 0) {
                        existingImagesDiv.append('<small class="text-muted d-block mb-2">Existing images:</small>');
                        task.images.forEach(function(img, idx) {
                            // Use url from model if available, otherwise try alternative paths
                            let imageUrl = '';
                            if (img.url) {
                                imageUrl = img.url;
                            } else if (img.image_path) {
                                // Try direct storage path first
                                imageUrl = `/storage/${img.image_path}`;
                                // If image has ID, use route as fallback
                                if (img.id) {
                                    imageUrl = `/task/${task.id}/image/${img.id}`;
                                }
                            }
                            
                            const imageName = img.original_name || 'Image';
                            const imageId = `edit-img-${task.id}-${idx}`;
                            
                            existingImagesDiv.append(`
                                <div class="d-inline-block me-2 mb-2 position-relative" id="img-container-${task.id}-${img.id}" style="border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                    <button type="button" 
                                            class="btn btn-sm btn-danger position-absolute" 
                                            style="top: 5px; right: 5px; padding: 2px 6px; font-size: 0.75rem; z-index: 10; line-height: 1; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;"
                                            onclick="deleteTaskImage(${task.id}, ${img.id}, 'img-container-${task.id}-${img.id}')" 
                                            title="Remove Image">
                                        <i class="bi bi-x" style="font-size: 0.8rem; font-weight: bold;"></i>
                                    </button>
                                    ${imageUrl ? `
                                        <img id="${imageId}" 
                                             src="${imageUrl}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover; display: block; cursor: pointer;"
                                             onclick="window.open('${imageUrl}', '_blank')"
                                             onerror="handleImageError('${imageId}', '${task.id}', ${img.id || 'null'})">
                                        <div id="${imageId}-placeholder" style="width: 80px; height: 80px; display: none; background: #f0f0f0; border: 1px dashed #ccc; text-align: center; line-height: 80px; font-size: 0.7rem; color: #999;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    ` : `
                                        <div style="width: 80px; height: 80px; background: #f0f0f0; border: 1px dashed #ccc; text-align: center; line-height: 80px; font-size: 0.7rem; color: #999;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    `}
                                    <small class="d-block text-truncate mt-1" style="max-width: 80px;" title="${imageName}">${imageName}</small>
                                </div>
                            `);
                        });
                    }
                }, 200);
                
        // Show modal
                $('#editTaskModal').modal('show');
            }
        }).fail(function() {
            alert('Error loading task data');
        });
    };

    // Image preview for edit form
    $('#edit_task_images').on('change', function(e) {
        const preview = $('#editImagePreview');
        preview.empty();
        const files = e.target.files;
        if (files.length > 0) {
            preview.append('<small class="text-muted d-block mb-2">New images to add:</small>');
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.append(`
                            <div class="d-inline-block me-2 mb-2" style="position: relative;">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Handle edit form submission with file uploads
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        let taskId = $('#edit_task_id').val();
        const formData = new FormData(this);
        
        // Add method spoofing
        formData.append('_method', 'PUT');
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        // Add task_type from radio button
        const taskType = $('input[name="edit_task_type"]:checked').val();
        formData.set('task_type', taskType || 'task');

        // Include pasted images in edit as well
        if (selectedEditImages.length > 0) {
            selectedEditImages.forEach((img) => {
                formData.append('images[]', img.file);
            });
        }

        // Recurrence fields (edit)
        const eIsRec = $('#edit_is_recurring').is(':checked');
        formData.append('is_recurring', eIsRec ? 1 : 0);
        if (eIsRec) {
            formData.append('recurrence_type', $('#edit_recurrence_type').val());
            formData.append('recurrence_interval', $('#edit_recurrence_interval').val() || 1);
            const dowsE = [];
            ['mon','tue','wed','thu','fri','sat','sun'].forEach(function(k){ if ($('#edit_dow_'+k).is(':checked')) dowsE.push(k); });
            if (dowsE.length) { dowsE.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
            const domE = $('#edit_recurrence_day_of_month').val();
            if (domE) formData.append('recurrence_day_of_month', domE);
            const monthsE = [];
            for (let i=1;i<=12;i++){ if ($('#edit_m_'+i).is(':checked')) monthsE.push(i); }
            if (monthsE.length) { monthsE.forEach(v => formData.append('recurrence_months[]', v)); }
            const endDateE = $('#edit_recurrence_end_date').val();
            if (endDateE) formData.append('recurrence_end_date', endDateE);
        }
        
        console.log('Updating task:', taskId);
        
        $.ajax({
            url: `/task/${taskId}`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Task updated:', response);
                alert(response.message || 'Task updated successfully!');
                $('#editTaskModal').modal('hide');
                $('#editTaskForm')[0].reset();
                $('#editImagePreview').empty();
                $('#existingImages').empty();
                loadTasks();
            },
            error: function(xhr, status, error) {
                console.error('Error updating task:', xhr.responseText, status, error);
                let errorMsg = 'Failed to update task';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                alert('Error: ' + errorMsg);
            }
        });
    });


    // Toggle done status function
    window.toggleDone = function(id) {
        console.log('Toggling done status for task:', id);
        
        $.ajax({
            url: `/task/${id}/toggle-done`,
            type: "POST",
            dataType: 'json',
            success: function(response) {
                console.log('Task status toggled:', response);
                alert(response.message || 'Task status updated!');
                loadTasks();
            },
            error: function(xhr, status, error) {
                console.error('Error toggling task status:', xhr.responseText, status, error);
                alert('Error: Failed to update task status');
            }
        });
    };

    // Delete task function
    window.deleteTask = function(id) {
        if (confirm('Are you sure you want to delete this task?')) {
            console.log('Deleting task:', id);
            $.ajax({
                url: `/task/${id}`,
                type: "DELETE",
                dataType: 'json',
                success: function(response) {
                    console.log('Task deleted:', response);
                    alert(response.message || 'Task deleted successfully!');
                    loadTasks();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting task:', xhr.responseText, status, error);
                    alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete task'));
                }
            });
        }
    };

    // Poke task (send reminder email to assigned user)
    window.pokeTask = function(btn, id) {
        if (!confirm('Send poke email to the assigned user for this task?')) return;
        const $btn = $(btn);
        const oldHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $.ajax({
            url: `/task/${id}/poke`,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                // Visual feedback
                $btn.html('<i class="bi bi-check2-circle"></i>');
                // const badge = $('<span class="poke-sent-badge">Poked</span>');
                $btn.after(badge);
                setTimeout(function(){
                    badge.fadeOut(200, function(){ $(this).remove(); });
                    $btn.prop('disabled', false).html(oldHtml);
                }, 1400);
            },
            error: function(xhr) {
                let msg = 'Failed to send poke';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alert('Error: ' + msg);
                $btn.prop('disabled', false).html(oldHtml);
            }
        });
    };

    // Filter change handlers
    $('#filter_user, #filter_creator, #filter_customer, #filter_status, #filter_priority, #filter_type').on('change', function() {
        loadTasks();
    });
    $('#filter_date_from, #filter_date_to').on('change', function() {
        loadTasks();
    });

    // Clear filters function
    window.clearFilters = function() {
        $('#filter_user').val('');
        $('#filter_creator').val('');
        $('#filter_customer').val('');
        $('#filter_status').val('');
        $('#filter_priority').val('');
        $('#filter_type').val('');
        $('#filter_date_from').val('');
        $('#filter_date_to').val('');
        currentPage = 1;
        const filtered = applyFilters(allTasks);
        renderTasksTable(filtered, currentPage);
    };

    // Action button functions
    // Action button functions
    // Edit task function


    // Handle edit form submission
    $('#editTaskForm').on('submit', function(e) {
      e.preventDefault();

      const taskId = $('#edit_task_id').val();
      const formData = new FormData(this);

      formData.append('_method', 'PUT');
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

      const taskType = $('input[name="edit_task_type"]:checked').val() || 'task';
      formData.set('task_type', taskType);

      if (selectedEditImages.length > 0) {
          selectedEditImages.forEach((img) => {
              formData.append('images[]', img.file);
          });
      }

      $.ajax({
        url: `/task/${taskId}`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
          console.log('Task updated:', response);
          alert(response.message || 'Task updated successfully!');
          $('#editTaskModal').modal('hide');
          $('#editTaskForm')[0].reset();
          selectedEditImages = [];
          $('#editImagePreview').empty();
          $('#existingImages').empty();
          loadTasks();
        },
        error: function(xhr, status, error) {
          console.error('Error updating task:', xhr.responseText, status, error);
          let errorMsg = 'Failed to update task';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
          } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
          }
          alert('Error: ' + errorMsg);
        }
      });
    });

    window.handleImageError = function(imageId, taskId, imageIdNum) {
        const img = document.getElementById(imageId);
        const placeholder = document.getElementById(imageId + '-placeholder');

        if (img) {
            img.style.display = 'none';
            if (imageIdNum && img.src.includes('/storage/')) {
                const altUrl = `/task/${taskId}/image/${imageIdNum}`;
                img.src = altUrl;
                img.style.display = 'block';
                img.onerror = function() {
                    this.style.display = 'none';
                    if (placeholder) placeholder.style.display = 'block';
                };
            } else {
                if (placeholder) placeholder.style.display = 'block';
            }
        }
    };

    window.deleteTaskImage = function(taskId, imageId, containerId) {
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        $.ajax({
            url: `/task/${taskId}/image/${imageId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                const container = document.getElementById(containerId);
                if (container) {
                    container.style.transition = 'opacity 0.3s';
                    container.style.opacity = '0';
                    setTimeout(() => {
                        container.remove();
                    }, 300);
                }
                alert('Image deleted successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error deleting image:', xhr.responseText, status, error);
                alert('Error: Failed to delete image');
            }
        });
    };

    function addPastedFilesToSelectedEdit(files) {
        if (!files || files.length === 0) return;
        files.forEach((file) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                selectedEditImages.push({ file, name: file.name, size: file.size, preview: e.target.result });
                $('#editImagePreview').append(`
                    <div class="d-inline-block me-2 mb-2" style="position: relative;">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                    </div>
                `);
            };
            reader.readAsDataURL(file);
        });
    }

    $('#edit_task_images').on('change', function(e) {
        const preview = $('#editImagePreview');
        preview.empty();
        const files = e.target.files || [];
        if (files.length > 0) {
            preview.append('<small class="text-muted d-block mb-2">New images to add:</small>');
            Array.from(files).forEach((file) => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.append(`
                        <div class="d-inline-block me-2 mb-2" style="position: relative;">
                            <img src="${ev.target.result}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                            <small class="d-block text-truncate" style="max-width: 80px;" title="${file.name}">${file.name}</small>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    $('#editImagePreview').on('paste', function(event) {
        const files = filesFromClipboard(event);
        if (files.length > 0) {
            event.preventDefault();
            addPastedFilesToSelectedEdit(files);
        }
    });

    $('#editTaskModal').on('hidden.bs.modal', function() {
        selectedEditImages = [];
        $('#edit_task_images').val('');
        $('#editImagePreview').empty();
        $('#existingImages').empty();
        $('input[name="edit_task_type"]').prop('checked', false);
    });

    window.deleteTask = function(id) {
      if (!confirm('Are you sure you want to delete this task?')) return;
      
      console.log('Deleting task:', id);
      $.ajax({
        url: `/task/${id}`,
        type: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          console.log('Task deleted:', response);
          alert(response.message || 'Task deleted successfully!');
          loadTasks();
        },
        error: function(xhr, status, error) {
          console.error('Error deleting task:', xhr.responseText, status, error);
          alert('Error: ' + (xhr.responseJSON?.message || 'Failed to delete task'));
        }
      });
    };

    window.pokeTask = function(btn, id) {
      if (!confirm('Send poke email to the assigned user for this task?')) return;
      
      const $btn = $(btn);
      const oldHtml = $btn.html();
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
      
      $.ajax({
        url: `/task/${id}/poke`,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
          $btn.html('<i class="bi bi-check2-circle"></i>');
          const badge = $('<span class="poke-sent-badge">Poked</span>');
          $btn.after(badge);
          setTimeout(function(){
            badge.fadeOut(200, function(){ $(this).remove(); });
            $btn.prop('disabled', false).html(oldHtml);
          }, 1400);
        },
        error: function(xhr) {
          let msg = 'Failed to send poke';
          if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
          alert('Error: ' + msg);
          $btn.prop('disabled', false).html(oldHtml);
        }
      });
    };

    window.toggleDone = function(id) {
      console.log('Toggling done status for task:', id);
      
      $.ajax({
        url: `/task/${id}/toggle-done`,
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function(response) {
          console.log('Task status toggled:', response);
          // Reload tasks to reflect the change
          loadTasks();
        },
        error: function(xhr, status, error) {
          console.error('Error toggling task status:', xhr.responseText, status, error);
          alert('Error: Failed to update task status');
        }
      });
    };

    // Initial load - removed duplicate
    console.log('Starting initial load...');
    // loadTasks() is already called above
});
</script>
@endpush
