

<?php $__env->startSection('title', 'Invoice Followup'); ?>
<?php $__env->startSection('page_title', 'Invoice Followup'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

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

  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
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
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
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
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
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
    white-space: nowrap;
    font-family: Montserrat;
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

  .custom-table th {
    background: #434AFA;
    color: white;
    font-weight: 600;
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .custom-table td {
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
  }

  .custom-table tbody tr {
    transition: all 0.3s ease;
  }

  .custom-table tbody tr:hover {
    background: rgba(67, 74, 250, 0.08);
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.15);
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .pagination .page-link {
    color: #434AFA;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .pagination .page-item.active .page-link {
    background: #434AFA;
    border-color: #434AFA;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434AFA;
    transform: translateY(-1px);
  }

  .loading-state {
    text-align: center;
    padding: 1rem;
    color: #434AFA;
    font-size: 10px;
  }

  .loading-state i {
    font-size: 1rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 1rem;
    color: #6c757d;
    font-size: 10px;
  }

  .empty-state i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
  }

  .invoice-info-card {
    background: white;
    color: black;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .invoice-info-card h5 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: black;
  }

  .invoice-info-card .invoice-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    font-size: 0.85rem;
    color: black;
  }

  .invoice-info-card .invoice-details span {
    color: black;
  }

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

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }

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

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <i class="bi bi-receipt" style="color: white; font-size: 1.2rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Amount</div>
        <div class="summary-card-value" id="invoice_amount">₹<?php echo e(number_format($invoice->amount ?? 0, 2)); ?></div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-emerald">
        <i class="bi bi-check-circle" style="color: white; font-size: 1.2rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Paid</div>
        <div class="summary-card-value" id="total_paid">₹<?php echo e(number_format($totalPaid ?? 0, 2)); ?></div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-rose">
        <i class="bi bi-clock-history" style="color: white; font-size: 1.2rem;"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Remaining Amount</div>
        <div class="summary-card-value" id="remaining_amount">₹<?php echo e(number_format($remainingAmount ?? 0, 2)); ?></div>
      </div>
    </div>
  </div>

  <!-- Invoice Info Card -->
  <div class="invoice-info-card" id="invoiceInfoCard">
    <h5><i class="bi bi-receipt"></i> Invoice Information</h5>
    <div class="invoice-details" id="invoiceDetails">
      <span><strong>Invoice No:</strong> <span id="invoice_number"><?php echo e($invoice->invoice_number ?? 'N/A'); ?></span></span>
      <span><strong>Due Date:</strong> <span id="invoice_due_date"><?php echo e($invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A'); ?></span></span>
      <span><strong>Status:</strong> <span id="invoice_status"><?php echo e(ucfirst($invoice->status ?? 'N/A')); ?></span></span>
    </div>
  </div>

  <!-- Filters -->
  <div class="filterBox mb-2">
    <div class="mb-2">
        <label for="from_date" class="form-label-modern">
            <i class="bi bi-calendar-event"></i> Payment From Date
        </label>
        <input type="date" class="form-control form-control-modern" id="from_date" name="from_date">
    </div>

    <div class="mb-2">
        <label for="to_date" class="form-label-modern">
            <i class="bi bi-calendar-check"></i> Payment To Date
        </label>
        <input type="date" class="form-control form-control-modern" id="to_date" name="to_date">
    </div>

    <div class="mb-2">
        <label for="followup_from_date" class="form-label-modern">
            <i class="bi bi-calendar-event"></i> Followup From Date
        </label>
        <input type="date" class="form-control form-control-modern" id="followup_from_date" name="followup_from_date">
    </div>

    <div class="mb-2">
        <label for="followup_to_date" class="form-label-modern">
            <i class="bi bi-calendar-check"></i> Followup To Date
        </label>
        <input type="date" class="form-control form-control-modern" id="followup_to_date" name="followup_to_date">
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search payment method, transaction ID, notes..." />
    </div>
    <button type="button" class="table-search-btn" id="addFollowupBtn">
      <i class="bi bi-plus me-1"></i>Add Payment
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="followups_table">
          <thead>
            <tr>
              <th>Amount Paid</th>
              <th>Payment Date</th>
              <th>Transaction ID</th>
              <th>Notes</th>
              <th>Next Followup Date</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading followups...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="followupsRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationfilterLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationsearchLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationdateLinks"></ul>
</div>

<!-- Add Followup Modal -->
<div class="modal fade modal-modern" id="addFollowupModal" tabindex="-1" aria-labelledby="addFollowupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 0px;">
      <div class="modal-header" style="background: #434AFA; color: white; border-radius: 0px;">
        <h5 class="modal-title" id="addFollowupModalLabel">
          <i class="bi bi-arrow-repeat"></i> Add Followup
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addFollowupForm">
        <div class="modal-body" style="padding: 1.5rem;">
          <input type="hidden" id="followup_id" name="followup_id">
          <input type="hidden" id="followup_invoice_id" name="invoice_id" value="<?php echo e($invoiceId); ?>">
          
          <div class="row g-3">
            <div class="col-md-6">
              <label for="followup_amount_paid" class="form-label-modern">Amount Paid <span class="text-danger">*</span></label>
              <input type="number" class="form-control form-control-modern" id="followup_amount_paid" name="amount_paid" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label for="followup_payment_date" class="form-label-modern">Payment Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control form-control-modern" id="followup_payment_date" name="payment_date" value="<?php echo e(date('Y-m-d')); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="followup_next_followup_date" class="form-label-modern">Next Followup Date</label>
              <input type="date" class="form-control form-control-modern" id="followup_next_followup_date" name="next_followup_date">
            </div>
            <div class="col-md-12">
              <label for="followup_notes" class="form-label-modern">Notes</label>
              <textarea class="form-control form-control-modern" id="followup_notes" name="notes" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 1rem 1.5rem; border-top: 1px solid #e9ecef;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="followupSubmitBtn" style="background: #434AFA; border: none; color: white;">Add Followup</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

let currentPage = 1;
let invoiceId = <?php echo json_encode($invoiceId, 15, 512) ?>;

// Format date
function formatDate(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const t = str.indexOf('T');
    if (t > 0) return str.slice(0, t);
    const d = new Date(str);
    if (!isNaN(d.getTime())) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    return str.length >= 10 ? str.slice(0, 10) : str;
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
        minimumFractionDigits: 2
    }).format(amount);
}

// Build pagination
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

// Load followups
function loadFollowups(page = 1) {
    if (!invoiceId) {
        $('#followups_table tbody').html('<tr><td colspan="7" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">No invoice ID provided.</p></td></tr>');
        return;
    }
    
    $.ajax({
        url: `<?php echo e(route("invoice-followup.data", ":id")); ?>?page=${page}`.replace(':id', invoiceId),
        type: 'GET',
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="7" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No followups found.</p></td></tr>';
            } else {
                data.data.forEach(function (followup) {
                    html += `
                        <tr>
                            <td>${formatCurrency(followup.amount_paid || 0)}</td>
                            <td>${formatDate(followup.payment_date)}</td>
                            <td>${followup.transaction_id || '-'}</td>
                            <td>${followup.notes || '-'}</td>
                            <td>${formatDate(followup.next_followup_date)}</td>
                            <td>${formatDate(followup.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-followup-btn" data-followup-id="${followup.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#followups_table tbody').html(html);
            renderPagination(data);
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#followups_table tbody').html('<tr><td colspan="7" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading followups. Please try again.</p></td></tr>');
        }
    });
}

function renderPagination(data) {
    const $pagination = $('#paginationLinks');
    const current = data.current_page;
    const last = data.last_page;
    buildSimplePagination($pagination, current, last);
    $('#paginationfilterLinks').hide();
    $('#paginationsearchLinks').hide();
    $('#paginationdateLinks').hide();
    $pagination.show();
}

function updateRangeInfo(from, to, total) {
    const $info = $('#followupsRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;
    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);
    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    $info.text(`Showing ${safeStart.toLocaleString('en-IN')}-${safeEnd.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        currentPage = page;
        loadFollowups(page);
    }
});

// Update summary cards
function updateSummaryCards() {
    // Fetch all followups to calculate total paid
    $.ajax({
        url: `<?php echo e(route("invoice-followup.data", ":id")); ?>?per_page=1000`.replace(':id', invoiceId),
        type: 'GET',
        success: function(response) {
            let totalPaid = 0;
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(followup) {
                    totalPaid += parseFloat(followup.amount_paid || 0);
                });
            }
            
            // Get invoice amount from the page
            const invoiceAmountText = $('#invoice_amount').text().replace('₹', '').replace(/,/g, '');
            const invoiceAmount = parseFloat(invoiceAmountText) || 0;
            const remainingAmount = invoiceAmount - totalPaid;
            
            // Update summary cards
            $('#total_paid').text('₹' + totalPaid.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#remaining_amount').text('₹' + remainingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        },
        error: function() {
            console.error('Error updating summary cards');
        }
    });
}

// Load followups on page load
$(document).ready(function () {
    loadFollowups();
});

// Handle Add Followup button click
$('#addFollowupBtn').on('click', function() {
    // Reset form and set to add mode
    $('#addFollowupForm')[0].reset();
    $('#followup_id').val('');
    $('#followup_invoice_id').val(invoiceId);
    $('#addFollowupModalLabel').html('<i class="bi bi-arrow-repeat"></i> Add Payment');
    $('#followupSubmitBtn').text('Add Payment');
    // Set default payment date to today
    const today = new Date().toISOString().split('T')[0];
    $('#followup_payment_date').val(today);
    $('#addFollowupModal').modal('show');
});

// Handle Edit Followup button click
$(document).on('click', '.edit-followup-btn', function() {
    const followupId = $(this).data('followup-id');
    
    // Fetch followup data
    $.ajax({
        url: `<?php echo e(route("invoice-followup.get", [":invoiceId", ":id"])); ?>`.replace(':invoiceId', invoiceId).replace(':id', followupId),
        type: 'GET',
        success: function(followupData) {
            // Set edit mode
            $('#followup_id').val(followupId);
            $('#addFollowupModalLabel').html('<i class="bi bi-pencil"></i> Edit Followup');
            $('#followupSubmitBtn').text('Update Followup');
            
            // Populate form with followup data
            $('#followup_amount_paid').val(followupData.amount_paid || '');
            $('#followup_payment_date').val(followupData.payment_date ? followupData.payment_date.split('T')[0] : '');
            $('#followup_next_followup_date').val(followupData.next_followup_date ? followupData.next_followup_date.split('T')[0] : '');
            $('#followup_notes').val(followupData.notes || '');
            $('#followup_invoice_id').val(followupData.invoice_id || invoiceId);
            
            // Show modal
            $('#addFollowupModal').modal('show');
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert('Error: ' + xhr.responseJSON.message);
            } else {
                alert('Error loading followup data');
            }
        }
    });
});

// Handle followup form submission
$('#addFollowupForm').on('submit', function(e) {
    e.preventDefault();
    
    const followupId = $('#followup_id').val();
    const isEdit = followupId && followupId !== '';
    
    const url = isEdit 
        ? `<?php echo e(route("invoice-followup.update", [":invoiceId", ":id"])); ?>`.replace(':invoiceId', invoiceId).replace(':id', followupId)
        : `<?php echo e(route("invoice-followup.store", ":id")); ?>`.replace(':id', invoiceId);
    
    const method = isEdit ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        type: method,
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            _method: isEdit ? 'PUT' : 'POST',
            amount_paid: $('#followup_amount_paid').val(),
            payment_date: $('#followup_payment_date').val(),
            notes: $('#followup_notes').val(),
            next_followup_date: $('#followup_next_followup_date').val()
        },
        success: function(response) {
            $('#addFollowupModal').modal('hide');
            alert(isEdit ? 'Followup updated successfully!' : 'Followup added successfully!');
            loadFollowups(currentPage);
            // Refresh summary cards
            updateSummaryCards();
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert('Error: ' + xhr.responseJSON.message);
            } else {
                alert(isEdit ? 'Error updating followup. Please try again.' : 'Error adding followup. Please try again.');
            }
        }
    });
});

// Search functionality
function searchFollowups(page = 1) {
    if (!invoiceId) return;
    
    let search = $("#search").val();

    $.ajax({
        url: `<?php echo e(route("invoice-followup.data", ":id")); ?>?search=${encodeURIComponent(search)}&page=${page}`.replace(':id', invoiceId),
        type: 'GET',
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="7" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No followups found.</p></td></tr>';
            } else {
                data.forEach(function (followup) {
                    html += `
                        <tr>
                            <td>${formatCurrency(followup.amount_paid || 0)}</td>
                            <td>${formatDate(followup.payment_date)}</td>
                            <td>${followup.transaction_id || '-'}</td>
                            <td>${followup.notes || '-'}</td>
                            <td>${formatDate(followup.next_followup_date)}</td>
                            <td>${formatDate(followup.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-followup-btn" data-followup-id="${followup.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#followups_table tbody').html(html);
            buildSimplePagination($('#paginationsearchLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationfilterLinks').hide();
            $('#paginationdateLinks').hide();
            $('#paginationsearchLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

$("#search").on("keyup", function () {
    searchFollowups(1); 
});

$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        searchFollowups(page);
    }
});

// Date filter functionality
function loadDateFilteredFollowups(from_date = '', to_date = '', followup_from_date = '', followup_to_date = '', page = 1) {
    if (!invoiceId) return;
    
    $.ajax({
        url: `<?php echo e(route("invoice-followup.data", ":id")); ?>?from_date=${from_date}&to_date=${to_date}&followup_from_date=${followup_from_date}&followup_to_date=${followup_to_date}&page=${page}`.replace(':id', invoiceId),
        type: 'GET',
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="7" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No followups found.</p></td></tr>';
            } else {
                data.forEach(function (followup) {
                    html += `
                        <tr>
                            <td>${formatCurrency(followup.amount_paid || 0)}</td>
                            <td>${formatDate(followup.payment_date)}</td>
                            <td>${followup.transaction_id || '-'}</td>
                            <td>${followup.notes || '-'}</td>
                            <td>${formatDate(followup.next_followup_date)}</td>
                            <td>${formatDate(followup.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-followup-btn" data-followup-id="${followup.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#followups_table tbody').html(html);
            buildSimplePagination($('#paginationdateLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationfilterLinks').hide();
            $('#paginationsearchLinks').hide();
            $('#paginationdateLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

$(document).on('change', '#from_date, #to_date, #followup_from_date, #followup_to_date', function () {
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    let followup_from_date = $('#followup_from_date').val();
    let followup_to_date = $('#followup_to_date').val();
    loadDateFilteredFollowups(from_date, to_date, followup_from_date, followup_to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    let followup_from_date = $('#followup_from_date').val();
    let followup_to_date = $('#followup_to_date').val();
    if (page) {
        loadDateFilteredFollowups(from_date, to_date, followup_from_date, followup_to_date, page);
    }
});

</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/invoice-followup.blade.php ENDPATH**/ ?>