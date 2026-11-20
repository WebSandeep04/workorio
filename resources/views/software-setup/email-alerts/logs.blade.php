@extends('layouts.app')

@section('title', 'Email Delivery Logs')
@section('page_title', 'Email Alerts Delivery Logs')

@push('styles')
<style>
    .logs-container {
        padding: 0.75rem;
        background: #f8f9fa;
    }
    .metric-card {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .logs-table-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .logs-table {
        margin-bottom: 0;
        font-size: 0.85rem;
    }
    .logs-table th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        vertical-align: middle;
        padding: 0.75rem 0.85rem;
        border-bottom: 2px solid #cbd5e1;
    }
    .logs-table td {
        vertical-align: middle;
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .logs-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .nav-tabs-modern {
        border-bottom: 2px solid #e2e8f0;
        gap: 0.5rem;
    }
    .nav-tabs-modern .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 0.6rem 1.25rem;
        border-radius: 8px 8px 0 0;
        background: transparent;
        transition: all 0.2s;
        text-decoration: none;
    }
    .nav-tabs-modern .nav-link:hover {
        color: #434afa;
        background: rgba(67, 74, 250, 0.05);
    }
    .nav-tabs-modern .nav-link.active {
        color: #434afa;
        border-bottom: 3px solid #434afa;
        background: #fff;
    }
    .badge-soft-success {
        background: #e6f9ed;
        color: #10b981;
        font-weight: 600;
        border: 1px solid #a7f3d0;
    }
    .badge-soft-danger {
        background: #fee2e2;
        color: #ef4444;
        font-weight: 600;
        border: 1px solid #fecaca;
    }
    .loading-overlay {
        position: relative;
    }
    .loading-overlay::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.65);
        z-index: 10;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="logs-container">
    <!-- Header Page Row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-envelope-paper-heart-fill text-primary me-2"></i>Email Alerts Delivery Logs
            </h5>
            <small class="text-muted">Monitor dispatched automated emails, verify recipient delivery history, and inspect errors.</small>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="metric-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Today's Sent Mails</span>
                    <h4 class="fw-bold text-success mb-0 mt-1">{{ number_format($todaySentCount) }}</h4>
                </div>
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Today's Failures</span>
                    <h4 class="fw-bold {{ $todayFailedCount > 0 ? 'text-danger' : 'text-muted' }} mb-0 mt-1">
                        {{ number_format($todayFailedCount) }}
                    </h4>
                </div>
                <div class="rounded-circle p-3 {{ $todayFailedCount > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-muted' }}">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Lifetime Logs</span>
                    <h4 class="fw-bold text-primary mb-0 mt-1">{{ number_format($totalLogsCount) }}</h4>
                </div>
                <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-database-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar (AJAX Enabled) -->
    <div class="metric-card mb-3">
        <form id="logsFilterForm" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" id="logSearch" name="search" class="form-control form-control-sm" placeholder="Search recipient email, name or subject..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div class="col-md-3">
                <select id="logMailCode" name="mail_code" class="form-select form-select-sm">
                    <option value="">All Mail Types</option>
                    @foreach($alerts as $alert)
                        <option value="{{ $alert->mail_code }}" {{ request('mail_code') == $alert->mail_code ? 'selected' : '' }}>
                            {{ $alert->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select id="logStatus" name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>🟢 Sent</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>🔴 Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" id="logDate" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button type="button" id="btnResetLogsFilter" class="btn btn-sm btn-outline-secondary w-100" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Logs Table Container (AJAX Enabled) -->
    <div class="logs-table-card" id="logsTableWrapper">
        @include('software-setup.email-alerts.logs-table', ['logs' => $logs])
    </div>
</div>

<!-- Modal Details -->
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="logModalTitle">
                    <i class="bi bi-info-circle text-primary me-2"></i>Email Delivery Audit Details
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logModalBody">
                <!-- Dynamically populated -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let logDebounceTimer = null;
    const logModal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    const modalBody = document.getElementById('logModalBody');

    function fetchLogs(pageUrl = null) {
        const $wrapper = $('#logsTableWrapper');
        $wrapper.addClass('loading-overlay');

        const url = pageUrl || "{{ route('software-setup.email-alerts.logs') }}";
        const data = pageUrl ? {} : {
            search: $('#logSearch').val(),
            mail_code: $('#logMailCode').val(),
            status: $('#logStatus').val(),
            date: $('#logDate').val()
        };

        $.ajax({
            url: url,
            type: "GET",
            data: data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(resp) {
                $wrapper.html(resp.html);
            },
            error: function(xhr) {
                console.error("Failed to load logs:", xhr);
            },
            complete: function() {
                $wrapper.removeClass('loading-overlay');
            }
        });
    }

    // Submit form AJAX
    $('#logsFilterForm').on('submit', function(e) {
        e.preventDefault();
        fetchLogs();
    });

    // Dropdown and Date changes
    $('#logMailCode, #logStatus, #logDate').on('change', function() {
        fetchLogs();
    });

    // Debounced text search
    $('#logSearch').on('input', function() {
        clearTimeout(logDebounceTimer);
        logDebounceTimer = setTimeout(function() {
            fetchLogs();
        }, 350);
    });

    // Reset filters
    $('#btnResetLogsFilter').on('click', function() {
        $('#logSearch').val('');
        $('#logMailCode').val('');
        $('#logStatus').val('');
        $('#logDate').val('');
        fetchLogs();
    });

    // Pagination clicks AJAX
    $(document).on('click', '#logsTableWrapper .pagination a', function(e) {
        e.preventDefault();
        let pageUrl = $(this).attr('href');
        if (pageUrl) {
            fetchLogs(pageUrl);
        }
    });

    // View Details Modal
    $(document).on('click', '.btn-view-log', function() {
        const data = JSON.parse(this.getAttribute('data-log'));

        let statusBadge = data.status === 'sent' 
            ? '<span class="badge badge-soft-success px-2 py-1"><i class="bi bi-check2-circle me-1"></i>Sent Successfully</span>'
            : '<span class="badge badge-soft-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Failed Delivery</span>';

        let html = `
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small fw-semibold">Alert Type</label>
                    <div class="fw-bold text-dark">${data.alert_name} <code class="small">(${data.mail_code})</code></div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-semibold">Delivery Status</label>
                    <div>${statusBadge}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-semibold">Recipient</label>
                    <div class="fw-bold">${data.recipient_name} &lt;${data.recipient_email}&gt;</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-semibold">Dispatched At</label>
                    <div class="fw-bold">${data.sent_at}</div>
                </div>
                <div class="col-12">
                    <label class="text-muted small fw-semibold">Subject</label>
                    <div class="p-2 bg-light border rounded text-dark">${data.subject || 'N/A'}</div>
                </div>
        `;

        if (data.status === 'failed' && data.error_message) {
            html += `
                <div class="col-12">
                    <label class="text-danger small fw-semibold">Error Message / SMTP Trace</label>
                    <div class="p-2 bg-danger bg-opacity-10 border border-danger rounded text-danger small font-monospace">
                        ${data.error_message}
                    </div>
                </div>
            `;
        }

        if (data.meta && Object.keys(data.meta).length > 0) {
            html += `
                <div class="col-12">
                    <label class="text-muted small fw-semibold">Context Metadata</label>
                    <pre class="p-2 bg-light border rounded small mb-0 font-monospace">${JSON.stringify(data.meta, null, 2)}</pre>
                </div>
            `;
        }

        html += `</div>`;
        modalBody.innerHTML = html;
        logModal.show();
    });
});
</script>
@endpush
