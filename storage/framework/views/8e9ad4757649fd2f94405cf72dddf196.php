

<?php $__env->startSection('title', 'Junk Calling'); ?>
<?php $__env->startSection('page_title', 'Junk Calling'); ?>

<?php $__env->startPush('styles'); ?>
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

    .table-range-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0.35rem 0 0.75rem;
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
        padding: 0px;
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

    .modern-card {
        padding: 0;
        margin-bottom: 0.5rem;
    }

    .modern-card-body {
        padding: 0px;
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

    .data-table-card .custom-table tbody td:nth-child(1) { min-width: 160px; }
    .data-table-card .custom-table tbody td:nth-child(2) { min-width: 160px; }
    .data-table-card .custom-table tbody td:nth-child(3) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(4) { min-width: 120px; }
    .data-table-card .custom-table tbody td:nth-child(5) { min-width: 120px; }
    .data-table-card .custom-table tbody td:nth-child(6) { min-width: 180px; }
    .data-table-card .custom-table tbody td:nth-child(7) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(8) { min-width: 220px; }
    .data-table-card .custom-table tbody td:nth-child(9) { min-width: 130px; }

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

    #alertContainer .alert,
    .alert-holder .alert {
        border-radius: 12px;
        border: none;
        padding: 0.85rem 1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .action-btn {
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

    .action-btn.danger {
        background: linear-gradient(135deg, #ff0844 0%, #ff4b2b 100%);
        color: #fff;
        box-shadow: 0 2px 4px rgba(255, 75, 43, 0.25);
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <div class="calling-hero-card">
        <div>
            <p class="eyebrow-text">Cleanup queue</p>
            <h2 class="hero-title">Junk call review</h2>
            <p class="mb-0">Review disqualified prospects, inspect remarks instantly, and permanently purge records when needed.</p>
        </div>
        <div class="hero-metrics">
            <div class="hero-metric-card">
                <div class="hero-metric-icon icon-rose">
                    <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Total Junk">
                </div>
                <div class="hero-metric-content">
                    <span class="metric-label">Total junk</span>
                    <span class="metric-value" id="totalJunk">0</span>
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
                    <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Last Purge">
                </div>
                <div class="hero-metric-content">
                    <span class="metric-label">Last purge</span>
                    <span class="metric-value" id="lastDeleted">--</span>
                </div>
            </div>
        </div>
    </div>

    <div id="alertContainer" class="alert-holder"></div>

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
        </div>
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="callingTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Calling Type</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th style="width: 180px;">Remarks</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-range-meta" id="junkCallingRangeInfo">
        Showing 0-0 from 0 data
    </div>

    <div class="pagination-wrapper">
        <ul class="pagination" id="paginationLinks"></ul>
    </div>
    <div class="pagination-wrapper">
        <ul class="pagination" id="paginationFilterLinks"></ul>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this junk call?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone. The call and all its remarks will be permanently deleted from the database.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function () {
        const $tbody = $('#callingTable tbody');
        let currentPage = 1;
        let currentFilterPage = 1;
        let lastDeletedTime = '--';

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const remarksUrlTemplate = '<?php echo e(route("calling.remarks.show", ["calling" => "__ID__"])); ?>';

        const formatNumber = (value) => Number(value || 0).toLocaleString('en-IN');

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="bi ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            $('#alertContainer').html(alertHtml);
            setTimeout(() => $('#alertContainer .alert').fadeOut(() => $(this).remove()), 4000);
        }

        function updateTotals(meta) {
            let total = 0;
            if (meta && typeof meta.total !== 'undefined') {
                total = meta.total;
            } else if (Array.isArray(meta)) {
                total = meta.length;
            } else if (meta && meta.data) {
                total = meta.data.length;
            }
            $('#totalJunk').text(formatNumber(total));
            $('#lastDeleted').text(lastDeletedTime);
        }

        function setActiveFiltersCount(count) {
            $('#activeFilters').text(formatNumber(count));
        }

        function loadStates() {
            $.get('<?php echo e(route("calling.junk.filter-options")); ?>', function (resp) {
                const $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function (st) {
                    $state.append('<option value="' + st.id + '">' + st.name + '</option>');
                });
            });
        }

        function loadCities(stateId) {
            if (!stateId) {
                $('#filter_city').html('<option value="">All Cities</option>');
                return;
            }
            $.get('<?php echo e(route("calling.junk.cities", ":stateId")); ?>'.replace(':stateId', stateId), function (resp) {
                const $city = $('#filter_city');
                $city.empty().append('<option value="">All Cities</option>');
                (resp || []).forEach(function (ct) {
                    $city.append('<option value="' + ct.id + '">' + ct.name + '</option>');
                });
            });
        }

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

        function renderRows(callings) {
            let html = '';
            if (!callings.length) {
                html = '<tr><td colspan="9" class="text-center">No junk calls found.</td></tr>';
            } else {
                callings.forEach(function (calling) {
                    const remarkText = calling.latest_remark ? calling.latest_remark.remark : '';
                    let shortRemark = remarkText ? (remarkText.length > 12 ? remarkText.substring(0, 12) + '...' : remarkText) : '-';
                    const remarkUrl = remarksUrlTemplate.replace('__ID__', calling.id);
                    html += `
                        <tr>
                            <td>${calling.name || '-'}</td>
                            <td>${calling.email || '-'}</td>
                            <td>${calling.calling_type ? calling.calling_type.name : '-'}</td>
                            <td>${calling.state ? calling.state.state_name : '-'}</td>
                            <td>${calling.city ? calling.city.city_name : '-'}</td>
                            <td>${calling.address || '-'}</td>
                            <td>${calling.phone || calling.mobile || '-'}</td>
                            <td><a href="${remarkUrl}" class="remark-link" title="${remarkText}">${shortRemark}</a></td>
                            <td>
                                <button class="action-btn restore-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);" data-id="${calling.id}">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                                <button class="action-btn danger delete-btn" data-id="${calling.id}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>`;
                });
            }
            $tbody.html(html);
        }

        function loadCallings(page = 1) {
            currentPage = page;
            $.get('<?php echo e(route("calling.junk.data")); ?>?page=' + page, function (data) {
                const rows = Array.isArray(data) ? data : (data.data || []);
                renderRows(rows);
                renderPagination(data);
                updateTotals(data);
                setActiveFiltersCount(0);
            });
        }

        function renderPagination(data) {
            const $pagination = $('#paginationLinks');
            buildDetailedPagination($pagination, data.current_page, data.last_page);
            $('#paginationLinks').show();
            $('#paginationFilterLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function renderFilterPagination(data) {
            const $pagination = $('#paginationFilterLinks');
            buildDetailedPagination($pagination, data.current_page, data.last_page);
            $('#paginationFilterLinks').show();
            $('#paginationLinks').hide();
            updateRangeInfo(data.from, data.to, data.total);
        }

        function updateRangeInfo(from, to, total) {
            const $info = $('#junkCallingRangeInfo');
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

        function filterCallings(page = 1) {
            currentFilterPage = page;
            const filters = {
                name: $('#filter_name').val(),
                state_id: $('#filter_state').val(),
                city_id: $('#filter_city').val()
            };
            const applied = Object.values(filters).filter(Boolean).length;
            if (!applied) {
                setActiveFiltersCount(0);
                loadCallings(1);
                return;
            }
            setActiveFiltersCount(applied);
            $.post('<?php echo e(route("calling.junk.filter")); ?>?page=' + page, filters, function (data) {
                const rows = Array.isArray(data) ? data : (data.data || []);
                renderRows(rows);
                renderFilterPagination(data);
                updateTotals(data);
            });
        }

        $('#filter_state').on('change', function () {
            const stateId = $(this).val();
            loadCities(stateId);
            filterCallings(1);
        });
        $('#filter_city').on('change', function () { filterCallings(1); });
        $('#filter_name').on('input', function () { filterCallings(1); });

        $('#resetFilters').on('click', function (e) {
            e.preventDefault();
            $('#filter_name').val('');
            $('#filter_state').val('');
            $('#filter_city').html('<option value="">All Cities</option>');
            setActiveFiltersCount(0);
            loadCallings(1);
        });

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
                filterCallings(page);
            }
        });

        let deleteId = null;
        $(document).on('click', '.delete-btn', function () {
            deleteId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDelete').on('click', function () {
            if (!deleteId) return;
            $.ajax({
                url: '<?php echo e(route("calling.junk.destroy", ":id")); ?>'.replace(':id', deleteId),
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    $('#deleteModal').modal('hide');
                    if (response.success) {
                        lastDeletedTime = new Date().toLocaleString();
                        showAlert('success', response.message || 'Deleted successfully');
                        loadCallings(currentPage);
                    } else {
                        showAlert('error', response.message || 'Failed to delete junk call');
                    }
                },
                error: function () {
                    $('#deleteModal').modal('hide');
                    showAlert('error', 'Failed to delete junk call');
                }
            });
        });

        $(document).on('click', '.restore-btn', function () {
            const id = $(this).data('id');
            if (!confirm('Are you sure you want to restore this junk call?')) return;
            $.ajax({
                url: '<?php echo e(route("calling.junk.restore", ":id")); ?>'.replace(':id', id),
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        showAlert('success', response.message || 'Restored successfully');
                        loadCallings(currentPage);
                    } else {
                        showAlert('error', response.message || 'Failed to restore junk call');
                    }
                },
                error: function () {
                    showAlert('error', 'Failed to restore junk call');
                }
            });
        });

        loadStates();
        loadCallings();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/junk.blade.php ENDPATH**/ ?>