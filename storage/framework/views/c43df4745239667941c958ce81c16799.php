<?php $__env->startSection('title', 'Subscription Customers'); ?>
<?php $__env->startSection('page_title', 'Subscription Customers'); ?>

<?php $__env->startPush('styles'); ?>
<style>

  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }
  .data-table-card .table-scroll {
    width: 100%;
    overflow-x: auto;
    padding: 0.5rem 0.75rem 1rem;
    margin-bottom: 0;
    background: transparent;
  }
  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 800px; /* Adjusted for both views */
    background: transparent;
    font-size: 0.85rem;
    table-layout: auto;
  }
  .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #f1f3f5;
    white-space: nowrap;
    font-family: Montserrat;
    text-align: center; /* Center headers */
  }
  .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
    text-align: center; /* Center cells */
  }
  .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
    transition: all 0.2s ease;
  }
  .action-btn {
     text-decoration: none;
     padding: 0.375rem 0.75rem;
     border-radius: 6px;
     font-size: 0.75rem;
     font-weight: 600;
     border: none;
     cursor: pointer;
     transition: all 0.3s ease;
     font-family: Montserrat, sans-serif;
     margin: 0 0.25rem;
     display: inline-block;
  }
  .action-btn.btn-primary {
    background: #434afa;  
    color: white;
  }
  .action-btn.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    color: white;
  }
  .action-btn.btn-danger {
    background: #ef4444;
    color: white;
  }
  .action-btn.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
  }

  /* Status Badges */
  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    font-family: Montserrat, sans-serif;
  }
  .status-badge.active { background: #d1fae5; color: #065f46; }
  .status-badge.expired { background: #fee2e2; color: #991b1b; }
  .status-badge.cancelled { background: #f3f4f6; color: #374151; }
  .status-badge.pending { background: #fef3c7; color: #92400e; }
  .status-badge.suspended { background: #fef3c7; color: #92400e; }

  /* Search Bar */
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
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

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
    width: 100%;
    min-height: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
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
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
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
    font-family: Montserrat;
  }
  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  /* Toggle Switch */
  .view-mode-toggle {
    margin-bottom: 1rem;
  }
  .btn-group .btn-outline-primary {
      color: #434afa;
      border-color: #434afa;
  }
  .btn-group .btn-check:checked + .btn-outline-primary {
      background-color: #434afa;
      color: white;
      border-color: #434afa;
  }
  .btn-group .btn-outline-primary:hover {
      background-color: #434afa;
      color: white;
  }
  .form-switch {
      padding-left: 2.8rem;
  }

  /* Recurrence & Form Styles */
  .form-accent {
    background: linear-gradient(135deg, #eef2ff 0%, #f0f9ff 100%);
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
  }
  .chip-toggle {
    display:inline-flex; align-items:center; gap:8px; padding:4px 10px; border-radius:999px;
    background:#ffffff; border:1px solid #dbeafe; color:#1d4ed8; font-weight:600; font-size:12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
  }
  .chip-toggle .form-check-input { margin-left:8px; width:36px; height:18px; }
  .chip-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .chip-row .title { font-weight:700; letter-spacing:.2px; color:#0f172a; font-size:0.9rem; }

  /* Custom Toast */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1060;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .custom-toast {
    background: #fff;
    color: #333;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    transform: translateX(120%);
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 5px solid #434afa;
    font-family: Montserrat, sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
  }
  .custom-toast.show { transform: translateX(0); }
  .custom-toast.success { border-left-color: #10b981; }
  .custom-toast.error { border-left-color: #ef4444; }
  .custom-toast i { font-size: 1.25rem; }
  .custom-toast.success i { color: #10b981; }
  .custom-toast.error i { color: #ef4444; }
  
  /* Filter Box Styles */
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
  .filterBox .form-control-modern option { color: #000; background: #fff; font-family: Montserrat, sans-serif; }
  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <i class="bi bi-people-fill text-white" style="font-size: 1.1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Customer</div>
        <div class="summary-card-value"><?php echo e($totalCustomers); ?></div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-sky">
        <i class="bi bi-credit-card-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Subs</div>
        <div class="summary-card-value"><?php echo e($totalSubscriptions); ?></div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);">
        <i class="bi bi-calendar-event-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Coming Due (15 Days)</div>
        <div class="summary-card-value"><?php echo e($comingDueCount); ?></div>
      </div>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444);">
        <i class="bi bi-exclamation-triangle-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Over Due</div>
        <div class="summary-card-value"><?php echo e($overDueCount); ?></div>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
      <div class="d-flex gap-3">
          <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="view_group_checkbox">
              <label class="form-check-label fw-bold small" for="view_group_checkbox" style="font-family: Montserrat;">Group View</label>
          </div>
          <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="view_email_checkbox">
              <label class="form-check-label fw-bold small" for="view_email_checkbox" style="font-family: Montserrat; color:#f97316;">Email View <i class="bi bi-envelope"></i></label>
          </div>
      </div>
  </div>

  <!-- Subscription Filters -->
  <div class="filterBox mb-2">
    <div class="mb-2">
      <label for="filter_customer" class="form-label-modern"><i class="bi bi-people"></i> Customer</label>
      <select class="form-control form-control-modern" id="filter_customer">
        <option value="">All Customers</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_product" class="form-label-modern"><i class="bi bi-box-seam"></i> Product</label>
      <select class="form-control form-control-modern" id="filter_product">
        <option value="">All Products</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_status" class="form-label-modern"><i class="bi bi-tag"></i> Status</label>
      <select class="form-control form-control-modern" id="filter_status">
        <option value="">All Statuses</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_recurrence" class="form-label-modern"><i class="bi bi-arrow-repeat"></i> Recurrence</label>
      <select class="form-control form-control-modern" id="filter_recurrence">
        <option value="">All Types</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
        <option value="quarterly">Quarterly</option>
        <option value="half_yearly">Half Yearly</option>
        <option value="yearly">Yearly</option>
        <option value="one_time">One Time</option>
      </select>
    </div>
    <div class="mb-2">
      <label for="filter_active" class="form-label-modern"><i class="bi bi-toggle-on"></i> Active/Inactive</label>
      <select class="form-control form-control-modern" id="filter_active">
        <option value="">All</option>
        <option value="1">Active</option>
        <option value="0">Inactive</option>
      </select>
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search subscriptions..." autocomplete="off">
    </div>
    <button class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
      <i class="bi bi-plus me-1"></i>Add Subscription
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="data_table">
          <thead id="table_head">
            <!-- Dynamic Headers -->
          </thead>
          <tbody id="table_body">
             <!-- Dynamic Body -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="rangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
     <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Add Subscription Modal -->
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="border-radius:0;background-color:#434afa;">
        <h5 class="modal-title" id="addSubscriptionModalLabel">Add Subscription</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="subscriptionForm">
          <?php echo csrf_field(); ?>
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="subscription_name" class="form-label">Subscription Name</label>
              <input type="text" class="form-control" id="subscription_name" name="subscription_name" placeholder="Enter subscription name (for manual entry)">
              <small class="text-muted">You can either select a customer & product, or just enter a subscription name.</small>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Customer</label>
              <select class="form-control" id="customer_id" name="customer_id">
                  <option value="">Select Customer</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="product_id" class="form-label">Product</label>
              <select class="form-control" id="product_id" name="product_id">
                <option value="">Select Product</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
              <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Billing Type</label>
              <div class="d-flex gap-3 align-items-center mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="billing_type" id="billing_type_prepaid" value="Prepaid">
                  <label class="form-check-label" for="billing_type_prepaid">Prepaid</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="billing_type" id="billing_type_postpaid" value="Postpaid" checked>
                  <label class="form-check-label" for="billing_type_postpaid">Postpaid</label>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="status_modal" name="status" required>
                <option value="">Select</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                  Active
                </label>
              </div>
            </div>
            <!-- Recurring (Top, colorful) -->
            <div class="col-md-12 mb-3">
              <div class="form-accent mb-2" id="recurrenceSection">
                <div class="chip-row">
                  <div class="title"><i class="bi bi-arrow-repeat me-1 text-primary"></i>Recurring</div>
                  <label class="chip-toggle">
                    Enable
                    <input class="form-check-input" type="checkbox" id="is_recurring" checked>
                  </label>
                </div>
                <div id="recurrencePanel" class="mt-2">
                  <div class="row g-2">
                    <div class="col-6 col-md-4">
                      <label class="form-label">Repeat</label>
                      <select id="recurrence_type" class="form-select form-select-sm">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="half_yearly">Half Yearly</option>
                        <option value="yearly">Yearly</option>
                      </select>
                    </div>
                    <div class="col-6 col-md-4">
                      <label class="form-label">Alert before (days)</label>
                      <input type="number" min="0" id="alert_before_days" class="form-control form-control-sm" placeholder="e.g. 3">
                    </div>
                    <div class="col-12 col-md-4" id="recurrence_interval_container">
                      <label class="form-label">Every</label>
                      <input type="number" min="1" value="1" id="recurrence_interval" class="form-control form-control-sm" placeholder="Interval">
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

                </div>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" style="background-color:#434afa;" id="saveSubscriptionBtn">Save Subscription</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Subscription Modal -->
<div class="modal fade" id="editSubscriptionModal" tabindex="-1" aria-labelledby="editSubscriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="border-radius:0;background-color:#434afa;">
        <h5 class="modal-title" id="editSubscriptionModalLabel">Edit Subscription</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editSubscriptionForm">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_subscription_id" name="subscription_id">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="edit_subscription_name" class="form-label">Subscription Name</label>
              <input type="text" class="form-control" id="edit_subscription_name" name="subscription_name" placeholder="Enter subscription name (for manual entry)">
              <small class="text-muted">You can either select a customer & product, or just enter a subscription name.</small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_customer_id" class="form-label">Customer</label>
              <select class="form-control" id="edit_customer_id" name="customer_id">
                  <option value="">Select Customer</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_product_id" class="form-label">Product</label>
              <select class="form-control" id="edit_product_id" name="product_id">
                <option value="">Select Product</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_amount" class="form-label">Amount <span class="text-danger">*</span></label>
              <input type="number" step="0.01" class="form-control" id="edit_amount" name="amount" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Billing Type</label>
              <div class="d-flex gap-3 align-items-center mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="edit_billing_type" id="edit_billing_type_prepaid" value="Prepaid">
                  <label class="form-check-label" for="edit_billing_type_prepaid">Prepaid</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="edit_billing_type" id="edit_billing_type_postpaid" value="Postpaid">
                  <label class="form-check-label" for="edit_billing_type_postpaid">Postpaid</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="edit_billing_type" id="edit_billing_type_none" value="">
                  <label class="form-check-label" for="edit_billing_type_none">None</label>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_status_modal" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_status_modal" name="status" required>
                <option value="">Select</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="edit_start_date" name="start_date" required disabled>
            </div>
            <div class="col-md-6 mb-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                <label class="form-check-label" for="edit_is_active">
                  Active
                </label>
              </div>
            </div>
            <!-- Recurring (Top, colorful) -->
            <div class="col-md-12 mb-3">
              <div class="form-accent mb-2" id="editRecurrenceSection">
                <div class="chip-row">
                  <div class="title"><i class="bi bi-arrow-repeat me-1 text-primary"></i>Recurring</div>
                  <label class="chip-toggle">
                    Enable
                    <input class="form-check-input" type="checkbox" id="edit_is_recurring">
                  </label>
                </div>
                <div id="edit_recurrencePanel" class="mt-2" style="display:none;">
                  <div class="row g-2">
                    <div class="col-6 col-md-4">
                      <label class="form-label">Repeat</label>
                      <select id="edit_recurrence_type" class="form-select form-select-sm">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="half_yearly">Half Yearly</option>
                        <option value="yearly">Yearly</option>
                      </select>
                    </div>
                    <div class="col-6 col-md-4">
                      <label class="form-label">Alert before (days)</label>
                      <input type="number" min="0" id="edit_alert_before_days" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 col-md-4" id="edit_recurrence_interval_container">
                      <label class="form-label">Every</label>
                      <input type="number" min="1" value="1" id="edit_recurrence_interval" class="form-control form-control-sm">
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
                  <!-- Hidden End Date Field Logic Handled by JS -->
                  <input type="hidden" id="edit_recurrence_end_date" name="recurrence_end_date">

                </div>
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <label for="edit_notes" class="form-label">Notes</label>
              <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" style="background-color:#434afa;" id="updateSubscriptionBtn">Update Subscription</button>
      </div>
    </div>
  </div>
</div>

<!-- View Subscription Modal -->
<div class="modal fade" id="viewSubscriptionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="border-radius:0;background-color:#434afa; color:white;">
        <h5 class="modal-title">Subscription Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="fw-bold text-muted small">Customer</label>
            <div id="view_customer_name" class="fw-bold"></div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-muted small">Product</label>
            <div id="view_product_name" class="fw-bold"></div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-muted small">Notes</label>
            <div id="view_notes" class="p-3 bg-light rounded text-break" style="min-height: 60px;"></div>
        </div>
        <hr>
        <div class="row">
           <div class="col-6 mb-2">
                <label class="fw-bold text-muted small">Amount</label>
                <div id="view_amount"></div>
           </div>
           <div class="col-6 mb-2">
                <label class="fw-bold text-muted small">Status</label>
                <div id="view_status"></div>
           </div>
           <div class="col-6 mb-2">
                <label class="fw-bold text-muted small">Next Due</label>
                <div id="view_next_due"></div>
           </div>
           <div class="col-6 mb-2">
                <label class="fw-bold text-muted small">Recurrence</label>
                <div id="view_recurrence"></div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentView = 'individual'; // Default is Individual
    let subscriptionStatuses = [];

    // Helper: Show Toast
    function showToast(message, type = 'success') {
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const toastHtml = `
            <div class="custom-toast ${type}">
                <i class="bi ${icon}"></i>
                <span>${message}</span>
            </div>
        `;
        const $toast = $(toastHtml);
        $('#toastContainer').append($toast);
        setTimeout(() => $toast.addClass('show'), 100);
        setTimeout(() => {
            $toast.removeClass('show');
            setTimeout(() => $toast.remove(), 400);
        }, 4000);
    }
    
    // View Mode Toggle (Checkbox)
    $('#view_group_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#view_email_checkbox').prop('checked', false);
            currentView = 'group';
        } else {
            currentView = 'individual';
        }
        currentPage = 1; // Reset to page 1
        loadData(currentPage);
        
        // Update placeholder
        $('#search').attr('placeholder', currentView === 'group' ? 'Search customers...' : 'Search subscriptions...');
    });

    $('#view_email_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $('#view_group_checkbox').prop('checked', false);
            currentView = 'email';
        } else {
            currentView = 'individual';
        }
        currentPage = 1; // Reset to page 1
        loadData(currentPage);
        
        // Update placeholder
        $('#search').attr('placeholder', 'Search subscriptions...');
    });

    // Load Data based on current View
    function loadData(page = 1) {
        const search = $('#search').val();
         
        // Gather filter values
        const customer_id = $('#filter_customer').val();
        const product_id = $('#filter_product').val();
        const status = $('#filter_status').val();
        const recurrence_type = $('#filter_recurrence').val();
        const is_active = $('#filter_active').val();

        // Convert recurrence value (custom logic)
        let is_recurring = '';
        let rec_type = '';
        if (recurrence_type === 'one_time') {
             is_recurring = '0';
        } else if (recurrence_type) {
             is_recurring = '1';
             rec_type = recurrence_type;
        }

        const params = {
             page: page,
             search: search,
             customer_id: customer_id,
             products_id: product_id,
             status: status,
             is_recurring: is_recurring,
             recurrence_type: rec_type,
             is_active: is_active
        };
        
        if (currentView === 'group') {
             loadCustomers(page, params);
        } else if (currentView === 'email') {
             loadEmailView();
        } else {
             loadSubscriptions(page, params);
        }
    }

    // Load customers (Group View)
    function loadCustomers(page, params) {
         // Setup Table Headers for Group
         $('#table_head').html(`
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Total Subs</th>
              <th>Company</th>
              <th class="text-center">Action</th>
            </tr>
         `);
         $('#table_body').html('<tr><td colspan="5" class="text-center py-4"><i class="bi bi-arrow-repeat spin"></i> Loading...</td></tr>');

        $.ajax({
            url: '<?php echo e(route("subscriptions.index")); ?>',
            type: 'GET',
            data: params,
            success: function(response) {
                renderCustomersTable(response);
            },
            error: function(xhr) {
                console.error("Error loading customers:", xhr);
                $('#table_body').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // Load email view data
    function loadEmailView() {
         // Setup Table Headers for Email View
         $('#table_head').html(`
            <tr>
              <th style="text-align:left;">Customer</th>
              <th style="text-align:left;">Product</th>
              <th style="text-align:right;">Amount</th>
              <th class="text-center">Due Date</th>
              <th class="text-center">Status</th>
            </tr>
         `);
         $('#table_body').html('<tr><td colspan="5" class="text-center py-4"><i class="bi bi-arrow-repeat spin"></i> Loading Email View...</td></tr>');

        $.ajax({
            url: '<?php echo e(route("subscriptions.email-view-data")); ?>',
            type: 'GET',
            success: function(response) {
                renderEmailTable(response);
            },
            error: function(xhr) {
                console.error("Error loading email view:", xhr);
                $('#table_body').html('<tr><td colspan="5" class="text-center text-danger">Error loading email view data</td></tr>');
            }
        });
    }

    // Load subscriptions (Individual View)
    function loadSubscriptions(page, params) {
         // Setup Table Headers
         $('#table_head').html(`
            <tr>
              <th>Customer</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Recurrence</th>
              <th>Next Due</th>
              <th>Total Due</th>
              <th>Alert Before</th>
              <th>Active</th>
              <th>Action</th>
            </tr>
         `);
         $('#table_body').html('<tr><td colspan="9" class="text-center py-4"><i class="bi bi-arrow-repeat spin"></i> Loading...</td></tr>');

        // Determine correct endpoint. existing 'fetch-all' might not support filtering? 
        // Controller typically uses 'filterSubscriptions' or modifies 'index/fetchAll'. 
        // Let's use 'filter-subscriptions' route if it supports advanced filtering, or check Controller.
        // Controller has 'filterSubscriptions' method! Route name 'subscriptions.filter'?
        // Assuming route name 'subscriptions.filter' exists or we use 'fetch-all' if it's updated. 
        // Wait, Controller 'fetchAllSubscriptions' supports simplified search. 'filterSubscriptions' supports all.
        // Let's use 'subscriptions.filter' Route. If not defined, we might need to assume 'fetch-all' works or update it.
        // Checking Controller: 'filterSubscriptions' supports status, products_id, is_recurring etc. 
        // Route likely: Route::get('/filter', ...) ->name('subscriptions.filter')
        // Let's try 'subscriptions.filter'.
        
        let url = '<?php echo e(route("subscriptions.filter")); ?>'; 

        $.ajax({
            url: url,
            type: 'GET',
            data: params,
            success: function(response) {
                renderSubscriptionsTable(response);
            },
            error: function(xhr) {
                console.error("Error loading subscriptions:", xhr);
                $('#table_body').html('<tr><td colspan="9" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // ... (render functions remain) ...

    // Trigger loads on Filter Change
    $('#filter_customer, #filter_product, #filter_status, #filter_recurrence, #filter_active').on('change', function() {
        currentPage = 1;
        loadData(currentPage);
    });

    // ... (rest of search/pagination listeners) ...

    // Render Group Table
    function renderCustomersTable(data) {
        let html = '';
        if (!data.data || data.data.length === 0) {
            html = `<tr>
                      <td colspan="5" class="text-center py-4">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                        No customers found with active subscriptions.
                      </td>
                    </tr>`;
        } else {
            $.each(data.data, function(index, customer) {
                const subCount = customer.subscriptions_count || 0;
                const viewUrl = `/subscriptions/customer/${customer.id}`;
                
                html += `<tr>
                          <td>
                            <a href="${viewUrl}" style="font-weight:600; text-decoration:none; color: #434AFA;">
                                ${customer.name || 'N/A'}
                            </a>
                          </td>
                          <td>${customer.email || 'N/A'}</td>
                          <td>
                              <span class="badge bg-light text-dark border">${subCount}</span>
                          </td>
                          <td>${customer.company_name || 'N/A'}</td>
                          <td class="text-center">
                              <a href="${viewUrl}" class="action-btn btn-primary" title="View Subscriptions">
                                <i class="bi bi-eye"></i>
                              </a>
                          </td>
                        </tr>`;
            });
        }
        $('#table_body').html(html);
        $('#paginationLinks').show();
        buildPagination($('#paginationLinks'), data.links);
        updateRangeInfo(data.from, data.to, data.total);
    }
    
    // Render Email Table
    function renderEmailTable(data) {
        let html = '';
        
        function getStatusStyle(s) {
            s = s.toLowerCase();
            if (s.includes('paid') || s.includes('received')) return "background:#d1fae5;color:#065f46;";
            if (s.includes('pending')) return "background:#fef3c7;color:#92400e;";
            if (s.includes('invoice') || s.includes('sent')) return "background:#dbeafe;color:#1e40af;";
            if (s.includes('overdue')) return "background:#fee2e2;color:#991b1b;";
            return "background:#f3f4f6;color:#374151;";
        }
        
        function renderItems(items, title, titleColor) {
            if (!items || items.length === 0) return '';
            let block = `<tr style="background:#f8f9fa;"><td colspan="5" style="text-align:left; font-weight:700; color:${titleColor}; padding:10px 15px;">${title} <span class="text-muted fw-normal ms-2">(${items.length})</span></td></tr>`;
            
            items.forEach(item => {
                let amount = parseFloat(item.amount || 0).toLocaleString('en-IN');
                let dateDisplay = item.due_date ? new Date(item.due_date).toLocaleDateString('en-GB') : '-';
                
                let dateStyle = "";
                if (title.includes('Overdue')) {
                    dateStyle = "color:#dc2626;font-weight:700;";
                } else if (item.notes && item.notes.includes('Due in')) {
                    dateStyle = "color:#d97706;font-weight:700;";
                    dateDisplay += `<div style="font-size:9px;">${item.notes}</div>`;
                }
                
                let stStyle = getStatusStyle(item.status);
                
                let statusOptions = '';
                if (subscriptionStatuses && subscriptionStatuses.length > 0) {
                    subscriptionStatuses.forEach(s => {
                        if(s.status_name) {
                            let selected = s.status_name.toLowerCase() === item.status.toLowerCase() ? 'selected' : '';
                            statusOptions += `<option value="${s.status_name}" ${selected} style="color:#000;background:#fff;">${s.status_name}</option>`;
                        }
                    });
                } else {
                    statusOptions = `<option value="${item.status}" selected>${item.status}</option>`;
                }
                
                let selectHtml = `<select class="email-status-update form-select" data-id="${item.id}" data-history-id="${item.history_id}" style="font-size:10px;padding:3px 20px 3px 8px;border-radius:12px;font-weight:600;border:none;cursor:pointer;background-color:transparent;appearance:auto;-webkit-appearance:auto;-moz-appearance:auto;${stStyle}">${statusOptions}</select>`;
                
                block += `<tr>
                    <td style="text-align:left; font-weight:600;">${item.customer}</td>
                    <td style="text-align:left; color:#4b5563;">${item.product}</td>
                    <td style="text-align:right; font-weight:bold;">₹${amount}</td>
                    <td class="text-center" style="${dateStyle}">${dateDisplay}</td>
                    <td class="text-center"><div style="display:inline-block; border-radius:12px; ${stStyle}">${selectHtml}</div></td>
                </tr>`;
            });
            return block;
        }

        if (data.overdueItems && data.overdueItems.length > 0) {
            html += renderItems(data.overdueItems, "⚠️ Overdue Subscriptions", "#dc2626");
        }

        if (data.statusGroups) {
            for (const [statusName, items] of Object.entries(data.statusGroups)) {
                if (items && items.length > 0) {
                    html += renderItems(items, "➤ Status: " + statusName, "#111827");
                }
            }
        }

        if (!html) {
            html = `<tr><td colspan="5" class="text-center py-4 text-muted">No active subscriptions found.</td></tr>`;
        }

        $('#table_body').html(html);
        $('#paginationLinks').hide(); // Hide pagination for email view
        $('#rangeInfo').text('Showing all data for Email View');
    }
    
    // Render Individual Table (Matching Customer Subscriptions Blade, plus Customer column)
    function renderSubscriptionsTable(data) {
        let html = '';
        if (!data.data || data.data.length === 0) {
            html = `<tr>
                      <td colspan="9" class="text-center py-4">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                        No subscriptions found.
                      </td>
                    </tr>`;
        } else {
             const trunc = (str, len = 20) => (str && str.length > len) ? str.substring(0, len) + '...' : (str || 'N/A');
             const escape = (str) => str ? String(str).replace(/"/g, '&quot;') : '';

            $.each(data.data, function(index, sub) {
                const customerName = sub.customer ? sub.customer.name : 'N/A';
                const fullProductName = sub.product?.product_name || sub.subscription_name || 'N/A';
                const displayProductName = trunc(fullProductName);
                const fullNotes = sub.notes || 'N/A';

                const amount = parseFloat(sub.amount || 0).toLocaleString('en-IN');
                const displayAmount = `₹${amount}`;

                const recurrenceType = sub.is_recurring 
                    ? (sub.recurrence_type ? sub.recurrence_type.charAt(0).toUpperCase() + sub.recurrence_type.slice(1) : 'Recurring') 
                    : 'One Time';
                
                const alertBefore = (sub.alert_before_days !== null && sub.alert_before_days !== undefined) ? sub.alert_before_days : 'N/A';

                const isActive = sub.is_active !== false; 
                const activeColor = isActive ? '#10b981' : '#6b7280';
                const activeTitle = isActive ? 'Active' : 'Inactive';
                const activeBadge = `<div style="width:10px; height:10px; border-radius:50%; background-color:${activeColor}; display:inline-block;" title="${activeTitle}"></div>`;

                const nextDue = (sub.latest_history && sub.latest_history.due_date)
                    ? new Date(sub.latest_history.due_date).toLocaleDateString('en-GB') 
                    : 'N/A';
                
                const totalDues = sub.histories_count || 0;
                
                // Status for modal view
                const statusName = sub.status || 'N/A';
                const displayStatus = (statusName && statusName !== 'N/A') ? statusName.charAt(0).toUpperCase() + statusName.slice(1) : 'N/A';

                // Data attrs for View Modal
                const dataAttrs = `
                    data-customer="${escape(customerName)}" 
                    data-product="${escape(fullProductName)}"
                    data-notes="${escape(fullNotes)}"
                    data-amount="${displayAmount}"
                    data-status="${displayStatus}"
                    data-recurrence="${recurrenceType}"
                    data-next-due="${nextDue}"
                `;

                const historyUrl = `/subscriptions/${sub.id}/history`;

                html += `<tr>
                          <td style="font-weight:600; color:#333;">${trunc(customerName, 15)}</td>
                          <td class="view-details-trigger text-primary" style="cursor:pointer;" ${dataAttrs}>${displayProductName}</td>
                          <td>${displayAmount}</td>
                          <td>${recurrenceType}</td>
                          <td>${nextDue}</td>
                          <td>${totalDues}</td>
                          <td>${alertBefore}</td>
                          <td>${activeBadge}</td>
                          <td>
                              <a href="${historyUrl}" class="action-btn btn-primary" title="Details"><i class="bi bi-eye"></i></a>
                              <button class="action-btn btn-primary edit-subscription" data-id="${sub.id}"><i class="bi bi-pencil"></i></button>
                              <button class="action-btn btn-danger delete-subscription" data-id="${sub.id}"><i class="bi bi-trash"></i></button>
                          </td>
                        </tr>`;
            });
        }
        $('#table_body').html(html);
        $('#paginationLinks').show();
        buildPagination($('#paginationLinks'), data.links);
        updateRangeInfo(data.from, data.to, data.total);
    }
    
    function updateRangeInfo(from, to, total) {
         $('#rangeInfo').text(`Showing ${from || 0}-${to || 0} from ${total || 0} data`);
    }

    // Pagination Builder
    function buildPagination($container, links) {
        $container.empty();
        if (!links || links.length === 0) return;

        let html = '';
        links.forEach(link => {
            if (link.url !== null) {
                let pageParam = '1';
                try {
                     let parts = link.url.split('page=');
                     if(parts.length > 1) pageParam = parts[1].split('&')[0];
                } catch(e) { }

                html += `<li class="page-item ${link.active ? 'active' : ''}">
                    <a href="#" class="page-link" data-page="${pageParam}">${link.label}</a>
                </li>`;
            } else {
                html += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
        });
        $container.html(html);
    }

    // Pagination Click
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadData(page);
        }
    });

    // Search Debounce
    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1; // Reset to page 1 on search
            loadData(currentPage);
        }, 300);
    });

    // Initial Load based on view
    loadData();

    // --- Modal & Form Logic ---

    // Load subscription statuses from master
    $.ajax({
        url: '<?php echo e(route("subscription-status.list")); ?>',
        type: 'GET',
        success: function(data) {
            subscriptionStatuses = data || [];
            
            // Populate Dropdowns in Add/Edit Modals AND Filters
            const dropdowns = ['#status_modal', '#edit_status_modal', '#filter_status'];
            dropdowns.forEach(sel => {
                const isFilter = sel === '#filter_status';
                const defaultText = isFilter ? 'All Statuses' : 'Select';
                $(sel).empty().append(`<option value="">${defaultText}</option>`);
                
                $.each(subscriptionStatuses, function(index, status) {
                    const name = status.status_name || '';
                    if (!name) return;
                    // Default Pending for Add Modal
                    const isSelected = (sel === '#status_modal' && name.toLowerCase() === 'pending') ? 'selected' : '';
                    $(sel).append(`<option value="${name}" ${isSelected}>${name}</option>`);
                });
            });
        },
        error: function() {
            console.error('Failed to load subscription statuses.');
        }
    });

    // Load customers for modal AND filter
    $.ajax({
        url: '<?php echo e(route("subscriptions.customers")); ?>',
        type: 'GET',
        success: function(data) {
            const dropdowns = ['#customer_id', '#edit_customer_id', '#filter_customer'];
            dropdowns.forEach(sel => {
                const isFilter = sel === '#filter_customer';
                const defaultText = isFilter ? 'All Customers' : 'Select Customer';
                $(sel).empty().append(`<option value="">${defaultText}</option>`);
                
                $.each(data, function(index, customer) {
                     $(sel).append(`<option value="${customer.id}">${customer.name || 'N/A'}</option>`);
                });
            });
        }
    });

    // Load Products for Dropdown AND Filter
    $.ajax({
        url: '<?php echo e(route("subscriptions.products")); ?>',
        type: 'GET',
        success: function(data) {
            // Modal Options
            const modalOptions = '<option value="">Select Product</option>' + 
                data.map(product => `<option value="${product.id}">${product.product_name}</option>`).join('');
            $('#product_id').html(modalOptions);
            $('#edit_product_id').html(modalOptions);
            
            // Filter Options
            const filterOptions = '<option value="">All Products</option>' + 
                data.map(product => `<option value="${product.id}">${product.product_name}</option>`).join('');
            $('#filter_product').html(filterOptions);
        },
        error: function(xhr) {
            console.error("Error loading products:", xhr.responseText);
        }
    });

    // Recurrence UI logic (Add Modal)
    $('#is_recurring').on('change', function(){ $('#recurrencePanel').toggle(this.checked); });
    $('#recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#recurrence_weekly, #recurrence_monthly').hide();
        if (t === 'quarterly' || t === 'half_yearly') {
            $('#recurrence_interval_container').hide(); $('#recurrence_interval').val(1);
        } else { $('#recurrence_interval_container').show(); }
        if (t === 'weekly') $('#recurrence_weekly').show();
        if (t === 'monthly') $('#recurrence_monthly').show();
    }).trigger('change');

    // Recurrence UI logic (Edit Modal)
    $('#edit_is_recurring').on('change', function(){ $('#edit_recurrencePanel').toggle(this.checked); });
    $('#edit_recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#edit_recurrence_weekly, #edit_recurrence_monthly').hide();
        if (t === 'quarterly' || t === 'half_yearly') {
            $('#edit_recurrence_interval_container').hide(); $('#edit_recurrence_interval').val(1);
        } else { $('#edit_recurrence_interval_container').show(); }
        if (t === 'weekly') $('#edit_recurrence_weekly').show();
        if (t === 'monthly') $('#edit_recurrence_monthly').show();
    }).trigger('change');

    // Save subscription (Add)
    $('#saveSubscriptionBtn').on('click', function() {
        // ... (Same data collection logic as before)
        const formData = new FormData();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        formData.append('subscription_name', $('#subscription_name').val());
        formData.append('customer_id', $('#customer_id').val());
        formData.append('product_id', $('#product_id').val());
        formData.append('amount', $('#amount').val());
        formData.append('billing_type', $('input[name="billing_type"]:checked').val());
        formData.append('status', $('#status_modal').val());
        formData.append('start_date', $('#start_date').val());
        formData.append('notes', $('#notes').val());
        formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);
        
        const isRecurring = $('#is_recurring').is(':checked');
        formData.append('is_recurring', isRecurring ? 1 : 0);
        if (isRecurring) {
            formData.append('recurrence_type', $('#recurrence_type').val());
            formData.append('recurrence_interval', $('#recurrence_interval').val() || 1);
            const alertDays = $('#alert_before_days').val();
            if (alertDays !== '') formData.append('alert_before_days', alertDays);
            const dows = [];
            $('input[id^="dow_"]:checked').each(function() { dows.push($(this).val()); });
            if (dows.length) { dows.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
            const dom = $('#recurrence_day_of_month').val();
            if (dom) formData.append('recurrence_day_of_month', dom);
        }
        
        $.ajax({
            url: '<?php echo e(route("subscriptions.store")); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addSubscriptionModal').modal('hide');
                    $('#subscriptionForm')[0].reset();
                    showToast('Subscription created successfully!', 'success');
                    loadData(currentPage); 
                    // Update stats if needed, or simple reload
                    // window.location.reload(); // Reload might be better for stats
                    setTimeout(() => window.location.reload(), 1000); 
                }
            },
            error: function(xhr) {
                console.error("Error:", xhr.responseText);
                showToast('Error creating subscription.', 'error');
            }
        });
    });

    // Edit Subscription Click Handler
    $(document).on('click', '.edit-subscription', function() {
        const subscriptionId = $(this).data('id');
        $.ajax({
            url: `<?php echo e(route("subscriptions.show", ":id")); ?>`.replace(':id', subscriptionId),
            type: 'GET',
            success: function(response) {
                const sub = response.subscription || response;
                $('#edit_subscription_id').val(sub.id);
                $('#edit_subscription_name').val(sub.subscription_name || '');
                $('#edit_customer_id').val(sub.customer_id);
                $('#edit_product_id').val(sub.product_id);
                $('#edit_amount').val(sub.amount);

                if (sub.billing_type === 'Postpaid') { $('#edit_billing_type_postpaid').prop('checked', true); }
                else if (sub.billing_type === 'Prepaid') { $('#edit_billing_type_prepaid').prop('checked', true); }
                else { $('#edit_billing_type_none').prop('checked', true); }

                $('#edit_status_modal').val(sub.status);
                const startDate = sub.start_date ? sub.start_date.toString().substring(0, 10) : '';
                $('#edit_start_date').val(startDate);
                $('#edit_notes').val(sub.notes);
                $('#edit_alert_before_days').val(sub.alert_before_days || '');
                $('#edit_is_active').prop('checked', sub.is_active !== false);

                const isRec = !!sub.is_recurring;
                $('#edit_is_recurring').prop('checked', isRec);
                $('#edit_recurrencePanel').toggle(isRec);
                $('#edit_recurrence_type').val(sub.recurrence_type || 'daily');
                $('#edit_recurrence_interval').val(sub.recurrence_interval || 1);
                
                $('input[id^="edit_dow_"]').prop('checked', false);
                if (Array.isArray(sub.recurrence_days_of_week)) {
                    sub.recurrence_days_of_week.forEach(k => $('#edit_dow_'+k).prop('checked', true));
                }
                $('#edit_recurrence_day_of_month').val(sub.recurrence_day_of_month || '');
                
                $('#edit_recurrence_type').trigger('change');
                $('#editSubscriptionModal').modal('show');
            },
            error: function(xhr) {
                showToast('Error loading subscription data.', 'error');
            }
        });
    });

    // Update Subscription (Edit)
    $('#updateSubscriptionBtn').on('click', function() {
        const subscriptionId = $('#edit_subscription_id').val();
        const formData = new FormData();
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        formData.append('_method', 'PUT');
        formData.append('subscription_name', $('#edit_subscription_name').val());
        formData.append('customer_id', $('#edit_customer_id').val());
        formData.append('product_id', $('#edit_product_id').val());
        formData.append('amount', $('#edit_amount').val());
        formData.append('billing_type', $('input[name="edit_billing_type"]:checked').val());
        formData.append('status', $('#edit_status_modal').val());
        formData.append('start_date', $('#edit_start_date').val());
        formData.append('notes', $('#edit_notes').val());
        formData.append('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
        
        const isRecurring = $('#edit_is_recurring').is(':checked');
        formData.append('is_recurring', isRecurring ? 1 : 0);
        if (isRecurring) {
            formData.append('recurrence_type', $('#edit_recurrence_type').val());
            formData.append('recurrence_interval', $('#edit_recurrence_interval').val() || 1);
            const alertDays = $('#edit_alert_before_days').val();
            if (alertDays) formData.append('alert_before_days', alertDays);
            
            const dows = [];
            $('input[id^="edit_dow_"]:checked').each(function() { dows.push($(this).val()); });
            if (dows.length) { dows.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
            
            const dom = $('#edit_recurrence_day_of_month').val();
            if (dom) formData.append('recurrence_day_of_month', dom);
        }
        
        $.ajax({
            url: `<?php echo e(route("subscriptions.update", ":id")); ?>`.replace(':id', subscriptionId),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success || response.message) {
                    $('#editSubscriptionModal').modal('hide');
                    showToast('Subscription updated successfully!', 'success');
                    loadData(currentPage);
                }
            },
            error: function(xhr) {
                showToast('Error updating subscription.', 'error');
            }
        });
    });

    // Delete subscription
    $(document).on('click', '.delete-subscription', function() {
        if (!confirm('Are you sure you want to delete this subscription?')) return;
        const subscriptionId = $(this).data('id');
        $.ajax({
            url: `<?php echo e(route("subscriptions.destroy", ":id")); ?>`.replace(':id', subscriptionId),
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.success || response.message) {
                    showToast('Subscription deleted successfully!', 'success');
                    loadData(currentPage);
                }
            },
            error: function(xhr) {
                showToast('Error deleting subscription.', 'error');
            }
        });
    });

    // View Details Modal Trigger
    $(document).on('click', '.view-details-trigger', function() {
        const $el = $(this);
        $('#view_customer_name').text($el.data('customer'));
        $('#view_product_name').text($el.data('product'));
        const notes = $el.data('notes');
        $('#view_notes').text((notes && notes !== 'N/A') ? notes : 'No notes available.');
        $('#view_amount').text($el.data('amount'));
        
        // Style status
        const status = String($el.data('status'));
        let statusColor = 'text-secondary';
        if(status.toLowerCase() === 'active') statusColor = 'text-success';
        if(status.toLowerCase() === 'pending') statusColor = 'text-warning';
        $('#view_status').text(status).removeClass().addClass(statusColor).addClass('fw-bold');

        $('#view_next_due').text($el.data('next-due'));
        $('#view_recurrence').text($el.data('recurrence'));
        
        $('#viewSubscriptionModal').modal('show');
    });

    // Email View Status Update
    $(document).on('change', '.email-status-update', function() {
        const subId = $(this).data('id');
        const historyId = $(this).data('history-id');
        const newStatus = $(this).val();
        
        const $select = $(this);
        $select.prop('disabled', true);
        
        $.ajax({
            url: `<?php echo e(route("subscriptions.update-status", ":id")); ?>`.replace(':id', subId),
            type: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                status: newStatus,
                history_id: historyId
            },
            success: function(response) {
                $select.prop('disabled', false);
                if (response.success || response.message) {
                    showToast('Status updated successfully!', 'success');
                    // Reload Email View to re-group based on the new status
                    loadEmailView(); 
                }
            },
            error: function(xhr) {
                $select.prop('disabled', false);
                showToast('Error updating status.', 'error');
            }
        });
    });

});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/subscription/index.blade.php ENDPATH**/ ?>