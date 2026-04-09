<?php $__env->startSection('title', 'Lock Calling'); ?>
<?php $__env->startSection('page_title', 'Lock Calling'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .calling-page { padding: 0.5rem; background: #f7f8fc; }
    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; width: 100%; margin-bottom: 1rem; }
    .hero-metric-card { background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.75rem 1rem; width: 100%; box-shadow: 0px 4px 4px 0px #0000000A; display: flex; align-items: center; gap: 0.75rem; }
    .hero-metric-icon { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hero-metric-icon img { width: 24px; height: 24px; object-fit: contain; }
    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .metric-label { font-size: 0.65rem; color: #000; text-transform: uppercase; font-weight: 600; font-family: Montserrat; }
    .metric-value { font-size: 1.2rem; font-weight: 700; color: #101828; font-family: Montserrat; }
    
    .filterBox { 
        display: grid; 
        grid-template-columns: 1fr 1fr 1fr 150px; 
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
    
    .filter-reset-btn { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 6px; background: rgba(255, 255, 255, 0.18); color: white; padding: 0.45rem; font-size: 10px; font-weight: 600; height: 35px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .filter-reset-btn:hover { background: rgba(255, 255, 255, 0.3); }
    
    .modern-card { border-radius: 8px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05); overflow: hidden; }
    .modern-card-header { padding: 1rem; border-bottom: 1px solid #eef0f6; display: flex; align-items: center; justify-content: space-between; background: white; }
    .section-eyebrow { font-size: 0.55rem; letter-spacing: 0.1em; text-transform: uppercase; color: #9ca3af; font-weight: 700; }
    .card-title-modern { margin: 0; font-size: 0.9rem; font-weight: 700; color: #101828; }
    
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .custom-table thead th { background: #fafbfc; color: #475467; font-size: 0.7rem; text-transform: uppercase; font-weight: 700; padding: 0.9rem 1rem; border-bottom: 1px solid #eaecf0; font-family: Montserrat; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.9rem 1rem; color: #344054; border-bottom: 1px solid #f2f4f7; font-family: Montserrat; }
    
    .btn-lock-leads { background: #434AFA; color: #fff; border: none; padding: 0.5rem 1.2rem; border-radius: 6px; font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; }
    .btn-lock-leads:hover { background: #3339d6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); }
    .btn-lock-leads:disabled { opacity: 0.6; cursor: not-allowed; }

    .chip-btn { border-radius: 6px; border: none; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s; }
    .chip-btn.ghost { background: #f3f5ff; color: #434afa; }
    .chip-btn.ghost:hover { background: #e8ebff; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <div id="alertContainer"></div>

    <!-- Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Total Records">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Campaign Leads</span>
                <span class="metric-value" id="totalCallings">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Selected">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Active Campaign</span>
                <span class="metric-value" id="currentCampaignName" style="font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 150px;">None</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Selected Count">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Selected Leads</span>
                <span class="metric-value" id="selectedCountMetric">0</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filterBox">
        <div>
            <label for="filter_campaign" class="form-label-modern"><i class="bi bi-megaphone"></i> Select Campaign</label>
            <select id="filter_campaign" class="form-control-modern">
                <option value="">-- Choose Campaign --</option>
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
            <button id="resetFilters" class="filter-reset-btn w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Search & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="input-group" style="max-width: 400px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="filter_name" class="form-control border-start-0" placeholder="Search by name or phone...">
        </div>
        <div class="d-flex gap-2">
            <button id="selectAllBtn" class="chip-btn ghost">
                <i class="bi bi-check2-square"></i> Select All
            </button>
            <button id="lockLeadsBtn" class="btn-lock-leads" disabled>
                <i class="bi bi-lock-fill"></i> Lock Selected (<span id="selectedCountText">0</span>)
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="modern-card">
        <div class="modern-card-header">
            <div>
                <p class="section-eyebrow mb-1">Lead Navigator</p>
                <h4 class="card-title-modern mb-0" id="tableTitle">Choose a campaign to start</h4>
            </div>
            <div id="selectionBadge" style="display:none;">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i> Ready to Lock
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table custom-table" id="callingTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">
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
                    <tr><td colspan="7" class="text-center p-5 text-muted">Please select a campaign from the dropdown above to view contacts.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="table-range-meta" id="callingRangeInfo">Showing 0-0 from 0 data</div>
        <div class="pagination-wrapper mb-0">
            <ul class="pagination" id="paginationFilterLinks"></ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        var $tbody = $('#callingTable tbody');
        let currentFilterPage = 1;

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function loadFilterData() {
            $.get('<?php echo e(route("calling.campaigns")); ?>', function(campaigns) {
                var $camp = $('#filter_campaign');
                $camp.empty().append('<option value="">-- Choose Campaign --</option>');
                (campaigns || []).forEach(function(c){
                    $camp.append('<option value="'+c.id+'">'+(c.name || 'Unnamed')+'</option>');
                });
            });

            $.get('<?php echo e(route("calling.filter-options")); ?>', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            var url = '<?php echo e(route("calling.cities", ["stateId" => 0])); ?>'.replace(/0$/, String(stateId));
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
                html = '<tr><td colspan="7" class="text-center p-4">No unassigned leads found.</td></tr>';
            }
            $tbody.html(html);
        }

        function applyFilters(page = 1) {
            currentFilterPage = page;
            var campaignId = $('#filter_campaign').val();
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            
            if (!campaignId) {
                $tbody.html('<tr><td colspan="7" class="text-center p-5 text-muted">Please select a campaign from the dropdown above to continue.</td></tr>');
                $('#totalCallings').text(0);
                $('#currentCampaignName').text('None');
                $('#tableTitle').text('Choose a campaign to start');
                $('#selectionBadge').hide();
                $('#paginationFilterLinks').empty();
                $('#selectAllCheckbox').prop('checked', false);
                updateSelectedCount();
                return;
            }

            const appliedCount = [name, stateId, cityId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('<?php echo e(route("calling.filter")); ?>?page=' + page, {
                campaign_id: campaignId,
                name: name,
                state_id: stateId,
                city_id: cityId,
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalCallings').text(data.total || 0);
                $('#tableTitle').text($('#filter_campaign option:selected').text());
                $('#currentCampaignName').text($('#filter_campaign option:selected').text());
                $('#selectionBadge').show();
                $('#selectAllCheckbox').prop('checked', false);
                updateSelectedCount();
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationFilterLinks');
            $container.empty();
            if (data.last_page <= 1) return;
            
            $container.append('<li class="page-item ' + (data.current_page === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">Previous</a></li>');
            $container.append('<li class="page-item active"><span class="page-link">' + data.current_page + ' / ' + data.last_page + '</span></li>');
            $container.append('<li class="page-item ' + (data.current_page === data.last_page ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">Next</a></li>');
            
            $('#callingRangeInfo').text('Showing ' + (data.from || 0) + '-' + (data.to || 0) + ' from ' + (data.total || 0) + ' data');
        }

        function updateSelectedCount() {
            var selectedCount = $('.row-checkbox:checked').length;
            $('#selectedCountText').text(selectedCount);
            $('#selectedCountMetric').text(selectedCount.toLocaleString());
            $('#lockLeadsBtn').prop('disabled', selectedCount === 0);
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show shadow-sm" style="border-radius:8px; border:none;" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            $('#alertContainer').html(alertHtml);
            setTimeout(() => { $('#alertContainer .alert').fadeOut(); }, 4000);
        }

        // Init
        loadFilterData();

        // Events
        $('#filter_campaign').on('change', () => applyFilters(1));
        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); applyFilters(1); });
        $('#filter_city').on('change', () => applyFilters(1));
        $('#filter_name').on('input', debounce(() => applyFilters(1), 300));
        
        $('#resetFilters').on('click', function() {
            $('#filter_name').val('');
            $('#filter_campaign').val('');
            $('#filter_state').val('');
            $('#filter_city').html('<option value="">All Cities</option>');
            applyFilters(1);
        });

        $('#selectAllCheckbox').on('change', function() {
            $('.row-checkbox').prop('checked', $(this).is(':checked'));
            updateSelectedCount();
        });

        $(document).on('change', '.row-checkbox', function() {
            updateSelectedCount();
            var total = $('.row-checkbox').length;
            var checked = $('.row-checkbox:checked').length;
            $('#selectAllCheckbox').prop('checked', total === checked);
            $('#selectAllCheckbox').prop('indeterminate', checked > 0 && checked < total);
        });

        $('#selectAllBtn').on('click', function() {
            var total = $('.row-checkbox').length;
            if (total === 0) return;
            var checked = $('.row-checkbox:checked').length;
            var newState = checked !== total;
            $('.row-checkbox').prop('checked', newState);
            $('#selectAllCheckbox').prop('checked', newState).prop('indeterminate', false);
            $(this).html(newState ? '<i class="bi bi-square"></i> Deselect All' : '<i class="bi bi-check2-square"></i> Select All');
            updateSelectedCount();
        });

        $('#lockLeadsBtn').on('click', function() {
            var selectedIds = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();
            var campaignId = $('#filter_campaign').val();
            if (!campaignId || selectedIds.length === 0) return;

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Locking...');

            $.post('<?php echo e(route("calling.lock-leads")); ?>', {
                campaign_id: campaignId,
                calling_ids: selectedIds,
                _token: '<?php echo e(csrf_token()); ?>'
            }).done(resp => {
                if (resp.success) {
                    showAlert('success', resp.message);
                    applyFilters(currentFilterPage); // Refresh current view
                } else {
                    showAlert('error', resp.message);
                }
            }).fail(xhr => {
                showAlert('error', 'Critical error during locking process.');
                console.error(xhr.responseText);
            }).always(() => {
                $btn.prop('disabled', false).html('<i class="bi bi-lock-fill"></i> Lock Selected (<span id="selectedCountText">0</span>)');
            });
        });

        $(document).on('click', '#paginationFilterLinks .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) applyFilters(page);
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/lock.blade.php ENDPATH**/ ?>