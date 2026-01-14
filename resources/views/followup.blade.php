@extends('layouts.app')

@section('title', 'Follow Ups')
@section('page_title', 'Follow Ups')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards,
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.4rem;
    margin-bottom: 0.75rem;
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

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 20px;
    height: 20px;
    object-fit: contain;
  }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card::before {
    display: none;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card.card-1,
  .summary-card.card-2,
  .summary-card.card-3,
  .summary-card.card-4,
  .summary-card.card-5 {
    background: #fff;
  }

  .status-card {
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
    flex-direction: column;
    justify-content: space-between;
  }

  .status-card::before {
    display: none;
  }

  .status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .status-card:nth-child(6n+1),
  .status-card:nth-child(6n+2),
  .status-card:nth-child(6n+3),
  .status-card:nth-child(6n+4),
  .status-card:nth-child(6n+5),
  .status-card:nth-child(6n+6),
  .status-card:nth-child(6n+7),
  .status-card:nth-child(6n+8),
  .status-card:nth-child(6n+9),
  .status-card:nth-child(6n+10),
  .status-card:nth-child(6n+11),
  .status-card:nth-child(6n+12) {
    background: #fff;
  }

  .summary-card-label {
    font-size: 7px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  .status-card-label {
    font-size: 7px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .status-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
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
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }

  .form-label-modern {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .form-control-modern option {
    color: #000;
    background: #fff;
    font-family: Montserrat, sans-serif;
  }

  .form-control-modern:focus {
    outline: none;
    border-color: white;
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: white;
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

  .table-search-btn:active {
    transform: translateY(0);
    background: #2d30b8;
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

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0px;
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
    margin-bottom: 0;
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
    background: #434AFA;
    border-radius: 999px;
  }

  .data-table-card .table-scroll {
    scrollbar-color: #434AFA #e4e7ec;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1100px;
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

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 100px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(13) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(14) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(15) { min-width: 150px; }

  .status-badge {
    display: inline-block;
    color: #000;
    font-size: 0.85rem;
    font-weight: normal;
    font-family: Montserrat, sans-serif;
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

  .table-responsive {
    border-radius: 5px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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

   @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-right: 0;
    }
    
    .summary-cards,
    .status-cards {
      grid-template-columns: repeat(2, 1fr);
    }

    .table-search {
      flex-direction: row;
      gap: 0.5rem;
    }
    
    .table-search-btn {
      width: auto;
      padding: 0.35rem 0.75rem;
    }

    .table-search-field {
        width: 100%;
    }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise">
        <img src="{{ asset('img/icons/call.png') }}" alt="Calls">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today's Follow Ups</div>
        <div class="summary-card-value" id="todayFollowups">0</div>
      </div>
      <a href="{{ route('todayfollowupstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-amber">
        <img src="{{ asset('img/icons/underprocess.png') }}" alt="Under Process">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Under Process</div>
        <div class="summary-card-value" id="underProcess">0</div>
      </div>
      <a href="{{ route('underprocesstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-emerald">
        <img src="{{ asset('img/icons/tick.png') }}" alt="Completed">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today Completed</div>
        <div class="summary-card-value" id="todayCompleted">0</div>
      </div>
      <a href="{{ route('todaycompletedtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-rose">
        <img src="{{ asset('img/icons/pending.png') }}" alt="Pending">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today Pending</div>
        <div class="summary-card-value" id="todayPending">0</div>
      </div>
      <a href="{{ route('todaypendingtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-5">
      <div class="summary-card-icon icon-sky">
        <img src="{{ asset('img/icons/new.png') }}" alt="New">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today New</div>
        <div class="summary-card-value" id="todayNew">0</div>
      </div>
      <a href="{{ route('todaynewtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <div class="status-cards" id="statusCardsContainer">
    <div class="status-card">
      <div class="status-card-label">Loading...</div>
      <div class="status-card-value">0</div>
    </div>
  </div>

  <div class="filterBox mb-2">
    <div class="mb-2">
        <label for="sales_status" class="form-label-modern">
            <i class="bi bi-tag"></i> Status
        </label>
        <select class="form-control form-control-modern" id="sales_status" name="sales_status">
            <option value="">Select</option>
        </select>
    </div>

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
            <i class="bi bi-funnel"></i> Lead Source
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
    <a href="{{ route('lead') }}" class="table-search-btn" id="addBtn">
      <i class="bi bi-plus me-1"></i>Add
    </a>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="followup_table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Prospect</th>
              <th>Lead</th>
              <th>Contact Person</th>
              <th>Contact No.</th>
              <th>Next Follow</th>
              <th>Remark</th>
              <th>Address</th>
              <th>State</th>
              <th>City</th>
              <th>Email</th>
              <th>Business</th>
              <th>Source</th>
              <th>Product</th>
              <th>Ticket</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="15" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading follow ups...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="followupRangeInfo">
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
@endsection

@push('scripts')
<script>
const perPage = 10;
let currentPage = 1;

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

function getValue(record, keys, fallback = 'N/A') {
    for (const key of keys) {
        const parts = key.split('.');
        let value = record;
        let found = true;
        for (const part of parts) {
            if (value && typeof value === 'object' && part in value) {
                value = value[part];
            } else {
                found = false;
                break;
            }
        }
        if (found && value !== null && value !== undefined && value !== '') {
            return value;
        }
    }
    return fallback;
}

function buildRemark(record) {
    const remarkSource = record.latest_remark?.remark ?? record.last_remark ?? '';
    if (!remarkSource) {
        return '-';
    }
    const shortRemark = remarkSource.length > 15 ? `${remarkSource.substring(0, 15)}...` : remarkSource;
    const safeRemark = remarkSource.replace(/"/g, '&quot;');
    return `<a href="/remark?sales_record_id=${record.id}" class="remark-link" title="${safeRemark}">${shortRemark}</a>`;
}

function buildTableRow(record) {
    const statusName = getValue(record, ['status.status_name', 'status_name']);
    const prospect = getValue(record, ['prospectus.prospectus_name', 'prospectus_name']);
    const business = getValue(record, ['business_type.business_name', 'business_name']);
    const source = getValue(record, ['lead_source.source_name', 'source_name']);
    const product = getValue(record, ['product.product_name', 'product_name']);
    const state = getValue(record, ['state.state_name', 'state_name']);
    const city = getValue(record, ['city.city_name', 'city_name']);
    const ticket = getValue(record, ['ticket_value'], '0');

    return `
        <tr>
            <td><span class="status-badge">${statusName}</span></td>
            <td>${prospect}</td>
            <td>${record.leads_name ?? 'N/A'}</td>
            <td>${record.contact_person ?? 'N/A'}</td>
            <td>${record.contact_number ?? 'N/A'}</td>
            <td>${formatDateOnly(record.next_follow_up_date)}</td>
            <td>${buildRemark(record)}</td>
            <td>${record.address ?? 'N/A'}</td>
            <td>${state}</td>
            <td>${city}</td>
            <td>${record.email ?? 'N/A'}</td>
            <td>${business}</td>
            <td>${source}</td>
            <td>${product}</td>
            <td>${ticket}</td>
        </tr>
    `;
}

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

function loadSummaryStats() {
    $.ajax({
        url: '{{ route("followup.summary-stats") }}',
        type: 'GET',
        success: function(data) {
            $('#todayFollowups').text(data.today_followups || 0);
            $('#underProcess').text(data.under_process || 0);
            $('#todayCompleted').text(data.today_completed || 0);
            $('#todayPending').text(data.today_pending || 0);
            $('#todayNew').text(data.today_new || 0);
        },
        error: function(xhr) {
            console.error('Error loading summary stats:', xhr.responseText);
        }
    });
}

function loadStatusCounts() {
    $.ajax({
        url: '{{ route("followup.status-counts") }}',
        type: 'GET',
        success: function(data) {
            const $container = $('#statusCardsContainer');
            $container.empty();
            if (!data.length) {
                $container.append(`
                    <div class="status-card">
                        <div class="status-card-label">No Status Data</div>
                        <div class="status-card-value">0</div>
                    </div>
                `);
                return;
            }

            data.forEach(function(status) {
                $container.append(`
                    <div class="status-card">
                        <div class="status-card-label">${status.status_name}</div>
                        <div class="status-card-value">${status.count}</div>
                    </div>
                `);
            });
        },
        error: function(xhr) {
            console.error('Error loading status counts:', xhr.responseText);
        }
    });
}

function renderTableBody(records) {
    if (!records.length) {
        return '<tr><td colspan="15" class="empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
    }

    return records.map(buildTableRow).join('');
}

function loadFollowupRecords(page = 1) {
    $.ajax({
        url: '{{ route("sales.records") }}',
        type: 'GET',
        data: { page, per_page: perPage },
        success: function (data) {
            $('#followup_table tbody').html(renderTableBody(data.data || []));
            renderPagination(data, '#paginationLinks');
        },
        error: function (xhr) {
            console.error('Error:', xhr.responseText);
            $('#followup_table tbody').html('<tr><td colspan="15" class="empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data.</p></td></tr>');
        }
    });
}

function renderPagination(data, selector) {
    const current = data.current_page || 1;
    const last = data.last_page || 1;
    const $target = $(selector);
    buildSimplePagination($target, current, last);
    $('#paginationLinks, #paginationfilterLinks, #paginationsearchLinks, #paginationdateLinks').hide();
    $target.show();
    updateRangeInfo(data.from, data.to, data.total);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#followupRangeInfo');
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
        loadFollowupRecords(page);
    }
});

function searchFollowups(page = 1) {
    const search = $('#search').val();
    $.ajax({
        url: '{{ route("search") }}',
        type: 'GET',
        data: { search, page, per_page: perPage },
        success: function(response) {
            const rows = renderTableBody(response.data || []);
            $('#followup_table tbody').html(rows);
            renderPagination(response, '#paginationsearchLinks');
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });
}

$('#search').on('keyup', function() {
    searchFollowups(1);
});

$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        searchFollowups(page);
    }
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredFollowups(page = 1) {
    $.ajax({
        url: '{{ route("filter") }}?page=' + page,
        type: 'POST',
        data: {
            status: $('#sales_status').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            business: $('#business_type').val(),
            source: $('#lead_source').val(),
            product: $('#product_type').val(),
            per_page: perPage
        },
        success: function(response) {
            $('#followup_table tbody').html(renderTableBody(response.data || []));
            renderPagination(response, '#paginationfilterLinks');
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Server error occurred. Check the console.');
        }
    });
}

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page) {
        loadFilteredFollowups(page);
    }
});

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
    loadFilteredFollowups(1);
});

function loadDateFilteredFollowups(from_date = '', to_date = '', page = 1) {
    $.ajax({
        url: '{{ route("filterdate") }}?page=' + page,
        type: 'POST',
        data: {
            from_date,
            to_date,
            per_page: perPage
        },
        success: function(response) {
            $('#followup_table tbody').html(renderTableBody(response.data || []));
            renderPagination(response, '#paginationdateLinks');
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Server error occurred. Check the console.');
        }
    });
}

$(document).on('change', '#from_date, #to_date', function () {
    const from_date = $('#from_date').val();
    const to_date = $('#to_date').val();
    loadDateFilteredFollowups(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    const from_date = $('#from_date').val();
    const to_date = $('#to_date').val();
    if (page) {
        loadDateFilteredFollowups(from_date, to_date, page);
    }
});

function loadFilterOptions() {
    $.ajax({
        url: "{{ route('getbusiness') }}",
        type: "GET",
        success: function (data) {
            $('#business_type').empty().append('<option value=\"\">Select</option>');
            $.each(data, function (index, type) {
                $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#business_type').html('<option value=\"\">Unable to load types</option>');
        }
    });

    $.ajax({
        url: "{{ route('getStatuses') }}",
        type: 'GET',
        success: function (data) {
            $('#sales_status').empty().append('<option value=\"\">Select</option>');
            $.each(data, function (key, status) {
                $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load sales statuses.');
        }
    });

    $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty().append('<option value=\"\">Select</option>');
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });

    $.ajax({
        url: "{{ route('getsource') }}",
        type: "GET",
        success: function (data) {
            $('#lead_source').empty().append('<option value=\"\">Select</option>');
            $.each(data, function (index, type) {
                $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
            });
        },
        error: function () {
            $('#lead_source').html('<option value=\"\">Unable to load types</option>');
        }
    });

    $.ajax({
        url: "{{ route('getproduct') }}",
        type: "GET",
        success: function (data) {
            $('#product_type').empty().append('<option value=\"\">Select</option>');
            $.each(data, function (index, type) {
                $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
            });
        },
        error: function () {
            $('#product_type').html('<option value=\"\">Unable to load types</option>');
        }
    });

    $.ajax({
        url: "{{ route('allcity') }}",
        type: "GET",
        success: function (data) {
            $('#city').empty().append('<option value=\"\">Select</option>');
            $.each(data, function (index, type) {
                $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
            });
        },
        error: function () {
            $('#city').html('<option value=\"\">Unable to load types</option>');
        }
    });

    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `/city/${stateId}`,
                type: 'GET',
                success: function(response) {
                    let cityOptions = '<option value=\"\">Select City</option>';
                    $.each(response, function(id, name) {
                        cityOptions += `<option value="${id}">${name}</option>`;
                    });
                    $('#city').html(cityOptions);
                },
                error: function() {
                    $('#city').html('<option value=\"\">Unable to load cities</option>');
                }
            });
        } else {
            $('#city').html('<option value=\"\">Select City</option>');
        }
    });
}

$(document).ready(function () {
    loadSummaryStats();
    loadStatusCounts();
    loadFollowupRecords();
    loadFilterOptions();
});
</script>
@endpush

