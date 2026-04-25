

<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
  }

  .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
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

  /* Controls (Search/Refresh) */
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
  
  .data-table-card .table-scroll::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-scroll::-webkit-scrollbar-thumb { background: #434aFA; border-radius: 999px; }

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
    background: #f8f9ff; transform: translateY(-1px); box-shadow: 0px 2px 5px rgba(0,0,0,0.02);
  }
  
  /* Pagination */
  .pagination .page-link {
    color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px;
    padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-weight: 500; cursor: pointer;
    transition: all 0.3s ease;
  }
  .pagination .page-item.active .page-link {
    background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }
  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }
  
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }
  
  /* Modals */
  .modal-header { background-color: #434afa !important; color: white; border-radius: 0; }
  .modal-content { border-radius: 0; border: none; }
  .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
<?php $__env->stopPush(); ?>

<div class="container-fluid px-2 mt-2">
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-clock"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Office Hours</div>
                <div class="summary-card-value" id="totalOfficeHours">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Field Hours</div>
                <div class="summary-card-value" id="totalFieldHours">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-purple">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Days</div>
                <div class="summary-card-value" id="totalDays">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-orange">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Cycles</div>
                <div class="summary-card-value" id="totalCycles">0</div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="attendanceSearch" placeholder="Filter current page..." />
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
                            <th>Office Hours</th>
                            <th>Field Hours</th>
                            <th>Total Hours</th>
                            <th>Cycles</th>
                            <th>Movements</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <tr><td colspan="7" class="text-center py-4 text-muted">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-2">
        <ul class="pagination" id="pagination"></ul>
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
            <div class="modal-footer border-0 p-0"></div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
// We'll keep the server-side pagination flow as the main data source
// But styling the UI to match the new look

document.addEventListener('DOMContentLoaded', function() {
    loadAttendanceHistory();
    
    // Client-side simple filter for visible rows
    const searchInput = document.getElementById('attendanceSearch');
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#attendanceTableBody tr');
            rows.forEach(row => {
                // Skip if it's a loading or info row
                if(row.cells.length < 2) return;
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});

function loadAttendanceHistory(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('attendanceTableBody');
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>';
    
    $.ajax({
        url: '/attendance/history/data',
        method: 'GET',
        data: { page: page, per_page: 15 }, // Increased per page slightly
        success: function(response) {
            if (response && response.data) {
                displayAttendanceData(response.data);
                generatePagination(response.current_page, response.last_page);
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Data format error</td></tr>';
            }
        },
        error: function(xhr) {
             tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Failed to load attendance history</td></tr>';
        }
    });
}

function displayAttendanceData(attendances) {
    window.currentAttendances = attendances; // Store for modal
    const tbody = document.getElementById('attendanceTableBody');
    
    if (!attendances || attendances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No attendance records found</td></tr>';
        updateSummaryStats([], 0, 0, 0);
        return;
    }

    let html = '';
    let totalOfficeHours = 0;
    let totalFieldHours = 0;
    let totalCycles = 0;
    
    attendances.forEach(function(attendance) {
        const dateObj = new Date(attendance.date);
        const dateStr = dateObj.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
        
        const officeHours = calculateTypeHours(attendance.movements, 'office');
        const fieldHours = calculateTypeHours(attendance.movements, 'field');
        const totalHours = officeHours + fieldHours;
        const cycles = calculateCycles(attendance.movements);
        
        totalOfficeHours += officeHours;
        totalFieldHours += fieldHours;
        totalCycles += cycles.office + cycles.field + cycles.break;
        
        html += `<tr>
            <td class="fw-bold">${dateStr}</td>
            <td>${formatHoursMinutes(officeHours)}</td>
            <td>${formatHoursMinutes(fieldHours)}</td>
            <td class="fw-bold text-dark">${formatHoursMinutes(totalHours)}</td>
            <td>
                <span class="badge text-white me-1" style="background-color: #434afa;">O: ${cycles.office}</span>
                <span class="badge text-white me-1" style="background-color: #434afa;">F: ${cycles.field}</span>
                <span class="badge text-white" style="background-color: #434afa;">B: ${cycles.break}</span>
            </td>
             <td>
              ${attendance.movements ? attendance.movements.length + ' events' : '0 events'}
             </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-white shadow-sm" style="background-color: #434afa; border:none; padding: 0.25rem 0.75rem; border-radius: 4px;" onclick="viewMovements(${attendance.id})">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
    updateSummaryStats(attendances, totalOfficeHours, totalFieldHours, totalCycles);
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

function updateSummaryStats(attendances, totalOfficeHours, totalFieldHours, totalCycles) {
    document.getElementById('totalOfficeHours').textContent = formatHoursMinutes(totalOfficeHours);
    document.getElementById('totalFieldHours').textContent = formatHoursMinutes(totalFieldHours);
    document.getElementById('totalDays').textContent = attendances.length || 0;
    document.getElementById('totalCycles').textContent = totalCycles || 0;
}

function calculateTypeHours(movements, type) {
    const typeMovements = movements.filter(m => m.movement_type === type);
    if (!typeMovements.length) return 0;
    typeMovements.sort((a, b) => new Date(a.time) - new Date(b.time));
    
    let firstPunchIn = null, lastPunchOut = null;
    
    typeMovements.forEach(m => {
        if (m.movement_action === 'in' && !firstPunchIn) firstPunchIn = new Date(m.time);
        if (m.movement_action === 'out') lastPunchOut = new Date(m.time);
    });
    
    if (!firstPunchIn) return 0;
    if (!lastPunchOut) {
        const endOfDay = new Date(firstPunchIn);
        endOfDay.setHours(18, 0, 0, 0);
        lastPunchOut = (new Date() > endOfDay) ? endOfDay : new Date();
    }
    return ((lastPunchOut - firstPunchIn) / (1000 * 60)) / 60;
}

function calculateCycles(movements) {
    const cycles = { office: 0, field: 0, break: 0 };
    const grouped = { office: [], field: [], break: [] };
    movements.forEach(m => { if(grouped[m.movement_type]) grouped[m.movement_type].push(m); });
    
    if(grouped.break.length) {
        let s=0, e=0;
        grouped.break.forEach(m => { if(m.movement_action === 'start') s++; if(m.movement_action === 'end') e++; });
        cycles.break = Math.min(s, e) || (s > 0 ? s : 0); // approx
    }
    
    ['office', 'field'].forEach(type => {
        if(grouped[type].length) {
            let i=0, o=0;
            grouped[type].forEach(m => { if(m.movement_action === 'in') i++; if(m.movement_action === 'out') o++; });
            cycles[type] = Math.max(i, o); // rough cycle count
        }
    });
    return cycles;
}

function generatePagination(current, last) {
    const pagination = document.getElementById('pagination');
    if (last <= 1) { pagination.innerHTML = ''; return; }
    
    let html = '';
    if (current > 1) {
        html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(${current - 1})">Prev</a></li>`;
    }
    
    if (last <= 7) {
        for(let i=1; i<=last; i++) {
            html += `<li class="page-item ${i===current?'active':''}"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(${i})">${i}</a></li>`;
        }
    } else {
        html += `<li class="page-item ${1===current?'active':''}"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(1)">1</a></li>`;
        if(current > 3) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        let s = Math.max(2, current-1), e = Math.min(last-1, current+1);
        for(let i=s; i<=e; i++) {
            html += `<li class="page-item ${i===current?'active':''}"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(${i})">${i}</a></li>`;
        }
        if(current < last-2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
         html += `<li class="page-item ${last===current?'active':''}"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(${last})">${last}</a></li>`;
    }

    if (current < last) {
         html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="loadAttendanceHistory(${current + 1})">Next</a></li>`;
    }
    pagination.innerHTML = html;
}

function viewMovements(id) {
    const attendances = window.currentAttendances || [];
    const att = attendances.find(a => a.id === id);
    if (!att) return;
    
    // Simple detail render for modal
    const moves = att.movements || [];
    moves.sort((a,b) => new Date(a.time) - new Date(b.time));
    
    let html = `
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead><tr class="bg-light"><th>Time</th><th>Type</th><th>Action</th><th>Description</th></tr></thead>
                <tbody>
    `;
    if(moves.length === 0) {
        html += '<tr><td colspan="4" class="text-center text-muted">No details</td></tr>';
    } else {
        moves.forEach(m => {
            const t = new Date(m.time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
            
            html += `<tr>
                <td>${t}</td>
                <td><span class="badge text-white" style="background-color: #434afa;">${m.movement_type}</span></td>
                <td><span class="badge text-white" style="background-color: #434afa;">${m.movement_action}</span></td>
                <td><small>${m.description || '-'}</small></td>
            </tr>`;
        });
    }
    html += `</tbody></table></div>`;
    
    document.getElementById('movementDetails').innerHTML = html;
    new bootstrap.Modal(document.getElementById('movementModal')).show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/attendance/history.blade.php ENDPATH**/ ?>