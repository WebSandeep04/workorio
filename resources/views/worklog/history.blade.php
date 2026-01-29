@extends('layouts.app')

@section('title', 'Worklog History')
@section('page_title', 'Worklog History')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }

  .container-fluid {
    padding: 0.5rem;
  }

  /* Summary Cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.75rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-height: 80px;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
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
  
  .summary-card-icon i {
    font-size: 1.25rem;
    color: white;
  }

  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1;
  }

  .summary-card-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 0.2rem;
    letter-spacing: 0.5px;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.4rem;
    font-weight: 700;
    line-height: 1;
    color: #0f172a;
    font-family: Montserrat;
  }

  /* Table Styles */
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

  .data-table-card .table-scroll {
    width: 100%;
    overflow-x: auto;
    padding: 0.5rem 0.75rem 1rem;
    background: transparent;
  }

  .data-table-card .table-scroll::-webkit-scrollbar {
    height: 8px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-thumb {
    background: #434aFA;
    border-radius: 999px;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1000px;
    background: transparent;
    font-size: 0.85rem;
    table-layout: auto;
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
    border-right: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table thead th:last-child {
    border-right: none;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #0f172a;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    vertical-align: middle;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    transform: translateY(-1px);
    box-shadow: 0px 2px 5px rgba(0,0,0,0.02);
  }

  .badge-custom {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  .badge-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
  .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
  .badge-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
  
  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }
  
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card card-1">
            <div class="summary-card-icon icon-sky">
                <i class="bi bi-list-task"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Entries</div>
                <div class="summary-card-value" id="totalEntries">0</div>
            </div>
        </div>
        <div class="summary-card card-2">
            <div class="summary-card-icon icon-amber">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Pending</div>
                <div class="summary-card-value" id="totalPending">0</div>
            </div>
        </div>
        <div class="summary-card card-3">
             <div class="summary-card-icon icon-emerald">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Approved</div>
                <div class="summary-card-value" id="totalApproved">0</div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="worklogTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Entry Type</th>
                            <th>Customer</th>
                            <th>Project</th>
                            <th>Module</th>
                            <th>Status</th>
                            <th width="30%">Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loaded via JS -->
                        <tr><td colspan="8" class="text-center py-4 text-muted">Loading worklogs...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="table-range-meta" id="worklogRangeInfo">
        Showing 0-0 of 0 entries
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-2">
        <ul class="pagination" id="pagination"></ul>
    </div>
    
    <!-- No Data Message -->
    <div id="noDataMessage" class="text-center py-5 d-none">
        <div class="mb-3"><i class="bi bi-clipboard-x text-muted" style="font-size: 2rem;"></i></div>
        <h6 class="text-muted">No worklog entries found</h6>
        <p class="text-muted small">Start logging your work to see your history here.</p>
        <a href="{{ route('worklog') }}" class="btn btn-sm btn-primary" style="background:#434afa; border:none; border-radius:0;">
            <i class="bi bi-plus-lg me-1"></i> Add Entry
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    let colorClass = type === 'success' ? 'alert-success' : 'alert-danger';
    $('#alertBox').html(`
        <div class="alert ${colorClass} alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:0;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    setTimeout(() => $('.alert').fadeOut(500, function() { $(this).remove(); }), 3000);
}

$(function () {
    loadWorklogs();
    loadStats();
});

function loadWorklogs(page = 1) {
    $.get("{{ route('worklog-history.fetch') }}", { page: page }, function (response) {
        if (!response.data || response.data.length === 0) {
            $('.data-table-card').hide();
            $('#pagination').hide();
            $('#worklogRangeInfo').hide();
            $('#noDataMessage').removeClass('d-none');
        } else {
            $('.data-table-card').show();
            $('#pagination').show();
            $('#worklogRangeInfo').show();
            $('#noDataMessage').addClass('d-none');
            
            let rows = '';
            $.each(response.data, function (i, worklog) {
                // Status text (no badge)
                let statusText = (worklog.status || '').toUpperCase();
                
                // Delete button with background
                const deleteButton = worklog.status === 'pending' ? 
                    `<button class="btn btn-sm text-white p-1 px-2 rounded-0 deleteBtn" data-id="${worklog.id}" title="Delete" style="background:#434afa; border:none;"><i class="bi bi-trash"></i></button>` : '';

                rows += `<tr>
                    <td>${new Date(worklog.work_date).toLocaleDateString()}</td>
                    <td>${worklog.entry_type ? worklog.entry_type.name : '-'}</td>
                    <td>${worklog.customer ? worklog.customer.name : '-'}</td>
                    <td>${worklog.service ? worklog.service.name : '-'}</td>
                    <td>${worklog.module ? worklog.module.name : '-'}</td>
                    <td>${statusText}</td>
                    <td><div class="text-wrap" style="max-width: 300px; font-size:0.8rem; color:#475569;">${worklog.description || '-'}</div></td>
                    <td class="text-center">${deleteButton}</td>
                </tr>`;
            });
            $('#worklogTable tbody').html(rows);
            
            // Stats Range
            const from = response.pagination.from || 0;
            const to = response.pagination.to || 0;
            const total = response.pagination.total || 0;
            $('#worklogRangeInfo').text(`Showing ${from}-${to} of ${total} entries`);

            // Pagination (Simple)
            if (response.pagination.last_page > 1) {
                buildPagination($('#pagination'), response.pagination.current_page, response.pagination.last_page);
            } else {
                $('#pagination').empty();
            }
        }
    }).fail(function () {
        showAlert('error', 'Error loading worklog history.');
    });
}

// Simple Pagination (Prev | X/Y | Next)
function buildPagination($container, current, last) {
    $container.empty();
    let html = '';
    
    // Previous
    html += `<li class="page-item ${current <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadWorklogs(${current - 1})" tabindex="-1" aria-disabled="true">Previous</a>
    </li>`;

    // Active X/Y
    html += `<li class="page-item active">
        <span class="page-link">${current} / ${last}</span>
    </li>`;

    // Next
    html += `<li class="page-item ${current >= last ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadWorklogs(${current + 1})">Next</a>
    </li>`;
    
    $container.html(html);
}

function loadStats() {
    $.get("{{ route('worklog-history.stats') }}", function (data) {
        $('#totalEntries').text(data.total_entries || 0);
        $('#totalPending').text(data.total_pending || 0);
        $('#totalApproved').text(data.total_approved || 0);
    }).fail(function () {
        console.error('Failed to load stats');
    });
}

$(document).on('click', '.deleteBtn', function () {
    const worklogId = $(this).data('id');
    if (confirm('Are you sure you want to delete this worklog entry?')) {
        $.ajax({
            url: `/worklog-history/${worklogId}`,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (response) {
                if (response.success) {
                    loadWorklogs();
                    loadStats();
                    showAlert('success', 'Deleted successfully.');
                }
            },
            error: function () {
                showAlert('error', 'Error deleting worklog entry.');
            }
        });
    }
});
</script>
@endpush
