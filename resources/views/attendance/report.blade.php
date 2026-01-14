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
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
  .icon-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

  .summary-card-content { flex-grow: 1; }
  .summary-card-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #64748b; font-family: Montserrat; }
  .summary-card-value { font-size: 1.1rem; font-weight: 700; line-height: 1; color: #0f172a; font-family: Montserrat; }

  /* Controls (Filter Form) */
  .filter-bar {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      background: #fff;
      padding: 1rem 1.5rem;
      border-radius: 5px;
      border: 1px solid #f2f4f7;
      margin-bottom: 1rem;
      flex-wrap: wrap;
  }
  .filter-group {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex: 1;
      min-width: 250px;
  }
  .filter-label { font-size: 0.85rem; font-weight: 600; color: #64748b; white-space: nowrap; width: 50px; }
  .form-select-custom, .form-control-custom {
      background: #f4f5f7;
      border: 1px solid #e5e7eb;
      border-radius: 4px;
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
      flex: 1;
      outline: none;
      color: #111827;
      min-width: 0;
  }
  .btn-load {
    padding: 0.5rem 1.5rem;
    background: #434afa;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2);
    transition: all 0.2s;
    margin-left: auto;
    white-space: nowrap;
  }
  .btn-load:hover { background: #3538d4; transform: translateY(-1px); }

  /* Mobile Responsiveness */
  @media (max-width: 768px) {
      .filter-bar {
          flex-direction: column;
          align-items: stretch;
          gap: 1rem;
          padding: 1rem;
      }
      .filter-group {
          min-width: 100%;
          gap: 0.5rem;
      }
      .filter-label {
          width: 50px; /* Keep label width consistent or allow it to be auto if favored */
      }
      .btn-load {
          width: 100%;
          margin-left: 0;
          text-align: center;
      }
      .summary-cards {
          grid-template-columns: 1fr 1fr; /* 2 columns on mobile */
          gap: 0.5rem;
      }
      .summary-card {
          padding: 0.5rem;
          flex-direction: column;
          text-align: center;
          gap: 0.25rem;
      }
      .summary-card-icon {
          width: 35px;
          height: 35px;
      }
      .summary-card-icon i { font-size: 1rem; }
      .summary-card-label { font-size: 0.65rem; }
      .summary-card-value { font-size: 0.9rem; }
  }

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
    border-collapse: separate; border-spacing: 0; width: 100%; min-width: 1000px;
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

  .data-table-card .custom-table tfoot td {
      background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;
  }
  
  .btn-view-details {
      background-color: #434afa;
      border: none;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      font-size: 0.75rem;
  }
  .btn-view-details:hover { background-color: #3538d4; color: white; }
</style>
@endpush

<div class="container-fluid px-2 mt-2">

    <!-- Filters -->
    <div class="filter-bar">
        <div class="filter-group">
            <label class="filter-label">User</label>
            <select id="user_id" class="form-select-custom">
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Month</label>
            <input type="month" id="month" class="form-control-custom" value="{{ now()->format('Y-m') }}">
        </div>
        <button type="button" id="loadReport" class="btn-load">
            <i class="bi bi-play-circle me-1"></i> Load Report
        </button>
    </div>

    <!-- Summary Cards -->
    <div id="reportSummary" class="summary-cards" style="display:none;">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Working Days</div>
                <div class="summary-card-value" id="sumWorkingDays">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-briefcase"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Days Worked</div>
                <div class="summary-card-value" id="sumDaysWorked">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-purple">
                <i class="bi bi-percent"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Attendance %</div>
                <div class="summary-card-value" id="sumAttendancePct">0%</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-orange">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Hours</div>
                <div class="summary-card-value" id="sumTotalHours">0:00</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card" id="dailyTableCard" style="display:none;">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="dailyTable">
                    <thead>
                        <tr>
                            <th style="min-width:140px;">Date</th>
                            <th>Status</th>
                            <th>First In</th>
                            <th>Last Out</th>
                            <th>Total (H:MM)</th>
                            <th>Office</th>
                            <th>Field</th>
                            <th>Break</th>
                            <th>Late Reason</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data loaded via JS -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end">TOTAL</td>
                            <td id="ftTotal"></td>
                            <td id="ftOffice"></td>
                            <td id="ftField"></td>
                            <td id="ftBreak"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loadReport').addEventListener('click', loadReport);
});

function loadReport(){
    const userId = document.getElementById('user_id').value;
    const month = document.getElementById('month').value;
    const tbody = document.querySelector('#dailyTable tbody');
    const tableCard = document.getElementById('dailyTableCard');
    const summaryDiv = document.getElementById('reportSummary');
    
    if(!userId || !month) return;

    // Loading State
    summaryDiv.style.display = 'none';
    tableCard.style.display = 'block';
    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading report...</td></tr>';
    
    $.ajax({
        url: '/attendance/report-data',
        method: 'POST',
        data: { user_id: userId, month: month, _token: '{{ csrf_token() }}' },
        success: function(res){
            const s = res.summary;
            document.getElementById('sumWorkingDays').textContent = s.total_working_days;
            document.getElementById('sumDaysWorked').innerHTML = `${s.days_worked} <small class="text-muted fw-normal fs-7 ms-1">(Leave ${s.days_on_leave}, Abs ${s.days_absent})</small>`;
            document.getElementById('sumAttendancePct').textContent = s.attendance_percentage + '%';
            document.getElementById('sumTotalHours').textContent = hoursClock(s.total_hours);
            summaryDiv.style.display = 'grid';
            
            tbody.innerHTML = '';
            let tot=0, to=0, tf=0, tb=0;
            
            if(res.daily_breakdown && res.daily_breakdown.length > 0) {
                res.daily_breakdown.forEach(function(d, idx){
                    const tr = document.createElement('tr');
                    const firstIn = findFirstIn(d.movements);
                    const lastOut = findLastOut(d.movements);
                    
                    tr.innerHTML = `
                        <td class="fw-bold text-dark">${d.display_date}</td>
                        <td>${statusBadge(d.status, d.holiday_name)}</td>
                        <td class="font-monospace">${firstIn||'-'}</td>
                        <td class="font-monospace">${lastOut||'-'}</td>
                        <td class="fw-bold">${hoursClock(d.hours)}</td>
                        <td>${hoursClock(d.office_hours)}</td>
                        <td>${hoursClock(d.field_hours)}</td>
                        <td>${hoursClock(d.break_time)}</td>
                        <td><small class="text-muted text-break" style="font-size:0.7em;">${d.description||'-'}</small></td>
                        <td class="text-center">
                            <button class="btn-view-details shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mov-${idx}" aria-expanded="false">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </td>`;
                    tbody.appendChild(tr);
                    
                    const trDet = document.createElement('tr');
                    trDet.innerHTML = `<td colspan="10" class="p-0 border-0">
                        <div id="mov-${idx}" class="collapse bg-light border-bottom">
                            ${renderMovements(d.movements)}
                        </div>
                    </td>`;
                    tbody.appendChild(trDet);
                    
                    tot += Number(d.hours||0); to += Number(d.office_hours||0); tf += Number(d.field_hours||0); tb += Number(d.break_time||0);
                });
            } else {
                 tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">No data found for this period.</td></tr>';
            }
            
            document.getElementById('ftTotal').textContent = hoursClock(tot);
            document.getElementById('ftOffice').textContent = hoursClock(to);
            document.getElementById('ftField').textContent = hoursClock(tf);
            document.getElementById('ftBreak').textContent = hoursClock(tb);
        },
        error: function(xhr){
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-danger">Failed to load report. Please try again.</td></tr>';
            console.error(xhr.responseText);
        }
    });
}

function hoursClock(decimal){
    const minutes = Math.round(Number(decimal||0) * 60);
    const h = Math.floor(minutes/60);
    const m = minutes%60;
    return h + ':' + String(m).padStart(2,'0');
}

function statusBadge(status, holidayName = null){
    const map = {
        present:'success', 
        leave:'warning', 
        holiday:'info', 
        sunday:'secondary', 
        absent:'danger',
        halfday:'warning',
        'absent by less hr':'danger'
    };
    const cls = map[status]||'secondary';
    
    if (status === 'holiday' && holidayName) {
        return `<span class="badge w-100 bg-${cls} bg-opacity-25 text-${cls} border border-${cls}">${status.charAt(0).toUpperCase()+status.slice(1)}: ${holidayName}</span>`;
    }
    
    // Handle status with spaces like "absent by less hr"
    const displayStatus = status.replace(/\b\w/g, l => l.toUpperCase());
    
    // Modern badge styling
    let badgeClass = '';
    if(cls === 'success') badgeClass = 'text-success fw-bold';
    else if(cls === 'danger') badgeClass = 'text-danger fw-bold';
    else badgeClass = `badge bg-${cls} bg-opacity-10 text-${cls} px-3 rounded-pill`;
    
    // If it's a simple text status preference like in previous screens, use text-success, else use badges. 
    // Given the previous step requested text-success/bold for Active, I'll use badges for these varied statuses as they are more complex (Holiday, Leave, etc), but styled softly.
    
    return `<span class="badge bg-${cls} bg-opacity-10 text-${cls} border border-${cls} border-opacity-25 rounded-pill px-2">${displayStatus}</span>`;
}

function findFirstIn(movements){
    if(!movements||!movements.length) return '';
    const first = movements.find(m=> (m.type==='Office' || m.type==='Field') && m.action==='In');
    return first ? first.time : '';
}

function findLastOut(movements){
    if(!movements||!movements.length) return '';
    const outs = movements.filter(m=> (m.type==='Office' || m.type==='Field') && m.action==='Out');
    if(!outs.length) return '';
    return outs[outs.length-1].time;
}

function renderMovements(movements){
    if(!movements||!movements.length) return '<div class="p-3 text-muted text-center small">No detailed movements recorded.</div>';
    
    let html = '<div class="p-3"><div class="table-responsive"><table class="table table-sm table-bordered mb-0 bg-white" style="font-size:0.75rem;">';
    html += '<thead class="table-light"><tr><th style="width:100px;">Time</th><th>Type</th><th>Action</th><th>Description</th></tr></thead><tbody>';
    movements.forEach(m=>{
        // Badge styles for internal table
        let badgeStyle = 'background-color:#6c757d;color:white;';
        if(m.type === 'Office') badgeStyle = 'background-color:#434afa;color:white;'; // Office Blue
        if(m.type === 'Field') badgeStyle = 'background-color:#10b981;color:white;'; // Field Green
        
        let actionBadge = 'secondary';
        if(m.action === 'In' || m.action === 'Start') actionBadge = 'success';
        if(m.action === 'Out' || m.action === 'End') actionBadge = 'danger';
        
        // Consistent with previous step: make Type/Action badges in details blue
        // User asked "match this blade UI", and previously asked for Blue badges in the *modal*. 
        // I will use the #434afa style for badges here too for consistency if desired, or keep logic.
        // Let's use #434afa for Type and Action as requested in Step 969 ("same for type and action 434afa and white").
        
        const typeBadge = `<span class="badge text-white" style="background-color: #434afa;">${m.type}</span>`;
        const actBadge = `<span class="badge text-white" style="background-color: #434afa;">${m.action}</span>`;
        
        html += `<tr>
            <td class="font-monospace">${m.time}</td>
            <td>${typeBadge}</td>
            <td>${actBadge}</td>
            <td>${m.description||'-'}</td>
        </tr>`;
    });
    html += '</tbody></table></div></div>';
    return html;
}
</script>
@endsection
