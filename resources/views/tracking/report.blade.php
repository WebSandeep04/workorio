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

  .data-table-card .custom-table thead th {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
  .icon-red { background: linear-gradient(135deg, #ef4444, #f87171); }
  .icon-teal { background: linear-gradient(135deg, #0d9488, #2dd4bf); }

  .summary-card-content { flex-grow: 1; }
  .summary-card-label { font-size: 0.7rem; font-weight: 700;  color: #000; font-family: Montserrat; }
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
          width: 50px;
      }
      .btn-load {
          width: 100%;
          margin-left: 0;
          text-align: center;
      }
      .summary-cards {
          grid-template-columns: 1fr 1fr;
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
    text-transform: capitalize; font-weight: 700; padding: 0.6rem 0.75rem;
    text-align: left; border-bottom: 1px solid #f1f3f5; border-right: 1px solid #f1f3f5;
    position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat;
  }
  .data-table-card .custom-table thead th:last-child { border-right: none; }
  
  .sticky-col { position: sticky !important; }

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

  /* Custom Toast */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1060;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .custom-toast {
    background: #fff;
    color: #333;
    padding: 12px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    transform: translateX(120%);
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 5px solid #434afa;
    font-family: Montserrat, sans-serif;
    font-size: 0.9rem;
    font-weight: 600;
  }
  .custom-toast.show { transform: translateX(0); }
  .custom-toast.success { border-left-color: #10b981; }
  .custom-toast.error { border-left-color: #ef4444; }
  .custom-toast i { font-size: 1.25rem; }
  .custom-toast.success i { color: #10b981; }
  .custom-toast.error i { color: #ef4444; }
</style>
@endpush

<div class="container-fluid px-2 mt-2">

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-tab-pane" type="button" role="tab" aria-controls="user-tab-pane" aria-selected="true">
                <i class="bi bi-person me-1"></i> User Wise Tracking
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly-tab-pane" type="button" role="tab" aria-controls="monthly-tab-pane" aria-selected="false">
                <i class="bi bi-calendar3 me-1"></i> Monthly Summary Matrix
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily-tab-pane" type="button" role="tab" aria-controls="daily-tab-pane" aria-selected="false">
                <i class="bi bi-calendar-event me-1"></i> Daily Date Wise Tracking
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reportTabsContent">
        
        <!-- User Wise Tab -->
        <div class="tab-pane fade show active" id="user-tab-pane" role="tabpanel" aria-labelledby="user-tab" tabindex="0">
            <!-- Filters -->
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">User</label>
                    <select id="user_id" class="form-select-custom">
                        @if($users->isEmpty())
                            <option value="">No users with tracking enabled</option>
                        @else
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Month</label>
                    <input type="month" id="month" class="form-control-custom" value="{{ now()->format('Y-m') }}">
                </div>
                <button type="button" id="loadReport" class="btn-load">
                    <i class="bi bi-play-circle me-1"></i> Load Report
                </button>
                <button type="button" id="exportExcel" class="btn-load" style="background: #434afa; box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2); margin-left: 0.5rem;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </button>
            </div>

            <!-- Summary Cards -->
            <div id="reportSummary" class="summary-cards" style="display:none;">
                <div class="summary-card">
                    <div class="summary-card-icon icon-blue">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total KM Travelled</div>
                        <div class="summary-card-value" id="sumTotalDistance">0.00 km</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-green">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Days Present</div>
                        <div class="summary-card-value" id="sumPresent">0</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-red">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Days Absent</div>
                        <div class="summary-card-value" id="sumAbsent">0</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-purple">
                        <i class="bi bi-calendar"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total Days</div>
                        <div class="summary-card-value" id="sumTotalDays">0</div>
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
                                    <th>Total Hours</th>
                                    <th>KM Travelled</th>
                                    <th>View Map Journey</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end">TOTAL</td>
                                    <td id="ftTotalHours" class="fw-bold">0.00 hrs</td>
                                    <td id="ftTotalDistance" class="fw-bold">0.00 km</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Tab -->
        <div class="tab-pane fade" id="monthly-tab-pane" role="tabpanel" aria-labelledby="monthly-tab" tabindex="0">
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label">Month</label>
                    <input type="month" id="monthly_month" class="form-control-custom" value="{{ now()->format('Y-m') }}">
                </div>
                <button type="button" id="loadMonthlyReport" class="btn-load">
                    <i class="bi bi-play-circle me-1"></i> Load Summary
                </button>
                <button type="button" id="exportMonthlyExcel" class="btn-load" style="background: #434afa; box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2); margin-left: 0.5rem;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </button>
            </div>

            <div class="modern-card data-table-card" id="monthlyTableCard" style="display:none;">
                <div class="modern-card-body">
                    <div class="table-scroll">
                        <table class="table custom-table" id="monthlyTable">
                            <thead>
                                <!-- Rendered dynamically via JS -->
                            </thead>
                            <tbody>
                                <!-- Data loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily / Date Wise Tab -->
        <div class="tab-pane fade" id="daily-tab-pane" role="tabpanel" aria-labelledby="daily-tab" tabindex="0">
            <div class="filter-bar">
                <div class="filter-group">
                    <label class="filter-label" style="width: 100px;">Select Date</label>
                    <input type="date" id="report_date" class="form-control-custom" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button type="button" id="loadDailyReport" class="btn-load">
                    <i class="bi bi-play-circle me-1"></i> Load Report
                </button>
                <button type="button" id="exportDailyExcel" class="btn-load" style="background: #434afa; box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2); margin-left: 0.5rem;">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </button>
            </div>

            <!-- Daily Summary Cards -->
            <div id="dailyReportSummary" class="summary-cards" style="display:none;">
                <div class="summary-card">
                    <div class="summary-card-icon icon-blue">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total Employees</div>
                        <div class="summary-card-value" id="dailySumUsers">0</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-teal">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Total KM Travelled</div>
                        <div class="summary-card-value" id="dailySumDistance">0.00 km</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-green">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Present</div>
                        <div class="summary-card-value" id="dailySumPresent">0</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-card-icon icon-red">
                        <i class="bi bi-person-x"></i>
                    </div>
                    <div class="summary-card-content">
                        <div class="summary-card-label">Absent</div>
                        <div class="summary-card-value" id="dailySumAbsent">0</div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="modern-card data-table-card" id="dailyReportTableCard" style="display:none;">
                <div class="modern-card-body">
                    <div class="table-scroll">
                        <table class="table custom-table" id="dailyReportTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Total Hours</th>
                                    <th>KM Travelled</th>
                                    <th>View Map Journey</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data loaded via JS -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end">TOTAL</td>
                                    <td></td>
                                    <td id="ftDailyTotalHours" class="fw-bold">0.00 hrs</td>
                                    <td id="ftDailyTotalDistance" class="fw-bold">0.00 km</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="toastContainer" class="toast-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('loadReport').addEventListener('click', loadReport);
    document.getElementById('loadMonthlyReport').addEventListener('click', loadMonthlySummary);
    document.getElementById('loadDailyReport').addEventListener('click', loadDailyReport);
    
    document.getElementById('exportExcel').addEventListener('click', function() {
        const userId = document.getElementById('user_id').value;
        const month = document.getElementById('month').value;
        if(!userId || !month) return;
        if (isFutureMonth(month)) {
            showToast('Cannot generate report for future months.', 'error');
            return;
        }
        window.location.href = `/tracking/export-user-report?user_id=${userId}&month=${month}`;
    });

    document.getElementById('exportMonthlyExcel').addEventListener('click', function() {
        const month = document.getElementById('monthly_month').value;
        if(!month) return;
        if (isFutureMonth(month)) {
            showToast('Cannot generate report for future months.', 'error');
            return;
        }
        window.location.href = `/tracking/export-monthly-report?month=${month}`;
    });

    document.getElementById('exportDailyExcel').addEventListener('click', function() {
        const date = document.getElementById('report_date').value;
        if(!date) return;
        if (isFutureDate(date)) {
            showToast('Cannot generate report for future dates.', 'error');
            return;
        }
        window.location.href = `/tracking/export-date-report?date=${date}`;
    });
});

function loadReport(){
    const userId = document.getElementById('user_id').value;
    const month = document.getElementById('month').value;
    const tbody = document.querySelector('#dailyTable tbody');
    const tableCard = document.getElementById('dailyTableCard');
    const summaryDiv = document.getElementById('reportSummary');
    
    if(!userId || !month) return;

    if (isFutureMonth(month)) {
        showToast('Cannot generate report for future months.', 'error');
        return;
    }

    summaryDiv.style.display = 'none';
    tableCard.style.display = 'block';
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading report...</td></tr>';
    
    $.ajax({
        url: '/tracking/report-data',
        method: 'POST',
        data: { user_id: userId, month: month, _token: '{{ csrf_token() }}' },
        success: function(res){
            const s = res.summary;
            document.getElementById('sumTotalDistance').textContent = s.total_distance_km.toFixed(2) + ' km';
            document.getElementById('sumPresent').textContent = s.total_present;
            document.getElementById('sumAbsent').textContent = s.total_absent;
            document.getElementById('sumTotalDays').textContent = s.total_days;
            summaryDiv.style.display = 'grid';
            
            tbody.innerHTML = '';
            let totalKm = 0;
            let totalHours = 0;
            
            if(res.daily_data && res.daily_data.length > 0) {
                res.daily_data.forEach(function(d){
                    const tr = document.createElement('tr');
                    
                    if(d.status === 'sunday') {
                        tr.style.backgroundColor = '#ffd1d1';
                    }
                    
                    let viewMapBtn = '-';
                    if (d.locations_count > 0) {
                        viewMapBtn = `<a href="/tracking?employee_id=${res.user.id}&date=${d.date}" class="badge bg-primary text-white border-0 py-2 px-3 text-decoration-none rounded-pill" style="background-color: #434afa !important; font-size: 0.75rem;"><i class="bi bi-geo-alt-fill me-1"></i> View Route</a>`;
                    }
                    
                    tr.innerHTML = `
                        <td class="fw-bold text-dark">${d.display_date} (${d.day_name})</td>
                        <td>${statusBadge(d.status)}</td>
                        <td class="fw-bold text-dark font-monospace">${d.hours > 0 ? d.hours.toFixed(2) + ' hrs' : '-'}</td>
                        <td class="fw-bold text-dark font-monospace">${d.km_travelled > 0 ? d.km_travelled.toFixed(2) + ' km' : '-'}</td>
                        <td>${viewMapBtn}</td>`;
                    tbody.appendChild(tr);
                    
                    totalKm += d.km_travelled;
                    totalHours += d.hours;
                });
            } else {
                 tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No data found for this period.</td></tr>';
            }
            
            document.getElementById('ftTotalHours').textContent = totalHours.toFixed(2) + ' hrs';
            document.getElementById('ftTotalDistance').textContent = totalKm.toFixed(2) + ' km';
        },
        error: function(xhr){
            let msg = 'Failed to load report. Please try again.';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">${msg}</td></tr>`;
            console.error(xhr.responseText);
        }
    });
}

function loadMonthlySummary(){
    const month = document.getElementById('monthly_month').value;
    const thead = document.querySelector('#monthlyTable thead');
    const tbody = document.querySelector('#monthlyTable tbody');
    const tableCard = document.getElementById('monthlyTableCard');
    
    if(!month) return;

    if (isFutureMonth(month)) {
        showToast('Cannot generate report for future months.', 'error');
        return;
    }

    tableCard.style.display = 'block';
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading summary matrix...</td></tr>';
    
    $.ajax({
        url: '/tracking/monthly-report-data',
        method: 'POST',
        data: { month: month, _token: '{{ csrf_token() }}' },
        success: function(res){
            // 1. Build Header
            let headerRow = '<tr><th class="sticky-col" style="min-width:150px; background:#fff; left:0; z-index:10; border-right:2px solid #f1f3f5;">User</th>';
            
            if(res.month && res.month.dates){
                res.month.dates.forEach(d => {
                    const isSunday = d.is_sunday;
                    const style = isSunday ? 'color:red; background:#fff0f0;' : '';
                    headerRow += `<th class="text-center" style="min-width:40px; ${style}">
                        <div style="font-size:0.7em;">${d.day}</div>
                        <div style="font-size:0.6em;">${d.day_name}</div>
                    </th>`;
                });
            }
            
            headerRow += '<th style="min-width:75px;">Total KM</th>';
            headerRow += '<th style="min-width:65px;">Present</th>'; 
            headerRow += '<th style="min-width:65px;">Absent</th>';
            headerRow += '</tr>';
            thead.innerHTML = headerRow;

            // 2. Build Body
            tbody.innerHTML = '';
            
            if(res.data && res.data.length > 0) {
                res.data.forEach(function(item){
                    const s = item.summary;
                    const tr = document.createElement('tr');
                    
                    let rowHtml = `<td class="sticky-col fw-bold text-dark" style="background:#fff; left:0; z-index:5; border-right:2px solid #f1f3f5;">${item.user.name}</td>`;
                    
                    if(item.daily_statuses){
                        item.daily_statuses.forEach(dayStat => {
                             let bgStyle = '';
                             if(dayStat.code === 'S' || dayStat.code === 'H') bgStyle = 'background-color:#f8f9fa;'; 
                             
                             let displayContent = '-';
                             let displayClass = 'text-muted';
                             if (dayStat.km_travelled > 0) {
                                 displayContent = `${dayStat.km_travelled.toFixed(2)}`;
                                 displayClass = 'text-dark font-monospace';
                             }
                             
                             rowHtml += `<td class="text-center ${displayClass}" style="${bgStyle} font-size:0.75rem; white-space:nowrap; padding:0.4rem 0.2rem;">
                                ${displayContent}
                             </td>`;
                        });
                    }
                    
                    rowHtml += `<td class="text-center fw-bold text-primary font-monospace" style="color: #434afa !important;">${s.total_distance_km.toFixed(2)} km</td>`;
                    rowHtml += `<td class="text-center fw-bold text-success">${s.total_present}</td>`; 
                    rowHtml += `<td class="text-center fw-bold text-danger">${s.total_absent}</td>`;
                    
                    tr.innerHTML = rowHtml;
                    tbody.appendChild(tr);
                });
            } else {
                 tbody.innerHTML = '<tr><td colspan="35" class="text-center py-4 text-muted">No data found for this month.</td></tr>';
            }
        },
        error: function(xhr){
            let msg = 'Failed to load summary. Please try again.';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            tbody.innerHTML = `<tr><td colspan="35" class="text-center py-4 text-danger">${msg}</td></tr>`;
            console.error(xhr.responseText);
        }
    });
}

function loadDailyReport(){
    const date = document.getElementById('report_date').value;
    const tbody = document.querySelector('#dailyReportTable tbody');
    const tableCard = document.getElementById('dailyReportTableCard');
    const summaryDiv = document.getElementById('dailyReportSummary');
    
    if(!date) return;

    if (isFutureDate(date)) {
        showToast('Cannot generate report for future dates.', 'error');
        return;
    }

    summaryDiv.style.display = 'none';
    tableCard.style.display = 'block';
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading daily report...</td></tr>';
    
    $.ajax({
        url: '/tracking/date-report-data',
        method: 'POST',
        data: { date: date, _token: '{{ csrf_token() }}' },
        success: function(res){
            const s = res.summary;
            document.getElementById('dailySumUsers').textContent = s.total_users;
            document.getElementById('dailySumDistance').textContent = s.total_distance_km.toFixed(2) + ' km';
            document.getElementById('dailySumPresent').textContent = s.present;
            document.getElementById('dailySumAbsent').textContent = s.absent;
            summaryDiv.style.display = 'grid';
            
            tbody.innerHTML = '';
            let totalKm = 0;
            let totalHours = 0;
            
            if(res.data && res.data.length > 0) {
                res.data.forEach(function(d){
                    const tr = document.createElement('tr');
                    
                    let viewMapBtn = '-';
                    if (d.locations_count > 0) {
                        viewMapBtn = `<a href="/tracking?employee_id=${d.user.id}&date=${res.date.date}" class="badge bg-primary text-white border-0 py-2 px-3 text-decoration-none rounded-pill" style="background-color: #434afa !important; font-size: 0.75rem;"><i class="bi bi-geo-alt-fill me-1"></i> View Route</a>`;
                    }
                    
                    tr.innerHTML = `
                        <td class="fw-bold text-dark">${d.user.name}</td>
                        <td>${statusBadge(d.status)}</td>
                        <td class="fw-bold text-dark font-monospace">${d.hours > 0 ? d.hours.toFixed(2) + ' hrs' : '-'}</td>
                        <td class="fw-bold text-dark font-monospace">${d.km_travelled > 0 ? d.km_travelled.toFixed(2) + ' km' : '-'}</td>
                        <td>${viewMapBtn}</td>`;
                    tbody.appendChild(tr);
                    
                    totalKm += d.km_travelled;
                    totalHours += d.hours;
                });
            } else {
                 tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No active tracking users found.</td></tr>';
            }
            
            document.getElementById('ftDailyTotalHours').textContent = totalHours.toFixed(2) + ' hrs';
            document.getElementById('ftDailyTotalDistance').textContent = totalKm.toFixed(2) + ' km';
        },
        error: function(xhr){
            let msg = 'Failed to load daily report. Please try again.';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">${msg}</td></tr>`;
            console.error(xhr.responseText);
        }
    });
}

function statusBadge(status){
    const map = {
        present:'success', 
        'present with SL':'success',
        'present with HD':'success',
        'present (partial leave)':'success',
        leave:'warning', 
        holiday:'info', 
        sunday:'secondary', 
        absent:'danger',
        halfday:'warning',
        'absent by less hr':'danger',
        'weekly off':'secondary',
        'restricted holiday':'info',
        'short leave':'info'
    };
    const cls = map[status]||'secondary';
    const displayStatus = status.replace(/\b\w/g, l => l.toUpperCase());
    return `<span class="badge bg-${cls} bg-opacity-10 text-${cls} border border-${cls} border-opacity-25 rounded-pill px-2 py-1">${displayStatus}</span>`;
}

function isFutureMonth(monthStr) {
    if (!monthStr) return false;
    const [year, month] = monthStr.split('-').map(Number);
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;
    
    if (year > currentYear) return true;
    if (year === currentYear && month > currentMonth) return true;
    
    return false;
}

function isFutureDate(dateStr) {
    if (!dateStr) return false;
    const now = new Date();
    const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    return dateStr > todayStr;
}

function showToast(message, type = 'success') {
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    const toastHtml = `
        <div class="custom-toast ${type}">
            <i class="bi ${icon}"></i>
            <span>${message}</span>
        </div>
    `;
    const $toast = $(toastHtml);
    $('#toastContainer').append($toast);
    setTimeout(() => $toast.addClass('show'), 100);
    setTimeout(() => {
        $toast.removeClass('show');
        setTimeout(() => $toast.remove(), 400);
    }, 4000);
}
</script>
@endsection
