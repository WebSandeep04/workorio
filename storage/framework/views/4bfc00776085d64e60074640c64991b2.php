

<?php $__env->startSection('title', 'Invoices'); ?>
<?php $__env->startSection('page_title', 'Invoices - Lead Invoices'); ?>

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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
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
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .status-badge.pending {
    background: #fef3c7;
    color: #92400e;
  }

  .status-badge.paid {
    background: #d1fae5;
    color: #065f46;
  }

  .status-badge.overdue {
    background: #fee2e2;
    color: #991b1b;
  }

  .status-badge.cancelled {
    background: #f3f4f6;
    color: #374151;
  }

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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    transform: translateY(-1px);
  }

  .loading-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
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

  #invoice_products_dropdown {
    background: #fff;
  }

  #invoice_products_dropdown option {
    padding: 0.5rem;
  }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <!-- Filters -->
  <div class="filterBox mb-2">
    <div class="mb-2">
        <label for="invoice_status" class="form-label-modern">
            <i class="bi bi-tag"></i> Status
        </label>
        <select class="form-control form-control-modern" id="invoice_status" name="status">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="overdue">Overdue</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="from_date" class="form-label-modern">
            <i class="bi bi-calendar-event"></i> From Date
        </label>
        <input type="date" class="form-control form-control-modern" id="from_date" name="from_date">
    </div>

    <div class="mb-2">
        <label for="to_date" class="form-label-modern">
            <i class="bi bi-calendar-check"></i> To Date
        </label>
        <input type="date" class="form-control form-control-modern" id="to_date" name="to_date">
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search invoice number, amount..." />
    </div>
    <button type="button" class="btn btn-primary" id="addInvoiceBtn" style="margin-right: 0.5rem; background: #434AFA; border: none; white-space: nowrap;">
      <i class="bi bi-plus me-1"></i>Add Invoice
    </button>
    <button type="button" class="table-search-btn" id="payLumpsumBtn" style="background: #434AFA; border: none; color: white;">
      <i class="bi bi-cash-coin me-1"></i>Pay Lumpsum
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="invoices_table">
          <thead>
            <tr>
              <th>Invoice No.</th>
              <th>Product</th>
              <th>Amount</th>
              <th>Paid Amount</th>
              <th>Remaining Amount</th>
              <th>Due Date</th>
              <th>Status</th>
              <th>Notes</th>
              <th>Created At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="10" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading invoices...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="invoicesRangeInfo">
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

<!-- Add Invoice Modal -->
<div class="modal fade modal-modern" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 0px;">
      <div class="modal-header" style="background: #434AFA; color: white; border-radius: 0px;">
        <h5 class="modal-title" id="addInvoiceModalLabel">
          <i class="bi bi-receipt"></i> Add Invoice
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addInvoiceForm">
        <div class="modal-body" style="padding: 1.5rem;">
          <input type="hidden" id="invoice_id" name="invoice_id">
          <input type="hidden" id="invoice_sales_record_id" name="sales_record_id">
          <input type="hidden" id="invoice_customer_id" name="customer_id">
          
          <!-- Lead Details Section -->
          <div class="mb-4">
            <h6 class="mb-3" style="color: #434AFA; font-weight: 600; border-bottom: 2px solid #434AFA; padding-bottom: 0.5rem;">Lead Details</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-modern">Prospect Name</label>
                <input type="text" class="form-control form-control-modern" id="invoice_prospect_name" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Lead Name</label>
                <input type="text" class="form-control form-control-modern" id="invoice_lead_name" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="invoice_contact_person" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Contact Number</label>
                <input type="text" class="form-control form-control-modern" id="invoice_contact_number" readonly>
              </div>
              <div class="col-md-12">
                <label class="form-label-modern">Address</label>
                <textarea class="form-control form-control-modern" id="invoice_address" rows="2" readonly></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label-modern">State</label>
                <input type="text" class="form-control form-control-modern" id="invoice_state" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label-modern">City</label>
                <input type="text" class="form-control form-control-modern" id="invoice_city" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label-modern">Email</label>
                <input type="email" class="form-control form-control-modern" id="invoice_email" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Business Type</label>
                <input type="text" class="form-control form-control-modern" id="invoice_business_type" readonly>
              </div>
              <div class="col-md-12">
                <label class="form-label-modern">Products (All Close Won Leads)</label>
                <select class="form-control form-control-modern" id="invoice_products_dropdown" name="product_id">
                  <option value="">Loading products...</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Invoice Details Section -->
          <div class="mb-3">
            <h6 class="mb-3" style="color: #434AFA; font-weight: 600; border-bottom: 2px solid #434AFA; padding-bottom: 0.5rem;">Invoice Details</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="invoice_number" class="form-label-modern">Invoice Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="invoice_number" name="invoice_number" required placeholder="Enter Invoice Number">
              </div>
              <div class="col-md-6">
                <label for="invoice_amount" class="form-label-modern">Amount <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="invoice_amount" name="amount" step="0.01" min="0" required>
              </div>
              <div class="col-md-6">
                <label for="invoice_due_date" class="form-label-modern">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-modern" id="invoice_due_date" name="due_date" required>
              </div>
              <div class="col-md-12">
                <label for="invoice_notes" class="form-label-modern">Notes</label>
                <textarea class="form-control form-control-modern" id="invoice_notes" name="notes" rows="3"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 1rem 1.5rem; border-top: 1px solid #e9ecef;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="invoiceSubmitBtn" style="background: #434AFA; border: none; color: white;">Add Invoice</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Pay Lumpsum Modal -->
<div class="modal fade modal-modern" id="payLumpsumModal" tabindex="-1" aria-labelledby="payLumpsumModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="border-radius: 0px;">
      <div class="modal-header" style="background: #434AFA; color: white; border-radius: 0px;">
        <h5 class="modal-title" id="payLumpsumModalLabel">
          <i class="bi bi-cash-coin"></i> Pay Lumpsum
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="payLumpsumForm">
        <div class="modal-body" style="padding: 1.5rem;">
          <input type="hidden" id="lumpsum_customer_id" name="customer_id">
          
          <div class="mb-3">
            <h6 class="mb-3" style="color: #434AFA; font-weight: 600; border-bottom: 2px solid #434AFA; padding-bottom: 0.5rem;">Payment Details</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="lumpsum_amount" class="form-label-modern">Lumpsum Amount <span class="text-danger">*</span></label>
                <input type="number" class="form-control form-control-modern" id="lumpsum_amount" name="amount" step="0.01" min="0.01" required>
              </div>
              <div class="col-md-6">
                <label for="lumpsum_payment_date" class="form-label-modern">Payment Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-modern" id="lumpsum_payment_date" name="payment_date" value="<?php echo e(date('Y-m-d')); ?>" required>
              </div>
              <div class="col-md-6">
                <label for="lumpsum_next_followup_date" class="form-label-modern">Next Followup Date</label>
                <input type="date" class="form-control form-control-modern" id="lumpsum_next_followup_date" name="next_followup_date">
              </div>
              <div class="col-md-12">
                <label for="lumpsum_notes" class="form-label-modern">Notes</label>
                <textarea class="form-control form-control-modern" id="lumpsum_notes" name="notes" rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="mb-3" id="lumpsum_invoice_preview" style="display: none;">
            <h6 class="mb-3" style="color: #434AFA; font-weight: 600; border-bottom: 2px solid #434AFA; padding-bottom: 0.5rem;">Payment Distribution Preview</h6>
            <div id="lumpsum_preview_content" class="table-responsive" style="max-height: 200px; overflow-y: auto;">
              <!-- Preview will be populated here -->
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 1rem 1.5rem; border-top: 1px solid #e9ecef;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" style="background: #434AFA; border: none; color: white;">Pay Lumpsum</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

let currentPage = 1;
let customerId = <?php echo json_encode($customerId, 15, 512) ?>;
let defaultSalesRecordId = <?php echo json_encode($defaultSalesRecordId, 15, 512) ?>;
let leadDataCache = {};

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

// Load invoices
function loadInvoices(page = 1) {
    if (!customerId) {
        $('#invoices_table tbody').html('<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">No customer ID provided.</p></td></tr>');
        return;
    }
    
    $.ajax({
        url: `<?php echo e(route("invoices.index")); ?>?customer_id=${customerId}&page=${page}`,
        type: 'GET',
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No invoices found.</p></td></tr>';
            } else {
                data.data.forEach(function (invoice) {
                    const statusClass = invoice.status || 'pending';
                    const statusBadge = `<span class="status-badge ${statusClass}">${(invoice.status || 'pending').charAt(0).toUpperCase() + (invoice.status || 'pending').slice(1)}</span>`;
                    
                    html += `
                        <tr>
                            <td>${invoice.invoice_number || 'N/A'}</td>
                            <td>${invoice.product_name || 'N/A'}</td>
                            <td>${formatCurrency(invoice.amount || 0)}</td>
                            <td>${formatCurrency(invoice.paid_amount || 0)}</td>
                            <td>${formatCurrency(invoice.remaining_amount || invoice.amount || 0)}</td>
                            <td>${formatDate(invoice.due_date)}</td>
                            <td>${statusBadge}</td>
                            <td>${invoice.notes || '-'}</td>
                            <td>${formatDate(invoice.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-invoice-btn" data-invoice-id="${invoice.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-right: 0.25rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="/invoice-followup/${invoice.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white;">
                                    <i class="bi bi-arrow-repeat"></i> Add Payment
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#invoices_table tbody').html(html);
            renderPagination(data);
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#invoices_table tbody').html('<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading invoices. Please try again.</p></td></tr>');
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
    const $info = $('#invoicesRangeInfo');
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
        loadInvoices(page);
    }
});

// Fetch and cache lead data by sales record id
function fetchLeadData(salesRecordId, callback) {
    if (!salesRecordId) return;
    if (leadDataCache[salesRecordId]) {
        callback && callback(leadDataCache[salesRecordId]);
        return;
    }
    $.ajax({
        url: `<?php echo e(route("payment-followup.lead", ":id")); ?>`.replace(':id', salesRecordId),
        type: 'GET',
        success: function(recordData) {
            leadDataCache[salesRecordId] = recordData;
            callback && callback(recordData);
        },
        error: function() {
            console.error('Error loading lead data');
            callback && callback(null);
        }
    });
}

// Change sales_record_id when product dropdown changes (for new invoice)
$('#invoice_products_dropdown').on('change', function() {
    const srId = $(this).find('option:selected').data('sales-record-id');
    if (srId) {
        $('#invoice_sales_record_id').val(srId);
        fetchLeadData(srId, function(data) {
            if (data) {
                populateInvoiceModal(data, false);
            }
        });
    }
});

// Load invoices on page load
$(document).ready(function () {
    // Preload default lead data if available
    if (defaultSalesRecordId) {
        fetchLeadData(defaultSalesRecordId, function(data) {
            if (data) {
                window.leadData = data;
            }
        });
    }
    
    loadInvoices();
});

// Handle Add Invoice button click
$('#addInvoiceBtn').on('click', function() {
    // Determine which sales record to use (selected product or default)
    const selectedSrId = $('#invoice_products_dropdown').find('option:selected').data('sales-record-id') || defaultSalesRecordId;
    const targetSrId = selectedSrId || defaultSalesRecordId;

    if (targetSrId) {
        fetchLeadData(targetSrId, function(recordData) {
            if (recordData) {
                populateInvoiceModal(recordData);
            } else if (window.leadData) {
                populateInvoiceModal(window.leadData);
            } else {
                alert('Unable to load lead data');
            }
        });
    } else if (window.leadData) {
        populateInvoiceModal(window.leadData);
    } else {
        alert('Unable to load lead data');
    }
});

// Function to populate invoice modal
function populateInvoiceModal(recordData, resetForm = true) {
    const recordId = recordData.id || $('#invoice_sales_record_id').val() || defaultSalesRecordId;
    
    // Reset form only if not in edit mode
    if (resetForm) {
        $('#addInvoiceForm')[0].reset();
    }
    
    // Populate lead details in modal
    $('#invoice_sales_record_id').val(recordId);
    // Set customer_id from recordData, sales record, or page customerId
    if (recordData.customer_id) {
        $('#invoice_customer_id').val(recordData.customer_id);
    } else if (recordData.directCustomer && recordData.directCustomer.id) {
        $('#invoice_customer_id').val(recordData.directCustomer.id);
    } else if (customerId) {
        $('#invoice_customer_id').val(customerId);
    }
    $('#invoice_prospect_name').val(recordData.prospectus?.prospectus_name || 'N/A');
    $('#invoice_lead_name').val(recordData.leads_name || '');
    $('#invoice_contact_person').val(recordData.contact_person || '');
    $('#invoice_contact_number').val(recordData.contact_number || '');
    $('#invoice_address').val(recordData.address || '');
    $('#invoice_state').val(recordData.state?.state_name || 'N/A');
    $('#invoice_city').val(recordData.city?.city_name || 'N/A');
    $('#invoice_email').val(recordData.email || '');
    $('#invoice_business_type').val(recordData.business_type?.business_name || 'N/A');
    
    // Populate all products from close won leads in dropdown
    const productsDropdown = $('#invoice_products_dropdown');
    productsDropdown.empty();
    
    if (recordData.all_products && recordData.all_products.length > 0) {
        productsDropdown.append('<option value="">Select Product</option>');
        recordData.all_products.forEach(function(product) {
            if (product && product.product_name) {
                const selectedAttr = product.sales_record_id == recordId ? 'selected' : '';
                productsDropdown.append(
                    `<option value="${product.id}" data-sales-record-id="${product.sales_record_id}" ${selectedAttr}>${product.product_name}</option>`
                );
            }
        });
        // Set hidden sales_record_id to selected option's sales_record_id (default to first if none selected)
        const $selected = productsDropdown.find('option:selected');
        if ($selected.length && $selected.data('sales-record-id')) {
            $('#invoice_sales_record_id').val($selected.data('sales-record-id'));
        } else {
            const $first = productsDropdown.find('option[data-sales-record-id]').first();
            if ($first.length) {
                $first.prop('selected', true);
                $('#invoice_sales_record_id').val($first.data('sales-record-id'));
            }
        }
    } else {
        productsDropdown.append('<option value="">No products found</option>');
        $('#invoice_sales_record_id').val(recordId);
    }
    
    // Show modal
    $('#addInvoiceModal').modal('show');
}

// Handle Edit Invoice button click
$(document).on('click', '.edit-invoice-btn', function() {
            const invoiceId = $(this).data('invoice-id');
    
    // Fetch invoice data
    $.ajax({
        url: `<?php echo e(route("invoices.get", ":id")); ?>`.replace(':id', invoiceId),
        type: 'GET',
        success: function(invoiceData) {
            // Set edit mode
            $('#invoice_id').val(invoiceId);
            $('#addInvoiceModalLabel').html('<i class="bi bi-pencil"></i> Edit Invoice');
            $('#invoiceSubmitBtn').text('Update Invoice');
            
            // Populate form with invoice data
            $('#invoice_number').val(invoiceData.invoice_number || '');
            $('#invoice_amount').val(invoiceData.amount || '');
            $('#invoice_due_date').val(invoiceData.due_date ? invoiceData.due_date.split('T')[0] : '');
            $('#invoice_notes').val(invoiceData.notes || '');
            $('#invoice_sales_record_id').val(invoiceData.sales_record_id || defaultSalesRecordId);
            $('#invoice_customer_id').val(invoiceData.customer_id || customerId);
            
            // Load and populate lead data for this invoice's sales record
            const srIdForInvoice = invoiceData.sales_record_id || defaultSalesRecordId;
            if (srIdForInvoice) {
                fetchLeadData(srIdForInvoice, function(recordData) {
                    if (recordData) {
                        populateInvoiceModal(recordData, false);
                        // Set product_id if invoice has one
                        if (invoiceData.product_id) {
                            $('#invoice_products_dropdown').val(invoiceData.product_id);
                            // Update sales_record_id based on selected product
                            const $selected = $('#invoice_products_dropdown').find('option:selected');
                            if ($selected.length && $selected.data('sales-record-id')) {
                                $('#invoice_sales_record_id').val($selected.data('sales-record-id'));
                            }
                        }
                    } else {
                        $('#addInvoiceModal').modal('show');
                    }
                });
            } else {
                // If no sales record, still show modal and set product if available
                if (invoiceData.product_id) {
                    $('#invoice_products_dropdown').val(invoiceData.product_id);
                }
                $('#addInvoiceModal').modal('show');
            }
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert('Error: ' + xhr.responseJSON.message);
            } else {
                alert('Error loading invoice data');
            }
        }
    });
});

// Handle invoice form submission
$('#addInvoiceForm').on('submit', function(e) {
    e.preventDefault();
    
    const invoiceId = $('#invoice_id').val();
    const isEdit = invoiceId && invoiceId !== '';
    
    const url = isEdit 
        ? `<?php echo e(route("invoices.update", ":id")); ?>`.replace(':id', invoiceId)
        : '<?php echo e(route("invoices.store")); ?>';
    
    const method = isEdit ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        type: method,
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            _method: isEdit ? 'PUT' : 'POST',
            sales_record_id: $('#invoice_sales_record_id').val(),
            customer_id: $('#invoice_customer_id').val(),
            product_id: $('#invoice_products_dropdown').val(),
            invoice_number: $('#invoice_number').val(),
            amount: $('#invoice_amount').val(),
            due_date: $('#invoice_due_date').val(),
            notes: $('#invoice_notes').val()
        },
        success: function(response) {
            $('#addInvoiceModal').modal('hide');
            alert(isEdit ? 'Invoice updated successfully!' : 'Invoice added successfully!');
            loadInvoices(currentPage);
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert('Error: ' + xhr.responseJSON.message);
            } else {
                alert(isEdit ? 'Error updating invoice. Please try again.' : 'Error adding invoice. Please try again.');
            }
        }
    });
});

// Search functionality
function searchInvoices(page = 1) {
    if (!customerId) return;
    
    let search = $("#search").val();

    $.ajax({
        url: `<?php echo e(route("invoices.index")); ?>?customer_id=${customerId}&search=${encodeURIComponent(search)}&page=${page}`,
        type: 'GET',
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No invoices found.</p></td></tr>';
            } else {
                data.forEach(function (invoice) {
                    const statusClass = invoice.status || 'pending';
                    const statusBadge = `<span class="status-badge ${statusClass}">${(invoice.status || 'pending').charAt(0).toUpperCase() + (invoice.status || 'pending').slice(1)}</span>`;
                    
                    html += `
                        <tr>
                            <td>${invoice.invoice_number || 'N/A'}</td>
                            <td>${invoice.product_name || 'N/A'}</td>
                            <td>${formatCurrency(invoice.amount || 0)}</td>
                            <td>${formatCurrency(invoice.paid_amount || 0)}</td>
                            <td>${formatCurrency(invoice.remaining_amount || invoice.amount || 0)}</td>
                            <td>${formatDate(invoice.due_date)}</td>
                            <td>${statusBadge}</td>
                            <td>${invoice.notes || '-'}</td>
                            <td>${formatDate(invoice.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-invoice-btn" data-invoice-id="${invoice.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-right: 0.25rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="/invoice-followup/${invoice.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white;">
                                    <i class="bi bi-arrow-repeat"></i> Followup
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#invoices_table tbody').html(html);
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
    searchInvoices(1); 
});

$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        searchInvoices(page);
    }
});

// Filter functionality
function loadFilteredInvoices(page = 1) {
    if (!customerId) return;
    
    $.ajax({
        url: `<?php echo e(route("invoices.index")); ?>?customer_id=${customerId}&status=${$('#invoice_status').val()}&page=${page}`,
        type: 'GET',
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No invoices found.</p></td></tr>';
            } else {
                data.forEach(function (invoice) {
                    const statusClass = invoice.status || 'pending';
                    const statusBadge = `<span class="status-badge ${statusClass}">${(invoice.status || 'pending').charAt(0).toUpperCase() + (invoice.status || 'pending').slice(1)}</span>`;
                    
                    html += `
                        <tr>
                            <td>${invoice.invoice_number || 'N/A'}</td>
                            <td>${invoice.product_name || 'N/A'}</td>
                            <td>${formatCurrency(invoice.amount || 0)}</td>
                            <td>${formatCurrency(invoice.paid_amount || 0)}</td>
                            <td>${formatCurrency(invoice.remaining_amount || invoice.amount || 0)}</td>
                            <td>${formatDate(invoice.due_date)}</td>
                            <td>${statusBadge}</td>
                            <td>${invoice.notes || '-'}</td>
                            <td>${formatDate(invoice.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-invoice-btn" data-invoice-id="${invoice.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-right: 0.25rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="/invoice-followup/${invoice.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white;">
                                    <i class="bi bi-arrow-repeat"></i> Followup
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#invoices_table tbody').html(html);
            buildSimplePagination($('#paginationfilterLinks'), response.current_page, response.last_page);
            $('#paginationLinks').hide();
            $('#paginationsearchLinks').hide();
            $('#paginationdateLinks').hide();
            $('#paginationfilterLinks').show();
            updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
        }
    });
}

$(document).on('change', '#invoice_status', function () {
    loadFilteredInvoices(1);
});

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadFilteredInvoices(page);
    }
});

// Date filter functionality
function loadDateFilteredInvoices(from_date = '', to_date = '', page = 1) {
    if (!customerId) return;
    
    $.ajax({
        url: `<?php echo e(route("invoices.index")); ?>?customer_id=${customerId}&from_date=${from_date}&to_date=${to_date}&page=${page}`,
        type: 'GET',
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="10" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No invoices found.</p></td></tr>';
            } else {
                data.forEach(function (invoice) {
                    const statusClass = invoice.status || 'pending';
                    const statusBadge = `<span class="status-badge ${statusClass}">${(invoice.status || 'pending').charAt(0).toUpperCase() + (invoice.status || 'pending').slice(1)}</span>`;
                    
                    html += `
                        <tr>
                            <td>${invoice.invoice_number || 'N/A'}</td>
                            <td>${invoice.product_name || 'N/A'}</td>
                            <td>${formatCurrency(invoice.amount || 0)}</td>
                            <td>${formatCurrency(invoice.paid_amount || 0)}</td>
                            <td>${formatCurrency(invoice.remaining_amount || invoice.amount || 0)}</td>
                            <td>${formatDate(invoice.due_date)}</td>
                            <td>${statusBadge}</td>
                            <td>${invoice.notes || '-'}</td>
                            <td>${formatDate(invoice.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-invoice-btn" data-invoice-id="${invoice.id}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-right: 0.25rem; background: #434AFA; border: none; color: white;">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <a href="/invoice-followup/${invoice.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white;">
                                    <i class="bi bi-arrow-repeat"></i> Followup
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#invoices_table tbody').html(html);
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

$(document).on('change', '#from_date, #to_date', function () {
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    loadDateFilteredInvoices(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    if (page) {
        loadDateFilteredInvoices(from_date, to_date, page);
    }
});

// Handle Pay Lumpsum button click
$('#payLumpsumBtn').on('click', function() {
    $('#lumpsum_customer_id').val(customerId);
    $('#payLumpsumForm')[0].reset();
    $('#lumpsum_customer_id').val(customerId);
    // Set default payment date to today
    const today = new Date().toISOString().split('T')[0];
    $('#lumpsum_payment_date').val(today);
    $('#lumpsum_invoice_preview').hide();
    $('#payLumpsumModal').modal('show');
});

// Preview payment distribution when amount changes
$('#lumpsum_amount').on('input', function() {
    const amount = parseFloat($(this).val());
    const customerIdVal = $('#lumpsum_customer_id').val();
    
    if (amount > 0 && customerIdVal) {
        // Fetch invoices for preview
        $.ajax({
            url: `<?php echo e(route("invoices.index")); ?>?customer_id=${customerIdVal}&per_page=1000`,
            type: 'GET',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // Sort invoices by created_at (oldest first) - same as backend
                    const sortedInvoices = response.data.sort(function(a, b) {
                        const dateA = new Date(a.created_at);
                        const dateB = new Date(b.created_at);
                        if (dateA.getTime() !== dateB.getTime()) {
                            return dateA - dateB; // Sort by date ascending
                        }
                        // If dates are same, sort by id ascending
                        return (a.id || 0) - (b.id || 0);
                    });
                    
                    let remainingAmount = amount;
                    let previewHtml = '<table class="table table-sm" style="font-size: 0.85rem;"><thead><tr><th>Invoice No.</th><th>Invoice Amount</th><th>Already Paid</th><th>Remaining</th><th>Payment</th></tr></thead><tbody>';
                    
                    sortedInvoices.forEach(function(invoice) {
                        const invoiceAmount = parseFloat(invoice.amount || 0);
                        const paidAmount = parseFloat(invoice.paid_amount || 0);
                        const remaining = invoiceAmount - paidAmount;
                        
                        let paymentAmount = 0;
                        if (remainingAmount > 0 && remaining > 0) {
                            paymentAmount = Math.min(remainingAmount, remaining);
                            remainingAmount -= paymentAmount;
                        }
                        
                        previewHtml += `<tr>
                            <td>${invoice.invoice_number || 'N/A'}</td>
                            <td>${formatCurrency(invoiceAmount)}</td>
                            <td>${formatCurrency(paidAmount)}</td>
                            <td>${formatCurrency(remaining)}</td>
                            <td><strong>${formatCurrency(paymentAmount)}</strong></td>
                        </tr>`;
                    });
                    
                    previewHtml += '</tbody></table>';
                    
                    if (remainingAmount > 0) {
                        previewHtml += `<div class="alert alert-warning mt-2" style="font-size: 0.85rem; padding: 0.5rem;">Excess amount: ${formatCurrency(remainingAmount)}</div>`;
                    }
                    
                    $('#lumpsum_preview_content').html(previewHtml);
                    $('#lumpsum_invoice_preview').show();
                } else {
                    $('#lumpsum_invoice_preview').hide();
                }
            },
            error: function() {
                $('#lumpsum_invoice_preview').hide();
            }
        });
    } else {
        $('#lumpsum_invoice_preview').hide();
    }
});

// Handle lumpsum payment form submission
$('#payLumpsumForm').on('submit', function(e) {
    e.preventDefault();
    
    const customerIdVal = $('#lumpsum_customer_id').val();
    const amount = parseFloat($('#lumpsum_amount').val());
    
    if (!customerIdVal || !amount || amount <= 0) {
        alert('Please enter a valid lumpsum amount');
        return;
    }
    
    $.ajax({
        url: '<?php echo e(route("payment-followup.pay-lumpsum")); ?>',
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            customer_id: customerIdVal,
            amount: amount,
            payment_date: $('#lumpsum_payment_date').val(),
            next_followup_date: $('#lumpsum_next_followup_date').val() || null,
            notes: $('#lumpsum_notes').val() || ''
        },
        success: function(response) {
            $('#payLumpsumModal').modal('hide');
            alert(response.message || 'Lumpsum payment processed successfully!');
            // Reload the invoices table to show updated data
            loadInvoices(currentPage);
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                alert('Error: ' + xhr.responseJSON.message);
            } else {
                alert('Error processing lumpsum payment. Please try again.');
            }
        }
    });
});


</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/invoices.blade.php ENDPATH**/ ?>