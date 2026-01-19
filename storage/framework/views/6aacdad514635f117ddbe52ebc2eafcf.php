

<?php $__env->startSection('title', 'Customer Project Services'); ?>
<?php $__env->startSection('page_title', 'Customer Project Services'); ?>

<?php $__env->startPush('styles'); ?>
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
    color: #000;
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

  .btn-modern-danger {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-danger:hover {
    background: #3538d4;
    color: white;
  }
  
  /* Additional Styles for Customer Project specifics */
  .section-title {
      font-weight: 600;
      font-size: 1rem;
      color: #333;
      margin-bottom: 0.25rem;
  }
  .section-hint {
      font-size: 0.8rem;
      color: #6c757d;
      margin-bottom: 0;
  }
  .section-heading {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-bottom: 1rem;
  }
  .modal-section {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1rem;
      border: 1px solid #eee;
  }

  .form-control{
    background-color: #f0f0f0;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search projects..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createCustomerProjectModal">
      <i class="bi bi-plus me-1"></i>New Project
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="customerProjectTable">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Project</th>
              <th>Description</th>
              <th>Status</th>
              <th>Critical Path</th>
              <th>Start</th>
              <th>End</th>
              <th style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading projects...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="projectRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Customer Project Modal -->
<div class="modal fade modal-modern" id="createCustomerProjectModal" tabindex="-1" aria-labelledby="createCustomerProjectModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="createCustomerProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="createCustomerProjectModalLabel">
                        <i class="bi bi-plus-circle text-white"></i>
                        Open New Project Service
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <?php echo csrf_field(); ?>
                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-primary-subtle text-primary"><i class="bi bi-info-circle"></i></span>
                            <div>
                                <h6 class="section-title">Project Basics</h6>
                                <p class="section-hint">Who is this for and what are we delivering?</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label-modern">Customer <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="service_id" class="form-label-modern">Service <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="project_name" class="form-label-modern">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-modern" id="project_name" name="project_name" placeholder="e.g. CRM rollout for ACME" required>
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label-modern">Description</label>
                                <textarea class="form-control form-control-modern" id="description" name="description" rows="3" placeholder="Add context, goals, or critical milestones"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-success-subtle text-success"><i class="bi bi-calendar-range"></i></span>
                            <div>
                                <h6 class="section-title">Schedule & Tracking</h6>
                                <p class="section-hint">Outline key dates and whether you need a critical path.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label-modern">Start Date</label>
                                <input type="date" class="form-control form-control-modern" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label-modern">End Date</label>
                                <input type="date" class="form-control form-control-modern" id="end_date" name="end_date">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="critical_path_enabled" name="critical_path_enabled" value="1">
                                    <label class="form-check-label" for="critical_path_enabled">Enable Critical Path</label>
                                </div>
                            </div>
                            <div class="col-md-12 d-none" id="critical_path_template_wrapper">
                                <label for="critical_path_template_id" class="form-label-modern">Workflow Template</label>
                                <select class="form-select form-select-modern" id="critical_path_template_id" name="workflow_template_id">
                                    <option value="">Select Template</option>
                                </select>
                                <small class="text-muted d-block mt-1">Pick the workflow template that will define the critical path for this project.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-warning-subtle text-warning"><i class="bi bi-cash-coin"></i></span>
                            <div>
                                <h6 class="section-title">Financial Overview</h6>
                                <p class="section-hint">Keep tabs on estimates and profitability.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="original_value" class="form-label-modern">Original Value</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="original_value" name="original_value" placeholder="0.00">
                            </div>
                            <div class="col-md-4">
                                <label for="estimated_value" class="form-label-modern">Estimated Value</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="estimated_value" name="estimated_value" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="profit_value" class="form-label-modern">Profit / Loss</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="profit_value" name="profit_value" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary"><i class="bi bi-people"></i></span>
                            <div>
                                <h6 class="section-title">Team & Modules</h6>
                                <p class="section-hint">Assign owners and choose the modules involved.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label-modern">Assign Users</label>
                                <div id="assign_users_container" class="assign-users-scroll border p-2 rounded bg-white" style="max-height: 200px; overflow-y: auto;"></div>
                                <small class="text-muted d-block mt-1">Activate a user and add days to factor into the cost estimate.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">Select Modules <span class="text-danger">*</span></label>
                                <div id="modules_container" class="modules-scroll border p-2 rounded bg-white" style="max-height: 200px; overflow-y: auto;">
                                    <p class="text-muted mb-0">Select a service first to load available modules.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern btn-modern-danger w-100 justify-content-center" style="background: #434AFA; color: white;">
                        <i class="bi bi-check-circle"></i>
                        Open Project Service
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Project Modal -->
<div class="modal fade modal-modern" id="editCustomerProjectModal" tabindex="-1" aria-labelledby="editCustomerProjectModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editCustomerProjectForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="editCustomerProjectModalLabel">
                        <i class="bi bi-pencil-square text-white"></i>
                        Edit Customer Project
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit_customer_project_id">
                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-primary-subtle text-primary"><i class="bi bi-info-circle"></i></span>
                            <div>
                                <h6 class="section-title">Project Basics</h6>
                                <p class="section-hint">Adjust who this project is for and what it covers.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="edit_customer_id" class="form-label-modern">Customer <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="edit_customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_service_id" class="form-label-modern">Service <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="edit_service_id" name="service_id" required>
                                    <option value="">Select Service</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_project_name" class="form-label-modern">Project Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-modern" id="edit_project_name" name="project_name" required>
                            </div>
                            <div class="col-md-12">
                                <label for="edit_description" class="form-label-modern">Description</label>
                                <textarea class="form-control form-control-modern" id="edit_description" name="description" rows="3" placeholder="Update goals, scope, or key notes"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-success-subtle text-success"><i class="bi bi-calendar-range"></i></span>
                            <div>
                                <h6 class="section-title">Schedule & Tracking</h6>
                                <p class="section-hint">Keep timelines accurate and signal if critical path is required.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label-modern">Start Date</label>
                                <input type="date" class="form-control form-control-modern" id="edit_start_date" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label-modern">End Date</label>
                                <input type="date" class="form-control form-control-modern" id="edit_end_date" name="end_date">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-modern" id="edit_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="edit_critical_path_enabled" name="critical_path_enabled" value="1">
                                    <label class="form-check-label" for="edit_critical_path_enabled">Enable Critical Path</label>
                                </div>
                            </div>
                            <div class="col-md-12 d-none" id="edit_critical_path_template_wrapper">
                                <label for="edit_critical_path_template_id" class="form-label-modern">Workflow Template</label>
                                <select class="form-select form-select-modern" id="edit_critical_path_template_id" name="workflow_template_id">
                                    <option value="">Select Template</option>
                                </select>
                                <small class="text-muted d-block mt-1">Pick the workflow template that should remain tied to this project.</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-warning-subtle text-warning"><i class="bi bi-cash-coin"></i></span>
                            <div>
                                 <h6 class="section-title">Financial Overview</h6>
                                 <p class="section-hint">Adjust values as the project evolves.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="edit_original_value" class="form-label-modern">Original Value</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="edit_original_value" name="original_value">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_estimated_value" class="form-label-modern">Estimated Value</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="edit_estimated_value" name="estimated_value" readonly>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_profit_value" class="form-label-modern">Profit / Loss</label>
                                <input type="number" step="0.01" class="form-control form-control-modern" id="edit_profit_value" name="profit_value" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="modal-section">
                        <div class="section-heading">
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary"><i class="bi bi-people"></i></span>
                            <div>
                                <h6 class="section-title">Team Assignment</h6>
                                <p class="section-hint">Fine-tune who’s involved and their allocation.</p>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label-modern">Assign Users</label>
                                <div id="edit_assign_users_container" class="assign-users-scroll border p-2 rounded bg-white" style="max-height: 200px; overflow-y: auto;"></div>
                                <small class="text-muted d-block mt-1">Activate a user and update their booked days to refresh the estimate.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn-modern btn-modern-danger w-100 justify-content-center" style="background: #434AFA; color: white;">
                        <i class="bi bi-check-circle"></i>
                        Update Project
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Description Modal -->
<div class="modal fade modal-modern" id="customerProjectDescriptionModal" tabindex="-1" aria-labelledby="customerProjectDescriptionModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="customerProjectDescriptionModalLabel">
                    <i class="bi bi-file-text text-white"></i>
                    Project Description
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4 pb-4" id="customerProjectDescriptionBody">
                —
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modal-modern" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="deleteProjectModalLabel">
          <i class="bi bi-exclamation-triangle text-white"></i>
          Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-4 pb-4">
        <p class="mb-0 text-center fs-6">Are you sure you want to delete this project?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn-modern btn-modern-danger" id="confirmDelete" style="background: #dc3545; color: white;">
          <i class="bi bi-trash"></i>
          Delete
        </button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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

function escapeHtml(value) {
    return $('<div/>').text(value ?? '').html();
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
    const $info = $('#projectRangeInfo');
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
    let workflowTemplates = [];
    let workflowTemplatesLoaded = false;
    let searchTimeout;
    const descriptionModalEl = document.getElementById('customerProjectDescriptionModal');
    const descriptionModal = descriptionModalEl ? new bootstrap.Modal(descriptionModalEl) : null;

    loadCustomerProjects();
    loadCustomers();
    loadServices();
    loadWorkflowTemplates();
    loadUsers();

    function loadCustomerProjects(page = 1) {
        let search = $('#search').val();

        $('#customerProjectTable tbody').html(`
          <tr>
            <td colspan="8" class="loading-state">
              <i class="bi bi-arrow-repeat spin"></i>
              <p class="mt-2 mb-0">Loading projects...</p>
            </td>
          </tr>
        `);
        
        $.get(`<?php echo e(route('customer-project.fetch')); ?>?page=${page}&search=${search}`, function (data) {
            if (!data.data || data.data.length === 0) {
                $('#customerProjectTable tbody').html(`
                  <tr>
                    <td colspan="8" class="empty-state">
                      <i class="bi bi-inbox"></i>
                      <h5>No Projects Found</h5>
                      <p>Get started by creating your first project.</p>
                    </td>
                  </tr>
                `);
                $('#paginationLinks').empty();
                updateRangeInfo(0, 0, 0);
                return;
            }
            
            let rows = '';
            $.each(data.data, function (i, cp) {
                let statusClass = '';
                switch(cp.status) {
                    case 'completed': statusClass = 'badge bg-success'; break;
                    case 'in_progress': statusClass = 'badge bg-warning'; break;
                    case 'cancelled': statusClass = 'badge bg-danger'; break;
                    default: statusClass = 'badge bg-secondary';
                }

                const projectName = cp.project_name || (cp.project ? cp.project.name : '') || '';
                const projectNameAttr = encodeURIComponent(projectName);
                const projectCell = projectName
                    ? `<span class="d-inline-block text-truncate" style="max-width: 160px;">${escapeHtml(projectName)}</span>`
                    : '<span class="text-muted">—</span>';

                const rawDescription = cp.description ? cp.description.trim() : '';
                const descriptionPreview = rawDescription.length > 60
                    ? `${escapeHtml(rawDescription.substring(0, 60))}&hellip;`
                    : escapeHtml(rawDescription);
                const descriptionCell = rawDescription
                    ? `<span class="d-inline-block text-truncate" style="max-width: 200px;">${descriptionPreview}</span>${rawDescription.length > 60 ? ` <button type="button" class="btn btn-link btn-sm p-0 align-baseline customer-project-view-description" data-description="${encodeURIComponent(rawDescription)}">View</button>` : ''}`
                    : '<span class="text-muted">—</span>';

                const startDateValue = cp.start_date ? cp.start_date.substring(0, 10) : '';
                const endDateValue = cp.end_date ? cp.end_date.substring(0, 10) : '';

                const templateName = cp.workflow_template ? cp.workflow_template.name || '' : '';
                const criticalPathDisplay = cp.critical_path_enabled
                    ? `<span class="badge bg-info text-dark">Yes${templateName ? ' - ' + escapeHtml(templateName) : ''}</span>`
                    : '<span class="text-muted">No</span>';
                const templateNameAttr = encodeURIComponent(templateName);

                rows += `<tr style="animation-delay: ${i * 0.1}s;">
                    <td class="text-start"><strong>${escapeHtml(cp.customer.name || '')}</strong></td>
                    <td class="text-start">${projectCell}</td>
                    <td class="text-start">${descriptionCell}</td>
                    <td><span class="${statusClass}">${cp.status.replace('_', ' ')}</span></td>
                    <td>${criticalPathDisplay}</td>
                    <td>${startDateValue || '-'}</td>
                    <td>${endDateValue || '-'}</td>
                    <td>
                      <div class="d-flex gap-2 justify-content-center">
                        <button class="btn-action btn-action-edit editBtn" data-id="${cp.id}" 
                                data-customer-id="${cp.customer_id}" data-service-id="${cp.service_id}"
                                data-project-name="${projectNameAttr}"
                                data-start-date="${startDateValue}" data-end-date="${endDateValue}"
                                data-status="${cp.status}" data-description="${encodeURIComponent(rawDescription)}"
                                data-original-value="${cp.original_value || ''}" data-estimated-value="${cp.estimated_value || ''}"
                                data-profit-value="${cp.profit_value || ''}" data-assigned-users='${JSON.stringify(cp.assigned_users || [])}'
                                data-critical-path="${cp.critical_path_enabled ? 1 : 0}" data-template-id="${cp.workflow_template_id || ''}"
                                data-template-name="${templateNameAttr}" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn-action btn-action-delete deleteBtn" data-id="${cp.id}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                </tr>`;
            });
            $('#customerProjectTable tbody').html(rows);
            
            // Simple pagination
            buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
            updateRangeInfo(data.from, data.to, data.total);

        }).fail(function() {
            $('#customerProjectTable tbody').html(`
              <tr>
                <td colspan="8" class="text-danger text-center py-4">
                  <i class="bi bi-exclamation-triangle"></i>
                  Failed to load projects. Please try again.
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
        loadCustomerProjects(page);
      }
    });

    // Search input
    $('#search').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadCustomerProjects(1);
        }, 300);
    });

    function loadCustomers() {
        $.get("<?php echo e(route('customer-project.customers')); ?>", function (data) {
            let options = '<option value="">Select Customer</option>';
            $.each(data, function (i, customer) {
                options += `<option value="${customer.id}">${customer.name}</option>`;
            });
            $('#customer_id, #edit_customer_id').html(options);
        });
    }

    function loadServices() {
        $.get("<?php echo e(route('customer-project.services')); ?>")
            .done(function (data) {
                let options = '<option value="">Select Service</option>';
                if (data && data.length > 0) {
                    $.each(data, function (i, service) {
                        options += `<option value="${service.id}">${service.name}</option>`;
                    });
                } else {
                    options += '<option value="" disabled>No services available</option>';
                }
                $('#service_id, #edit_service_id').html(options);
            })
            .fail(function (xhr, status, error) {
                console.error('Error loading services:', error, xhr.responseText);
                $('#service_id, #edit_service_id').html('<option value="">Error loading services</option>');
        });
    }

    function populateWorkflowTemplateOptions() {
        const options = ['<option value="">Select Template</option>'];
        if (workflowTemplates.length > 0) {
            workflowTemplates.forEach(function(template){
                options.push(`<option value="${template.id}">${escapeHtml(template.name)}</option>`);
            });
        } else {
            options.push('<option value="" disabled>No templates available</option>');
        }
        $('#critical_path_template_id').html(options.join(''));
        $('#edit_critical_path_template_id').html(options.join(''));
    }

    function loadWorkflowTemplates() {
        $.get("<?php echo e(route('workflow-templates.fetch')); ?>")
            .done(function(response){
                if (response.success) {
                    workflowTemplates = response.data || [];
                } else {
                    workflowTemplates = [];
                }
                workflowTemplatesLoaded = true;
                populateWorkflowTemplateOptions();
            })
            .fail(function(){
                workflowTemplates = [];
                workflowTemplatesLoaded = true;
                populateWorkflowTemplateOptions();
            });
    }

    function toggleCriticalPathSelect(isEdit = false) {
        if (isEdit) {
            const enabled = $('#edit_critical_path_enabled').is(':checked');
            $('#edit_critical_path_template_wrapper').toggleClass('d-none', !enabled);
            if (!enabled) {
                $('#edit_critical_path_template_id').val('');
            }
        } else {
            const enabled = $('#critical_path_enabled').is(':checked');
            $('#critical_path_template_wrapper').toggleClass('d-none', !enabled);
            if (!enabled) {
                $('#critical_path_template_id').val('');
            }
        }
    }

    $('#critical_path_enabled').on('change', function(){
        if (!workflowTemplatesLoaded) {
            loadWorkflowTemplates();
        }
        toggleCriticalPathSelect(false);
    });

    $('#edit_critical_path_enabled').on('change', function(){
        if (!workflowTemplatesLoaded) {
            loadWorkflowTemplates();
        }
        toggleCriticalPathSelect(true);
    });

    $(document).on('click', '.customer-project-view-description', function(){
        if (!descriptionModal) return;
        const description = decodeURIComponent($(this).data('description') || '');
        $('#customerProjectDescriptionBody').text(description || 'No description available.');
        descriptionModal.show();
    });

    // Load users for assignment
    function loadUsers() {
        console.log('Fetching users from:', "<?php echo e(route('fetchUsersForManager')); ?>");
        $.get("<?php echo e(route('fetchUsersForManager')); ?>", function (users) {
            console.log('Users fetched:', users);
            if (!users || users.length === 0) {
                 console.warn('No users returned from API');
                 $('#assign_users_container').html('<p class="text-danger">No users found.</p>');
                 return;
            }
            
            let html = '';
            let editHtml = '';
            users.forEach(u => {
                const salary = u.salary_per_month || 0;
                html += `<div class="d-flex align-items-center mb-2">
                    <div class="form-check me-2 small">
                        <input class="form-check-input assign-user" type="checkbox" name="assigned_user_ids[${u.id}][id]" value="${u.id}" data-salary="${salary}" id="assign_user_${u.id}">
                        <label class="form-check-label" for="assign_user_${u.id}">${u.name}</label>
                    </div>
                    <input type="number" name="assigned_user_ids[${u.id}][days]" min="0" class="form-control form-control-sm ms-2 days-input" data-user="${u.id}" placeholder="Days" style="width:100px" disabled>
                </div>`;
                
                editHtml += `<div class="d-flex align-items-center mb-2">
                    <div class="form-check me-2 small">
                        <input class="form-check-input edit-assign-user" type="checkbox" name="assigned_user_ids[${u.id}][id]" value="${u.id}" data-salary="${salary}" id="edit_assign_user_${u.id}">
                        <label class="form-check-label" for="edit_assign_user_${u.id}">${u.name}</label>
                    </div>
                    <input type="number" name="assigned_user_ids[${u.id}][days]" min="0" class="form-control form-control-sm ms-2 edit-days-input" data-user="${u.id}" placeholder="Days" style="width:100px" disabled>
                </div>`;
            });
            $('#assign_users_container').html(html);
            $('#edit_assign_users_container').html(editHtml);

            // Toggle days input on check for create modal
            $(document).on('change', '.assign-user', function(){
                const userId = $(this).val();
                const input = $(`.days-input[data-user='${userId}']`);
                input.prop('disabled', !this.checked);
                if(!this.checked) input.val('');
                recalcEstimates();
            });
            $(document).on('input', '.days-input', recalcEstimates);
            
            // Toggle days input on check for edit modal
            $(document).on('change', '.edit-assign-user', function(){
                const userId = $(this).val();
                const input = $(`.edit-days-input[data-user='${userId}']`);
                input.prop('disabled', !this.checked);
                if(!this.checked) input.val('');
                recalcEditEstimates();
            });
            $(document).on('input', '.edit-days-input', recalcEditEstimates);
        }).fail(function(xhr, status, error) {
            console.error('Error fetching users:', status, error);
            console.error('Response:', xhr.responseText);
            $('#assign_users_container').html('<p class="text-danger">Error loading users. Check console.</p>');
        });
    }
    
    function recalcEstimates() {
        let total = 0;
        $('.assign-user:checked').each(function(){
            const monthly = parseFloat($(this).data('salary')) || 0;
            const perDay = monthly / 30.0;
            const userId = $(this).val();
            const userDays = parseFloat($(`.days-input[data-user='${userId}']`).val()) || 0;
            total += perDay * userDays;
        });
        $('#estimated_value').val(total.toFixed(2));
        const original = parseFloat($('#original_value').val()) || 0;
        $('#profit_value').val((original - total).toFixed(2));
    }

    function recalcEditEstimates() {
        let total = 0;
        $('.edit-assign-user:checked').each(function(){
            const monthly = parseFloat($(this).data('salary')) || 0;
            const perDay = monthly / 30.0;
            const userId = $(this).val();
            const userDays = parseFloat($(`.edit-days-input[data-user='${userId}']`).val()) || 0;
            total += perDay * userDays;
        });
        $('#edit_estimated_value').val(total.toFixed(2));
        const original = parseFloat($('#edit_original_value').val()) || 0;
        $('#edit_profit_value').val((original - total).toFixed(2));
    }
    
    $('#original_value').on('change keyup', recalcEstimates);
    $('#edit_original_value').on('change keyup', recalcEditEstimates);

    // Load modules when project is selected
    $('#service_id').change(function() {
        let serviceId = $(this).val();
        if (serviceId) {
            $.get(`/module/service/${serviceId}`)
                .done(function (data) {
                    let modulesHtml = '';
                    if (data && data.length > 0) {
                        modulesHtml += `<div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="select_all_modules">
                            <label class="form-check-label fw-semibold small text-uppercase text-muted" for="select_all_modules">
                                Select All Modules
                            </label>
                        </div>
                        <div class="border-top border-light mb-2"></div>`;
                        $.each(data, function (i, module) {
                            modulesHtml += `<div class="form-check">
                                <input class="form-check-input module-checkbox" type="checkbox" name="module_ids[]" value="${module.id}" id="module_${module.id}">
                                <label class="form-check-label" for="module_${module.id}">
                                    ${module.name}
                                </label>
                            </div>`;
                        });
                    } else {
                        modulesHtml = '<p class="text-muted">No modules available for this service</p>';
                    }
                    $('#modules_container').html(modulesHtml);
                })
                .fail(function (xhr, status, error) {
                    console.error('Error loading modules:', error, xhr.responseText);
                    $('#modules_container').html('<p class="text-danger">Error loading modules. Please try again.</p>');
            });
        } else {
            $('#modules_container').html('<p class="text-muted">Select a service first to see available modules</p>');
        }
    });

    $(document).on('change', '#select_all_modules', function() {
        $('.module-checkbox').prop('checked', this.checked);
    });

    $(document).on('change', '.module-checkbox', function() {
        var allChecked = $('.module-checkbox:checked').length === $('.module-checkbox').length;
        $('#select_all_modules').prop('checked', allChecked);
    });

    $('#createCustomerProjectForm').submit(function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Creating...');
        
        let formData = new FormData(this);

        $.ajax({
            url: "<?php echo e(route('customer-project.store')); ?>",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createCustomerProjectModal').modal('hide');
                    $('#createCustomerProjectForm')[0].reset();
                    $('#modules_container').empty();
                    
                    // Reset assigned users
                    $('.assign-user').prop('checked', false);
                    $('.days-input').val('').prop('disabled', true);
                    
                    loadCustomerProjects();
                    showAlert('success', 'Project created successfully.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) { // Validation error
                    let errors = Object.values(xhr.responseJSON.errors).join("\n");
                    showAlert('error', errors);
                } else {
                    showAlert('error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error creating project.');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Open Project Service');
            }
        });
    });

    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $('#edit_customer_project_id').val(id);
        $('#edit_customer_id').val($(this).data('customer-id'));
        $('#edit_service_id').val($(this).data('service-id')); // Note: service can't usually be changed easily due to modules
        $('#edit_project_name').val(decodeURIComponent($(this).data('project-name')));
        $('#edit_start_date').val($(this).data('start-date'));
        $('#edit_end_date').val($(this).data('end-date'));
        $('#edit_status').val($(this).data('status'));
        $('#edit_description').val(decodeURIComponent($(this).data('description')));
        $('#edit_original_value').val($(this).data('original-value'));
        $('#edit_estimated_value').val($(this).data('estimated-value'));
        $('#edit_profit_value').val($(this).data('profit-value'));
        
        const criticalPathEnabled = $(this).data('critical-path') == 1;
        $('#edit_critical_path_enabled').prop('checked', criticalPathEnabled);
        
        // This triggers loadWorkflowTemplates if not loaded, and toggles visibility
        $('#edit_critical_path_enabled').trigger('change');
        
        // Wait briefly for templates to possibly load/render before setting val
        setTimeout(() => {
             $('#edit_critical_path_template_id').val($(this).data('template-id'));
        }, 100);

        // Populate assigned users
        const assignedUsers = $(this).data('assigned-users');
        $('.edit-assign-user').prop('checked', false);
        $('.edit-days-input').val('').prop('disabled', true);

        if (assignedUsers && assignedUsers.length > 0) {
            assignedUsers.forEach(u => {
                const userId = u.id; // Correct as per pivot
                $(`#edit_assign_user_${userId}`).prop('checked', true);
                const daysInput = $(`.edit-days-input[data-user='${userId}']`);
                daysInput.prop('disabled', false).val(u.pivot ? u.pivot.days_allocated : 0);
            });
        }
        
        recalcEditEstimates();

        $('#editCustomerProjectModal').modal('show');
    });

    $('#editCustomerProjectForm').submit(function (e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
        
        const id = $('#edit_customer_project_id').val();
        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // For Laravel partial update

        $.ajax({
            url: `/customer-project/${id}`,
            type: 'POST', // POST with _method=PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editCustomerProjectModal').modal('hide');
                    loadCustomerProjects();
                    showAlert('success', 'Project updated successfully.');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                     let errors = Object.values(xhr.responseJSON.errors).join("\n");
                     showAlert('error', errors);
                } else {
                     showAlert('error', xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error updating project.');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Project');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function () {
        $('#confirmDelete').data('id', $(this).data('id'));
        $('#deleteProjectModal').modal('show');
    });

    $('#confirmDelete').click(function() {
        const id = $(this).data('id');
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Deleting...');

        $.ajax({
            url: `/customer-project/${id}`,
            type: 'DELETE',
            data: { _token: '<?php echo e(csrf_token()); ?>' },
            success: function (response) {
                if (response.success) {
                    $('#deleteProjectModal').modal('hide');
                    loadCustomerProjects();
                    showAlert('success', 'Project deleted successfully.');
                }
            },
            error: function () {
                showAlert('error', 'Error deleting project.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete');
            }
        });
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/customer-project/index.blade.php ENDPATH**/ ?>