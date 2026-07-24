@extends('layouts.app')

@section('title', 'Payroll Details')
@section('page_title', 'Payroll Details - ' . date('F', mktime(0, 0, 0, $payroll->month, 1)) . ' ' . $payroll->year)

@push('styles')
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .modern-card-body { padding: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-responsive { border-radius: 5px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; scrollbar-color: #434AFA #e4e7ec; }
  .data-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-responsive::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb { background: #434AFA; border-radius: 999px; }
  .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }
  .btn-action { display: inline-flex; align-items: center; justify-content: center; background: transparent !important; border: none !important; padding: 0.35rem 0.6rem; color: #6c757d; transition: all 0.2s ease; cursor: pointer; text-decoration: none; }
  .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
  .btn-action-download { color: white !important; background: #10B981 !important; border-radius: 4px; }
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }
  .loading-state, .empty-state { text-align: center; padding: 1rem; color: #667eea; font-size: 10px; }
  .empty-state { color: #6c757d; }
  .loading-state i, .empty-state i { font-size: 1.5rem; margin-bottom: 0.5rem; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .spin { animation: spin 1s linear infinite; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="mb-3 d-flex align-items-center justify-content-between">
    <div>
        <a href="{{ route('payroll.process.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
        <span class="ms-2 fw-bold">Batch ID: #{{ $payroll->id }} | Status: {{ $payroll->status }}</span>
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search employee..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="detailsTable">
          <thead>
            <tr>
              <th>Employee Name</th>
              <th>Employee Code</th>
              <th>Gross Salary</th>
              <th>Net Salary</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="detailsTableBody">
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading details...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="detailsRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>
@endsection

@push('scripts')
<script>
function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#detailsRangeInfo');
    if (!$info.length) return;
    const safeTotal = Number.isFinite(Number(total)) ? Number(total) : 0;
    const safeStart = safeTotal === 0 ? 0 : Number(from);
    const safeEnd = safeTotal === 0 ? 0 : Number(to);
    $info.text(`Showing ${safeStart.toLocaleString('en-IN')}-${safeEnd.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

function escapeHtml(text = '') {
  return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/\'/g,'&#039;');
}

$(function () {
  const baseUrl = "{{ route('payroll.process.show', $payroll->id) }}";
  let searchTimeout;
  
  loadDetails();

  function loadDetails(page = 1) {
    let search = $('#search').val();
    
    $('#detailsTableBody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading details...</p>
        </td>
      </tr>
    `);
    
    $.ajax({
      url: baseUrl,
      type: 'GET',
      data: { page: page, search: search },
      success: function (data) {
        if (!data.data || data.data.length === 0) {
          $('#detailsTableBody').html(`
            <tr>
              <td colspan="5" class="empty-state">
                <i class="bi bi-person-x"></i>
                <h5>No Records Found</h5>
              </td>
            </tr>
          `);
          $('#paginationLinks').empty();
          updateRangeInfo(0, 0, 0);
          return;
        }
        
        let rows = '';
        $.each(data.data, function (i, row) {
          const emp = row.employee || {};
          const downloadUrl = `/payroll/payslip/${row.id}/download`;

          rows += `
            <tr style="animation-delay: ${i * 0.05}s;">
              <td><strong>${escapeHtml(emp.name)}</strong></td>
              <td>${escapeHtml(emp.employee_code)}</td>
              <td>₹${parseFloat(row.gross_salary).toFixed(2)}</td>
              <td><strong class="text-success">₹${parseFloat(row.net_salary).toFixed(2)}</strong></td>
              <td>
                <div class="d-flex gap-2">
                  <a href="${downloadUrl}" class="btn-action btn-action-download" target="_blank" title="Download PDF">
                      <i class="bi bi-file-earmark-pdf"></i>
                  </a>
                </div>
              </td>
            </tr>
          `;
        });
        $('#detailsTableBody').html(rows);
        buildSimplePagination($('#paginationLinks'), data.current_page || 1, data.last_page || 1);
        updateRangeInfo(data.from, data.to, data.total);
      },
      error: function() {
        $('#detailsTableBody').html(`
          <tr>
            <td colspan="5" class="text-danger text-center py-4">
              <i class="bi bi-exclamation-triangle"></i> Failed to load records.
            </td>
          </tr>
        `);
      }
    });
  }

  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) loadDetails(page);
  });
  
  $('#search').on('keyup', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => loadDetails(1), 300);
  });
});
</script>
@endpush
