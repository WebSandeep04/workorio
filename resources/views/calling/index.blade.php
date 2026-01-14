@extends('layouts.app')

@section('title', 'Calling')
@section('page_title', 'Calling')

@push('styles')
<style>
    .calling-page {
        padding: 0.5rem;
        background: #f7f8fc;
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
    .data-table-card .custom-table tbody td:nth-child(8) { min-width: 130px; }


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
            <label for="filter_calling_type" class="form-label-modern"><i class="bi bi-telephone"></i> Calling Type</label>
            <select id="filter_calling_type" class="form-control-modern">
                                <option value="">All Types</option>
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
                <button id="lockSelectedBtn" class="chip-btn primary" disabled>
                            <i class="bi bi-lock"></i> Lock Selected (<span id="selectedCount">0</span>)
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
                                    <th>Calling Type</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th style="width: 180px;">Remarks</th>
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
@endsection     

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

        function loadStates() {
            $.get('{{ route("calling.filter-options") }}', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });
                
                // Store calling types globally for use in dropdowns
                window.callingTypes = resp.calling_types || [];
                
                // Load calling types for filter
                var $callingType = $('#filter_calling_type');
                $callingType.empty().append('<option value="">All Types</option>');
                window.callingTypes.forEach(function(ct){
                    $callingType.append('<option value="'+ct.id+'">'+ct.name+'</option>');
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
            }).fail(function(xhr){
                console.error('Failed to load cities', xhr.status, xhr.responseText);
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    var stateName = (r.state && (r.state.state_name || r.state.name)) || '-';
                    var cityName = (r.city && (r.city.city_name || r.city.name)) || '-';
                    var phone = r.phone || r.mobile || '';
                    var full = (r.latest_remark && r.latest_remark.remark) ? r.latest_remark.remark : '';
                    var short = full ? (full.length > 10 ? full.substring(0,10) + '...' : full) : '-';
                    var remarkLink = '<a href="/calling/'+ r.id +'/remarks" title="'+ (full || '') +'">' + short + '</a>';

                    // Create calling type dropdown
                    var callingTypeOptions = '';
                    if (window.callingTypes && window.callingTypes.length > 0) {
                        window.callingTypes.forEach(function(ct) {
                            var selected = ct.id === r.calling_type_id ? 'selected' : '';
                            callingTypeOptions += '<option value="' + ct.id + '" ' + selected + '>' + ct.name + '</option>';
                        });
                    }
                    var callingTypeDropdown = '<select class="form-select form-select-sm calling-type-select" data-calling-id="' + r.id + '" style="min-width: 100px;">' + callingTypeOptions + '</select>';

                    html += '\n<tr>' +
                        '<td><input type="checkbox" class="form-check-input row-checkbox" value="' + r.id + '" data-calling-id="' + r.id + '"></td>' +
                        '<td>' + (r.name || '-') + '</td>' +
                        '<td>' + (r.email || '-') + '</td>' +
                        '<td>' + callingTypeDropdown + '</td>' +
                        '<td>' + stateName + '</td>' +
                        '<td>' + cityName + '</td>' +
                        '<td>' + (r.address || '-') + '</td>' +
                        '<td>' + phone + '</td>' +
                        '<td>' + remarkLink + '</td>' +
                    '</tr>';
                });
            } else {
                html = '<tr><td colspan="10" class="text-center">No records found.</td></tr>';
            }
            $tbody.html(html);
        }

        let currentPage = 1;
        let currentFilterPage = 1;

        // Build simple pagination: "Previous [current / last] Next"
        function buildDetailedPagination($container, current, last) {
            $container.empty();
            
            // Previous button
            $container.append(`
                <li class="page-item ${current === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
                      <i class="bi bi-chevron-left"></i> Previous
                    </a>
                </li>
            `);
            
            // Current page display
            $container.append(`
                <li class="page-item active">
                    <span class="page-link">${current} / ${last}</span>
                </li>
            `);
            
            // Next button
            $container.append(`
                <li class="page-item ${current === last ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
                      Next <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `);
        }

        function loadCallings(page = 1) {
            currentPage = page;
            $.get('{{ route("calling.data") }}?page=' + page, function(data){
                var rows = Array.isArray(data) ? data : (data.data || []);
                renderRows(rows);
                renderPagination(data);
                updateTotals(data);
                setActiveFiltersCount(0);
            }).fail(function(xhr){
                console.error('Failed to load callings', xhr.status, xhr.responseText);
            });
        }

        function renderPagination(data) {
            const $pagination = $('#paginationLinks');
            const current = data.current_page;
            const last = data.last_page;
            buildDetailedPagination($pagination, current, last);
            $('#paginationLinks').show();
            $('#paginationFilterLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function applyFilters(page = 1) {
            currentFilterPage = page;
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            var callingTypeId = $('#filter_calling_type').val();
            const appliedCount = [name, stateId, cityId, callingTypeId].filter(Boolean).length;
            if (!appliedCount) {
                setActiveFiltersCount(0);
                loadCallings(1);
                return;
            }
            setActiveFiltersCount(appliedCount);
            $.post('{{ route("calling.filter") }}?page=' + page, {
                name: name,
                state_id: stateId,
                city_id: cityId,
                calling_type_id: callingTypeId
            }).done(function(data){
                var rows = Array.isArray(data) ? data : (data.data || []);
                renderRows(rows);
                renderFilterPagination(data);
                updateTotals(data);
            }).fail(function(xhr){
                console.error('Filter failed', { name, stateId, cityId, callingTypeId }, xhr.status, xhr.responseText);
                alert('Failed to fetch filtered results');
            });
        }

        function renderFilterPagination(data) {
            const $pagination = $('#paginationFilterLinks');
            const current = data.current_page;
            const last = data.last_page;
            buildDetailedPagination($pagination, current, last);
            $('#paginationFilterLinks').show();
            $('#paginationLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function updateRangeInfo(from, to, total) {
            const $info = $('#callingRangeInfo');
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

        // Init
        loadStates();
        loadCallings();

        // Events
        function debounce(fn, delay){
            let t; return function(){
                clearTimeout(t); const args = arguments; const ctx = this;
                t = setTimeout(function(){ fn.apply(ctx, args); }, delay);
            };
        }

        const triggerFilter = debounce(applyFilters, 300);

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); triggerFilter(); });
        $('#filter_city').on('change', triggerFilter);
        $('#filter_calling_type').on('change', triggerFilter);
        $('#filter_name').on('input', triggerFilter);
        $('#resetFilters').on('click', function(e){ e.preventDefault(); $('#filter_name').val(''); $('#filter_state').val(''); $('#filter_city').html('<option value="">All Cities</option>'); $('#filter_calling_type').val(''); setActiveFiltersCount(0); loadCallings(1); });

        // Pagination click handlers
        $(document).on('click', '#paginationLinks .page-link', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && page !== currentPage) {
                loadCallings(page);
            }
        });

        $(document).on('click', '#paginationFilterLinks .page-link', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && page !== currentFilterPage) {
                applyFilters(page);
            }
        });

        // Handle calling type dropdown changes
        $(document).on('change', '.calling-type-select', function() {
            var callingId = $(this).data('calling-id');
            var newCallingType = $(this).val();
            var $select = $(this);
            
            // Show loading state
            $select.prop('disabled', true);
            
            $.ajax({
                url: '{{ route("calling.update-type") }}',
                type: 'POST',
                data: {
                    calling_id: callingId,
                    calling_type_id: newCallingType,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showAlert('success', 'Calling type updated successfully!');
                        
                        // If changed to Junk, remove from current view
                        var junkTypeId = window.callingTypes.find(ct => ct.name === 'Junk')?.id;
                        if (newCallingType == junkTypeId) {
                            $select.closest('tr').fadeOut(function() {
                                $(this).remove();
                            });
                        }
                    } else {
                        showAlert('error', 'Failed to update calling type.');
                        // Revert the dropdown
                        reloadAll();
                    }
                },
                error: function(xhr) {
                    console.error('Update failed:', xhr.responseText);
                    showAlert('error', 'Failed to update calling type.');
                    // Revert the dropdown
                    reloadAll();
                },
                complete: function() {
                    $select.prop('disabled', false);
                }
            });
        });


        // Show alert function
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#alertContainer').html(alertHtml);
            
            setTimeout(function() {
                $('#alertContainer .alert').fadeOut(function(){ $(this).remove(); });
            }, 4000);
        }

        // Checkbox and Lock functionality
        function updateSelectedCount() {
            var selectedCount = $('.row-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);
            $('#heroSelected').text(formatNumber(selectedCount));
            $('#lockSelectedBtn').prop('disabled', selectedCount === 0);
        }

        // Select All functionality
        $('#selectAllCheckbox').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
            updateSelectedCount();
        });

        // Individual checkbox change
        $(document).on('change', '.row-checkbox', function() {
            updateSelectedCount();
            
            // Update select all checkbox state
            var totalCheckboxes = $('.row-checkbox').length;
            var checkedCheckboxes = $('.row-checkbox:checked').length;
            
            if (checkedCheckboxes === 0) {
                $('#selectAllCheckbox').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAllCheckbox').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('indeterminate', true);
            }
        });

        // Select All button functionality
        $('#selectAllBtn').on('click', function() {
            var $btn = $(this);
            var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
            
            if (allChecked) {
                $('.row-checkbox').prop('checked', false);
                $('#selectAllCheckbox').prop('checked', false).prop('indeterminate', false);
                $btn.html('<i class="bi bi-check2-square"></i> Select All');
            } else {
                $('.row-checkbox').prop('checked', true);
                $('#selectAllCheckbox').prop('checked', true).prop('indeterminate', false);
                $btn.html('<i class="bi bi-square"></i> Deselect All');
            }
            updateSelectedCount();
        });

        // Lock Selected functionality
        $('#lockSelectedBtn').on('click', function() {
            var selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });
            
            if (selectedIds.length === 0) {
                showAlert('error', 'Please select at least one calling to lock.');
                return;
            }
            
            // Confirmation dialog
            if (!confirm(`Are you sure you want to lock ${selectedIds.length} selected calling(s)? This will assign them to you.`)) {
                return;
            }
            
            var $btn = $(this);
            const defaultLabel = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Locking...');
            
            $.ajax({
                url: '{{ route("calling.lock-selected") }}',
                type: 'POST',
                data: {
                    calling_ids: selectedIds,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', `Successfully locked ${response.locked_count} calling(s)!`);
                        
                        // Remove locked rows from current view or reload
                        selectedIds.forEach(function(id) {
                            $('input[value="' + id + '"]').closest('tr').fadeOut(function() {
                                $(this).remove();
                                updateSelectedCount();
                            });
                        });
                        
                        // Reset checkboxes
                        $('#selectAllCheckbox').prop('checked', false).prop('indeterminate', false);
                        $('#selectAllBtn').html('<i class="bi bi-check2-square"></i> Select All');
                    } else {
                        showAlert('error', response.message || 'Failed to lock selected callings.');
                    }
                },
                error: function(xhr) {
                    console.error('Lock failed:', xhr.responseText);
                    showAlert('error', 'Failed to lock selected callings. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(defaultLabel);
                    updateSelectedCount();
                }
            });
        });
    });
</script>
@endpush
