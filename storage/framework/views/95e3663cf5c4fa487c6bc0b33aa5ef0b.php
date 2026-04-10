<?php $__env->startSection('title', 'My Calling'); ?>
<?php $__env->startSection('page_title', 'My Calling'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .calling-page {
        padding: 0.5rem;
        background: #f7f8fc;
    }

    /* Hero Metrics */
    .hero-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
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

    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }

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

    /* View Toggle */
    .view-toggle-wrapper { margin-bottom: 1rem; display: flex; gap: 0.25rem; background: #eaecf0; padding: 4px; border-radius: 8px; width: fit-content; }
    .view-toggle-btn { border: none; padding: 0.4rem 1rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700; /* text-transform: uppercase; removed */ transition: all 0.3s ease; background: transparent; color: #475467; font-family: Montserrat; }
    .view-toggle-btn.active { background: #fff; color: #434afa; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    /* Filter Box */
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

    .form-label-modern {
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        letter-spacing: 0.05em;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern {
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 6px;
        padding: 0.35rem 0.5rem;
        background: #fff;
        color: #000;
        font-size: 10px;
        font-family: Montserrat, sans-serif;
        width: 100%;
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
    .filter-reset-btn:hover { background: rgba(255, 255, 255, 0.28); }

    /* Search Section */
    .table-search-field {
        width: 100%;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f4f5f7;
        border: 1px solid #e5e7eb;
        border-radius: 2px;
        padding: 0.35rem 0.9rem;
        margin-bottom: 1rem;
    }
    .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
    .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; font-family: Montserrat; }

    /* Table System */
    .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; margin-bottom: 1rem; }
    .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
    .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-family: Montserrat; }
    .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.6rem 0.75rem; color: #1f2937; border-bottom: 1px solid #f4f4f6; white-space: nowrap; }
    .custom-table tbody tr:hover { background: #f8f9ff; transform: translateY(-1px); }
    
    .remark-link { color: #434AFA; text-decoration: none; font-weight: 500; }
    .remark-link:hover { text-decoration: underline; }

    .assign-select {
        font-size: 11px;
        padding: 4px 6px;
        border-radius: 4px;
        border: 1px solid #d0d5dd;
        font-family: Montserrat;
        width: 140px;
    }

    /* Campaign Cards */
    .campaign-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    .campaign-card { background: #fff; border: 1px solid #eceef3; border-radius: 10px; padding: 1.25rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0px 4px 4px 0px #0000000A; font-family: Montserrat; }
    .campaign-card:hover { transform: translateY(-2px); border-color: #434AFA; box-shadow: 0px 8px 15px rgba(67, 74, 250, 0.1); }
    .camp-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; color: #101828; }
    .camp-meta { font-size: 0.75rem; color: #6b7280; font-weight: 500; }
    .camp-leads-count { color: #434AFA; font-weight: 700; }

    .back-to-camp { cursor: pointer; color: #434AFA; font-weight: 600; margin-bottom: 1rem; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-family: Montserrat; transition: 0.2s; }
    .back-to-camp:hover { transform: translateX(-4px); }

    .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-family: Montserrat; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }

    .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; font-family: Montserrat; }

    #campaignListView, #campaignDataView { display: none; }
    
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <!-- Hero Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Total Assigned">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Assigned</span>
                <span class="metric-value" id="totalAssigned">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Active Filters">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Active Filters</span>
                <span class="metric-value" id="activeFilters">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Latest Update">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Latest update</span>
                <span class="metric-value" id="lastUpdated">--</span>
            </div>
        </div>
    </div>

    <!-- Toggle View -->
    <div class="view-toggle-wrapper">
        <button class="view-toggle-btn active" data-view="list">Detailed View</button>
        <button class="view-toggle-btn" data-view="campaign">Campaign Matrix</button>
    </div>

    <!-- 1. DETAILED VIEW -->
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

        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="filter_name" placeholder="Quick search by name or contact..." />
        </div>

        <div class="data-table-card">
            <div class="table-scroll">
                <table class="table custom-table" id="callingTable">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Lead Name</th>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Legal Status</th>
                            <th>GST No</th>
                            <th>Turnover</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Contact</th>
                            <th style="width: 150px;">Assign To</th>
                            <th style="width: 160px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" class="text-center p-4 text-muted">Awaiting data stream...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-2 px-1">
            <div class="table-range-meta" id="rangeInfo">Showing 0-0 from 0 data</div>
            <ul class="pagination mb-0" id="paginationLinks"></ul>
        </div>
    </div>

    <!-- 2. CAMPAIGN MATRIX -->
    <div id="campaignListView">
        <div class="campaign-list" id="campaignGrid"></div>
    </div>

    <!-- 3. DRILLDOWN -->
    <div id="campaignDataView">
        <div class="back-to-camp" onclick="showCampaignList()"><i class="bi bi-arrow-left"></i> Return to Matrix</div>
        <div class="data-table-card">
            <div class="p-3 border-bottom bg-white"><h5 class="m-0 fw-bold" id="selectedCampTitle" style="font-size: 0.9rem; font-family: Montserrat;">Scope</h5></div>
            <div class="table-scroll">
                <table class="table custom-table" id="campDataTable">
                    <thead>
                        <tr>
                            <th>Identity</th>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Communication</th>
                            <th>Legal Status</th>
                            <th>GST No</th>
                            <th>Region</th>
                            <th>District</th>
                            <th>Phone</th>
                            <th style="width: 150px;">Handover</th>
                            <th style="width: 160px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        const $tbody = $('#callingTable tbody');
        let currentCampaignId = null;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

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
            $('#campaignGrid').html('<div class="p-5 text-center w-100 text-muted">Indexing campaign records...</div>');
            $.get('<?php echo e(route("calling.my.my-campaigns")); ?>', function(resp) {
                let html = '';
                resp.forEach(function(c) {
                    html += `
                    <div class="campaign-card" onclick="loadCampaignLeads(${c.id}, '${c.name.replace(/'/g, "\\'")}')">
                        <div class="camp-title">${c.name}</div>
                        <div class="camp-meta">
                            Dedicated leads: <span class="camp-leads-count">${c.leads_count}</span>
                        </div>
                    </div>`;
                });
                if(!resp.length) html = '<div class="p-5 text-center w-100 text-muted">No isolated campaigns found.</div>';
                $('#campaignGrid').html(html);
            });
        }

        window.loadCampaignLeads = function(campId, campName) {
            currentCampaignId = campId;
            $('#selectedCampTitle').text(campName);
            $('#campaignListView').hide();
            $('#campaignDataView').fadeIn(200);
            $('#campDataTable tbody').html('<tr><td colspan="7" class="text-center p-4 text-muted">Executing query...</td></tr>');

            $.post('<?php echo e(route("calling.my.filter")); ?>', { campaign_id: campId }).done(function(data) {
                let rows = data.data || [];
                let html = '';
                rows.forEach(function(r) {
                    let remarkText = r.latest_remark ? r.latest_remark.substring(0, 15) + (r.latest_remark.length > 15 ? '...' : '') : 'Add remark';
                    let dropdown = getTeamDropdown(r.id, r.calling_campaign_id);
                    html += `<tr id="row-${r.id}-${r.calling_campaign_id}">
                        <td>${r.name || '-'}</td>
                        <td>${r.company_name || '-'}</td>
                        <td>${r.contact_person || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.legal_status || '-'}</td>
                        <td>${r.gst_number || '-'}</td>
                        <td>${r.turnover || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.phone || '-'}</td>
                        <td>${dropdown}</td>
                        <td>
                            <a href="/calling/${r.id}/remarks?campaign_id=${r.calling_campaign_id}" class="remark-link" title="${r.latest_remark || ''}">
                                <i class="bi bi-chat-text-fill"></i> ${remarkText}
                            </a>
                        </td>
                    </tr>`;
                });
                if(!rows.length) html = '<tr><td colspan="7" class="text-center p-4">Empty result set.</td></tr>';
                $('#campDataTable tbody').html(html);
            });
        }

        function getTeamDropdown(id, campId) {
            let options = '<option value="">Assign To...</option>';
            if (window.teamMembers && window.teamMembers.length > 0) {
                window.teamMembers.forEach(m => {
                    options += `<option value="${m.id}">${m.name}</option>`;
                });
            }
            return `<select class="assign-select" onchange="performReassign(${id}, ${campId}, this.value)">${options}</select>`;
        }

        window.performReassign = function(callingId, campId, newUserId) {
            if (!newUserId) return;
            if (!confirm('Reassign this lead to the selected user? It will be removed from your list.')) return;

            $.post('<?php echo e(route("calling.my.reassign")); ?>', {
                calling_id: callingId,
                campaign_id: campId,
                new_user_id: newUserId
            }).done(function(resp) {
                if (resp.success) {
                    $(`#row-${callingId}-${campId}`).fadeOut(300, function() { $(this).remove(); });
                    // Also update hero count
                    let count = parseInt($('#totalAssigned').text().replace(/,/g, '')) - 1;
                    $('#totalAssigned').text(count.toLocaleString('en-IN'));
                }
            });
        }

        function loadFilterData() {
            $.get('<?php echo e(route("calling.my.filter-options")); ?>', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });

                var $type = $('#filter_calling_type');
                $type.empty().append('<option value="">All Types</option>');
                (resp.calling_types || []).forEach(function(t){
                    $type.append('<option value="'+t.id+'">'+t.name+'</option>');
                });
            });
        }

        function loadTeamMembers(callback) {
            $.get('<?php echo e(route("calling.my.team-members")); ?>', function(resp) {
                window.teamMembers = resp || [];
                if(callback) callback();
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            $.get('<?php echo e(route("calling.my.cities", ["stateId" => ":id"])); ?>'.replace(':id', stateId), function(cities){
                var $city = $('#filter_city');
                $city.empty().append('<option value="">All Cities</option>');
                (cities || []).forEach(function(c){ $city.append('<option value="'+c.id+'">'+c.name+'</option>'); });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    let remarkText = r.latest_remark ? r.latest_remark.substring(0, 15) + (r.latest_remark.length > 15 ? '...' : '') : 'Remarks';
                    let dropdown = getTeamDropdown(r.id, r.calling_campaign_id);
                    html += `<tr id="row-${r.id}-${r.calling_campaign_id}">
                        <td>${r.campaign_name || 'Legacy'}</td>
                        <td>${r.name || '-'}</td>
                        <td>${r.company_name || '-'}</td>
                        <td>${r.contact_person || '-'}</td>
                        <td>${r.legal_status || '-'}</td>
                        <td>${r.gst_number || '-'}</td>
                        <td>${r.turnover || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.phone || '-'}</td>
                        <td>${dropdown}</td>
                        <td>
                            <a href="/calling/${r.id}/remarks?campaign_id=${r.calling_campaign_id}" class="remark-link" title="${r.latest_remark || ''}">
                                <i class="bi bi-chat-left-dots-fill"></i> ${remarkText}
                            </a>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="8" class="text-center p-5 text-muted">No records matching current scope.</td></tr>';
            }
            $tbody.html(html);
        }

        function loadData(page = 1) {
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            var typeId = $('#filter_calling_type').val();
            const appliedCount = [name, stateId, cityId, typeId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('<?php echo e(route("calling.my.filter")); ?>?page=' + page, {
                name: name, state_id: stateId, city_id: cityId, calling_type_id: typeId
            }).done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalAssigned').text((data.total || 0).toLocaleString('en-IN'));
                $('#lastUpdated').text(new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationLinks'); $container.empty();
            if (data.last_page <= 1) return;
            $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="bi bi-chevron-left"></i> Previous</a></li>`);
            $container.append(`<li class="page-item active"><span class="page-link">${data.current_page} / ${data.last_page}</span></li>`);
            $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next <i class="bi bi-chevron-right"></i></a></li>`);
            $('#rangeInfo').text(`Showing ${data.from || 0}-${data.to || 0} from ${data.total || 0} data`);
        }

        loadFilterData(); 
        loadTeamMembers(() => {
            loadData(1);
        });

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); loadData(1); });
        $('#filter_city').on('change', () => loadData(1));
        $('#filter_calling_type').on('change', () => loadData(1));
        $('#filter_name').on('input', function() { loadData(1); });
        $('#resetFilters').on('click', function() {
            $('#filter_name').val(''); $('#filter_state').val(''); $('#filter_city').html('<option value="">All Cities</option>');
            $('#filter_calling_type').val('');
            loadData(1);
        });

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); loadData($(this).data('page')); });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/mycalling.blade.php ENDPATH**/ ?>