@extends('layouts.app')

@section('title', 'Missing Worklog Summary')
@section('page_title', 'Missing Worklog Summary')

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
  
  .icon-red { background: linear-gradient(135deg, #ef4444, #f87171); }
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }

  .summary-card-content { flex-grow: 1; }
  .summary-card-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; color: #64748b; font-family: Montserrat; }
  .summary-card-value { font-size: 1.4rem; font-weight: 700; line-height: 1; color: #0f172a; font-family: Montserrat; }

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

  .btn-refresh {
    padding: 0.4rem 1rem;
    background: #434afa;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s;
  }
  .btn-refresh:hover { background: #3538d4; color: white; transform: translateY(-1px); }

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
    color: #667eea; border: 2px solid #e0e0e0; border-radius: 6px;
    padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-weight: 500; cursor: pointer;
  }
  .pagination .page-item.active .page-link {
    background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }
  
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }
  
  /* Modals */
  .modal-content { border-radius: 0; border: none; }
  .modal-header { border-radius: 0; background-color: #434afa !important; color: white; }
  .modal-title { font-weight: 600; font-size: 1rem; }
  .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
  .btn { border-radius: 0; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-calendar-x"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Dates with Missing Entries</div>
                <div class="summary-card-value" id="cardTotalDates">0</div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="summarySearch" placeholder="Search date or users..." />
        </div>

    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="summaryTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Missing Count</th>
                            <th width="50%">Missing Users (Preview)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <tr><td colspan="4" class="text-center py-4 text-muted">Loading summary...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="table-range-meta" id="rangeInfo">
        Showing 0-0 of 0 entries
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-2">
        <ul class="pagination" id="pagination"></ul>
    </div>
</div>

<!-- Missing Users Modal -->
<div class="modal fade" id="missingUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Missing Users for <span id="modalDate"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="modalUsersList" class="list-group list-group-flush"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allData = [];
let filteredData = [];
let currentPage = 1;
let itemsPerPage = 15;

$(document).ready(function() {
    loadSummary();
    
    $('#summarySearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        filteredData = allData.filter(item => {
            const dateStr = new Date(item.date).toLocaleDateString().toLowerCase();
            const usersStr = item.missing_users.map(u => u.name).join(' ').toLowerCase();
            return dateStr.includes(query) || usersStr.includes(query);
        });
        currentPage = 1;
        renderTable();
    });
});

function loadSummary() {
    $('#summaryTableBody').html('<tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>');
    
    $.ajax({
        url: '{{ route("worklog.missing-summary") }}',
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.summary) {
                allData = response.summary;
                $('#cardTotalDates').text(response.total_missing_dates || 0);
            } else {
                allData = [];
                $('#cardTotalDates').text(0);
            }
            filteredData = [...allData];
            currentPage = 1;
            renderTable();
        },
        error: function(xhr) {
            $('#summaryTableBody').html('<tr><td colspan="4" class="text-center py-4 text-danger">Error loading summary.</td></tr>');
        }
    });
}

function renderTable() {
    const total = filteredData.length;
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    const pageData = filteredData.slice(start, end);
    
    let html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">No missing entries found.</td></tr>';
    } else {
        pageData.forEach(item => {
            const dateObj = new Date(item.date);
            const dateStr = dateObj.toLocaleDateString('en-US', { weekday:'short', year:'numeric', month:'short', day:'numeric' });
            // Preview 3 users
            let names = item.missing_users.map(u => u.name).slice(0, 3).join(', ');
            if(item.missing_users.length > 3) names += `, +${item.missing_users.length - 3} more`;
            
            // Clean JSON for onclick
            const usersJson = JSON.stringify(item.missing_users).replace(/"/g, '&quot;');
            
            html += `<tr>
                <td>${dateStr}</td>
                <td>${item.count}</td>
                <td class="text-muted small">${names}</td>
                <td class="text-center">
                    <button class="btn btn-sm text-white shadow-sm" style="background-color: #434afa; border:none; padding: 0.25rem 0.75rem; border-radius: 4px;" onclick="showMissingUsers('${item.date}', ${usersJson})">
                        <i class="bi bi-eye"></i> View
                    </button>
                </td>
            </tr>`;
        });
    }
    
    $('#summaryTableBody').html(html);
    $('#rangeInfo').text(`Showing ${total > 0 ? start + 1 : 0}-${end} of ${total} entries`);
    
    // Pagination (Simple Numbered)
    const totalPages = Math.ceil(total / itemsPerPage);
    let pagHtml = '';
    if (totalPages > 1) {
        if (currentPage > 1) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage-1})">Prev</a></li>`;
        
         if (totalPages <= 10) {
             for(let i=1; i<=totalPages; i++) pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
         } else {
             pagHtml += `<li class="page-item ${1===currentPage?'active':''}"><a class="page-link" onclick="changePage(1)">1</a></li>`;
             if(currentPage > 3) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             let s = Math.max(2, currentPage - 1), e = Math.min(totalPages - 1, currentPage + 1);
             for(let i=s; i<=e; i++) pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
             if(currentPage < totalPages - 2) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             pagHtml += `<li class="page-item ${totalPages===currentPage?'active':''}"><a class="page-link" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
         }

        if (currentPage < totalPages) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage+1})">Next</a></li>`;
    }
    $('#pagination').html(pagHtml);
}

function changePage(p) { currentPage = p; renderTable(); }

function showMissingUsers(date, users) {
    const dateStr = new Date(date).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
    $('#modalDate').text(dateStr);
    
    let usersHtml = '';
    users.forEach(u => {
        usersHtml += `
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; color: #434afa;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">${u.name}</h6>
                        <small class="text-muted">${u.email || 'No Email'}</small>
                    </div>
                </div>
                <span class="badge bg-danger rounded-pill">Missing</span>
            </div>
        `;
    });
    
    $('#modalUsersList').html(usersHtml);
    const modal = new bootstrap.Modal(document.getElementById('missingUsersModal'));
    modal.show();
}
</script>
@endpush
