

<?php $__env->startSection('title', 'Payment Followup Details'); ?>
<?php $__env->startSection('page_title', 'Payment Followup Details'); ?>

<?php
  $cardTitle = 'Total Invoices';
  $iconClass = 'icon-violet';
  // Use a single static image for all cards as requested
  $iconImg = 'pending.png'; 

  switch($type) {
    case 'pending-invoices':
      $cardTitle = 'Pending Invoices';
      $iconClass = 'icon-amber';
      break;
    case 'paid-invoices':
      $cardTitle = 'Paid Invoices';
      $iconClass = 'icon-emerald';
      break;
    case 'remaining-amount':
      $cardTitle = 'Remaining Amount';
      $iconClass = 'icon-rose';
      break;
    case 'received-amount':
      $cardTitle = 'Received Amount';
      $iconClass = 'icon-emerald';
      break;
    case 'total-amount':
      $cardTitle = 'Total Amount';
      $iconClass = 'icon-sky';
      break;
  }
?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="sales_table"></div>

  <!-- Summary Cards -->
  <div class="summary-cards mb-3">
    <div class="summary-card card-1" style="max-width: 250px;">
      <div class="summary-card-icon <?php echo e($iconClass); ?>">
        <img src="<?php echo e(asset('img/icons/' . $iconImg)); ?>" alt="<?php echo e($cardTitle); ?>" onerror="this.onerror=null;this.src='<?php echo e(asset('img/icons/file.png')); ?>';">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label"><?php echo e($cardTitle); ?></div>
        <div class="summary-card-value" id="totalCountCard">0</div>
      </div>
    </div>
  </div>

  <!-- Custom Filter Panel for Invoices -->
  <div class="filterScroll mb-2">
    <div class="filterBox">
      <div class="row">
        <div class="col-md-4">
            <label class="form-label-modern">Status</label>
            <select class="form-control-modern" id="invoice_status">
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
                <option value="partially_paid">Partially Paid</option>
            </select>
        </div>
        <div class="col-md-4">
             <label class="form-label-modern">From Due Date</label>
             <input type="date" class="form-control-modern" id="from_date">
        </div>
        <div class="col-md-4">
             <label class="form-label-modern">To Due Date</label>
             <input type="date" class="form-control-modern" id="to_date">
        </div>
      </div>
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="detailsSearch" placeholder="Search invoice number, customer..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="details_table">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Received</th>
              <th>Due Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
                <td colspan="6" class="loading-state">
                    <i class="bi bi-arrow-repeat"></i>
                    <p class="mt-2 mb-0">Loading data...</p>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta mt-2" id="pageSummaryBottom">
     Page 1 of 1 • Showing 0-0 of 0
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  /* Import fonts */
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  /* Global font family */
  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Summary Card CSS */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
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

  .summary-card-icon img {
    width: 24px;
    height: 24px;
    object-fit: contain;
  }
  
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

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
    color: #000;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  /* Filter Panel CSS (If included via x-filter-panel, these styles might be redundant but ensuring consistency) */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    flex-wrap: wrap;
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
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
    width: 100%;
    margin-top: 0;
  }
  
  .filterBox .form-control-modern option {
      color: #000;
      background: #fff;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  /* Table Search */
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

  /* Pagination */
  .pagination .page-link {
    color: #667eea;
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
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    transform: translateY(-1px);
    color: #334155;
  }
  
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
  
  .loading-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
  }
  .loading-state i { font-size: 1.5rem; animation: spin 1s linear infinite; display:block; margin-bottom:0.5rem; }
  @keyframes spin { 100% { transform: rotate(360deg); } }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const type = '<?php echo e($type); ?>';
    
    // Parse URL params on load
    const urlParams = new URLSearchParams(window.location.search);
    
    // Filter Toggle
    $(document).ready(function () {
      // Initialize Filters from URL
      initFilters();

      // Load Data
      loadData();
    });
    
    function initFilters() {
        if(urlParams.get('status')) $('#invoice_status').val(urlParams.get('status'));
        if(urlParams.get('date_from')) $('#from_date').val(urlParams.get('date_from'));
        if(urlParams.get('date_to')) $('#to_date').val(urlParams.get('date_to'));
        if(urlParams.get('search')) $('#detailsSearch').val(urlParams.get('search'));
    }

    function formatDateOnly(value) {
        if (!value) return 'N/A';
        const str = String(value);
        const t = str.indexOf('T');
        if (t > 0) return str.slice(0, t);
        return str.length >= 10 ? str.slice(0, 10) : str;
    }

    function capitalizeFirstLetter(string) {
        if(!string) return '';
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    function loadData(page = 1) {
        // Collect params from Inputs (priority)
        let data = {
            page: page,
            date_from: $('#from_date').val(),
            date_to: $('#to_date').val(),
            search: $('#detailsSearch').val(),
            status: $('#invoice_status').val()
        };

        $.ajax({
            url: '<?php echo e(route("payment-followup.details.data", ["type" => ":type"])); ?>'.replace(':type', type),
            type: 'GET',
            data: data,
            success: function(response) {
                let html = '';
                if (!response.data || response.data.length === 0) {
                     html = '<tr><td colspan="6" class="text-center empty-state"><p class="mt-2 mb-0">No records found.</p></td></tr>';
                } else {
                    response.data.forEach(function(invoice) {
                        let badgeClass = 'bg-secondary';
                        const st = (invoice.status || '').toLowerCase();
                        if(st === 'paid') badgeClass = 'bg-success';
                        else if(st === 'pending') badgeClass = 'bg-warning text-dark';
                        else if(st === 'overdue') badgeClass = 'bg-danger';
                        else if(st === 'partially_paid') badgeClass = 'bg-info text-dark';
                        
                        html += `
                            <tr>
                                <td>${invoice.invoice_number}</td>
                                <td>${invoice.customer ? invoice.customer.name : 'N/A'}</td>
                                <td>₹${parseFloat(invoice.amount).toFixed(2)}</td>
                                <td>₹${parseFloat(invoice.received_amount).toFixed(2)}</td>
                                <td>${formatDateOnly(invoice.due_date)}</td>
                                <td><span class="badge ${badgeClass}">${capitalizeFirstLetter(st)}</span></td>
                            </tr>
                        `;
                    });
                }
                
                $('#details_table tbody').html(html);
                renderPagination(response);
                updateSummary(response);
            },
            error: function(xhr) {
                 console.error("Error:", xhr.responseText);
                 $('#details_table tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data.</td></tr>');
            }
        });
    }

    function renderPagination(data) {
        const current = Number(data.current_page) || 1;
        const last = Number(data.last_page) || 1;
        const $container = $('#paginationLinks');
        $container.empty();
        if (last <= 1) return;

        $container.append(`
            <li class="page-item ${current === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current - 1}">
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
                <a class="page-link" href="#" data-page="${current + 1}">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `);
    }
    
    $(document).on('click', '.pagination .page-link', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) loadData(page);
    });

    function updateSummary(response) {
        const current = response.current_page || 1;
        const last = response.last_page || 1;
        const total = response.total || 0;
        const from = response.from || 0;
        const to = response.to || 0;
        
        $('#totalCountCard').text(total);
        if (total === 0) {
             $('#pageSummaryBottom').text('Page 1 of 1 • Showing 0-0 of 0');
        } else {
             $('#pageSummaryBottom').text(`Page ${current} of ${last} • Showing ${from}-${to} of ${total}`);
        }
    }
    
    // Search Debounce
    let debounceTimer;
    $('#detailsSearch').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            loadData(1);
        }, 500);
    });
    
    // Filter Changes
    $(document).on('change', '#invoice_status, #from_date, #to_date', function () {
        loadData(1);
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/payment-followup/details.blade.php ENDPATH**/ ?>