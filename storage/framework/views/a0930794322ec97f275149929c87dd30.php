

<?php $__env->startSection('title', 'Project Tracking'); ?>
<?php $__env->startSection('page_title', 'Project Tracking'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  
  /* Summary Cards */
  .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 0.5rem; margin-bottom: 1rem; }
  .summary-card { background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.4rem; box-shadow: 0px 4px 4px 0px #0000000A; transition: all 0.3s ease; width: 100%; min-height: 55px; height: 55px; display: flex; align-items: center; gap: 0.5rem; }
  .summary-card:hover { transform: translateY(-2px); box-shadow: 0px 8px 8px 0px #0000000A; }
  .summary-card-icon { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
  .icon-orange { background: linear-gradient(135deg, #f97316, #fb923c); }
  .summary-card-content { display: flex; flex-direction: column; justify-content: center; flex-grow: 1; min-width: 0; }
  .summary-card-label { font-size: 8px; font-weight: 700; text-transform: uppercase; margin-bottom: 0.15rem; color: #000; line-height: 1.1; font-family: Montserrat; }
  .summary-card-value { font-size: 0.9rem; font-weight: 700; margin: 0; line-height: 1; color: #101828; font-family: Montserrat; }

  /* Project/Customer Cards Grid */
  .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
  }
  
  .item-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
  }
  
  .item-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      border-color: #434afa;
  }
  
  .item-card .card-header-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 0.5rem;
  }
  
  .item-card .card-title {
      font-size: 1rem;
      font-weight: 600;
      color: #111827;
      margin-bottom: 0.2rem;
      font-family: Montserrat, sans-serif;
  }
  
  .item-card .card-subtitle {
      font-size: 0.75rem;
      color: #6b7280;
      font-family: Montserrat, sans-serif;
  }
  
  .item-card .card-meta {
      font-size: 0.75rem;
      color: #374151;
      margin-top: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
  }
  
  .item-card .card-footer-row {
      margin-top: 1rem;
      padding-top: 0.75rem;
      border-top: 1px solid #f3f4f6;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }
  
  .status-pill {
      font-size: 0.7rem;
      padding: 0.15rem 0.6rem;
      border-radius: 999px;
      font-weight: 600;
      text-transform: uppercase;
  }
  .status-pill.pending { background: #fef3c7; color: #92400e; }
  .status-pill.in_progress { background: #dbeafe; color: #1e40af; }
  .status-pill.completed { background: #d1fae5; color: #065f46; }
  .status-pill.cancelled { background: #f3f4f6; color: #374151; }

  /* Search Bar */
  .table-search {
    width: 100%;
    margin-bottom: 1rem;
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
  .table-search-btn:hover { background: #3538d4; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Back Button */
  .back-btn {
      background: transparent;
      border: 1px solid #e5e7eb;
      color: #374151;
      padding: 0.35rem 0.8rem;
      border-radius: 4px;
      font-size: 0.8rem;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      cursor: pointer;
      margin-right: 0.5rem;
  }
  .back-btn:hover { background: #f3f4f6; }
  
  .pagination { justify-content: center; margin-top: 1rem; }
  .pagination .page-link { color: #434afa; font-size: 0.8rem; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; }
  
  .no-data {
      text-align: center;
      padding: 2rem;
      color: #6b7280;
      font-style: italic;
      grid-column: 1 / -1;
  }

  .action-buttons {
      display: flex;
      gap: 0.3rem;
  }
  .small-btn {
      padding: 0.2rem 0.5rem;
      font-size: 0.7rem;
      border-radius: 4px;
      border: none;
      cursor: pointer;
  }
  .edit-btn { background: #eff6ff; color: #3b82f6; }
  .edit-btn:hover { background: #dbeafe; }
  .delete-btn { background: #fef2f2; color: #ef4444; }
  .delete-btn:hover { background: #fee2e2; }

  /* Task Modal Styles */
  .assign-users-grid {
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    padding: 0.5rem;
    background: #f9fafb;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.5rem;
  }
  .assign-users-grid .form-check { margin-bottom: 0; }
  .assign-users-grid .form-check-label { font-size: 0.75rem; cursor: pointer; }
  .assign-users-grid .form-check-input { width: 1rem; height: 1rem; cursor: pointer; }
  
  .form-compact .form-label { font-size: 0.8rem; font-weight: 600; margin-bottom: 0.2rem; color: #374151; }
  .form-compact .form-control, .form-compact .form-select { font-size: 0.85rem; padding: 0.4rem 0.6rem; }
  .form-compact textarea { resize: vertical; }

  /* Vertical Projects List Layout */
  /* Vertical Projects List Layout - REMOVED/UPDATED */
  #projectsGrid {
      /* Resetting to grid layout */
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Responsive grid */
      gap: 1rem;
      max-height: none; /* Remove height constraint */
      overflow-y: visible; /* Remove scroll */
      padding: 0;
      align-items: stretch;
  }
  
  #projectsGrid .item-card {
      width: 100%; /* Full width of grid cell */
      max-width: none;
      height: 100%; /* Uniform height */
      display: flex;
      flex-direction: column;
  }
  
  #projectsGrid .item-card .card-footer-row {
      margin-top: auto; /* Push footer to bottom */
  }


  /* Expanded Task Modal Styles */
  .subHeader { display: flex; justify-content: space-between; align-items: center; width: 100%; }
  .task-type-wrapper { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; }
  .task-type-title { font-weight: 600; color: #555; }
  .task-type-option { cursor: pointer; display: flex; align-items: center; gap: 5px; }
  
  .form-accent { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 0.75rem; }
  .chip-row { display: flex; align-items: center; justify-content: space-between; }
  .chip-title { font-weight: 600; color: #434afa; font-size: 0.9rem; }
  .chip-toggle { font-size: 0.8rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
  
  .file-upload-box { border: 2px dashed #ddd; border-radius: 8px; transition: all 0.3s ease; }
  .file-upload-box:hover { border-color: #434afa; background: #f0f4ff; }

  /* Data Table Styles */
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .table-responsive { border-radius: 18px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; }
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; background: transparent; font-size: 0.85rem; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.4rem 0.5rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.25rem 0.5rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Global Stats -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-blue">
        <i class="bi bi-folder-fill text-white" style="font-size: 1.1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Projects</div>
        <div class="summary-card-value"><?php echo e($totalProjects ?? 0); ?></div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-green">
        <i class="bi bi-check-circle-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Active</div>
        <div class="summary-card-value"><?php echo e($activeProjects ?? 0); ?></div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
        <i class="bi bi-patch-check-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Completed</div>
        <div class="summary-card-value"><?php echo e($completedProjects ?? 0); ?></div>
      </div>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-orange">
        <i class="bi bi-clock-fill text-white" style="font-size: 1rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Pending</div>
        <div class="summary-card-value"><?php echo e($pendingProjects ?? 0); ?></div>
      </div>
    </div>
  </div>

  <!-- Search & Actions -->
  <div class="table-search">
    <button class="back-btn" id="backBtn" style="display:none;">
        <i class="bi bi-arrow-left"></i> Back
    </button>
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search..." autocomplete="off">
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-3">
        <!-- Left Column: Navigation / Lists -->
        <div class="col-md-12" id="listColumn">
           
            <!-- Dynamic Title for Context -->
            <h6 id="viewTitle" class="mb-3 fw-bold" style="font-family: Montserrat; color: #1f2937;">All Customers</h6>

            <!-- View 1: Customers Grid (Hidden/Removed) -->
            <div id="customersView" style="display:none;">
                <div class="cards-grid" id="customersGrid"></div>
                <div class="d-flex justify-content-center">
                    <ul class="pagination" id="customerPagination"></ul>
                </div>
            </div>

            <!-- View 2: Projects Grid (Default) -->
            <div id="projectsView">
                <div class="cards-grid" id="projectsGrid">
                    <!-- Project Cards will be injected here -->
                </div>
                <div class="d-flex justify-content-center">
                    <ul class="pagination" id="projectPagination"></ul>
                </div>
            </div>
        </div>


  </div>

</div>

<!-- Add Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="border-radius:0;background-color:#434afa;">
        <h5 class="modal-title">Add Project</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="projectForm">
          <?php echo csrf_field(); ?>
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="project_name" name="project_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
              <select class="form-control" id="customer_id" name="customer_id" required>
                  <option value="">Select Customer</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="service_id" class="form-label">Service <span class="text-danger">*</span></label>
              <select class="form-control" id="service_id" name="service_id" required>
                <option value="">Select Service</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
             <div class="col-md-6 mb-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end_date" name="end_date">
            </div>
            <div class="col-md-12 mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" style="background-color:#434afa;" id="saveProjectBtn">Save Project</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Project Modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="border-radius:0;background-color:#434afa;">
        <h5 class="modal-title">Edit Project</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editProjectForm">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_project_id" name="project_id">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="edit_project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit_project_name" name="project_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_customer_id" name="customer_id" required>
                  <option value="">Select Customer</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_service_id" class="form-label">Service <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_service_id" name="service_id" required>
                <option value="">Select Service</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="edit_status" name="status" required>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="edit_start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="edit_start_date" name="start_date">
            </div>
             <div class="col-md-6 mb-3">
              <label for="edit_end_date" class="form-label">End Date</label>
              <input type="date" class="form-control" id="edit_end_date" name="end_date">
            </div>
            <div class="col-md-12 mb-3">
              <label for="edit_description" class="form-label">Description</label>
              <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" style="background-color:#434afa;" id="updateProjectBtn">Update Project</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let currentView = 'projects'; // Changed default to 'projects'
    let currentCustomerId = null;
    let currentCustomerName = '';
    let currentProjectId = null;
    let currentProjectName = '';
    let customerPage = 1;
    let projectPage = 1;
    let search = '';
    let projectTasks = [];
    
    // Initial Load
    // fetchCustomers(); // Removed customer fetch
    fetchProjects(null); // Fetch all projects directly
    fetchOptions();
    
    // Set initial title
    $('#viewTitle').text('All Projects');

    // Event Listeners
    $('#search').on('keyup', function() {
        search = $(this).val().toLowerCase();
        if(currentView === 'customers') {
            customerPage = 1;
            fetchCustomers();
        } else if (currentView === 'projects') {
            projectPage = 1;
            fetchProjects(currentCustomerId);
        } else if (currentView === 'projectTasks') {
            filterAndRenderTasks();
        }
    });
    
    $('#backBtn').click(function() {
        if (currentView === 'projectTasks') {
            $('#viewTitle').text('All Projects');
            
            // Reset Layout
            $('#listColumn').removeClass('col-md-4').addClass('col-md-12');
            $('#tasksColumn').hide();
            $('#projectTasksView').hide(); 
            
            $('#projectsView').show();
            currentView = 'projects';
            $('#search').val(''); 
            search = '';
            
            // Refresh projects to be safe
            fetchProjects(null);
            $('#backBtn').hide(); // Hide back button when on main projects list
        }
    });

    $(document).on('click', '.customer-card', function() {
        const customerId = $(this).data('id');
        const customerName = $(this).data('name');
        
        currentCustomerId = customerId;
        currentCustomerName = customerName;
        currentView = 'projects';
        
        $('#viewTitle').text(`Projects for ${customerName}`);
        $('#customersView').hide();
        $('#projectsView').show();
        $('#backBtn').show();
        $('#search').val('');
        search = '';
        
        projectPage = 1;
        fetchProjects(customerId);
    });

    // Pagination Listeners
    $(document).on('click', '#customerPagination .page-link', function(e) {
        e.preventDefault();
        customerPage = $(this).attr('href').split('page=')[1];
        fetchCustomers();
    });
    
    $(document).on('click', '#projectPagination .page-link', function(e) {
        e.preventDefault();
        projectPage = $(this).attr('href').split('page=')[1];
        fetchProjects(currentCustomerId);
    });

    // Modals & Forms
    $('#saveProjectBtn').click(function(e) {
        e.preventDefault();
        saveProject();
    });

    $('#updateProjectBtn').click(function(e) {
        e.preventDefault();
        updateProject();
    });

    $(document).on('click', '.edit-btn', function(e) {
        e.stopPropagation();
        const project = $(this).data('project');
        populateEditModal(project);
    });
    
    $(document).on('click', '.delete-btn', function(e) {
        e.stopPropagation();
        const id = $(this).data('id');
        if(confirm('Are you sure you want to delete this project?')) {
            deleteProject(id);
        }
    });

    // --- API Functions ---

    function fetchCustomers() {
        $.ajax({
            url: "<?php echo e(route('projects.fetch_customers')); ?>",
            type: "GET",
            data: { page: customerPage, search: search },
            success: function(response) {
                renderCustomerGrid(response.data);
                renderPagination(response, '#customerPagination');
            }
        });
    }

    function fetchProjects(customerId) {
        $.ajax({
            url: "<?php echo e(route('projects.fetch')); ?>",
            type: "GET",
            data: { 
                page: projectPage, 
                search: search, 
                customer_id: customerId 
            },
            success: function(response) {
                renderProjectGrid(response.data);
                renderPagination(response, '#projectPagination');
            }
        });
    }

    function fetchOptions() {
        $.ajax({
            url: "<?php echo e(route('projects.options')); ?>",
            type: "GET",
            success: function(response) {
                let customerOptions = '<option value="">Select Customer</option>';
                response.customers.forEach(c => {
                    customerOptions += `<option value="${c.id}">${c.name}</option>`;
                });
                $('#customer_id, #edit_customer_id').html(customerOptions);

                let serviceOptions = '<option value="">Select Service</option>';
                response.services.forEach(s => {
                    serviceOptions += `<option value="${s.id}">${s.name}</option>`;
                });
                $('#service_id, #edit_service_id').html(serviceOptions);
            }
        });
    }

    // --- Renders ---

    function renderCustomerGrid(data) {
        let html = '';
        if(data.length > 0) {
            data.forEach(item => {
                html += `
                    <div class="item-card customer-card" data-id="${item.id}" data-name="${item.name}">
                        <div class="card-header-row">
                            <div class="card-title text-primary">${item.name}</div>
                            <span class="badge bg-light text-dark">${item.customer_projects_count} Projects</span>
                        </div>
                        <div class="card-subtitle">${item.company_name || 'No Company'}</div>
                        <div class="card-meta">
                            <i class="bi bi-envelope"></i> ${item.email || 'N/A'}
                        </div>
                        <div class="card-footer-row text-muted small">
                            <span>Click to view projects</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="no-data">No customers found.</div>';
        }
        $('#customersGrid').html(html);
    }

    function renderProjectGrid(data) {
        let html = '';
        if(data.length > 0) {
            data.forEach(item => {
                let statusClass = 'pending';
                if(item.status === 'in_progress') statusClass = 'in_progress';
                if(item.status === 'completed') statusClass = 'completed';
                if(item.status === 'cancelled') statusClass = 'cancelled';

                html += `
                    <div class="item-card project-card" data-id="${item.id}" data-name="${item.project_name}" data-customer="${item.customer_id}">
                        <div class="card-header-row">
                            <div class="card-title">${item.project_name}</div>
                            <span class="status-pill ${statusClass}">${item.status.replace('_', ' ')}</span>
                        </div>
                        <div class="card-subtitle mb-2">${item.service ? item.service.name : 'No Service'}</div>
                        <div class="small text-muted mb-2" style="font-size:0.75rem;">
                            ${item.description ? (item.description.substring(0,60) + (item.description.length>60?'...':'')) : 'No description'}
                        </div>
                        <div class="card-meta">
                            <i class="bi bi-calendar"></i> Starts: ${item.start_date || 'N/A'}
                        </div>
                        <div class="card-footer-row">
                             <div class="text-muted small">ID: #${item.id}</div>
                             <div class="action-buttons">
                                <button class="small-btn edit-btn" data-project='${JSON.stringify(item).replace(/'/g, "&apos;")}' title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="small-btn delete-btn" data-id="${item.id}" title="Delete"><i class="bi bi-trash"></i></button>
                             </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = '<div class="no-data">No projects found for this customer.</div>';
        }
        $('#projectsGrid').html(html);
    }

    function renderPagination(response, targetId) {
        let links = '';
        if(response.links && response.last_page > 1) {
            response.links.forEach(link => {
                let activeClass = link.active ? 'active' : '';
                let disabledClass = link.url ? '' : 'disabled';
                // Clean label
                let label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                links += `<li class="page-item ${activeClass} ${disabledClass}">
                    <a class="page-link" href="${link.url}">${label}</a>
                </li>`;
            });
        } else {
            links = '';
        }
        $(targetId).html(links);
    }

    // --- CRUD ---

    function saveProject() {
        let formData = {
            project_name: $('#project_name').val(),
            customer_id: $('#customer_id').val(),
            service_id: $('#service_id').val(),
            status: $('#status').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            description: $('#description').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        $.ajax({
            url: "<?php echo e(route('projects.store')); ?>",
            type: "POST",
            data: formData,
            success: function(response) {
                $('#addProjectModal').modal('hide');
                $('#projectForm')[0].reset();
                // Refresh projects list regardless of customer
                fetchProjects(currentCustomerId);
            },
            error: function(xhr) { alert('Error processing request'); }
        });
    }
    
    function updateProject() {
        let id = $('#edit_project_id').val();
         let formData = {
            project_name: $('#edit_project_name').val(),
            customer_id: $('#edit_customer_id').val(),
            service_id: $('#edit_service_id').val(),
            status: $('#edit_status').val(),
            start_date: $('#edit_start_date').val(),
            end_date: $('#edit_end_date').val(),
            description: $('#edit_description').val(),
             _token: $('meta[name="csrf-token"]').attr('content')
        };
        $.ajax({
            url: `/projects/${id}`,
            type: "PUT",
            data: formData,
            success: function(response) {
                $('#editProjectModal').modal('hide');
                fetchProjects(currentCustomerId);
            },
            error: function(xhr) { alert('Error updating project'); }
        });
    }
    
    function deleteProject(id) {
        $.ajax({
            url: `/projects/${id}`,
            type: "DELETE",
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                fetchProjects(currentCustomerId);
                // If we were in task view of this project, go back
                if(currentView === 'projectTasks' && currentProjectId == id) {
                    $('#backBtn').click();
                }
            },
            error: function(xhr) { alert('Error deleting project'); }
        });
    }
    
    function populateEditModal(project) {
        $('#edit_project_id').val(project.id);
        $('#edit_project_name').val(project.project_name);
        $('#edit_customer_id').val(project.customer_id);
        $('#edit_service_id').val(project.service_id);
        $('#edit_status').val(project.status);
        $('#edit_start_date').val(project.start_date);
        $('#edit_end_date').val(project.end_date);
        $('#edit_description').val(project.description);
        $('#editProjectModal').modal('show');
    }

    // --- Task Assignment Logic ---
    
    let globalUsers = [];
    let globalStatuses = [];
    let globalPriorities = [];
    let taskOptionsLoaded = false;

    function loadTaskOptions() {
        if(taskOptionsLoaded) return;
        
        // Load Users
        $.get("<?php echo e(route('task.users')); ?>", function(data) {
            globalUsers = Array.isArray(data) ? data : [];
            renderUserCheckboxes();
        });

        // Load Statuses
        $.get("<?php echo e(route('task.statuses')); ?>", function(data) {
            globalStatuses = data;
            let options = '<option value="">Select Status</option>';
            data.forEach(s => {
                options += `<option value="${s.id}">${s.name}</option>`;
            });
            $('#task_status_id').html(options);
            
            // Default to Pending if found
            const pending = data.find(s => s.name.toLowerCase() === 'pending');
            if(pending) $('#task_status_id').val(pending.id);
        });

        // Load Priorities
        $.get("<?php echo e(route('task.priorities')); ?>", function(data) {
            globalPriorities = data;
            let options = '<option value="">Select Priority</option>';
            data.forEach(p => {
                options += `<option value="${p.id}">${p.name}</option>`;
            });
            $('#task_priority_id').html(options);
        });
        
        taskOptionsLoaded = true;
    }

    function renderUserCheckboxes() {
        if(!globalUsers.length) {
            $('#assignUsersGrid').html('<small class="text-danger">No users found</small>');
            return;
        }
        let html = '';
        globalUsers.forEach(user => {
             html += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="user_ids[]" value="${user.id}" id="user_${user.id}">
                    <label class="form-check-label" for="user_${user.id}">
                        ${user.name}
                    </label>
                </div>
             `;
        });
        $('#assignUsersGrid').html(html);
    }

    $(document).on('click', '.assign-task-btn', function(e) {
        e.stopPropagation();
        const projectId = $(this).data('id');
        const customerId = $(this).data('customer');
        
        // $('#task_customer_project_id').val(projectId); // Removed hidden input logic
        
        // Set Customer Select
        // We need to make sure the customer options are loaded in the task modal too.
        // We can just copy options from #customer_id
        if($('#task_customer_id option').length <= 1) {
             $('#task_customer_id').html($('#customer_id').html());
        }
        $('#task_customer_id').val(customerId);
        
        // Load projects
        loadCustomerProjects(customerId, '#task_customer_project_id', projectId);
        
        // Clear other fields
        $('#task_name').val('');
        $('#task_description').val('');
        $('#task_due_date').val('');
        $('input[name="user_ids[]"]').prop('checked', false);
        $('#type_task').prop('checked', true);
        
        // Load options if not loaded
        loadTaskOptions();
        
        $('#createTaskModal').modal('show');
    });

    // --- Click Handler for Projects ---
    $(document).on('click', '.project-card', function(e) {
        // Prevent if clicking action buttons (like Edit/Delete on the card)
        if ($(e.target).closest('button').length) return;
        
        const projectId = $(this).data('id');
        // Navigate to the dedicated project details page
        window.location.href = `/project-tracking/${projectId}`;
    });

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/projects/project-tracking.blade.php ENDPATH**/ ?>