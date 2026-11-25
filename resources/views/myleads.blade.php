@extends('layouts.app')

@section('title', 'My Leads')
@section('page_title', 'My Leads')

@push('styles')
<style>
  .container-fluid {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    padding: 6px 4px 20px;
    margin-right: 0;
    color: #09090B;
  }

  /* ── Top Summary KPI Cards (Nexora Micro-Dot Style matching Dashboard) ── */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 12px;
  }

  .sc {
    background-color: #FFFFFF;
    background-image: radial-gradient(#E5E7EB 1px, transparent 1px);
    background-size: 14px 14px;
    border: 1px solid #E4E7EC;
    border-radius: 16px;
    padding: 15px 16px;
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 122px;
    position: relative;
    overflow: hidden;
    text-decoration: none;
  }

  .sc:hover {
    border-color: #CBD5E1;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -6px rgba(15, 23, 42, 0.08);
  }

  .sc-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
  }

  .sc-label {
    font-size: 12.5px;
    font-weight: 500;
    color: #71717A;
    line-height: 1.3;
  }

  .sc-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
  }

  .sc-icon svg {
    width: 17px;
    height: 17px;
  }

  .sc-icon.c-amber   { background: #F97316; box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25); }
  .sc-icon.c-emerald { background: #10B981; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25); }
  .sc-icon.c-blue    { background: #2563EB; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25); }
  .sc-icon.c-violet  { background: #8B5CF6; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.25); }
  .sc-icon.c-rose    { background: #EF4444; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25); }

  .sc-val {
    font-size: 26px;
    font-weight: 700;
    color: #09090B;
    letter-spacing: -0.025em;
    line-height: 1.15;
    margin: 12px 0 10px;
    font-variant-numeric: tabular-nums;
  }

  .sc-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
  }

  .sc-pill {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid transparent;
    line-height: 1.4;
  }

  .sc-pill.p-amber   { background: #FFF7ED; color: #C2410C; border-color: #FFEDD5; }
  .sc-pill.p-emerald { background: #ECFDF5; color: #047857; border-color: #D1FAE5; }
  .sc-pill.p-blue    { background: #EFF6FF; color: #1D4ED8; border-color: #DBEAFE; }
  .sc-pill.p-violet  { background: #F5F3FF; color: #6D28D9; border-color: #EDE9FE; }
  .sc-pill.p-rose    { background: #FEF2F2; color: #B91C1C; border-color: #FEE2E2; }

  .sc-arrow {
    color: #A1A1AA;
    font-size: 13px;
    transition: transform 0.16s ease, color 0.16s ease;
  }

  .sc:hover .sc-arrow {
    color: #434AFA;
    transform: translateX(3px);
  }

  /* ── Status Breakdown Strip (Clean Soft Pastel Tints, No Dots) ── */
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(125px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
  }

  .status-card {
    background: #F8FAFC;
    border-radius: 12px;
    border: 1px solid #E4E7EC;
    padding: 10px 14px;
    min-height: 62px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
    gap: 4px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
    cursor: pointer;
  }

  .status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px -6px rgba(15, 23, 42, 0.1);
  }

  .status-card:nth-child(7n+1) { background: #FEF2F2; border-color: #FEE2E2; }
  .status-card:nth-child(7n+1) .status-card-label { color: #B91C1C; }

  .status-card:nth-child(7n+2) { background: #ECFDF5; border-color: #D1FAE5; }
  .status-card:nth-child(7n+2) .status-card-label { color: #047857; }

  .status-card:nth-child(7n+3) { background: #EFF6FF; border-color: #DBEAFE; }
  .status-card:nth-child(7n+3) .status-card-label { color: #1D4ED8; }

  .status-card:nth-child(7n+4) { background: #FFF7ED; border-color: #FFEDD5; }
  .status-card:nth-child(7n+4) .status-card-label { color: #C2410C; }

  .status-card:nth-child(7n+5) { background: #F5F3FF; border-color: #EDE9FE; }
  .status-card:nth-child(7n+5) .status-card-label { color: #6D28D9; }

  .status-card:nth-child(7n+6) { background: #FFF1F2; border-color: #FFE4E6; }
  .status-card:nth-child(7n+6) .status-card-label { color: #BE123C; }

  .status-card:nth-child(7n+7) { background: #EEF2FF; border-color: #E0E7FF; }
  .status-card:nth-child(7n+7) .status-card-label { color: #434AFA; }

  .status-card-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #52525B;
    line-height: 1.2;
  }

  .status-card-value {
    font-size: 18px;
    font-weight: 700;
    color: #09090B;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
  }

  /* ── Clean Light Nexora Filter Card ── */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(168px, 1fr));
    gap: 10px 12px;
    background: #FFFFFF;
    padding: 14px 16px;
    color: #09090B;
    border-radius: 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    margin-bottom: 14px !important;
    border: 1px solid #E4E7EC;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .filterBox > div {
    margin-bottom: 0 !important;
  }

  .filterBox .form-label-modern {
    color: #3F3F46;
    font-weight: 600;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .filterBox .form-label-modern i {
    color: #64748B;
    font-size: 12px;
  }

  .filterBox .form-control-modern {
    height: 34px;
    border: 1px solid #D4D8E1;
    border-radius: 8px;
    padding: 4px 10px;
    background: #FFFFFF;
    color: #09090B;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    font-size: 12.5px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #434AFA;
    background: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
    color: #09090B;
  }

  .filterBox .form-control-modern:hover {
    border-color: #B8C0CC;
  }

  /* ── Search + Add Toolbar ── */
  .table-search {
    width: 100%;
    margin-bottom: 12px !important;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .table-search-field {
    flex: 1;
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #FFFFFF;
    border: 1px solid #D4D8E1;
    border-radius: 10px;
    padding: 0 14px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .table-search-field:focus-within {
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
  }

  .table-search-field i {
    color: #94A3B8;
    font-size: 14px;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 13.5px;
    width: 100%;
    outline: none;
    color: #09090B;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .table-search-field input::placeholder {
    color: #9CA3AF;
  }

  .table-search-btn {
    height: 40px;
    padding: 0 18px;
    background: #434AFA;
    color: #FFFFFF;
    border: none;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.24);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .table-search-btn:hover {
    background: #3138D8;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(67, 74, 250, 0.32);
    color: #FFFFFF;
    text-decoration: none;
  }

  /* ── Clean Nexora Data Table ── */
  .modern-card {
    padding: 0;
    margin-bottom: 10px;
  }

  .data-table-card {
    border-radius: 14px;
    border: 1px solid #E4E7EC;
    background: #FFFFFF;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 14px;
    border: none;
    box-shadow: none;
    padding: 0;
    overflow-x: auto;
    background: transparent;
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 transparent;
  }

  .data-table-card .table-responsive::-webkit-scrollbar { height: 6px; }
  .data-table-card .table-responsive::-webkit-scrollbar-track { background: #F1F5F9; }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 999px; }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    margin: 0;
    font-size: 13px;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
    box-shadow: none;
  }

  .data-table-card .custom-table thead th {
    background: #F8FAFC;
    color: #52525B;
    font-size: 11px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 11px 14px;
    text-align: left;
    border-bottom: 1px solid #E4E7EC;
    position: sticky;
    top: 0;
    z-index: 5;
    box-shadow: none;
    text-shadow: none;
    white-space: nowrap;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .data-table-card .custom-table tbody td {
    font-size: 13px;
    padding: 10px 12px;
    color: #18181B;
    border-bottom: 1px solid #F1F5F9;
    text-align: left;
    vertical-align: middle;
    background: transparent;
    white-space: nowrap;
    max-width: 165px;
    overflow: hidden;
    text-overflow: ellipsis;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .data-table-card .custom-table tbody td:nth-child(1) { max-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(2) { max-width: 170px; }
  .data-table-card .custom-table tbody td:nth-child(3) { max-width: 155px; }
  .data-table-card .custom-table tbody td:nth-child(4) { max-width: 135px; }
  .data-table-card .custom-table tbody td:nth-child(5) { max-width: 125px; }
  .data-table-card .custom-table tbody td:nth-child(6) { max-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(7) { max-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(8) { max-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(9),
  .data-table-card .custom-table tbody td:nth-child(10) { max-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(11) { max-width: 165px; }
  .data-table-card .custom-table tbody td:last-child { max-width: 155px; overflow: visible; }

  .data-table-card .custom-table tbody td .remark-link {
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
  }

  .data-table-card .custom-table tbody tr {
    transition: background-color 0.12s ease;
  }

  .data-table-card .custom-table tbody tr:hover {
    background-color: #F8FAFF;
    box-shadow: none;
    transform: none;
  }

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 999px;
    background: #F4F4F5;
    color: #27272A;
    font-size: 11.5px;
    font-weight: 600;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  }

  .remark-link {
    color: #434AFA;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s ease;
  }

  .remark-link:hover {
    color: #3138D8;
    text-decoration: underline;
  }

  .assign-select {
    font-size: 12px;
    padding: 4px 8px;
    border: 1px solid #D4D8E1;
    border-radius: 7px;
    background: #FFFFFF;
    color: #18181B;
    width: 100%;
    max-width: 140px;
    cursor: pointer;
  }

  .assign-select:focus {
    outline: none;
    border-color: #434AFA;
    box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.14);
  }

  .table-range-meta {
    font-size: 12px;
    color: #71717A;
    margin: 6px 2px 10px;
  }

  /* ── Pagination ── */
  .pagination {
    gap: 4px;
  }

  .pagination .page-link {
    color: #3F3F46;
    border: 1px solid #E4E7EC;
    border-radius: 8px !important;
    padding: 5px 11px;
    font-size: 12.5px;
    font-weight: 500;
    background: #FFFFFF;
    transition: all 0.15s ease;
  }

  .pagination .page-item.active .page-link {
    background: #434AFA;
    border-color: #434AFA;
    color: #FFFFFF;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.25);
  }

  .pagination .page-link:hover {
    background: #F4F4F5;
    border-color: #D4D8E1;
    color: #09090B;
  }

  .loading-state {
    text-align: center;
    padding: 35px 20px;
    color: #64748B;
    font-size: 13px;
  }

  .loading-state i {
    font-size: 20px;
    color: #434AFA;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 35px 20px;
    color: #94A3B8;
    font-size: 13px;
  }

  .empty-state i {
    font-size: 26px;
    margin-bottom: 6px;
    color: #CBD5E1;
  }

  @media (max-width: 991px) {
    .summary-cards {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 575px) {
    .summary-cards {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <!-- Top Summary KPI Cards (Nexora Micro-Dot Style) -->
  <div class="summary-cards">
    <a href="{{ route('todayfollowupstable') }}" class="sc">
      <div class="sc-top">
        <span class="sc-label">Today's Followups</span>
        <div class="sc-icon c-amber">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
          </svg>
        </div>
      </div>
      <div class="sc-val" id="todayFollowups">0</div>
      <div class="sc-foot">
        <span class="sc-pill p-amber">Scheduled</span>
        <i class="bi bi-arrow-right sc-arrow"></i>
      </div>
    </a>

    <a href="{{ route('underprocesstable') }}" class="sc">
      <div class="sc-top">
        <span class="sc-label">Under Process</span>
        <div class="sc-icon c-emerald">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
        </div>
      </div>
      <div class="sc-val" id="underProcess">0</div>
      <div class="sc-foot">
        <span class="sc-pill p-emerald">In Pipeline</span>
        <i class="bi bi-arrow-right sc-arrow"></i>
      </div>
    </a>

    <a href="{{ route('todaycompletedtable') }}" class="sc">
      <div class="sc-top">
        <span class="sc-label">Today Completed</span>
        <div class="sc-icon c-blue">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
      </div>
      <div class="sc-val" id="todayCompleted">0</div>
      <div class="sc-foot">
        <span class="sc-pill p-blue">Done Today</span>
        <i class="bi bi-arrow-right sc-arrow"></i>
      </div>
    </a>

    <a href="{{ route('todaypendingtable') }}" class="sc">
      <div class="sc-top">
        <span class="sc-label">Today Pending</span>
        <div class="sc-icon c-violet">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
        </div>
      </div>
      <div class="sc-val" id="todayPending">0</div>
      <div class="sc-foot">
        <span class="sc-pill p-violet">Action Due</span>
        <i class="bi bi-arrow-right sc-arrow"></i>
      </div>
    </a>

    <a href="{{ route('todaynewtable') }}" class="sc">
      <div class="sc-top">
        <span class="sc-label">New Followups</span>
        <div class="sc-icon c-rose">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
        </div>
      </div>
      <div class="sc-val" id="todayNew">0</div>
      <div class="sc-foot">
        <span class="sc-pill p-rose">Fresh</span>
        <i class="bi bi-arrow-right sc-arrow"></i>
      </div>
    </a>
  </div>

  <!-- Status Cards -->
  <div class="status-cards" id="statusCardsContainer">
    <div class="status-card">
      <div class="status-card-label">Loading...</div>
      <div class="status-card-value">0</div>
    </div>
  </div>

  <!-- Clean Nexora Filter Box -->
  <div class="filterBox mb-2">
    <div>
      <label for="sales_status" class="form-label-modern">
        <i class="bi bi-tag"></i> Status
      </label>
      <select class="form-control form-control-modern" id="sales_status" name="sales_status">
        <option value="">Select Status</option>
      </select>
    </div>

    <div>
      <label for="state" class="form-label-modern">
        <i class="bi bi-geo-alt"></i> State
      </label>
      <select class="form-control form-control-modern" id="state" name="state">
        <option value="">Select State</option>
      </select>
    </div>

    <div>
      <label for="city" class="form-label-modern">
        <i class="bi bi-building"></i> City
      </label>
      <select class="form-control form-control-modern" id="city" name="city">
        <option value="">Select City</option>
      </select>
    </div>

    <div>
      <label for="business_type" class="form-label-modern">
        <i class="bi bi-briefcase"></i> Business Type
      </label>
      <select class="form-control form-control-modern" id="business_type" name="business_type">
        <option value="">Select Business Type</option>
      </select>
    </div>

    <div>
      <label for="lead_source" class="form-label-modern">
        <i class="bi bi-funnel"></i> Lead Sources
      </label>
      <select class="form-control form-control-modern" id="lead_source" name="lead_source">
        <option value="">Select Source</option>
      </select>
    </div>

    <div>
      <label for="product_type" class="form-label-modern">
        <i class="bi bi-box-seam"></i> Product Type
      </label>
      <select class="form-control form-control-modern" id="product_type" name="product_type">
        <option value="">Select Product Type</option>
      </select>
    </div>

    <div>
      <label for="from_date" class="form-label-modern">
        <i class="bi bi-calendar-event"></i> From Date
      </label>
      <input type="date" class="form-control form-control-modern" id="from_date" name="from_date">
    </div>

    <div>
      <label for="to_date" class="form-label-modern">
        <i class="bi bi-calendar-check"></i> To Date
      </label>
      <input type="date" class="form-control form-control-modern" id="to_date" name="to_date">
    </div>
  </div>

  <!-- Search + Add Toolbar -->
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search leads, contacts, emails..." />
    </div>
    <button type="button" class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addLeadModal">
      <i class="bi bi-plus-lg me-1"></i>Add Lead
    </button>
  </div>

  <!-- Table Card -->
  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="sales_table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Prospect</th>
              <th>Lead</th>
              <th>Contact Person</th>
              <th>Contact No.</th>
              <th>Remark</th>
              <th>Next Follow</th>
              <th>Address</th>
              <th>State</th>
              <th>City</th>
              <th>Email</th>
              <th>Business</th>
              <th>Source</th>
              <th>Product</th>
              <th>Ticket</th>
              <th>Assign To</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="16" class="loading-state">
                <i class="bi bi-arrow-repeat"></i>
                <p class="mt-2 mb-0">Loading leads...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="myleadsRangeInfo">
    Showing 0-0 from 0 data
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

  @include('partials.remarks-modal')

  @include('partials.add-lead-modal')
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
window.allUsers = [];

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

// Build compact pagination: "Previous [current / last] Next"
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

// Helper to build assign dropdown options
function buildAssignDropdown(recordId) {
    let dropdownOptions = '<option value="">Select User</option>';
    if (window.allUsers && window.allUsers.length > 0) {
        window.allUsers.forEach(function (u) {
            dropdownOptions += `<option value="${u.id}">${u.name}</option>`;
        });
    }
    return `
        <td>
            <select class="assign-select" data-lead-id="${recordId}" onchange="reassignLead(${recordId}, this.value)">
                ${dropdownOptions}
            </select>
        </td>
    `;
}

function renderTableRows(records) {
    if (!records || records.length === 0) {
        return '<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
    }

    let html = '';
    records.forEach(function (record) {
        let remark = '-';
        if (record.latest_remark) {
            const fullRemark = record.latest_remark.remark || '';
            const shortRemark = fullRemark.length > 18 ? fullRemark.substring(0, 18) + '...' : fullRemark;
            remark = `<a href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}" class="remark-link">${shortRemark}</a>`;
        }

        const statusName = record.status?.status_name ?? 'N/A';
        const statusBadge = `<span class="status-badge">${statusName}</span>`;

        html += `
            <tr>
                <td>${statusBadge}</td>
                <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                <td>${record.leads_name ?? ''}</td>
                <td>${record.contact_person ?? ''}</td>
                <td>${record.contact_number ?? ''}</td>
                <td>${remark}</td>
                <td>${formatDateOnly(record.next_follow_up_date)}</td>
                <td>${record.address ?? 'N/A'}</td>
                <td>${record.state?.state_name ?? 'N/A'}</td>
                <td>${record.city?.city_name ?? 'N/A'}</td>
                <td>${record.email ?? ''}</td>
                <td>${record.business_type?.business_name ?? 'N/A'}</td>
                <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                <td>${record.product?.product_name ?? 'N/A'}</td>
                <td>${record.ticket_value ?? '0'}</td>
                ${buildAssignDropdown(record.id)}
            </tr>
        `;
    });
    return html;
}

// Load summary stats
function loadSummaryStats() {
    $.ajax({
        url: '{{ route("myleads.summary-stats") }}',
        type: 'GET',
        success: function(data) {
            $('#todayFollowups').text(data.today_followups || 0);
            $('#underProcess').text(data.under_process || 0);
            $('#todayCompleted').text(data.today_completed || 0);
            $('#todayPending').text(data.today_pending || 0);
            $('#todayNew').text(data.today_new || 0);
        },
        error: function(xhr) {
            console.error("Error loading summary stats:", xhr.responseText);
        }
    });
}

// Load status counts
function loadStatusCounts() {
    $.ajax({
        url: '{{ route("myleads.status-counts") }}',
        type: 'GET',
        success: function(data) {
            const $container = $('#statusCardsContainer');
            $container.empty();
            
            if (!data || data.length === 0) {
                $container.append(`
                    <div class="status-card">
                        <div class="status-card-label">No Status Data</div>
                        <div class="status-card-value">0</div>
                    </div>
                `);
            } else {
                let hasPositive = false;
                data.forEach(function(status) {
                    if (status.count > 0) {
                        hasPositive = true;
                        $container.append(`
                            <div class="status-card">
                                <div class="status-card-label">${status.status_name}</div>
                                <div class="status-card-value">${status.count}</div>
                            </div>
                        `);
                    }
                });
                if (!hasPositive) {
                    $container.append(`
                        <div class="status-card">
                            <div class="status-card-label">Total Statuses</div>
                            <div class="status-card-value">${data.length}</div>
                        </div>
                    `);
                }
            }
        },
        error: function(xhr) {
            console.error("Error loading status counts:", xhr.responseText);
        }
    });
}

function loadMyLeads(page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            per_page: 10
        },
        success: function (data) {
            $('#sales_table tbody').html(renderTableRows(data.data));
            renderPagination(data);
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
            console.error("Error:", xhr.responseText);
            $('#sales_table tbody').html('<tr><td colspan="16" class="text-center empty-state"><i class="bi bi-exclamation-triangle"></i><p class="mt-2 mb-0">Error loading data. Please try again.</p></td></tr>');
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
    const $info = $('#myleadsRangeInfo');
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
        loadMyLeads(page);
    }
});

// Search functionality with debounce
let searchTimeout = null;
function searchMyLeads(page = 1) {
    let search = $("#search").val();

    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            search: search,
            per_page: 10
        },
        success: function (response) {
            $('#sales_table tbody').html(renderTableRows(response.data));
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

$("#search").on("input", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        searchMyLeads(1);
    }, 280);
});

$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        searchMyLeads(page);
    }
});

// Filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredMyLeads(page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            status_id: $('#sales_status').val(),
            city_id: $('#city').val(),
            state_id: $('#state').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            per_page: 10
        },
        success: function (response) {
            $('#sales_table tbody').html(renderTableRows(response.data));
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
        loadFilteredMyLeads(page);
    }
});

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
    loadFilteredMyLeads(1);
});

// Date filter functionality
function loadDateFilteredMyLeads(from_date = '', to_date = '', page = 1) {
    $.ajax({
        url: '{{ route("myleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            date_from: from_date,
            date_to: to_date,
            per_page: 10
        },
        success: function (response) {
            $('#sales_table tbody').html(renderTableRows(response.data));
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
    loadDateFilteredMyLeads(from_date, to_date, 1);
});

$(document).on('click', '#paginationdateLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    if (page) {
        loadDateFilteredMyLeads(from_date, to_date, page);
    }
});

// Load all users for reassignment dropdowns asynchronously
function loadAllUsers() {
    return $.ajax({
        url: '{{ route("fetchUsersForManager") }}',
        type: 'GET',
        success: function(response) {
            window.allUsers = response || [];
            // Update any existing assign-select dropdowns that were rendered before users loaded
            if (window.allUsers.length > 0) {
                $('.assign-select').each(function() {
                    const currentVal = $(this).val();
                    let opts = '<option value="">Select User</option>';
                    window.allUsers.forEach(function (u) {
                        opts += `<option value="${u.id}" ${currentVal == u.id ? 'selected' : ''}>${u.name}</option>`;
                    });
                    $(this).html(opts);
                });
            }
        }
    });
}

// Handle lead reassignment
function reassignLead(leadId, newUserId) {
    if (!newUserId) return;
    
    $.ajax({
        url: '{{ route("myleads.reassign") }}',
        type: 'POST',
        data: {
            lead_id: leadId,
            new_user_id: newUserId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Lead reassigned successfully!');
                loadMyLeads();
                loadSummaryStats();
                loadStatusCounts();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Error reassigning lead:', xhr.responseText);
            alert('Error reassigning lead. Please try again.');
        }
    });
}

// Filter Dropdown Loaders (Fast & Parallel)
$(document).ready(function() {
    // 1. Immediately fire lead table and stats in parallel (NO blocking chain!)
    loadMyLeads();
    loadSummaryStats();
    loadStatusCounts();
    loadAllUsers();

    // 2. Load filter options
    $.get("{{ route('getStatuses') }}", function(data) {
        let opts = '<option value="">Select Status</option>';
        $.each(data, function (key, status) {
            opts += `<option value="${status.id}">${status.status_name}</option>`;
        });
        $('#sales_status').html(opts);
    });

    $.get("{{ route('state') }}", function(states) {
        let opts = '<option value="">Select State</option>';
        $.each(states, function (id, name) {
            opts += `<option value="${id}">${name}</option>`;
        });
        $('#state').html(opts);
    });

    $.get("{{ route('getbusiness') }}", function(data) {
        let opts = '<option value="">Select Business Type</option>';
        $.each(data, function (index, type) {
            opts += `<option value="${type.id}">${type.business_name}</option>`;
        });
        $('#business_type').html(opts);
    });

    $.get("{{ route('getsource') }}", function(data) {
        let opts = '<option value="">Select Source</option>';
        $.each(data, function (index, type) {
            opts += `<option value="${type.id}">${type.source_name}</option>`;
        });
        $('#lead_source').html(opts);
    });

    $.get("{{ route('getproduct') }}", function(data) {
        let opts = '<option value="">Select Product Type</option>';
        $.each(data, function (index, type) {
            opts += `<option value="${type.id}">${type.product_name}</option>`;
        });
        $('#product_type').html(opts);
    });

    // Dynamic State -> Cities (instant & specific, not loading 10k cities on page load!)
    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.get(`/myleads/cities/${stateId}`, function(response) {
                let cityOptions = '<option value="">Select City</option>';
                (response || []).forEach(function(city) {
                    cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
                });
                $('#city').html(cityOptions);
            });
        } else {
            $('#city').html('<option value="">Select City</option>');
        }
    });
});
</script>
@endpush
