@extends('layouts.app')

@section('title', 'Team Leads')
@section('page_title', 'Team Leads')

@push('styles')
<style>
.data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sky">
        <img src="{{ asset('img/icons/call.png') }}" alt="Total Leads">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Leads</div>
        <div class="summary-card-value" id="teamLeadsTotal">0</div>
      </div>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-emerald">
        <img src="{{ asset('img/icons/tick.png') }}" alt="Active Members">
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
                <div class="table-responsive">
                    <table class="table custom-table" id="sales_table">
                        <thead>
                            <tr>
                                <th style="min-width: 80px;">Status</th>
                                <th style="min-width: 120px;">Prospect</th>
                                <th style="min-width: 150px;">Lead</th>
                                <th style="min-width: 130px;">Contact Person</th>
                                <th style="min-width: 110px;">Contact No.</th>
                                <th style="min-width: 110px;">Next Follow</th>
                                <th style="min-width: 160px;">Remark</th>
                                <th style="min-width: 110px;">State</th>
                                <th style="min-width: 110px;">City</th>
                                <th style="min-width: 160px;">Email</th>
                                <th style="min-width: 110px;">Business</th>
                                <th style="min-width: 110px;">Source</th>
                                <th style="min-width: 110px;">Product</th>
                                <th style="min-width: 90px;">Ticket</th>
                                <th style="min-width: 150px;">Assign To</th>
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
    </div>

    <div class="table-range-meta" id="teamleadsRangeInfo">
        Showing 0-0 from 0 data
    </div>
</div>

@include('partials.remarks-modal')

<div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
</div>
@endsection

@push('styles')
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
        font-family: 'Montserrat', sans-serif;
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
        color: #000;
        font-size: 0.85rem;
        font-weight: normal;
        font-family: Montserrat, sans-serif;
    }

    .assign-select {
        font-size: 9px;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        border: 1px solid #d1d5db;
        background: #fff;
        width: 100%;
        font-family: Montserrat;
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
@endpush

@push('scripts')
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
            url: '{{ route("teamleads.team-members") }}',
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
        url: '{{ route("teamleads.filter") }}?page=' + page,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
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
                let html = '';
                const uniqueMembers = new Set();
                const data = response.data || [];

                if (data.length === 0) {
                    html = '<tr><td colspan="15" class="text-center empty-state"><i class="bi bi-inbox"></i><p class="mt-2 mb-0">No records found.</p></td></tr>';
                } else {
                    data.forEach(function (record) {
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
                            remark = `<a href="#" class="remark-link" onclick="showRemarksModal(${record.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
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
                }

                $('#sales_table tbody').html(html);
                $('#teamLeadsTotal').text(response.total ?? 0);
                $('#teamMembersActive').text(uniqueMembers.size);
                updatePagination(response.current_page, response.last_page, response.total);
                updateRangeInfo(response.from, response.to, response.total);
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
        url: '{{ route("teamleads.reassign") }}',
        type: 'POST',
        data: {
            lead_id: leadId,
            new_user_id: newUserId,
            _token: '{{ csrf_token() }}'
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
        url: "{{ route('getbusiness') }}",
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
        url: "{{ route('getStatuses') }}",
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
        url: "{{ route('state') }}",
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
        url: "{{ route('getsource') }}",
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
        url: "{{ route('getproduct') }}",
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
        url: "{{ route('allcity') }}",
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

// Build compact pagination: "Previous [current / last] Next"
function buildSimplePagination($container, current, last) {
    $container.empty();
    // Prev
    $container.append(`
        <li class="page-item ${current === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
              <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
    `);
    // Current (disabled as display only)
    $container.append(`
        <li class="page-item active">
            <span class="page-link">${current} / ${last}</span>
        </li>
    `);
    // Next
    $container.append(`
        <li class="page-item ${current === last ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
              Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    `);
}

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
    const $pagination = $('#paginationLinks');
    buildSimplePagination($pagination, currentPage, lastPage);
    if (lastPage > 1) {
        $pagination.show();
    } else {
        $pagination.hide();
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

// Pagination click handler
$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    let page = $(this).data('page');
    if (page) {
        loadTeamLeads(page);
    }
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
@endpush
