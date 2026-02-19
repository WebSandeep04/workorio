@extends('layouts.app')

@section('title', 'Project Details')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  
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
      text-decoration: none;
  }
  .back-btn:hover { background: #f3f4f6; color: #374151; }

  /* Data Table Styles */
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .table-responsive { border-radius: 18px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; }
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; background: transparent; font-size: 0.85rem; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.4rem 0.5rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.25rem 0.5rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }

  /* Task Modal Styles */
  .assign-users-grid {
    max-height: 180px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: #fff;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.5rem;
  }
  .assign-users-grid .form-check { margin-bottom: 6px; }
  .assign-users-grid .form-check-label { font-size: 0.75rem; cursor: pointer; }
  .assign-users-grid .form-check-input { width: 1rem; height: 1rem; cursor: pointer; }
  
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
    background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; padding: 0.75rem;
  }
  .chip-toggle {
    display:inline-flex; align-items:center; gap:8px; padding:4px 10px; 
     color:#1d4ed8; font-weight:600; font-size:12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    cursor: pointer;
  }
  .chip-toggle .form-check-input { margin-left:8px; width:36px; height:18px; }
  .chip-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
  .chip-row .title { font-weight:700; letter-spacing:.2px; color: #0f172a; font-size:0.9rem; }

    .chip-title{
        border-bottom: none !important;
        font-weight: 700;
        color: #434afa; font-size: 0.9rem;
    }

  /* File Upload */
    .file-upload-box {
      border: 3px dashed #434AFA;
      border-radius: 12px;
      background-color: #f3f4f6;
      cursor: pointer;
      transition: 0.3s;
      padding: 3px !important;
    }

    .file-upload-box:hover {
      border: 3px solid blue;
    }

    .upload-icon {
      font-size: 42px;
      color: #434AFA;
    }

  /* Task Type Wrapper */
    .task-type-wrapper {
      display: inline-flex;
      align-items: center;
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
      align-items: center;
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
        align-items: center !important;
        gap: 10px !important;
        width: auto !important;
    }

    .modal-header {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .modal-title {
        margin: 0 !important;
    }
    
    .modal-footer-custom {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
    
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('projects.index') }}" class="back-btn">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
        <h5 class="mb-0 fw-bold" style="font-family: Montserrat;">{{ $project->project_name }} <span class="text-muted fs-6">({{ $project->customer->name }})</span></h5>
    </div>

    <div class="data-table-card h-100">
        <div class="p-3 bg-white d-flex justify-content-between align-items-center border-bottom sticky-top">
            <h5 class="mb-0 text-primary fw-bold" style="font-family: Montserrat;" id="tasksTitle">Tasks</h5>
            <div class="d-flex gap-2">
                <input type="text" id="taskSearch" class="form-control form-control-sm" placeholder="Search tasks..." style="width: 200px;">
                <button class="btn btn-sm btn-primary" id="addTaskBtn"><i class="bi bi-plus"></i> Add Task</button>
            </div>
        </div>
        <div class="table-responsive" style="max-height: calc(100vh - 200px); overflow-y: auto;">
            <table class="custom-table" id="projectTasksTable">
                <thead>
                    <tr>
                        <th style="width: 15%;">Assigned To</th>
                        <th style="width: 20%;">Task</th>
                        <th style="width: 5%;">Type</th>
                        <th style="width: 8%;">Priority</th>
                        <th style="width: 8%;">Status</th>
                        <th style="width: 10%;">Due Date</th>
                        <th class="text-end" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tasks will be injected here -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header"> 
                <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
                <div class="subHeader ms-3">
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
                    </div>
                </div>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                <form id="createTaskForm" class="h-100 d-flex flex-column">
                    <div class="p-3 form-compact flex-grow-1 overflow-auto">
                        @csrf
                        <!-- Hidden fields -->
                        <input type="hidden" name="customer_id" value="{{ $project->customer_id }}">
                        <input type="hidden" name="customer_project_id" value="{{ $project->id }}">

                        <!-- Recurring (Top, colorful) -->
                        <div class="form-accent mb-2" id="recurrenceSection">
                            <div class="chip-row">
                                <div class="chip-title">Recurring</div>
                                <label class="chip-toggle">
                                    Enable
                                    <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring">
                                </label>
                            </div>
                            <div id="recurrencePanel" class="mt-2" style="display:none;">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label">Repeat</label>
                                        <select id="recurrence_type" name="recurrence_type" class="form-select form-select-sm">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label">Every</label>
                                        <input type="number" min="1" value="1" id="recurrence_interval" name="recurrence_interval" class="form-control form-control-sm" placeholder="Interval">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label">End date</label>
                                        <input type="date" id="recurrence_end_date" name="recurrence_end_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <div id="recurrence_weekly" class="mt-2" style="display:none;">
                                    <label class="form-label">On days</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="mon" id="dow_mon"><label class="form-check-label" for="dow_mon">Mon</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="tue" id="dow_tue"><label class="form-check-label" for="dow_tue">Tue</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="wed" id="dow_wed"><label class="form-check-label" for="dow_wed">Wed</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="thu" id="dow_thu"><label class="form-check-label" for="dow_thu">Thu</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="fri" id="dow_fri"><label class="form-check-label" for="dow_fri">Fri</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="sat" id="dow_sat"><label class="form-check-label" for="dow_sat">Sat</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="sun" id="dow_sun"><label class="form-check-label" for="dow_sun">Sun</label></div>
                                    </div>
                                </div>
                                <div id="recurrence_monthly" class="mt-2" style="display:none;">
                                    <label class="form-label">On day of month</label>
                                    <input type="number" id="recurrence_day_of_month" name="recurrence_day_of_month" class="form-control form-control-sm" min="1" max="31" placeholder="1-31">
                                </div>
                                <div id="recurrence_yearly" class="mt-2" style="display:none;">
                                    <label class="form-label">In months</label>
                                    <div class="row g-1">
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="1" id="m_1"><label class="form-check-label" for="m_1">Jan</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="2" id="m_2"><label class="form-check-label" for="m_2">Feb</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="3" id="m_3"><label class="form-check-label" for="m_3">Mar</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="4" id="m_4"><label class="form-check-label" for="m_4">Apr</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="5" id="m_5"><label class="form-check-label" for="m_5">May</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="6" id="m_6"><label class="form-check-label" for="m_6">Jun</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="7" id="m_7"><label class="form-check-label" for="m_7">Jul</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="8" id="m_8"><label class="form-check-label" for="m_8">Aug</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="9" id="m_9"><label class="form-check-label" for="m_9">Sep</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="10" id="m_10"><label class="form-check-label" for="m_10">Oct</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="11" id="m_11"><label class="form-check-label" for="m_11">Nov</label></div></div>
                                        <div class="col-6 col-md-3"><div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_months[]" value="12" id="m_12"><label class="form-check-label" for="m_12">Dec</label></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Client/Project Info -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" id="label_customer">Client</label>
                                    <select class="form-select form-select-sm" disabled style="background:#e9ecef; opacity:1;">
                                        <option selected>{{ $project->customer ? $project->customer->name : 'N/A' }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Project</label>
                                    <select class="form-select form-select-sm" disabled style="background:#e9ecef; opacity:1;">
                                        <option selected>{{ $project->project_name }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- User Select -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" id="label_user">Assign To</label>
                                    <div id="assignUsersGrid" class="assign-users-grid" data-input-name="user_ids[]"></div>
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
                                <input type="date" name="due_date" id="task_due_date" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class = "row">
                            <!-- Task Description -->
                            <div class="mb-3 col-12 col-md-8">
                                <label for="task" class="form-label" id="label_task_desc">Description</label>
                                <textarea name="task" id="task_description" class="form-control form-control-sm" rows="4" required placeholder="Enter..."></textarea>
                                
                                <div class="row mt-3">
                                    <!-- Task Status Select -->
                                    <div class="mb-3 col-6">
                                        <label for="task_status_id" class="form-label" id="label_task_status">Status</label>
                                        <select name="task_status_id" id="task_status_id" class="form-select form-select-sm" required>
                                            <option value="">Select Status</option>
                                        </select>
                                    </div>

                                    <!-- Task Priority Select -->
                                    <div class="mb-3 col-6">
                                        <label for="task_priority_id" class="form-label" id="label_task_priority">Priority</label>
                                        <select name="task_priority_id" id="task_priority_id" class="form-select form-select-sm">
                                            <option value="">Select Priority</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                                <!-- Image Upload with Add More -->
                            <div class="mb-3 col-12 col-md-4">
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
                    <div class="p-4 modal-footer-custom modal-footer bg-white border-top">
                        <button type="submit" class="btn btn-primary" style = "background: #434AFa;" id="createTaskSubmitBtn">
                            Submit
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Task</h5>
                 <div class="subHeader ms-3">
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
            
            <div class="modal-body p-0">
                <form id="editTaskForm" class="h-100 d-flex flex-column">
                    <div class="p-3 form-compact flex-grow-1 overflow-auto">
                        @csrf
                        <input type="hidden" id="edit_task_id" name="task_id">
                        
                        <!-- Recurring (Edit) -->
                        <div class="form-accent mb-2" id="edit_recurrenceSection">
                            <div class="chip-row">
                                <div class="chip-title">Recurring</div>
                                <label class="chip-toggle">
                                    Enable
                                    <input class="form-check-input" type="checkbox" id="edit_is_recurring" name="is_recurring">
                                </label>
                            </div>
                            <div id="edit_recurrencePanel" class="mt-2" style="display:none;">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <label class="form-label">Repeat</label>
                                        <select id="edit_recurrence_type" name="recurrence_type" class="form-select form-select-sm">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label">Every</label>
                                        <input type="number" min="1" value="1" id="edit_recurrence_interval" name="recurrence_interval" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label">End date</label>
                                        <input type="date" id="edit_recurrence_end_date" name="recurrence_end_date" class="form-control form-control-sm">
                                    </div>
                                </div>
                                <!-- Weekly/Monthly/Yearly specifics for edit (simplified for brevity, can be expanded if needed) -->
                                <div id="edit_recurrence_weekly" class="mt-2" style="display:none;">
                                    <label class="form-label">On days</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="mon" id="edit_dow_mon"><label class="form-check-label" for="edit_dow_mon">Mon</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="tue" id="edit_dow_tue"><label class="form-check-label" for="edit_dow_tue">Tue</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="wed" id="edit_dow_wed"><label class="form-check-label" for="edit_dow_wed">Wed</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="thu" id="edit_dow_thu"><label class="form-check-label" for="edit_dow_thu">Thu</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="fri" id="edit_dow_fri"><label class="form-check-label" for="edit_dow_fri">Fri</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="sat" id="edit_dow_sat"><label class="form-check-label" for="edit_dow_sat">Sat</label></div>
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="recurrence_days_of_week[]" value="sun" id="edit_dow_sun"><label class="form-check-label" for="edit_dow_sun">Sun</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Client/Project Info -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" id="label_customer">Client</label>
                                    <select class="form-select form-select-sm" disabled style="background:#e9ecef; opacity:1;">
                                        <option selected>{{ $project->customer ? $project->customer->name : 'N/A' }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Project</label>
                                    <select class="form-select form-select-sm" disabled style="background:#e9ecef; opacity:1;">
                                        <option selected>{{ $project->project_name }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- User Select -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Assign To</label>
                                <div id="editAssignUsersGrid" class="assign-users-grid" data-input-name="user_ids[]"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_task_name" class="form-label">Task Name</label>
                                <input type="text" name="task_name" id="edit_task_name" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edit_due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="edit_due_date" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="edit_task_description" class="form-label">Description</label>
                                <textarea name="task" id="edit_task_description" class="form-control form-control-sm" rows="4" required></textarea>
                                
                                <div class="row mt-3">
                                    <div class="col-6 mb-3">
                                        <label for="edit_task_status_id" class="form-label">Status</label>
                                        <select name="task_status_id" id="edit_task_status_id" class="form-select form-select-sm" required></select>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="edit_task_priority_id" class="form-label">Priority</label>
                                        <select name="task_priority_id" id="edit_task_priority_id" class="form-select form-select-sm"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <!-- Image Upload (Edit) -->
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

                                <div id="editImagePreview" class="mt-2 d-flex gap-2 flex-wrap"></div>
                                <hr>
                                <label class="form-label small">Existing Images:</label>
                                <div id="existingImagesList" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" style="background-color:#434afa;">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Task Modal -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Task Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6"><strong>Task Name:</strong> <span id="view_task_name"></span></div>
            <div class="col-md-6"><strong>Customer:</strong> <span id="view_customer"></span></div>
        </div>
        <div class="row mb-3">
             <div class="col-md-12"><strong>Project:</strong> <span id="view_project_name"></span></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><strong>Assigned To:</strong> <span id="view_assigned_to"></span></div>
            <div class="col-md-6"><strong>Created By:</strong> <span id="view_created_by"></span></div>
        </div>
        <div class="mb-3">
             <strong>Description:</strong>
             <p id="view_task_description" class="p-2 bg-light rounded mt-1"></p>
        </div>
        <div class="mb-3">
             <strong>Images:</strong>
             <div id="view_task_images" class="d-flex flex-wrap gap-2 mt-2"></div>
        </div>
      </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const projectId = {{ $project->id }};
    let projectTasks = [];
    let search = '';
    
    // Define global variables first
    let globalUsers = [];
    let taskOptionsLoaded = false;
    
    // Initial Load
    loadTaskOptions();
    fetchProjectTasks();

    // Event Listeners
    $('#taskSearch').on('keyup', function() {
        search = $(this).val().toLowerCase();
        filterAndRenderTasks();
    });

    $('#addTaskBtn').click(function() {
        $('#createTaskModal').modal('show');
    });

    function loadTaskOptions() {
        if(taskOptionsLoaded) return;
        
        // Load Users
        $.get("{{ route('task.users') }}", function(data) {
            globalUsers = Array.isArray(data) ? data : [];
            renderUserCheckboxes();
        });

        // Load Statuses
        $.get("{{ route('task.statuses') }}", function(data) {
            let options = '<option value="">Select Status</option>';
            data.forEach(s => {
                options += `<option value="${s.id}">${s.name}</option>`;
            });
            $('#task_status_id, #edit_task_status_id').html(options);
            
            // Default to Pending if found
            const pending = data.find(s => s.name.toLowerCase() === 'pending');
            if(pending) $('#task_status_id').val(pending.id);
        });

        // Load Priorities
        $.get("{{ route('task.priorities') }}", function(data) {
            let options = '<option value="">Select Priority</option>';
            data.forEach(p => {
                options += `<option value="${p.id}">${p.name}</option>`;
            });
            $('#task_priority_id, #edit_task_priority_id').html(options);
        });
        
        taskOptionsLoaded = true;
    }

    // --- Recurrence & Type Logic ---
    function setupRecurrenceLogic(prefix = '') {
        // Toggle recurrence panel
        $(`#${prefix}is_recurring`).on('change', function() {
            if($(this).is(':checked')) {
                $(`#${prefix}recurrencePanel`).slideDown();
            } else {
                $(`#${prefix}recurrencePanel`).slideUp();
            }
        });

        // Toggle sub-options based on type (weekly, monthly, yearly)
        $(`#${prefix}recurrence_type`).on('change', function() {
            const val = $(this).val();
            $(`#${prefix}recurrence_weekly`).hide();
            $(`#${prefix}recurrence_monthly`).hide();
            $(`#${prefix}recurrence_yearly`).hide();

            if(val === 'weekly') $(`#${prefix}recurrence_weekly`).show();
            else if(val === 'monthly') $(`#${prefix}recurrence_monthly`).show();
            else if(val === 'yearly') $(`#${prefix}recurrence_yearly`).show();
        });
    }
    setupRecurrenceLogic('');     // Create
    setupRecurrenceLogic('edit_'); // Edit

    // Handle Create Task Type Change
    $(document).on('change', 'input[name="task_type"]', function() {
        const type = $(this).val();
        if(type === 'qc' || type === 'cp') {
             $('#recurrenceSection').hide();
             $('#is_recurring').prop('checked', false).trigger('change');
        } else {
             $('#recurrenceSection').show();
        }
        
        // Update Labels/Button text if needed
        const label = type === 'qc' ? 'Create QC' : (type === 'cp' ? 'Create CP' : 'Create Task');
        $('#createTaskModalLabel').text(label);
        $('#createTaskSubmitBtn').text('Submit');
    });

    // --- End Recurrence Logic ---

    function renderUserCheckboxes() {
        if(!globalUsers.length) {
            $('.assign-users-grid').html('<small class="text-danger">No users found</small>');
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
        $('#assignUsersGrid, #editAssignUsersGrid').html(html);
    }

    $('#createTaskForm').on('submit', function(e) {
        e.preventDefault();
        
        if($('input[name="user_ids[]"]:checked').length === 0) {
            alert('Please select at least one user.');
            return;
        }

        let formData = new FormData(this);
        formData.append('task_type', $('input[name="task_type"]:checked').val());
        
        $.ajax({
             url: "{{ route('task.store') }}",
             type: "POST",
             data: formData,
             processData: false,
             contentType: false,
             success: function(response) {
                 $('#createTaskModal').modal('hide');
                 alert('Task assigned successfully!');
                 // Clear form
                 $('#createTaskForm')[0].reset();
                 $('#imagePreview').empty();
                 $('#recurrencePanel').hide();
                 fetchProjectTasks();
             },
             error: function(xhr) {
                 alert('Error creating task. Please try again.');
             }
        });
    });

    // --- Task Fetch & Render ---

    function fetchProjectTasks() {
        $('#projectTasksTable tbody').html('<tr><td colspan="10" class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Loading tasks...</div></td></tr>');
        
        $.ajax({
            url: `/task/project/${projectId}`,
            type: "GET",
            dataType: 'json',
            success: function(data) {
                projectTasks = data || [];
                filterAndRenderTasks();
            },
            error: function(xhr) {
                $('#projectTasksTable tbody').html('<tr><td colspan="10" class="text-center text-danger p-4">Failed to load tasks.</td></tr>');
            }
        });
    }

    function filterAndRenderTasks() {
        const term = search;
        const filtered = projectTasks.filter(task => {
            if (!term) return true;
            
            const name = task.task_name ? task.task_name.toLowerCase() : '';
            const desc = task.task ? task.task.toLowerCase() : '';
            const status = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
            const creator = (task.user && task.user.name) ? task.user.name.toLowerCase() : '';
            
            let assignees = '';
            if(task.assigned_users && task.assigned_users.length) {
                assignees = task.assigned_users.map(u => u.name ? u.name.toLowerCase() : '').join(' ');
            }
            
            return name.includes(term) || 
                   desc.includes(term) || 
                   status.includes(term) || 
                   creator.includes(term) || 
                   assignees.includes(term);
        });
        renderTasksTable(filtered);
    }
    
    function isDateOverdue(dateString) {
      if (!dateString) return false;
      const now = new Date();
      const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
      let due = new Date(dateString);
      due.setHours(0,0,0,0);
      return due < today;
    }

    function renderTasksTable(tasks) {
        let html = '';
        if(tasks.length === 0) {
            html = '<tr><td colspan="10" class="text-center p-4 text-muted">No tasks found for this project.</td></tr>';
        } else {
            tasks.forEach(task => {
                 // Check Overdue
                let isOverdue = false;
                const statusName = (task.status && task.status.name) ? task.status.name.toLowerCase() : '';
                const isTaskCompleted = task.is_done || statusName === 'done' || statusName.includes('completed') || statusName.includes('complete');
                
                if (task.due_date && !isTaskCompleted && isDateOverdue(task.due_date)) {
                    isOverdue = true;
                }

                // Status HTML
                let statusHtml = '';
                if (task.is_done) {
                    statusHtml = '<span class="fw-bold text-success">Done</span>';
                } else if (task.status) {
                    statusHtml = `<span class="fw-bold" style="color: ${task.status.color || '#6c757d'}">${task.status.name}</span>`;
                } else {
                    statusHtml = '<span class="fw-bold text-warning">Pending</span>';
                }

                // Priority HTML
                let priorityHtml = 'N/A';
                if (task.priority) {
                    priorityHtml = `<span class="fw-bold" style="color: ${task.priority.color || '#6c757d'}">${task.priority.name}</span>`;
                }

                // Assigned To
                let assignedTo = 'N/A';
                if (task.assigned_users && task.assigned_users.length > 0) {
                    assignedTo = task.assigned_users.map(u => u.name).join(', ');
                } else if (task.user) {
                    assignedTo = task.user.name;
                }

                // Type Badge
                let typeBadge = task.task_type || 'task';
                let typeColor = '#0d6efd';
                if (typeBadge === 'qc') typeColor = '#0dcaf0';
                else if (typeBadge === 'cp') typeColor = '#dc3545';

                // Due Date
                let dueDateRaw = task.due_date ? new Date(task.due_date).toLocaleDateString('en-GB') : 'N/A';
                let dueDate = isOverdue ? `<span class="text-danger fw-bold" title="Overdue">${dueDateRaw}</span>` : dueDateRaw;
                
                // Done Button
                let doneBtn = task.is_done 
                    ? `<button class="btn btn-sm btn-secondary action-btn" onclick="toggleDone(${task.id})" title="Mark as Pending"><i class="bi bi-x-circle"></i></button>`
                    : `<button class="btn btn-sm btn-success action-btn" onclick="toggleDone(${task.id})" title="Mark as Done"><i class="bi bi-check-circle"></i></button>`;

                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="d-block text-truncate" style="max-width: 100px;" title="${assignedTo}">${assignedTo}</span>
                            </div>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick="viewTaskDetails(${task.id})" class="text-dark text-decoration-none fw-bold" title="${task.task_name || ''}">
                                ${(task.task_name || 'N/A').length > 25 ? (task.task_name || 'N/A').substring(0, 25) + '...' : (task.task_name || 'N/A')}
                            </a>
                        </td>
                        <td><span class="badge" style="background-color: ${typeColor}">${typeBadge.toUpperCase()}</span></td>
                        <td>${priorityHtml}</td>
                        <td>${statusHtml}</td>
                        <td>${dueDate}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-primary action-btn mb-1" onclick="editTask(${task.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                            ${doneBtn}
                            <button class="btn btn-sm btn-warning action-btn mb-1" onclick="pokeTask(${task.id})" title="Poke"><i class="bi bi-bell"></i></button>
                            <button class="btn btn-sm btn-danger action-btn mb-1" onclick="deleteTask(${task.id})" title="Delete"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#projectTasksTable tbody').html(html);
    }
    
    // --- Actions ---
    
    window.toggleDone = function(id) {
        if(!confirm('Are you sure you want to change the status of this task?')) return;
        $.post(`/task/${id}/toggle-done`, { _token: $('meta[name="csrf-token"]').attr('content') })
         .done(function() { fetchProjectTasks(); })
         .fail(function() { alert('Error updating status'); });
    };

    window.pokeTask = function(id) {
        $.post(`/task/${id}/poke`, { _token: $('meta[name="csrf-token"]').attr('content') })
         .done(function() { alert('Poke sent successfully!'); })
         .fail(function() { alert('Error sending poke'); });
    };

    window.deleteTask = function(id) {
        if(!confirm('Are you sure you want to delete this task?')) return;
        $.ajax({
            url: `/task/${id}`,
            type: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function() { fetchProjectTasks(); },
            error: function() { alert('Error deleting task'); }
        });
    };
    
    // Edit Modal Logic (Simplified for this view)
    // Update edit modal recurrence visibility
    window.updateEditTypeState = function() {
        const type = $('input[name="edit_task_type"]:checked').val() || 'task';
        const isQC = (type === 'qc');
        const isCP = (type === 'cp');

        if (isQC || isCP) {
            $('#edit_recurrenceSection').hide();
            $('#edit_is_recurring').prop('checked', false).trigger('change');
            $('#edit_recurrencePanel').hide();
        } else {
             $('#edit_recurrenceSection').show();
        }
    };
    $(document).on('change', 'input[name="edit_task_type"]', updateEditTypeState);

    window.editTask = function(id) {
        const task = projectTasks.find(t => t.id === id);
        if(!task) return;
        
        $('#edit_task_id').val(task.id);
        $('#edit_task_name').val(task.task_name);
        $('#edit_task_description').val(task.task);
        $('#edit_due_date').val(task.due_date ? task.due_date.substring(0, 10) : '');
        
        // Type
        const type = task.task_type || 'task';
        $(`input[name="edit_task_type"][value="${type}"]`).prop('checked', true);
        updateEditTypeState();

        // Status & Priority
        $('#edit_task_status_id').val(task.task_status_id);
        $('#edit_task_priority_id').val(task.task_priority_id);
        
        // Users
        $('input[name="user_ids[]"]').prop('checked', false);
        if(task.assigned_users) {
            task.assigned_users.forEach(u => {
                $(`#editAssignUsersGrid input[value="${u.id}"]`).prop('checked', true);
            });
        }

         // Recurrence (Basic population)
        $('#edit_is_recurring').prop('checked', task.is_recurring == 1);
        if(task.is_recurring == 1) {
            $('#edit_recurrencePanel').show();
            $('#edit_recurrence_type').val(task.recurrence_type);
            $('#edit_recurrence_interval').val(task.recurrence_interval);
            $('#edit_recurrence_end_date').val(task.recurrence_end_date);
            // Weekly/Monthly specifics logic can be added here if needed
        } else {
            $('#edit_recurrencePanel').hide();
        }

        // Images
        let imgsHtml = '';
        if(task.images && task.images.length) {
            task.images.forEach(img => {
                let src = `/task/${task.id}/image/${img.id}`;
                imgsHtml += `
                    <div class="position-relative d-inline-block" id="img_container_${img.id}">
                        <a href="${src}" target="_blank">
                            <img src="${src}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        </a>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0" 
                                style="width:18px;height:18px;line-height:16px;font-size:10px; border-radius: 50%;"
                                onclick="deleteTaskImage(${task.id}, ${img.id})">&times;</button>
                    </div>
                `;
            });
        } else {
            imgsHtml = '<span class="text-muted small">No images attached.</span>';
        }
        $('#existingImagesList').html(imgsHtml);
        $('#edit_task_images').val('');
        $('#editImagePreview').empty();
        
        $('#editTaskModal').modal('show');
    };
    
    // Update Task Submit
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_task_id').val();
        let formData = new FormData(this);
        
        // Append Radio Value manually since it is outside the form
        formData.append('task_type', $('input[name="edit_task_type"]:checked').val());
        
        $.ajax({
             url: `/task/${id}/update`,
             type: "POST", 
             data: formData,
             processData: false,
             contentType: false,
             success: function(response) {
                 $('#editTaskModal').modal('hide');
                 alert('Task updated successfully!');
                 fetchProjectTasks();
             },
             error: function(xhr) {
                 console.error(xhr.responseText);
                 alert('Error updating task: ' + (xhr.responseJSON?.message || 'Unknown error'));
             }
        });
    });

    window.deleteTaskImage = function(taskId, imageId) {
        if(!confirm('Delete this image?')) return;
        $.ajax({
            url: `/task/${taskId}/image/${imageId}`,
            type: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function() {
                $(`#img_container_${imageId}`).remove();
                // Update local data
                const t = projectTasks.find(x => x.id === taskId);
                if(t && t.images) {
                    t.images = t.images.filter(i => i.id !== imageId);
                }
            },
            error: function() { alert('Failed to delete image'); }
        });
    };

    window.viewTaskDetails = function(id) {
        const task = projectTasks.find(t => t.id === id);
        if(!task) return;
        
        $('#view_task_name').text(task.task_name || 'N/A');
        $('#view_customer').text(task.customer?.name || 'N/A');
        $('#view_project_name').text(task.customer_project?.project_name || 'N/A');
        
        let assignedTo = 'N/A';
        if (task.assigned_users && task.assigned_users.length > 0) {
            assignedTo = task.assigned_users.map(u => u.name).join(', ');
        } else if (task.user) {
            assignedTo = task.user.name;
        }
        $('#view_assigned_to').text(assignedTo);
        $('#view_created_by').text(task.creator?.name || 'N/A');
        $('#view_task_description').text(task.task || 'No description.');
        
        const imagesDiv = $('#view_task_images');
        imagesDiv.empty();
        if(task.images && task.images.length > 0) {
            task.images.forEach(img => {
                let src = `/task/${task.id}/image/${img.id}`;
                imagesDiv.append(`<a href="${src}" target="_blank"><img src="${src}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;"></a>`);
            });
        }
        
        $('#viewTaskModal').modal('show');
    };

    // Recurrence Toggles
    $('#is_recurring').change(function() {
        if($(this).is(':checked')) $('#recurrencePanel').slideDown();
        else $('#recurrencePanel').slideUp();
    });
    $('#edit_is_recurring').change(function() {
        if($(this).is(':checked')) $('#edit_recurrencePanel').slideDown();
        else $('#edit_recurrencePanel').slideUp();
    });

    // File Upload Preview
    // File Upload Preview
     $(document).on('change', '#task_images, #edit_task_images', function() {
        const fileInput = this;
        const isEdit = $(this).attr('id') === 'edit_task_images';
        const previewContainer = isEdit ? $('#editImagePreview') : $('#imagePreview');
        previewContainer.empty();
        
        if (fileInput.files) {
            Array.from(fileInput.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = $('<img>').attr('src', e.target.result)
                    .css({width: '50px', height: '50px', objectFit: 'cover', borderRadius: '4px', border: '1px solid #ddd'});
                    previewContainer.append(img);
                }
                reader.readAsDataURL(file);
            });
        }
    });

});
</script>
@endpush
