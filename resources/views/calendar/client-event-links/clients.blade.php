@extends('layouts.app')

@section('title', 'Client-Event Links')
@section('page_title', 'Client-Event Links')

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
  
  .btn-sm-manage {
      background-color: #434afa;
      border: none;
      color: white;
      padding: 0.35rem 1rem;
      border-radius: 4px;
      font-size: 0.8rem;
      text-decoration: none;
      display: inline-block;
  }
  .btn-sm-manage:hover { background-color: #3538d4; color: white; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-people"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Clients</div>
                <div class="summary-card-value" id="totalClientsCard">0</div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="clientSearch" placeholder="Search clients..." />
        </div>

    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="clientsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="clientsTbody">
                        <tr><td colspan="3" class="text-center py-4 text-muted">Loading...</td></tr>
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
@endsection

@push('scripts')
<script>
(function(){
    let allClients = [];
    let filteredClients = [];
    let currentPage = 1;
    let itemsPerPage = 15;

    window.loadClients = function(){
        $('#clientsTbody').html('<tr><td colspan="3" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</td></tr>');
        
        $.get("{{ route('calendar-client-event.clients') }}").done(function(rows){
            allClients = rows || [];
            filteredClients = [...allClients];
            $('#totalClientsCard').text(allClients.length);
            currentPage = 1;
            renderTable();
        }).fail(function(){ 
            $('#clientsTbody').html('<tr><td colspan="3" class="text-center py-4 text-danger">Failed to load data</td></tr>'); 
        });
    }

    $('#clientSearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        filteredClients = allClients.filter(c => c.name.toLowerCase().includes(query));
        currentPage = 1;
        renderTable();
    });

    window.renderTable = function() {
        const total = filteredClients.length;
        const start = (currentPage - 1) * itemsPerPage;
        const end = Math.min(start + itemsPerPage, total);
        const pageData = filteredClients.slice(start, end);
        
        let html = '';
        if (pageData.length === 0) {
            html = '<tr><td colspan="3" class="text-center py-4 text-muted">No clients found</td></tr>';
        } else {
            pageData.forEach(function(c){
                const activeStatus = c.is_active 
                    ? '<span class="text-success fw-bold">Active</span>' 
                    : '<span class="text-secondary">Inactive</span>';
                
                html += `<tr>
                    <td class="fw-bold text-dark">${c.name}</td>
                    <td>${activeStatus}</td>
                    <td class="text-center">
                        <a class="btn-sm-manage shadow-sm" href="/calendar/client-event-links/${c.id}"><i class="bi bi-gear-fill me-1"></i> Manage</a>
                    </td>
                </tr>`;
            });
        }
        
        $('#clientsTbody').html(html);
        $('#rangeInfo').text(`Showing ${total > 0 ? start + 1 : 0}-${end} of ${total} entries`);
        renderPagination(total);
    }

    function renderPagination(total) {
        const totalPages = Math.ceil(total / itemsPerPage);
        let pagHtml = '';
        if (totalPages > 1) {
             if (currentPage > 1) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage-1})">Prev</a></li>`;
             
             if (totalPages <= 7) {
                 for(let i=1; i<=totalPages; i++) pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
             } else {
                 pagHtml += `<li class="page-item ${1===currentPage?'active':''}"><a class="page-link" onclick="changePage(1)">1</a></li>`;
                 if(currentPage > 3) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                 
                 let s = Math.max(2, currentPage - 1);
                 let e = Math.min(totalPages - 1, currentPage + 1);
                 for(let i=s; i<=e; i++) pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
                 
                 if(currentPage < totalPages - 2) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                 pagHtml += `<li class="page-item ${totalPages===currentPage?'active':''}"><a class="page-link" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
             }

             if (currentPage < totalPages) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage+1})">Next</a></li>`;
        }
        $('#pagination').html(pagHtml);
    }

    window.changePage = function(p) { currentPage = p; renderTable(); }

    $(document).ready(loadClients);
})();
</script>
@endpush
