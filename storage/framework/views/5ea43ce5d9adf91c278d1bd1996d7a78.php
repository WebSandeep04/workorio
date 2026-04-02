<?php $__env->startSection('title', 'Assigned Gen Lead'); ?>
<?php $__env->startSection('page_title', 'Assigned Gen Lead'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .container-fluid {
        padding: 0.5rem;
        padding-right: 0.5rem;
        margin-right: 0;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .summary-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef3;
        padding: 0.4rem;
        box-shadow: 0px 4px 4px 0px #0000000A;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
        min-height: 55px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .summary-card-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .summary-card-icon img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }

    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }

    .summary-card-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        min-width: 0;
    }

    .summary-card-label {
        font-size: 8px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.15rem;
        color: #000;
        font-family: Montserrat;
    }

    .summary-card-value {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0;
        color: #101828;
        font-family: Montserrat;
    }

    .filterBox {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.5rem;
        background: #434AFA;
        padding: 0.75rem;
        color: #fff;
        border: 1px solid #434AFA;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
        margin-bottom: 0.5rem;
        font-family: Montserrat, sans-serif;
    }

    .form-label-modern {
        color: #fff;
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 10px;
    }

    .form-control-modern {
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 2px;
        padding: 0.35rem 0.5rem;
        background: #fff;
        color: #000;
        font-size: 10px;
    }

    .table-search {
        width: 100%;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .table-search-field {
        flex: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f4f5f7;
        border: 1px solid #e5e7eb;
        border-radius: 2px;
        padding: 0.35rem 0.9rem;
    }

    .data-table-card {
        border-radius: 5px;
        border: 1px solid #f2f4f7;
        background: #fff;
        margin-bottom: 1rem;
    }

    .custom-table th {
        padding: 0.5rem 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #000;
        background: #f8f8fb;
        font-size: 9px;
        font-family: Montserrat;
    }

    .custom-table td {
        padding: 0.4rem 0.75rem;
        color: #000;
        font-size: 9px;
        font-family: Montserrat;
    }

    .loading-state {
        text-align: center;
        padding: 1rem;
        color: #667eea;
    }

    .pagination .page-link {
        color: #434afa;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        font-size: 10px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    <div class="summary-cards">
        <div class="summary-card card-1">
            <div class="summary-card-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Today's Follow Ups">
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Today's Follow Ups</div>
                <div class="summary-card-value" id="todayFollowups">0</div>
            </div>
        </div>
        <div class="summary-card card-2">
            <div class="summary-card-icon icon-amber">
                <img src="<?php echo e(asset('img/icons/underprocess.png')); ?>" alt="Under Process">
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Under Process</div>
                <div class="summary-card-value" id="underProcess">0</div>
            </div>
        </div>
        <div class="summary-card card-3">
            <div class="summary-card-icon icon-emerald">
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Today Completed">
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Today Completed</div>
                <div class="summary-card-value" id="todayCompleted">0</div>
            </div>
        </div>
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-rose">
                <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Today Pending">
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Today Pending</div>
                <div class="summary-card-value" id="todayPending">0</div>
            </div>
        </div>
        <div class="summary-card card-5">
            <div class="summary-card-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/new.png')); ?>" alt="Today New">
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Today New</div>
                <div class="summary-card-value" id="todayNew">0</div>
            </div>
        </div>
    </div>

    <div class="filterBox mb-2">
        <div>
            <label for="sales_status" class="form-label-modern"><i class="bi bi-tag"></i> Status</label>
            <select class="form-control form-control-modern" id="sales_status" name="sales_status">
                <option value="">Select Status</option>
            </select>
        </div>
        <div>
            <label for="state" class="form-label-modern"><i class="bi bi-geo-alt"></i> State</label>
            <select class="form-control form-control-modern" id="state" name="state">
                <option value="">Select State</option>
            </select>
        </div>
        <div>
            <label for="city" class="form-label-modern"><i class="bi bi-building"></i> City</label>
            <select class="form-control form-control-modern" id="city" name="city">
                <option value="">Select City</option>
            </select>
        </div>
        <div>
            <label for="business_type" class="form-label-modern"><i class="bi bi-briefcase"></i> Business Type</label>
            <select class="form-control form-control-modern" id="business_type" name="business_type">
                <option value="">Select Business</option>
            </select>
        </div>
        <div>
            <label for="lead_source" class="form-label-modern"><i class="bi bi-funnel"></i> Lead Source</label>
            <select class="form-control form-control-modern" id="lead_source" name="lead_source">
                <option value="">Select Source</option>
            </select>
        </div>
        <div>
            <label for="product_type" class="form-label-modern"><i class="bi bi-box-seam"></i> Product</label>
            <select class="form-control form-control-modern" id="product_type" name="product_type">
                <option value="">Select Product</option>
            </select>
        </div>
        <div>
            <label for="from_date" class="form-label-modern"><i class="bi bi-calendar-event"></i> From Date</label>
            <input type="date" class="form-control form-control-modern" id="from_date" name="from_date" />
        </div>
        <div>
            <label for="to_date" class="form-label-modern"><i class="bi bi-calendar-check"></i> To Date</label>
            <input type="date" class="form-control form-control-modern" id="to_date" name="to_date" />
        </div>
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="search" placeholder="Search assigned generated leads..." />
        </div>
    </div>

    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table" id="sales_table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Owner</th>
                            <th>Prospect</th>
                            <th>Lead</th>
                            <th>Contact Person</th>
                            <th>Contact No.</th>
                            <th>Next Follow</th>
                            <th>Remark</th>
                            <th>Address</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Email</th>
                            <th>Business</th>
                            <th>Source</th>
                            <th>Product</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="16" class="loading-state">
                                <i class="bi bi-arrow-repeat spin"></i>
                                <p class="mt-2 mb-0">Loading assigned leads...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-range-meta" id="assignedRangeInfo">
        Showing 0-0 from 0 data
    </div>
</div>

<div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
</div>

<?php echo $__env->make('partials.remarks-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;

function formatDateOnly(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const d = new Date(str);
    if (!isNaN(d.getTime())) return d.toISOString().split('T')[0];
    return str.slice(0, 10);
}

function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}"><i class="bi bi-chevron-left"></i> Previous</a>
        </li>
        <li class="page-item active"><span class="page-link">${current} / ${last}</span></li>
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">Next <i class="bi bi-chevron-right"></i></a>
        </li>
    `);
}

function loadSummaryStats() {
    $.get('<?php echo e(route("leadgen.my.summary-stats")); ?>', function(data) {
        $('#todayFollowups').text(data.today_followups);
        $('#underProcess').text(data.under_process);
        $('#todayCompleted').text(data.today_completed);
        $('#todayPending').text(data.today_pending);
        $('#todayNew').text(data.today_new);
    });
}

function loadAssignedGenLeads(page = 1) {
    const filters = {
        _token: '<?php echo e(csrf_token()); ?>',
        status_id: $('#sales_status').val(),
        state_id: $('#state').val(),
        city_id: $('#city').val(),
        business_type_id: $('#business_type').val(),
        lead_source_id: $('#lead_source').val(),
        products_id: $('#product_type').val(),
        search: $('#search').val(),
        date_from: $('#from_date').val(),
        date_to: $('#to_date').val(),
        per_page: 10
    };

    $.post('<?php echo e(route("leadgen.assigned.data")); ?>?page=' + page, filters, function(data) {
        let html = '';
        if (data.data.length === 0) {
            html = '<tr><td colspan="16" class="text-center">No assigned leads found.</td></tr>';
        } else {
            data.data.forEach(record => {
                let remark = '-';
                if (record.latest_remark) {
                    remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})">${record.latest_remark.remark.substring(0, 15)}...</a>`;
                }

                html += `
                    <tr>
                        <td><span class="status-badge">${record.status?.status_name ?? 'N/A'}</span></td>
                        <td>${record.user?.name ?? 'N/A'}</td>
                        <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                        <td>${record.leads_name ?? ''}</td>
                        <td>${record.contact_person ?? ''}</td>
                        <td>${record.contact_number ?? ''}</td>
                        <td>${formatDateOnly(record.next_follow_up_date)}</td>
                        <td>${remark}</td>
                        <td>${record.address ?? ''}</td>
                        <td>${record.state?.state_name ?? ''}</td>
                        <td>${record.city?.city_name ?? ''}</td>
                        <td>${record.email ?? ''}</td>
                        <td>${record.business_type?.business_name ?? ''}</td>
                        <td>${record.lead_source?.source_name ?? ''}</td>
                        <td>${record.product?.product_name ?? ''}</td>
                        <td>${record.ticket_value ?? 0}</td>
                    </tr>
                `;
            });
        }
        $('#sales_table tbody').html(html);
        buildSimplePagination($('#paginationLinks'), data.current_page, data.last_page);
        $('#assignedRangeInfo').text(`Showing ${data.from || 0}-${data.to || 0} from ${data.total} data`);
    });
}

$(document).ready(function() {
    loadSummaryStats();
    loadAssignedGenLeads();

    // Populate filter options
    $.get('<?php echo e(route("leadgen.my.filter-options")); ?>', function(res) {
        res.statuses.forEach(s => $('#sales_status').append(`<option value="${s.id}">${s.status_name}</option>`));
        res.states.forEach(s => $('#state').append(`<option value="${s.id}">${s.state_name}</option>`));
        res.business_types.forEach(b => $('#business_type').append(`<option value="${b.id}">${b.business_name}</option>`));
        res.lead_sources.forEach(s => $('#lead_source').append(`<option value="${s.id}">${s.source_name}</option>`));
        res.products.forEach(p => $('#product_type').append(`<option value="${p.id}">${p.product_name}</option>`));
    });

    $('#state').on('change', function() {
        $.get(`/leadgen/my/cities/${$(this).val()}`, (data) => {
            const $c = $('#city').html('<option value="">Select City</option>');
            data.forEach(city => $c.append(`<option value="${city.id}">${city.city_name}</option>`));
        });
    });

    $(document).on('change', '#sales_status, #state, #city, #business_type, #lead_source, #product_type, #from_date, #to_date', () => loadAssignedGenLeads(1));
    $('#search').on('keyup', () => loadAssignedGenLeads(1));
    $(document).on('click', '#paginationLinks .page-link', function(e) {
        e.preventDefault();
        loadAssignedGenLeads($(this).data('page'));
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/leadgen/assigned.blade.php ENDPATH**/ ?>