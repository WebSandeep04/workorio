@extends('layouts.app')

@section('content')
@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
  }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.75rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .summary-card-icon {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .summary-card-icon i { font-size: 1.25rem; color: white; }
  
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
  .icon-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content { flex-grow: 1; }
  .summary-card-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #64748b; font-family: Montserrat; }
  .summary-card-value { font-size: 1.1rem; font-weight: 700; line-height: 1; color: #0f172a; font-family: Montserrat; }

  /* Controls */
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
  }
  
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Table Styles */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card {
    border-radius: 5px; border: 1px solid #f2f4f7; background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden;
  }
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; background: transparent; }
  
  .data-table-card .custom-table {
    border-collapse: separate; border-spacing: 0; width: 100%; min-width: 800px;
    background: transparent; font-size: 0.85rem; table-layout: auto;
  }

  .data-table-card .custom-table thead th {
    background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em;
    text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem;
    text-align: left; border-bottom: 1px solid #f1f3f5; border-right: 1px solid #f1f3f5;
    position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat;
  }
  .data-table-card .custom-table thead th:last-child { border-right: none; }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #0f172a;
    border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent;
    font-family: Montserrat; vertical-align: middle;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
  }
  
  /* Modals */
  .modal-header { background-color: #434afa !important; color: white; border-radius: 0; }
  .modal-content { border-radius: 0; border: none; }
  .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
@endpush

<div class="container-fluid px-2 mt-2">
    <!-- Status Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-calendar-range"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Working Days</div>
                <div class="summary-card-value" id="totalWorkingDays">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Present</div>
                <div class="summary-card-value" id="totalPresent">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-orange">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Half-Day</div>
                <div class="summary-card-value" id="totalHalfDay">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-purple">
                <i class="bi bi-calendar-event"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Leave</div>
                <div class="summary-card-value" id="totalLeave">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-blue" style="background: linear-gradient(135deg, #f43f5e, #fb7185) !important;">
                <i class="bi bi-person-x"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Absent</div>
                <div class="summary-card-value" id="totalAbsent">0</div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="attendanceSearch" placeholder="Filter current page..." />
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="monthFilter" class="small fw-bold text-muted mb-0">Month:</label>
            <input type="month" id="monthFilter" class="form-control form-control-sm shadow-sm" 
                   value="{{ date('Y-m') }}" style="width: auto; border-radius: 4px; border: 1px solid #e5e7eb;">
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Status Reason</th>
                            <th>First In</th>
                            <th>Last Out</th>
                            <th>Total Hours</th>
                            <th>Office</th>
                            <th>Field</th>
                            <th>Break</th>
                            <th>Late By</th>
                            <th>Grace Bal.</th>
                            <th>Late Reason</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <tr><td colspan="13" class="text-center py-4 text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Movement Details Modal -->
<div class="modal fade" id="movementModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="movementDetails">
                <!-- Data loaded via JS -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAttendanceHistory();
    
    document.getElementById('monthFilter').addEventListener('change', loadAttendanceHistory);

    document.getElementById('attendanceSearch').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('#attendanceTableBody tr');
        rows.forEach(row => {
            if(row.cells.length < 2) return;
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});

function loadAttendanceHistory() {
    const tbody = document.getElementById('attendanceTableBody');
    const month = document.getElementById('monthFilter').value;
    
    tbody.innerHTML = '<tr><td colspan="13" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';
    
    $.ajax({
        url: '/attendance/history/data',
        method: 'GET',
        data: { month: month },
        success: function(response) {
            if (response && response.attendances) {
                displayAttendanceData(response.attendances, response.summary);
            } else {
                tbody.innerHTML = '<tr><td colspan="13" class="text-center text-danger">Data format error</td></tr>';
            }
        },
        error: function(xhr) {
             tbody.innerHTML = '<tr><td colspan="13" class="text-center text-danger">Failed to load attendance history</td></tr>';
        }
    });
}

function displayAttendanceData(attendances, summary) {
    window.currentAttendances = attendances;
    const tbody = document.getElementById('attendanceTableBody');
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="13" class="text-center text-muted">No attendance records found</td></tr>';
        updateSummaryStats([], summary);
        return;
    }

    let html = '';
    attendances.forEach(function(attendance) {
        const dateStr = attendance.display_date || attendance.date;
        const status = (attendance.status || 'absent').toLowerCase();
        const officeHours = attendance.office_hours || 0;
        const fieldHours = attendance.field_hours || 0;
        const breakHours = attendance.break_time || 0;
        const totalHours = attendance.hours || 0;
        
        let statusBadge = '';
        let rowClass = '';
        
        const displayLabel = status.replace(/\b\w/g, l => l.toUpperCase());
        
        switch(status) {
            case 'present':
            case 'present with sl':
            case 'present with hd':
            case 'present (partial leave)':
                statusBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2">${displayLabel}</span>`;
                break;
            case 'halfday':
                statusBadge = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">Half-Day</span>';
                break;
            case 'weekly off':
            case 'sunday':
                statusBadge = '<span class="badge bg-info-subtle text-info border border-info-subtle px-2">Weekly Off</span>';
                rowClass = 'table-light text-muted';
                break;
            case 'holiday':
                statusBadge = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">Holiday${attendance.holiday_name ? ': ' + attendance.holiday_name : ''}</span>`;
                rowClass = 'table-light text-muted';
                break;
            case 'leave':
            case 'short leave':
            case 'restricted holiday':
                statusBadge = `<span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2" style="background-color: #f3e8ff; color: #7e22ce;">${displayLabel}</span>`;
                break;
            case 'absent':
            case 'absent by less hr':
                statusBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2">${displayLabel}</span>`;
                break;
            default:
                if (status.includes('working')) {
                    statusBadge = `<span class="badge bg-info-subtle text-info border border-info-subtle px-2">${displayLabel}</span>`;
                } else {
                    statusBadge = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2">${displayLabel}</span>`;
                }
        }

        html += `<tr class="${rowClass}">
            <td class="fw-bold">${dateStr}</td>
            <td>${statusBadge}</td>
            <td class="text-muted small">${attendance.status_reason || '-'}</td>
            <td>${attendance.first_in || '-'}</td>
            <td>${attendance.last_out || '-'}</td>
            <td class="fw-bold text-dark">${totalHours > 0 ? formatHoursMinutes(totalHours) : '-'}</td>
            <td>${officeHours > 0 ? formatHoursMinutes(officeHours) : '-'}</td>
            <td>${fieldHours > 0 ? formatHoursMinutes(fieldHours) : '-'}</td>
            <td>${breakHours > 0 ? formatHoursMinutes(breakHours) : '-'}</td>
            <td class="${attendance.late_by && attendance.late_by !== '-' ? 'text-danger fw-medium' : ''}">${attendance.late_by || '-'}</td>
            <td class="text-success fw-medium">${attendance.grace_balance || '-'}</td>
            <td class="text-muted small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${attendance.late_reason || ''}">${attendance.late_reason || '-'}</td>
            <td class="text-center">
                ${attendance.movements && attendance.movements.length > 0 ? `
                <button type="button" class="btn btn-sm text-white shadow-sm" style="background-color: #434afa; border:none; padding: 0.25rem 0.75rem; border-radius: 4px;" onclick="viewMovementsForDate('${attendance.date}')">
                    <i class="fas fa-chevron-down"></i>
                </button>
                ` : '-'}
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
    updateSummaryStats(attendances, summary);
}

function formatHoursMinutes(decimalHours) {
    if (!decimalHours) return '-';
    const hours = Math.floor(decimalHours);
    const minutes = Math.round((decimalHours - hours) * 60);
    let finalHours = hours;
    let finalMinutes = minutes;
    if (minutes >= 60) { finalHours += 1; finalMinutes = 0; }
    return `${finalHours}h ${finalMinutes.toString().padStart(2, '0')}m`;
}

function updateSummaryStats(attendances, summary) {
    if (summary) {
        document.getElementById('totalWorkingDays').textContent = summary.total_working_days || 0;
        document.getElementById('totalPresent').textContent = summary.total_present || 0;
        document.getElementById('totalHalfDay').textContent = summary.total_halfday || 0;
        document.getElementById('totalLeave').textContent = summary.days_on_leave || 0;
        document.getElementById('totalAbsent').textContent = summary.days_absent || 0;
    }
}

function viewMovementsForDate(date) {
    if (!window.currentAttendances) return;
    const attendance = window.currentAttendances.find(a => a.date === date);
    if (!attendance || !attendance.movements || attendance.movements.length === 0) {
        alert('No movements found for this date.');
        return;
    }
    
    const moves = attendance.movements;
    let html = `
        <div class="table-responsive">
            <table class="table table-sm custom-table">
                <thead>
                    <tr class="bg-light">
                        <th>Time</th>
                        <th>Type</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    moves.forEach(m => {
        html += `
            <tr>
                <td><span class="fw-bold">${m.time}</span></td>
                <td><span class="badge text-white" style="background-color: #434afa;">${m.type}</span></td>
                <td><span class="badge text-white" style="background-color: #434afa;">${m.action}</span></td>
                <td><small class="text-secondary">${m.description || '-'}</small></td>
            </tr>
        `;
    });
    
    html += `</tbody></table></div>`;
    
    document.getElementById('movementDetails').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('movementModal'));
    modal.show();
}
</script>
@endsection
