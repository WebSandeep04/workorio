

<?php $__env->startSection('title', 'Team Leads'); ?>
<?php $__env->startSection('page_title', 'Team Leads'); ?>

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
        <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Total Leads">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Leads</div>
        <div class="summary-card-value" id="teamLeadsTotal">0</div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-emerald">
        <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Active Members">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Active Members</div>
        <div class="summary-card-value" id="teamMembersActive">0</div>
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

        <div class="mb-2">
            <label for="state" class="form-label-modern">
                <i class="bi bi-geo-alt"></i> State
            </label>
            <select class="form-control form-control-modern" id="state" name="state">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="city" class="form-label-modern">
                <i class="bi bi-building"></i> City
            </label>
            <select class="form-control form-control-modern" id="city" name="city">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="business_type" class="form-label-modern">
                <i class="bi bi-briefcase"></i> Business Type
            </label>
            <select class="form-control form-control-modern" id="business_type" name="business_type">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="lead_source" class="form-label-modern">
                <i class="bi bi-funnel"></i> Lead Source
            </label>
            <select class="form-control form-control-modern" id="lead_source" name="lead_source">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="product_type" class="form-label-modern">
                <i class="bi bi-box-seam"></i> Product
            </label>
            <select class="form-control form-control-modern" id="product_type" name="product_type">
                <option value="">Loading...</option>
            </select>
        </div>

        <div class="mb-2">
            <label for="from_date" class="form-label-modern">
                <i class="bi bi-calendar-event"></i> From Date
            </label>
            <input type="date" class="form-control form-control-modern" id="from_date" name="from_date" />
        </div>

        <div class="mb-2">
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
    </div>

    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="sales_table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Prospect</th>
                            <th>Lead</th>
                            <th>Contact Person</th>
                            <th>Contact No.</th>
                            <th>Next Follow</th>
                            <th>Remark</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Email</th>
                            <th>Business</th>
                            <th>Source</th>
                            <th>Product</th>
                            <th>Ticket</th>
                            <th>Assign To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="15" class="loading-state">
                                <i class="bi bi-arrow-repeat"></i>
                                <p class="mt-2 mb-0">Loading team leads...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="table-range-meta" id="teamleadsRangeInfo">
        Showing 0-0 from 0 data
    </div>
</div>

<div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .container-fluid {
        padding: 0.5rem;
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
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }

    .summary-card-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex-grow: 1;
        min-width: 0;
    }

    .summary-card-label {
        font-size: 8px;
        font-weight: 700;
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
        color: white;
        border-radius: 5px;
        flex-wrap: wrap;
        box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
        margin-bottom: 0.5rem;
        border: 1px solid #434AFA;
        font-family: Montserrat, sans-serif;
    }

    .form-label-modern {
        color: white;
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 10px;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern {
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 2px;
        padding: 0.35rem 0.5rem;
        background: rgba(255, 255, 255, 0.98);
        color: #000;
        transition: all 0.3s ease;
        font-size: 10px;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern option {
        color: #000;
        background: #fff;
        font-family: Montserrat, sans-serif;
    }

    .form-control-modern:focus {
        outline: none;
        border-color: #fff;
        background: white;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
        transform: translateY(-1px);
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

    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        padding: 0;
    }

    .modern-card-body {
        padding: 0.5rem;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .custom-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 0.45rem;
        border: none;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .custom-table td {
        font-size: 0.85rem;
        padding: 0.4rem 0.45rem;
        vertical-align: middle;
        border-bottom: 1px solid #eef2ff;
        text-align: center;
    }

    .custom-table tbody tr:hover {
        background: rgba(102, 126, 234, 0.08);
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

    .assign-select {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #fff;
        width: 140px;
    }

    .assign-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.25);
    }

    .remark-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
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
        min-width: 1100px;
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
        color: #000;
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

    .data-table-card .custom-table tbody td:nth-child(1) { min-width: 120px; }
    .data-table-card .custom-table tbody td:nth-child(2) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(3) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(4) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(5) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(6) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(7) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(8) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
    .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
    .data-table-card .custom-table tbody td:nth-child(12) { min-width: 140px; }
    .data-table-card .custom-table tbody td:nth-child(13) { min-width: 120px; }
    .data-table-card .custom-table tbody td:nth-child(14) { min-width: 160px; }
    .data-table-card .custom-table tbody td:nth-child(15) { min-width: 200px; }

     @media (max-width: 767px){
    .container-fluid{
      /* margin-left: 20px; */
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Load team leads on page load
$(document).ready(function () {
    // Load team members first, then load team leads
    loadTeamMembers().then(function() {
        loadTeamLeads();
    });
});

// Load team members for reassignment dropdowns
function loadTeamMembers() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: '<?php echo e(route("teamleads.team-members")); ?>',
            type: 'GET',
            success: function (response) {
                window.teamMembers = response;
                resolve();
            },
            error: function (xhr, status, error) {
                console.error("Failed to load team members:", xhr.responseText);
                reject(error);
            }
        });
    });
}

function loadTeamLeads(page = 1) {
    // Ensure team members are loaded first
    if (!window.teamMembers) {
        loadTeamMembers().then(function() {
            loadTeamLeads(page);
        });
        return;
    }

    $.ajax({
        url: '<?php echo e(route("teamleads.filter")); ?>?page=' + page,
        type: 'POST',
        data: {
            _token: '<?php echo e(csrf_token()); ?>',
            status_id: $('#sales_status').val(),
            city_id: $('#city').val(),
            state_id: $('#state').val(),
            business_type_id: $('#business_type').val(),
            lead_source_id: $('#lead_source').val(),
            products_id: $('#product_type').val(),
            search: $('#search').val(),
            date_from: $('#from_date').val(),
            date_to: $('#to_date').val(),
            per_page: 10
        },
        success: function (response) {
            if (response.success) {
                let html = '';
                const uniqueMembers = new Set();

                response.data.forEach(function (record) {
                    // Create dropdown options for team members
                    let dropdownOptions = '<option value="">Select Member</option>';
                    if (window.teamMembers && window.teamMembers.length > 0) {
                        window.teamMembers.forEach(function (member) {
                            const selected = member.id == record.user?.id ? 'selected' : '';
                            dropdownOptions += `<option value="${member.id}" ${selected}>${member.name}</option>`;
                        });
                    }

                    if (record.user && record.user.id) {
                        uniqueMembers.add(record.user.id);
                    }

                    let remark = '-';
                    if (record.latest_remark) {
                        const fullRemark = record.latest_remark.remark || '';
                        const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                        remark = `<a class="remark-link" href="/remark?sales_record_id=${record.id}" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
                    }

                    const statusName = record.status?.status_name ?? 'N/A';
                    const statusBadge = `<span class="status-badge">${statusName}</span>`;

                    html += `
                        <tr>
                            <td>${statusBadge}</td>
                            <td>${record.prospectus?.prospectus_name ?? 'N/A'}</td>
                            <td>${record.leads_name ?? ''}</td>
                            <td>${record.contact_person ?? ''}</td>
                            <td>${record.contact_number ?? ''}</td>
                            <td>${formatDateOnly(record.next_follow_up_date)}</td>
                            <td>${remark}</td>
                            <td>${record.state?.state_name ?? 'N/A'}</td>
                            <td>${record.city?.city_name ?? 'N/A'}</td>
                            <td>${record.email ?? ''}</td>
                            <td>${record.business_type?.business_name ?? 'N/A'}</td>
                            <td>${record.lead_source?.source_name ?? 'N/A'}</td>
                            <td>${record.product?.product_name ?? 'N/A'}</td>
                            <td>${record.ticket_value ?? 'N/A'}</td>
                            <td>
                                <select class="assign-select" data-lead-id="${record.id}" onchange="reassignLead(${record.id}, this.value)">
                                    ${dropdownOptions}
                                </select>
                            </td>
                        </tr>
                    `;
                });

                $('#sales_table tbody').html(html);
                $('#teamLeadsTotal').text(response.total ?? response.data.length);
                $('#teamMembersActive').text(uniqueMembers.size);
                updatePagination(response.current_page, response.last_page, response.total);
                updateRangeInfo(response.from, response.to, response.total);
            } else {
                console.error('Error loading team leads:', response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Failed to load team leads:", xhr.responseText);
        }
    });
}

// Handle lead reassignment
function reassignLead(leadId, newUserId) {
    if (!newUserId) return;
    
    $.ajax({
        url: '<?php echo e(route("teamleads.reassign")); ?>',
        type: 'POST',
        data: {
            lead_id: leadId,
            new_user_id: newUserId,
            _token: '<?php echo e(csrf_token()); ?>'
        },
        success: function(response) {
            if (response.success) {
                // Show success message
                alert('Lead reassigned successfully!');
                // Reload the table to reflect changes
                loadTeamLeads();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error reassigning lead:', xhr.responseText);
            alert('Error reassigning lead. Please try again.');
        }
    });
}

// Load filter options - same as My Leads and Follow Up
$(document).ready(function() {
    // get business type 
    $.ajax({
        url: "<?php echo e(route('getbusiness')); ?>",
        type: "GET",
        success: function (data) {
            $('#business_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
            });
        },
        error: function () {
            $('#business_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get status
    $.ajax({
        url: "<?php echo e(route('getStatuses')); ?>",
        type: 'GET',
        success: function (data) {
            $('#sales_status').empty().append('<option value="">Select</option>');
            $.each(data, function (key, status) {
                $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
            });
        },
        error: function () {
            alert('Failed to load sales statuses.');
        }
    });

    // get state
    $.ajax({
        url: "<?php echo e(route('state')); ?>",
        type: "GET",
        dataType: "json",
        success: function (states) {
            let $stateDropdown = $('#state');
            $stateDropdown.empty();
            $stateDropdown.append('<option value="">Select</option>');
            
            $.each(states, function (id, name) {
                $stateDropdown.append(`<option value="${id}">${name}</option>`);
            });
        },
        error: function () {
            alert("Failed to load states.");
        }
    });

    // get sources
    $.ajax({
        url: "<?php echo e(route('getsource')); ?>",
        type: "GET",
        success: function (data) {
            $('#lead_source').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
            });
        },
        error: function () {
            $('#lead_source').html('<option value="">Unable to load types</option>');
        }
    });

    // get product
    $.ajax({
        url: "<?php echo e(route('getproduct')); ?>",
        type: "GET",
        success: function (data) {
            $('#product_type').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
            });
        },
        error: function () {
            $('#product_type').html('<option value="">Unable to load types</option>');
        }
    });

    // get all cities
    $.ajax({
        url: "<?php echo e(route('allcity')); ?>",
        type: "GET",
        success: function (data) {
            $('#city').empty().append('<option value="">Select</option>');
            $.each(data, function (index, type) {
                $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
            });
        },
        error: function () {
            $('#city').html('<option value="">Unable to load types</option>');
        }
    });

    // State change - load cities for selected state
    $('#state').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: `/teamleads/cities/${stateId}`,
                type: "GET",
                success: function (cities) {
                    let $cityDropdown = $('#city');
                    $cityDropdown.empty();
                    $cityDropdown.append('<option value="">Select</option>');
                    
                    $.each(cities, function (city) {
                        $cityDropdown.append(`<option value="${city.id}">${city.city_name}</option>`);
                    });
                },
                error: function () {
                    alert("Failed to load cities for selected state.");
                }
            });
        } else {
            $('#city').empty().append('<option value="">Select</option>');
        }
    });
});

// Helper functions
function formatDateOnly(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
}

function updatePagination(currentPage, lastPage, total) {
    if (lastPage > 1) {
        let paginationHtml = '';
        
        // Previous button
        if (currentPage > 1) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadTeamLeads(${currentPage - 1})">Previous</a></li>`;
        }
        
        // Page numbers
        for (let i = 1; i <= lastPage; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadTeamLeads(${i})">${i}</a></li>`;
        }
        
        // Next button
        if (currentPage < lastPage) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="loadTeamLeads(${currentPage + 1})">Next</a></li>`;
        }
        
        $('#paginationLinks').html(paginationHtml).show();
    } else {
        $('#paginationLinks').hide();
    }
}

function updateRangeInfo(from, to, total) {
    const $info = $('#teamleadsRangeInfo');
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

// Filter functionality - same as My Leads
$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
    loadTeamLeads(1);
});

// Search functionality
$('#search').on('input', function() {
    loadTeamLeads(1);
});

// Date filter functionality
$(document).on('change', '#from_date, #to_date', function () {
    loadTeamLeads(1);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/teamleads.blade.php ENDPATH**/ ?>