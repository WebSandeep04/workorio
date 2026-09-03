@extends('layouts.app')
@section('title', 'Attendance')
@section('page_title', 'Attendance')

@push('styles')
<style>
  .attendance-dashboard {
    padding: 0.5rem;
    font-family: 'Montserrat', sans-serif;
    background-color: #f4f7fa;
    min-height: 100vh;
  }

  .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    padding: 1rem 1.5rem;
    margin: -1rem -1.5rem 1.5rem -1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }

  .page-header h4 {
    margin: 0;
    font-weight: 700;
    color: #1a1a1a;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .metric-card {
    background: #434afa;
    border-radius: 12px;
    padding: 1.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(67, 74, 250, 0.15);
    min-height: 140px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .metric-card::after {
    content: '';
    position: absolute;
    right: 0px;
    bottom: -10px;
    width: 100px;
    height: 130px;
    background: url("/img/side-icon.png") no-repeat center center;
    background-size: contain;
    /* transform: rotate(45deg); */
  }

  .metric-card p {
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
    opacity: 0.9;
  }

  .metric-card h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1;
  }

  .metric-card span {
    font-size: 0.8rem;
    opacity: 0.8;
    margin-top: 0.5rem;
  }

  .timeline-card {
    background: #fff;
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
  }

  .timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #edf2f7;
  }

  .timeline-header h5 {
    margin: 0;
    font-weight: 700;
    color: #2d3748;
    font-size: 1.25rem;
  }

  .action-buttons {
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }

  .btn-action {
    padding: 0.6rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    transition: all 0.2s ease;
    color: white !important;
  }

  .btn-punch { background-color: #434afa; }
  .btn-punch:hover { background-color: #434afa; }
  
  .btn-field { background-color: #434afa; }
  .btn-field:hover { background-color: #434afa; }
  
  .btn-break { background-color: #434AFA; }
  .btn-break:hover { background-color: #434afa; }

  .btn-action:disabled {
    background-color: #cbd5e0;
    cursor: not-allowed;
  }

  .movements-table-container {
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
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 500;
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
    background: #f9fafb;
    box-shadow: none;
    transform: none;
  }

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 100px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(13) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(14) { min-width: 140px; }

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }

  .data-table-card .custom-table tbody td .text-danger,
  .data-table-card .custom-table tbody td .priority-high,
  .data-table-card .custom-table tbody td .highlight-high {
    color: #ef4444;
    font-weight: 600;
  }

  .custom-table,
.custom-table th,
.custom-table td {
    border: none !important;
}

.custom-table thead th {
    box-shadow: inset 2px 3px 0 #e5e7eb;
}

.custom-table tbody td {
    box-shadow: inset 0 1px 0 #f1f5f9;
}



  .custom-table {
    width: 100%;
    border-collapse: collapse;
  }

  .custom-table th {
    background: #f8fafc;
    color: #4a5568;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 1.5rem;
    text-align: left;
    border-bottom: 2px solid #edf2f7;
  }

  .custom-table td {
    padding: 1rem 1.5rem;
    font-size: 0.9rem;
    color: #2d3748;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
  }

  .badge-type {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-weight: 400;
    text-transform: uppercase;
    font-size: 0.75rem;
  }

  .badge-action {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-weight: 400;
    font-size: 0.75rem;
  }

  .badge-cycle {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-weight: 400;
    font-size: 0.75rem;
  }

  /* Custom Alert Overrides for Attendance Dashboard */
  #attendanceAlerts .custom-alert {
    width: 100%;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    position: relative;
    top: 0;
    right: 0;
  }
  
  .custom-alert-success { background-color: #198754; }
  .custom-alert-error   { background-color: #dc3545; }
  .custom-alert-info    { background-color: #434afa; }
  .custom-alert-warning { background-color: #ffc107; color: #000; }


  @media (max-width: 992px) {
    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .page-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 1rem;
      margin-left: -0.5rem;
      margin-right: -0.5rem;
    }
    .page-header .d-flex {
      width: 100%;
      justify-content: space-between;
      flex-wrap: wrap;
    }
    .stats-grid {
      grid-template-columns: 1fr;
    }
    .timeline-header {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }
    .action-buttons {
      width: 100%;
      flex-direction: column;
      gap: 0.75rem !important;
    }
    .control-actions {
      width: 100%;
    }
    .action-buttons .btn {
      width: 100% !important;
      padding: 0.75rem !important;
      font-size: 1rem !important;
    }

    #worklogValidationMessage {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 1rem;
    }
    #worklogValidationMessage .btn {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
<div class="attendance-dashboard container-fluid py-4">
  <div class="page-header mb-4">
    <h4>Attendance</h4>
    <div class="d-flex align-items-center gap-3">
       <span class="text-muted fw-semi-bold">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</span>
       <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tipsModal">
         <i class="fas fa-lightbulb me-1"></i> Tips
       </button>
    </div>
  </div>

  <div id="attendanceAlerts"></div>
  <div class="stats-grid" id="attendanceStats">
    <div class="metric-card">
      <p>Today's Hours</p>
      <h2 id="todayHours">0</h2>
      <span>Tracked today</span>
    </div>
    <div class="metric-card">
      <p>Month Hours</p>
      <h2 id="monthHours">0</h2>
      <span>Current cycle</span>
    </div>
    <div class="metric-card">
      <p>Total Days</p>
      <h2 id="totalDays">0</h2>
      <span>Attendance logged</span>
    </div>
    <div class="metric-card">
      <p>Avg Hours / Day</p>
      <h2 id="avgHours">0</h2>
      <span>Consistency</span>
    </div>
  </div>

  <div id="worklogValidationAlert" class="alert alert-warning border-0 shadow-sm rounded-3 mb-4" style="display:none;">
    <div id="worklogValidationMessage" class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <i class="fas fa-exclamation-triangle fs-4 text-warning"></i>
        <div>
          <strong class="d-block">Worklog Required</strong>
          <span id="worklogValidationText" class="small text-muted"></span>
        </div>
      </div>
      <a href="{{ route('worklog') }}" class="btn btn-warning btn-sm fw-bold">
        <i class="fas fa-clock me-1"></i> Go to Worklog
      </a>
    </div>
  </div>

  <div class="timeline-card">
    <div class="timeline-header">
      <div class="d-flex align-items-center gap-3">
        <h5>Timeline</h5>
        <!-- <span id="attendanceStatusDot" class="rounded-circle" style="width: 12px; height: 12px; background-color: #cbd5e0; display: inline-block;"></span> -->
      </div>
      
      @php
          $currentUser = auth()->check() ? auth()->user() : (session()->has('user_id') ? \App\Models\User::find(session('user_id')) : null);
          $isTrackingEnabled = $currentUser?->is_tracking || optional($currentUser?->employee)->is_tracking;
          $isFieldAttendanceEnabled = optional($currentUser?->employee)->is_field_attendance_enable;
      @endphp
      @if(!$isTrackingEnabled)
      <div class="action-buttons justify-content-around d-flex flex-wrap gap-3 align-items-center">
          <div class="form-check form-switch mb-0" style="padding-left: 2.5em;">
            <input class="form-check-input" type="checkbox" id="is_wfh_toggle" style="cursor: pointer; width: 2.5em; height: 1.25em;">
            <label class="form-check-label fw-bold ms-2" for="is_wfh_toggle" style="cursor: pointer; color: #2d3748;">Work From Home</label>
          </div>

          <!-- Office Actions -->
            <div class="control-actions d-grid gap-3">
              <button type="button" class="btn btn-success" style="background: #434AFA; border-radius:3px;" id="officePunchIn" onclick="punchIn('office')">
                <i class="fas fa-sign-in-alt me-1"></i> Punch In
              </button>
              <button type="button" class="btn btn-danger d-none" id="officePunchOut" onclick="punchOut('office')">
                <i class="fas fa-sign-out-alt me-1"></i> Punch Out
              </button>
            </div>

          @if($isFieldAttendanceEnabled)
          <!-- Field Actions -->
            <div class="control-actions d-flex">
                <button type="button" class="btn btn-info text-white" style="background: #434AFA; border-radius:3px;" id="fieldPunchIn" onclick="punchIn('field')">
                <i class="fas fa-map-marker-alt me-1"></i>Field In
                </button>
                <button type="button" class="btn btn-danger d-none" id="fieldPunchOut" onclick="punchOut('field')">
                <i class="fas fa-home me-1"></i>Field Out
                </button>
            </div>
          @endif

          <!-- Break Actions -->
           <div class="control-actions d-grid gap-3">
              <button type="button" class="btn btn-warning text-white" style="background: #434AFA; border-radius:3px;" id="breakStart" onclick="startBreak()">
                <i class="fas fa-coffee me-1"></i>Break
              </button>
              <button type="button" class="btn btn-secondary text-white d-none" id="breakEnd" onclick="endBreak()">
                <i class="fas fa-play me-1"></i> End Break
              </button>
            </div>
      </div>
      @endif
    </div>

    <div id="todayMovements" class="movements-table-container">
      <p class="text-muted text-center py-5 mb-0">No movements recorded yet.</p>
    </div>
  </div>
</div>
</div>

<!-- Tips Modal -->
<div class="modal fade" id="tipsModal" tabindex="-1" aria-labelledby="tipsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tipsModalLabel"><i class="fas fa-lightbulb me-2"></i>Smart Attendance System</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="mb-0">
                    <li>✅ <strong>Multiple Cycles:</strong> You can punch in/out multiple times for office and field work</li>
                    <li>✅ <strong>Multiple Breaks:</strong> Take multiple breaks throughout the day</li>
                    <li>✅ <strong>Office → Field:</strong> Starting field work automatically ends office work</li>
                    <li>✅ <strong>Field → Office:</strong> Starting office work automatically ends field work</li>
                    <li>✅ <strong>No Descriptions:</strong> All actions are automatic and seamless</li>
                    <li>✅ <strong>Status Badges:</strong>
                        <ul class="mt-1">
                            <li><span class="badge bg-success">Punched In/In Field/On Break</span> = Currently active</li>
                            <li><span class="badge bg-primary">Ready for New Cycle</span> = Can start new cycle</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>

<!-- Late Reason Modal (for first late office/field punch-in) -->
<div class="modal fade" id="lateReasonModal" tabindex="-1" aria-labelledby="lateReasonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: #434afa; color: #fff;">
        <h5 class="modal-title" id="lateReasonModalLabel">
          <i class="bi bi-clock-history me-2"></i>Late Punch-in Reason
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Select Reason <span class="text-danger">*</span></label>
          <select id="lateReasonSelect" class="form-select">
            <option value="">-- Select reason --</option>
          </select>
          <small class="text-muted d-block mt-1">Reasons are managed from the Late Reasons master.</small>
        </div>
        <div class="mb-3 d-none" id="lateReasonCustomWrapper">
          <label class="form-label">Custom Description <span class="text-danger">*</span></label>
          <textarea id="lateReasonCustom" class="form-control" rows="2" placeholder="Describe your reason"></textarea>
        </div>
        <div class="alert alert-danger d-none" id="lateReasonError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="lateReasonSaveBtn">
          <i class="bi bi-check-circle me-1"></i>Save Reason & Punch In
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Generic Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 0; overflow: hidden;">
      <div class="modal-header border-0" id="messageModalHeader" style="background-color: #434afa; color: #fff; border-radius: 0;">
        <h5 class="modal-title" id="messageModalLabel">Message</h5>
      </div>
      <div class="modal-body text-center py-4">
        <div id="messageModalIcon" class="mb-3" style="font-size: 3rem;"></div>
        <p id="messageModalText" class="mb-0 fs-5"></p>
        <div id="messageModalExtra" class="mt-3 text-muted small"></div>
      </div>
      <div class="modal-footer justify-content-center border-0 pb-4">
        <a href="{{ route('my-tasks.index') }}" class="btn text-white d-none" id="messageModalTaskLink" style="background-color: #434afa; border-color: #434afa; border-radius: 3px; padding: 0.5rem 1.5rem;">Go to My Tasks</a>
        <a href="{{ route('leave.index') }}" class="btn text-white d-none" id="messageModalLeaveLink" style="background-color: #198754; border-color: #198754; border-radius: 3px; padding: 0.5rem 1.5rem;">Apply for Leave</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 3px; padding: 0.5rem 1.5rem;">Close</button>
      </div>

    </div>
  </div>
</div>

<script>
let pendingLatePunchType = null;

// Load attendance status on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTodayStatus();
    loadAttendanceStats();
    checkWorklogValidation();
});

// Attach jQuery event handlers when jQuery is ready
// Use a more robust approach to ensure jQuery is loaded
(function() {
    var attempts = 0;
    var maxAttempts = 20; // Try for up to 2 seconds (20 * 100ms)
    
    function initJQueryHandlers() {
        if (typeof window.jQuery !== 'undefined' && typeof window.$ !== 'undefined') {
            // jQuery is loaded, attach event handlers
            jQuery(document).ready(function($) {
                // Backup event handler for late reason save button
                $(document).on('click', '#lateReasonSaveBtn', handleLateReasonSave);
                
                // Toggle custom textbox when "Other" reason is selected
                $(document).on('change', '#lateReasonSelect', function() {
                    const selectedId = parseInt($(this).val(), 10);
                    const selectedText = ($(this).find('option:selected').text() || '').trim().toLowerCase();

                    // Treat as "Other" if:
                    // - id is 1 (original seed), OR
                    // - text starts with "other"
                    const isOther =
                        selectedId === 6 ||
                        selectedText.startsWith('other');

                    if (isOther) {
                        $('#lateReasonCustomWrapper').removeClass('d-none');
                    } else {
                        $('#lateReasonCustomWrapper').addClass('d-none');
                        $('#lateReasonCustom').val('');
                    }
                });
            });
        } else if (attempts < maxAttempts) {
            // Retry after a short delay if jQuery isn't loaded yet
            attempts++;
            setTimeout(initJQueryHandlers, 100);
        } else {
            // jQuery failed to load after max attempts
            console.warn('jQuery not loaded after maximum attempts. Some features may not work.');
        }
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initJQueryHandlers);
    } else {
        initJQueryHandlers();
    }
})();

function punchIn(type) {
    performPunchIn(type);
}

function punchOut(type) {
    performPunchOut(type);
}

function startBreak() {
    performStartBreak();
}

function endBreak() {
    performEndBreak();
}

function performPunchIn(type) {
    // Helper to execute the AJAX call
    const executePunchIn = (lat = null, long = null) => {
        const payload = {
            movement_type: type,
            _token: '{{ csrf_token() }}',
            latitude: lat,
            longitude: long,
            work_from_home: $('#is_wfh_toggle').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: '/attendance/punch-in',
            method: 'POST',
            data: payload,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadTodayStatus();
                    loadAttendanceStats();
                
                    
                    // Show task reminder modal if user has pending tasks
                    if (response.show_task_reminder) {
                        showTaskReminderModal(response.punch_type || 'in');
                    }
                } else if (response.require_late_reason) {
                    // Show late reason modal with dropdown from master
                    pendingLatePunchType = type;
                    openLateReasonModal();
                } else {
                    showAlert('error', response.message || 'Action failed');
                }
            },
            error: function(xhr) {
                console.error('Punch in error:', xhr.responseText);
                
                // Parse error message
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                if (xhr.status === 403) {
                    if (xhr.responseJSON && xhr.responseJSON.late_allowance_exceeded) {
                        showMessageModal('Late Allowance Exceeded', errorMessage, 'warning', false, true);
                    } else {
                        showAlert('error', errorMessage);
                    }
                } else if (xhr.status === 422) {
                    // Validation errors (missing location, late reason, etc)
                    if (xhr.responseJSON && xhr.responseJSON.require_late_reason) {
                         pendingLatePunchType = type;
                         openLateReasonModal();
                    } else {
                         // Likely missing location data
                         showAlert('error', errorMessage);
                    }
                } else if (xhr.status === 500) {
                    showAlert('error', 'Server error occurred. Please check the console for details.');
                } else {
                    showAlert('error', errorMessage);
                }
            }
        });
    };

    // Attempt to get location first
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                console.log("User Location:", position.coords.latitude, position.coords.longitude);
                executePunchIn(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.warn("Location access denied or failed:", error.message);
                // Proceed without location; server will enforce if restriction is enabled
                executePunchIn(null, null);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    } else {
        // Browser doesn't support geolocation
        executePunchIn(null, null);
    }
}

function performPunchOut(type) {
    const executePunchOut = (lat = null, long = null) => {
        $.ajax({
            url: '/attendance/punch-out',
            method: 'POST',
            data: {
                movement_type: type,
                _token: '{{ csrf_token() }}',
                latitude: lat,
                longitude: long
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadTodayStatus();
                
                    
                    // Show task reminder modal if user has pending tasks
                    if (response.show_task_reminder) {
                        showTaskReminderModal(response.punch_type || 'out');
                    }
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function(xhr) {
                console.error('Punch out error:', xhr.responseText);
                if (xhr.status === 500) {
                    showAlert('error', 'Server error occurred. Please check the console for details.');
                } else if (xhr.status === 422) {
                    // Check if it's the specific Pending Tasks error
                    const msg = xhr.responseJSON ? xhr.responseJSON.message : '';
                    if (msg && msg.includes('pending task(s) that were not updated today')) {
                        showMessageModal('Action Required', msg, 'warning', true);
                    } else {
                        showAlert('error', msg || 'Validation error');
                    }
                } else {
                    showAlert('error', 'An error occurred. Please try again.');
                }
            }
        });
    };

    // Attempt to get location first
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                executePunchOut(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.warn("Location access denied or failed:", error.message);
                // Proceed without location; server will enforce if restriction is enabled
                executePunchOut(null, null);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    } else {
        // Browser doesn't support geolocation
        executePunchOut(null, null);
    }
}

function showMessageModal(title, message, type = 'info', showTaskButton = false, showLeaveButton = false) {
    const modalEl = document.getElementById('messageModal');
    const header = document.getElementById('messageModalHeader');
    const icon = document.getElementById('messageModalIcon');
    const text = document.getElementById('messageModalText');
    const taskBtn = document.getElementById('messageModalTaskLink');
    const leaveBtn = document.getElementById('messageModalLeaveLink');
    
    // Set Title
    document.getElementById('messageModalLabel').textContent = title;
    
    // Set Content
    text.textContent = message;
    
    // Icon Configuration
    if (type === 'error' || type === 'danger') {
        icon.innerHTML = '<i class="bi bi-exclamation-octagon text-danger"></i>';
    } else if (type === 'warning') {
         icon.innerHTML = '<i class="bi bi-exclamation-triangle text-warning"></i>';
    } else if (type === 'success') {
        icon.innerHTML = '<i class="bi bi-check-circle text-success"></i>';
    } else {
        icon.innerHTML = '<i class="bi bi-info-circle text-primary"></i>';
    }

    // Toggle Buttons
    taskBtn.classList.toggle('d-none', !showTaskButton);
    leaveBtn.classList.toggle('d-none', !showLeaveButton);

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function performStartBreak() {
    $.ajax({
        url: '/attendance/start-break',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
            
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Start break error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function performEndBreak() {
    $.ajax({
        url: '/attendance/end-break',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
            
            } else {
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('End break error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function loadTodayStatus() {
    $.ajax({
        url: '/attendance/today-status',
        method: 'GET',
        cache: false,
        success: function(response) {
            // Handle case when no attendance record exists
            if (response.status === 'not_started') {
                // Create default status structure for new users
                const defaultStatus = {
                    office: { punched_in: false, punched_out: false, break_started: false, break_ended: false },
                    field: { punched_in: false, punched_out: false, break_started: false, break_ended: false },
                    break: { punched_in: false, punched_out: false, break_started: false, break_ended: false }
                };
                updateStatusDisplay(defaultStatus);
            } else {
                updateStatusDisplay(response.status);
            }
            updateMovementsDisplay(response.movements);
        },
        error: function(xhr) {
            console.error('Error loading today status:', xhr.responseText);
            showAlert('error', 'Failed to load attendance status. Please refresh the page.');
        }
    });
}



function loadAttendanceStats() {
    $.ajax({
        url: '/attendance/stats',
        method: 'GET',
        cache: false,
        success: function(response) {
            document.getElementById('todayHours').textContent = formatHoursClock(response.today_hours);
            document.getElementById('monthHours').textContent = formatHoursClock(response.month_hours);
            document.getElementById('totalDays').textContent = formatInteger(response.total_days);
            document.getElementById('avgHours').textContent = formatHoursClock(response.avg_hours_per_day);
            if (lateCard) {
                lateCard.style.background = 'linear-gradient(135deg, #434afa 0%, #1e25fa 100%)';
            }
        },
        error: function(xhr) {
            console.error('Error loading attendance stats:', xhr.responseText);
            // Don't show alert for stats errors as they're not critical
        }
    });
}

function setStatusPill(element, variant, text) {
    if (!element) return;
    element.className = `status-pill status-pill-${variant}`;
    element.textContent = text;
}

function toggleButton(button, visible) {
    if (!button) return;
    button.classList.toggle('d-none', !visible);
}

function setButtonDisabled(button, disabled) {
    if (!button) return;
    button.disabled = !!disabled;
}

function updateStatusDisplay(status) {
    // Check if user is currently on break
    const isOnBreak = status.break && status.break.can_end;
    const isOfficeActive = status.office && status.office.can_end;
    const isFieldActive = status.field && status.field.can_end;
    
    // Update global status dot
    const statusDot = document.getElementById('attendanceStatusDot');
    if (statusDot) {
        if (isOnBreak) statusDot.style.backgroundColor = '#f39c12'; // Orange
        else if (isOfficeActive) statusDot.style.backgroundColor = '#28a745'; // Green
        else if (isFieldActive) statusDot.style.backgroundColor = '#c82333'; // Red
        else statusDot.style.backgroundColor = '#434afa'; // Blue (Ready)
    }

    // Office buttons
    const officePunchIn = document.getElementById('officePunchIn');
    const officePunchOut = document.getElementById('officePunchOut');
    
    if (isOnBreak) {
        toggleButton(officePunchIn, false);
        toggleButton(officePunchOut, false);
        setButtonDisabled(officePunchIn, true);
        setButtonDisabled(officePunchOut, true);
    } else if (isOfficeActive) {
        toggleButton(officePunchIn, false);
        toggleButton(officePunchOut, true);
        setButtonDisabled(officePunchIn, false);
        setButtonDisabled(officePunchOut, false);
    } else {
        toggleButton(officePunchIn, true);
        toggleButton(officePunchOut, false);
        setButtonDisabled(officePunchIn, false);
        setButtonDisabled(officePunchOut, false);
    }

    // Field buttons
    const fieldPunchIn = document.getElementById('fieldPunchIn');
    const fieldPunchOut = document.getElementById('fieldPunchOut');
    
    if (isOnBreak) {
        toggleButton(fieldPunchIn, false);
        toggleButton(fieldPunchOut, false);
        setButtonDisabled(fieldPunchIn, true);
        setButtonDisabled(fieldPunchOut, true);
    } else if (isFieldActive) {
        toggleButton(fieldPunchIn, false);
        toggleButton(fieldPunchOut, true);
        setButtonDisabled(fieldPunchIn, false);
        setButtonDisabled(fieldPunchOut, false);
    } else {
        toggleButton(fieldPunchIn, true);
        toggleButton(fieldPunchOut, false);
        setButtonDisabled(fieldPunchIn, false);
        setButtonDisabled(fieldPunchOut, false);
    }

    // Break buttons
    const breakStart = document.getElementById('breakStart');
    const breakEnd = document.getElementById('breakEnd');
    
    if (isOnBreak) {
        toggleButton(breakStart, false);
        toggleButton(breakEnd, true);
        setButtonDisabled(breakStart, false);
        setButtonDisabled(breakEnd, false);
    } else {
        toggleButton(breakStart, true);
        toggleButton(breakEnd, false);
        setButtonDisabled(breakStart, false);
        setButtonDisabled(breakEnd, false);
    }
}

function updateMovementsDisplay(movements) {
    const container = document.getElementById('todayMovements');
    
    if (!movements || Object.keys(movements).length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-5 mb-0">No movements recorded yet.</p>';
        return;
    }

    let html = `
     <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th style="width: 25%">TIME</th>
                            <th style="width: 25%">TYPE</th>
                            <th style="width: 25%">ACTION</th>
                            <th style="width: 25%">CYCLE</th>
                        </tr>
                    </thead>
                    <tbody>`;
    
    let allMovements = [];
    Object.values(movements).forEach(typeMovements => {
        typeMovements.forEach(movement => {
            allMovements.push(movement);
        });
    });
    
    // Sort by time
    allMovements.sort((a, b) => new Date(a.time) - new Date(b.time));
    
    allMovements.forEach(movement => {
        const time = new Date(movement.time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const type = movement.movement_type.toUpperCase();
        const action = movement.movement_action.charAt(0).toUpperCase() + movement.movement_action.slice(1);
        
        // Check if this is an automatic transition
        const isAutoTransition = movement.description && movement.description.includes('Auto-ended');
        const actionText = isAutoTransition 
            ? `${action} (Auto)`
            : action;
        
        // Calculate cycle number for this movement
        const cycleNumber = getCycleNumber(movements, movement);
        
        html += `
                <tr>
                    <td>${time}</td>
                    <td><span class="badge-type">${type}</span></td>
                    <td><span class="badge-action text-${getActionColor(movement.movement_action)}">${actionText}</span></td>
                    <td><span class="badge-cycle">${cycleNumber}</span></td>
                </tr>`;
    });
    
    html += '</tbody></table></div></div></div>';
    container.innerHTML = html;
}

function calculateWorkCycles(movements) {
    const cycles = { office: 0, field: 0, break: 0 };
    
    Object.keys(movements).forEach(type => {
        const typeMovements = movements[type];
        if (type === 'break') {
            // Count completed break cycles (start-end pairs)
            let startCount = 0;
            let endCount = 0;
            typeMovements.forEach(movement => {
                if (movement.movement_action === 'start') startCount++;
                if (movement.movement_action === 'end') endCount++;
            });
            cycles[type] = Math.min(startCount, endCount);
        } else {
            // Count completed punch in-out cycles
            let inCount = 0;
            let outCount = 0;
            typeMovements.forEach(movement => {
                if (movement.movement_action === 'in') inCount++;
                if (movement.movement_action === 'out') outCount++;
            });
            cycles[type] = Math.min(inCount, outCount);
        }
    });
    
    return cycles;
}

function getCycleNumber(movements, currentMovement) {
    const type = currentMovement.movement_type;
    const action = currentMovement.movement_action;
    const typeMovements = movements[type] || [];
    
    let cycleCount = 0;
    let currentCycle = 1;
    
    for (let i = 0; i < typeMovements.length; i++) {
        const movement = typeMovements[i];
        
        if (type === 'break') {
            if (movement.movement_action === 'start') {
                cycleCount++;
                currentCycle = cycleCount;
            }
        } else {
            if (movement.movement_action === 'in') {
                cycleCount++;
                currentCycle = cycleCount;
            }
        }
        
        if (movement.id === currentMovement.id) {
            break;
        }
    }
    
    return currentCycle;
}

function updateWorkCyclesSummary(cycles) {
    document.getElementById('officeCycles').textContent = cycles.office || 0;
    document.getElementById('fieldCycles').textContent = cycles.field || 0;
    document.getElementById('breakCycles').textContent = cycles.break || 0;
}

function getActionColor(action) {
    switch(action) {
        case 'in': return 'success';
        case 'out': return 'danger';
        case 'start': return 'warning';
        case 'end': return 'info';
        default: return 'secondary';
    }
}

// Formatting helpers to avoid long floating point numbers in UI
function formatHours(value) {
    if (value === undefined || value === null || value === '') return '0';
    const num = Number(value);
    if (!isFinite(num)) return String(value);
    // Show up to 2 decimals, trim trailing zeros and dot
    return num.toFixed(2).replace(/\.00$/, '').replace(/\.(\d*[1-9])0+$/, '.$1');
}

function formatInteger(value) {
    if (value === undefined || value === null || value === '') return '0';
    const num = Number(value);
    if (!isFinite(num)) return String(value);
    return Math.round(num).toString();
}

// Display decimal hours in clock style H:MM (e.g., 2.82 -> 2:49)
function formatHoursClock(value) {
    if (value === undefined || value === null || value === '') return '0:00';
    const num = Number(value);
    if (!isFinite(num)) return String(value);
    const totalMinutes = Math.round(num * 60);
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return hours + ':' + String(minutes).padStart(2, '0');
}

// Task Reminder Modal Functions
function showTaskReminderModal(punchType) {
    let modalEl = document.getElementById('taskReminderModal');
    if (!modalEl) {
        const html = `
        <div class="modal fade" id="taskReminderModal" tabindex="-1" aria-labelledby="taskReminderModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
                <h5 class="modal-title" id="taskReminderModalLabel">
                  <i class="bi bi-bell-fill me-2"></i>Task Reminder
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-center">
                <p class="fs-5 mb-3">You have pending tasks!</p>
                <p class="text-muted">Would you like to view your tasks now?</p>
              </div>
              <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success btn-lg px-5" onclick="handleTaskReminderResponse(true, '${punchType}')">
                  <i class="bi bi-check-circle me-2"></i>Yes
                </button>
                <button type="button" class="btn btn-secondary btn-lg px-5" onclick="handleTaskReminderResponse(false, '${punchType}')">
                  <i class="bi bi-x-circle me-2"></i>No
                </button>
              </div>
            </div>
          </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modalEl = document.getElementById('taskReminderModal');
    }
    
    // Store punch type in modal for later use
    modalEl.setAttribute('data-punch-type', punchType);
    
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function handleTaskReminderResponse(response, punchType) {
    // Save response to database
    $.ajax({
        url: "{{ route('attendance.task-reminder-response') }}",
        method: 'POST',
        data: {
            response: response ? 1 : 0,
            punch_type: punchType,
            _token: '{{ csrf_token() }}'
        },
        success: function(responseData) {
            // Close modal
            const modalEl = document.getElementById('taskReminderModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            }
            
            // If user clicked Yes, redirect to my-tasks page
            if (response) {
                window.location.href = "{{ route('my-tasks.index') }}";
            }
        },
        error: function(xhr) {
            console.error('Error saving response:', xhr.responseText);
            // Still close modal and redirect if yes
            const modalEl = document.getElementById('taskReminderModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            }
            
            if (response) {
                window.location.href = "{{ route('my-tasks.index') }}";
            }
        }
    });
}

function showAlert(type, message) {
    let alertType = type;
    if (type === 'error') alertType = 'error';
    if (type === 'danger') alertType = 'error';
    if (type === 'success') alertType = 'success';
    
    let colorClass = 'custom-alert-' + alertType;
    let alertHtml = `
        <div class="custom-alert ${colorClass}">
            <div class="d-flex align-items-center gap-2">
                <i class="fas ${alertType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="custom-alert-close" onclick="$(this).closest('.custom-alert').fadeOut(300, function(){ $(this).remove(); })">×</button>
        </div>
    `;
    
    $('#attendanceAlerts').html(alertHtml);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        $('#attendanceAlerts .custom-alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 4000);
}

function checkWorklogValidation() {
    $.ajax({
        url: '/attendance/check-worklog-validation',
        method: 'GET',
        cache: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (!response.can_perform_attendance) {
                // Show worklog validation alert
                document.getElementById('worklogValidationAlert').style.display = 'block';
                document.getElementById('worklogValidationText').textContent = response.message;
                
                // Disable all attendance buttons
                disableAttendanceButtons(true);
            } else {
                // Hide worklog validation alert
                document.getElementById('worklogValidationAlert').style.display = 'none';
                
                // Enable all attendance buttons
                disableAttendanceButtons(false);
            }
        },
        error: function(xhr) {
            console.error('Worklog validation check error:', xhr);
        }
    });
}

function disableAttendanceButtons(disable) {
    const buttons = [
        'officePunchIn', 'officePunchOut',
        'fieldPunchIn', 'fieldPunchOut',
        'breakStart', 'breakEnd'
    ];
    
    buttons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.disabled = disable;
        }
    });
}

// Open Late Reason modal and load reasons from master
function openLateReasonModal() {
    // Reset state
    $('#lateReasonError').addClass('d-none').text('');
    $('#lateReasonCustomWrapper').addClass('d-none');
    $('#lateReasonCustom').val('');
    
    const $select = $('#lateReasonSelect');
    $select.empty();
    $select.append('<option value="">-- Select reason --</option>');

    // Load reasons from late_reasons master
    $.get("{{ route('late-reasons.list') }}")
        .done(function(rows) {
            if (Array.isArray(rows)) {
                rows.forEach(function(r) {
                    // Only show active reasons
                    if (r.active) {
                        $select.append(
                            '<option value="' + r.id + '">' + (r.reason || '') + '</option>'
                        );
                    }
                });
            }
            const modalEl = document.getElementById('lateReasonModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        })
        .fail(function() {
            showAlert('error', 'Unable to load late reasons. Please try again.');
        });
}

// Handle save button click
function handleLateReasonSave(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('Save button clicked');
    
    const reasonId = $('#lateReasonSelect').val();
    const reasonText = $('#lateReasonSelect option:selected').text();
    const customText = $('#lateReasonCustom').val().trim();

    console.log('Reason ID:', reasonId, 'Reason Text:', reasonText, 'Custom:', customText, 'Punch Type:', pendingLatePunchType);

    if (!reasonId) {
        $('#lateReasonError').removeClass('d-none').text('Please select a reason.');
        return false;
    }

    // If "Other" (id = 1) is selected, require custom description
    // NOTE: Check if id=1 is truly 'Other' in your master data or adjust logic as needed
    if (parseInt(reasonId, 10) === 6 && !customText) {
        $('#lateReasonError').removeClass('d-none').text('Please enter a custom description for Other.');
        return false;
    }

    if (!pendingLatePunchType) {
        $('#lateReasonError').removeClass('d-none').text('Unable to detect punch type. Please close and try again.');
        return false;
    }

    $('#lateReasonError').addClass('d-none').text('');
    const $btn = $('#lateReasonSaveBtn');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    // Build final reason text
    let finalReason;
    if (parseInt(reasonId, 10) === 6) {
        finalReason = customText;
    } else {
        finalReason = reasonText;
    }

    // Helper to execute punch-in provided lat/long
    const executeLatePunchIn = (lat = null, long = null) => {
        console.log('Sending AJAX request with reason:', finalReason);
        
        $.ajax({
            url: '/attendance/punch-in',
            method: 'POST',
            data: {
                movement_type: pendingLatePunchType,
                late_reason: finalReason,
                latitude: lat,
                longitude: long,
                work_from_home: $('#is_wfh_toggle').is(':checked') ? 1 : 0,
                _token: '{{ csrf_token() }}'
            },
            success: function(resp) {
                console.log('Punch-in response:', resp);
                if (resp.success) {
                    const modalEl = document.getElementById('lateReasonModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    pendingLatePunchType = null;
                    showAlert('success', resp.message);
                    loadTodayStatus();
                    loadAttendanceStats();
                
                    if (resp.show_task_reminder) {
                        showTaskReminderModal(resp.punch_type || 'in');
                    }
                } else if (resp.require_late_reason) {
                    $('#lateReasonError').removeClass('d-none').text(resp.message || 'Reason is required.');
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Save Reason & Punch In');
                } else {
                    $('#lateReasonError').removeClass('d-none').text(resp.message || 'Unable to punch in.');
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Save Reason & Punch In');
                }
            },
            error: function(xhr) {
                console.error('Punch in error (late reason modal):', xhr.responseText);
                let errorMsg = 'An error occurred while saving late reason.';
                
                if (xhr.status === 403) {
                     if (xhr.responseJSON && xhr.responseJSON.late_allowance_exceeded) {
                        const modalEl = document.getElementById('lateReasonModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        showMessageModal('Late Allowance Exceeded', xhr.responseJSON.message, 'warning', false, true);
                        return;
                     }
                     errorMsg = xhr.responseJSON.message || errorMsg;
                } else if (xhr.status === 422) {
                     errorMsg = xhr.responseJSON.message || errorMsg;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                $('#lateReasonError').removeClass('d-none').text(errorMsg);
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Save Reason & Punch In');
            }
        });
    };

    // Attempt location retrieval before submitting
    if (navigator.geolocation) {
         navigator.geolocation.getCurrentPosition(
            (position) => {
                console.log("User Location (Late Punch):", position.coords.latitude, position.coords.longitude);
                executeLatePunchIn(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.warn("Location access denied/failed in late reason modal:", error.message);
                executeLatePunchIn(null, null);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    } else {
        executeLatePunchIn(null, null);
    }
    
    return false;
}


</script>
@endsection
