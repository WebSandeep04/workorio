

<?php $__env->startSection('title', 'Assigned Leads'); ?>
<?php $__env->startSection('page_title', 'Assigned Leads'); ?>
<?php $__env->startPush('styles'); ?>
<style>
.data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
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
            <label for="sales_status" class="form-label-modern">
                <i class="bi bi-tag"></i> Status
            </label>
            <select class="form-control form-control-modern" id="sales_status" name="sales_status">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="state" class="form-label-modern">
                <i class="bi bi-geo-alt"></i> State
            </label>
            <select class="form-control form-control-modern" id="state" name="state">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="city" class="form-label-modern">
                <i class="bi bi-building"></i> City
            </label>
            <select class="form-control form-control-modern" id="city" name="city">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="business_type" class="form-label-modern">
                <i class="bi bi-briefcase"></i> Business Type
            </label>
            <select class="form-control form-control-modern" id="business_type" name="business_type">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="lead_source" class="form-label-modern">
                <i class="bi bi-funnel"></i> Lead Source
            </label>
            <select class="form-control form-control-modern" id="lead_source" name="lead_source">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="product_type" class="form-label-modern">
                <i class="bi bi-box-seam"></i> Product
            </label>
            <select class="form-control form-control-modern" id="product_type" name="product_type">
                <option value="">Loading...</option>
            </select>
        </div>

        <div>
            <label for="from_date" class="form-label-modern">
                <i class="bi bi-calendar-event"></i> From Date
            </label>
            <input type="date" class="form-control form-control-modern" id="from_date" name="from_date" />
        </div>

        <div>
            <label for="to_date" class="form-label-modern">
                <i class="bi bi-calendar-check"></i> To Date
            </label>
            <input type="date" class="form-control form-control-modern" id="to_date" name="to_date" />
        </div>
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="search" placeholder="Search leads, contacts, emails..." />
        </div>
        <!-- <a href="<?php echo e(route('lead')); ?>" class="table-search-btn" id="addBtn">
            <i class="bi bi-plus me-1"></i>Add
        </a> -->
    </div>

    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <div class="table-responsive">
                    <table class="table custom-table" id="sales_table">
                        <thead>
                            <tr>
                                <th style="min-width: 80px;">Status</th>
                                <th style="min-width: 100px;">Owner</th>
                                <th style="min-width: 120px;">Prospect</th>
                                <th style="min-width: 150px;">Lead</th>
                                <th style="min-width: 130px;">Contact Person</th>
                                <th style="min-width: 110px;">Contact No.</th>
                                <th style="min-width: 110px;">Next Follow</th>
                                <th style="min-width: 160px;">Remark</th>
                                <th style="min-width: 150px;">Address</th>
                                <th style="min-width: 110px;">State</th>
                                <th style="min-width: 110px;">City</th>
                                <th style="min-width: 160px;">Email</th>
                                <th style="min-width: 110px;">Business</th>
                                <th style="min-width: 110px;">Source</th>
                                <th style="min-width: 110px;">Product</th>
                                <th style="min-width: 90px;">Ticket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="15" class="loading-state">
                                    <i class="bi bi-arrow-repeat"></i>
                                    <p class="mt-2 mb-0">Loading assigned leads...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="table-range-meta" id="assignedRangeInfo">
        Showing 0-0 from 0 data
    </div>
</div>

<?php echo $__env->make('partials.remarks-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
</div>
<?php $__env->stopSection(); ?>

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

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0px 8px 8px 0px #0000000A;
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
        flex-wrap: wrap;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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
        text-shadow: none;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern {
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 2px;
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
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .table-search-btn {
        padding: 0.35rem 1rem;
        background: #434AFA;
        color: white;
        border: none;
        border-radius: 2px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        box-shadow: 0 2px 4px rgba(67, 74, 250, 0.2);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .table-search-btn:hover {
        background: #3538d4;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
        color: white;
        text-decoration: none;
    }

    .table-search-btn:active {
        transform: translateY(0);
        background: #2d30b8;
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

    .data-table-card {
        border-radius: 5px;
        border: 1px solid #f2f4f7;
        background: #fff;
        box-shadow: #0000000;
        margin-bottom: 1rem;
    }

    .data-table-card .modern-card-body {
        padding: 0.5rem;
    }

    .table-scroll {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 0;
        padding-bottom: 8px;
    }

    .table-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .table-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 999px;
    }

    .table-scroll::-webkit-scrollbar-thumb {
        background: #434AFA;
        border-radius: 999px;
    }

    .table-scroll::-webkit-scrollbar-thumb:hover {
        background: #3538d4;
    }

    .data-table-card .table-responsive::-webkit-scrollbar-thumb {
        background: #434AFA;
    }

    .data-table-card .table-responsive {
        scrollbar-color: #434AFA #e4e7ec;
    }

    .data-table-card .table-responsive {
        background: transparent;
        box-shadow: none;
    }

    .data-table-card .custom-table {
        width: 100%;
        table-layout: auto;
        white-space: nowrap;
        font-size: 9px;
        border-collapse: separate;
        border-spacing: 0;
        background: #fff;
    }

    .data-table-card .custom-table th,
    .data-table-card .custom-table td {
        padding: 0;
        font-size: 9px;
        text-align: left;
        vertical-align: middle;
        border-bottom: 1px solid #eef0f6;
    }

    .data-table-card .custom-table th {
        padding: 0.5rem 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #000;
        background: #f8f8fb;
        position: sticky;
        top: 0;
        z-index: 2;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        font-family: Montserrat;
    }

    .data-table-card .custom-table tbody td {
        padding: 0.4rem 0.75rem;
        color: #000;
        font-family: Montserrat;
    }

    .data-table-card .custom-table tbody tr:hover {
        background: #f2f4f7;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    .data-table-card .custom-table tbody tr:nth-child(even) {
        background-color: #fcfcfd;
    }

    .modern-card {
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
    }

    .modern-card-body {
        padding: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        background: linear-gradient(135deg, #52c234 0%, #061700 100%);
        color: white;
        text-transform: uppercase;
    }

    .remark-link {
        color: #434AFA;
        text-decoration: none;
        font-weight: 500;
    }

    .remark-link:hover {
        text-decoration: underline;
    }

    .loading-state {
        text-align: center;
        padding: 1rem;
        color: #667eea;
        font-size: 0.85rem;
    }

    .loading-state i {
        animation: spin 1s linear infinite;
        display: inline-block;
        font-size: 1rem;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .pagination .page-link {
        color: #667eea;
        border: 2px solid #e4e8ff;
        border-radius: 10px;
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        margin: 0 0.2rem;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }

    @media (max-width: 767px){
        .container-fluid{
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            margin-left: 0;
        }

        .summary-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-search {
            flex-direction: row;
            gap: 0.5rem;
        }
        
        .table-search-btn {
            width: auto;
            padding: 0.35rem 0.75rem;
        }

        .table-search-field {
            width: 100%;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>

<script>

let currentPage = 1;

// Show only date part like YYYY-MM-DD for any date-like input
function formatDateOnly(value) {
    if (!value) return 'N/A';
    const str = String(value);
    const t = str.indexOf('T');
    if (t > 0) return str.slice(0, t);
    // Fallback: try to parse and format
    const d = new Date(str);
    if (!isNaN(d.getTime())) {
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }
    return str.length >= 10 ? str.slice(0, 10) : str;
}

// Build compact pagination: "pre [current] next"
function buildSimplePagination($container, current, last) {
    $container.empty();
    // Prev
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">pre</a>
        </li>
    `);
    // Current (disabled as display only)
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current}</span>
        </li>
    `);
    // Next
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">next</a>
        </li>
    `);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#assignedRangeInfo');
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

function loadAssignedLeads(page = 1) {
    $.ajax({
        url: '<?php echo e(route("assignedleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            per_page: 7
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="15" class="text-center">No assigned leads found.</td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.user?.name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? 'N/A'}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // Build pagination
            if (data.last_page > 1) {
                buildSimplePagination($('#paginationLinks'), data.current_page, data.last_page);
                $('#paginationLinks').show();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            } else {
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            }
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

// Load assigned leads on page load
$(document).ready(function () {
    loadAssignedLeads();
    loadFilterOptions();
});

// Load filter options
function loadFilterOptions() {
    $.ajax({
        url: '<?php echo e(route("assignedleads.filter-options")); ?>',
        type: 'GET',
        success: function (response) {
            // Populate status dropdown
            let statusOptions = '<option value="">All Statuses</option>';
            response.statuses.forEach(function (status) {
                statusOptions += `<option value="${status.id}">${status.status_name}</option>`;
            });
            $('#sales_status').html(statusOptions);

            // Populate state dropdown
            let stateOptions = '<option value="">All States</option>';
            response.states.forEach(function (state) {
                stateOptions += `<option value="${state.id}">${state.state_name}</option>`;
            });
            $('#state').html(stateOptions);

            // Populate city dropdown
            let cityOptions = '<option value="">All Cities</option>';
            response.cities.forEach(function (city) {
                cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
            });
            $('#city').html(cityOptions);

            // Populate business type dropdown
            let businessOptions = '<option value="">All Business Types</option>';
            response.business_types.forEach(function (business) {
                businessOptions += `<option value="${business.id}">${business.business_name}</option>`;
            });
            $('#business_type').html(businessOptions);

            // Populate lead source dropdown
            let sourceOptions = '<option value="">All Lead Sources</option>';
            response.lead_sources.forEach(function (source) {
                sourceOptions += `<option value="${source.id}">${source.source_name}</option>`;
            });
            $('#lead_source').html(sourceOptions);

            // Populate product dropdown
            let productOptions = '<option value="">All Products</option>';
            response.products.forEach(function (product) {
                productOptions += `<option value="${product.id}">${product.product_name}</option>`;
            });
            $('#product_type').html(productOptions);
        },
        error: function () {
            alert("Failed to load filter options");
        }
    });
}

// Handle state change to load cities
$(document).on('change', '#state', function () {
    const stateId = $(this).val();
    if (stateId) {
        $.ajax({
            url: `/assignedleads/cities/${stateId}`,
            type: 'GET',
            success: function(response) {
                let cityOptions = '<option value="">Select City</option>';
                response.forEach(function(city) {
                    cityOptions += `<option value="${city.id}">${city.city_name}</option>`;
                });
                $('#city').html(cityOptions);
            },
            error: function() {
                $('#city').html('<option value="">Unable to load cities</option>');
            }
        });
    } else {
        $('#city').html('<option value="">Select City</option>');
    }
});

// filter functionality
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadFilteredAssignedLeads(page = 1) {
    $.ajax({
        url: '<?php echo e(route("assignedleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            status_id: $('#sales_status').val(),
            state_id: $('#state').val(),
            city_id: $('#city').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            per_page: 7
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="15" class="text-center">No assigned leads found.</td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.user?.name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? 'N/A'}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // Build pagination
            if (data.last_page > 1) {
                buildSimplePagination($('#paginationfilterLinks'), data.current_page, data.last_page);
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').show();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            } else {
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            }
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

// Handle filter changes
$(document).on('change', '#sales_status, #state, #city, #business_type, #lead_source, #product_type', function () {
    $('#paginationLinks').hide();
    $('#paginationfilterLinks').hide();
    $('#paginationsearchLinks').hide();
    $('#paginationdateLinks').hide();
    loadFilteredAssignedLeads(1);
});

// Handle pagination clicks for filtered results
$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadFilteredAssignedLeads(page);
    }
});

// Handle pagination clicks for main results
$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadAssignedLeads(page);
    }
});

// Search functionality
$(document).on('input', '#search', function () {
    const searchTerm = $(this).val();
    if (searchTerm.length >= 3 || searchTerm.length === 0) {
        $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        $('#paginationsearchLinks').hide();
        $('#paginationdateLinks').hide();
        loadSearchResults(searchTerm, 1);
    }
});

function loadSearchResults(searchTerm, page = 1) {
    $.ajax({
        url: '<?php echo e(route("assignedleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            search: searchTerm,
            per_page: 7
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="15" class="text-center">No assigned leads found.</td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.user?.name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? 'N/A'}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // Build pagination
            if (data.last_page > 1) {
                buildSimplePagination($('#paginationsearchLinks'), data.current_page, data.last_page);
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').show();
                $('#paginationdateLinks').hide();
            } else {
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            }
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

// Handle pagination clicks for search results
$(document).on('click', '#paginationsearchLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        const searchTerm = $('#search').val();
        loadSearchResults(searchTerm, page);
    }
});

// Date filter functionality
function loadDateFilteredAssignedLeads(fromDate, toDate, page = 1) {
    $.ajax({
        url: '<?php echo e(route("assignedleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            date_from: fromDate,
            date_to: toDate,
            per_page: 7
        },
        success: function (data) {
            let html = '';

            if (data.data.length === 0) {
                html = '<tr><td colspan="15" class="text-center">No assigned leads found.</td></tr>';
            } else {
                data.data.forEach(function (record) {
                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
                    }

                    html += `
                        <tr>
                            <td>${record.status?.status_name ?? 'N/A'}</td>
                            <td>${record.user?.name ?? 'N/A'}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.address ?? 'N/A'}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? 'N/A'}</td>
                        </tr>
                    `;
                });
            }

            $('#sales_table tbody').html(html);

            // Build pagination
            if (data.last_page > 1) {
                buildSimplePagination($('#paginationdateLinks'), data.current_page, data.last_page);
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').show();
            } else {
                $('#paginationLinks').hide();
                $('#paginationfilterLinks').hide();
                $('#paginationsearchLinks').hide();
                $('#paginationdateLinks').hide();
            }
            updateRangeInfo(data.from, data.to, data.total);
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
            alert("Server error occurred. Check the console.");
        }
    });
}

$(document).on('change', '#from_date, #to_date', function () {
       $('#paginationLinks').hide();
        $('#paginationfilterLinks').hide();
        $('#paginationsearchLinks').hide();
    let from_date = $('#from_date').val();
    let to_date = $('#to_date').val();
    loadDateFilteredAssignedLeads(from_date, to_date, 1); // Start from page 1
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
        loadDateFilteredAssignedLeads(from_date, to_date, page);
    }
});

// hide filters

 $(document).ready(function () {
    $('#toggleFiltersBtn').on('click', function () {
      let $filterBox = $('.filterScroll');

      if ($filterBox.is(':visible')) {
        $filterBox.slideUp('fast');
        $(this).text('Show Filters ▼');
      } else {
        $filterBox.slideDown('fast');
        $(this).text('Hide Filters ▲');
      }
    });
  });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/assignedleads.blade.php ENDPATH**/ ?>