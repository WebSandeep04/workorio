<?php $__env->startSection('title', 'Lock calling'); ?>
<?php $__env->startSection('page_title', 'Lock Calling'); ?>

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
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

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
        text-transform: uppercase;
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
    .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #1f2937; border-bottom: 1px solid #f4f4f6; white-space: nowrap; }
    .custom-table tbody tr:hover { background: #f8f9ff; transform: translateY(-1px); }

    .btn-lock-leads { background: #434AFA; color: #fff; border: none; padding: 0.4rem 1rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem; display: flex; align-items: center; gap: 0.5rem; transition: all 0.3s; font-family: Montserrat; }
    .btn-lock-leads:hover:not(:disabled) { background: #3339d6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); }
    .btn-lock-leads:disabled { opacity: 0.6; cursor: not-allowed; }

    .chip-btn { border-radius: 4px; border: none; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s; font-family: Montserrat; }
    .chip-btn.ghost { background: #f3f5ff; color: #434afa; }
    .chip-btn.ghost:hover { background: #e8ebff; }

    .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-family: Montserrat; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }

    .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; font-family: Montserrat; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <!-- Hero Metrics -->
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
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Active Campaign">
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
            <label class="form-label-modern" style="visibility: hidden;">Reset</label>
            <button id="resetFilters" class="filter-reset-btn w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="filter_name" placeholder="Search by name, contact, or address..." />
    </div>

    <div class="data-table-card">
        <div class="table-scroll">
            <table class="table custom-table" id="callingTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                        </th>
                        <th>Lead Name</th>
                        <th>Email</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="text-center p-5 text-muted">Please select a campaign to view unassigned leads.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
        <div class="d-flex gap-2">
           <button id="selectAllBtn" class="chip-btn ghost">
                <i class="bi bi-check2-square"></i> Select All
            </button>
            <button id="lockLeadsBtn" class="btn-lock-leads" disabled>
                <i class="bi bi-lock-fill"></i> Lock Selected (<span id="selectedCountText">0</span>)
            </button>
        </div>
        <div class="d-flex flex-column align-items-end">
             <div class="table-range-meta" id="callingRangeInfo">Showing 0-0 from 0 data</div>
             <ul class="pagination mb-0" id="paginationFilterLinks"></ul>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        const $tbody = $('#callingTable tbody');
        let currentFilterPage = 1;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        function loadFilterData() {
            $.get('<?php echo e(route("calling.campaigns")); ?>', function(campaigns) {
                var $camp = $('#filter_campaign');
                $camp.empty().append('<option value="">-- Choose Campaign --</option>');
                (campaigns || []).forEach(function(c){ $camp.append('<option value="'+c.id+'">'+(c.name || 'Unnamed')+'</option>'); });
            });
            $.get('<?php echo e(route("calling.filter-options")); ?>', function(resp) {
                var $state = $('#filter_state'); $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){ $state.append('<option value="'+s.id+'">'+s.name+'</option>'); });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            $.get('<?php echo e(route("calling.cities", ["stateId" => ":id"])); ?>'.replace(':id', stateId), function(cities){
                var $city = $('#filter_city'); $city.empty().append('<option value="">All Cities</option>');
                (cities || []).forEach(function(c){ $city.append('<option value="'+c.id+'">'+c.name+'</option>'); });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    html += `<tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" value="${r.id}"></td>
                        <td>${r.name || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td class="text-truncate" style="max-width: 200px;" title="${r.address || ''}">${r.address || '-'}</td>
                        <td>${r.phone || '-'}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center p-5 text-muted">No unassigned leads found for this scope.</td></tr>';
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
                $tbody.html('<tr><td colspan="7" class="text-center p-5 text-muted">Please select a campaign to continue.</td></tr>');
                $('#totalCallings').text(0); $('#currentCampaignName').text('None');
                $('#paginationFilterLinks').empty(); $('#selectAllCheckbox').prop('checked', false);
                updateSelectedCount(); return;
            }

            const appliedCount = [name, stateId, cityId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('<?php echo e(route("calling.filter")); ?>?page=' + page, { campaign_id: campaignId, name, state_id: stateId, city_id: cityId })
            .done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalCallings').text((data.total || 0).toLocaleString('en-IN'));
                $('#currentCampaignName').text($('#filter_campaign option:selected').text());
                $('#selectAllCheckbox').prop('checked', false);
                updateSelectedCount();
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationFilterLinks'); $container.empty();
            if (data.last_page <= 1) return;
            $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="bi bi-chevron-left"></i> Previous</a></li>`);
            $container.append(`<li class="page-item active"><span class="page-link">${data.current_page} / ${data.last_page}</span></li>`);
            $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next <i class="bi bi-chevron-right"></i></a></li>`);
            $('#callingRangeInfo').text(`Showing ${data.from || 0}-${data.to || 0} from ${data.total || 0} data`);
        }

        function updateSelectedCount() {
            var selectedCount = $('.row-checkbox:checked').length;
            $('#selectedCountText').text(selectedCount);
            $('#selectedCountMetric').text(selectedCount.toLocaleString());
            $('#lockLeadsBtn').prop('disabled', selectedCount === 0);
        }

        loadFilterData();

        $('#filter_campaign').on('change', () => applyFilters(1));
        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); applyFilters(1); });
        $('#filter_city').on('change', () => applyFilters(1));
        $('#filter_name').on('input', function() { applyFilters(1); });
        $('#resetFilters').on('click', function() {
            $('#filter_name').val(''); $('#filter_campaign').val(''); $('#filter_state').val(''); $('#filter_city').html('<option value="">All Cities</option>');
            applyFilters(1);
        });

        $('#selectAllCheckbox').on('change', function() { $('.row-checkbox').prop('checked', $(this).is(':checked')); updateSelectedCount(); });
        $(document).on('change', '.row-checkbox', function() { updateSelectedCount(); });

        $('#selectAllBtn').on('click', function() {
            var newState = $('.row-checkbox:not(:checked)').length > 0;
            $('.row-checkbox').prop('checked', newState);
            $('#selectAllCheckbox').prop('checked', newState);
            updateSelectedCount();
        });

        $('#lockLeadsBtn').on('click', function() {
            var selectedIds = $('.row-checkbox:checked').map(function() { return $(this).val(); }).get();
            var campaignId = $('#filter_campaign').val();
            if (!campaignId || selectedIds.length === 0) return;

            var $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Locking...');

            $.post('<?php echo e(route("calling.lock-leads")); ?>', { campaign_id: campaignId, calling_ids: selectedIds })
            .done(resp => {
                if (resp.success) {
                    Swal.fire('Success', resp.message, 'success');
                    applyFilters(currentFilterPage);
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            }).always(() => {
                $btn.prop('disabled', false).html('<i class="bi bi-lock-fill"></i> Lock Selected (<span id="selectedCountText">0</span>)');
            });
        });

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); applyFilters($(this).data('page')); });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/lock.blade.php ENDPATH**/ ?>