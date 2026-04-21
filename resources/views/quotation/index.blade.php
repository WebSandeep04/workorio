@extends('layouts.app')

@section('title', 'Quotations')
@section('page_title', 'Quotations')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
    font-size: 1.2rem;
    color: white;
  }

  .icon-blue { background: linear-gradient(135deg, #434AFA, #667eea); }
  .icon-green { background: linear-gradient(135deg, #10b981, #34d399); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #4b5563;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #111827;
    font-family: Montserrat;
  }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-label-modern {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    font-size: 9px;
    height: auto;
  }

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
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-search-btn:hover {
    background: #3538d4;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .custom-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .custom-table thead th {
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
    font-family: Montserrat;
  }

  .custom-table tbody td {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    color: #111827;
    border-bottom: 1px solid #f4f4f6;
    background: transparent;
    font-family: Montserrat;
    white-space: nowrap;
  }

  .custom-table tbody tr:hover {
    background: #f8f9ff;
  }

  .action-buttons {
    display: flex;
    gap: 0.4rem;
  }

  .btn-action {
    border: none;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    min-width: 30px;
    height: 30px;
  }

  .btn-download { background: #434AFA; }
  .btn-revise { background: #434AFA; }
  .btn-history { background: #434AFA; }

  .btn-action:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    color: white;
  }

  .pagination .page-link {
    color: #434afa;
    border: 1px solid #e5e7eb;
    font-size: 0.8rem;
    padding: 0.4rem 0.75rem;
  }

  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
  }

  .loading-state {
    text-align: center;
    padding: 3rem !important;
  }

  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #434AFA;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  /* Modal Styles */
  .modal-content {
      border-radius: 0;
      border: none;
  }
  .modal-header {
      background: #434AFA;
      color: white;
      border-radius: 0;
  }
  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-blue">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Quotations</div>
                <div class="summary-card-value" id="totalQuotations">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-green">
                <i class="bi bi-currency-rupee"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Overall Value</div>
                <div class="summary-card-value" id="totalValue">₹0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-amber">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">This Month</div>
                <div class="summary-card-value" id="monthQuotations">0</div>
            </div>
        </div>
    </div>

    <!-- Filter Box -->
    <div class="filterBox">
        <div>
            <label class="form-label-modern"><i class="bi bi-person"></i> Executive</label>
            <select class="form-control-modern w-100" id="filter_user">
                <option value="">All Executives</option>
            </select>
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-person-badge"></i> Customer</label>
            <select class="form-control-modern w-100" id="filter_customer">
                <option value="">All Customers</option>
            </select>
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-person-lines-fill"></i> Prospect</label>
            <select class="form-control-modern w-100" id="filter_prospect">
                <option value="">All Prospects</option>
            </select>
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-calendar"></i> From</label>
            <input type="date" class="form-control-modern w-100" id="filter_from_date">
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-calendar"></i> To</label>
            <input type="date" class="form-control-modern w-100" id="filter_to_date">
        </div>
        <div>
            <label class="form-label-modern"><i class="bi bi-tag"></i> Type</label>
            <select class="form-control-modern w-100" id="customer_type">
                <option value="">All Types</option>
                <option value="customer">Customer</option>
                <option value="prospect">Prospect</option>
            </select>
        </div>
    </div>

    <!-- Search and Add -->
    <div class="table-search">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Search by quotation number or customer name...">
        </div>
        <a href="{{ route('quotation.create') }}" class="table-search-btn">
            <i class="bi bi-plus-lg"></i> New Quotation
        </a>
    </div>

    <!-- Table Section -->
    <div class="data-table-card">
        <div class="table-responsive">
            <table class="custom-table" id="quotationsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Quotation No.</th>
                        <th>Customer / Prospect</th>
                        <th>Executive</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th style="border-right: none;">Actions</th>
                    </tr>
                </thead>
                <tbody id="quotationsBody">
                    <tr>
                        <td colspan="7" class="loading-state">
                            <div class="loading-spinner"></div>
                            <p class="mt-2 mb-0">Loading quotations...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Placeholder -->
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted small" id="paginationInfo">
            Showing ...
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm m-0" id="paginationLinks">
                <!-- Links will be injected by JS -->
            </ul>
        </nav>
    </div>
</div>

<!-- Revisions Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0">Revision History: <span id="revQuoteNo"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Version</th>
                                <th>Date</th>
                                <th class="text-center pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="revBody">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadUsers();
    loadCustomers();
    loadProspects();
    loadQuotations();

    // Search and Filters
    $('#searchInput, #filter_user, #filter_customer, #filter_prospect, #filter_from_date, #filter_to_date, #customer_type').on('input change', function() {
        filterTable();
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('history')) {
        openHistoryById(urlParams.get('history'));
    }
});

let allQuotations = [];

function loadUsers() {
    $.get("{{ route('quotation.users') }}")
        .done(function(resp) {
            let html = '<option value="">All Executives</option>';
            resp.forEach(u => {
                html += `<option value="${u.id}">${u.name}</option>`;
            });
            $('#filter_user').html(html);
        });
}

function loadCustomers() {
    $.get("{{ route('quotation.customers') }}")
        .done(function(resp) {
            let html = '<option value="">All Customers</option>';
            resp.forEach(c => {
                const label = c.name + (c.company_name ? ` (${c.company_name})` : '');
                html += `<option value="${c.id}">${label}</option>`;
            });
            $('#filter_customer').html(html);
        });
}

function loadProspects() {
    $.get("{{ route('quotation.prospects') }}")
        .done(function(resp) {
            let html = '<option value="">All Prospects</option>';
            resp.forEach(p => {
                const label = p.prospectus_name + (p.contact_person ? ` (${p.contact_person})` : '');
                html += `<option value="${p.id}">${label}</option>`;
            });
            $('#filter_prospect').html(html);
        });
}

function loadQuotations() {
    $.get("{{ route('quotation.list') }}")
        .done(function(resp) {
            allQuotations = (resp && resp.data) ? resp.data : [];
            renderTable(allQuotations);
            updateSummary(allQuotations);
        })
        .fail(function() {
            $('#quotationsBody').html('<tr><td colspan="7" class="text-center text-danger py-4">Failed to load data.</td></tr>');
        });
}

function updateSummary(data) {
    const total = data.length;
    let value = 0;
    let thisMonthCount = 0;
    const now = new Date();
    
    data.forEach(r => {
        const amt = parseFloat(r.total_amount || r.grand_total || 0);
        value += isNaN(amt) ? 0 : amt;
        
        const d = new Date(r.created_at);
        if (d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()) {
            thisMonthCount++;
        }
    });

    $('#totalQuotations').text(total);
    $('#totalValue').text('₹' + value.toLocaleString('en-IN', { maximumFractionDigits: 0 }));
    $('#monthQuotations').text(thisMonthCount);
}

function renderTable(data) {
    if (!data.length) {
        $('#quotationsBody').html('<tr><td colspan="7" class="text-center text-muted py-4">No records found.</td></tr>');
        $('#paginationInfo').text('Showing 0 records');
        return;
    }

    let html = '';
    data.forEach((r, idx) => {
        const qNo = r.quotation_number || '-';
        const customer = r.customer_display || r.customer_name || '-';
        const executive = r.creator_name || '-';
        const amount = Number(r.total_amount || r.grand_total || 0);
        const date = r.created_at ? new Date(r.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
        const fileUrl = r.file_url || '';
        const id = r.id;

        html += `
            <tr>
                <td>${idx + 1}</td>
                <td><strong>${qNo}</strong></td>
                <td>${customer}</td>
                <td>${executive}</td>
                <td>₹${amount.toLocaleString('en-IN')}</td>
                <td>${date}</td>
                <td>
                    <div class="action-buttons">
                        <a href="${fileUrl}" target="_blank" class="btn-action btn-download" title="Download PDF">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <button class="btn-action btn-revise" onclick="reviseQuote('${qNo}')" title="Revise">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn-action btn-history" onclick="openRevisionHistory('${id}', '${qNo}')" title="Revisions">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    $('#quotationsBody').html(html);
    $('#paginationInfo').text(`Showing ${data.length} records`);
}

function filterTable() {
    const search = $('#searchInput').val().toLowerCase();
    const userId = $('#filter_user').val();
    const custId = $('#filter_customer').val();
    const prosId = $('#filter_prospect').val();
    const fromDate = $('#filter_from_date').val();
    const toDate = $('#filter_to_date').val();
    const type = $('#customer_type').val();
    
    let filtered = allQuotations.filter(r => {
        // Search
        const matchesSearch = (r.quotation_number && r.quotation_number.toLowerCase().includes(search)) || 
                             (r.customer_display && r.customer_display.toLowerCase().includes(search)) ||
                             (r.customer_name && r.customer_name.toLowerCase().includes(search));
        
        // User
        const matchesUser = !userId || (String(r.created_by) === String(userId));

        // Customer
        const matchesCustomer = !custId || (r.customer_type === 'customer' && String(r.customer_id) === String(custId));
        
        // Prospect
        const targetProsId = r.prospect_id || (r.customer_type === 'prospect' ? r.customer_id : null);
        const matchesProspect = !prosId || (r.customer_type === 'prospect' && String(targetProsId) === String(prosId));

        // Type
        const matchesType = !type || (r.customer_type === type);
        
        // Date Range
        let matchesDate = true;
        if (r.created_at) {
            const createdAt = new Date(r.created_at).setHours(0,0,0,0);
            if (fromDate) {
                const start = new Date(fromDate).setHours(0,0,0,0);
                if (createdAt < start) matchesDate = false;
            }
            if (toDate) {
                const end = new Date(toDate).setHours(0,0,0,0);
                if (createdAt > end) matchesDate = false;
            }
        } else if (fromDate || toDate) {
            matchesDate = false;
        }
        
        return matchesSearch && matchesUser && matchesCustomer && matchesProspect && matchesType && matchesDate;
    });
    
    renderTable(filtered);
}

function reviseQuote(no) {
    window.location.href = `{{ route('quotation.create') }}?quote=${encodeURIComponent(no)}&revise=1`;
}

function openRevisionHistory(id, no) {
    $('#revQuoteNo').text(no);
    $('#revBody').html('<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');
    
    $.get(`{{ url('/quotation') }}/${id}/revisions`)
        .done(function(resp) {
            const rows = resp.data || [];
            if (!rows.length) {
                $('#revBody').html('<tr><td colspan="4" class="text-center text-muted py-3">No revisions found.</td></tr>');
                return;
            }
            let html = '';
            rows.forEach((r, i) => {
                const date = new Date(r.created_at).toLocaleString('en-GB');
                const link = r.file_url ? `<a href="${r.file_url}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 11px;">View PDF</a>` : '-';
                html += `
                    <tr>
                        <td class="ps-3">${i + 1}</td>
                        <td>Version ${r.version}</td>
                        <td>${date}</td>
                        <td class="text-center pe-3">${link}</td>
                    </tr>
                `;
            });
            $('#revBody').html(html);
        });
        
    new bootstrap.Modal(document.getElementById('revisionModal')).show();
}

function openHistoryById(id) {
    openRevisionHistory(id, id);
}

</script>
@endpush
