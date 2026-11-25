<!-- from the new laptop2 -->
@extends('layouts.app')

@section('title', 'Sales Product')
@section('page_title', 'Sales Product')

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
    .data-table-card .custom-table tbody td:nth-child(4) { max-width: 115px; }
    .data-table-card .custom-table tbody td:nth-child(5) { max-width: 170px; }
    .data-table-card .custom-table tbody td:nth-child(6) { max-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(7) { max-width: 125px; }
    .data-table-card .custom-table tbody td:nth-child(8),
    .data-table-card .custom-table tbody td:nth-child(9) { max-width: 125px; }
    .data-table-card .custom-table tbody td:nth-child(10) { max-width: 175px; }
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

    /* ── Bulk Assignment Bar ── */
    .bulk-assignment-controls {
      background: #FFFFFF;
      border: 1px solid #E4E7EC;
      border-radius: 12px;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
      padding: 10px 14px;
      margin: 10px 0;
    }

    .bulk-assignment-controls .card {
      border: none;
      box-shadow: none;
      background: transparent;
    }

    .bulk-assignment-controls .card-body {
      padding: 0;
    }

    .bulk-assignment-controls .btn-primary {
      background: #434AFA;
      border: none;
      border-radius: 8px;
      padding: 6px 14px;
      font-size: 12.5px;
      font-weight: 600;
    }

    .bulk-assignment-controls .btn-primary:hover {
      background: #3138D8;
    }

    .lead-checkbox {
      width: 15px;
      height: 15px;
      cursor: pointer;
      accent-color: #434AFA;
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
      background: #EEF2FF;
      border-color: #C7D2FE;
      color: #434AFA;
    }

    .loading-state,
    .empty-state {
      text-align: center;
      padding: 28px 16px;
      color: #71717A;
      font-size: 13px;
    }

    .loading-state i {
      font-size: 18px;
      color: #434AFA;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    /* ── Nexora Modals ── */
    .modal-content {
      border-radius: 16px !important;
      border: 1px solid #E4E7EC;
      box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.22);
      overflow: hidden;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .modal-header {
      border-radius: 0 !important;
      background: #FFFFFF !important;
      color: #09090B;
      border-bottom: 1px solid #F1F5F9;
      padding: 14px 20px;
    }

    .modal-header .modal-title {
      color: #09090B !important;
      font-weight: 700;
      font-size: 15px !important;
    }

    .modal-header .modal-title i {
      color: #434AFA !important;
    }

    .modal-header .btn-close-white {
      filter: none !important;
    }

    .modal-body {
      padding: 18px 20px;
    }

    .modal-footer {
      border-top: 1px solid #F1F5F9;
      padding: 12px 20px;
      background: #F8FAFC;
    }

    .form-label-modern {
      color: #3F3F46;
      font-weight: 600;
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 12px;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .form-control-modern,
    .form-select-modern {
      border: 1px solid #D4D8E1;
      border-radius: 9px;
      padding: 7px 11px;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
      font-size: 13px;
      color: #09090B;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .form-control-modern:focus,
    .form-select-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.12);
      outline: none;
    }

    .btn-modern {
      padding: 8px 18px;
      border-radius: 9px;
      font-weight: 600;
      transition: all 0.15s ease;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .btn-modern-primary {
      background: #434AFA;
      color: #FFFFFF;
    }

    .btn-modern-primary:hover {
      background: #3138D8;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(67, 74, 250, 0.28);
      color: #FFFFFF;
    }

    .blur-active {
      filter: blur(2px);
      transition: filter 0.3s ease;
      pointer-events: none;
    }

    #addProspectusModal .modal-content {
      filter: none !important;
      pointer-events: auto;
    }

    .modal-backdrop.show {
      background-color: rgba(15, 23, 42, 0.5);
    }

    .prospect-search-wrapper {
      width: 215px;
      max-width: 60%;
    }

    .prospect-search-box {
      border: 1px solid #D4D8E1;
      border-radius: 8px;
      background: #F8FAFC;
      display: flex;
      align-items: center;
      height: 30px;
      padding: 0 8px;
      transition: all 0.15s ease;
    }

    .prospect-search-box:focus-within {
      background: #FFFFFF;
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.12);
    }

    .prospect-search-box input {
      border: none !important;
      outline: none !important;
      background: transparent !important;
      box-shadow: none !important;
      font-size: 12px !important;
      width: 100%;
      padding: 0 6px !important;
      color: #09090B;
      height: 100%;
    }

    .prospect-search-box .search-icon {
      color: #64748B;
      font-size: 12px;
      flex-shrink: 0;
    }

    .prospect-search-box .clear-btn {
      background: none;
      border: none;
      padding: 0 2px;
      color: #94A3B8;
      font-size: 12px;
      cursor: pointer;
      display: none;
      flex-shrink: 0;
      line-height: 1;
    }

    @media (max-width: 1199px) {
      .summary-cards {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 767px) {
      .summary-cards,
      .status-cards {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .data-table-card .custom-table tbody td {
        font-size: 12px;
      }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid px-2">
    <!-- Summary Cards (Nexora Micro-Dot Style matching Dashboard) -->
    <div class="summary-cards">
      <a href="{{ route('alldata.today-followups') }}" class="sc">
        <div class="sc-top">
          <span class="sc-label">Today's Followups</span>
          <span class="sc-icon c-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </span>
        </div>
        <div class="sc-val"><span id="todayFollowups">0</span></div>
        <div class="sc-foot">
          <span class="sc-pill p-amber">Scheduled</span>
          <span class="sc-arrow">→</span>
        </div>
      </a>

      <a href="{{ route('alldata.under-process') }}" class="sc">
        <div class="sc-top">
          <span class="sc-label">Under Process</span>
          <span class="sc-icon c-emerald">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          </span>
        </div>
        <div class="sc-val"><span id="underProcess">0</span></div>
        <div class="sc-foot">
          <span class="sc-pill p-emerald">In Pipeline</span>
          <span class="sc-arrow">→</span>
        </div>
      </a>

      <a href="{{ route('alldata.today-completed') }}" class="sc">
        <div class="sc-top">
          <span class="sc-label">Today Completed</span>
          <span class="sc-icon c-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </span>
        </div>
        <div class="sc-val"><span id="todayCompleted">0</span></div>
        <div class="sc-foot">
          <span class="sc-pill p-blue">Done Today</span>
          <span class="sc-arrow">→</span>
        </div>
      </a>

      <a href="{{ route('alldata.today-pending') }}" class="sc">
        <div class="sc-top">
          <span class="sc-label">Today Pending</span>
          <span class="sc-icon c-violet">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
        </div>
        <div class="sc-val"><span id="todayPending">0</span></div>
        <div class="sc-foot">
          <span class="sc-pill p-violet">Action Due</span>
          <span class="sc-arrow">→</span>
        </div>
      </a>

      <a href="{{ route('alldata.today-new') }}" class="sc">
        <div class="sc-top">
          <span class="sc-label">Today's New</span>
          <span class="sc-icon c-rose">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
          </span>
        </div>
        <div class="sc-val"><span id="todayNew">0</span></div>
        <div class="sc-foot">
          <span class="sc-pill p-rose">Fresh</span>
          <span class="sc-arrow">→</span>
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

    <!-- Filters - Always Visible -->
    <x-filter-panel :show-search="false" />

    <div class="table-search mb-2">
      <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search leads, contacts, emails..." />
      </div>
      <button type="button" class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addLeadModal">
        <i class="bi bi-plus me-1"></i>Add
      </button>
    </div>

    <div class="modern-card data-table-card">
      <div class="modern-card-body">
        <div class="table-responsive">
          <table class="table custom-table" id="alldatatable">
            <thead>
              <tr>
                <th>Status</th>
                <th>Prospect</th>
                <th>Remark</th>
                <th>Next Follow</th>
                <th>Lead</th>
                <th>Contact Person</th>
                <th>Contact No.</th>
                <th>State</th>
                <th>City</th>
                <th>Email</th>
                <th>Business</th>
                <th>Source</th>
                <th>Products</th>
                <th>Ticket</th>
                <th>Owner</th>
                <th>Assigned By</th>
                @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
                  <th>Assign To</th>
                @endif
              </tr>
            </thead>
            <tbody>
              <tr>
                <td
                  colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}"
                  class="loading-state">
                  <i class="bi bi-arrow-repeat"></i>
                  <p class="mt-2 mb-0">Loading sales records...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="table-range-meta" id="alldataRangeInfo">
      Showing 0-0 from 0 data
    </div>

    <!-- Bulk Assignment Controls -->
    @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
      <div class="bulk-assignment-controls" id="bulkAssignmentControls" style="display: none;">
        <div class="card">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-4">
                <span class="text-muted fw-bold" style="font-size: 10px;">
                  <i class="bi bi-check-circle"></i>
                  <span id="selectedCount">0</span> leads selected
                </span>
              </div>
              <div class="col-md-4">
                <select class="form-select" id="bulkAssignUser" style="font-size: 10px; padding: 0.25rem 0.5rem;">
                  <option value="">Select User to Assign</option>
                </select>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-primary btn-sm" id="bulkAssignBtn" disabled>
                  <i class="bi bi-people"></i>
                  Assign Selected
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clearSelectionBtn"
                  style="font-size: 10px; padding: 0.25rem 0.75rem;">
                  <i class="bi bi-x-circle"></i>
                  Clear
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
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

  <!-- Add Lead Modal -->
  <div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 1040px;">
      <div class="modal-content blur-target">
        <div class="modal-header">
          <h5 class="modal-title" id="addLeadModalLabel" style="font-size: 1rem;">
            <i class="bi bi-person-plus-fill text-white me-2"></i>Add New Lead
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-3">
          <form method="POST" action="" id="addLeadForm">
            <div class="row g-3">
              <!-- Row 1 -->
              <div class="col-md-4">
                <label for="add_lead_prospectus" class="form-label-modern">Prospect <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center gap-2">
                  <div class="flex-grow-1" style="min-width: 0;">
                    <select class="form-select form-select-modern" id="add_lead_prospectus" name="prospectus" required>
                      <option value="">Select Prospect</option>
                    </select>
                  </div>
                  <button class="btn btn-sm d-inline-flex align-items-center justify-content-center flex-shrink-0" type="button"
                    onclick="openProspectusModal()" title="Add New Prospect"
                    style="width: 38px; height: 38px; border-radius: 10px; border: 1px solid #C7D2FE; background: #EEF2FF; color: #434AFA;">
                    <i class="bi bi-plus-lg"></i>
                  </button>
                </div>
              </div>

              <div class="col-md-4">
                <label for="add_lead_leadsName" class="form-label-modern">Lead Name</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_leadsName" name="leads_name"
                  placeholder="Enter Lead Name" style="height: 38px;">
              </div>

              <div class="col-md-4">
                <label for="add_lead_contactPerson" class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_contactPerson"
                  name="contact_person" placeholder="Enter Contact Person Name" style="height: 38px;">
              </div>

              <!-- Row 2 -->
              <div class="col-md-4">
                <label for="add_lead_contactNumber" class="form-label-modern">Contact Number</label>
                <input type="tel" class="form-control form-control-modern" id="add_lead_contactNumber"
                  name="contact_number" placeholder="Enter Contact Number" style="height: 38px;">
              </div>

              <div class="col-md-4">
                <label for="add_lead_email" class="form-label-modern">Email ID</label>
                <input type="email" class="form-control form-control-modern" id="add_lead_email" name="email"
                  placeholder="Enter Email ID" style="height: 38px;">
              </div>

              <div class="col-md-4">
                <label for="add_lead_website_link" class="form-label-modern">Website</label>
                <input type="url" class="form-control form-control-modern" id="add_lead_website_link"
                  name="website_link" placeholder="Enter Website URL" style="height: 38px;">
              </div>

              <!-- Row 3 -->
              <div class="col-md-4">
                <label for="add_lead_address" class="form-label-modern">Address</label>
                <input type="text" class="form-control form-control-modern" id="add_lead_address" name="address"
                  placeholder="Enter Address" style="height: 38px;">
              </div>

              <div class="col-md-4">
                <label for="add_lead_state" class="form-label-modern">State</label>
                <select class="form-select form-select-modern" id="add_lead_state" name="state">
                  <option value="">Select State</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="add_lead_city" class="form-label-modern">City</label>
                <select class="form-select form-select-modern" id="add_lead_city" name="city">
                  <option value="">Select City</option>
                </select>
              </div>

              <!-- Row 4 -->
              <div class="col-md-4">
                <label for="add_lead_business_type" class="form-label-modern">Business Type</label>
                <select class="form-select form-select-modern" id="add_lead_business_type" name="business_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="add_lead_lead_source" class="form-label-modern">Lead Sources</label>
                <select class="form-select form-select-modern" id="add_lead_lead_source" name="lead_source">
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="add_lead_product_type" class="form-label-modern">Product Type</label>
                <select class="form-select form-select-modern" id="add_lead_product_type" name="product_type">
                  <option value="">Loading...</option>
                </select>
              </div>

              <!-- Row 5 -->
              <div class="col-md-4">
                <label for="add_lead_sales_status" class="form-label-modern">Status <span class="text-danger">*</span></label>
                <select class="form-select form-select-modern" id="add_lead_sales_status" name="sales_status" required>
                  <option value="">Loading...</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="add_lead_next_follow_up" class="form-label-modern">Next Follow-up Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-modern" id="add_lead_next_follow_up"
                  name="next_follow_up_date" required style="height: 38px;">
              </div>

              <div class="col-md-4">
                <label for="add_lead_remark" class="form-label-modern">Remark <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-modern" id="add_lead_remark" name="remark"
                  placeholder="Enter Remark" rows="1" required style="min-height: 38px;"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-sm px-3" data-bs-dismiss="modal"
            style="height: 36px; border-radius: 9px; border: 1px solid #D4D8E1; background: #FFFFFF; color: #3F3F46; font-weight: 600; font-size: 13px;">
            Cancel
          </button>
          <button type="submit" onclick="submitLead(event)"
            class="btn-modern btn-modern-primary" form="addLeadForm" style="height: 36px; padding: 0 20px;">
            <i class="bi bi-check-circle"></i> Submit Lead
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Prospectus Modal -->
  <div class="modal fade" id="addProspectusModal" tabindex="-1" aria-labelledby="addProspectusModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProspectusModalLabel" style="font-size: 1rem;">
            <i class="bi bi-building-add text-white me-2"></i>Add New Prospect
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body py-2">
          <form id="addProspectusForm">
            <div class="row g-2">
              <div class="col-md-6">
                <label for="modalnewProspectusName" class="form-label-modern">Prospect Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="modalnewProspectusName"
                  name="modal_new_prospectus_name" placeholder="Enter Prospect Name" required>
              </div>
              <div class="col-md-6">
                <label for="modal_contact_person" class="form-label-modern">Contact Person</label>
                <input type="text" class="form-control form-control-modern" id="modal_contact_person"
                  name="modal_contact_person" placeholder="Enter Contact Person">
              </div>
              <div class="col-md-6">
                <label for="modal_contact_number" class="form-label-modern">Contact Number</label>
                <input type="text" class="form-control form-control-modern" id="modal_contact_number"
                  name="modal_contact_number" placeholder="Enter Contact Number">
              </div>
              <div class="col-md-6">
                <label for="modal_address" class="form-label-modern">Address</label>
                <input type="text" class="form-control form-control-modern" id="modal_address" name="modal_address"
                  placeholder="Enter Address">
              </div>
              <div class="col-md-6">
                <label for="modal_state" class="form-label-modern">State</label>
                <select class="form-select form-select-modern" id="modal_state" name="modal_state">
                  <option value="">Select State</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="modal_city" class="form-label-modern">City</label>
                <select class="form-select form-select-modern" id="modal_city" name="modal_city">
                  <option value="">Select City</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="modal_email" class="form-label-modern">Email</label>
                <input type="email" class="form-control form-control-modern" id="modal_email" name="modal_email"
                  placeholder="Enter Email">
              </div>
              <div class="col-md-6">
                <label for="modal_website_link" class="form-label-modern">Website</label>
                <input type="url" class="form-control form-control-modern" id="modal_website_link"
                  name="modal_website_link" placeholder="Enter Website URL">
              </div>
              <div class="col-md-6">
                <label for="modal_business_type" class="form-label-modern">Business Type</label>
                <select class="form-select form-select-modern" id="modal_business_type" name="modal_business_type">
                  <option value="">Loading...</option>
                </select>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="submit" onclick="submitProspect(event)"
            class="btn-modern btn-modern-primary w-100 justify-content-center" form="addProspectusForm">
            <i class="bi bi-check-circle"></i> Save Prospect
          </button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    let currentPage = 1;

    // Load summary stats
    function loadSummaryStats() {
      $.get("{{ route('alldata.summary-stats') }}")
        .done(function (data) {
          $('#todayFollowups').text(data.today_followups || 0);
          $('#underProcess').text(data.under_process || 0);
          $('#todayCompleted').text(data.today_completed || 0);
          $('#todayPending').text(data.today_pending || 0);
          $('#todayNew').text(data.today_new || 0);
        })
        .fail(function () {
          console.error('Failed to load summary stats');
        });
    }

    // Load status counts
    function loadStatusCounts() {
      $.get("{{ route('alldata.status-counts') }}")
        .done(function (data) {
          if (!data || data.length === 0) {
            $('#statusCardsContainer').html('<div class="status-card"><div class="status-card-label">No Statuses</div><div class="status-card-value">0</div></div>');
            return;
          }

          let html = '';
          data.forEach(function (status) {
            html += `
                <div class="status-card">
                  <div class="status-card-label">${status.status_name || 'N/A'}</div>
                  <div class="status-card-value">${status.count || 0}</div>
                </div>
              `;
          });
          $('#statusCardsContainer').html(html);
        })
        .fail(function () {
          console.error('Failed to load status counts');
          $('#statusCardsContainer').html('<div class="status-card"><div class="status-card-label">Error Loading</div><div class="status-card-value">-</div></div>');
        });
    }

    function loadSalesRecords(page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Loading sales records...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '{{ route("fetchalldata") }}?page=' + page,
        type: 'GET',
        success: function (data) {
          let html = '';

          if (data.data.length === 0) {
            html = `<tr>
                      <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No Records Found</h5>
                        <p>No sales records available at the moment.</p>
                      </td>
                    </tr>`;
          } else {
            data.data.forEach(function (record, index) {
              let remark = '-';
              if (record.latest_remark) {
                const fullRemark = record.latest_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              @endif

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                <td>${record.owner_name ?? 'N/A'}</td>
                                <td>${record.creator_name ?? 'N/A'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);
          renderPagination(data);
          updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
          $('#alldatatable tbody').html(`
                  <tr>
                    <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="text-danger text-center py-4">
                      <i class="bi bi-exclamation-triangle"></i>
                      <p class="mt-2">Failed to load records. Please try again.</p>
                    </td>
                  </tr>
                `);
        }
      });
    }

    function renderPagination(data) {
      let pagination = $('#paginationLinks');
      pagination.empty();

      const current = data.current_page;
      const last = data.last_page;

      pagination.append(`
            <li class="page-item ${current === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current - 1}">
                  <i class="bi bi-chevron-left"></i> Previous
                </a>
            </li>
        `);

      pagination.append(`
            <li class="page-item active">
                <span class="page-link">${current} / ${last}</span>
            </li>
        `);

      pagination.append(`
            <li class="page-item ${current === last ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current + 1}">
                  Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `);
    }

    function updateRangeInfo(from, to, total) {
      const $info = $('#alldataRangeInfo');
      if (!$info.length) return;

      const totalValue = Number(total);
      const safeTotal = Number.isFinite(totalValue) && totalValue >= 0 ? totalValue : 0;

      const startValue = Number(from);
      const safeStart = safeTotal === 0 ? 0 : (Number.isFinite(startValue) && startValue > 0 ? startValue : 1);

      const endValue = Number(to);
      let safeEnd = safeTotal === 0 ? 0 : (Number.isFinite(endValue) && endValue >= safeStart ? endValue : safeStart);

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
        loadSalesRecords(page);
      }
    });

    $(document).ready(function () {
      loadSummaryStats();
      loadStatusCounts();
      loadSalesRecords();
      setInterval(loadSummaryStats, 60000); // Refresh stats every minute
      setInterval(loadStatusCounts, 60000); // Refresh status counts every minute
    });

    // search 

    function searchSalesTable(page = 1) {
      let search = $("#search").val();

      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Searching...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '{{ route("alldatasearch") }}?page=' + page,
        type: 'GET',
        data: { search: search },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>No Results Found</h5>
                        <p>Try adjusting your search criteria.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              const fullRemark = record.last_remark || '';
              const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;

              let assignToColumn = '';
              @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              @endif

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${fullRemark ? `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>` : '-'}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.leads_name ?? 'N/A'}</td>
                                <td>${record.contact_person ?? 'N/A'}</td>
                                <td>${record.contact_number ?? 'N/A'}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? 'N/A'}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                <td>${record.owner_name ?? 'N/A'}</td>
                                <td>${record.creator_name ?? 'N/A'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });

          $('#paginationsearchLinks').html(links);
          updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
        }
      });
    }

    $("#search").on("keyup", function () {
      $('#paginationLinks').hide();
      $('#paginationfilterLinks').hide();
      searchSalesTable(1);
    });

    $(document).on('click', '#paginationsearchLinks .page-link', function (e) {
      e.preventDefault();
      let page = $(this).data('page');
      if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        searchSalesTable(page);
      }
    });

    // get business type 
    $.ajax({
      url: "{{ route('getbusiness') }}",
      type: "GET",
      success: function (data) {
        $('#business_type').empty().append('<option value="">Select</option>');
        $.each(data, function (index, type) {
          $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
        });
      },
      error: function () {
        $('#business_type').html('<option value="">Unable to load types</option>');
      }
    });

    // get status
    $.ajax({
      url: "{{ route('getStatuses') }}",
      type: 'GET',
      success: function (data) {
        $('#sales_status').empty().append('<option value="">Select</option>');
        $.each(data, function (key, status) {
          $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
        });
      },
      error: function () {
        alert('Failed to load sales statuses.');
      }
    });

    // get state
    $(document).ready(function () {
      $.ajax({
        url: "{{ route('state') }}",
        type: "GET",
        dataType: "json",
        success: function (states) {
          let $stateDropdown = $('#state');
          $stateDropdown.empty();
          $stateDropdown.append('<option value="">Select</option>');

          $.each(states, function (id, name) {
            $stateDropdown.append(`<option value="${id}">${name}</option>`);
          });
        },
        error: function () {
          alert("Failed to load states.");
        }
      });
    });

    // get sources
    $.ajax({
      url: "{{ route('getsource') }}",
      type: "GET",
      success: function (data) {
        $('#lead_source').empty().append('<option value="">Select</option>');
        $.each(data, function (index, type) {
          $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
        });
      },
      error: function () {
        $('#lead_source').html('<option value="">Unable to load types</option>');
      }
    });

    // get product
    $.ajax({
      url: "{{ route('getproduct') }}",
      type: "GET",
      success: function (data) {
        $('#product_type').empty().append('<option value="">Select</option>');
        $.each(data, function (index, type) {
          $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
        });
      },
      error: function () {
        $('#product_type').html('<option value="">Unable to load types</option>');
      }
    });

    // get tenant sales users for dropdown
    $.ajax({
      url: "{{ route('user.sales-users') }}",
      type: "GET",
      success: function (data) {
        $('#user_id').empty().append('<option value="">All Sales Users</option>');
        $.each(data, function (index, user) {
          $('#user_id').append(`<option value="${user.id}">${user.name}</option>`);
        });
      },
      error: function () {
        $('#user_id').html('<option value="">Unable to load users</option>');
      }
    });

    // get all cities
    $.ajax({
      url: "{{ route('allcity') }}",
      type: "GET",
      success: function (data) {
        $('#city').empty().append('<option value="">Select</option>');
        $.each(data, function (index, type) {
          $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
        });
      },
      error: function () {
        $('#city').html('<option value="">Unable to load types</option>');
      }
    });

    // State change - load cities for selected state
    $('#state').on('change', function () {
      const stateId = $(this).val();
      if (stateId) {
        $.ajax({
          url: `/city/${stateId}`,
          type: 'GET',
          success: function (response) {
            let cityOptions = '<option value="">Select City</option>';
            $.each(response, function (id, name) {
              cityOptions += `<option value="${id}">${name}</option>`;
            });
            $('#city').html(cityOptions);
          },
          error: function () {
            $('#city').html('<option value="">Unable to load cities</option>');
          }
        });
      } else {
        $('#city').html('<option value="">Select City</option>');
      }
    });

    // filter
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function loadFilteredTable(page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Filtering records...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '{{ route("alldatafilter") }}?page=' + page,
        type: 'POST',
        data: {
          status: $('#sales_status').val(),
          city: $('#city').val(),
          state: $('#state').val(),
          business: $('#business_type').val(),
          source: $('#lead_source').val(),
          product: $('#product_type').val(),
          user_id: $('#user_id').val(),
        },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="empty-state">
                        <i class="bi bi-funnel"></i>
                        <h5>No Records Found</h5>
                        <p>Try adjusting your filter criteria.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              let remark = '-';
              if (record.last_remark) {
                const fullRemark = record.last_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              @endif

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                <td>${record.owner_name ?? 'N/A'}</td>
                                <td>${record.creator_name ?? 'N/A'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });

          $('#paginationfilterLinks').html(links);
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
        $('#paginationLinks').hide();
        loadFilteredTable(page);
      }
    });

    $(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type, #user_id', function () {
      $('#paginationLinks').hide();
      loadFilteredTable(1);
    });

    // date filter
    function loadDateFilteredTable(from_date = '', to_date = '', page = 1) {
      $('#alldatatable tbody').html(`
          <tr>
            <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="loading-state">
              <i class="bi bi-arrow-repeat"></i>
              <p class="mt-2 mb-0">Filtering by date...</p>
            </td>
          </tr>
        `);

      $.ajax({
        url: '{{ route("alldatafilterdate") }}?page=' + page,
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          from_date: from_date,
          to_date: to_date
        },
        success: function (response) {
          let data = response.data;
          let html = '';

          if (data.length === 0) {
            html = `<tr>
                      <td colspan="{{ auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1) ? '17' : '16' }}" class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h5>No Records Found</h5>
                        <p>No records found for the selected date range.</p>
                      </td>
                    </tr>`;
          } else {
            data.forEach(function (record, index) {
              let remark = '-';
              if (record.last_remark) {
                const fullRemark = record.last_remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
              }

              let assignToColumn = '';
              @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
                let dropdownOptions = '<option value="">Select Member</option>';
                if (window.teamMembers && window.teamMembers.length > 0) {
                  window.teamMembers.forEach(function (member) {
                    dropdownOptions += `<option value="${member.id}">${member.name}</option>`;
                  });
                }
                assignToColumn = `
                                <td>
                                    <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                        ${dropdownOptions}
                                    </select>
                                </td>
                            `;
              @endif

              html += `
                            <tr>
                                <td><span class="status-badge">${record.status_name ?? 'N/A'}</span></td>
                                <td>${record.prospectus_name ?? 'N/A'}</td>
                                <td>${remark}</td>
                                <td>${record.next_follow_up_date ?? 'N/A'}</td>
                                <td>${record.leads_name ?? ''}</td>
                                <td>${record.contact_person ?? ''}</td>
                                <td>${record.contact_number ?? ''}</td>
                                <td>${record.state_name ?? 'N/A'}</td>
                                <td>${record.city_name ?? 'N/A'}</td>
                                <td>${record.email ?? ''}</td>
                                <td>${record.business_name ?? 'N/A'}</td>
                                <td>${record.source_name ?? 'N/A'}</td>
                                <td>${record.product_name ?? 'N/A'}</td>
                                <td>${record.ticket_value ?? '0'}</td>
                                <td>${record.owner_name ?? 'N/A'}</td>
                                <td>${record.creator_name ?? 'N/A'}</td>
                                ${assignToColumn}
                            </tr>
                        `;
            });
          }

          $('#alldatatable tbody').html(html);

          let links = '';
          response.links.forEach(link => {
            if (link.url !== null) {
              links += `<li class="page-item ${link.active ? 'active' : ''}">
                            <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                        </li>`;
            } else {
              links += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
            }
          });
          $('#paginationdateLinks').html(links);
          updateRangeInfo(response.from, response.to, response.total);
        },
        error: function (xhr) {
          console.error("Error:", xhr.responseText);
        }
      });
    }

    $(document).on('change', '#from_date, #to_date', function () {
      $('#paginationLinks').hide();
      $('#paginationfilterLinks').hide();
      $('#paginationsearchLinks').hide();
      let from_date = $('#from_date').val();
      let to_date = $('#to_date').val();
      loadDateFilteredTable(from_date, to_date, 1);
    });

    $(document).on('click', '#paginationdateLinks .page-link', function (e) {
      e.preventDefault();
      let page = $(this).data('page');
      let from_date = $('#from_date').val();
      let to_date = $('#to_date').val();
      if (page) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        $('#paginationsearchLinks').hide();
        loadDateFilteredTable(from_date, to_date, page);
      }
    });

    // Load team members for reassignment dropdowns
    function loadTeamMembers() {
      var teamMembersUrl = '{{ route("alldata.team-members") }}';

      return $.ajax({
        url: teamMembersUrl,
        type: 'GET',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function (response) {
          window.teamMembers = response;
          if (window.teamMembers && window.teamMembers.length > 0) {
            loadSalesRecords();
          }
        },
        error: function (xhr) {
          console.error('Error loading team members:', xhr.responseText);
        }
      });
    }

    function reassignLead(leadId, newUserId) {
      if (!newUserId) return;

      $.ajax({
        url: '{{ route("alldata.reassign") }}',
        type: 'POST',
        data: {
          lead_id: leadId,
          new_user_id: newUserId,
          _token: '{{ csrf_token() }}'
        },
        success: function (response) {
          if (response.success) {
            showAlert('success', 'Lead reassigned successfully!');
            loadSalesRecords();
          } else {
            showAlert('error', 'Error: ' + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.error('Error reassigning lead:', xhr.responseText);
          showAlert('error', 'Error reassigning lead. Please try again.');
        }
      });
    }

    $(document).ready(function () {
      @if(auth()->check() && (auth()->user()->subordinates()->exists() || auth()->user()->role_id == 1))
        loadTeamMembers();
        setupBulkAssignment();
      @else
        loadSalesRecords();
      @endif
      });

    function setupBulkAssignment() {
      if (window.teamMembers && window.teamMembers.length > 0) {
        let bulkDropdown = $('#bulkAssignUser');
        bulkDropdown.empty().append('<option value="">Select User to Assign</option>');
        window.teamMembers.forEach(function (member) {
          bulkDropdown.append(`<option value="${member.id}">${member.name}</option>`);
        });
      }

      $('#selectAll').on('change', function () {
        let isChecked = $(this).is(':checked');
        $('.lead-checkbox').prop('checked', isChecked);
        updateBulkAssignmentControls();
      });

      $(document).on('change', '.lead-checkbox', function () {
        updateBulkAssignmentControls();
        updateSelectAllCheckbox();
      });

      $('#bulkAssignBtn').on('click', function () {
        let selectedUser = $('#bulkAssignUser').val();
        if (!selectedUser) {
          showAlert('error', 'Please select a user to assign leads to.');
          return;
        }

        let selectedLeads = $('.lead-checkbox:checked').map(function () {
          return $(this).val();
        }).get();

        if (selectedLeads.length === 0) {
          showAlert('error', 'Please select at least one lead to assign.');
          return;
        }

        if (confirm(`Are you sure you want to assign ${selectedLeads.length} leads to the selected user?`)) {
          bulkAssignLeads(selectedLeads, selectedUser);
        }
      });

      $('#clearSelectionBtn').on('click', function () {
        $('.lead-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
        updateBulkAssignmentControls();
      });
    }

    function updateBulkAssignmentControls() {
      let checkedCount = $('.lead-checkbox:checked').length;
      let bulkControls = $('#bulkAssignmentControls');
      let selectedCount = $('#selectedCount');
      let bulkAssignBtn = $('#bulkAssignBtn');

      if (checkedCount > 0) {
        bulkControls.show();
        selectedCount.text(checkedCount);
        bulkAssignBtn.prop('disabled', false);
      } else {
        bulkControls.hide();
        selectedCount.text('0');
        bulkAssignBtn.prop('disabled', true);
      }
    }

    function updateSelectAllCheckbox() {
      let totalCheckboxes = $('.lead-checkbox').length;
      let checkedCheckboxes = $('.lead-checkbox:checked').length;

      if (checkedCheckboxes === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
      } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
      } else {
        $('#selectAll').prop('indeterminate', true);
      }
    }

    function bulkAssignLeads(leadIds, userId) {
      let promises = leadIds.map(function (leadId) {
        return new Promise(function (resolve, reject) {
          $.ajax({
            url: '{{ route("alldata.reassign") }}',
            type: 'POST',
            data: {
              lead_id: leadId,
              new_user_id: userId,
              _token: '{{ csrf_token() }}'
            },
            success: function (response) {
              resolve(response);
            },
            error: function (xhr) {
              reject(xhr);
            }
          });
        });
      });

      Promise.all(promises)
        .then(function (results) {
          let successCount = results.filter(r => r.success).length;
          let failCount = results.length - successCount;

          if (failCount === 0) {
            showAlert('success', `Successfully assigned ${successCount} leads!`);
          } else {
            showAlert('error', `Assigned ${successCount} leads successfully. ${failCount} leads failed to assign.`);
          }

          $('.lead-checkbox').prop('checked', false);
          $('#selectAll').prop('checked', false);
          updateBulkAssignmentControls();

          if (window.currentView === 'search') {
            searchSalesTable(1);
          } else if (window.currentView === 'filter') {
            loadFilteredTable(1);
          } else if (window.currentView === 'date') {
            loadDateFilteredTable($('#from_date').val(), $('#to_date').val(), 1);
          } else {
            loadSalesRecords(1);
          }
        })
        .catch(function (error) {
          console.error('Bulk assignment error:', error);
          showAlert('error', 'An error occurred during bulk assignment. Please try again.');
        });
    }

    function showAlert(type, message) {
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
      const alertHtml = `
          <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        `;
      $('body').append(alertHtml);
      setTimeout(() => $('.alert').fadeOut(), 3000);
    }

  </script>

  <script>
    // --- Add Lead Modal Logic ---
    $(document).ready(function () {
      // Initial fetch for Add Lead modal dropdowns
      fetchAddLeadOptions();
    });

    let alldataProspectsList = [];

    function populateAlldataProspectDropdown(list, selectedVal = null, placeholder = 'Select Prospect') {
      let opts = `<option value="">${placeholder}</option>`;
      let currentVal = selectedVal !== null ? selectedVal : $('#add_lead_prospectus').val();
      let hasSelected = false;
      $.each(list, function (key, val) {
        let isSelected = (currentVal && currentVal == val.id) ? 'selected' : '';
        if (isSelected) hasSelected = true;
        opts += `<option value="${val.id}" ${isSelected}>${val.prospectus_name}</option>`;
      });
      $('#add_lead_prospectus').html(opts);
      if (selectedVal) {
        $('#add_lead_prospectus').val(selectedVal);
      } else if (currentVal && hasSelected) {
        $('#add_lead_prospectus').val(currentVal);
      }
    }

    function fetchAddLeadOptions() {
      // Prospect
      $.get("{{ route('getProspectus') }}", function (data) {
        alldataProspectsList = data || [];
        populateAlldataProspectDropdown(alldataProspectsList);
      });

      // States (Add Lead)
      $.get("{{ route('state') }}", function (data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#add_lead_state').html(opts);
      });

      // States (Prospect Modal)
      $.get("{{ route('state') }}", function (data) {
        let opts = '<option value="">Select State</option>';
        $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
        $('#modal_state').html(opts);
      });

      // Status
      $.get("{{ route('getStatuses') }}", function (data) {
        let opts = '<option value="">Select Status</option>';
        $.each(data, function (key, val) { opts += `<option value="${val.id}">${val.status_name}</option>`; });
        $('#add_lead_sales_status').html(opts);
      });

      // Business Type (Add Lead)
      $.get("{{ route('getbusiness') }}", function (data) {
        let opts = '<option value="">Select Business Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
        $('#add_lead_business_type').html(opts);
      });

      // Business Type (Prospect Modal)
      $.get("{{ route('getbusiness') }}", function (data) {
        let opts = '<option value="">Select Business Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.business_name}</option>`; });
        $('#modal_business_type').html(opts);
      });

      // Lead Source
      $.get("{{ route('getsource') }}", function (data) {
        let opts = '<option value="">Select Source</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.source_name}</option>`; });
        $('#add_lead_lead_source').html(opts);
      });

      // Product Type
      $.get("{{ route('getproduct') }}", function (data) {
        let opts = '<option value="">Select Product Type</option>';
        $.each(data, function (idx, val) { opts += `<option value="${val.id}">${val.product_name}</option>`; });
        $('#add_lead_product_type').html(opts);
      });
    }

    // Add Lead State Change -> Cities
    $(document).on('change', '#add_lead_state', function () {
      let stateId = $(this).val();
      if (stateId) {
        $.get("/city/" + stateId, function (data) {
          let opts = '<option value="">Select City</option>';
          $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
          $('#add_lead_city').html(opts);
        });
      } else {
        $('#add_lead_city').html('<option value="">Select City</option>');
      }
    });

    // Prospect Modal State Change -> Cities
    $(document).on('change', '#modal_state', function () {
      let stateId = $(this).val();
      if (stateId) {
        $.get("/city/" + stateId, function (data) {
          let opts = '<option value="">Select City</option>';
          $.each(data, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
          $('#modal_city').html(opts);
        });
      } else {
        $('#modal_city').html('<option value="">Select City</option>');
      }
    });

    // Prospectus Change -> Fill Data
    $(document).on('change', '#add_lead_prospectus', function () {
      let id = $(this).val();
      if (id) {
        $.get('/fillprospectus/' + id, function (data) {
          $('#add_lead_contactPerson').val(data.contact_person);
          $('#add_lead_contactNumber').val(data.contact_number);
          $('#add_lead_address').val(data.address);
          $('#add_lead_leadsName').val(data.prospectus_name);
          $('#add_lead_email').val(data.email);
          $('#add_lead_website_link').val(data.website_link);
          $('#add_lead_business_type').val(data.business_type_id);
          $('#add_lead_state').val(data.state_id);

          if (data.state_id) {
            $.get("/city/" + data.state_id, function (cities) {
              let opts = '<option value="">Select City</option>';
              $.each(cities, function (id, name) { opts += `<option value="${id}">${name}</option>`; });
              $('#add_lead_city').html(opts).val(data.city_id);
            });
          }
        });
      }
    });

    function submitLead(e) {
      e.preventDefault();
      let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_id: $('#add_lead_prospectus').val(),
        leads_name: $('#add_lead_leadsName').val(),
        contact_person: $('#add_lead_contactPerson').val(),
        contact_number: $('#add_lead_contactNumber').val(),
        status_id: $('#add_lead_sales_status').val(),
        address: $('#add_lead_address').val(),
        state_id: $('#add_lead_state').val(),
        city_id: $('#add_lead_city').val(),
        email: $('#add_lead_email').val(),
        website_link: $('#add_lead_website_link').val(),
        next_follow_up_date: $('#add_lead_next_follow_up').val(),
        business_type_id: $('#add_lead_business_type').val(),
        remark: $('#add_lead_remark').val(),
        lead_source_id: $('#add_lead_lead_source').val(),
        products_id: $('#add_lead_product_type').val()
      };

      $.post('/savelead', data)
        .done(function (response) {
          showAlert('success', 'Sales record submitted successfully!');
          $('#addLeadForm')[0].reset();
          $('#addLeadModal').modal('hide');
          loadSalesRecords();
          loadSummaryStats();
          loadStatusCounts();
        })
        .fail(function (xhr) {
          console.error(xhr.responseText);
          showAlert('error', 'Failed to submit sales record!');
        });
    }

    function submitProspect(e) {
      e.preventDefault();
      let data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        prospectus_name: $('#modalnewProspectusName').val(),
        contact_person: $('#modal_contact_person').val(),
        contact_number: $('#modal_contact_number').val(),
        address: $('#modal_address').val(),
        state_id: $('#modal_state').val(),
        city_id: $('#modal_city').val(),
        email: $('#modal_email').val(),
        website_link: $('#modal_website_link').val(),
        business_type_id: $('#modal_business_type').val()
      };

      $.post('/prospectus', data)
        .done(function (response) {
          $('#addProspectusModal').modal('hide');
          $('#addProspectusForm')[0].reset();
          showAlert('success', 'Prospect added successfully.');
          // Refresh Prospect Dropdown in Add Lead Modal
          $.get("{{ route('getProspectus') }}", function (data) {
            alldataProspectsList = data || [];
            let q = $('#alldata_search_prospect_input').val().toLowerCase().trim();
            if (q) {
              let filtered = alldataProspectsList.filter(function (p) {
                return (p.prospectus_name || '').toLowerCase().includes(q) ||
                       (p.contact_person || '').toLowerCase().includes(q) ||
                       (p.contact_number || '').toLowerCase().includes(q);
              });
              populateAlldataProspectDropdown(filtered);
            } else {
              populateAlldataProspectDropdown(alldataProspectsList);
            }
          });
        })
        .fail(function (xhr) {
          if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error === 'duplicate') {
            let p = xhr.responseJSON.prospect;
            let detailHtml = `
                <div class="text-start mt-2 p-2 border rounded" style="background:#f8f9fa; font-size: 0.88rem; line-height: 1.6;">
                    <div><strong>Prospect Name:</strong> ${p.prospectus_name || '-'}</div>
                    <div><strong>Contact Person:</strong> ${p.contact_person || '-'}</div>
                    <div><strong>Contact Number:</strong> ${p.contact_number || '-'}</div>
                    ${p.email ? `<div><strong>Email:</strong> ${p.email}</div>` : ''}
                    ${p.address ? `<div><strong>Address:</strong> ${p.address}</div>` : ''}
                    ${p.city ? `<div><strong>City / State:</strong> ${p.city}${p.state ? ', ' + p.state : ''}</div>` : ''}
                    ${p.business_type ? `<div><strong>Business Type:</strong> ${p.business_type}</div>` : ''}
                </div>
            `;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Prospect Already Exists!',
                    html: `<p class="mb-2 text-danger fw-semibold">${xhr.responseJSON.message}</p>${detailHtml}`,
                    confirmButtonText: 'Use This Existing Prospect',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#434afa',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#addProspectusModal').modal('hide');
                        $('#addProspectusForm')[0].reset();
                        // Select this existing prospect in the lead form
                        $.get("{{ route('getProspectus') }}", function(data) {
                            alldataProspectsList = data || [];
                            populateAlldataProspectDropdown(alldataProspectsList, p.id);
                            $('#add_lead_prospectus').val(p.id).trigger('change');
                        });
                    }
                });
            } else {
                alert(`${xhr.responseJSON.message}\nName: ${p.prospectus_name}\nPerson: ${p.contact_person}\nPhone: ${p.contact_number}`);
            }
          } else {
            showAlert('error', 'Something went wrong while adding prospect!');
            console.log(xhr.responseText);
          }
        });
    }

    // Search filter for Prospect dropdown in alldata with auto-selection
    function filterAlldataProspects(q) {
      if (!q) {
        populateAlldataProspectDropdown(alldataProspectsList);
        return;
      }
      let filtered = alldataProspectsList.filter(function (p) {
        let name = (p.prospectus_name || '').toLowerCase();
        let person = (p.contact_person || '').toLowerCase();
        let phone = (p.contact_number || '').toLowerCase();
        let email = (p.email || '').toLowerCase();
        return name.includes(q) || person.includes(q) || phone.includes(q) || email.includes(q);
      });

      if (filtered.length === 0) {
        populateAlldataProspectDropdown([], null, `No match for "${q}"`);
      } else if (filtered.length === 1) {
        // Auto-select single match and auto-fill lead details!
        populateAlldataProspectDropdown(filtered, filtered[0].id);
        $('#add_lead_prospectus').val(filtered[0].id).trigger('change');
      } else {
        populateAlldataProspectDropdown(filtered, null, `Select Prospect (${filtered.length} found)`);
      }
    }

    $(document).on('input', '#alldata_search_prospect_input', function () {
      let raw = $(this).val();
      if (raw.length > 0) {
        $('#alldata_clear_prospect_search').show();
      } else {
        $('#alldata_clear_prospect_search').hide();
      }
      filterAlldataProspects(raw.toLowerCase().trim());
    });

    $(document).on('click', '#alldata_clear_prospect_search', function () {
      $('#alldata_search_prospect_input').val('');
      $(this).hide();
      filterAlldataProspects('');
      $('#alldata_search_prospect_input').focus();
    });

    // Press Enter to pick first match immediately
    $(document).on('keydown', '#alldata_search_prospect_input', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        let q = $(this).val().toLowerCase().trim();
        if (!q) return;
        let filtered = alldataProspectsList.filter(function (p) {
          let name = (p.prospectus_name || '').toLowerCase();
          let person = (p.contact_person || '').toLowerCase();
          let phone = (p.contact_number || '').toLowerCase();
          let email = (p.email || '').toLowerCase();
          return name.includes(q) || person.includes(q) || phone.includes(q) || email.includes(q);
        });
        if (filtered.length > 0) {
          populateAlldataProspectDropdown(filtered, filtered[0].id);
          $('#add_lead_prospectus').val(filtered[0].id).trigger('change');
        }
      }
    });
    function openProspectusModal() {
      $('#addLeadModal .blur-target').addClass('blur-active');

      // Prefill from Add Lead form if values are present
      let leadContactPerson = $('#add_lead_contactPerson').val();
      let leadContactNumber = $('#add_lead_contactNumber').val();
      let leadAddress = $('#add_lead_address').val();
      let leadEmail = $('#add_lead_email').val();
      let leadWebsite = $('#add_lead_website_link').val();
      let leadBusinessType = $('#add_lead_business_type').val();
      let leadState = $('#add_lead_state').val();
      let leadCity = $('#add_lead_city').val();
      let leadName = $('#add_lead_leadsName').val();

      if (leadContactPerson && !$('#modal_contact_person').val()) {
          $('#modal_contact_person').val(leadContactPerson);
      }
      if (leadContactNumber && !$('#modal_contact_number').val()) {
          $('#modal_contact_number').val(leadContactNumber);
      }
      if (leadAddress && !$('#modal_address').val()) {
          $('#modal_address').val(leadAddress);
      }
      if (leadEmail && !$('#modal_email').val()) {
          $('#modal_email').val(leadEmail);
      }
      if (leadWebsite && !$('#modal_website_link').val()) {
          $('#modal_website_link').val(leadWebsite);
      }
      if (leadBusinessType && !$('#modal_business_type').val()) {
          $('#modal_business_type').val(leadBusinessType);
      }
      if (leadState && !$('#modal_state').val()) {
          $('#modal_state').val(leadState).trigger('change');
          if (leadCity) {
              setTimeout(function() {
                  $('#modal_city').val(leadCity);
              }, 300);
          }
      }
      if (leadName && !$('#modalnewProspectusName').val()) {
          $('#modalnewProspectusName').val(leadName);
      } else if (leadContactPerson && !$('#modalnewProspectusName').val()) {
          $('#modalnewProspectusName').val(leadContactPerson);
      }

      $('#addProspectusModal').modal('show');
    }

    $(document).ready(function () {

      $('#addProspectusModal').on('hidden.bs.modal', function () {
        $('#addLeadModal .blur-target').removeClass('blur-active');
      });

    });

  </script>
@endpush