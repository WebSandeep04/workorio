@extends('layouts.app')

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
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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

  /* Filter Bar */
  .filter-bar {
      display: flex;
      align-items: center;
      gap: 1rem;
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
      gap: 0.5rem;
      flex: 1;
      min-width: 200px;
  }
  .filter-label { font-size: 0.8rem; font-weight: 600; color: #64748b; white-space: nowrap; margin-bottom: 0; }
  .form-select-custom, .form-control-custom {
      background: #f4f5f7;
      border: 1px solid #e5e7eb;
      border-radius: 4px;
      padding: 0.5rem 0.75rem;
      font-size: 0.85rem;
      width: 100%;
      outline: none;
      color: #111827;
      min-width: 0;
  }

  /* Buttons */
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
    white-space: nowrap;
  }
  .btn-load:hover { background: #3538d4; transform: translateY(-1px); }
  .btn-load:disabled { background: #a0a0a0; cursor: not-allowed; }

  .btn-pdf {
    padding: 0.5rem 1.5rem;
    background: #fff;
    color: #434afa;
    border: 1px solid #434afa;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .btn-pdf:hover:not(:disabled) { background: #f0f4ff; }
  .btn-pdf:disabled { border-color: #e5e7eb; color: #9ca3af; cursor: not-allowed; }

  /* Charts & Table */
  .modern-card { padding: 0; margin-bottom: 0.75rem; border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 4px 6px rgba(0,0,0,0.02); overflow: hidden; }
  
  .modern-card-header {
      padding: 0.75rem 1rem;
      background: #fff;
      border-bottom: 1px solid #f1f3f5;
      font-weight: 600;
      font-size: 0.9rem;
      color: #0f172a;
      display: flex;
      align-items: center;
      justify-content: space-between;
  }

  .modern-card-body { padding: 1rem; }
  
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; background: transparent; }
  
  .data-table-card .table-scroll::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-scroll::-webkit-scrollbar-thumb { background: #434aFA; border-radius: 999px; }

  .custom-table {
    border-collapse: separate; border-spacing: 0; width: 100%; min-width: 1000px;
    background: transparent; font-size: 0.85rem; table-layout: auto;
  }

  .custom-table thead th {
    background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em;
    text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem;
    text-align: left; border-bottom: 1px solid #f1f3f5; border-right: 1px solid #f1f3f5;
    position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat;
  }
  .custom-table thead th:last-child { border-right: none; }

  .custom-table tbody td {
    font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #0f172a;
    border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent;
    font-family: Montserrat; vertical-align: middle;
  }

  .custom-table tbody tr:hover {
    background: #f8f9ff; transform: translateY(-1px); box-shadow: 0px 2px 5px rgba(0,0,0,0.02);
  }
  
  /* OVERRIDE table-info to look like modern group header while keeping the class for PDF logic */
  .table-info {
      background-color: #f8fafc !important;
  }
  .table-info td {
      background-color: #f8fafc !important;
      border-bottom: 2px solid #e2e8f0;
      padding-top: 1rem !important;
      padding-bottom: 1rem !important;
      color: #0f172a;
  }

  /* Mobile Responsiveness */
  @media (max-width: 768px) {
      .filter-bar {
          flex-direction: column;
          align-items: stretch;
          padding: 1rem;
          gap: 1rem;
      }
      .filter-group {
          width: 100%;
          min-width: 100%;
          display: block;
      }
      .filter-label { display: block; margin-bottom: 0.25rem; }
      .btn-load, .btn-pdf { width: 100%; margin-top: 0.5rem; text-align: center; }
      .summary-cards { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
      .summary-card { padding: 0.5rem; flex-direction: column; text-align: center; }
  }
  
  .worklog-report .worklog-main-card .card-header {
      background: linear-gradient(135deg, #5317bd 0%, #1e90ff 100%);
      color: #fff;
      border-bottom: none;
      padding: 0.75rem 1rem;
      margin-top: 10px;
  }
  .worklog-report .worklog-main-card .card-header .card-title {
      font-weight: 600;
      letter-spacing: 0.02em;
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid px-2 mt-2 worklog-report">

    <!-- Filters -->
    <div class="filter-bar">
        <div class="filter-group">
            <label class="filter-label">Customer</label>
            <select id="customer_id" class="form-select-custom">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Project</label>
            <select id="customer_project_id" class="form-select-custom">
                <option value="">All Projects</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">From</label>
            <input type="date" id="from" class="form-control-custom" />
        </div>
        <div class="filter-group">
            <label class="filter-label">To</label>
            <input type="date" id="to" class="form-control-custom" />
        </div>
        <div class="filter-group" style="min-width: 140px; flex: 0;">
             <div class="form-check d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" id="groupByUser" checked>
                <label class="form-check-label small fw-bold text-secondary" for="groupByUser">
                    Group by User
                </label>
            </div>
        </div>
        <div class="filter-group" style="flex: 0; min-width: auto; gap: 0.5rem;">
             <button type="button" id="load" class="btn-load">Load</button>
             <button type="button" id="downloadPdf" class="btn-pdf" disabled>PDF</button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div id="worklogSummary" style="display: none;">
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-card-icon icon-blue">
                    <i class="bi bi-people"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Users Involved</div>
                    <div class="summary-card-value" id="totalUsers">0</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-green">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total Time</div>
                    <div class="summary-card-value" id="totalTime">0:00</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-purple">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total Entries</div>
                    <div class="summary-card-value" id="totalEntries">0</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-orange">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Avg Time/User</div>
                    <div class="summary-card-value" id="avgTimePerUser">0:00</div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row g-2 mb-3">
             <div class="col-md-6">
                 <div class="modern-card h-100">
                     <div class="modern-card-header">Time Distribution by User</div>
                     <div class="modern-card-body">
                         <canvas id="userTimeChart" height="250"></canvas>
                     </div>
                 </div>
             </div>
             <div class="col-md-6">
                 <div class="modern-card h-100">
                     <div class="modern-card-header">Time Distribution by Module</div>
                     <div class="modern-card-body">
                         <canvas id="moduleTimeChart" height="250"></canvas>
                     </div>
                 </div>
             </div>
             <div class="col-12 mt-2">
                 <div class="modern-card">
                     <div class="modern-card-header">Daily Worklog Trend</div>
                     <div class="modern-card-body" style="height: 250px;">
                         <canvas id="dailyTrendChart"></canvas>
                     </div>
                 </div>
             </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card" id="reportTableContainer" style="display: none;">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="reportTable">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Date</th>
                            <th>User</th>
                            <th>Customer</th>
                            <th>Project</th>
                            <th>Module</th>
                            <th>Entry Type</th>
                            <th>Hours</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('load').addEventListener('click', fetchData);
    document.getElementById('customer_id').addEventListener('change', loadCustomerProjects);
    document.getElementById('downloadPdf').addEventListener('click', downloadPdf);
});

let userTimeChart = null, moduleTimeChart = null, dailyTrendChart = null;
let latestReportResponse = null;

function loadCustomerProjects(){
    const customerId = document.getElementById('customer_id').value;
    const projSelect = document.getElementById('customer_project_id');
    projSelect.innerHTML = '<option value="">All Customer Projects</option>';
    if(!customerId){ return; }
    $.ajax({
        url: '/customer-project/fetch?customer_id=' + encodeURIComponent(customerId),
        method: 'GET',
        success: function(res){
            const list = res && res.data ? res.data : res;
            if(!list || !list.length) return;
            list.forEach(function(p){
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.project_name || p.name;
                projSelect.appendChild(opt);
            });
        },
        error: function(xhr){ console.error('Failed to load customer projects', xhr.responseText); }
    });
}

function fetchData(){
    const tbody = document.querySelector('#reportTable tbody');
    const tableContainer = document.getElementById('reportTableContainer');
    const summaryDiv = document.getElementById('worklogSummary');
    
    // UI Loading state
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary"></div> Loading...</td></tr>';
    tableContainer.style.display = 'block';
    
    const params = {
        customer_id: document.getElementById('customer_id').value,
        customer_project_id: document.getElementById('customer_project_id').value,
        from: document.getElementById('from').value,
        to: document.getElementById('to').value,
        group_by_user: document.getElementById('groupByUser').checked
    };
    
    $.ajax({
        url: '/reports/worklog/fetch',
        method: 'GET',
        data: params,
        success: function(res){
            latestReportResponse = res;
            tbody.innerHTML = '';
            
            if(res && res.summary && res.data && res.data.length > 0) {
                updateSummary(res.summary);
                summaryDiv.style.display = 'block';
                document.getElementById('downloadPdf').disabled = false;
                createCharts(res.data);
            } else {
                summaryDiv.style.display = 'none';
                tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No entries found.</td></tr>';
                return;
            }
            
            if(document.getElementById('groupByUser').checked && res.grouped_data) {
                displayGroupedData(res.grouped_data, tbody);
            } else {
                res.data.forEach(function(r){
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td>${formatDate(r.work_date)}</td>
                                    <td>${escapeHtml(r.user)}</td>
                                    <td>${escapeHtml((r.customer&&r.customer.name)|| '')}</td>
                                    <td>${escapeHtml(r.customer_project_name || (r.customer_project&&r.customer_project.name) || (r.service&&r.service.name) || '')}</td>
                                    <td>${escapeHtml((r.module&&r.module.name) || '')}</td>
                                    <td>${escapeHtml((r.entry_type&&r.entry_type.name) || '')}</td>
                                    <td class="fw-bold text-dark">${pad(r.hours)}:${pad(r.minutes)}</td>
                                    <td><small class="text-muted">${escapeHtml(r.description || '')}</small></td>`;
                    tbody.appendChild(tr);
                });
            }
        },
        error: function(xhr){
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load data.</td></tr>';
        }
    });
}

function pad(n){ return String(n).padStart(2,'0'); }
function formatDate(d){
    if(!d) return '';
    try{ return (new Date(d)).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}); }catch(e){ return d; }
}
function escapeHtml(s){ if(s===null||s===undefined) return ''; return $('<div>').text(s).html(); }

function updateSummary(summary) {
    document.getElementById('totalUsers').textContent = summary.total_users || 0;
    document.getElementById('totalTime').textContent = `${pad(summary.total_hours || 0)}:${pad(summary.total_minutes || 0)}`;
    document.getElementById('totalEntries').textContent = summary.total_entries || 0;
    
    // Calculate average time per user
    const totalMinutes = ((summary.total_hours || 0) * 60) + (summary.total_minutes || 0);
    const avgMinutes = summary.total_users > 0 ? Math.round(totalMinutes / summary.total_users) : 0;
    const avgHours = Math.floor(avgMinutes / 60);
    const avgMins = avgMinutes % 60;
    
    document.getElementById('avgTimePerUser').textContent = `${pad(avgHours)}:${pad(avgMins)}`;
}

function displayGroupedData(groupedData, tbody) {
    Object.keys(groupedData).forEach(function(userName) {
        const userData = groupedData[userName];
        // User header row
        const userHeaderRow = document.createElement('tr');
        userHeaderRow.className = 'table-info'; 
        userHeaderRow.innerHTML = `<td colspan="8">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark"><i class="fas fa-user me-2"></i> ${escapeHtml(userName)}</span>
                <span class="badge bg-primary bg-opacity-10 text-primary ms-2 border border-primary border-opacity-25">${pad(userData.total_hours)}:${pad(userData.total_minutes)} Total</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">${userData.entries.length} Entries</span>
            </div>
        </td>`;
        tbody.appendChild(userHeaderRow);
        
        userData.entries.forEach(function(entry) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td class="ps-4 border-start border-4 border-light">${formatDate(entry.work_date)}</td>
                            <td class="text-muted small"></td>
                            <td>${escapeHtml((entry.customer&&entry.customer.name)|| '')}</td>
                            <td>${escapeHtml(entry.customer_project_name || (entry.customer_project&&entry.customer_project.name) || (entry.service&&entry.service.name) || '')}</td>
                            <td>${escapeHtml((entry.module&&entry.module.name) || '')}</td>
                            <td>${escapeHtml((entry.entry_type&&entry.entry_type.name) || '')}</td>
                            <td class="fw-bold text-dark">${pad(entry.hours)}:${pad(entry.minutes)}</td>
                            <td><small class="text-muted">${escapeHtml(entry.description || '')}</small></td>`;
            tbody.appendChild(tr);
        });
    });
}

function createCharts(data) {
    if (userTimeChart) userTimeChart.destroy();
    if (moduleTimeChart) moduleTimeChart.destroy();
    if (dailyTrendChart) dailyTrendChart.destroy();
    
    const userTimeData = prepareUserTimeData(data);
    const moduleTimeData = prepareModuleTimeData(data);
    const dailyTrendData = prepareDailyTrendData(data);
    
    const userCtx = document.getElementById('userTimeChart').getContext('2d');
    userTimeChart = new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: userTimeData.labels,
            datasets: [{
                data: userTimeData.data,
                backgroundColor: ['#434afa', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: {family:'Montserrat', size:11} } } },
            layout: { padding: 10 }
        }
    });
    
    const moduleCtx = document.getElementById('moduleTimeChart').getContext('2d');
    moduleTimeChart = new Chart(moduleCtx, {
        type: 'bar',
        data: {
            labels: moduleTimeData.labels,
            datasets: [{
                label: 'Hours',
                data: moduleTimeData.data,
                backgroundColor: '#434afa',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });
    
    const trendCtx = document.getElementById('dailyTrendChart').getContext('2d');
    dailyTrendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: dailyTrendData.labels,
            datasets: [{
                label: 'Daily Hours',
                data: dailyTrendData.data,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                x: { grid: { display: false } }
            }
        }
    });
}

function prepareUserTimeData(data) {
    const userTime = {};
    data.forEach(entry => {
        const user = entry.user || 'Unknown';
        const hours = entry.hours + (entry.minutes / 60);
        userTime[user] = (userTime[user] || 0) + hours;
    });
    return { labels: Object.keys(userTime), data: Object.values(userTime) };
}

function prepareModuleTimeData(data) {
    const moduleTime = {};
    data.forEach(entry => {
        const module = (entry.module && entry.module.name) || 'Unknown';
        const hours = entry.hours + (entry.minutes / 60);
        moduleTime[module] = (moduleTime[module] || 0) + hours;
    });
    return { labels: Object.keys(moduleTime), data: Object.values(moduleTime) };
}

function prepareDailyTrendData(data) {
    const dailyTime = {};
    data.forEach(entry => {
        const date = entry.work_date;
        const hours = entry.hours + (entry.minutes / 60);
        dailyTime[date] = (dailyTime[date] || 0) + hours;
    });
    const sortedDates = Object.keys(dailyTime).sort();
    return {
        labels: sortedDates.map(date => new Date(date).toLocaleDateString('en-US', {month:'short', day:'numeric'})),
        data: sortedDates.map(date => dailyTime[date])
    };
}

// PDF download (client-side) - EXACT ORIGINAL FUNCTION
function downloadPdf(){
    const table = document.getElementById('reportTable');
    const rows = [...table.querySelectorAll('tbody tr')];
    if(rows.length === 0){ return; }
    if(!latestReportResponse){
        alert('Please load report data before downloading the PDF.');
        return;
    }

    const JsPDFCtor = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);
    if(!JsPDFCtor){
        alert('PDF library not loaded. Please check your connection.');
        return;
    }

    const doc = new JsPDFCtor({ unit: 'pt', format: 'a4' });
    const padding = 36;
    const pageWidth = doc.internal.pageSize.getWidth();

    // Header
    doc.setFillColor(25, 118, 210);
    doc.rect(0, 0, pageWidth, 70, 'F');
    doc.setFontSize(16);
    doc.setTextColor(255,255,255);
    doc.text('Worklog Report', padding, 44);

    doc.setFontSize(10);
    doc.setTextColor(60,60,60);
    const customer = document.getElementById('customer_id');
    const customerName = customer.options[customer.selectedIndex]?.text || 'All Customers';
    const proj = document.getElementById('customer_project_id');
    const projName = proj.options[proj.selectedIndex]?.text || 'All Projects';
    const from = document.getElementById('from').value || '-';
    const to = document.getElementById('to').value || '-';

    doc.autoTable({
        startY: 90,
        body: [
            ['Customer', customerName],
            ['Project', projName],
            ['Range', `${from} to ${to}`]
        ],
        theme: 'grid',
        styles: { fontSize: 10, cellPadding: 4 },
        columnStyles: {
            0: { fontStyle: 'bold', halign: 'left', cellWidth: 120 },
            1: { halign: 'left' }
        },
        margin: { left: padding, right: padding },
        head: []
    });

    let currentY = doc.lastAutoTable.finalY + 16;
    doc.setFontSize(11);
    doc.setTextColor(40,40,40);
    const reportSummary = latestReportResponse.summary || null;
    const userSummaryList = getUserSummaryForPdf();

    if(reportSummary){
        const summaryCards = [
            {
                label: 'Total Users',
                value: reportSummary.total_users || 0,
                color: [41, 101, 255]
            },
            {
                label: 'Total Entries',
                value: reportSummary.total_entries || 0,
                color: [16, 185, 129]
            },
            {
                label: 'Total Time',
                value: `${pad(reportSummary.total_hours || 0)}:${pad(reportSummary.total_minutes || 0)}`,
                color: [236, 72, 153]
            }
        ];

        const cardWidth = 160;
        const cardHeight = 60;
        const cardGap = 20;
        let cardX = padding;

        doc.setFontSize(10);

        summaryCards.forEach(card => {
            doc.setFillColor(card.color[0], card.color[1], card.color[2]);
            doc.roundedRect(cardX, currentY, cardWidth, cardHeight, 6, 6, 'F');

            doc.setTextColor(255, 255, 255);
            doc.setFont(undefined, 'bold');
            doc.text(card.label, cardX + 12, currentY + 22);

            doc.setFontSize(16);
            doc.text(String(card.value), cardX + 12, currentY + 40);

            cardX += cardWidth + cardGap;
            doc.setFontSize(10);
        });

        currentY += cardHeight + 20;
        doc.setTextColor(40, 40, 40);
        doc.setFont(undefined, 'normal');
    }

    if(userSummaryList.length){
        doc.autoTable({
            startY: currentY,
            head: [['User', 'Total Time']],
            body: userSummaryList.map(item => [item.name, `${pad(item.hours)}:${pad(item.minutes)}`]),
            theme: 'grid',
            styles: { fontSize: 9, cellPadding: 4 },
            headStyles: { fillColor: [41,101,255], textColor: [255,255,255], halign: 'center' },
            columnStyles: {
                0: { halign: 'left' },
                1: { halign: 'center' }
            },
            margin: { left: padding, right: padding }
        });
        currentY = doc.lastAutoTable.finalY + 18;
    }

    // Build table data from DOM; merge grouped user header rows across all columns
    // Exclude 'User' column in PDF
    const head = [['Date','Customer','Project','Module','Entry Type','Hours','Description']];
    const body = [];
    rows.forEach(tr => {
        if (tr.classList.contains('table-info')) {
            let txt = (tr.querySelector('td')?.textContent || '');
            // Collapse multiple spaces/newlines into a single space to make it a straight line
            txt = txt.replace(/\s+/g, ' ').trim();
            body.push([{ content: txt, colSpan: head[0].length, styles: { fillColor: [232,244,255], fontStyle: 'bold', halign: 'left' } }]);
        } else {
            const cells = [...tr.children].map(td => td.textContent.trim());
            // Remove the 'User' column (index 1 in original table)
            const filtered = [cells[0], cells[2], cells[3], cells[4], cells[5], cells[6], cells[7]];
            body.push(filtered);
        }
    });

    doc.autoTable({
        startY: Math.max(currentY, 110),
        head: head,
        body: body,
        styles: { fontSize: 9, cellPadding: 4 },
        headStyles: { fillColor: [25,118,210], textColor: [255,255,255] },
        theme: 'grid',
        margin: { left: padding, right: padding }
    });

    try {
        const blobUrl = doc.output('bloburl');
        window.open(blobUrl, '_blank');
    } catch(e){
        doc.output('dataurlnewwindow');
    }
}

function getUserSummaryForPdf(){
    if(!latestReportResponse) return [];
    const grouped = latestReportResponse.grouped_data;
    if(grouped && Object.keys(grouped).length){
        return Object.keys(grouped).map(name => {
            const data = grouped[name] || {};
            return {
                name,
                hours: data.total_hours || 0,
                minutes: data.total_minutes || 0
            };
        });
    }
    const accumulator = {};
    (latestReportResponse.data || []).forEach(entry => {
        const userName = entry.user || 'Unknown';
        if(!accumulator[userName]){
            accumulator[userName] = { totalMinutes: 0 };
        }
        accumulator[userName].totalMinutes += ((entry.hours || 0) * 60) + (entry.minutes || 0);
    });
    return Object.keys(accumulator).map(name => {
        const totalMinutes = accumulator[name].totalMinutes;
        return {
            name,
            hours: Math.floor(totalMinutes / 60),
            minutes: totalMinutes % 60
        };
    });
}
</script>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
@endpush
