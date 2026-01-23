@extends('layouts.app')

@section('title', 'All Data - Today Completed')
@section('page_title', 'All Data - Today Completed')

@section('content')
<div class="container-fluid px-2">
  <div class="sales_table"></div>

  <!-- Summary Cards -->
  <div class="summary-cards mb-3">
    <div class="summary-card card-1" style="max-width: 250px;">
      <div class="summary-card-icon icon-emerald">
        <img src="{{ asset('img/icons/checklist.png') }}" alt="Completed" onerror="this.onerror=null;this.src='{{ asset('img/icons/double-check.png') }}';">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today Completed</div>
        <div class="summary-card-value" id="totalTodayCompletedCard">0</div>
      </div>
    </div>
  </div>

  <x-filter-panel :showSearch="false" />

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="recordSearch" placeholder="Search leads, contacts, emails..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="dataTable">
          <thead>
            <tr>
              <th>Status</th>
              <th>Prospect</th>
              <th>Remark</th>
              <th>Lead</th>
              <th>Contact Person</th>
              <th>Contact No.</th>
              <th>Next Follow</th>
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
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta mt-2" id="pageSummaryBottom">
     Page 1 of 1 • Showing 0-0 of 0
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationfilterLinks"></ul>
</div>
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationsearchLinks"></ul>
</div>

@include('partials.remarks-modal')

@endsection

@push('styles')
<style>
  /* Import fonts */
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  /* Global font family */
  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Summary Card CSS */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 24px;
    height: 24px;
    object-fit: contain;
  }

  /* Emerald gradient for Completed */
  .icon-emerald { background: linear-gradient(135deg, #10b981, #059669); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  /* Filter Panel CSS */
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

  .filterBox .form-label-modern {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 6px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
    width: 100%;
    margin-top: 0;
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
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  .filterBox .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.8);
    background: #fff;
  }

  /* Table Search */
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

  /* Table CSS */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    font-family: Montserrat;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 5px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
  }
  .data-table-card .table-responsive::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
    font-family: Montserrat !important;
  }

  /* IMPORTANT: White header style with vertical borders */
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
    border-right: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }
  
  .data-table-card .custom-table thead th:last-child {
    border-right: none;
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
  
  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 100px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 120px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(10) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(11) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(12) { min-width: 130px; }
  .data-table-card .custom-table tbody td:nth-child(13) { min-width: 110px; }
  .data-table-card .custom-table tbody td:nth-child(14) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(15) { min-width: 140px; }
  .data-table-card .custom-table tbody td:nth-child(16) { min-width: 180px; }

  /* Pagination */
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
    color: #334155;
  }
  
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;

// Filter Toggle
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

function updateSummary(meta) {
  const current = meta.current_page || 1;
  const last = meta.last_page || 1;
  const total = meta.total || 0;
  const perPage = Number(meta.per_page || 20); 
  const from = (current - 1) * perPage + 1;
  let to = current * perPage;
  if (to > total) to = total;
  
  if (total === 0) {
    $('#pageSummaryBottom').text('Page 1 of 1 • Showing 0-0 of 0');
  } else {
    $('#pageSummaryBottom').text(`Page ${current} of ${last} • Showing ${from}-${to} of ${total}`);
  }
}

$(document).ready(function () {
  loadData();
  loadSummaryStats();
});

function loadSummaryStats() {
  $.ajax({
    url: '{{ route("alldata.summary-stats") }}',
    method: 'GET',
    success: function(response) {
      $('#totalTodayCompletedCard').text(response.today_completed || 0);
    },
    error: function() { console.error('Failed to load summary stats'); }
  });
}

function loadData(page = 1) {
  $.ajax({
    url: `{{ route('alldata.today-completed.data') }}?page=${page}`,
    method: 'GET',
    success: function (response) {
      const tbody = $('#dataTable tbody');
      tbody.empty();
      let data = response.data || [];
      
      if (data.length === 0) {
        tbody.append(`<tr><td colspan="15" class="text-center">No records found</td></tr>`);
        $('#paginationLinks').empty();
        updateSummary({ current_page: 1, last_page: 1, total: 0 });
        return;
      }

      data.forEach(item => {
        const rawRemark = item.latest_remark || '';
        const shortRemark = rawRemark.length > 20 ? rawRemark.substring(0, 20) + '...' : rawRemark;
        const fullRemark = rawRemark.replace(/"/g, '&quot;');


        tbody.append(`
          <tr>
            <td>${item.status_name ?? '-'}</td>
            <td>${item.prospectus_name ?? '-'}</td>
            <td>${rawRemark ? `<a href="#" class="remark-link text-decoration-underline text-primary" onclick="showRemarksModal(${item.id})" title="${fullRemark}">${shortRemark}</a>` : '-'}</td>
            <td>${item.leads_name ?? '-'}</td>
            <td>${item.contact_person ?? '-'}</td>
            <td>${item.contact_number ?? '-'}</td>
            <td>${item.next_follow_up_date ?? '-'}</td>
            <td>${item.address ?? '-'}</td>
            <td>${item.state_name ?? '-'}</td>
            <td>${item.city_name ?? '-'}</td>
            <td>${item.email ?? '-'}</td>
            <td>${item.business_name ?? '-'}</td>
            <td>${item.source_name ?? '-'}</td>
            <td>${item.product_name ?? '-'}</td>
            <td>${item.ticket_value ?? '-'}</td>
          </tr>
        `);
      });

      renderPagination(response);
      updateSummary(response);
    }
  });
}

function renderPagination(data) {
  const current = Number(data.current_page) || 1;
  const last = Number(data.last_page) || 1;
  const $container = $('#paginationLinks');
  $container.show();
  buildSimplePagination($container, current, last);
}

function buildSimplePagination($container, current, last) {
  $container.empty();
  if (last <= 1) return;

  $container.append(`
    <li class="page-item ${current === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current - 1}">
        <i class="bi bi-chevron-left"></i> Previous
      </a>
    </li>
  `);
  
  $container.append(`
    <li class="page-item active">
      <span class="page-link">${current} / ${last}</span>
    </li>
  `);

  $container.append(`
    <li class="page-item ${current === last ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current + 1}">
        Next <i class="bi bi-chevron-right"></i>
      </a>
    </li>
  `);
}

// Handle pagination clicks
$(document).on('click', '.pagination .page-link', function (e) {
  e.preventDefault();
  const page = $(this).data('page');
  const parentId = $(this).closest('ul').attr('id');
  if (page) {
    if (parentId === 'paginationfilterLinks') {
       loadFilteredFollowups(page);
    } else if (parentId === 'paginationsearchLinks') {
       loadSearchData(page);
    } else {
       loadData(page);
    }
  }
});

let currentSearch = '';
$('#recordSearch').on('keyup', function () {
  currentSearch = $(this).val();
  loadSearchData(1);
});

function loadSearchData(page = 1) {
  $.ajax({
    url: '{{ route("alldatasearch") }}?page=' + page,
    method: 'GET',
    data: { search: currentSearch },
    success: function (response) {
      let tbody = $('#dataTable tbody');
      tbody.empty();
      $('#paginationLinks').hide(); 

      let data = response.data || [];

      if (data.length === 0) {
        tbody.append('<tr><td colspan="15" class="text-center">No records found</td></tr>');
        updateSummary({ total: 0 });
        $('#paginationsearchLinks').empty();
      } else {
        data.forEach((item) => {
          const rawRemark = item.last_remark || ''; 
          const shortRemark = rawRemark.length > 20 ? rawRemark.substring(0, 20) + '...' : rawRemark;
          const fullRemark = rawRemark.replace(/"/g, '&quot;');
          
          tbody.append(`
            <tr>
              <td>${item.status_name ?? '-'}</td>
              <td>${item.prospectus_name ?? '-'}</td>
              <td>${rawRemark ? `<a href="#" class="remark-link text-decoration-underline text-primary" onclick="showRemarksModal(${item.id})" title="${fullRemark}">${shortRemark}</a>` : '-'}</td>
              <td>${item.leads_name ?? '-'}</td>
              <td>${item.contact_person ?? '-'}</td>
              <td>${item.contact_number ?? '-'}</td>
              <td>${item.next_follow_up_date ?? '-'}</td>
              <td>${item.address ?? '-'}</td>
              <td>${item.state_name ?? '-'}</td>
              <td>${item.city_name ?? '-'}</td>
              <td>${item.email ?? '-'}</td>
              <td>${item.business_name ?? '-'}</td>
              <td>${item.source_name ?? '-'}</td>
              <td>${item.product_name ?? '-'}</td>
              <td>${item.ticket_value ?? '-'}</td>
            </tr>
          `);
        });
        
        const $pContainer = $('#paginationsearchLinks');
        $pContainer.show();
        buildSimplePagination($pContainer, response.current_page || 1, response.last_page || 1);
        updateSummary(response);
      }
    },
    error: function () { console.error('Search failed.'); }
  });
}

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function loadFilteredFollowups(page = 1) {
  $.ajax({
    url: '{{ route("alldatafilter") }}?page=' + page,
    type: 'POST',
    data: {
      status: $('#sales_status').val(),
      city: $('#city').val(),
      state: $('#state').val(),
      business: $('#business_type').val(),
      source: $('#lead_source').val(),
      product: $('#product_type').val()
    },
    success: function (response) {
      let data = response.data || [];
      let tbody = $('#dataTable tbody');
      tbody.empty();
      
      if (data.length === 0) {
        tbody.append('<tr><td colspan="15" class="text-center">No records found.</td></tr>');
      } else {
        data.forEach(function (item) {
          const rawRemark = item.last_remark || item.latest_remark || '';
          const shortRemark = rawRemark.length > 20 ? rawRemark.substring(0, 20) + '...' : rawRemark;
          const fullRemark = rawRemark.replace(/"/g, '&quot;');
          tbody.append(`
            <tr>
              <td>${item.status_name ?? '-'}</td>
              <td>${item.prospectus_name ?? '-'}</td>
              <td>${rawRemark ? `<a href="#" class="remark-link text-decoration-underline text-primary" onclick="showRemarksModal(${item.id})" title="${fullRemark}">${shortRemark}</a>` : '-'}</td>
              <td>${item.leads_name ?? '-'}</td>
              <td>${item.contact_person ?? '-'}</td>
              <td>${item.contact_number ?? '-'}</td>
              <td>${item.next_follow_up_date ?? '-'}</td>
              <td>${item.address ?? '-'}</td>
              <td>${item.state_name ?? '-'}</td>
              <td>${item.city_name ?? '-'}</td>
              <td>${item.email ?? '-'}</td>
              <td>${item.business_name ?? '-'}</td>
              <td>${item.source_name ?? '-'}</td>
              <td>${item.product_name ?? '-'}</td>
              <td>${item.ticket_value ?? '-'}</td>
            </tr>
          `);
        });
      }
      
      const $pContainer = $('#paginationfilterLinks');
      $pContainer.show();
      buildSimplePagination($pContainer, response.current_page || 1, response.last_page || 1);
      updateSummary(response);
    }
  });
}

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
  $('#paginationLinks').hide();
  $('#paginationsearchLinks').hide();
  loadFilteredFollowups(1);
});

// Load filter options (standard boilerplate)
$(document).ready(function() {
  $.ajax({
    url: "{{ route('getbusiness') }}",
    type: "GET",
    success: function (data) {
      $('#business_type').empty().append('<option value="">Select</option>');
      $.each(data, function (index, type) {
        $('#business_type').append(`<option value="${type.id}">${type.business_name}</option>`);
      });
    },
    error: function () { $('#business_type').html('<option value="">Unable to load types</option>'); }
  });

  $.ajax({
    url: "{{ route('getStatuses') }}",
    type: 'GET',
    success: function (data) {
      $('#sales_status').empty().append('<option value="">Select</option>');
      $.each(data, function (key, status) {
        $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
      });
    },
    error: function () { console.error('Failed to load sales statuses.'); }
  });

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
    error: function () { console.error("Failed to load states."); }
  });

  $.ajax({
    url: "{{ route('getsource') }}",
    type: "GET",
    success: function (data) {
      $('#lead_source').empty().append('<option value="">Select</option>');
      $.each(data, function (index, type) {
        $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
      });
    },
    error: function () { $('#lead_source').html('<option value="">Unable to load types</option>'); }
  });

  $.ajax({
    url: "{{ route('getproduct') }}",
    type: "GET",
    success: function (data) {
      $('#product_type').empty().append('<option value="">Select</option>');
      $.each(data, function (index, type) {
        $('#product_type').append(`<option value="${type.id}">${type.product_name}</option>`);
      });
    },
    error: function () { $('#product_type').html('<option value="">Unable to load types</option>'); }
  });

  $.ajax({
    url: "{{ route('allcity') }}",
    type: "GET",
    success: function (data) {
      $('#city').empty().append('<option value="">Select</option>');
      $.each(data, function (index, type) {
        $('#city').append(`<option value="${type.id}">${type.city_name}</option>`);
      });
    },
    error: function () { $('#city').html('<option value="">Unable to load types</option>'); }
  });

  $('#state').on('change', function() {
    const stateId = $(this).val();
    if (stateId) {
      $.ajax({
        url: `/city/${stateId}`,
        type: 'GET',
        success: function(response) {
          let cityOptions = '<option value="">Select City</option>';
          $.each(response, function(id, name) {
            cityOptions += `<option value="${id}">${name}</option>`;
          });
          $('#city').html(cityOptions);
        },
        error: function() { $('#city').html('<option value="">Unable to load cities</option>'); }
      });
    } else {
      $('#city').html('<option value="">Select City</option>');
    }
  });
});
</script>
@endpush
