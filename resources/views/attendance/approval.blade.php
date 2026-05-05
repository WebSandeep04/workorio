@extends('layouts.app')

@section('title', 'Attendance Approval')
@section('page_title', 'Attendance Approval')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Table Header - no uppercase, specific shadow */
  .data-table-card .custom-table thead th {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    text-transform: none !important; /* REQUESTED CHANGE: Remove uppercase */
    font-size: 0.75rem !important; /* Slightly larger for readability if mixed case */
    letter-spacing: normal !important;
  }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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

  .summary-card-icon i {
    font-size: 1.25rem;
  }

  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
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

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: space-between;
    flex-wrap: wrap; /* Allow wrapping on small screens */
  }

  .table-search-field {
    flex: 1;
    min-width: 200px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
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
  
  /* Date Filter Input Style */
  .date-filter-input {
      background: #f4f5f7;
      border: 1px solid #e5e7eb;
      border-radius: 2px;
      padding: 0.35rem 0.5rem;
      font-size: 0.85rem;
      color: #111827;
      font-family: Montserrat;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }
  .date-filter-input:focus {
      outline: none;
      border-color: #434afa;
  }

  .btn-custom-primary {
    background-color: #434afa;
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    padding: 0.35rem 1rem;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
  }
  
  .btn-custom-primary:hover {
     background-color: #3538d4;
     color: white;
     box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
  }
  
  .btn-custom-primary:disabled {
      background-color: #a0a3f5;
      cursor: not-allowed;
      box-shadow: none;
  }

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

  .data-table-card .table-responsive {
    border-radius: 18px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
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
    font-weight: 700;
    padding: 0.4rem 0.5rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.75rem;
    padding: 0.3rem 0.5rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
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
  
  .spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .badge-emergency {
      background-color: #ef4444;
      color: white;
      font-size: 0.65rem;
      padding: 2px 6px;
      border-radius: 4px;
      margin-left: 5px;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">

  <!-- Summary Cards -->
  <div class="summary-cards">
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-amber">
          <i class="bi bi-person-check fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Pending Approvals</div>
          <div class="summary-card-value text-dark" id="stat_pending_count">0</div>
        </div>
      </div>
  </div>

  <!-- Actions & Search -->
  <div class="table-search mb-2">
    <!-- Date Filter -->
    <div class="d-flex align-items-center gap-2">
        <input type="date" id="filterDate" class="date-filter-input" value="{{ \Carbon\Carbon::today()->toDateString() }}" title="Filter by Date" onchange="fetchAttendance(1)">
    </div>

    <div class="table-search-field mx-2">
      <i class="bi bi-search"></i>
      <input type="text" id="searchInput" placeholder="Search employee..." />
    </div>
    
    <div class="d-flex gap-2">
      <button class="btn btn-custom-primary btn-sm" id="btnPostDaily" onclick="postDailyAttendance()">
        <i class="bi bi-send-fill me-1"></i> Post Daily Attendance
      </button>
      <button class="btn btn-outline-secondary btn-sm" onclick="resetAndRefresh()" style="font-size: 0.85rem; border-radius: 2px;" title="Reset Filters & Refresh">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="attendanceTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Employee</th>
              <th>In</th>
              <th>Out</th>
              <th>Status</th>
              <th class="text-center">Actions</th>
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

<!-- Edit Time Modal -->
<div class="modal fade" id="editTimeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="modal-header bg-primary text-white p-2">
        <h6 class="modal-title ms-2">Edit Punch Times</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editTimeForm">
        @csrf
        <input type="hidden" id="edit_attendance_id">
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label small fw-bold">Punch In Time</label>
            <input type="time" class="form-control form-control-sm" name="in_time" id="edit_in_time" required>
          </div>
          <div class="mb-3">
            <label class="small fw-bold">Punch Out Time</label>
            <input type="time" class="form-control form-control-sm" name="out_time" id="edit_out_time">
          </div>
          <div class="mb-1">
            <label class="form-label small fw-bold text-danger">Reason for Change <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm" name="reason" id="edit_reason" rows="2" required placeholder="e.g. Forgot to punch in..."></textarea>
            <small class="text-muted" style="font-size: 0.65rem;">Minimum 5 characters required.</small>
          </div>
        </div>
        <div class="modal-footer p-2 d-flex justify-content-center border-0">
          <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary px-3">Update & Log</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Quick Leave Modal -->
<div class="modal fade" id="quickLeaveModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="modal-header bg-info text-white p-2">
        <h6 class="modal-title ms-2">Convert Absence to Leave</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="quickLeaveForm">
        @csrf
        <input type="hidden" id="ql_user_id" name="user_id">
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label small fw-bold">Select Leave Type</label>
            <select class="form-select form-select-sm" name="leave_type_id" id="ql_leave_type" required onchange="toggleQuickLeaveCategory()">
                <option value="">Loading balances...</option>
            </select>
          </div>
          
          <div class="mb-3" id="ql_category_container" style="display:none;">
            <label class="form-label small fw-bold">Leave Category</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="leave_category" id="ql_cat_full" value="full" checked onchange="togglePartialDayFields()">
                    <label class="form-check-label small" for="ql_cat_full">Full Day</label>
                </div>
                <div class="form-check" id="ql_hd_option">
                    <input class="form-check-input" type="radio" name="leave_category" id="ql_cat_half" value="half" onchange="togglePartialDayFields()">
                    <label class="form-check-label small" for="ql_cat_half">Half Day</label>
                </div>
                <div class="form-check" id="ql_sl_option">
                    <input class="form-check-input" type="radio" name="leave_category" id="ql_cat_short" value="short" onchange="togglePartialDayFields()">
                    <label class="form-check-label small" for="ql_cat_short">Short Leave</label>
                </div>
            </div>
          </div>

          <div id="ql_hd_fields" style="display:none;" class="mb-3">
            <label class="form-label small fw-bold">Half Day Period</label>
            <select class="form-select form-select-sm" name="half_day_period">
                <option value="pre_lunch">Pre-Lunch</option>
                <option value="post_lunch">Post-Lunch</option>
            </select>
          </div>

          <div id="ql_sl_fields" style="display:none;" class="mb-3">
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small fw-bold">Start Time</label>
                    <input type="time" class="form-control form-control-sm" name="start_time" id="ql_sl_start">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold">End Time</label>
                    <input type="time" class="form-control form-control-sm" name="end_time" id="ql_sl_end">
                </div>
            </div>
          </div>

          <div class="mb-1">
            <label class="form-label small fw-bold">Reason/Remarks <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm" name="reason" id="ql_reason" rows="2" required placeholder="Reason for adjusting absence..."></textarea>
          </div>
        </div>
        <div class="modal-footer p-2 d-flex justify-content-center border-0">
          <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-info text-white px-3">Apply & Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="table-range-meta" id="attendanceRangeInfo" style="font-size:0.75rem; color:#6b7280; padding: 0.5rem 1rem;">Showing 0-0 of 0 entries</div>
</div>

<!-- Manual Attendance Modal -->
<div class="modal fade" id="manualAttendanceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="modal-header bg-dark text-white p-2">
        <h6 class="modal-title ms-2">Mark Manual Attendance</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="manualAttendanceForm">
        @csrf
        <input type="hidden" id="ma_user_id" name="user_id">
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label small fw-bold">Punch In Time <span class="text-danger">*</span></label>
            <input type="time" class="form-control form-control-sm" name="in_time" id="ma_in_time" required>
          </div>
          <div class="mb-3">
            <label class="small fw-bold">Punch Out Time</label>
            <input type="time" class="form-control form-control-sm" name="out_time" id="ma_out_time">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Movement Type</label>
            <select class="form-select form-select-sm" name="movement_type" id="ma_movement_type">
                <option value="office">Office</option>
                <option value="field">Field</option>
                <option value="wfh">WFH</option>
            </select>
          </div>
          <div class="alert alert-warning p-2 mb-0" style="font-size: 0.65rem;">
            <i class="bi bi-info-circle-fill me-1"></i> Marking attendance will automatically approve the record.
          </div>
        </div>
        <div class="modal-footer p-2 d-flex justify-content-center border-0">
          <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-dark px-3">Mark Present</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reject Attendance Modal -->
<div class="modal fade" id="rejectAttendanceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="modal-header bg-danger text-white p-2">
        <h6 class="modal-title ms-2">Reject Attendance</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="rejectAttendanceForm">
        @csrf
        <input type="hidden" id="reject_attendance_id">
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label small fw-bold">Reason for Rejection</label>
            <textarea class="form-control form-control-sm" name="reason" id="reject_reason_text" rows="3" required placeholder="Enter reason..."></textarea>
          </div>
        </div>
        <div class="modal-footer p-2 d-flex justify-content-center border-0">
          <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-danger px-3">Reject</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    let currentPage = 1;

    window.fetchAttendance = function(page = 1) {
        currentPage = page;
        let searchTerm = $('#searchInput').val();
        let dateFilter = $('#filterDate').val();
        
        // Disable bulk btn
        $('#checkAll').prop('checked', false);
        $('#selectedCount').text(0);
        $('#btnBulkApprove').hide(); 
        // toggleBulkButton(); // Remove this as we manually hid it above for immediate effect

        // Show loading state
        $('#attendanceTable tbody').html(`
           <tr>
               <td colspan="7" class="text-center py-4 text-muted">
                   <i class="bi bi-arrow-repeat spin" style="font-size: 1.2rem;"></i> Loading records...
               </td>
           </tr>
        `);

        $.ajax({
            url: "{{ route('attendance.approval.fetch') }}",
            type: 'GET',
            data: { 
                page: page, 
                search: searchTerm,
                date: dateFilter
            },
            success: function(response) {
                let rows = '';
                let data = response.data;
                $('#stat_pending_count').text(response.pending_count || 0);
                
                if (data.length > 0) {
                    data.forEach(function(item) {
                        let emergencyBadge = item.is_emergency ? '<span class="badge-emergency">Provisional</span>' : '';
                        let wfhBadge = item.is_wfh ? '<span class="badge bg-secondary text-white ms-1" style="font-size: 0.6rem; vertical-align: middle;">WFH</span>' : '';
                        
                        // Helper for badges
                        let getBadge = (type) => {
                            if (!type) return '';
                            let cls = (type === 'field') ? 'bg-info' : 'bg-primary';
                            return `<span class="badge ${cls} me-1" style="font-size: 0.6rem; text-transform: capitalize;">${type}</span>`;
                        };

                        let inEntry = `${getBadge(item.in_type)} ${item.in_time}`;
                        let outEntry = `${getBadge(item.out_type)} ${item.out_time}`;

                        // Determine Status Badge based on Calculated Status
                        let statusBadge = '';
                        let actions = '';
                        let checkbox = '';
                        let rowClass = '';

                        const statusColors = {
                            'present': 'bg-success',
                            'present with sl': 'bg-success',
                            'present with hd': 'bg-success',
                            'present (partial leave)': 'bg-success',
                            'halfday': 'bg-warning text-dark',
                            'absent by less hr': 'bg-danger-soft text-danger border-danger',
                            'absent': 'bg-danger-soft text-danger border-danger',
                            'weekly off': 'bg-info text-white',
                            'holiday': 'bg-info text-white',
                            'leave': 'bg-info text-white',
                            'short leave': 'bg-info text-white',
                            'restricted holiday': 'bg-info text-white',
                            'weekly off working': 'bg-success',
                            'holiday working': 'bg-success'
                        };

                        let bgColor = statusColors[item.status.toLowerCase()] || 'bg-secondary';
                        statusBadge = `<span class="badge ${bgColor}" style="font-size: 0.7rem; text-transform: capitalize;">${item.status}</span>`;
                        
                        if (item.hours > 0) {
                            statusBadge += `<br><small class="text-muted fw-bold" style="font-size:0.65rem;">⏱️ ${item.hours} hrs</small>`;
                        }

                        // Add Overlap Warning
                        if (item.leave_details) {
                            statusBadge += `<br><small class="text-danger fw-bold" style="font-size:0.6rem;">⚠️ ${item.leave_details}</small>`;
                            if (item.leave_id) {
                                statusBadge += ` <button class="btn btn-xs btn-outline-danger p-0 px-1 ms-1" style="font-size: 0.55rem;" onclick="processEarlyReturn(${item.leave_id})" title="Curtail Leave & Mark as Early Return">Early Return</button>`;
                            }
                        }

                        // Add Adjusted Info
                        if (item.is_edited) {
                            let logTitle = item.edit_history.map(h => `${h.at}: ${h.reason} (by ${h.by})`).join('\n');
                            statusBadge += `<br><span class="text-primary" style="cursor:help; font-size:0.7rem;" title="${logTitle}"><i class="bi bi-info-circle-fill"></i> Adjusted</span>`;
                        }

                        // Always allow editing times if attendance exists
                        if (item.id) {
                            actions = `
                                <button class="btn-action text-primary ms-1" title="Edit Times" onclick="editTimes(${item.id}, '${item.in_time_raw}', '${item.out_time_raw}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action text-danger ms-1" title="Void Attendance (Make Absent)" onclick="voidAttendance(${item.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            `;
                            
                            // Add a small indicator for approval status
                            if (item.is_approved == 1) {
                                actions += `<i class="bi bi-check-circle-fill text-success ms-1" title="Approved"></i>`;
                            } else if (item.is_approved == 2) {
                                actions += `<i class="bi bi-x-circle-fill text-danger ms-1" title="Rejected"></i>`;
                            }
                        } else {
                            // No attendance yet, allow manual punch
                            actions = `
                                <button class="btn-action text-dark ms-1" title="Manual Punch In" onclick="openManualAttendance(${item.user_id}, '${item.shift_in}', '${item.shift_out}')">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                            `;
                        }

                        const isAlreadyPresent = item.status.toLowerCase().includes('present');
                        const hasLeave = item.leave_details !== null && item.leave_details !== '';

                        if ((item.status.toLowerCase() === 'absent' || item.status.toLowerCase() === 'absent by less hr' || item.is_early_out) && !hasLeave && !isAlreadyPresent) {
                            let slParams = `${item.user_id}`;
                            if (item.is_early_out) {
                                slParams = `${item.user_id}, true, '${item.suggested_sl_start}', '${item.suggested_sl_end}'`;
                            }
                            statusBadge += `<br><button class="btn btn-xs btn-outline-info p-0 px-1 mt-1" style="font-size: 0.55rem;" onclick="openQuickLeave(${slParams})" title="Apply leave for this employee">Apply Leave</button>`;
                            
                            if (item.status.toLowerCase() === 'absent' || item.status.toLowerCase() === 'absent by less hr') {
                                rowClass = 'bg-light-red';
                            }
                        }

                        rows += `
                            <tr class="${rowClass}">
                                <td style="font-size: 0.7rem;">${item.date}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">${item.user_name}</span>
                                        <div>${emergencyBadge} ${wfhBadge}</div>
                                    </div>
                                </td>
                                <td>${inEntry}</td>
                                <td>${outEntry}</td>
                                <td>${statusBadge}</td>
                                <td class="text-center">${actions}</td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="7" class="text-center py-4 text-muted">No pending approvals found</td></tr>`;
                }
                
                $('#attendanceTable tbody').html(rows);
                
                // Render Pagination
                let links = '';
                if (response.links) {
                  let linkHtml = '';
                  response.links.forEach(link => {
                    if (link.url) {
                         let activeClass = link.active ? 'active' : '';
                         // Replace HTML entities for better rendering
                         let label = String(link.label).replace('&laquo;', '«').replace('&raquo;', '»');
                         linkHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchAttendance(${link.url.split('page=')[1]})">${label}</a></li>`;
                    } else {
                         let label = String(link.label).replace('&laquo;', '«').replace('&raquo;', '»');
                         linkHtml += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
                    }
                  });
                  $('#paginationLinks').html(`<ul class="pagination pagination-sm">${linkHtml}</ul>`);
                }
            },
            error: function(xhr) {
                console.error('Error fetching data', xhr);
                $('#attendanceTable tbody').html(`<tr><td colspan="7" class="text-center py-4 text-danger">Error loading data</td></tr>`);
            }
        });
    }

    // CHECKBOX LOGIC
    $('#checkAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkButton();
    });

    $(document).on('change', '.row-checkbox', function() {
        toggleBulkButton();
        // Update header checkbox
        let allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
        $('#checkAll').prop('checked', allChecked);
    });

    function toggleBulkButton() {
        let count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#btnBulkApprove').fadeIn(200);
        } else {
            $('#btnBulkApprove').fadeOut(200);
        }
    }

    // Edit Times Modal Helper
    window.editTimes = function(id, inRaw, outRaw) {
        $('#edit_attendance_id').val(id);
        $('#edit_in_time').val(inRaw);
        $('#edit_out_time').val(outRaw);
        $('#edit_reason').val('');
        $('#editTimeModal').modal('show');
    }

    // Submit Time Edit
    $('#editTimeForm').submit(function(e) {
        e.preventDefault();
        let id = $('#edit_attendance_id').val();
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();

        submitBtn.text('Updating...').prop('disabled', true);

        $.ajax({
            url: "/attendance/update-times/" + id,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                submitBtn.text(originalText).prop('disabled', false);
                if (response.success) {
                    $('#editTimeModal').modal('hide');
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                let msg = 'Error updating times';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });

    window.processEarlyReturn = function(leaveId) {
        if (!confirm('Are you sure the employee has returned to work early? This will curtail the leave and allow attendance posting.')) return;

        $.ajax({
            url: "/leave/" + leaveId + "/curtail",
            type: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                is_early_return: 1,
                resume_date: $('#filterDate').val() // The date they actually punched in
            },
            success: function(response) {
                if (response.success) {
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let msg = 'Error processing early return';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    }

    window.openQuickLeave = function(userId, isEarlyOut = false, slStart = '', slEnd = '') {
        $('#ql_user_id').val(userId);
        $('#ql_reason').val(isEarlyOut ? 'Short leave for early departure' : '');
        $('#ql_sl_start').val(slStart);
        $('#ql_sl_end').val(slEnd);
        
        $('#ql_leave_type').html('<option value="">Loading balances...</option>');
        $('#ql_category_container, #ql_hd_fields, #ql_sl_fields').hide();
        $('#ql_cat_full').prop('checked', true);
        
        if (isEarlyOut) {
            // We can't automatically select SL yet because balances haven't loaded,
            // but we can set the radio button for when the type is selected.
            $('#ql_cat_short').prop('checked', true);
        }

        $('#quickLeaveModal').modal('show');

        $.get("/attendance/leave-balances/" + userId, function(res) {
            if (res.success) {
                let options = '<option value="">-- Select Type --</option>';
                res.balances.forEach(b => {
                    let disabled = b.remaining <= 0 ? 'disabled' : '';
                    options += `<option value="${b.type_id}" ${disabled} 
                        data-is-sl="${b.is_sl ? 1 : 0}" 
                        data-allow-hd="${b.allow_hd ? 1 : 0}"
                        data-rem="${b.remaining}">
                        ${b.type_name} (${b.remaining} days left)
                    </option>`;
                });
                $('#ql_leave_type').html(options);
            } else {
                alert('Error loading balances');
            }
        });
    }

    window.toggleQuickLeaveCategory = function() {
        const selected = $('#ql_leave_type option:selected');
        const isSl = selected.data('is-sl') == 1;
        const allowHd = selected.data('allow-hd') == 1;
        
        if (selected.val()) {
            $('#ql_category_container').show();
            if (isSl) $('#ql_sl_option').show(); else $('#ql_sl_option').hide();
            if (allowHd) $('#ql_hd_option').show(); else $('#ql_hd_option').hide();
            
            // If currently selected category is hidden, reset to full
            if ((isSl === false && $('#ql_cat_short').is(':checked')) || (allowHd === false && $('#ql_cat_half').is(':checked'))) {
                $('#ql_cat_full').prop('checked', true);
            }
        } else {
            $('#ql_category_container').hide();
        }
        togglePartialDayFields();
    }

    window.togglePartialDayFields = function() {
        const cat = $('input[name="leave_category"]:checked').val();
        $('#ql_hd_fields').toggle(cat === 'half');
        $('#ql_sl_fields').toggle(cat === 'short');
        
        // Reset required state if needed (optional but good practice)
        if (cat === 'short') {
            $('#ql_sl_start, #ql_sl_end').attr('required', true);
        } else {
            $('#ql_sl_start, #ql_sl_end').removeAttr('required');
        }
    }

    $('#quickLeaveForm').submit(function(e) {
        e.preventDefault();
        const date = $('#filterDate').val();
        if (!date) return alert('Select date first.');

        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();
        submitBtn.text('Processing...').prop('disabled', true);

        $.ajax({
            url: "{{ route('attendance.apply-quick-leave') }}",
            type: 'POST',
            data: $(this).serialize() + '&date=' + date,
            success: function(response) {
                submitBtn.text(originalText).prop('disabled', false);
                if (response.success) {
                    $('#quickLeaveModal').modal('hide');
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "Error applying leave.";
                alert(msg);
            }
        });
    });

    window.openManualAttendance = function(userId, shiftIn = '', shiftOut = '') {
        $('#ma_user_id').val(userId);
        $('#ma_in_time').val(shiftIn);
        $('#ma_out_time').val(shiftOut);
        $('#manualAttendanceModal').modal('show');
    }

    window.voidAttendance = function(id) {
        if (!confirm('Are you sure you want to VOID this attendance? All movements for this day will be deleted and the status will become ABSENT.')) return;

        $.ajax({
            url: "{{ route('attendance.void') }}",
            type: 'POST',
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function(response) {
                if (response.success) {
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "Error voiding attendance.";
                alert(msg);
            }
        });
    }

    $('#manualAttendanceForm').submit(function(e) {
        e.preventDefault();
        const date = $('#filterDate').val();
        if (!date) return alert('Select date first.');

        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();
        submitBtn.text('Marking...').prop('disabled', true);

        $.ajax({
            url: "{{ route('attendance.mark-attendance') }}",
            type: 'POST',
            data: $(this).serialize() + '&date=' + date,
            success: function(response) {
                submitBtn.text(originalText).prop('disabled', false);
                if (response.success) {
                    $('#manualAttendanceModal').modal('hide');
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                submitBtn.text(originalText).prop('disabled', false);
                let msg = xhr.responseJSON ? xhr.responseJSON.message : "Error marking attendance.";
                alert(msg);
            }
        });
    });

    window.postDailyAttendance = function() {
        const date = $('#filterDate').val();
        if (!date) return alert('Please select a date.');

        if (!confirm(`Are you sure you want to Post all pending attendance for ${date}? This action cannot be undone.`)) return;

        const btn = $('#btnPostDaily');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Posting...');

        $.ajax({
            url: "{{ route('attendance.post-daily') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                date: date
            },
            success: function(response) {
                btn.prop('disabled', false).html(originalText);
                if (response.success) {
                    alert(response.message);
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalText);
                let msg = 'Error posting attendance';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    }

    window.bulkApprove = function() {
        let ids = [];
        $('.row-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        if (!confirm(`Are you sure you want to approve ${ids.length} records?`)) return;

        $.ajax({
            url: "{{ route('attendance.approve-bulk') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                ids: ids
            },
            success: function(response) {
                if (response.success) {
                    fetchAttendance(currentPage);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let msg = 'Error processing bulk approval';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    }

    // Search Debounce
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            fetchAttendance(1);
        }, 500);
    });
    
    // Date input change
    $('#filterDate').on('change', function() {
        console.log('Date Filter Changed:', $(this).val());
        fetchAttendance(1);
    });

    // Reset and Refresh
    window.resetAndRefresh = function() {
        // Set date filter to today's date
        let today = new Date().toISOString().split('T')[0];
        $('#filterDate').val(today);
        $('#searchInput').val(''); // Clear search input
        fetchAttendance(1);
    }

    // Initial Load
    fetchAttendance();
});
</script>
@endpush
