@extends('layouts.app')

@section('title', 'My Calling')
@section('page_title', 'My Calling')

@push('styles')
<style>
    .container-fluid {
        padding: 0.5rem 0.75rem;
        background: #f7f8fc;
        min-height: calc(100vh - 60px);
    }

    /* Metrics Section */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .summary-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eef0f7;
        padding: 0.75rem 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease;
    }

    .summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    .summary-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .summary-card-icon img { width: 24px; height: 24px; }
    .icon-sky { background: #e0f2fe; color: #0369a1; }
    .icon-amber { background: #fef3c7; color: #b45309; }
    .icon-emerald { background: #dcfce7; color: #15803d; }

    .summary-card-label {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #71717a;
        margin-bottom: 2px;
        letter-spacing: 0.025em;
    }

    .summary-card-value { font-size: 1.1rem; font-weight: 800; color: #18181b; margin: 0; font-family: 'Inter', sans-serif; }

    /* View Toggle */
    .view-toggle-wrapper { margin-bottom: 1rem; display: flex; gap: 0.25rem; background: #f1f5f9; padding: 4px; border-radius: 10px; width: fit-content; }
    .view-toggle-btn { border: none; padding: 0.5rem 1.25rem; border-radius: 8px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; transition: all 0.2s ease; background: transparent; color: #64748b; font-family: 'Inter', sans-serif; }
    .view-toggle-btn.active { background: #fff; color: #434afa; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

    /* Filter Box */
    .filterBox { 
        display: grid; 
        grid-template-columns: 1fr 1fr 120px; 
        gap: 1rem; 
        background: #434AFA; 
        padding: 1rem; 
        border-radius: 8px; 
        color: #fff; 
        margin-bottom: 1rem;
    }

    .form-label-modern { color: rgba(255,255,255,0.9); font-size: 0.65rem; font-weight: 600; margin-bottom: 0.4rem; display: block; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-control-modern { border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 6px; padding: 0.5rem 0.75rem; background: #fff; color: #1f2937; font-size: 0.8rem; font-family: 'Inter', sans-serif; width: 100%; transition: 0.2s; }
    .form-control-modern:focus { outline: none; box-shadow: 0 0 0 3px rgba(255,255,255,0.25); }
    
    .filter-reset-btn { border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 6px; background: rgba(255, 255, 255, 0.1); color: white; padding: 0.5rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; display: flex; align-items: center; justify-content: center; transition: 0.2s; height: 38px; }
    .filter-reset-btn:hover { background: rgba(255, 255, 255, 0.2); }

    /* Search field */
    .table-search-field {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        width: 100%;
        max-width: 400px;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .table-search-field i { color: #94a3b8; font-size: 0.9rem; }
    .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #1e293b; }

    /* Table System */
    .modern-card { border-radius: 12px; border: 1px solid #e2e8f0; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
    .table-scroll { width: 100%; overflow-x: auto; }
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 900px; }
    .custom-table thead th { background: #f8fafc; color: #475569; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; padding: 0.75rem 1rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; letter-spacing: 0.025em; }
    .custom-table tbody td { padding: 0.75rem 1rem; font-size: 0.8rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .custom-table tbody tr:hover { background: #f8fafc; }
    
    .remark-link { color: #434AFA; text-decoration: none; font-weight: 600; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.4rem; }
    .remark-link:hover { text-decoration: underline; color: #2d38e0; }

    /* Campaign Grid */
    .campaign-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
    .campaign-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: left; }
    .campaign-card:hover { border-color: #434AFA; box-shadow: 0 10px 15px -3px rgba(67, 74, 250, 0.1); transform: translateY(-2px); }
    .camp-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem; }
    .camp-meta { font-size: 0.85rem; color: #64748b; font-weight: 500; }
    .camp-leads-count { color: #434AFA; font-weight: 700; margin-left: 0.25rem; }

    .back-to-camp { cursor: pointer; color: #434AFA; font-weight: 600; margin-bottom: 1.25rem; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; transition: transform 0.2s; }
    .back-to-camp:hover { transform: translateX(-4px); }
    
    .range-info { font-size: 0.75rem; color: #64748b; font-weight: 500; }
    
    #paginationLinks .page-item .page-link { border: 1px solid #e2e8f0; font-size: 0.75rem; font-weight: 600; color: #475569; padding: 0.4rem 0.75rem; border-radius: 6px; margin: 0 2px; }
    #paginationLinks .page-item.active .page-link { background: #434AFA; border-color: #434AFA; color: #fff; }

    /* Conditional Views */
    #campaignListView { display: none; }
    #campaignDataView { display: none; }
    
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Summary Metrics -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-card-icon icon-sky">
                <i class="bi bi-person-lines-fill"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total leads</div>
                <div class="summary-card-value" id="totalAssigned">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-amber">
                <i class="bi bi-funnel"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Active filters</div>
                <div class="summary-card-value" id="activeFilters">0</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-icon icon-emerald">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Last updated</div>
                <div class="summary-card-value" id="lastUpdated" style="font-size: 0.85rem;">--</div>
            </div>
        </div>
    </div>

    <!-- Interface Switching -->
    <div class="view-toggle-wrapper">
        <button class="view-toggle-btn active" data-view="list">Detailed List</button>
        <button class="view-toggle-btn" data-view="campaign">Campaign Matrix</button>
    </div>

    <!-- 1. MAIN TABULAR VIEW -->
    <div id="mainListView">
        <div class="filterBox">
            <div class="flex-grow-1">
                <label for="filter_state" class="form-label-modern">Geography (State)</label>
                <select id="filter_state" class="form-control-modern">
                    <option value="">Global - All States</option>
                </select>
            </div>
            <div class="flex-grow-1">
                <label for="filter_city" class="form-label-modern">Locality (City)</label>
                <select id="filter_city" class="form-control-modern">
                    <option value="">Global - All Cities</option>
                </select>
            </div>
            <div>
                <label class="form-label-modern" style="visibility: hidden;">Action</label>
                <button id="resetFilters" class="filter-reset-btn w-100">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </button>
            </div>
        </div>

        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="filter_name" placeholder="Quick find by name or mobile..." />
        </div>

        <div class="modern-card">
            <div class="table-scroll">
                <table class="table custom-table mb-0" id="callingTable">
                    <thead>
                        <tr>
                            <th>Module / Campaign</th>
                            <th>Lead Name</th>
                            <th>Email Address</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Phone No.</th>
                            <th style="min-width: 160px;">Engagement Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" class="text-center p-5 text-muted">Synchronizing data queue...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 px-2">
            <div class="range-info" id="rangeInfo">Showing 0-0 from 0 data</div>
            <ul class="pagination mb-0" id="paginationLinks"></ul>
        </div>
    </div>

    <!-- 2. CAMPAIGN AGGREGATION VIEW -->
    <div id="campaignListView">
        <div class="campaign-list" id="campaignGrid"></div>
    </div>

    <!-- 3. SPECIFIC CAMPAIGN DRILLDOWN -->
    <div id="campaignDataView">
        <div class="back-to-camp" onclick="showCampaignList()">
            <i class="bi bi-arrow-left-short" style="font-size: 1.4rem;"></i> Return to Campaigns
        </div>
        <div class="modern-card">
            <div class="bg-light border-bottom p-3">
                <h5 class="fw-bold mb-0 text-dark" id="selectedCampTitle" style="font-size: 1rem;">Campaign Scope</h5>
            </div>
            <div class="table-scroll">
                <table class="table custom-table mb-0" id="campDataTable">
                    <thead>
                        <tr>
                            <th>Lead Identity</th>
                            <th>Communication (Email)</th>
                            <th>Region</th>
                            <th>District</th>
                            <th>Contact</th>
                            <th style="min-width: 160px;">Last Interaction</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var $tbody = $('#callingTable tbody');
        let currentCampaignId = null;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // View Navigation Logic
        $('.view-toggle-btn').on('click', function() {
            const view = $(this).data('view');
            $('.view-toggle-btn').removeClass('active');
            $(this).addClass('active');

            if (view === 'list') {
                $('#mainListView').fadeIn(200);
                $('#campaignListView').hide();
                $('#campaignDataView').hide();
                loadData(1);
            } else {
                $('#mainListView').hide();
                $('#campaignListView').fadeIn(200);
                $('#campaignDataView').hide();
                loadCampaigns();
            }
        });

        window.showCampaignList = function() {
            $('#campaignListView').fadeIn(200);
            $('#campaignDataView').hide();
        };

        function loadCampaigns() {
            $('#campaignGrid').html('<div class="p-5 text-center w-100"><span class="spinner-border spinner-border-sm"></span> Mapping campaigns...</div>');
            $.get('{{ route("calling.my.my-campaigns") }}', function(resp) {
                let html = '';
                resp.forEach(function(c) {
                    html += `
                    <div class="campaign-card" onclick="loadCampaignLeads(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                        <div class="camp-title">${c.name}</div>
                        <div class="camp-meta">
                            Dedicated prospects: <span class="camp-leads-count">${c.leads_count}</span>
                        </div>
                    </div>`;
                });
                if(!resp.length) html = '<div class="p-5 text-center w-100 text-muted">No isolated campaigns active.</div>';
                $('#campaignGrid').html(html);
            });
        }

        window.loadCampaignLeads = function(campId, campName) {
            currentCampaignId = campId;
            $('#selectedCampTitle').text(campName);
            $('#campaignListView').hide();
            $('#campaignDataView').fadeIn(200);
            $('#campDataTable tbody').html('<tr><td colspan="6" class="text-center p-5 text-muted"><span class="spinner-border spinner-border-sm"></span> Extracting campaign data...</td></tr>');

            $.post('{{ route("calling.my.filter") }}', { campaign_id: campId }).done(function(data) {
                let rows = data.data || [];
                let html = '';
                rows.forEach(function(r) {
                    let remarkText = r.latest_remark ? r.latest_remark.substring(0, 18) + (r.latest_remark.length > 18 ? '...' : '') : 'Start Interaction';
                    html += `<tr>
                        <td><div class="fw-bold">${r.name || '-'}</div></td>
                        <td><span class="text-muted small">${r.email || '-'}</span></td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td class="fw-semibold">${r.phone || '-'}</td>
                        <td>
                            <a href="/calling/${r.id}/remarks?campaign_id=${r.calling_campaign_id}" class="remark-link" title="${r.latest_remark || ''}">
                                <i class="bi bi-chat-dots-fill"></i> ${remarkText}
                            </a>
                        </td>
                    </tr>`;
                });
                if(!rows.length) html = '<tr><td colspan="6" class="text-center p-5">Target parameters returned zero results.</td></tr>';
                $('#campDataTable tbody').html(html);
            });
        }

        function loadFilterData() {
            $.get('{{ route("assignedleads.filter-options") }}', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">Global - All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.state_name+'</option>');
                });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">Global - All Cities</option>'); return; }
            $.get('{{ route("assignedleads.cities", ["stateId" => ":id"]) }}'.replace(':id', stateId), function(cities){
                var $city = $('#filter_city');
                $city.empty().append('<option value="">Global - All Cities</option>');
                (cities || []).forEach(function(c){ $city.append('<option value="'+c.id+'">'+c.city_name+'</option>'); });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    let remarkText = r.latest_remark ? r.latest_remark.substring(0, 18) + (r.latest_remark.length > 18 ? '...' : '') : 'Log Interaction';
                    html += `<tr>
                        <td><span class="badge bg-soft-primary text-primary border p-2 fw-semibold" style="background: #f0f4ff;">${r.campaign_name || 'Legacy'}</span></td>
                        <td><div class="fw-bold">${r.name || '-'}</div></td>
                        <td><span class="text-muted small">${r.email || '-'}</span></td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td class="fw-semibold">${r.phone || '-'}</td>
                        <td>
                            <a href="/calling/${r.id}/remarks?campaign_id=${r.calling_campaign_id}" class="remark-link" title="${r.latest_remark || ''}">
                                <i class="bi bi-chat-left-text-fill"></i> ${remarkText}
                            </a>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center p-5 text-muted">Primary data stream is empty.</td></tr>';
            }
            $tbody.html(html);
        }

        function loadData(page = 1) {
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            const appliedCount = [name, stateId, cityId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('{{ route("calling.my.filter") }}?page=' + page, {
                name: name, state_id: stateId, city_id: cityId
            }).done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalAssigned').text(data.total || 0);
                $('#lastUpdated').text(new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationLinks'); $container.empty();
            if (data.last_page <= 1) return;
            $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="bi bi-chevron-left"></i></a></li>`);
            $container.append(`<li class="page-item active"><span class="page-link px-3">${data.current_page} / ${data.last_page}</span></li>`);
            $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page + 1}"><i class="bi bi-chevron-right"></i></a></li>`);
            $('#rangeInfo').text(`Syncing ${data.from || 0} to ${data.to || 0} of ${data.total || 0} entities`);
        }

        loadFilterData(); loadData(1);

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); loadData(1); });
        $('#filter_city').on('change', () => loadData(1));
        $('#filter_name').on('input', function() { loadData(1); });
        $('#resetFilters').on('click', function() {
            $('#filter_name').val(''); $('#filter_state').val(''); $('#filter_city').html('<option value="">Global - All Cities</option>');
            loadData(1);
        });

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); loadData($(this).data('page')); });
    });
</script>
@endpush
