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
  .icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
  .summary-card-content { flex-grow: 1; }
  .summary-card-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #64748b; font-family: Montserrat; }
  .summary-card-value { font-size: 1.1rem; font-weight: 700; line-height: 1; color: #0f172a; font-family: Montserrat; }

  /* Table */
  .modern-card { padding: 0; margin-bottom: 0.75rem; border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 4px 6px rgba(0,0,0,0.02); overflow: hidden; }
  .modern-card-body { padding: 0; }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; background: transparent; }
  
  .table-scroll::-webkit-scrollbar { height: 8px; }
  .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .table-scroll::-webkit-scrollbar-thumb { background: #434aFA; border-radius: 999px; }

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

  /* Mobile Responsiveness */
  @media (max-width: 768px) {
      .filter-bar { padding: 1rem; gap: 1rem; flex-direction: column; align-items: stretch; }
      .filter-group { width: 100%; min-width: 100%; display: block; }
      .filter-label { display: block; margin-bottom: 0.25rem; }
      .btn-load, .btn-pdf { width: 100%; margin-top: 0.5rem; text-align: center; }
      .summary-cards { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid px-2 mt-2 worklog-report">
    
    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <label class="filter-label">User</label>
            <select id="user_id" class="form-select-custom">
                <option value="">Select User</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
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
        <div class="filter-group">
            <label class="filter-label">Customer</label>
            <select id="customer_id" class="form-select-custom">
                <option value="">All Customers</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Project</label>
            <select id="customer_project_id" class="form-select-custom">
                <option value="">All Customer Projects</option>
            </select>
        </div>
        <div class="filter-group" style="flex: 0; min-width: auto; gap: 0.5rem;">
             <button type="button" id="load" class="btn-load">Load</button>
             <button type="button" id="downloadPdf" class="btn-pdf" disabled>PDF</button>
        </div>
    </div>

    <!-- Summary Section -->
    <div id="userSummary" class="mb-3" style="display:none;">
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-card-icon icon-purple">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Entries</div>
                    <div class="summary-card-value" id="sumEntries">0</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-card-icon icon-green">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="summary-card-content">
                    <div class="summary-card-label">Total Time</div>
                    <div class="summary-card-value" id="sumTime">0:00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="modern-card" id="reportTableContainer" style="display:none;">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Date</th>
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
    document.getElementById('load').addEventListener('click', fetchUserReport);
    document.getElementById('downloadPdf').addEventListener('click', downloadPdf);
    document.getElementById('customer_id').addEventListener('change', loadCustomerProjects);
    document.getElementById('user_id').addEventListener('change', loadCustomersForUser);
    loadCustomersForUser();
});

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

function loadCustomersForUser(){
    const userId = document.getElementById('user_id').value;
    const customerSelect = document.getElementById('customer_id');
    customerSelect.innerHTML = '<option value="">All Customers</option>';
    document.getElementById('customer_project_id').innerHTML = '<option value="">All Customer Projects</option>';
    if(!userId){ return; }
    $.ajax({
        url: '/reports/user-worklog/customers?user_id=' + encodeURIComponent(userId),
        method: 'GET',
        success: function(res){
            const list = res && res.data ? res.data : res;
            if(!list || !list.length) return;
            list.forEach(function(c){
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                customerSelect.appendChild(opt);
            });
        },
        error: function(xhr){ console.error('Failed to load customers for user', xhr.responseText); }
    });
}

function fetchUserReport(){
    const userId = document.getElementById('user_id').value;
    const from = document.getElementById('from').value;
    const to = document.getElementById('to').value;
    if(!userId){
        alert('Please select a user');
        return;
    }
    $.ajax({
        url: '/reports/user-worklog/fetch',
        method: 'GET',
        data: { user_id: userId, from: from, to: to, customer_id: document.getElementById('customer_id').value, customer_project_id: document.getElementById('customer_project_id').value },
        success: function(res){
            const tbody = document.querySelector('#reportTable tbody');
            tbody.innerHTML='';
            
            if(!res || !res.data || res.data.length===0){
                document.getElementById('reportTableContainer').style.display='none';
                document.getElementById('userSummary').style.display='none';
                document.getElementById('downloadPdf').disabled = true;
                return;
            }
            res.data.forEach(function(r){
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${formatDate(r.date)}</td>
                                <td>${escapeHtml(r.customer||'')}</td>
                                <td>${escapeHtml(r.project||'')}</td>
                                <td>${escapeHtml(r.module||'')}</td>
                                <td>${escapeHtml(r.entry_type||'')}</td>
                                <td>${pad(r.hours)}:${pad(r.minutes)}</td>
                                <td>${escapeHtml(r.description||'')}</td>`;
                tbody.appendChild(tr);
            });
            document.getElementById('reportTableContainer').style.display='block';
            document.getElementById('userSummary').style.display='block';
            document.getElementById('sumEntries').textContent = res.summary.total_entries;
            document.getElementById('sumTime').textContent = `${pad(res.summary.total_hours)}:${pad(res.summary.total_minutes)}`;
            document.getElementById('downloadPdf').disabled = false;
        },
        error: function(){ alert('Failed to load user report'); }
    });
}

function formatDate(d){ if(!d) return ''; try{ return (new Date(d)).toLocaleDateString(); }catch(e){ return d; } }
function escapeHtml(s){ if(s===null||s===undefined) return ''; return $('<div>').text(s).html(); }
function pad(n){ return String(n).padStart(2,'0'); }

// PDF download function - kept exactly as intended, referencing DOM elements correctly
// The summary logic in PDF function references hardcoded IDs like "sumEntries" which we preserved above.
function downloadPdf(){
    const table = document.getElementById('reportTable');
    const rows = [...table.querySelectorAll('tbody tr')];
    if(rows.length === 0){ return; }

    const JsPDFCtor = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : (window.jsPDF || null);
    if(!JsPDFCtor){ alert('PDF library not loaded.'); return; }
    const doc = new JsPDFCtor({ unit: 'pt', format: 'a4' });

    const padding = 36;
    const pageWidth = doc.internal.pageSize.getWidth();

    // Header
    doc.setFillColor(25,118,210);
    doc.rect(0,0,pageWidth,70,'F');
    doc.setFontSize(16);
    doc.setTextColor(255,255,255);
    doc.text('User-wise Worklog Report', padding, 44);

    doc.setFontSize(10);
    doc.setTextColor(60,60,60);
    const userSel = document.getElementById('user_id');
    const userName = userSel.options[userSel.selectedIndex]?.text || '';
    const from = document.getElementById('from').value || '-';
    const to = document.getElementById('to').value || '-';
    doc.text(`User: ${userName}`, padding, 88);
    doc.text(`Range: ${from} to ${to}`, padding + 300, 88);

    // Build table
    const head = [['Date','Customer','Project','Module','Entry Type','Hours','Description']];
    const body = rows.map(tr => [...tr.children].map(td => td.textContent.trim()));

    doc.autoTable({
        startY: 110,
        head: head,
        body: body,
        styles: { fontSize: 9, cellPadding: 4 },
        headStyles: { fillColor: [25,118,210], textColor: [255,255,255] },
        theme: 'grid',
        margin: { left: padding, right: padding }
    });

    try{ const url = doc.output('bloburl'); window.open(url, '_blank'); }catch(e){ doc.output('dataurlnewwindow'); }
}
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
@endpush
