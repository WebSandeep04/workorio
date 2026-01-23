@extends('layouts.app')

@section('title', 'IndiaMART Leads')
@section('page_title', 'IndiaMART Leads')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
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

  .summary-card-icon img {
    width: 24px;
    height: 24px;
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
    color: #000;
    font-family: Montserrat;
  }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border: 1px solid #434AFA;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    margin-bottom: 0.5rem;
    font-family: Montserrat, sans-serif;
  }

  .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    text-shadow: none;
    font-family: Montserrat, sans-serif;
  }

  .form-control-modern {
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: #fff;
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
    color: #000;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  .filterBox .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: #fff;
    color: #000;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
  }

  .table-search-field {
    width: 100%;
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

  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
  }

  .table-responsive {
    border-radius: 5px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td:first-child,
  .data-table-card .custom-table tbody td:nth-child(2) {
    font-weight: 600;
    color: #111827;
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

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 60px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 180px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 180px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 120px; }

  .badge-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
  }

  .status-new { background: #434AFA; }
  .status-processing { background: #ff9966; }
  .status-converted { background: #11998e; }
  .status-junk { background: #000; }

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

  #bulk_actions {
    display: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    margin-bottom: 0.5rem;
  }

  #bulk_actions .btn {
    font-size: 10px;
  }

  .lead-checkbox {
    width: 14px;
    height: 14px;
    cursor: pointer;
    accent-color: #667eea;
  }

  .lead-checkbox:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  .btn-group .btn {
    font-size: 9px;
    padding: 0.2rem 0.4rem;
  }

  .modern-modal .modal-content {
    border: none;
    border-radius: 0;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.35);
  }

  .modern-modal .modal-header {
    background: #434afa;
    color: #fff;
    border-bottom: none;
    padding: 1.25rem 1.5rem;
    border-radius: 0;
  }

  .modern-modal .modal-header .modal-title {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.02em;
  }

  .modern-modal .modal-body {
    padding: 1.5rem;
    background: #f8f9ff;
  }

  .modern-modal .form-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #4c4f6b;
    font-weight: 600;
  }

  .modern-modal select {
    border: 1px solid #cbd5e1;
    border-radius: 0;
    padding: 0.65rem 0.75rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
  }

  .modern-modal select:focus {
    border-color: #667eea;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.25);
  }

  .modern-modal .alert {
    border-radius: 0;
    font-size: 0.85rem;
    padding: 0.65rem 0.85rem;
  }

  .modern-modal .modal-footer {
    border-top: none;
    background: #f1f3ff;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: center;
  }

  .modern-modal .btn-primary {
    background: #434afa !important;
    border: 1px solid #434afa !important;
    border-radius: 0;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    box-shadow: none;
    color: #fff !important;
  }

  .modern-modal .btn-secondary {
    border-radius: 0;
    font-weight: 600;
  }

  .modern-modal textarea {
    border: 1px solid #cbd5e1;
    border-radius: 0;
    padding: 0.65rem 0.75rem;
    transition: all 0.3s ease;
  }

  .modern-modal textarea:focus {
    border-color: #667eea;
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
  }

  .comments-section {
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    margin-bottom: 1rem;
    scrollbar-width: thin;
  }
  .comments-section::-webkit-scrollbar { width: 6px; }
  .comments-section::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
  
  .comment-item {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 0.5rem;
    margin-bottom: 0.5rem;
  }
  .comment-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
  }
  .comment-date {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
  }
  .comment-text {
    font-size: 0.8rem;
    color: #334155;
    white-space: pre-wrap;
    line-height: 1.4;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sky">
        <img src="{{ asset('img/icons/new.png') }}" alt="New Leads">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">New Leads</div>
        <div class="summary-card-value" id="newLeadsCount">0</div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-amber">
        <img src="{{ asset('img/icons/underprocess.png') }}" alt="Processing">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Processing</div>
        <div class="summary-card-value" id="processingLeadsCount">0</div>
      </div>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-emerald">
        <img src="{{ asset('img/icons/tick.png') }}" alt="Converted">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Converted</div>
        <div class="summary-card-value" id="convertedLeadsCount">0</div>
      </div>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-violet">
        <img src="{{ asset('img/icons/call.png') }}" alt="Assigned">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Assigned</div>
        <div class="summary-card-value" id="assignedLeadsCount">0</div>
      </div>
    </div>
    <div class="summary-card card-5">
      <div class="summary-card-icon icon-rose">
        <img src="{{ asset('img/icons/pending.png') }}" alt="Junk">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Junk</div>
        <div class="summary-card-value" id="junkLeadsCount">0</div>
      </div>
    </div>
  </div>

  <div class="filterBox mb-2">
    <div>
      <label class="form-label-modern" for="im_status"><i class="bi bi-tag"></i> Status</label>
      <select id="im_status" class="form-control form-control-modern">
        <option value="">All</option>
      </select>
    </div>
    <div>
      <label class="form-label-modern" for="im_query_type"><i class="bi bi-funnel"></i> Query Type</label>
      <select id="im_query_type" class="form-control form-control-modern">
        <option value="">All</option>
      </select>
    </div>
    <div>
      <label class="form-label-modern" for="im_from"><i class="bi bi-calendar-event"></i> From Date</label>
      <input id="im_from" type="date" class="form-control form-control-modern" />
    </div>
    <div>
      <label class="form-label-modern" for="im_to"><i class="bi bi-calendar-check"></i> To Date</label>
      <input id="im_to" type="date" class="form-control form-control-modern" />
    </div>
  </div>

  <div id="bulk_actions">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <span id="selected_count" class="fw-semibold" style="font-size: 10px;">0 leads selected</span>
      <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" id="bulk_assign_btn" disabled>
          <i class="bi bi-person-plus"></i> Assign Selected
        </button>
        <button class="btn btn-outline-danger btn-sm" id="bulk_junk_btn" disabled>
          <i class="bi bi-trash"></i> Mark as Junk
        </button>
      </div>
    </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input id="im_search" type="text" placeholder="Search name, mobile, email, product, message..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="im_table">
          <thead>
            <tr>
              <th width="45"><input type="checkbox" id="select_all_leads" class="lead-checkbox"></th>
              <th>Query Time</th>
              <th>Query Type</th>
              <th>Subject</th>
              <th>Product</th>
              <th>Sender</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>Company</th>
              <th>City</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="12" class="text-center py-4 text-muted">
                <i class="bi bi-arrow-repeat me-2"></i>Loading IndiaMART leads...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="indiamartLeadsRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="im_pagination"></ul>
</div>
@endsection

@push('scripts')
<script>
(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const perPage = 10;
  let currentPage = 1;
  let searchTimer = null;
  let usersCache = null;

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

  function updateRangeInfo(from, to, total) {
    const $info = $('#indiamartLeadsRangeInfo');
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

  function statusBadge(status) {
    const key = (status || 'new').toString().trim().toLowerCase();
    const map = {
      'processing': 'status-processing',
      'converted': 'status-converted',
      'junk': 'status-junk',
      'new': 'status-new'
    };
    const cls = map[key] || 'status-new';
    const label = status ? status : 'new';
    return `<span class="badge-status ${cls}">${label}</span>`;
  }

  function formatDateTime(value) {
    if (!value) {
      return 'N/A';
    }
    const d = new Date(value);
    if (isNaN(d.getTime())) {
      return value;
    }
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd} ${hh}:${min}`;
  }

  function renderTableRows(rows) {
    if (!rows.length) {
      return '<tr><td colspan="12" class="text-center py-4 text-muted">No records found.</td></tr>';
    }

    return rows.map(function (r) {
      const canSelect = !(r.is_processed || (r.status && r.status.toLowerCase() === 'junk'));
      const canAssign = !(r.is_processed || (r.status && r.status.toLowerCase() === 'junk'));
      const canJunk = !(r.is_processed || r.sales_record_id);

      return `
        <tr>
          <td>
            <input type="checkbox"
                   class="lead-checkbox"
                   data-lead-id="${r.id}"
                   ${canSelect ? '' : 'disabled'}
                   ${!canSelect ? 'title="Cannot select processed or junk leads"' : ''}>
          </td>
          <td>${formatDateTime(r.query_time)}</td>
          <td>${r.query_type ?? 'N/A'}</td>
          <td>${r.subject ?? 'N/A'}</td>
          <td>${r.query_product_name ?? r.product_name ?? 'N/A'}</td>
          <td>${r.sender_name ?? 'N/A'}</td>
          <td>${r.sender_mobile ?? 'N/A'}</td>
          <td>${r.sender_email ?? 'N/A'}</td>
          <td>${r.sender_company ?? 'N/A'}</td>
          <td>${r.sender_city ?? 'N/A'}</td>
          <td>${statusBadge(r.status)}</td>
          <td>
            <div class="btn-group">
            <div class="btn-group">
              <button class="btn btn-sm text-white im-assign-btn" style="background:#434AFA; border:1px solid #434AFA;" data-lead-id="${r.id}" ${canAssign ? '' : 'disabled'}>Assign</button>
              <button class="btn btn-sm text-danger bg-white im-junk-btn" style="border:1px solid #434AFA;" data-lead-id="${r.id}" ${canJunk ? '' : 'disabled'}>Junk</button>
              <button class="btn btn-sm text-info bg-white im-followup-btn" style="border:1px solid #434AFA;" data-lead-id="${r.id}" title="Follow Up"><i class="bi bi-chat-text" style="color:#434AFA !important;"></i></button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function loadLeads(page = 1) {
    const params = {
      per_page: perPage,
      status: $('#im_status').val(),
      query_type: $('#im_query_type').val(),
      date_from: $('#im_from').val(),
      date_to: $('#im_to').val(),
      search: $('#im_search').val()
    };

    $.ajax({
      url: '{{ route("indiamart.fetch") }}?page=' + page,
      type: 'GET',
      data: params,
      success: function (resp) {
        $('#im_table tbody').html(renderTableRows(resp.data || []));
        buildSimplePagination($('#im_pagination'), resp.current_page || 1, resp.last_page || 1);
        resetSelections();
        updateRangeInfo(resp.from, resp.to, resp.total);
      },
      error: function () {
        $('#im_table tbody').html('<tr><td colspan="12" class="text-center py-4 text-danger">Failed to load leads.</td></tr>');
      }
    });
  }

  function loadSummaryStats() {
    $.get('{{ route("indiamart.summary-stats") }}', function (data) {
      $('#newLeadsCount').text(data.new_leads ?? 0);
      $('#processingLeadsCount').text(data.processing_leads ?? 0);
      $('#convertedLeadsCount').text(data.converted_leads ?? 0);
      $('#assignedLeadsCount').text(data.assigned_leads ?? 0);
      $('#junkLeadsCount').text(data.junk_leads ?? 0);
    });
  }

  function loadFilterOptions() {
    $.ajax({
      url: '{{ route("indiamart.filter-options") }}',
      type: 'GET',
      success: function (resp) {
        const statuses = resp.statuses || [];
        let statusOpts = '<option value="">All</option>';
        statuses.forEach(function (s) { statusOpts += `<option value="${s}">${s}</option>`; });
        $('#im_status').html(statusOpts);

        const queryTypes = resp.query_types || [];
        let qtOpts = '<option value="">All</option>';
        queryTypes.forEach(function (q) { qtOpts += `<option value="${q}">${q}</option>`; });
        $('#im_query_type').html(qtOpts);
      }
    });
  }

  function fetchUsers() {
    if (usersCache) {
      return Promise.resolve(usersCache);
    }
    return $.get('{{ route("fetchUsersForManager") }}').then(function (users) {
      usersCache = users || [];
      return usersCache;
    });
  }

  function updateSelectionUI() {
    const checkedBoxes = $('.lead-checkbox:checked');
    const totalBoxes = $('.lead-checkbox:not(:disabled)');
    const count = checkedBoxes.length;

    $('#selected_count').text(`${count} leads selected`);

    if (count > 0) {
      $('#bulk_actions').show();
      $('#bulk_assign_btn, #bulk_junk_btn').prop('disabled', false);
    } else {
      $('#bulk_actions').hide();
      $('#bulk_assign_btn, #bulk_junk_btn').prop('disabled', true);
    }

    if (count === 0) {
      $('#select_all_leads').prop('indeterminate', false).prop('checked', false);
    } else if (count === totalBoxes.length) {
      $('#select_all_leads').prop('indeterminate', false).prop('checked', true);
    } else {
      $('#select_all_leads').prop('indeterminate', true);
    }
  }

  function resetSelections() {
    $('#select_all_leads').prop('checked', false).prop('indeterminate', false);
    $('.lead-checkbox').prop('checked', false);
    updateSelectionUI();
  }

  $('#im_status, #im_query_type, #im_from, #im_to').on('change', function () {
    currentPage = 1;
    loadLeads(1);
    loadSummaryStats();
  });

  $('#im_search').on('keyup', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      currentPage = 1;
      loadLeads(1);
    }, 250);
  });

  $(document).on('click', '#im_pagination .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
      currentPage = page;
      loadLeads(page);
    }
  });

  $('#select_all_leads').on('change', function () {
    const isChecked = $(this).is(':checked');
    $('.lead-checkbox:not(:disabled)').prop('checked', isChecked);
    updateSelectionUI();
  });

  $(document).on('change', '.lead-checkbox', function () {
    updateSelectionUI();
  });

  $('#bulk_assign_btn').on('click', function () {
    const selectedIds = $('.lead-checkbox:checked').map(function () {
      return $(this).data('lead-id');
    }).get();

    if (!selectedIds.length) {
      alert('Please select leads to assign.');
      return;
    }

    $('#assign_lead_id').val(selectedIds.join(','));
    $('#assign_modal_title').text(`Assign ${selectedIds.length} IndiaMART Lead(s)`);
    $('#assign_error').addClass('d-none').text('');
    $('#assign_success').addClass('d-none');
    $('#assign_user_id').html('<option value="">Loading...</option>');

    fetchUsers().then(function (users) {
      let options = '<option value="">Select user</option>';
      (users || []).forEach(function (u) { options += `<option value="${u.id}">${u.name}</option>`; });
      $('#assign_user_id').html(options);
    });

    new bootstrap.Modal(document.getElementById('assignModal')).show();
  });

  $('#bulk_junk_btn').on('click', function () {
    const selectedIds = $('.lead-checkbox:checked').map(function () {
      return $(this).data('lead-id');
    }).get();

    if (!selectedIds.length) {
      alert('Please select leads to mark as junk.');
      return;
    }

    if (!confirm(`Mark ${selectedIds.length} selected lead(s) as junk? This action cannot be undone.`)) {
      return;
    }

    const $btn = $(this);
    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
      url: '{{ route("indiamart.bulk-junk") }}',
      type: 'POST',
      data: {
        lead_ids: selectedIds
      },
      success: function (resp) {
        $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Mark as Junk');
        if (resp && resp.success) {
          alert(`Successfully marked ${selectedIds.length} lead(s) as junk.`);
          resetSelections();
          loadLeads(currentPage);
          loadSummaryStats();
        } else {
          alert(resp.message || 'Failed to mark leads as junk.');
        }
      },
      error: function (xhr) {
        $btn.prop('disabled', false).html('<i class="bi bi-trash"></i> Mark as Junk');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to mark leads as junk.';
        alert(msg);
      }
    });
  });

  $(document).on('click', '.im-assign-btn', function () {
    const leadId = $(this).data('lead-id');
    $('#assign_lead_id').val(leadId);
    $('#assign_modal_title').text('Assign IndiaMART Lead');
    $('#assign_error').addClass('d-none').text('');
    $('#assign_success').addClass('d-none');
    $('#assign_user_id').html('<option value="">Loading...</option>');

    fetchUsers().then(function (users) {
      let options = '<option value="">Select user</option>';
      (users || []).forEach(function (u) { options += `<option value="${u.id}">${u.name}</option>`; });
      $('#assign_user_id').html(options);
    });

    new bootstrap.Modal(document.getElementById('assignModal')).show();
  });

  $(document).on('click', '#assign_submit_btn', function () {
    const leadId = $('#assign_lead_id').val();
    const userId = $('#assign_user_id').val();

    if (!userId) {
      $('#assign_error').removeClass('d-none').text('Please select a user.');
      return;
    }

    $('#assign_error').addClass('d-none').text('');
    $(this).prop('disabled', true).text('Assigning...');

    const isMultiple = leadId.includes(',');
    const route = isMultiple ? '{{ route("indiamart.bulk-assign") }}' : '{{ route("indiamart.assign") }}';
    const data = isMultiple ? {
      lead_ids: leadId.split(','),
      user_id: userId
    } : {
      lead_id: leadId,
      user_id: userId
    };

    $.ajax({
      url: route,
      type: 'POST',
      data: data,
      success: function (resp) {
        $('#assign_submit_btn').prop('disabled', false).text('Assign Now');
        if (resp && resp.success) {
          $('#assign_success').removeClass('d-none').text(isMultiple ? `${leadId.split(',').length} leads assigned successfully.` : 'Lead assigned successfully.');
          if (isMultiple) {
            resetSelections();
          }
          setTimeout(function () {
            bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
            loadLeads(currentPage);
            loadSummaryStats();
          }, 600);
        } else {
          $('#assign_error').removeClass('d-none').text(resp.message || 'Failed to assign.');
        }
      },
      error: function (xhr) {
        $('#assign_submit_btn').prop('disabled', false).text('Assign Now');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to assign.';
        $('#assign_error').removeClass('d-none').text(msg);
      }
    });
  });

  $(document).on('click', '.im-junk-btn', function () {
    const leadId = $(this).data('lead-id');
    $('#junk_lead_id').val(leadId);
    $('#junk_reason').val('');
    $('#junk_error').addClass('d-none').text('');
    new bootstrap.Modal(document.getElementById('junkModal')).show();
  });

  $(document).on('click', '#junk_submit_btn', function () {
    const leadId = $('#junk_lead_id').val();
    const reason = ($('#junk_reason').val() || '').trim();
    if (!reason) {
      $('#junk_error').removeClass('d-none').text('Please enter a junk reason.');
      return;
    }
    $('#junk_error').addClass('d-none').text('');
    $(this).prop('disabled', true).text('Saving...');

    $.ajax({
      url: '{{ route("indiamart.junk") }}',
      type: 'POST',
      data: {
        lead_id: leadId,
        junk_reason: reason
      },
      success: function (resp) {
        $('#junk_submit_btn').prop('disabled', false).text('Mark as Junk');
        if (resp && resp.success) {
          bootstrap.Modal.getInstance(document.getElementById('junkModal')).hide();
          loadLeads(currentPage);
          loadSummaryStats();
        } else {
          $('#junk_error').removeClass('d-none').text(resp.message || 'Failed to mark as junk.');
        }
      },
      error: function (xhr) {
        $('#junk_submit_btn').prop('disabled', false).text('Mark as Junk');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to mark as junk.';
        $('#junk_error').removeClass('d-none').text(msg);
      }
    });
  });

  /* Follow Up Logic */
  $(document).on('click', '.im-followup-btn', function() {
    const leadId = $(this).data('lead-id');
    $('#followup_lead_id').val(leadId);
    $('#followup_comment').val('');
    $('#new_followup_error').addClass('d-none').text('');
    
    // Load existing comments
    $('#followup_history').html('<div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...</div>');
    
    // Use a placeholder that we replace
    const url = '{{ route("indiamart.get-followups", ["lead" => "LEAD_ID"]) }}'.replace('LEAD_ID', leadId);
    
    $.ajax({
      url: url,
      type: 'GET',
      success: function(resp) {
        if(resp.success && resp.data && resp.data.length > 0) {
            let html = '';
            resp.data.forEach(function(c) {
                html += `
                    <div class="comment-item p-3 bg-white rounded shadow-sm border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                             <div class="comment-date text-muted small fw-bold">${c.created_at ? new Date(c.created_at).toLocaleString('en-IN') : 'N/A'}</div>
                        </div>
                        <div class="comment-text text-dark" style="font-size: 0.9rem;">${c.comment}</div>
                    </div>
                `;
            });
            $('#followup_history').html(html);
        } else {
            $('#followup_history').html('<div class="text-center text-muted py-3" style="font-size:0.8rem;">No comments yet.</div>');
        }
      },
      error: function() {
        $('#followup_history').html('<div class="text-center text-danger py-3" style="font-size:0.8rem;">Failed to load comments.</div>');
      }
    });

    new bootstrap.Modal(document.getElementById('followupModal')).show();
  });

  $(document).on('click', '#followup_submit_btn', function() {
    const leadId = $('#followup_lead_id').val();
    const comment = $('#followup_comment').val().trim();
    
    if(!comment) {
        $('#new_followup_error').removeClass('d-none').text('Please enter a comment.');
        return;
    }
    
    const $btn = $(this);
    // Show spinner icon only
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>');
    
    $.ajax({
        url: '{{ route("indiamart.store-followup") }}',
        type: 'POST',
        data: { lead_id: leadId, comment: comment },
        success: function(resp) {
            // Revert to send icon
            $btn.prop('disabled', false).html('<i class="bi bi-send-fill text-white"></i>');
            if(resp.success) {
                // Refresh comments
                let newComment = `
                    <div class="comment-item p-3 bg-white rounded shadow-sm border border-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                             <div class="comment-date text-success small fw-bold">Just now</div>
                        </div>
                        <div class="comment-text text-dark" style="font-size: 0.9rem;">${comment}</div>
                    </div>
                `;
                 // Remove "No comments" message if it exists
                if($('#followup_history .text-center').length) {
                    $('#followup_history').html(newComment);
                } else {
                    $('#followup_history').prepend(newComment);
                }
                
                $('#followup_comment').val('');
            } else {
                $('#new_followup_error').removeClass('d-none').text(resp.message);
            }
        },
        error: function(xhr) {
             // Revert to send icon
             $btn.prop('disabled', false).html('<i class="bi bi-send-fill text-white"></i>');
             const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error saving comment';
             $('#new_followup_error').removeClass('d-none').text(msg);
        }
    });
  });

  $(document).ready(function () {
    loadFilterOptions();
    loadSummaryStats();
    loadLeads(1);
  });
})();
</script>

<!-- Assign Modal -->
<div class="modal fade modern-modal" id="assignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-zoom">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <p class="mb-1 text-uppercase text-white-50" style="font-size:0.7rem; letter-spacing:0.2em;">Assignment</p>
          <h5 class="modal-title" id="assign_modal_title">Assign IndiaMART Lead</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="assign_lead_id" />
        <div class="mb-3">
          <label class="form-label">Choose team member</label>
          <select id="assign_user_id" class="form-select">
            <option value="">Loading...</option>
          </select>
          <small class="text-muted d-block mt-2" style="font-size:0.75rem;">Selected lead(s) will immediately move to this user’s queue.</small>
        </div>
        <div class="alert alert-danger d-none" id="assign_error"></div>
        <div class="alert alert-success d-none" id="assign_success">Lead assigned successfully.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary px-5" id="assign_submit_btn">
          <i class="bi bi-send-fill me-1"></i>Assign Now
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Junk Modal -->
<div class="modal fade modern-modal junk-modal" id="junkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-zoom">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <p class="mb-1 text-uppercase text-white-50" style="font-size:0.7rem; letter-spacing:0.2em;">Cleanup</p>
          <h5 class="modal-title">Mark Lead as Junk</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="junk_lead_id" />
        <div class="mb-3">
          <label class="form-label">Why mark as junk?</label>
          <textarea id="junk_reason" class="form-control" rows="3" placeholder="Share a quick reason..."></textarea>
          <small class="text-muted d-block mt-2" style="font-size:0.75rem;">This reason helps the team keep IndiaMART data healthy.</small>
        </div>
        <div class="alert alert-danger d-none" id="junk_error"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary px-5" id="junk_submit_btn">Mark as Junk</button>
      </div>
    </div>
  </div>
</div>
<!-- Follow Up Modal -->
<!-- Follow Up Modal -->
<div class="modal fade" id="followupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content" style="height: 75vh; overflow: hidden; border-radius: 0; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
      <div class="modal-header py-2 text-white" style="background: #434AFA; border-bottom: 1px solid rgba(255,255,255,0.1); border-radius: 0;">
        <h6 class="modal-title mb-0" style="font-size: 0.95rem; font-weight: 600;">External Lead Followups</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
      </div>
      <div class="modal-body p-0 d-flex flex-column bg-light">
        <input type="hidden" id="followup_lead_id" />
        
        <!-- Top Section: History (Flex Grow to take available space) -->
        <div class="flex-grow-1 p-3 overflow-auto" id="followup_history_container" style="background: #f8fafc;">
             <div id="followup_history" class="d-flex flex-column gap-3">
                <!-- Comments load here -->
             </div>
        </div>

        <!-- Bottom Section: Input (Fixed height / shrink) -->
        <div class="p-3 bg-white border-top shadow-sm" style="flex-shrink: 0; z-index: 10;">
          <div class="d-flex gap-2">
              <textarea id="followup_comment" class="form-control border-0 bg-light" rows="2" placeholder="Write a comment..." style="resize: none; font-size: 0.9rem; border-radius: 0; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);"></textarea>
              <button type="button" class="btn btn-primary px-4 d-flex align-items-center justify-content-center" id="followup_submit_btn" style="border-radius: 0; background: #434AFA; border-color: #434AFA;">
                  <i class="bi bi-send-fill text-white"></i>
              </button>
          </div>
          <div class="alert alert-danger d-none mt-2 mb-0 py-2 px-3 small" id="new_followup_error"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endpush
