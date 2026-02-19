

<?php $__env->startSection('title', 'Projects'); ?>
<?php $__env->startSection('page_title', 'Projects'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  /* Reuse styles from Subscription or define similar ones */
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
    min-width: 800px;
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
    text-align: center;
  }
  .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
    text-align: center;
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
  .action-btn.btn-primary { background: #434afa; color: white; }
  .action-btn.btn-primary:hover { background: #5568d3; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3); color: white; }
  .action-btn.btn-danger { background: #ef4444; color: white; }
  .action-btn.btn-danger:hover { background: #dc2626; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3); }

  /* Status Badges */
  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    font-family: Montserrat, sans-serif;
  }
  .status-badge.in_progress { background: #dbeafe; color: #1e40af; }
  .status-badge.completed { background: #d1fae5; color: #065f46; }
  .status-badge.pending { background: #fef3c7; color: #92400e; }
  .status-badge.cancelled { background: #f3f4f6; color: #374151; }

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
  .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; text-decoration: none; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Pagination */
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }

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

  /* Form Styles */
  .form-control-modern { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 2px; padding: 0.35rem 0.5rem; background: rgba(255, 255, 255, 0.98); color: #000; transition: all 0.3s ease; font-size: 10px; font-family: Montserrat, sans-serif; width: 100%; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
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

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search projects..." autocomplete="off">
    </div>
    <button class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addProjectModal">
      <i class="bi bi-plus me-1"></i>Add Project
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="data_table">
          <thead id="table_head">
             <tr>
                 <th width="30%">Project Name</th>
                 <th width="20%">Customer</th>
                 <th width="20%">Service</th>
                 <th width="10%">Status</th>
                 <th width="10%">Start Date</th>
                 <th width="10%">Action</th>
             </tr>
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
    let page = 1;
    let search = '';
    
    // Load initial data
    fetchProjects();
    fetchOptions();

    // Event Listeners
    $('#search').on('keyup', function() {
        search = $(this).val();
        page = 1;
        fetchProjects();
    });

    $('#saveProjectBtn').click(function(e) {
        e.preventDefault();
        saveProject();
    });

    $('#updateProjectBtn').click(function(e) {
        e.preventDefault();
        updateProject();
    });

    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        page = $(this).attr('href').split('page=')[1];
        fetchProjects();
    });

    $(document).on('click', '.edit-btn', function() {
        const project = $(this).data('project');
        populateEditModal(project);
    });
    
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        if(confirm('Are you sure you want to delete this project?')) {
            deleteProject(id);
        }
    });

    // Functions
    function fetchProjects() {
        $.ajax({
            url: "<?php echo e(route('projects.fetch')); ?>",
            type: "GET",
            data: { page: page, search: search },
            success: function(response) {
                renderTable(response.data);
                renderPagination(response);
                $('#rangeInfo').text(`Showing ${response.from}-${response.to} from ${response.total} data`);
            }
        });
    }

    function fetchOptions() {
        $.ajax({
            url: "<?php echo e(route('projects.options')); ?>", // Create this route
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

    function renderTable(data) {
        let rows = '';
        if(data.length > 0) {
            data.forEach(item => {
                let statusClass = 'pending';
                if(item.status === 'in_progress') statusClass = 'in_progress';
                if(item.status === 'completed') statusClass = 'completed';
                if(item.status === 'cancelled') statusClass = 'cancelled';
                
                rows += `
                    <tr>
                        <td class="fw-bold">${item.project_name}</td>
                        <td>${item.customer ? item.customer.name : '-'}</td>
                        <td>${item.service ? item.service.name : '-'}</td>
                        <td><span class="status-badge ${statusClass}">${item.status.replace('_', ' ').toUpperCase()}</span></td>
                        <td>${item.start_date ? item.start_date : '-'}</td>
                        <td>
                            <button class="action-btn btn-primary edit-btn" data-project='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                            <button class="action-btn btn-danger delete-btn" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        } else {
            rows = '<tr><td colspan="6" class="text-center py-4">No projects found</td></tr>';
        }
        $('#table_body').html(rows);
    }

    function renderPagination(response) {
        let links = '';
        if(response.links) {
            response.links.forEach(link => {
                let activeClass = link.active ? 'active' : '';
                let disabledClass = link.url ? '' : 'disabled';
                links += `<li class="page-item ${activeClass} ${disabledClass}">
                    <a class="page-link" href="${link.url}">${link.label}</a>
                </li>`;
            });
        }
        $('#paginationLinks').html(links);
    }

    function saveProject() {
        // Collect data
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
            url: "<?php echo e(route('projects.store')); ?>", // Create this route
            type: "POST",
            data: formData,
            success: function(response) {
                $('#addProjectModal').modal('hide');
                $('#projectForm')[0].reset();
                fetchProjects();
                // Show success toast
            },
            error: function(xhr) {
                alert('Error processing request');
            }
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
            url: `/projects/${id}`, // Create this route
            type: "PUT",
            data: formData,
            success: function(response) {
                $('#editProjectModal').modal('hide');
                fetchProjects();
            },
            error: function(xhr) {
                alert('Error updating project');
            }
        });
    }
    
    function deleteProject(id) {
        $.ajax({
            url: `/projects/${id}`, // Create this route
            type: "DELETE",
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                fetchProjects();
            },
             error: function(xhr) {
                alert('Error deleting project');
            }
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

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/projects/index.blade.php ENDPATH**/ ?>