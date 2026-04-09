@extends('layouts.app')

@section('title', 'Campaign')
@section('page_title', 'Campaign Management')

@push('styles')
<style>
    .calling-page {
        padding: 0.5rem;
        background: #f7f8fc;
    }

    .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
    .calling-hero-card {
        background: transparent;
        border-radius: 0;
        color: inherit;
        padding: 0;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .calling-hero-card > div:first-child {
        display: none;
    }

    .eyebrow-text {
        display: none;
    }

    .hero-title {
        display: none;
    }

    .hero-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        width: 100%;
    }

    .hero-metric-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef3;
        padding: 0.75rem 1rem;
        width: 100%;
        box-shadow: 0px 4px 4px 0px #0000000A;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .hero-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0px 8px 8px 0px #0000000A;
    }

    .hero-metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hero-metric-icon img {
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

    .hero-metric-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
        min-width: 0;
    }

    .metric-label {
        display: block;
        font-size: 0.65rem;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
        font-weight: 600;
        font-family: Montserrat;
    }

    .metric-value {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.2;
        display: block;
        color: #101828;
        font-family: Montserrat;
    }

    .filterBox {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 0.5rem;
        background: #434AFA;
        padding: 0.75rem;
        border-radius: 5px;
        color: #fff;
        border: 1px solid #434AFA;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 1rem;
        justify-items: stretch;
        font-family: Montserrat, sans-serif;
    }

    .filterBox > div {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 100%;
        gap: 0.35rem;
    }

    .form-label-modern {
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-shadow: none;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern {
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 6px;
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


    .filter-reset-btn {
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.18);
        color: white;
        padding: 0.35rem 0.5rem;
        font-weight: 600;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.3s ease;
    }

    .filter-reset-btn:hover {
        background: rgba(255, 255, 255, 0.28);
        border-color: rgba(255, 255, 255, 0.6);
    }

    .modern-card-header {
        padding: 0.35rem 0.75rem;
        border-bottom: 1px solid #eef0f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        background: white;
    }

    .section-eyebrow {
        font-size: 0.45rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 0.05rem;
        line-height: 1;
    }

    .card-title-modern {
        margin: 0;
        font-size: 0.7rem;
        font-weight: 600;
        color: #101828;
        line-height: 1.2;
    }

    .header-actions {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .chip-btn {
        border-radius: 4px;
        border: none;
        padding: 0.2rem 0.4rem;
        font-size: 0.6rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        transition: all 0.3s ease;
        line-height: 1.2;
    }

    .chip-btn.primary {
        background: linear-gradient(135deg, #ff7eb3 0%, #ff758c 100%);
        color: #fff;
        box-shadow: 0 2px 4px rgba(255, 118, 144, 0.25);
    }

    .chip-btn.primary:disabled {
        opacity: 0.5;
        box-shadow: none;
    }

    .chip-btn.ghost {
        background: #f3f5ff;
        color: #4a4de6;
    }

    .modern-card {
        padding: 0;
        margin-bottom: 0.5rem;
    }

    .modern-card-body {
        padding: 0.5rem;
    }

    .form-check-input,
    .row-checkbox {
        border: 1px solid #000 !important;
        border-radius: 3px;
    }

    .form-check-input:checked,
    .row-checkbox:checked {
        background-color: #667eea;
        border-color: #000 !important;
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
        min-width: 950px;
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
        color: #1f2937;
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
    .data-table-card .custom-table tbody td:nth-child(2) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(3) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(4) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(5) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(6) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(7) { min-width: 180px; }


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

    .pagination-wrapper {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
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

    #alertContainer .alert {
        border-radius: 12px;
        border: none;
        padding: 0.85rem 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

     @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-left: 0;
    }

    .hero-metrics{
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .hero-metric-card{
        margin-bottom: 0;
    }
}
    .campaign-item-card {
        background: #fff;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #eef0f7;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .campaign-item-card:hover {
        border-color: #434AFA;
        transform: translateX(-5px);
        box-shadow: 0 4px 12px rgba(67, 74, 250, 0.1);
    }

    .campaign-item-card.active {
        background: #434AFA;
        color: #fff;
        border-color: #434AFA;
    }

    .campaign-item-card.active .text-muted {
        color: rgba(255,255,255,0.8) !important;
    }

    .campaign-item-card .icon-label {
        width: 35px;
        height: 35px;
        background: #eef0ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #434AFA;
        margin-bottom: 0.5rem;
    }

    .campaign-item-card.active .icon-label {
        background: rgba(255,255,255,0.2);
        color: #fff;
    }

    .letter-spacing-1 { letter-spacing: 1px; }

</style>
@endpush

@section('content')
<div class="container-fluid px-2 calling-page">
    <div class="calling-hero-card">
        <div>
            <p class="eyebrow-text">Calling Queue</p>
            <h2 class="hero-title">React-inspired Calling Board</h2>
            <p class="mb-0">Assign prospects in seconds, change calling types inline, and keep the team moving without page reloads.</p>
        </div>
        <div class="hero-metrics">
            <div class="hero-metric-card">
                <div class="hero-metric-icon icon-sky">
                    <img src="{{ asset('img/icons/call.png') }}" alt="Total Records">
                </div>
                <div class="hero-metric-content">
                    <span class="metric-label">Total Records</span>
                    <span class="metric-value" id="totalCallings">0</span>
                </div>
            </div>
            <div class="hero-metric-card">
                <div class="hero-metric-icon icon-emerald">
                    <img src="{{ asset('img/icons/tick.png') }}" alt="Selected">
                </div>
                <div class="hero-metric-content">
                    <span class="metric-label">Selected</span>
                    <span class="metric-value" id="heroSelected">0</span>
                </div>
            </div>
            <div class="hero-metric-card">
                <div class="hero-metric-icon icon-amber">
                    <img src="{{ asset('img/icons/underprocess.png') }}" alt="Active Filters">
                </div>
                <div class="hero-metric-content">
                    <span class="metric-label">Active Filters</span>
                    <span class="metric-value" id="activeFilters">0</span>
                </div>
            </div>
        </div>
                </div>

    <div id="alertContainer"></div>

    <div class="filterBox">
        <div>
            <label for="filter_campaign" class="form-label-modern"><i class="bi bi-megaphone"></i> Campaign</label>
            <select id="filter_campaign" class="form-control-modern">
                <option value="">Master Record</option>
            </select>
        </div>
        <div>
            <label for="filter_state" class="form-label-modern"><i class="bi bi-geo-alt"></i> State</label>
            <select id="filter_state" class="form-control-modern">
                                <option value="">All States</option>
                            </select>
                        </div>
        <div>
            <label for="filter_city" class="form-label-modern"><i class="bi bi-buildings"></i> City</label>
            <select id="filter_city" class="form-control-modern">
                                <option value="">All Cities</option>
                            </select>
                        </div>
        <div>
            <label class="form-label-modern" style="visibility: hidden;">Reset</label>
            <button id="resetFilters" class="filter-reset-btn w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
                        </div>
                    </div>

        <div class="table-search mb-2">
            <div class="table-search-field">
                <i class="bi bi-search"></i>
                <input type="text" id="filter_name" placeholder="Search by name" />
            </div>
        </div>

        <div class="modern-card data-table-card">
        <div class="modern-card-header">
            <div>
                <p class="section-eyebrow mb-1">Live queue</p>
                <h4 class="card-title-modern mb-0">Unassigned callings</h4>
            </div>
            <div class="header-actions">
                <button id="openCampaignModalBtn" class="chip-btn primary" disabled>
                            <i class="bi bi-plus-circle"></i> Create Campaign (<span id="selectedCount">0</span>)
                        </button>
                <button id="selectAllBtn" class="chip-btn ghost">
                            <i class="bi bi-check2-square"></i> Select All
                        </button>
                    </div>
                </div>
        <div class="modern-card-body">
                    <div class="table-scroll">
                <table class="table custom-table" id="callingTable">
                    <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                    </th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                        <!-- Data loaded via ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    <div class="table-range-meta" id="callingRangeInfo">
        Showing 0-0 from 0 data
    </div>

    <div class="pagination-wrapper">
                <ul class="pagination" id="paginationLinks"></ul>
            </div>
    <div class="pagination-wrapper">
                <ul class="pagination" id="paginationFilterLinks"></ul>
    </div>
</div>

@include('partials.remarks-modal')

@endsection

<!-- Campaign Creation Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-megaphone me-2"></i> Create New Campaign</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="campaignName" class="form-label fw-bold small text-muted text-uppercase">Campaign Name</label>
                    <input type="text" id="campaignName" class="form-control form-control-lg border-2" style="border-radius: 10px;" placeholder="e.g. Q4 Real Estate Drive">
                </div>
                <p class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> 
                    You are grouping <span id="modalSelectedCount" class="fw-bold text-primary">0</span> contacts into this campaign.
                </p>
            </div>
            <div class="modal-footer border-0 p-3 pt-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmCampaignBtn" class="btn btn-primary px-4 fw-bold" style="background: #434AFA; border-radius: 8px;">
                    Create & Assign
                </button>
            </div>
        </div>
    </div>
</div>     

@push('scripts')
<script>
    $(document).ready(function() {
        var $tbody = $('#callingTable tbody');
        let totalRecords = 0;
        let activeFilterCount = 0;

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function formatNumber(num) {
            return Number(num || 0).toLocaleString('en-IN');
        }

        function updateTotals(meta) {
            if (meta && typeof meta.total !== 'undefined') {
                totalRecords = meta.total;
            } else if (Array.isArray(meta)) {
                totalRecords = meta.length;
            } else if (meta && meta.data) {
                totalRecords = meta.data.length;
            } else {
                totalRecords = 0;
            }
            $('#totalCallings').text(formatNumber(totalRecords));
        }

        function setActiveFiltersCount(count) {
            activeFilterCount = count;
            $('#activeFilters').text(formatNumber(count));
        }

        function loadFilterData() {
            // Load Campaigns
            $.get('{{ route("calling.campaigns") }}', function(campaigns) {
                var $camp = $('#filter_campaign');
                $camp.empty().append('<option value="">Master Record</option>');
                (campaigns || []).forEach(function(c){
                    $camp.append('<option value="'+c.id+'">'+(c.name || 'Unnamed')+'</option>');
                });
            });

            // Load States
            $.get('{{ route("calling.filter-options") }}', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            var url = '{{ route("calling.cities", ["stateId" => 0]) }}'.replace(/0$/, String(stateId));
            $.get(url, function(cities){
                var $city = $('#filter_city');
                $city.empty().append('<option value="">All Cities</option>');
                (cities || []).forEach(function(c){
                    $city.append('<option value="'+c.id+'">'+c.name+'</option>');
                });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    html += '<tr>' +
                        '<td><input type="checkbox" class="form-check-input row-checkbox" value="' + r.id + '"></td>' +
                        '<td>' + (r.name || '-') + '</td>' +
                        '<td>' + (r.email || '-') + '</td>' +
                        '<td>' + (r.state || '-') + '</td>' +
                        '<td>' + (r.city || '-') + '</td>' +
                        '<td>' + (r.address || '-') + '</td>' +
                        '<td>' + (r.phone || '-') + '</td>' +
                    '</tr>';
                });
            } else {
                html = '<tr><td colspan="7" class="text-center p-4">No records found.</td></tr>';
            }
            $tbody.html(html);
        }

        let currentPage = 1;
        let currentFilterPage = 1;

        function buildDetailedPagination($container, current, last) {
            $container.empty();
            if (last <= 1) return;
            $container.append('<li class="page-item ' + (current === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + Math.max(1, current - 1) + '">Previous</a></li>');
            $container.append('<li class="page-item active"><span class="page-link">' + current + ' / ' + last + '</span></li>');
            $container.append('<li class="page-item ' + (current === last ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + Math.min(last, current + 1) + '">Next</a></li>');
        }

        function renderPagination(data) {
            buildDetailedPagination($('#paginationLinks'), data.current_page, data.last_page);
            $('#paginationLinks').show();
            $('#paginationFilterLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function renderFilterPagination(data) {
            buildDetailedPagination($('#paginationFilterLinks'), data.current_page, data.last_page);
            $('#paginationFilterLinks').show();
            $('#paginationLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function updateRangeInfo(from, to, total) {
            $('#callingRangeInfo').text('Showing ' + (from || 0) + '-' + (to || 0) + ' from ' + (total || 0) + ' data');
        }

        function loadCallings(page = 1) {
            currentPage = page;
            $.get('{{ route("calling.data") }}?page=' + page, function(data){
                renderRows(data.data || []);
                renderPagination(data);
                $('#totalCallings').text(data.total || 0);
            });
        }

        function applyFilters(page = 1) {
            currentFilterPage = page;
            var campaignId = $('#filter_campaign').val();
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            
            const appliedCount = [name, stateId, cityId, campaignId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('{{ route("calling.filter") }}?page=' + page, {
                campaign_id: campaignId,
                name: name,
                state_id: stateId,
                city_id: cityId,
                _token: '{{ csrf_token() }}'
            }).done(function(data){
                renderRows(data.data || []);
                renderFilterPagination(data);
            });
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            $('#alertContainer').html(alertHtml);
            setTimeout(() => { $('#alertContainer .alert').fadeOut(() => $(this).remove()); }, 4000);
        }

        function updateSelectedCount() {
            var selectedCount = $('.row-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);
            $('#heroSelected').text(selectedCount.toLocaleString());
            $('#openCampaignModalBtn').prop('disabled', selectedCount === 0);
        }

        // Init
        loadFilterData();
        loadCallings();

        // Events
        function debounce(fn, delay){
            let t; return function(){
                clearTimeout(t); const args = arguments; const ctx = this;
                t = setTimeout(() => { fn.apply(ctx, args); }, delay);
            };
        }
        const triggerFilter = debounce(applyFilters, 300);

        $('#filter_campaign').on('change', function() {
            var campName = $(this).find('option:selected').text();
            if ($(this).val()) {
                $('.card-title-modern').text(campName);
                $('.section-eyebrow').text('Campaign Queue');
            } else {
                $('.card-title-modern').text('Unassigned callings');
                $('.section-eyebrow').text('Live queue');
            }
            triggerFilter();
        });

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); triggerFilter(); });
        $('#filter_city').on('change', triggerFilter);
        $('#filter_name').on('input', triggerFilter);
        
        $('#resetFilters').on('click', function(e){ 
            e.preventDefault(); 
            $('#filter_name').val(''); 
            $('#filter_campaign').val('');
            $('#filter_state').val(''); 
            $('#filter_city').html('<option value="">All Cities</option>'); 
            $('.card-title-modern').text('Unassigned callings');
            $('.section-eyebrow').text('Live queue');
            updateSelectedCount(); loadCallings(1); 
        });

        $(document).on('click', '#paginationLinks .page-link', function (e) {
            e.preventDefault(); const page = $(this).data('page');
            if (page && page !== currentPage) loadCallings(page);
        });

        $(document).on('click', '#paginationFilterLinks .page-link', function (e) {
            e.preventDefault(); const page = $(this).data('page');
            if (page && page !== currentFilterPage) applyFilters(page);
        });

        // Campaign Multi-assignment
        $('#openCampaignModalBtn').on('click', () => {
            $('#modalSelectedCount').text($('.row-checkbox:checked').length);
            $('#campaignName').val('');
            $('#campaignModal').modal('show');
        });

        $('#confirmCampaignBtn').on('click', function() {
            var campaignName = $('#campaignName').val().trim();
            var selectedIds = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();
            if (!campaignName) return alert('Campaign name required');

            var $btn = $(this); $btn.prop('disabled', true).text('Creating...');
            $.post('{{ route("calling.create-campaign") }}', {
                campaign_name: campaignName,
                calling_ids: selectedIds,
                _token: '{{ csrf_token() }}'
            }).done(resp => {
                if (resp.success) {
                    showAlert('success', resp.message);
                    $('#campaignModal').modal('hide');
                    $('.row-checkbox').prop('checked', false);
                    $('#selectAllCheckbox').prop('checked', false).prop('indeterminate', false);
                    updateSelectedCount();
                    loadFilterData(); // Refresh dropdown
                }
            }).always(() => { $btn.prop('disabled', false).text('Create & Assign'); });
        });

        $(document).on('change', '.row-checkbox', updateSelectedCount);
        $('#selectAllCheckbox').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        $('#selectAllBtn').on('click', function() {
            var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
            $('.row-checkbox').prop('checked', !allChecked);
            $('#selectAllCheckbox').prop('checked', !allChecked).prop('indeterminate', false);
            $(this).html(!allChecked ? '<i class="bi bi-square"></i> Deselect All' : '<i class="bi bi-check2-square"></i> Select All');
            updateSelectedCount();
        });
    });
</script>
@endpush
