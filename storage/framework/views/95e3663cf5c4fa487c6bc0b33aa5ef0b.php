<?php $__env->startSection('title', 'My Calling'); ?>
<?php $__env->startSection('page_title', 'My Calling'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .calling-page { padding: 0.5rem; background: #f7f8fc; }
    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; width: 100%; margin-bottom: 1rem; }
    .hero-metric-card { background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.75rem 1rem; width: 100%; box-shadow: 0px 4px 4px 0px #0000000A; display: flex; align-items: center; gap: 0.75rem; }
    .hero-metric-icon { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hero-metric-icon img { width: 24px; height: 24px; object-fit: contain; }
    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .metric-label { font-size: 0.65rem; color: #000; text-transform: uppercase; font-weight: 600; font-family: Montserrat; }
    .metric-value { font-size: 1.2rem; font-weight: 700; color: #101828; font-family: Montserrat; }
    
    .view-toggle-wrapper { margin-bottom: 1rem; display: flex; gap: 0.5rem; background: #eaecf0; padding: 4px; border-radius: 8px; width: fit-content; }
    .view-toggle-btn { border: none; padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; transition: all 0.3s ease; background: transparent; color: #475467; }
    .view-toggle-btn.active { background: #fff; color: #434afa; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    .filterBox { 
        display: grid; 
        grid-template-columns: 1fr 1fr 150px; 
        gap: 0.75rem; 
        background: #434AFA; 
        padding: 1rem; 
        border-radius: 8px; 
        color: #fff; 
        margin-bottom: 1rem; 
        align-items: end;
    }
    .form-label-modern { color: #fff; font-size: 10px; font-weight: 600; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.25rem; text-transform: uppercase; font-family: Montserrat; }
    .form-control-modern { border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 6px; padding: 0.45rem 0.6rem; background: #fff; color: #000; font-size: 11px; font-family: Montserrat; width: 100%; }
    
    .filter-reset-btn { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 6px; background: rgba(255, 255, 255, 0.18); color: white; padding: 0.45rem; font-size: 10px; font-weight: 600; height: 35px; display: flex; align-items: center; justify-content: center; }
    
    .modern-card { border-radius: 8px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05); overflow: hidden; }
    .modern-card-header { padding: 1rem; border-bottom: 1px solid #eef0f6; display: flex; align-items: center; justify-content: space-between; background: white; }
    .section-eyebrow { font-size: 0.55rem; letter-spacing: 0.1em; text-transform: uppercase; color: #9ca3af; font-weight: 700; }
    .card-title-modern { margin: 0; font-size: 0.9rem; font-weight: 700; color: #101828; }
    
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .custom-table thead th { background: #fafbfc; color: #475467; font-size: 0.7rem; text-transform: uppercase; font-weight: 700; padding: 0.9rem 1rem; border-bottom: 1px solid #eaecf0; font-family: Montserrat; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.9rem 1rem; color: #344054; border-bottom: 1px solid #f2f4f7; font-family: Montserrat; }
    
    .campaign-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .campaign-card { background: #fff; border: 1px solid #eaecf0; border-radius: 12px; padding: 1.25rem; transition: all 0.3s ease; cursor: pointer; position: relative; overflow: hidden; }
    .campaign-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px rgba(0,0,0,0.08); border-color: #434afa; }
    .campaign-card::after { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: #434afa; opacity: 0; transition: 0.3s; }
    .campaign-card:hover::after { opacity: 1; }
    .camp-title { font-size: 1rem; font-weight: 700; color: #101828; margin-bottom: 0.5rem; }
    .camp-meta { font-size: 0.75rem; color: #667085; display: flex; align-items: center; gap: 0.5rem; }
    .camp-leads-count { background: #eef2ff; color: #434afa; padding: 2px 8px; border-radius: 999px; font-weight: 600; }

    .pagination-wrapper { margin-top: 1.5rem; display: flex; justify-content: center; }
    .pagination .page-link { color: #434afa; border: 1px solid #dee2e6; padding: 0.25rem 0.75rem; margin: 0 2px; border-radius: 4px; font-size: 0.75rem; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: #fff; }

    #campaignDataView { display: none; }
    .back-to-camp { display: inline-flex; align-items: center; gap: 0.5rem; color: #434afa; font-weight: 600; font-size: 0.85rem; margin-bottom: 1rem; cursor: pointer; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <div id="alertContainer"></div>

    <!-- Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Total Assigned">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total leads</span>
                <span class="metric-value" id="totalAssigned">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Active Filters">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Active filters</span>
                <span class="metric-value" id="activeFilters">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Last Updated">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Last updated</span>
                <span class="metric-value" id="lastUpdated" style="font-size: 0.9rem;">--</span>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="view-toggle-wrapper">
        <button class="view-toggle-btn active" data-view="list"><i class="bi bi-list-ul"></i> All Leads</button>
        <button class="view-toggle-btn" data-view="campaign"><i class="bi bi-folder2-open"></i> Campaign View</button>
    </div>

    <!-- MAIN LIST VIEW SECTION -->
    <div id="mainListView">
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
                <button id="resetFilters" class="filter-reset-btn w-100">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>

        <div class="mb-3" style="max-width: 400px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="filter_name" class="form-control border-start-0" placeholder="Search by name or phone...">
            </div>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <div>
                    <p class="section-eyebrow mb-1">Assigned leads</p>
                    <h4 class="card-title-modern mb-0">My contact book</h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table custom-table" id="callingTable">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Phone</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" class="text-center p-5 text-muted">Loading leads...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small" id="rangeInfo">Showing 0-0 from 0 data</div>
            <div class="pagination-wrapper mb-0">
                <ul class="pagination" id="paginationLinks"></ul>
            </div>
        </div>
    </div>

    <!-- CAMPAIGN LIST VIEW SECTION -->
    <div id="campaignListView" style="display: none;">
        <div class="campaign-list" id="campaignGrid">
            <!-- Campaign cards here -->
        </div>
    </div>

    <!-- CAMPAIGN DATA VIEW (TABLE FOR SPECIFIC CAMP) -->
    <div id="campaignDataView">
        <div class="back-to-camp" onclick="showCampaignList()"><i class="bi bi-arrow-left"></i> Back to Campaigns</div>
        <div class="modern-card">
            <div class="modern-card-header">
                <div>
                    <p class="section-eyebrow mb-1">Campaign leads</p>
                    <h4 class="card-title-modern" id="selectedCampTitle">Campaign Name</h4>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table custom-table" id="campDataTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Phone</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('partials.remarks-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        var $tbody = $('#callingTable tbody');
        let currentMode = 'list'; // 'list' or 'campaign'
        let currentCampaignId = null;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Toggle Views
        $('.view-toggle-btn').on('click', function() {
            const view = $(this).data('view');
            $('.view-toggle-btn').removeClass('active');
            $(this).addClass('active');

            if (view === 'list') {
                $('#mainListView').show();
                $('#campaignListView').hide();
                $('#campaignDataView').hide();
                loadData(1);
            } else {
                $('#mainListView').hide();
                $('#campaignListView').show();
                $('#campaignDataView').hide();
                loadCampaigns();
            }
        });

        window.showCampaignList = function() {
            $('#campaignListView').show();
            $('#campaignDataView').hide();
        };

        function loadCampaigns() {
            $('#campaignGrid').html('<div class="p-5 text-center w-100">Loading campaigns...</div>');
            $.get('<?php echo e(route("calling.my.my-campaigns")); ?>', function(resp) {
                let html = '';
                resp.forEach(function(c) {
                    html += `
                    <div class="campaign-card" onclick="loadCampaignLeads(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                        <div class="camp-title">${c.name}</div>
                        <div class="camp-meta">
                            <span>Leads assigned:</span>
                            <span class="camp-leads-count">${c.leads_count}</span>
                        </div>
                    </div>`;
                });
                if(!resp.length) html = '<div class="p-5 text-center w-100">No campaigns found.</div>';
                $('#campaignGrid').html(html);
            });
        }

        window.loadCampaignLeads = function(campId, campName) {
            currentCampaignId = campId;
            $('#selectedCampTitle').text(campName);
            $('#campaignListView').hide();
            $('#campaignDataView').show();
            $('#campDataTable tbody').html('<tr><td colspan="6" class="text-center p-4">Loading leads...</td></tr>');

            $.post('<?php echo e(route("calling.my.filter")); ?>', {
                campaign_id: campId,
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(function(data) {
                let rows = data.data || [];
                let html = '';
                rows.forEach(function(r) {
                    html += `<tr>
                        <td>${r.name || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.phone || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary show-remarks" data-id="${r.id}">
                                <i class="bi bi-chat-text"></i> Remarks
                            </button>
                        </td>
                    </tr>`;
                });
                if(!rows.length) html = '<tr><td colspan="6" class="text-center p-4">No leads in this campaign.</td></tr>';
                $('#campDataTable tbody').html(html);
            });
        }

        // Standard Filter/Data logic
        function loadFilterData() {
            $.get('<?php echo e(route("calling.my.filter-options")); ?>', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            var url = '<?php echo e(route("calling.my.cities", ["stateId" => 0])); ?>'.replace(/0$/, String(stateId));
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
                    html += `<tr>
                        <td>${r.campaign_name || '-'}</td>
                        <td>${r.name || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.phone || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary show-remarks" data-id="${r.id}">
                                <i class="bi bi-chat-text"></i> Remarks
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center p-4">No leads found.</td></tr>';
            }
            $tbody.html(html);
        }

        function loadData(page = 1) {
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            const appliedCount = [name, stateId, cityId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('<?php echo e(route("calling.my.filter")); ?>?page=' + page, {
                name: name,
                state_id: stateId,
                city_id: cityId,
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalAssigned').text(data.total || 0);
                $('#lastUpdated').text(new Date().toLocaleTimeString());
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationLinks');
            $container.empty();
            if (data.last_page <= 1) return;
            $container.append('<li class="page-item ' + (data.current_page === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">Previous</a></li>');
            $container.append('<li class="page-item active"><span class="page-link">' + data.current_page + ' / ' + data.last_page + '</span></li>');
            $container.append('<li class="page-item ' + (data.current_page === data.last_page ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">Next</a></li>');
            $('#rangeInfo').text('Showing ' + (data.from || 0) + '-' + (data.to || 0) + ' from ' + (data.total || 0) + ' data');
        }

        loadFilterData();
        loadData(1);

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); loadData(1); });
        $('#filter_city').on('change', () => loadData(1));
        $('#filter_name').on('input', debounce(() => loadData(1), 300));
        $('#resetFilters').on('click', function() {
            $('#filter_name').val(''); $('#filter_state').val(''); $('#filter_city').html('<option value="">All Cities</option>');
            loadData(1);
        });

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); loadData($(this).data('page')); });
        $(document).on('click', '.show-remarks', function() {
            const id = $(this).data('id');
            if(window.showRemarksModal) window.showRemarksModal(id);
        });

        function debounce(fn, delay) {
            let t; return function() {
                clearTimeout(t); const args = arguments; const ctx = this;
                t = setTimeout(() => { fn.apply(ctx, args); }, delay);
            };
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/mycalling.blade.php ENDPATH**/ ?>