@extends('layouts.app')

@push('styles')
<style>
  .attendance-dashboard {
    padding: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  .attendance-hero {
    background: linear-gradient(135deg, #5b7cfd 0%, #8f6fff 60%, #c96bff 100%);
    border-radius: 28px;
    padding: 2rem;
    color: #fff;
    margin-bottom: 1.5rem;
    box-shadow: 0 25px 60px rgba(91, 124, 253, 0.3);
  }

  .attendance-hero h1 {
    font-size: 1.95rem;
    font-weight: 700;
    margin-bottom: 0.35rem;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .metric-card {
    border-radius: 20px;
    padding: 1.2rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 35px rgba(13, 26, 56, 0.25);
    min-height: 130px;
  }

  .metric-card::after {
    content: '';
    position: absolute;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    top: -40px;
    right: -30px;
  }

  .metric-card h2 {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0.25rem 0;
  }

  .metric-card p {
    text-transform: uppercase;
    letter-spacing: 0.12em;
    font-size: 0.7rem;
    margin-bottom: 0.2rem;
  }

  .metric-card span {
    font-size: 0.85rem;
    opacity: 0.85;
  }

  .metric-card.sunrise { background: linear-gradient(135deg, #ff9a44, #ff5e62); }
  .metric-card.ocean   { background: linear-gradient(135deg, #17ead9, #6078ea); }
  .metric-card.moss    { background: linear-gradient(135deg, #43cea2, #185a9d); }
  .metric-card.amber   { background: linear-gradient(135deg, #f7971e, #ffd200); }

  .react-alert {
    border-radius: 18px;
    padding: 1rem 1.25rem;
    background: rgba(255, 183, 77, 0.18);
    border: 1px solid rgba(255, 183, 77, 0.5);
    color: #7a3a00;
    margin-bottom: 1.25rem;
  }

  .control-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .control-card {
    background: #fff;
    border-radius: 22px;
    padding: 1.25rem;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.1);
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .control-card.office { border-left: 5px solid #34d399; }
  .control-card.field  { border-left: 5px solid #38bdf8; }
  .control-card.break  { border-left: 5px solid #f97316; }

  .control-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
  }

  .control-eyebrow {
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.7rem;
    color: #94a3b8;
    margin-bottom: 0.3rem;
  }

  .status-pill {
    padding: 0.35rem 0.9rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    background: rgba(15, 23, 42, 0.06);
    color: #0f172a;
  }

  .status-pill-primary {
    background: rgba(59, 130, 246, 0.15);
    color: #1d4ed8;
  }

  .status-pill-success {
    background: rgba(16, 185, 129, 0.16);
    color: #047857;
  }

  .status-pill-warning {
    background: rgba(251, 191, 36, 0.2);
    color: #92400e;
  }

  .status-pill-info {
    background: rgba(14, 165, 233, 0.18);
    color: #075985;
  }

  .control-actions .btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 0.55rem 0.75rem;
    font-size: 0.9rem;
  }

  .movements-card {
    background: #fff;
    border-radius: 24px;
    padding: 1.5rem;
    box-shadow: 0 25px 60px rgba(15, 23, 42, 0.12);
  }

  .movements-header {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .cycle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.85rem;
    margin-bottom: 1.2rem;
  }

  .cycle-card {
    border-radius: 18px;
    padding: 1rem;
    background: #f8fafc;
    text-align: center;
    box-shadow: inset 0 0 0 1px #e2e8f0;
  }

  .cycle-card h6 {
    font-size: 0.85rem;
    color: #475569;
    margin-bottom: 0.25rem;
  }

  .cycle-card h4 {
    font-size: 1.5rem;
    color: #0f172a;
    margin: 0;
  }

  #todayMovements {
    background: #f8fafc;
    border-radius: 18px;
    padding: 1rem;
    min-height: 120px;
  }

  @media (max-width: 576px) {
    .attendance-hero { padding: 1.5rem; }
    .control-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
<div class="attendance-dashboard container-fluid px-3 px-lg-4 mt-4">
  <div class="attendance-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
      <div>
        <p class="text-uppercase mb-2" style="letter-spacing:0.2em;font-size:0.7rem;opacity:0.85;">Attendance command</p>
        <h1 class="mb-1">Attendance Management</h1>
        <p class="mb-0 opacity-75">Today: {{ \Carbon\Carbon::today()->format('l, F j, Y') }}</p>
                                </div>
      <button type="button" class="btn btn-light text-primary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#tipsModal">
        <i class="fas fa-lightbulb"></i>
        Smart Tips
      </button>
                        </div>
                    </div>

  <div id="attendanceAlerts"></div>

  <div class="stats-grid" id="attendanceStats">
    <div class="metric-card sunrise">
      <p>Today's Hours</p>
      <h2 id="todayHours">0</h2>
      <span>Tracked today</span>
                                </div>
    <div class="metric-card ocean">
      <p>Month Hours</p>
      <h2 id="monthHours">0</h2>
      <span>Current cycle</span>
                            </div>
    <div class="metric-card moss">
      <p>Total Days</p>
      <h2 id="totalDays">0</h2>
      <span>Attendance logged</span>
                        </div>
    <div class="metric-card amber">
      <p>Avg Hours / Day</p>
      <h2 id="avgHours">0</h2>
      <span>Consistency</span>
                                </div>
                            </div>

  <div id="worklogValidationAlert" class="react-alert" style="display:none;">
    <div id="worklogValidationMessage" class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
      <div>
        <strong class="d-block mb-1">⚠️ Worklog Required</strong>
        <span id="worklogValidationText"></span>
                        </div>
      <a href="{{ route('worklog') }}" class="btn btn-primary btn-sm rounded-pill px-3">
        <i class="fas fa-clock me-1"></i> Go to Worklog
      </a>
                        </div>
                    </div>

  <div class="control-grid mb-4">
    <div class="control-card office">
      <div class="control-header">
        <div>
          <p class="control-eyebrow">Office</p>
          <h5 class="mb-0">Office Attendance</h5>
                                </div>
        <span id="officeStatus" class="status-pill">Not Started</span>
                                    </div>
      <div class="control-actions d-grid gap-2">
        <button type="button" class="btn btn-success" id="officePunchIn" onclick="punchIn('office')">
          <i class="fas fa-sign-in-alt me-1"></i> Punch In (Start Cycle)
                                        </button>
        <button type="button" class="btn btn-danger d-none" id="officePunchOut" onclick="punchOut('office')">
          <i class="fas fa-sign-out-alt me-1"></i> Punch Out
                                        </button>
                            </div>
                        </div>

    <div class="control-card field">
      <div class="control-header">
        <div>
          <p class="control-eyebrow">Field</p>
          <h5 class="mb-0">Field <br> Work</h5>
                                </div>
        <span id="fieldStatus" class="status-pill">Not Started</span>
                                    </div>
      <div class="control-actions d-grid gap-2">
        <button type="button" class="btn btn-info text-white" id="fieldPunchIn" onclick="punchIn('field')">
          <i class="fas fa-map-marker-alt me-1"></i> Start Field Cycle
                                        </button>
        <button type="button" class="btn btn-outline-primary d-none" id="fieldPunchOut" onclick="punchOut('field')">
          <i class="fas fa-home me-1"></i> End Field Work
                                        </button>
                            </div>
                        </div>

    <div class="control-card break">
      <div class="control-header">
        <div>
          <p class="control-eyebrow">Break</p>
          <h5 class="mb-0">Break Management</h5>
                                </div>
        <span id="breakStatus" class="status-pill">Not Started</span>
                                    </div>
      <div class="control-actions d-grid gap-2">
        <button type="button" class="btn btn-warning text-dark" id="breakStart" onclick="startBreak()">
          <i class="fas fa-coffee me-1"></i> Start Break
                                        </button>
        <button type="button" class="btn btn-secondary text-white d-none" id="breakEnd" onclick="endBreak()">
          <i class="fas fa-play me-1"></i> End Break
                                        </button>
                            </div>
                        </div>
                    </div>

  <div class="movements-card">
    <div class="movements-header">
      <div>
        <p class="control-eyebrow mb-1">Timeline</p>
        <h5 class="mb-0">Today's Movements</h5>
      </div>
                                    <small class="text-muted">
        <i class="fas fa-sync-alt me-1"></i> Auto-updates every action
                                    </small>
                                </div>

    <div class="cycle-grid" id="workCyclesSummary">
      <div class="cycle-card">
                                                    <h6>Office Cycles</h6>
                                                    <h4 id="officeCycles">0</h4>
                                                </div>
      <div class="cycle-card">
                                                    <h6>Field Cycles</h6>
                                                    <h4 id="fieldCycles">0</h4>
                                                </div>
      <div class="cycle-card">
                                                    <h6>Break Cycles</h6>
                                                    <h4 id="breakCycles">0</h4>
                                        </div>
                                    </div>
                                    
                                    <div id="todayMovements">
      <p class="text-muted mb-0">No movements recorded yet.</p>
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
    const payload = {
        movement_type: type,
        _token: '{{ csrf_token() }}'
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
                showAlert('error', response.message);
            }
        },
        error: function(xhr) {
            console.error('Punch in error:', xhr.responseText);
            if (xhr.status === 500) {
                showAlert('error', 'Server error occurred. Please check the console for details.');
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.require_late_reason) {
                // Validation-based late reason requirement - show modal with dropdown
                pendingLatePunchType = type;
                openLateReasonModal();
            } else {
                showAlert('error', 'An error occurred. Please try again.');
            }
        }
    });
}

function performPunchOut(type) {
    $.ajax({
        url: '/attendance/punch-out',
        method: 'POST',
        data: {
            movement_type: type,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadTodayStatus();
                loadAttendanceStats();
                
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
}

function showMessageModal(title, message, type = 'info', showTaskButton = false) {
    const modalEl = document.getElementById('messageModal');
    const header = document.getElementById('messageModalHeader');
    const icon = document.getElementById('messageModalIcon');
    const text = document.getElementById('messageModalText');
    const taskBtn = document.getElementById('messageModalTaskLink');
    
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

    // Toggle Task Button
    if (showTaskButton) {
        taskBtn.classList.remove('d-none');
    } else {
        taskBtn.classList.add('d-none');
    }

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
                loadAttendanceStats();
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
                loadAttendanceStats();
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
    
    // Office status
    const officeStatus = document.getElementById('officeStatus');
    const officePunchIn = document.getElementById('officePunchIn');
    const officePunchOut = document.getElementById('officePunchOut');
    
    if (isOnBreak) {
        // If on break, disable office actions and show break message
        setStatusPill(officeStatus, 'warning', 'On Break - Actions Disabled');
        toggleButton(officePunchIn, false);
        toggleButton(officePunchOut, false);
        setButtonDisabled(officePunchIn, true);
        setButtonDisabled(officePunchOut, true);
    } else if (status.office && status.office.can_end) {
        setStatusPill(officeStatus, 'success', 'Punched In');
        toggleButton(officePunchIn, false);
        toggleButton(officePunchOut, true);
        setButtonDisabled(officePunchIn, false);
        setButtonDisabled(officePunchOut, false);
    } else {
        setStatusPill(officeStatus, 'primary', 'Ready for New Cycle');
        toggleButton(officePunchIn, true);
        toggleButton(officePunchOut, false);
        setButtonDisabled(officePunchIn, false);
        setButtonDisabled(officePunchOut, false);
    }

    // Field status
    const fieldStatus = document.getElementById('fieldStatus');
    const fieldPunchIn = document.getElementById('fieldPunchIn');
    const fieldPunchOut = document.getElementById('fieldPunchOut');
    
    if (isOnBreak) {
        // If on break, disable field actions and show break message
        setStatusPill(fieldStatus, 'warning', 'On Break - Actions Disabled');
        toggleButton(fieldPunchIn, false);
        toggleButton(fieldPunchOut, false);
        setButtonDisabled(fieldPunchIn, true);
        setButtonDisabled(fieldPunchOut, true);
    } else if (status.field && status.field.can_end) {
        setStatusPill(fieldStatus, 'info', 'In Field');
        toggleButton(fieldPunchIn, false);
        toggleButton(fieldPunchOut, true);
        setButtonDisabled(fieldPunchIn, false);
        setButtonDisabled(fieldPunchOut, false);
    } else {
        setStatusPill(fieldStatus, 'primary', 'Ready for New Cycle');
        toggleButton(fieldPunchIn, true);
        toggleButton(fieldPunchOut, false);
        setButtonDisabled(fieldPunchIn, false);
        setButtonDisabled(fieldPunchOut, false);
    }

    // Break status
    const breakStatus = document.getElementById('breakStatus');
    const breakStart = document.getElementById('breakStart');
    const breakEnd = document.getElementById('breakEnd');
    
    if (status.break && status.break.can_end) {
        setStatusPill(breakStatus, 'warning', 'On Break');
        toggleButton(breakStart, false);
        toggleButton(breakEnd, true);
        setButtonDisabled(breakStart, false);
        setButtonDisabled(breakEnd, false);
    } else {
        setStatusPill(breakStatus, 'primary', 'Ready for New Cycle');
        toggleButton(breakStart, true);
        toggleButton(breakEnd, false);
        setButtonDisabled(breakStart, false);
        setButtonDisabled(breakEnd, false);
    }
}

function updateMovementsDisplay(movements) {
    const container = document.getElementById('todayMovements');
    
    if (!movements || Object.keys(movements).length === 0) {
        container.innerHTML = '<p class="text-muted">No movements recorded yet.</p>';
        updateWorkCyclesSummary({});
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-striped">';
    html += '<thead><tr><th>Time</th><th>Type</th><th>Action</th><th>Cycle</th></tr></thead><tbody>';
    
    let allMovements = [];
    Object.values(movements).forEach(typeMovements => {
        typeMovements.forEach(movement => {
            allMovements.push(movement);
        });
    });
    
    // Sort by time
    allMovements.sort((a, b) => new Date(a.time) - new Date(b.time));
    
    // Calculate cycles for each type
    const cycles = calculateWorkCycles(movements);
    updateWorkCyclesSummary(cycles);
    
    allMovements.forEach(movement => {
        const time = new Date(movement.time).toLocaleTimeString();
        const type = movement.movement_type.charAt(0).toUpperCase() + movement.movement_type.slice(1);
        const action = movement.movement_action.charAt(0).toUpperCase() + movement.movement_action.slice(1);
        
        // Check if this is an automatic transition
        const isAutoTransition = movement.description && movement.description.includes('Auto-ended');
        const actionBadge = isAutoTransition 
            ? `<span class="badge bg-secondary">${action} <i class="fas fa-magic ms-1"></i></span>`
            : `<span class="badge bg-${getActionColor(movement.movement_action)}">${action}</span>`;
        
        // Calculate cycle number for this movement
        const cycleNumber = getCycleNumber(movements, movement);
        
        html += `<tr>
            <td>${time}</td>
            <td><span class="badge bg-primary">${type}</span></td>
            <td>${actionBadge}</td>
            <td><span class="badge bg-info">Cycle ${cycleNumber}</span></td>
        </tr>`;
    });
    
    html += '</tbody></table></div>';
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
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    // Insert at the top of dedicated alerts container
    let alertContainer = document.getElementById('attendanceAlerts');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'attendanceAlerts';
        document.querySelector('.attendance-dashboard')?.prepend(alertContainer);
    }
    alertContainer.innerHTML = alertHtml;
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertContainer) {
            alertContainer.innerHTML = '';
        }
    }, 5000);
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
            if (disable) {
                button.classList.add('btn-secondary');
                button.classList.remove('btn-success', 'btn-danger', 'btn-warning', 'btn-info');
            } else {
                // Restore original button classes based on their purpose
                if (buttonId.includes('PunchIn')) {
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-success');
                } else if (buttonId.includes('PunchOut')) {
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-info');
                } else if (buttonId === 'breakStart') {
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-warning');
                } else if (buttonId === 'breakEnd') {
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-info');
                }
            }
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
    if (parseInt(reasonId, 10) === 1 && !customText) {
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

    // Build final reason text:
    // - If "Other" (id = 1), use ONLY the manual text
    // - Otherwise, use the selected master reason text
    let finalReason;
    if (parseInt(reasonId, 10) === 1) {
        finalReason = customText;
    } else {
        finalReason = reasonText;
    }

    console.log('Sending AJAX request with reason:', finalReason);

    $.ajax({
        url: '/attendance/punch-in',
        method: 'POST',
        data: {
            movement_type: pendingLatePunchType,
            late_reason: finalReason,
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
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
            }
            $('#lateReasonError').removeClass('d-none').text(errorMsg);
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Save Reason & Punch In');
        }
    });
    
    return false;
}


</script>
@endsection
