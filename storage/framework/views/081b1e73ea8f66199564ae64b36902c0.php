

<?php $__env->startSection('title', 'Quotation'); ?>
<?php $__env->startSection('page_title', 'Quotation'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .container-fluid {
        padding: 1rem;
        background: #f8f9fa;
    }

    /* Hero Section */
    .quotation-hero-card {
        background-color: #434AfA;
        border-radius: 16px;
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        background-img: url("public/img/side-icon.png");
    }

    .quotation-hero-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        opacity: 0.3;
    }

    .hero-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
        font-family: Montserrat;
    }

    .hero-subtitle {
        font-size: 0.95rem;
        margin: 0;
        opacity: 0.95;
        font-family: Montserrat;
    }

    /* Summary Cards */
    .summary-cards-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
    }

    .summary-card {
        background: #f5f5f5;
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: black;
        font-size: 1.5rem;
    }

    .summary-content {
        flex: 1;
    }

    .summary-label {
        font-size: 0.85rem;
        font-family: Montserrat;
        color: #000;
        margin-bottom: 0.25rem;
    }

    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #000;
        font-family: Monserrat;
    }

    /* Modern Card */
    .modern-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .modern-card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-modern {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #000;
        font-family: Montserrat;

    }

    .card-title-modern i {
        color: #667eea;
    }

    .btn-add-modern {
        background: #434afa;
        border: none;
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 3.3px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        Font-family: Montserrat;
    }

    .btn-add-modern:hover {
        background: #434afa;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    }

    .modern-card-body {
        padding: 0;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-color: #434AFA #f1f1f1;
        scrollbar-width: thin;
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #434AFA;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #3538d4;
    }

    .custom-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        
        margin: 0;
        font-family: Montserrat;
        font-weight: 500;
        font-size: 11px;
        line-height: 1.2;
        letter-spacing: 0;
    }

    .custom-table tbody tr td {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .custom-table thead {
        background: #fafafa;
    }

    .custom-table thead th {
        padding: 0.35rem 0.5rem;
        font-weight: 600;
        font-size: 10px;
        color: #000;
        border-bottom: 1px solid #e9ecef;
        text-align: left;
        white-space: nowrap;
    }

    .custom-table tbody tr {
        transition: background 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
        height: auto;
    }

    .custom-table tbody tr:hover {
        background: #f9fafb;
    }

    .custom-table tbody td {
        padding: 0.35rem 0.5rem;
        vertical-align: middle;
        color: #000;
        white-space: nowrap;
    }

    .loading-state {
        text-align: center;
        padding: 3rem !important;
        color: #000;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        margin: 0 auto;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 500;
    }

    .status-draft {
        background: #e3f2fd;
        color: #1976d2;
    }

    .priority-high {
        color: #dc3545;
        font-weight: 600;
    }

    /* Table Footer & Pagination */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid #f0f0f0;
        background: #fafafa;
    }

    .pagination-info {
        font-size: 0.9rem;
        color: #666;
    }

    .pagination-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .page-btn {
        min-width: 36px;
        height: 36px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 10px;
        color: #434afa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        padding: 0 0.5rem;
    }

    .page-btn:hover {
        background: rgba(67, 74, 250, 0.15);
        border-color: #434afa;
        transform: translateY(-1px);
    }

    .page-btn.active {
        background: #434afa;
        color: white;
        border-color: #434afa;
        box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }

    .modal-header-modern {
        padding: 1rem 1.5rem;
        border: none;
    }

    .modal-title-modern {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .btn-close-white {
        filter: invert(1);
        opacity: 0.8;
    }

    .btn-close-white:hover {
        opacity: 1;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer-modern {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e9ecef;
        background: #f8f9fa;
    }

    .btn-close-modern {
        background: #434AFA;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-close-modern:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        color: #434AFA;
    }

    .badge-modern {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-info-modern {
        background: #434AFA;
        color: white;
    }

    .btn-pdf-modern {
        background: #434AFA;
        color: white;
        border: none;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-pdf-modern:hover {
        color: white;
        opacity: 0.9;
        transform: translateY(-1px);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-direction: row;
        gap: 0.3rem;
        align-items: center;
    }

    .btn-action {
        border: none;
        color: white;
        padding: 0.25rem 0.4rem;
        border-radius: 4px;
        font-size: 9px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-family: Montserrat;
        white-space: nowrap;
        min-width: 28px;
        height: 28px;
    }

    .btn-action:hover {
        color: white;
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-download {
        background: #434AFA;
    }

    .btn-revise {
        background: #434AFA;
    }

    .btn-history {
        background: #434AFA;
    }

    .btn-action i {
        font-size: 12px;
    }

    .btn-action span {
        display: none;
    }

    .modal-header-modern{
        background: #434AFA;
        color: white;
    }

     /* Table CSS */
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
    font-family: Montserrat;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 5px;
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

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
    font-family: Montserrat !important;
  }

  /* IMPORTANT: White header style with vertical borders */
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
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
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

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 100px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(13) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(14) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(15) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(16) { min-width: 180px; }

  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }

    @media (max-width: 767px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .hero-title {
            font-size: 1.5rem;
        }

        .quotation-hero-card {
            padding: 1.5rem;
        }
        
        .summary-cards-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .summary-card {
            padding: 1rem;
            min-height: 100px;
        }
        
        .table-footer {
            flex-direction: column;
            gap: 1rem;
        }

        .modern-card-header {
            padding: 0.75rem 1rem;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 10px;
        }

        .card-title-modern {
            font-size: 0.95rem !important;
        }

        .btn-add-modern {
             padding: 0.4rem 0.75rem !important;
             font-size: 0.8rem !important;
             white-space: nowrap;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <!-- Hero Section with Metrics -->
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="quotation-hero-card h-100">
                <div>
                    <h2 class="hero-title">Quotations Overview</h2>
                    <p class="hero-subtitle">Manage your quotations, track revisions, and download PDFs with ease.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="summary-cards-wrapper">
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-label">Total Quotation</div>
                        <div class="summary-value" id="totalQuotations">0</div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-label">Total Value</div>
                        <div class="summary-value" id="totalValue">₹0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h5 class="card-title-modern">
                <i class="bi bi-file-earmark-text"></i>
                Quotations Overview
            </h5>
            <a href="<?php echo e(route('quotation.create')); ?>" class="btn-add-modern">
                Add +
            </a>
        </div>
        
            <div class="modern-card data-table-card">
                <div class="modern-card-body">
                    <div class="table-responsive">
                        <table class="table custom-table" id="quotationsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>QUOTATION NO</th>
                                    <th>CUSTOMER/PROSPECT</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>CREATED AT</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="quotationsBody">
                                <tr>
                                    <td colspan="6" class="loading-state">
                                        <div class="loading-spinner"></div>
                                        <p class="mt-2 mb-0">Loading quotations…</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        
            
            <!-- Pagination -->
            <div class="table-footer">
                <div class="pagination-info">
                    <span id="paginationInfo">Showing 1-5 from 100+ data</span>
                </div>
                <div class="pagination-controls">
                    <button class="page-btn" id="prevPage"><i class="bi bi-chevron-left"></i></button>
                    <button class="page-btn active" data-page="1">1</button>
                    <button class="page-btn" data-page="2">2</button>
                    <button class="page-btn" data-page="3">3</button>
                    <button class="page-btn" data-page="4">4</button>
                    <button class="page-btn" data-page="5">5</button>
                    <button class="page-btn" id="nextPage"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revision History Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header-modern">
                <h6 class="modal-title-modern">Revision History: <span id="revQuoteNo"></span></h6>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px;">#</th>
                                <th style="width:120px;">Version</th>
                                <th>Date</th>
                                <th style="width:140px;" class="text-center">File</th>
                            </tr>
                        </thead>
                        <tbody id="revBody">
                            <tr><td colspan="4" class="text-center text-muted">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-close-modern" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    console.log('Quotation page loaded');
    loadQuotations();
    // Auto open revisions if history=<id> is present in URL
    try {
        const params = new URLSearchParams(window.location.search);
        const hid = params.get('history');
        if (hid) {
            openHistoryById(hid);
        }
    } catch (e) {}
    
    // Pagination handlers
    $('.page-btn[data-page]').on('click', function() {
        $('.page-btn[data-page]').removeClass('active');
        $(this).addClass('active');
        // In a real implementation, this would load the specific page
        loadQuotations();
    });
    
    $('#prevPage').on('click', function() {
        const currentPage = $('.page-btn.active').data('page');
        if (currentPage > 1) {
            $('.page-btn[data-page="' + (currentPage - 1) + '"]').click();
        }
    });
    
    $('#nextPage').on('click', function() {
        const currentPage = $('.page-btn.active').data('page');
        const maxPage = 5; // You can make this dynamic
        if (currentPage < maxPage) {
            $('.page-btn[data-page="' + (currentPage + 1) + '"]').click();
        }
    });
});

function fmtCurrency(v){
    if (v === null || v === undefined) return '-';
    const num = Number(v);
    if (Number.isNaN(num)) return String(v);
    return '₹' + num.toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function safe(obj, key, fallback='-'){
    if (!obj) return fallback;
    const val = obj[key];
    if (val === undefined || val === null || val === '') return fallback;
    return val;
}

function animateValue(element, start, end, duration, prefix = '') {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        element.textContent = prefix + current.toLocaleString('en-IN');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

function loadQuotations(){
    $('#quotationsBody').html(`
        <tr>
            <td colspan="6" class="loading-state">
                <div class="loading-spinner"></div>
                <p class="mt-2 mb-0">Loading quotations…</p>
            </td>
        </tr>
    `);
    $.get("<?php echo e(route('quotation.list')); ?>")
        .done(function(resp){
            const rows = (resp && resp.data) ? resp.data : [];
            if (!rows.length) {
                $('#quotationsBody').html('<tr><td colspan="6" class="text-center text-muted py-4">No quotations found.</td></tr>');
                $('#totalQuotations').text('0');
                $('#totalValue').text('₹0');
                return;
            }

            // Update colspan for loading state
            $('#quotationsBody').find('td[colspan]').attr('colspan', '6');

            // Calculate totals
            let totalCount = rows.length;
            let totalValue = 0;
            rows.forEach(r => {
                const amount = parseFloat(r.total_amount || r.grand_total || 0);
                if (!isNaN(amount)) {
                    totalValue += amount;
                }
            });

            // Animate counters
            animateValue(document.getElementById('totalQuotations'), 0, totalCount, 1000);
            animateValue(document.getElementById('totalValue'), 0, Math.floor(totalValue), 1000, '₹');

            let html = '';
            rows.forEach((r, idx) => {
                // Map fields for table structure
                const quotationNo = safe(r, 'quotation_number', r.id ?? '-');
                const customer = safe(r, 'customer_display', safe(r, 'customer_name', safe(r, 'customer', '-')));
                const totalAmount = r.total_amount || r.grand_total || 0;
                const createdAt = safe(r, 'created_at', '-');
                const createdDate = createdAt ? new Date(createdAt).toLocaleDateString('en-GB', {year: 'numeric', month: 'short', day: 'numeric'}) : '-';
                const fileUrl = safe(r, 'file_url', '');
                const quoteId = r.id ?? '';
                
                html += `
                    <tr>
                        <td>${idx + 1}</td>
                        <td><strong>${quotationNo}</strong></td>
                        <td>${customer}</td>
                        <td><strong>${fmtCurrency(totalAmount)}</strong></td>
                        <td>${createdDate}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-download" onclick="event.stopPropagation(); downloadQuotation('${fileUrl}', '${quotationNo}')" title="Download">
                                    <i class="bi bi-download"></i>
                                    <span>Download</span>
                                </button>
                                <button class="btn-action btn-revise" onclick="event.stopPropagation(); reviseQuotation('${quoteId}', '${quotationNo}')" title="Revise">
                                    <i class="bi bi-arrow-repeat"></i>
                                    <span>Revise</span>
                                </button>
                                <button class="btn-action btn-history" onclick="event.stopPropagation(); openRevisionHistory('${quoteId}', '${quotationNo}')" title="History">
                                    <i class="bi bi-clock-history"></i>
                                    <span>History</span>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
            $('#quotationsBody').html(html);
            
            // Update pagination info
            $('#paginationInfo').text(`Showing 1-${Math.min(5, rows.length)} from ${rows.length}+ data`);
        })
        .fail(function(xhr){
            console.error('Failed to load quotations', xhr.responseText);
            $('#quotationsBody').html('<tr><td colspan="6" class="text-danger text-center py-4">Failed to load quotations.</td></tr>');
        });
}

// Download quotation file
function downloadQuotation(fileUrl, fileName) {
    if (!fileUrl) {
        alert('File not available');
        return;
    }
    // Create a temporary anchor element to trigger download
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = (fileName || 'quotation') + '.pdf';
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Revise quotation
function reviseQuotation(id, quotationNo) {
    if (!quotationNo) {
        alert('Quotation number not found');
        return;
    }
    // Redirect to create page with quotation number for revision
    window.location.href = `<?php echo e(route('quotation.create')); ?>?quote=${encodeURIComponent(quotationNo)}&revise=1`;
}

// History modal
function openRevisionHistory(id, no){
    if(!id){ return; }
    $('#revQuoteNo').text(no || id);
    $('#revBody').html('<tr><td colspan="4" class="text-center text-muted">Loading…</td></tr>');
    let url = `<?php echo e(url('/quotation')); ?>/${id}/revisions`;
    $.get(url)
      .done(function(resp){
        const rows = (resp && resp.data) ? resp.data : [];
        if(!rows.length){
          $('#revBody').html('<tr><td colspan="4" class="text-center text-muted">No history found.</td></tr>');
          return;
        }
        let html = '';
        rows.forEach((r, i) => {
          const ver = r.version ?? '-';
          const when = r.created_at ? new Date(r.created_at).toLocaleString() : '-';
          const label = r.label || '';
          const link = r.file_url ? `<a href="${r.file_url}" target="_blank" class="btn-pdf-modern">Open PDF</a>` : '-';
          html += `<tr>
              <td>${i+1}</td>
              <td>v${ver} ${label ? `<span class="badge-modern badge-info-modern ms-1">${label}</span>`:''}</td>
              <td>${when}</td>
              <td class="text-center">${link}</td>
          </tr>`;
        });
        $('#revBody').html(html);
      })
      .fail(function(xhr){
        $('#revBody').html('<tr><td colspan="4" class="text-danger text-center">Failed to load history</td></tr>');
      });
    const el = document.getElementById('revisionModal');
    if (window.bootstrap && bootstrap.Modal) {
        const m = new bootstrap.Modal(el);
        m.show();
    } else if (window.$ && $(el).modal) { // Bootstrap 4 fallback
        $(el).modal('show');
    } else {
        el.style.display = 'block';
    }
}

// Helper to open revisions by id when we don't have the quotation number (from deep link)
function openHistoryById(id){
    if(!id){ return; }
    $('#revQuoteNo').text(id);
    $('#revBody').html('<tr><td colspan="4" class="text-center text-muted">Loading…</td></tr>');
    let url = `<?php echo e(url('/quotation')); ?>/${id}/revisions`;
    $.get(url)
      .done(function(resp){
        const q = resp && resp.quotation ? resp.quotation : null;
        if (q && q.quotation_number) {
            $('#revQuoteNo').text(q.quotation_number);
        }
        const rows = (resp && resp.data) ? resp.data : [];
        if(!rows.length){
          $('#revBody').html('<tr><td colspan="4" class="text-center text-muted">No history found.</td></tr>');
        } else {
          let html = '';
          rows.forEach((r, i) => {
            const ver = r.version ?? '-';
            const when = r.created_at ? new Date(r.created_at).toLocaleString() : '-';
            const label = r.label || '';
            const link = r.file_url ? `<a href="${r.file_url}" target="_blank" class="btn-pdf-modern">Open PDF</a>` : '-';
            html += `<tr>
                <td>${i+1}</td>
                <td>v${ver} ${label ? `<span class="badge-modern badge-info-modern ms-1">${label}</span>`:''}</td>
                <td>${when}</td>
                <td class="text-center">${link}</td>
            </tr>`;
          });
          $('#revBody').html(html);
        }
        const el = document.getElementById('revisionModal');
        if (window.bootstrap && bootstrap.Modal) {
            const m = new bootstrap.Modal(el);
            m.show();
        } else if (window.$ && $(el).modal) {
            $(el).modal('show');
        } else {
            el.style.display = 'block';
        }
      });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/index.blade.php ENDPATH**/ ?>