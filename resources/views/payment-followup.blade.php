@extends('layouts.app')

@section('title', 'Payment Followup')
@section('page_title', 'Payment Followup')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .status-card {
    border-radius: 8px;
    padding: 0.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: linear-gradient(135deg, #16a085 0%, #27ae60 100%);
  }

  .status-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
    background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent);
  }

  .status-card:hover {
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
  }

  .status-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    opacity: 0.95;
    flex-shrink: 0;
    line-height: 1.2;
  }

  .status-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
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

  .filterBox .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: #fff;
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

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
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
    color: #000;
    font-size: 0.85rem;
    font-weight: normal;
    font-family: Montserrat, sans-serif;
  }

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

  .remark-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .remark-link:hover {
    color: #764ba2;
    text-decoration: underline;
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

  /* Summary Cards CSS from alldata blade */
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
    padding: 0.4rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
    font-size: 1.2rem;
    color: white;
  }
  
  /* Icon Gradients */
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-green { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
  .icon-orange { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-red { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-teal { background: linear-gradient(135deg, #2dd4bf, #14b8a6); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    color: #1e293b;
    font-family: Montserrat;
  }

  /* Status cards (legacy override or coexistence) */
  /* Keeping existing status-cards styles as requested to Add summary cards, not replace everything */

  @media (max-width: 767px){
    .container-fluid{
      margin-right: 0;
    }
    .summary-cards {
      grid-template-columns: repeat(2, 1fr);
    }
    .table-search {
      flex-direction: row;

    }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card">
      <div class="summary-card-icon icon-blue">
        <i class="bi bi-receipt"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Invoices</div>
        <div class="summary-card-value" id="statsTotalInvoices">0</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('total-invoices')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-orange">
        <i class="bi bi-hourglass-split"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Pending Invoices</div>
        <div class="summary-card-value" id="statsPendingInvoices">0</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('pending-invoices')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-green">
        <i class="bi bi-check-lg"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Paid Invoices</div>
        <div class="summary-card-value" id="statsPaidInvoices">0</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('paid-invoices')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-red">
        <i class="bi bi-cash-stack"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Remaining Amount</div>
        <div class="summary-card-value" id="statsRemainingAmount">₹0.00</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('remaining-amount')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-teal">
        <i class="bi bi-wallet2"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Received Amount</div>
        <div class="summary-card-value" id="statsReceivedAmount">₹0.00</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('received-amount')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-purple">
        <i class="bi bi-graph-up-arrow"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Amount</div>
        <div class="summary-card-value" id="statsTotalAmount">₹0.00</div>
      </div>
      <a href="javascript:void(0)" onclick="openDetails('total-amount')" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <!-- Filters -->
  <div class="filterBox mb-2">

    <div class="mb-2">
        <label for="state" class="form-label-modern">
            <i class="bi bi-geo-alt"></i> State
        </label>
        <select class="form-control form-control-modern" id="state" name="state">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="city" class="form-label-modern">
            <i class="bi bi-building"></i> City
        </label>
        <select class="form-control form-control-modern" id="city" name="city">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="business_type" class="form-label-modern">
            <i class="bi bi-briefcase"></i> Business Type
        </label>
        <select class="form-control form-control-modern" id="business_type" name="business_type">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="lead_source" class="form-label-modern">
            <i class="bi bi-funnel"></i> Lead Sources
        </label>
        <select class="form-control form-control-modern" id="lead_source" name="lead_source">
            <option value="">Select</option>
        </select>
    </div>

    <div class="mb-2">
        <label for="product_type" class="form-label-modern">
            <i class="bi bi-box-seam"></i> Product Type
        </label>
        <select class="form-control form-control-modern" id="product_type" name="product_type">
            <option value="">Select</option>
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
      <input type="text" id="search" placeholder="Search leads, contacts, emails..." />
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addInvoiceModal" style="background:#434AFA; border-color:#434AFA; display:flex; align-items:center; gap:5px; white-space:nowrap;">
        <i class="bi bi-plus-lg"></i> Add Invoice
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="sales_table">
          <thead>
            <tr>
              <th>Next Payment</th>
              <th>Name</th>
              <th>Total Invoices</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading customers...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="paymentFollowupRangeInfo">
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
          <!-- Customer Selection (Crucial for Payment Followup Page) -->
          <div class="mb-4">
               <label for="invoice_customer_id" class="form-label-modern" style="color: #434AFA;">Select Customer <span class="text-danger">*</span></label>
               <select class="form-select" id="invoice_customer_id" name="customer_id" required>
                   <option value="">Select Customer</option>
               </select>
          </div>

          <!-- Lead/Customer Details Section (Readonly) -->
          <div class="mb-4">
            <h6 class="mb-3" style="color: #434AFA; font-weight: 600; border-bottom: 2px solid #434AFA; padding-bottom: 0.5rem;">Customer Details</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-modern">Name</label>
                <input type="text" class="form-control form-control-modern" id="invoice_lead_name" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Company Name</label>
                <input type="text" class="form-control form-control-modern" id="invoice_company_name" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Email</label>
                <input type="email" class="form-control form-control-modern" id="invoice_email" readonly>
              </div>
               <div class="col-md-6">
                <label class="form-label-modern">Phone</label>
                <input type="text" class="form-control form-control-modern" id="invoice_phone" readonly>
              </div>
              <div class="col-md-12">
                <label class="form-label-modern">Address</label>
                <textarea class="form-control form-control-modern" id="invoice_address" rows="2" readonly></textarea>
              </div>
              
              <div class="col-md-12">
                <label class="form-label-modern">Product (Optional)</label>
                <select class="form-control form-control-modern" id="invoice_product_id" name="product_id">
                    <option value="">Select Product</option>
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
                <input type="number" class="form-control form-control-modern" id="invoice_amount" name="amount" step="0.01" min="0" required value="0">
              </div>
              <div class="col-md-6">
                <label for="invoice_due_date" class="form-label-modern">Due Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-modern" id="invoice_due_date" name="due_date" required> 
                <!-- Intentionally left blank value -->
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

@endsection

@push('scripts')

<script>

let currentPage = 1;

// Show only date part like YYYY-MM-DD for any date-like input
function formatDateOnly(value) {
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

// Build compact pagination
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

// Helper to get filter data
function getFilterData() {
    return {
        _token: '{{ csrf_token() }}',
        state_id: $('#state').val(),
        city_id: $('#city').val(),
        business_type_id: $('#business_type').val(),
        // map lead_source to whatever the controller expects. Controller doesn't explicitly check lead_source in filterLeads but has it in getFilterOptions?
        // Wait, Steps 485/567 filterLeads checks state, city, business_type. IT DOES NOT CHECK LEAD SOURCE.
        // It checks products_id via product_type input? Controller checks `products_id`. Blade has `#product_type`.
        products_id: $('#product_type').val(), 
        date_from: $('#from_date').val(),
        date_to: $('#to_date').val(),
        search: $('#search').val(),
        per_page: 10
    };
}

function loadStats() {
    $.ajax({
        url: '{{ route("payment-followup.stats") }}',
        type: 'GET',
        data: getFilterData(),
        success: function(data) {
            $('#statsTotalInvoices').text(data.total_invoices);
            $('#statsPendingInvoices').text(data.pending_invoices);
            $('#statsPaidInvoices').text(data.paid_invoices);
            
            const formatter = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' });
            
            $('#statsRemainingAmount').text(formatter.format(data.total_remaining_amount));
            $('#statsReceivedAmount').text(formatter.format(data.received_amount));
            $('#statsTotalAmount').text(formatter.format(data.total_amount));
        },
        error: function(xhr) {
            console.error("Error loading stats:", xhr);
        }
    });
}

// Open details page with current filters
function openDetails(type) {
    const params = new URLSearchParams(getFilterData());
    // remove _token and per_page as they are not needed in URL usually, or keep them if controller ignores
    params.delete('_token'); 
    params.delete('per_page');
    
    // Construct URL
    const url = '{{ route("payment-followup.details", ["type" => ":type"]) }}'.replace(':type', type) + '?' + params.toString();
    window.location.href = url;
}

function loadPaymentFollowupLeads(page = 1) {
    const data = getFilterData();
    
    $.ajax({
        url: '{{ route("payment-followup.filter") }}?page=' + page,
        type: 'POST',
        data: data,
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="4" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.data.forEach(function (record) {
                    html += `
                        <tr>
                            <td>${record.next_followup_date_formatted ? formatDateOnly(record.next_followup_date_formatted) : 'N/A'}</td>
                            <td>${record.name ?? 'N/A'}</td>
                            <td>${record.total_invoices_count ?? 0}</td>
                            <td>
                                <a href="/invoices/${record.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white; margin-right: 0.25rem;">
                                    <i class="bi bi-receipt"></i> Raise Invoice
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
            renderPagination(data);
            updateRangeInfo(data.from, data.to, data.total);
            
            // Also load stats when table reloads
            loadStats(); 
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#sales_table tbody').html('<tr><td colspan="4" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data. Please try again.</p></td></tr>');
        }
    });
}

function openCustomerDetails(id, name) {
    // Navigate to details page filtered for this customer
    // We can reuse the details page but pass a special type or query param
    // Let's use type='total-invoices' (or 'all') and add customer_id to query params
    // OR create a new route. I'll use existing details route with customer_id param.
    const url = '{{ route("payment-followup.details", ["type" => "total-invoices"]) }}?customer_id=' + id;
    window.location.href = url;
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
    const $info = $('#paymentFollowupRangeInfo');
    if (!$info.length) return;

    const totalValue = Number(total);
    const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

    const startValue = Number(from);
    const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

    const endValue = Number(to);
    const safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

    const formattedStart = safeStart.toLocaleString('en-IN');
    const formattedEnd = safeEnd.toLocaleString('en-IN');
    const formattedTotal = safeTotal.toLocaleString('en-IN');

    $info.text(`Showing ${formattedStart}-${formattedEnd} from ${formattedTotal} data`);
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        currentPage = page;
        loadPaymentFollowupLeads(page);
    }
});

// Event listeners for filters
$(document).on('change', '#state, #city, #business_type, #lead_source, #product_type, #from_date, #to_date', function() {
    currentPage = 1;
    loadPaymentFollowupLeads(1);
});

// Search with debounce
let searchTimer;
$(document).on('keyup', '#search', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function() {
        currentPage = 1;
        loadPaymentFollowupLeads(1);
    }, 500);
});

$(document).ready(function () {
    loadPaymentFollowupLeads();
    
    // Load filter options
    $.ajax({
        url: '{{ route("payment-followup.filter-options") }}',
        type: 'GET',
        success: function(data) {
            // Load states
            $('#state').empty().append('<option value="">Select</option>');
            if (data.states) {
                data.states.forEach(function (state) {
                    $('#state').append(`<option value="${state.id}">${state.state_name}</option>`);
                });
            }

            // Load cities
            $('#city').empty().append('<option value="">Select</option>');
            if (data.cities) {
                data.cities.forEach(function (city) {
                    $('#city').append(`<option value="${city.id}">${city.city_name}</option>`);
                });
            }

            // Load business types
            $('#business_type').empty().append('<option value="">Select</option>');
            if (data.business_types) {
                data.business_types.forEach(function (type) {
                    $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
                });
            }

            // Load lead sources
            $('#lead_source').empty().append('<option value="">Select</option>');
            if (data.lead_sources) {
                data.lead_sources.forEach(function (source) {
                    $('#lead_source').append(`<option value="${source.id}">${source.source_name}</option>`);
                });
            }

            // Load products
            $('#product_type').empty().append('<option value="">Select</option>');
            if (data.products) {
                data.products.forEach(function (product) {
                    $('#product_type').append(`<option value="${product.id}">${product.product_name}</option>`);
                });
            }
        },
        error: function() {
            console.error('Failed to load filter options');
        }
    });

    // State change - load cities
    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `{{ route("payment-followup.cities", ":id") }}`.replace(':id', stateId),
                type: 'GET',
                success: function(response) {
                    let cityOptions = '<option value="">Select City</option>';
                    response.forEach(function(city) {
                        cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
                    });
                    $('#city').html(cityOptions);
                },
                error: function() {
                    $('#city').html('<option value="">Unable to load cities</option>');
                }
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });
});

// Search functionality
function searchPaymentFollowupLeads(page = 1) {
    let search = $("#search").val();

    $.ajax({
        url: '{{ route("payment-followup.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            search: search,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="14" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    html += `
                        <tr>
                            <td>${record.name ?? 'N/A'}</td>
                            <td>${record.email ?? 'N/A'}</td>
                            <td>${record.phone ?? 'N/A'}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.company_name ?? 'N/A'}</td>
                            <td>${record.created_at_formatted ?? 'N/A'}</td>
                            <td>${record.updated_at_formatted ?? 'N/A'}</td>
                            <td>
                                <a href="/invoices/${record.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white; margin-right: 0.25rem;">
                                    <i class="bi bi-receipt"></i> Raise Invoice
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
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
    searchPaymentFollowupLeads(1); 
});

$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        searchPaymentFollowupLeads(page);
    }
});

// Filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredPaymentFollowupLeads(page = 1) {
    $.ajax({
        url: '{{ route("payment-followup.filter") }}?page=' + page,
        type: 'POST',
        data: {
            city_id: $('#city').val(),
            state_id: $('#state').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="9" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    html += `
                        <tr>
                            <td>${record.next_followup_date_formatted ? formatDateOnly(record.next_followup_date_formatted) : 'N/A'}</td>
                            <td>${record.name ?? 'N/A'}</td>
                            <td>${record.email ?? 'N/A'}</td>
                            <td>${record.phone ?? 'N/A'}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.company_name ?? 'N/A'}</td>
                            <td>${record.created_at_formatted ? formatDateOnly(record.created_at_formatted) : 'N/A'}</td>
                            <td>${record.updated_at_formatted ? formatDateOnly(record.updated_at_formatted) : 'N/A'}</td>
                            <td>
                                <a href="/invoices/${record.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white; margin-right: 0.25rem;">
                                    <i class="bi bi-receipt"></i> Raise Invoice
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
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

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadFilteredPaymentFollowupLeads(page);
    }
});

$(document).on('change', '#city, #state, #business_type, #lead_source, #product_type', function () {
    loadFilteredPaymentFollowupLeads(1);
});

// Date filter functionality
function loadDateFilteredPaymentFollowupLeads(from_date = '', to_date = '', page = 1) {
    $.ajax({
        url: '{{ route("payment-followup.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            date_from: from_date,
            date_to: to_date,
            per_page: 10
        },
        success: function (response) {
            let data = response.data;
            let html = '';

            if (data.length === 0) {
                html = '<tr><td colspan="9" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
            } else {
                data.forEach(function (record) {
                    html += `
                        <tr>
                            <td>${record.next_followup_date_formatted ? formatDateOnly(record.next_followup_date_formatted) : 'N/A'}</td>
                            <td>${record.name ?? 'N/A'}</td>
                            <td>${record.email ?? 'N/A'}</td>
                            <td>${record.phone ?? 'N/A'}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.company_name ?? 'N/A'}</td>
                            <td>${record.created_at_formatted ? formatDateOnly(record.created_at_formatted) : 'N/A'}</td>
                            <td>${record.updated_at_formatted ? formatDateOnly(record.updated_at_formatted) : 'N/A'}</td>
                            <td>
                                <a href="/invoices/${record.id}" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #434AFA; border: none; text-decoration: none; color: white; margin-right: 0.25rem;">
                                    <i class="bi bi-receipt"></i> Raise Invoice
                                </a>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);
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
    loadDateFilteredPaymentFollowupLeads(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    if (page) {
        loadDateFilteredPaymentFollowupLeads(from_date, to_date, page);
    }
});



// Add Invoice Modal Logic
$('#addInvoiceModal').on('show.bs.modal', function () {
    // Clear previous values but keep dropdowns if loaded
    // (Optional: reset form if needed, but keeping product/customer loaded is good)
    
    // Load customers if not already loaded (or reload)
    $.ajax({
        url: '{{ route("payment-followup.customers") }}',
        type: 'GET',
        success: function(data) {
            let options = '<option value="">Select Customer</option>';
            data.forEach(function(customer) {
                const label = customer.company_name ? `${customer.name} (${customer.company_name})` : customer.name;
                // Store attributes for population
                const safeName = (customer.name || '').replace(/"/g, '&quot;');
                const safeCompany = (customer.company_name || '').replace(/"/g, '&quot;');
                const safeEmail = (customer.email || '').replace(/"/g, '&quot;');
                const safePhone = (customer.phone || '').replace(/"/g, '&quot;');
                const safeAddress = (customer.address || '').replace(/"/g, '&quot;');
                
                options += `<option value="${customer.id}" 
                    data-name="${safeName}" 
                    data-company="${safeCompany}" 
                    data-email="${safeEmail}" 
                    data-phone="${safePhone}" 
                    data-address="${safeAddress}"
                >${label}</option>`;
            });
            $('#invoice_customer_id').html(options);
        },
        error: function() {
            alert('Failed to load customers');
        }
    });

    // Load products
    $.ajax({
        url: '{{ route("payment-followup.products") }}',
        type: 'GET',
        success: function(data) {
            let options = '<option value="">Select Product</option>';
            data.forEach(function(product) {
                options += `<option value="${product.id}">${product.product_name}</option>`;
            });
            $('#invoice_product_id').html(options);
        },
        error: function() {
            console.error('Failed to load products');
        }
    });
});

// Populate Customer Details on selection
$('#invoice_customer_id').on('change', function() {
    const $opt = $(this).find(':selected');
    if ($opt.val()) {
        $('#invoice_lead_name').val($opt.data('name'));
        $('#invoice_company_name').val($opt.data('company'));
        $('#invoice_email').val($opt.data('email'));
        $('#invoice_phone').val($opt.data('phone'));
        $('#invoice_address').val($opt.data('address'));
    } else {
        // Clear fields
        $('#invoice_lead_name').val('');
        $('#invoice_company_name').val('');
        $('#invoice_email').val('');
        $('#invoice_phone').val('');
        $('#invoice_address').val('');
    }
});

$('#addInvoiceForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.text();
    btn.prop('disabled', true).text('Creating...');

    $.ajax({
        url: '{{ route("invoices.store") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            invoice_number: $('#invoice_number').val(),
            customer_id: $('#invoice_customer_id').val(),
            product_id: $('#invoice_product_id').val(),
            sales_record_id: null, // No lead selected
            amount: $('#invoice_amount').val(), // Read from input
            due_date: $('#invoice_due_date').val(), // Read from input
            notes: $('#invoice_notes').val() // Read from input
        },
        success: function(response) {
            $('#addInvoiceModal').modal('hide');
            $('#addInvoiceForm')[0].reset();
            alert('Invoice created successfully! Number: ' + response.invoice_number);
            loadPaymentFollowupLeads(currentPage); // Reload table
        },
        error: function(xhr) {
            let msg = 'Error creating invoice';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            alert(msg);
        },
        complete: function() {
            btn.prop('disabled', false).text(originalText);
        }
    });
});
</script>
@endpush

