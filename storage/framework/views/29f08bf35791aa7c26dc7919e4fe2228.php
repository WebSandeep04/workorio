<?php $__env->startSection('title', 'Subscriptions'); ?>
<?php $__env->startSection('page_title', 'Subscriptions'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Recurrence UI styles from task */
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

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-label-modern {
    color: white;
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
    border-radius: 6px;
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

  .data-table-card .table-scroll {
    width: 100%;
    overflow-x: auto;
    padding: 0.5rem 0.75rem 1rem;
    margin-bottom: 0;
    background: transparent;
  }

  .data-table-card .table-scroll::-webkit-scrollbar {
    height: 8px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }

  .data-table-card .table-scroll {
    scrollbar-color: #434AFA #e4e7ec;
  }

  .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1100px;
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
    text-align: center;
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
    text-align: center;
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

  .loading-state, .empty-state {
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

  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

  /* Modal Improvements */
  .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  }

  .modal-header {
    background: #434afa;  
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
  }

  .modal-header .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    font-family: Montserrat, sans-serif;
  }

  .modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.9;
  }

  .modal-header .btn-close:hover {
    opacity: 1;
  }

  .modal-body {
    padding: 1.5rem;
    background: #f8f9fa;
  }

  .modal-body .form-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    font-family: Montserrat, sans-serif;
  }

  .modal-body .form-control,
  .modal-body .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.625rem 0.875rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    background: white;
  }

  .modal-body .form-control:focus,
  .modal-body .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
  }

  .modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
    background: white;
    border-radius: 0 0 12px 12px;
  }

  .modal-footer .btn {
    border-radius: 8px;
    padding: 0.625rem 1.5rem;
    font-weight: 600;
    font-family: Montserrat, sans-serif;
    transition: all 0.3s ease;
  }

  .modal-footer .btn-primary {
    background: #434afa;  
    border: none;
    color: white;
  }

  .modal-footer .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  }

  .modal-footer .btn-secondary {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    color: #374151;
  }

  .modal-footer .btn-secondary:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
  }

  .action-btn {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: Montserrat, sans-serif;
    margin: 0 0.25rem;
  }

  .action-btn.btn-primary {
    background: #434afa;  
    color: white;
  }

  .action-btn.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
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
  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #434AFA;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    transition: all 0.3s ease;
  }
  .back-btn:hover {
    color: #2e35d2;
    transform: translateX(-3px);
    text-decoration: none;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
    <a href="<?php echo e(route('subscriptions.index')); ?>" class="back-btn mb-0">
      <i class="bi bi-arrow-left"></i> Back to Customers
    </a>
    <h5 class="mb-0 fw-bold" style="font-family: Montserrat;"><?php echo e($customer->name); ?></h5>
  </div>
  <!-- Filters -->
  <div class="filterBox mb-2">
    <div class="mb-2">
        <label for="status" class="form-label-modern">
            <i class="bi bi-tag"></i> Status
        </label>
        <select class="form-control form-control-modern" id="status" name="status">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="product_type" class="form-label-modern">
            <i class="bi bi-box-seam"></i> Product
        </label>
        <select class="form-control form-control-modern" id="product_type" name="product_type">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="filter_is_recurring" class="form-label-modern">
            <i class="bi bi-arrow-repeat"></i> Recurring
        </label>
        <select class="form-control form-control-modern" id="filter_is_recurring" name="is_recurring">
            <option value="">All</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="filter_recurrence_type" class="form-label-modern">
            <i class="bi bi-clock-history"></i> Frequency
        </label>
        <select class="form-control form-control-modern" id="filter_recurrence_type" name="recurrence_type">
            <option value="">All</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="half_yearly">Half Yearly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="filter_is_active" class="form-label-modern">
            <i class="bi bi-toggle-on"></i> Active
        </label>
        <select class="form-control form-control-modern" id="filter_is_active" name="is_active">
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search subscriptions, customers..." />
    </div>

  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="subscriptions_table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Amount</th>
              <th>Recurrence</th>
              <th>Next Due</th>
              <th>Total Due</th>
              <th>Alert Before</th>

              <th>Active</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading subscriptions...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="subscriptionsRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationfilterLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationsearchLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationdateLinks"></ul>
</div>

<!-- Add Subscription Modal -->


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
              <input type="text" class="form-control" value="<?php echo e($customer->name); ?>" readonly>
              <input type="hidden" id="edit_customer_id" name="customer_id" value="<?php echo e($customer->id); ?>">
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateSubscriptionBtn">Update Subscription</button>
      </div>
    </div>
  </div>
</div>

<!-- View Subscription Modal -->
<div class="modal fade" id="viewSubscriptionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div id="toastContainer" class="toast-container"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;
let subscriptionStatuses = [];
const viewCustomerName = <?php echo json_encode($customer->name, 15, 512) ?>;

// ---------- Date helpers ----------
function formatDateOnly(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const t = str.indexOf('T');
    if (t > 0) return str.slice(0, t);
    const d = new Date(str);
    if (!isNaN(d.getTime())) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    return str.length >= 10 ? str.slice(0, 10) : str;
}

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

// Calculate an automatic end date from start date + recurrence settings
function calculateAutoEndDate(startDateStr, recurrenceType, interval) {
    if (!startDateStr || !recurrenceType || !interval) return null;
    const d = new Date(startDateStr);
    if (isNaN(d.getTime())) return null;

    const intVal = parseInt(interval, 10);
    if (!intVal || intVal <= 0) return null;

    const result = new Date(d.getTime());

    switch (recurrenceType) {
        case 'daily':
            result.setDate(result.getDate() + intVal);
            break;
        case 'weekly':
            result.setDate(result.getDate() + intVal * 7);
            break;
        case 'monthly':
            result.setMonth(result.getMonth() + intVal);
            break;
        case 'quarterly':
            result.setMonth(result.getMonth() + (intVal * 3));
            break;
        case 'half_yearly':
            result.setMonth(result.getMonth() + (intVal * 6));
            break;
        case 'yearly':
            result.setFullYear(result.getFullYear() + intVal);
            break;
        default:
            return null;
    }

    const yyyy = result.getFullYear();
    const mm = String(result.getMonth() + 1).padStart(2, '0');
    const dd = String(result.getDate()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd}`;
}

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
    const $info = $('#subscriptionsRangeInfo');
    if (!$info.length) return;
    const totalValue = Number(total) || 0;
    const safeStart = totalValue === 0 ? 0 : (Number(from) || 1);
    const safeEnd = totalValue === 0 ? 0 : (Number(to) || safeStart);
    $info.text(`Showing ${safeStart}-${safeEnd} from ${totalValue} data`);
}

// Helper function to render subscription table row (reduces code duplication)
function renderSubscriptionRow(subscription) {
    // Truncate helper
    const trunc = (str, len = 7) => (str && str.length > len) ? str.substring(0, len) + '...' : (str || 'N/A');
    const escape = (str) => str ? String(str).replace(/"/g, '&quot;') : '';

    // Build Status Badge and Dropdown
    const statusName = subscription.status || 'N/A';
    const displayStatus = (statusName && statusName !== 'N/A') ? statusName.charAt(0).toUpperCase() + statusName.slice(1) : 'N/A';
    const statusClass = statusName.replace(/\s+/g, '-').toLowerCase();
    
    // Grid layout ensuring the dots align vertically (Right side) while badge is centered relative to the remaining space
    const statusHtml = `
            <div class="text-center">
                <span class="status-badge ${statusClass}">${displayStatus}</span>
            </div>
    `;

    const fullProductName = subscription.product?.product_name || subscription.sales_record?.product?.product_name || subscription.subscription_name || 'N/A';
    const displayProductName = trunc(fullProductName);
    
    const fullNotes = subscription.notes || 'N/A';

    const amount = parseFloat(subscription.amount || 0).toLocaleString('en-IN');
    const displayAmount = `₹${amount}`;
    
    const recurrenceType = subscription.recurrence_type ? subscription.recurrence_type.charAt(0).toUpperCase() + subscription.recurrence_type.slice(1) : 'N/A';
    const alertBefore = (subscription.alert_before_days !== null && subscription.alert_before_days !== undefined)
        ? subscription.alert_before_days
        : 'N/A';
    const isActive = subscription.is_active !== false; 
    const activeColor = isActive ? '#10b981' : '#6b7280';
    const activeTitle = isActive ? 'Active' : 'Inactive';
    const activeBadge = `<div style="width:10px; height:10px; border-radius:50%; background-color:${activeColor}; display:inline-block;" title="${activeTitle}"></div>`;
    
    const nextDue = (subscription.latest_history && subscription.latest_history.due_date)
        ? new Date(subscription.latest_history.due_date).toLocaleDateString('en-GB') 
        : 'N/A';

    const totalDues = subscription.histories_count || 0;
    
    // Data attributes for View Details Modal
    const dataAttrs = `
        data-customer="${escape(viewCustomerName)}" 
        data-product="${escape(fullProductName)}"
        data-notes="${escape(fullNotes)}"
        data-amount="${displayAmount}"
        data-status="${displayStatus}"
        data-recurrence="${recurrenceType}"
        data-next-due="${nextDue}"
    `;
    
    return `
        <tr>
            <td class="view-details-trigger" style="cursor:pointer;" ${dataAttrs}>${displayProductName}</td>
            <td>${displayAmount}</td>
            <td>${recurrenceType}</td>
            <td>${nextDue}</td>
            <td>${totalDues}</td>
            <td>${alertBefore}</td>

            <td>${activeBadge}</td>
            <td>
                <a href="/subscriptions/${subscription.id}/history" class="action-btn btn-primary" title="Details"><i class="bi bi-eye"></i></a>
                <button class="action-btn btn-primary edit-subscription" data-id="${subscription.id}"><i class="bi bi-pencil"></i></button>
                <button class="action-btn btn-danger delete-subscription" data-id="${subscription.id}"><i class="bi bi-trash"></i></button>
            </td>
        </tr>
    `;
}

// Helper function to render table and pagination (reduces code duplication)
function renderSubscriptionsTable(data, paginationElement, hideElements) {
    if (data && data.current_page) {
        currentPage = data.current_page;
    }
    let html = '';
    if (data.data.length === 0) {
        html = '<tr><td colspan="8" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No subscriptions found.</p></td></tr>';
    } else {
        data.data.forEach(function (subscription) {
            html += renderSubscriptionRow(subscription);
        });
    }
    $('#subscriptions_table tbody').html(html);
    buildSimplePagination(paginationElement, data.current_page, data.last_page);
    // Hide other pagination elements
    $('#paginationLinks, #paginationfilterLinks, #paginationsearchLinks, #paginationdateLinks').hide();
    paginationElement.show();
    updateRangeInfo(data.from, data.to, data.total);
}

function loadSubscriptions(page = 1) {
    // Current customer context
    const customerId = "<?php echo e($customer->id); ?>";
    
    $.ajax({
        url: '<?php echo e(route("subscriptions.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            per_page: 10,
            customer_id: customerId
        },
        success: function (data) {
            renderSubscriptionsTable(data, $('#paginationLinks'), ['#paginationfilterLinks', '#paginationsearchLinks', '#paginationdateLinks']);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#subscriptions_table tbody').html('<tr><td colspan="8" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data.</p></td></tr>');
        }
    });
}

// Load filter options
$(document).ready(function() {
    $.ajax({
        url: '<?php echo e(route("subscriptions.filter-options")); ?>',
        type: 'GET',
        success: function(data) {
            // Load products
            $('#product_type').empty().append('<option value="">Select</option>');
            $.each(data.products, function(index, product) {
                $('#product_type').append(`<option value="${product.id}">${product.product_name}</option>`);
            });
        }
    });

    // Load subscription statuses from master
    $.ajax({
        url: '<?php echo e(route("subscription-status.list")); ?>',
        type: 'GET',
        success: function(data) {
            subscriptionStatuses = data || [];
            // Filter dropdown
            $('#status').empty().append('<option value="">Select</option>');
            // Modal dropdowns
            $('#status_modal').empty().append('<option value="">Select</option>');
            $('#edit_status_modal').empty().append('<option value="">Select</option>');

            $.each(subscriptionStatuses, function(index, status) {
                const name = status.status_name || '';
                if (!name) return;
                const isSelected = (name.toLowerCase() === 'pending') ? 'selected' : '';
                $('#status').append(`<option value="${name}">${name}</option>`);
                $('#status_modal').append(`<option value="${name}" ${isSelected}>${name}</option>`);
                $('#edit_status_modal').append(`<option value="${name}">${name}</option>`);
            });

            // After statuses are loaded, load subscriptions so table dropdowns have all options
            loadSubscriptions();
        },
        error: function() {
            console.error('Failed to load subscription statuses.');
        }
    });


    


    // Edit recurrence UI logic
    $('#edit_is_recurring').on('change', function(){
        $('#edit_recurrencePanel').toggle(this.checked);
    });
    $('#edit_recurrence_type').on('change', function(){
        const t = $(this).val();
        $('#edit_recurrence_weekly, #edit_recurrence_monthly').hide();
        
        // Hide "Every" interval for quarterly/half_yearly
        if (t === 'quarterly' || t === 'half_yearly') {
            $('#edit_recurrence_interval_container').hide();
            $('#edit_recurrence_interval').val(1);
        } else {
            $('#edit_recurrence_interval_container').show();
        }

        if (t === 'weekly') $('#edit_recurrence_weekly').show();
        if (t === 'monthly') $('#edit_recurrence_monthly').show();
    });
    
    // Load Products for Dropdowns (Add and Edit modals)
    function loadProductsForDropdown() {
        $.ajax({
            url: '<?php echo e(route("subscriptions.products")); ?>',
            type: 'GET',
            success: function(data) {
                const options = '<option value="">Select Product</option>' + 
                    data.map(product => `<option value="${product.id}">${product.product_name}</option>`).join('');
                
                $('#product_id').html(options);
                $('#edit_product_id').html(options);
            },
            error: function(xhr) {
                console.error("Error loading products:", xhr.responseText);
            }
        });
    }
    loadProductsForDropdown();


});





// $('#is_recurring, #start_date, #recurrence_type, #recurrence_interval').on('change', updateCreateEndDateFromRecurrence);

// Edit subscription handler
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
            


            // Set product_id
            $('#edit_product_id').val(sub.product_id);

            $('#edit_amount').val(sub.amount);

            // Set billing_type radio
            if (sub.billing_type === 'Postpaid') {
                $('#edit_billing_type_postpaid').prop('checked', true);
            } else if (sub.billing_type === 'Prepaid') {
                $('#edit_billing_type_prepaid').prop('checked', true);
            } else {
                $('#edit_billing_type_none').prop('checked', true);
            }
            $('#edit_status_modal').val(sub.status);

            // Normalize dates to YYYY-MM-DD for date inputs
            const startDate = sub.start_date ? sub.start_date.toString().substring(0, 10) : '';
            $('#edit_start_date').val(startDate);

            $('#edit_notes').val(sub.notes);
            $('#edit_alert_before_days').val(sub.alert_before_days || '');
            $('#edit_is_active').prop('checked', sub.is_active !== false); // Default to true if not set
            
            // Prefill recurrence
            const isRec = !!sub.is_recurring;
            $('#edit_is_recurring').prop('checked', isRec);
            $('#edit_recurrencePanel').toggle(isRec);
            $('#edit_recurrence_type').val(sub.recurrence_type || 'daily');
            $('#edit_recurrence_interval').val(sub.recurrence_interval || 1);
            $('#edit_recurrence_end_date').val(sub.recurrence_end_date || '');
            
            // Days of week
            $('input[id^="edit_dow_"]').prop('checked', false);
            if (Array.isArray(sub.recurrence_days_of_week)) {
                sub.recurrence_days_of_week.forEach(k => $('#edit_dow_'+k).prop('checked', true));
            }
            
            // Day of month
            $('#edit_recurrence_day_of_month').val(sub.recurrence_day_of_month || '');
            
            // Months
            $('input[id^="edit_m_"]').prop('checked', false);
            if (Array.isArray(sub.recurrence_months)) {
                sub.recurrence_months.forEach(m => $('#edit_m_'+m).prop('checked', true));
            }
            
            $('#edit_recurrence_type').trigger('change');
            $('#editSubscriptionModal').modal('show');
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
            showToast('Error loading subscription data.', 'error');
        }
    });
});

// --- Auto end-date calculation for edit ---
function updateEditEndDateFromRecurrence() {
    const isRec = $('#edit_is_recurring').is(':checked');
    const start = $('#edit_start_date').val();
    const type = $('#edit_recurrence_type').val();
    const interval = $('#edit_recurrence_interval').val() || 1;
    if (!isRec || !start) return;

    const autoEnd = calculateAutoEndDate(start, type, interval);
    if (autoEnd) {
        $('#edit_recurrence_end_date').val(autoEnd);
    }
}

// $('#edit_is_recurring, #edit_start_date, #edit_recurrence_type, #edit_recurrence_interval').on('change', updateEditEndDateFromRecurrence);



// View Details Modal Trigger
$(document).on('click', '.view-details-trigger', function() {
    const $el = $(this);
    
    $('#view_customer_name').text($el.data('customer'));
    $('#view_product_name').text($el.data('product'));
    
    const notes = $el.data('notes');
    $('#view_notes').text((notes && notes !== 'N/A') ? notes : 'No notes available.');
    
    $('#view_amount').text($el.data('amount'));
    $('#view_status').text($el.data('status'));
    $('#view_next_due').text($el.data('next-due'));
    $('#view_recurrence').text($el.data('recurrence'));
    
    const status = String($el.data('status')).toLowerCase();
    let statusColor = 'text-secondary';
    if(status === 'active') statusColor = 'text-success';
    if(status === 'pending') statusColor = 'text-warning';
    
    $('#view_status').removeClass().addClass(statusColor).addClass('fw-bold');

    $('#viewSubscriptionModal').modal('show');
});

// Update subscription
$('#updateSubscriptionBtn').on('click', function() {
    const subscriptionId = $('#edit_subscription_id').val();
    const formData = new FormData();
    formData.append('_token', '<?php echo e(csrf_token()); ?>');
    formData.append('_method', 'PUT');
    formData.append('subscription_name', $('#edit_subscription_name').val());
    formData.append('customer_id', $('#edit_customer_id').val());
    // formData.append('sales_record_id', $('#edit_sales_record_id').val());
    formData.append('product_id', $('#edit_product_id').val());
    formData.append('amount', $('#edit_amount').val());
    formData.append('billing_type', $('input[name="edit_billing_type"]:checked').val());
    formData.append('status', $('#edit_status_modal').val());
    formData.append('start_date', $('#edit_start_date').val());
    formData.append('notes', $('#edit_notes').val());
    formData.append('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
    
    // Recurrence fields (edit)
    const eIsRec = $('#edit_is_recurring').is(':checked');
    formData.append('is_recurring', eIsRec ? 1 : 0);
    if (eIsRec) {
        formData.append('recurrence_type', $('#edit_recurrence_type').val());
        formData.append('recurrence_interval', $('#edit_recurrence_interval').val() || 1);
        const alertDaysE = $('#edit_alert_before_days').val();
        if (alertDaysE) formData.append('alert_before_days', alertDaysE);
        const dowsE = [];
        ['mon','tue','wed','thu','fri','sat','sun'].forEach(function(k){
            if ($('#edit_dow_'+k).is(':checked')) dowsE.push(k);
        });
        if (dowsE.length) { dowsE.forEach(v => formData.append('recurrence_days_of_week[]', v)); }
        const domE = $('#edit_recurrence_day_of_month').val();
        if (domE) formData.append('recurrence_day_of_month', domE);
        const monthsE = [];
        for (let i=1;i<=12;i++){ if ($('#edit_m_'+i).is(':checked')) monthsE.push(i); }
        if (monthsE.length) { monthsE.forEach(v => formData.append('recurrence_months[]', v)); }
        const endDateE = $('#edit_recurrence_end_date').val();
        if (endDateE) formData.append('recurrence_end_date', endDateE);
    }
    
    $.ajax({
        url: `<?php echo e(route("subscriptions.update", ":id")); ?>`.replace(':id', subscriptionId),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success || response.message) {
                showToast('Subscription updated successfully!', 'success');
                $('#editSubscriptionModal').modal('hide');
                loadSubscriptions();
            }
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
            showToast('Error updating subscription. Please try again.', 'error');
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
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success || response.message) {
                showToast('Subscription deleted successfully!', 'success');
                loadSubscriptions();
            }
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
            showToast('Error deleting subscription. Please try again.', 'error');
        }
    });
});

// Filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredSubscriptions(page = 1) {
    $.ajax({
        url: '<?php echo e(route("subscriptions.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            status: $('#status').val(),
            products_id: $('#product_type').val(),
            is_recurring: $('#filter_is_recurring').val(),
            recurrence_type: $('#filter_recurrence_type').val(),
            is_active: $('#filter_is_active').val(),
            per_page: 10,
            customer_id: "<?php echo e($customer->id); ?>"
        },
        success: function (response) {
            renderSubscriptionsTable(response, $('#paginationfilterLinks'), ['#paginationLinks', '#paginationsearchLinks', '#paginationdateLinks']);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#subscriptions_table tbody').html('<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data.</p></td></tr>');
        }
    });
}

$(document).on('change', '#status, #product_type, #filter_is_recurring, #filter_recurrence_type, #filter_is_active', function () {
    loadFilteredSubscriptions(1);
});

// Search functionality with debouncing for better performance
let searchTimeout;
$("#search").on("keyup", function () {
    const search = $(this).val();
    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(function() {
        $.ajax({
            url: '<?php echo e(route("subscriptions.filter")); ?>?page=1',
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                search: search,
                per_page: 10,
                customer_id: "<?php echo e($customer->id); ?>"
            },
            success: function (response) {
                renderSubscriptionsTable(response, $('#paginationsearchLinks'), ['#paginationLinks', '#paginationfilterLinks', '#paginationdateLinks']);
            },
            error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#subscriptions_table tbody').html('<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data.</p></td></tr>');
            }
        });
    }, 300); // Debounce: wait 300ms after user stops typing
});

// Pagination handlers
$(document).on('click', '#paginationLinks .page-link, #paginationfilterLinks .page-link, #paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        if ($('#paginationfilterLinks').is(':visible')) {
            loadFilteredSubscriptions(page);
        } else if ($('#paginationsearchLinks').is(':visible')) {
            // Handle search pagination
        } else {
            loadSubscriptions(page);
        }
    }
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/subscription/customer_subscriptions.blade.php ENDPATH**/ ?>