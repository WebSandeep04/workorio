

<?php $__env->startSection('title', 'Petty Cash'); ?>
<?php $__env->startSection('page_title', 'Petty Cash'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .modal-header{
    background-color: #434AFA;
    color: #fff;
  }

  .save-btn{
    background-color: #434AFA;
    color: #fff;
  }

  .form-label{
    font-weight: 700;
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
    padding: 0.6rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .metric-arrow {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    position: absolute;
    right: 6px;
    bottom: 6px;
    font-size: 0.8rem;
  }

  .metric-arrow:hover {
    background: #5b59f7;
    color: #fff;
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

  .summary-card-icon img, .summary-card-icon i {
    width: 20px;
    height: 20px;
    object-fit: contain;
    font-size: 1.25rem; /* Ensure icons are visible */
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

  .summary-card.card-1 { background: #fff; }
  .summary-card.card-2 { background: #fff; }
  .summary-card.card-3 { background: #fff; }
  .summary-card.card-4 { background: #fff; }
  .summary-card.card-5 { background: #fff; }

  /* Status cards - all white background like dashboard */
  .status-card:nth-child(n) {
    background: #fff;
  }

  .summary-card-label,
  .status-card-label {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value,
  .status-card-value {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #000;
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

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
  }

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background: white;
    border-radius: 0px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  }

  .custom-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 9px;
    padding: 0;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .custom-table td {
    font-size: 9px;
    padding: 0;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
  }

  .custom-table tbody tr {
    transition: all 0.3s ease;
  }

  .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
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

  .table-responsive {
    border-radius: 5px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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
    font-family: Montserrat;
  }

  .data-table-card .custom-table thead th,
  .data-table-card .custom-table tbody td {
    white-space: nowrap;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
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

  /* Status Badges */
  .badge-approved {
    background-color: #d1fae5;
    color: #065f46;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .btn-action:hover {
    color: #434afa;
    transform: scale(1.1);
  }

  .spin {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
    }

    .summary-cards,
    .status-cards {
      grid-template-columns: repeat(2, 1fr);
    }

    .data-table-card .custom-table tbody td {
      font-size: 0.75rem
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-violet">
          <i class="bi bi-wallet2 fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Opening Balance</div>
          <div class="summary-card-value text-dark">₹<span id="stat_opening_balance">0.00</span></div>
        </div>
        <a href="<?php echo e(route('petty-cash.department-summary')); ?>" class="metric-arrow">
            <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      <div class="summary-card card-5">
        <div class="summary-card-icon icon-rose">
          <i class="bi bi-cash-stack fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Expense</div>
          <div class="summary-card-value text-danger">₹<span id="stat_all_expense">0.00</span></div>
        </div>
      </div>
      <div class="summary-card card-1">
        <div class="summary-card-icon icon-sunrise">
          <i class="bi bi-piggy-bank fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Remaining Op-Expense</div>
          <div class="summary-card-value text-primary">₹<span id="stat_remaining_balance">0.00</span></div>
        </div>
      </div>
  </div>

  <!-- Status Cards (Placeholder/Loading as per request)
  <div class="status-cards" id="statusCardsContainer">
    <div class="status-card">
      <div class="status-card-label">LOADING...</div>
      <div class="status-card-value">0</div>
    </div>
  </div> -->

  <!-- Filters - Blue Filter Box -->
  <div class="filterBox mb-3">
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-building"></i> Department</label>
        <select class="form-control-modern" id="filter_department">
            <option value="">All Departments</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($department->id); ?>"><?php echo e($department->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-tag-fill"></i> Expense Type</label>
        <select class="form-control-modern" id="filter_expense">
            <option value="">All Expenses</option>
            <!-- Populated by JS -->
        </select>
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-check-circle-fill"></i> Status</label>
        <select class="form-control-modern" id="filter_status">
            <option value="">All Status</option>
            <option value="1">Approved</option>
            <option value="0">Pending</option>
        </select>
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> From Date</label>
        <input type="date" class="form-control-modern" id="filter_from_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> To Date</label>
        <input type="date" class="form-control-modern" id="filter_to_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-month"></i> Month</label>
        <select class="form-control-modern" id="filter_month">
            <option value="">All Months</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
  </div>

  <!-- Search & Add (Separate Row) -->
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="filter_search" placeholder="Search expenses..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createEntryModal">
      <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="pettyCashTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Department</th>
              <th>Expense Name</th>
              <th>Price (₹)</th>
              <th>Remark</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="text-center py-4">
                <i class="bi bi-arrow-repeat spin"></i> Loading data...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 d-flex justify-content-center">
    <div id="paginationLinks"></div>
  </div>
</div>

<!-- Create Entry Modal -->
<div class="modal fade" id="createEntryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Petty Cash Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createEntryForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Wallet <span class="text-danger">*</span></label>
            <select class="form-select" name="department_id" id="create_department_id" required>
              <option value="">Select Wallet</option>
              <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($department->id); ?>" <?php echo e($department->id == 1 ? 'selected' : ''); ?>><?php echo e($department->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Expense Type <span class="text-danger">*</span></label>
            <select class="form-select" name="expense_id" id="create_expense_id" required>
              <option value="">Select Expense</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="price" id="create_price" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Attachment</label>
            <input type="file" class="form-control" name="attachment" id="create_attachment">
          </div>
          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea class="form-control" name="remark" id="create_remark" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
          <button type="submit" class="btn btn-primary save-btn">Save Entry</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Entry Modal -->
<div class="modal fade" id="editEntryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editEntryForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="edit_entry_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Wallet <span class="text-danger">*</span></label>
            <select class="form-select" name="department_id" id="edit_department_id" required>
              <option value="">Select Wallet</option>
              <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($department->id); ?>"><?php echo e($department->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Expense Type <span class="text-danger">*</span></label>
            <select class="form-select" name="expense_id" id="edit_expense_id" required>
              <option value="">Select Expense</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Remark</label>
            <textarea class="form-control" name="remark" id="edit_remark" rows="2"></textarea>
          </div>
           <!-- Attachment edit usually requires separate logic, omitted for now or handle update similarly -->
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_approved" id="edit_is_approved" value="1">
            <label class="form-check-label" for="edit_is_approved">Approved</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Entry</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<!-- Remark View Modal -->
<div class="modal fade" id="viewRemarkModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Full Remark</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="full_remark_text" style="white-space: pre-wrap; word-break: break-word;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
  loadData();
  loadStats();
  loadExpenses();

  let searchTimer;
  $('#filter_search').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      loadData(1);
    }, 500);
  });

  // Filter change events
  $('#filter_department, #filter_expense, #filter_status, #filter_from_date, #filter_to_date, #filter_month').on('change', function() {
    loadData(1);
    loadStats();
  });

  function loadStats() {
      const departmentId = $('#filter_department').val();
      $.get("<?php echo e(route('petty-cash.stats')); ?>", { department_id: departmentId }, function(data) {
          // Overall Stats
          $('#stat_opening_balance').text(parseFloat(data.total_opening_balance || 0).toFixed(2));
          $('#stat_all_expense').text(parseFloat(data.total_expense || 0).toFixed(2));
          $('#stat_remaining_balance').text(parseFloat(data.remaining_balance || 0).toFixed(2));
      });
  }

  function loadExpenses() {
    $.get("<?php echo e(route('petty-cash.fetch-expenses')); ?>", function(data) {
      let options = '<option value="">Select Expense</option>';
      let filterOptions = '<option value="">All Expenses</option>';
      
      if(Array.isArray(data)) {
        data.forEach(function(expense) {
          options += `<option value="${expense.id}" data-price="${expense.price}">${expense.name}</option>`;
          filterOptions += `<option value="${expense.id}">${expense.name}</option>`;
        });
      }
      
      $('#create_expense_id, #edit_expense_id').html(options);
      $('#filter_expense').html(filterOptions);
    });
  }

  // Auto-fill price when expense is selected
  $('#create_expense_id').on('change', function() {
    let price = $(this).find(':selected').data('price');
    if(price) $('#create_price').val(price);
  });

  $('#edit_expense_id').on('change', function() {
    let price = $(this).find(':selected').data('price');
    if(price) $('#edit_price').val(price);
  });

  // Create Entry Submission with Attachment
  $('#createEntryForm').submit(function(e) {
      e.preventDefault();
      
      let formData = new FormData(this);
      
      $.ajax({
          url: "<?php echo e(route('petty-cash.store')); ?>",
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
              if(response.success) {
                  $('#createEntryModal').modal('hide');
                  $('#createEntryForm')[0].reset();
                  loadData();
                  loadStats();
              }
          },
          error: function(err) {
              alert('Error creating entry');
          }
      });
  });

  // Edit Entry - Load Data
  $(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $('#edit_entry_id').val(id);
    $('#edit_expense_id').val($(this).data('expense-id'));
    $('#edit_department_id').val($(this).data('department-id'));
    $('#edit_price').val($(this).data('price'));
    $('#edit_remark').val($(this).data('remark'));
    $('#edit_is_approved').prop('checked', $(this).data('approved'));
    
    $('#editEntryModal').modal('show');
  });

  // Edit Entry Submission
  $('#editEntryForm').submit(function(e) {
      e.preventDefault();
      let id = $('#edit_entry_id').val();
      $.ajax({
          url: `/petty-cash/${id}`,
          method: 'PUT',
          data: $(this).serialize(),
          success: function(response) {
              $('#editEntryModal').modal('hide');
              loadData();
              loadStats();
          },
          error: function(err) {
              alert('Error updating entry');
          }
      });
  });

  // Delete Entry
  window.deleteEntry = function(id) {
    if(confirm('Are you sure you want to delete this entry?')) {
      $.ajax({
        url: `/petty-cash/${id}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function(res) {
          loadData();
          loadStats();
        }
      });
    }
  }

  // Toggle Approval
  window.toggleApproval = function(id) {
      $.post(`/petty-cash/${id}/toggle-approval`, {
          _token: '<?php echo e(csrf_token()); ?>'
      }, function(response) {
          loadData();
          loadStats();
      }).fail(function() {
          alert('Failed to update status');
      });
  }

  // View Full Remark
  window.viewRemark = function(remark) {
      $('#full_remark_text').text(remark || 'No remark provided.');
      $('#viewRemarkModal').modal('show');
  }

  function loadData(page = 1) {
    let search = $('#filter_search').val();
    let expense_id = $('#filter_expense').val();
    let department_id = $('#filter_department').val();
    let status = $('#filter_status').val();
    let from_date = $('#filter_from_date').val();
    let to_date = $('#filter_to_date').val();
    let month = $('#filter_month').val();

    $.ajax({
      url: "<?php echo e(route('petty-cash.fetch')); ?>",
      data: {
        page: page,
        search: search,
        expense_id: expense_id,
        department_id: department_id,
        status: status,
        from_date: from_date,
        to_date: to_date,
        month: month
      },
      beforeSend: function() {
        $('#pettyCashTable tbody').html(`
          <tr>
            <td colspan="7" class="text-center py-4">
              <i class="bi bi-arrow-repeat spin"></i> Loading data...
            </td>
          </tr>
        `);
      },
      success: function(response) {
        let rows = '';
        if(response.data.length > 0) {
          response.data.forEach(item => {
            let statusBadge = item.is_approved == 1 
                ? '<span class="badge badge-approved">Approved</span>' 
                : '<span class="badge badge-pending">Pending</span>';
            
            let date = new Date(item.created_at).toLocaleDateString('en-GB', {
               day: '2-digit', month: 'short', year: 'numeric'
            });

            // Handle attachment display if needed
            let attachmentIcon = '';
            if(item.attachment) {
               attachmentIcon = `<a href="/storage/${item.attachment}" target="_blank" class="text-primary ms-2"><i class="bi bi-paperclip"></i></a>`;
            }

            rows += `
              <tr>
                <td>${date}</td>
                <td>${item.department ? item.department.name : '-'}</td>
                <td>${item.expense ? item.expense.name : 'N/A'} ${attachmentIcon}</td>
                <td class="fw-bold">₹${parseFloat(item.price).toFixed(2)}</td>
                <td>
                  ${item.remark ? (item.remark.length > 7 ? 
                    `<span style="cursor:pointer; text-decoration: underline dotted;" onclick="viewRemark(\`${item.remark.replace(/`/g, '\\`').replace(/\n/g, '\\n').replace(/\r/g, '\\r')}\`)">${item.remark.substring(0, 7)}...</span>` : 
                    item.remark) : '-'}
                </td>
                <td style="cursor:pointer;" onclick="toggleApproval(${item.id})">${statusBadge}</td>
                <td>
                  ${item.is_approved == 0 ? `
                  <button class="btn-action edit-btn" 
                    data-id="${item.id}"
                    data-expense-id="${item.expense_id}"
                    data-department-id="${item.department_id}"
                    data-price="${item.price}"
                    data-remark="${item.remark || ''}"
                    data-approved="${item.is_approved}"
                    title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action text-danger" onclick="deleteEntry(${item.id})">
                    <i class="bi bi-trash"></i>
                  </button>` : ''}
                </td>
              </tr>
            `;
          });
        } else {
          rows = `<tr><td colspan="7" class="text-center py-4 text-muted">No data found</td></tr>`;
        }
        $('#pettyCashTable tbody').html(rows);

        // Render Pagination
        let links = '';
        if (response.links) {
          response.links.forEach(link => {
            let activeClass = link.active ? 'active' : '';
            let disabledClass = link.url ? '' : 'disabled';
            let label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
            if (link.url) {
                links += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadData(${link.url.split('page=')[1]})">${label}</a></li>`;
            } else {
                links += `<li class="page-item ${disabledClass}"><span class="page-link">${label}</span></li>`;
            }
          });
          $('#paginationLinks').html(`<ul class="pagination pagination-sm">${links}</ul>`);
        }
      }
    }); // end ajax
  }
  
  // Expose loadData to global scope if needed
  window.loadData = loadData;

});
</script>
<!-- Toastr for notifications (if usually present in layout) -->
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/pettycash/index.blade.php ENDPATH**/ ?>