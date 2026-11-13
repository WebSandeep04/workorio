@extends('layouts.app')

@section('title', $title)
@section('page_title', $title)

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Summary Cards matching main screen */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.6rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
    font-size: 1.25rem;
    color: white;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #000;
    font-family: Montserrat;
  }

  .metric-arrow {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    position: absolute;
    right: 6px;
    bottom: 6px;
    font-size: 0.8rem;
  }

  .metric-arrow:hover {
    background: #5b59f7;
    color: #fff;
  }

  /* Filter Box */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern option {
    color: #000;
    background: #fff;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  /* Table search & buttons */
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

  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }

  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434afa;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    color: white;
  }

  /* Table card matching main screen */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 18px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
  }

  .data-table-card .table-responsive::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }

  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }

  .data-table-card .table-responsive {
    scrollbar-color: #434AFA #e4e7ec;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    font-family: Montserrat;
    white-space: nowrap;
  }

  .data-table-card .custom-table thead th,
  .data-table-card .custom-table tbody td {
    white-space: nowrap;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }

  .page-nav-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
  }

  .page-nav-header a {
    text-decoration: none;
    font-weight: 700;
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }

  .spin {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <!-- Summary Cards matching main screen -->
  <div class="summary-cards">
      <div class="summary-card card-4" style="border: 2px solid #8b5cf6;">
        <div class="summary-card-icon icon-violet">
          <i class="bi bi-wallet2 fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Opening Balance</div>
          <div class="summary-card-value text-dark">₹<span id="stat_opening_balance">{{ number_format($totalAmount, 2) }}</span></div>
        </div>
      </div>
      <a href="{{ route('petty-cash.department.expenses') }}" class="summary-card card-5 text-decoration-none" title="Click to view Expenses History">
        <div class="summary-card-icon icon-rose">
          <i class="bi bi-cash-stack fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Expense</div>
          <div class="summary-card-value text-danger">₹<span id="stat_all_expense">{{ number_format($totalExpense, 2) }}</span></div>
        </div>
        <div class="metric-arrow" title="View Expenses History">
            <i class="bi bi-arrow-right"></i>
        </div>
      </a>
      <div class="summary-card card-1">
        <div class="summary-card-icon icon-sunrise">
          <i class="bi bi-piggy-bank fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Remaining Op-Expense</div>
          <div class="summary-card-value text-primary">₹<span id="stat_remaining_balance">{{ number_format($remainingBalance, 2) }}</span></div>
        </div>
      </div>
  </div>

  <!-- Filters - Blue Filter Box matching main screen -->
  <div class="filterBox mb-3">
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-building"></i> Department</label>
        <select class="form-control-modern" id="filter_department">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" {{ (string)$selectedDeptId === (string)$dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> From Date</label>
        <input type="date" class="form-control-modern" id="filter_from_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> To Date</label>
        <input type="date" class="form-control-modern" id="filter_to_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-month"></i> Month</label>
        <select class="form-control-modern" id="filter_month">
            <option value="">All Months</option>
            @for($m = 1; $m <= 12; $m++)
              <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
            @endfor
        </select>
    </div>
  </div>

  <!-- Search & Action Row matching main screen -->
  <div class="table-search mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="filter_search" placeholder="Search opening balances..." />
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="table-search-btn" id="btnResetFilters" style="background: #434afa;">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
    </div>
  </div>

  <!-- Table Card matching main screen structure -->
  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="obTable">
          <thead>
            <tr>
              <th style="width: 70px;">#</th>
              <th>Date & Time</th>
              <th>Department</th>
              <th class="text-end">Amount (₹)</th>
            </tr>
          </thead>
          <tbody id="obTableBody">
            @forelse($balances as $index => $item)
            <tr>
              <td class="text-muted fw-bold">{{ $index + 1 }}</td>
              <td>{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '-' }}</td>
              <td>{{ $item->department ? $item->department->name : '-' }}</td>
              <td class="text-end fw-bold text-dark">₹{{ number_format($item->amount, 2) }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">No opening balance records found.</td>
            </tr>
            @endforelse
          </tbody>
          <tfoot id="obTableFoot">
            <tr style="background: #f8fafc; font-weight: 700;">
              <td colspan="3" class="text-end">Total:</td>
              <td class="text-end" id="obFootTotal" style="color: #434AFA; font-size: 1rem;">
                ₹{{ number_format($totalAmount, 2) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 d-flex justify-content-center">
    <div id="paginationLinks">
      {{ $balances->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimer;

    // Delegate pagination link clicks inside paginationLinks
    $(document).on('click', '#paginationLinks a', function(e) {
        e.preventDefault();
        let href = $(this).attr('href');
        if (href && href !== '#' && href.indexOf('page=') !== -1) {
            let page = href.split('page=')[1].split('&')[0];
            loadBalancesAjax(page);
        }
    });

    function loadBalancesAjax(page = 1) {
        let deptId = $('#filter_department').val();
        let fromDate = $('#filter_from_date').val();
        let toDate = $('#filter_to_date').val();
        let month = $('#filter_month').val();
        let search = $('#filter_search').val();

        let deptText = $('#filter_department option:selected').text().trim();
        if (deptId) {
            $('#pageTitleText').text(deptText + ' - Opening Balance History');
        } else {
            $('#pageTitleText').text('All Departments - Opening Balance History');
        }

        $('#obTableBody').html(`
            <tr>
                <td colspan="4" class="text-center py-4 text-muted">
                    <i class="bi bi-arrow-repeat spin me-2 text-primary"></i> Loading data...
                </td>
            </tr>
        `);

        $.ajax({
            url: "{{ route('petty-cash.department.opening-balances') }}",
            type: "GET",
            data: {
                page: page,
                department_id: deptId,
                from_date: fromDate,
                to_date: toDate,
                month: month,
                search: search
            },
            success: function(res) {
                if (res.success) {
                    let totalFormatted = Number(res.total_amount).toLocaleString('en-IN', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    });
                    let expFormatted = Number(res.total_expense).toLocaleString('en-IN', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    });
                    let remFormatted = Number(res.remaining_balance).toLocaleString('en-IN', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    });

                    $('#stat_opening_balance').text(totalFormatted);
                    $('#stat_all_expense').text(expFormatted);
                    $('#stat_remaining_balance').text(remFormatted);
                    $('#obFootTotal').text('₹' + totalFormatted);

                    if (res.data && res.data.length > 0) {
                        $('#obTableFoot').show();
                        let rows = '';
                        let startIdx = ((res.current_page || 1) - 1) * (res.per_page || 15);
                        res.data.forEach((item, index) => {
                            let dateObj = item.created_at ? new Date(item.created_at) : null;
                            let dateFormatted = dateObj ? dateObj.toLocaleString('en-GB', {
                                day: '2-digit', month: 'short', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', hour12: true
                            }) : '-';
                            let deptName = item.department ? item.department.name : '-';
                            let amt = Number(item.amount).toLocaleString('en-IN', {
                                minimumFractionDigits: 2, maximumFractionDigits: 2
                            });

                            rows += `
                                <tr>
                                    <td class="text-muted fw-bold">${startIdx + index + 1}</td>
                                    <td>${dateFormatted}</td>
                                    <td>${deptName}</td>
                                    <td class="text-end fw-bold text-dark">₹${amt}</td>
                                </tr>
                            `;
                        });
                        $('#obTableBody').html(rows);

                        // Render Pagination
                        let links = '';
                        if (res.links) {
                            res.links.forEach(link => {
                                let activeClass = link.active ? 'active' : '';
                                let disabledClass = link.url ? '' : 'disabled';
                                let label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                                if (link.url) {
                                    links += `<li class="page-item ${activeClass}"><a class="page-link" href="javascript:void(0)" onclick="loadBalancesAjax(${link.url.split('page=')[1]})">${label}</a></li>`;
                                } else {
                                    links += `<li class="page-item ${disabledClass}"><span class="page-link">${label}</span></li>`;
                                }
                            });
                            $('#paginationLinks').html(`<ul class="pagination pagination-sm">${links}</ul>`);
                        } else {
                            $('#paginationLinks').html('');
                        }
                    } else {
                        $('#obTableFoot').hide();
                        $('#paginationLinks').html('');
                        $('#obTableBody').html(`
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No opening balance records found.</td>
                            </tr>
                        `);
                    }
                }
            },
            error: function() {
                $('#obTableBody').html(`
                    <tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i> Failed to load data.
                        </td>
                    </tr>
                `);
            }
        });
    }

    $('#filter_department, #filter_from_date, #filter_to_date, #filter_month').on('change', function() {
        loadBalancesAjax();
    });

    $('#filter_search').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadBalancesAjax();
        }, 400);
    });

    $('#btnResetFilters').on('click', function() {
        $('#filter_department').val('');
        $('#filter_from_date').val('');
        $('#filter_to_date').val('');
        $('#filter_month').val('');
        $('#filter_search').val('');
        loadBalancesAjax();
    });
    window.loadBalancesAjax = loadBalancesAjax;
});
</script>
@endpush
