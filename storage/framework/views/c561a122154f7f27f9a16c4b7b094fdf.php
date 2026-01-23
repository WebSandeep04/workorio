
<?php $__env->startSection('title', 'All Tasks'); ?>
<?php $__env->startSection('page_title', 'All Tasks'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
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

  .summary-cards,
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
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

  .status-card:nth-child(6n+1),
  .status-card:nth-child(6n+2),
  .status-card:nth-child(6n+3),
  .status-card:nth-child(6n+4),
  .status-card:nth-child(6n+5),
  .status-card:nth-child(6n+6),
  .status-card:nth-child(6n+7),
  .status-card:nth-child(6n+8),
  .status-card:nth-child(6n+9),
  .status-card:nth-child(6n+10),
  .status-card:nth-child(6n+11),
  .status-card:nth-child(6n+12) {
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

  .status-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
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
    width: 100%;
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

  .edit-modal-header {
    background-color: #434afa !important;
    color: white !important;
  }

  .edit-modal-header .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
  }

  .btn-update-task {
    background-color: #434afa !important;
    border-color: #434afa !important;
    color: white !important;
  }

  @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-left: 0;
    }

    .filterBox {
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      padding: 1rem;
    }

    .filterBox .mb-2 {
      margin-bottom: 0 !important;
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

    .modal-footer {
      justify-content: center !important;
      padding: 1rem;
    }
    .modal-footer .btn {
      width: 100% !important;
      margin: 0 !important;
    }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Total Tasks">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Tasks</div>
        <div class="summary-card-value" id="totalTasks">0</div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-amber">
        <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="In Progress">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">In Progress</div>
        <div class="summary-card-value" id="inProgressTasks">0</div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-emerald">
        <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Completed">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Completed</div>
        <div class="summary-card-value" id="completedTasks">0</div>
      </div>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-rose">
        <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Pending">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Pending</div>
        <div class="summary-card-value" id="pendingTasks">0</div>
      </div>
    </div>
    <div class="summary-card card-5">
      <div class="summary-card-icon icon-sky">
        <img src="<?php echo e(asset('img/icons/new.png')); ?>" alt="Today's Tasks">
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
    <!-- <a href="<?php echo e(route('task.index')); ?>" class="table-search-btn" id="addBtn">
      <i class="bi bi-plus me-1"></i>Add
    </a> -->
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
      <div class="modal-header edit-modal-header">
        <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editTaskForm">
        <div class="modal-body" style="font-size: 0.9rem;">
          <input type="hidden" id="edit_task_id">

          <!-- Task Type Selection -->
          <div class="mb-3">
            <label class="form-label">Task Type <span class="text-danger">*</span></label>
            <div class="form-check form-check-inline me-3">
              <input class="form-check-input" type="radio" name="edit_task_type" id="edit_task_type_task" value="task">
              <label class="form-check-label" for="edit_task_type_task">Task</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="edit_task_type" id="edit_task_type_qc" value="qc">
              <label class="form-check-label" for="edit_task_type_qc">QC</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="edit_task_type" id="edit_task_type_cp" value="cp">
              <label class="form-check-label" for="edit_task_type_cp">Critical Path</label>
            </div>
          </div>

          <div class="mb-3">
            <label for="edit_customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
            <select name="customer_id" id="edit_customer_id" class="form-select form-select-sm" required>
              <option value="">Select Customer</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_task_name" class="form-label">Task Name <span class="text-danger">*</span></label>
            <input type="text" name="task_name" id="edit_task_name" class="form-control form-control-sm" required placeholder="Enter task name...">
          </div>

          <div class="mb-3">
            <label for="edit_due_date" class="form-label">Due Date</label>
            <input type="date" name="due_date" id="edit_due_date" class="form-control form-control-sm">
          </div>

          <div class="mb-3">
            <label for="edit_task" class="form-label">Task Description <span class="text-danger">*</span></label>
            <textarea name="task" id="edit_task" class="form-control form-control-sm" rows="3" required placeholder="Enter task details..."></textarea>
          </div>

          <!-- Image Upload for Edit -->
          <div class="mb-3">
              <label for="edit_task_images" class="form-label">Add More Images (Optional)</label>
              <input type="file" name="images[]" id="edit_task_images" class="form-control form-control-sm" multiple accept="image/*">
              <small class="help-text">You can select or paste images (Ctrl+V). Max 5MB per image.</small>
              <div id="editImagePreview" class="mt-2" tabindex="0" style="min-height:56px;"></div>
              <div id="existingImages" class="mt-2"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">Assign Users <span class="text-danger">*</span></label>
            <div id="globalEditAssignUsersContainer" style="max-height: 150px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; padding: 8px;"></div>
          </div>

          <div class="mb-3">
            <label for="edit_task_status_id" class="form-label">Task Status <span class="text-danger">*</span></label>
            <select name="task_status_id" id="edit_task_status_id" class="form-select form-select-sm" required>
              <option value="">Select Status</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_task_priority_id" class="form-label">Task Priority</label>
            <select name="task_priority_id" id="edit_task_priority_id" class="form-select form-select-sm">
              <option value="">Select Priority</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-update-task">Update Task</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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
    url: "<?php echo e(route('all-tasks.fetch')); ?>",
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
        statusHtml = '<span class="text-success">Done</span>';
      } else if (task.status) {
        let statusColor = task.status.color || '#6c757d';
        statusHtml = `<span style="color: ${statusColor}">${task.status.name}</span>`;
      } else {
        statusHtml = '<span class="text-warning">Pending</span>';
      }
      
      // Priority text
      let priorityHtml = 'N/A';
      if (task.priority) {
        let priorityColor = task.priority.color || '#6c757d';
        priorityHtml = `<span style="color: ${priorityColor}">${task.priority.name}</span>`;
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
          ? `<span class="text-danger" title="Overdue">${dueDateRaw}</span>` 
          : dueDateRaw;
      
      // Created at
      let createdAt = task.created_at ? new Date(task.created_at).toLocaleDateString('en-GB') : 'N/A';
      
      // Images count
      let imagesCount = 0;
      if (task.images && Array.isArray(task.images)) {
        imagesCount = task.images.length;
      }
      let imagesDisplay = imagesCount > 0 ? `<span class="badge bg-info">${imagesCount}</span>` : 'N/A';
      
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
          <td><span style="color: ${typeColor}">${typeBadge.toUpperCase()}</span></td>
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

// Load data on page load
$(document).ready(function() {
  // Store users globally for edit modal
  window.globalUsers = [];
  
  // Load users
  $.get("<?php echo e(route('task.users')); ?>", function(data) {
    let options = '<option value="">All Users</option>';
    if (data && data.length > 0) {
      window.globalUsers = data; // Store globally
      $.each(data, function(i, user) {
        options += `<option value="${user.id}">${user.name}</option>`;
      });
    }
    $('#filter_user').html(options);
  });
  
  // Load statuses
  $.get("<?php echo e(route('task.statuses')); ?>", function(data) {
    let options = '<option value="">All Statuses</option><option value="done">Done</option>';
    if (data && data.length > 0) {
      $.each(data, function(i, status) {
        options += `<option value="${status.id}">${status.name}</option>`;
      });
    }
    $('#filter_status').html(options);
  });
  
  // Load priorities
  $.get("<?php echo e(route('task.priorities')); ?>", function(data) {
    let options = '<option value="">All Priorities</option>';
    if (data && data.length > 0) {
      $.each(data, function(i, priority) {
        options += `<option value="${priority.id}">${priority.name}</option>`;
      });
    }
    $('#filter_priority').html(options);
  });
  
  loadTasks();
});

// Action button functions
    
    window.viewTaskDetails = function(id) {
      const task = allTasks.find(t => t.id === id);
      if (task) {
        // Assigned users logic reuse
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

    // Store selected edit images
    let selectedEditImages = [];

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

    // Handle image load errors
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

    // Paste on edit image preview
    $('#editImagePreview').on('paste', function(event) {
        const files = filesFromClipboard(event);
        if (files.length > 0) {
            event.preventDefault();
            addPastedFilesToSelectedEdit(files);
        }
    });

    // Handle file input change
    $('#edit_task_images').on('change', function(e) {
        const preview = $('#editImagePreview');
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
                    // Add to selected images array if needed, but for input[type=file] we can rely on FormData
                    // However, we want to unify them. 
                    // To keep it simple: we'll append files from input to FormData directly on submit
                    selectedEditImages.push({ file: file }); 
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Clean up on modal close
    $('#editTaskModal').on('hidden.bs.modal', function() {
        selectedEditImages = [];
        $('#edit_task_images').val('');
        $('#editImagePreview').empty();
        $('#existingImages').empty();
        $('#editTaskForm')[0].reset();
    });

window.editTask = function(id) {
  console.log('Editing task:', id);

  // Reset images
  selectedEditImages = [];
  $('#edit_task_images').val('');
  $('#editImagePreview').empty();
  $('#existingImages').empty();

  // Load edit modal dropdowns
  function loadEditModalDropdowns() {
    // Load customers
    $.get("<?php echo e(route('task.customers')); ?>", function(data) {
      let options = '<option value="">Select Customer</option>';
      if (data && data.length > 0) {
        $.each(data, function(i, customer) {
          options += `<option value="${customer.id}">${customer.name}</option>`;
        });
      }
      $('#edit_customer_id').html(options);
    });

    // Load statuses
    $.get("<?php echo e(route('task.statuses')); ?>", function(data) {
      let options = '<option value="">Select Status</option>';
      if (data && data.length > 0) {
        $.each(data, function(i, status) {
          options += `<option value="${status.id}">${status.name}</option>`;
        });
      }
      $('#edit_task_status_id').html(options);
    });

    // Load priorities
    $.get("<?php echo e(route('task.priorities')); ?>", function(data) {
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

  $.get("<?php echo e(route('all-tasks.fetch')); ?>", function(data) {
    let task = (data || []).find(t => t.id === id);
    if (task) {
      $('#edit_task_id').val(task.id);

      setTimeout(function() {
        // Load user checkboxes with selected users
        const editAssignees = Array.isArray(task.assigned_users) && task.assigned_users.length
          ? task.assigned_users.map(user => String(user.id))
          : (task.user_id ? [String(task.user_id)] : []);
        
        // Render checkboxes with selected users
        const container = $('#globalEditAssignUsersContainer');
        if (window.globalUsers && window.globalUsers.length) {
          const selectedSet = new Set(editAssignees);
          const html = window.globalUsers.map(user => {
            const id = String(user.id);
            const checked = selectedSet.has(id) ? 'checked' : '';
            return `
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${id}" id="globalEditAssignUsersContainer_${id}" name="user_ids[]" ${checked}>
                <label class="form-check-label" for="globalEditAssignUsersContainer_${id}">${user.name}</label>
              </div>`;
          }).join('');
          container.html(html);
        }

        $('#edit_customer_id').val(task.customer_id);
        $('#edit_task_name').val(task.task_name || '');
        $('#edit_task').val(task.task || '');
        $('#edit_task_status_id').val(task.task_status_id);
        $('#edit_task_priority_id').val(task.task_priority_id);
        $('#edit_due_date').val(task.due_date ? task.due_date.substring(0, 10) : '');

        const taskType = task.task_type || 'task';
        $(`input[name="edit_task_type"][value="${taskType}"]`).prop('checked', true);

        // Display existing images
        const existingImagesDiv = $('#existingImages');
        existingImagesDiv.empty();
        if (task.images && task.images.length > 0) {
            existingImagesDiv.append('<small class="text-muted d-block mb-2">Existing images:</small>');
            task.images.forEach(function(img, idx) {
                let imageUrl = '';
                if (img.url) {
                    imageUrl = img.url;
                } else if (img.image_path) {
                    imageUrl = `/storage/${img.image_path}`;
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

      $('#editTaskModal').modal('show');
    } else {
      alert('Task not found.');
    }
  }).fail(function() {
    alert('Error loading task data');
  });
};

// Handle edit form submission
$('#editTaskForm').on('submit', function(e) {
  e.preventDefault();

  const taskId = $('#edit_task_id').val();
  const formData = new FormData(this);

  formData.append('_method', 'PUT');
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

  const taskType = $('input[name="edit_task_type"]:checked').val() || 'task';
  formData.set('task_type', taskType);

  // Append images
  if (selectedEditImages.length > 0) {
      selectedEditImages.forEach((img) => {
          // Avoid duplicating files if they were added via standard input
          // But since we control the array, let's just append
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
      const filtered = applyFilters(allTasks);
      loadTasks();
    },
    error: function(xhr, status, error) {
      console.error('Error toggling task status:', xhr.responseText, status, error);
      alert('Error: Failed to update task status');
    }
  });
};
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/worklog/all-tasks.blade.php ENDPATH**/ ?>