@extends('layouts.app')

@section('title', $title)
@section('page_title', $title)

@section('content')
<div class="container-fluid calling-status-page px-2">
    <div class="dashboard-header mb-3">
        <h4 class="fw-bold" style="font-family: Montserrat; color: #101828;">{{ $title }}</h4>
        <p class="text-muted small">Viewing records for your calling activities</p>
    </div>

    <!-- Stats Card -->
    <div class="summary-cards mb-3">
        <div class="summary-card card-1" style="max-width: 250px;">
            <div class="summary-card-icon" style="background: linear-gradient(135deg, #434AFA, #667eea);">
                <i class="bi bi-person-lines-fill text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Records</div>
                <div class="summary-card-value" id="totalCountCard">0</div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table" id="statusTable">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Campaign</th>
                            <th>Company Name</th>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Legal Status</th>
                            <th>Phone</th>
                            <th>GST</th>
                            <th>Turnover</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Next Followup</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="10" class="text-center p-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2 text-muted">Loading records...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="table-range-meta" id="pageSummary">
            Showing 0 to 0 of 0 entries
        </div>
        <ul class="pagination mb-0" id="paginationLinks"></ul>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calling-status-page { background: #f8f9fc; }
    .data-table-card { border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
    .custom-table thead th { 
        background: #fff; 
        color: #64748b; 
        font-weight: 700; 
        /* text-transform: uppercase; removed */ 
        font-size: 11px; 
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-family: Montserrat;
    }
    .custom-table tbody td { 
        padding: 15px 20px; 
        font-size: 13px; 
        color: #1e293b;
        border-bottom: 1px solid #f8fafc;
        font-family: Montserrat;
        vertical-align: middle;
    }
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        background: #e0e7ff;
        color: #434AFA;
    }
    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .summary-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .summary-card-label { font-size: 11px; color: #64748b; font-weight: 600; /* text-transform: uppercase; removed */ }
    .summary-card-value { font-size: 24px; font-weight: 700; color: #1e293b; }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    loadTableData(1);
});

function loadTableData(page) {
    $.ajax({
        url: '{{ $data_url }}?page=' + page,
        success: function(resp) {
            $('#totalCountCard').text(resp.total);
            $('#pageSummary').text(`Showing ${resp.from || 0} to ${resp.to || 0} of ${resp.total} entries`);
            
            const tbody = $('#statusTable tbody');
            tbody.empty();
            
            if (resp.data.length === 0) {
                tbody.append('<tr><td colspan="10" class="text-center p-5 text-muted">No records found.</td></tr>');
            } else {
                resp.data.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td><span class="status-badge">${item.status_name || 'Pending'}</span></td>
                            <td><span class="fw-bold" style="color: #434AFA;">${item.campaign_name}</span></td>
                            <td>${item.company_name || '-'}</td>
                            <td>${item.name}</td>
                            <td>${item.contact_person || '-'}</td>
                            <td>${item.legal_status || '-'}</td>
                            <td>${item.phone}</td>
                            <td>${item.gst_number || '-'}</td>
                            <td>${item.turnover || '-'}</td>
                            <td>${item.state || '-'}</td>
                            <td>${item.city || '-'}</td>
                            <td><span class="text-danger fw-bold">${item.pivot_followup || '-'}</span></td>
                            <td>
                                <a href="/calling/${item.id}/remarks?campaign_id=${item.calling_campaign_id}" class="btn btn-sm btn-primary" style="border-radius: 6px; padding: 2px 8px;">
                                    <i class="bi bi-chat-dots"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                });
            }
            renderPagination(resp);
        }
    });
}

function renderPagination(data) {
    const $container = $('#paginationLinks');
    $container.empty();
    
    if (data.last_page <= 1) return;

    // Previous
    $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadTableData(${data.current_page - 1}); return false;">Prev</a>
    </li>`);

    // Only show current and total for simplicity like Sales
    $container.append(`<li class="page-item active"><span class="page-link">${data.current_page} / ${data.last_page}</span></li>`);

    // Next
    $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadTableData(${data.current_page + 1}); return false;">Next</a>
    </li>`);
}
</script>
@endpush
