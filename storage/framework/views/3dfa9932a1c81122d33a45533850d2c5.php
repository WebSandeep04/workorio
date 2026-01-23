

<?php $__env->startSection('title', 'Sales Product'); ?>
<?php $__env->startSection('page_title', 'Sales Product'); ?>
<?php $__env->startPush('styles'); ?>
<style>

    .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="sales_table"></div>

  <div class="summary-cards mb-3">
    <div class="summary-card card-1" style="max-width: 250px;">
      <div class="summary-card-icon icon-sunrise">
        <img src="<?php echo e(asset('img/icons/call.png')); ?>" alt="Calls">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Today's Pending</div>
        <div class="summary-card-value" id="totalPendingCard">0</div>
      </div>
    </div>
  </div>

  <?php if (isset($component)) { $__componentOriginalf3f7946f558699cf27352737986448eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf3f7946f558699cf27352737986448eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.filter-panel','data' => ['showSearch' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filter-panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['showSearch' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $attributes = $__attributesOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__attributesOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf3f7946f558699cf27352737986448eb)): ?>
<?php $component = $__componentOriginalf3f7946f558699cf27352737986448eb; ?>
<?php unset($__componentOriginalf3f7946f558699cf27352737986448eb); ?>
<?php endif; ?>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="followupSearch" placeholder="Search leads, contacts, emails..." />
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="followupsTable">
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
<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationdateLinks"></ul>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards,
  .status-cards {
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
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
    height: 80px;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 6px 8px 0px #00000014;
    border-color: #dbe0e9;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 20px;
    height: 20px;
    object-fit: contain;
  }

  .summary-card.card-1 .summary-card-icon { background: linear-gradient(135deg, #FFF0F0 0%, #FFE0E0 100%); }

  .summary-card-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .summary-card-label {
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.15rem;
    font-family: Montserrat, sans-serif;
  }

  .summary-card-value {
    color: #1e293b;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1.1;
    font-family: Montserrat, sans-serif;
  }

  .metric-arrow {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.2s;
    text-decoration: none;
  }
  .metric-arrow:hover { background: #0d6efd; color: #fff; }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.6rem;
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
    border-color: rgba(255, 255, 255, 0.6);
    background: #fff;
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
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
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

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
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

  .data-table-card .table-responsive {
    scrollbar-color: #434AFA #e4e7ec;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
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

  .custom-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    text-align: center;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .custom-table td {
    font-size: 10px;
    padding: 0.25rem 0.35rem;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
  }

  .custom-table tbody tr {
    transition: all 0.3s ease;
  }

  .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
  }

  .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .assign-select {
    font-size: 9px;
    padding: 2px 4px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background: white;
    width: 100%;
    max-width: 120px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .assign-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.3);
  }

  .status-badge {
    display: inline-block;
    color: #000;
    font-size: 0.85rem;
    font-weight: normal;
    font-family: Montserrat, sans-serif;
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
</style>
<?php $__env->stopPush(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;

function loadFollowups(page = 1) {
  $.ajax({
    url: `/todaypendingfollowupstabledata?page=${page}`,
    method: 'GET',
    success: function (response) {
      const tbody = $('#followupsTable tbody');
      tbody.empty();

      if (response.data.length === 0) {
        tbody.append(`<tr><td colspan="15" class="text-center">No records found</td></tr>`);
        $('#paginationLinks').empty();
        $('#pageSummaryBottom').text('Showing 0-0 of 0');
        $('#totalPendingCard').text('0');
        return;
      }

      response.data.forEach(item => {
        const rawRemark = item.latest_remark || '';
        const displayRemark = rawRemark.length > 12 ? rawRemark.substring(0, 12) + '...' : rawRemark;
        const remark = rawRemark
          ? `<a href="/remark?sales_record_id=${item.id}" class="text-decoration-underline text-primary" title="${rawRemark}">${displayRemark}</a>`
          : '-';

        tbody.append(`
          <tr>
            <td>${item.status_name ?? '-'}</td>
            <td>${item.prospectus_name ?? '-'}</td>
            <td>${remark}</td>
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
      updateSummary({
        current_page: response.current_page || 1,
        last_page: response.last_page || 1,
        total: response.total ?? (response.data ? response.data.length : 0),
        per_page: response.per_page || (response.data ? response.data.length : 0),
        data_length: response.data ? response.data.length : 0
      });
    }
  });
}

// Update total and page summary
function updateSummary(meta) {
  const total = Number(meta.total || 0);
  const current = Number(meta.current_page || 1);
  const last = Number(meta.last_page || 1);
  const perPage = Number(meta.per_page || 0);
  const dataLen = Number(meta.data_length || 0);
  const start = total > 0 && perPage > 0 ? ((current - 1) * perPage) + 1 : (total === 0 ? 0 : 1);
  const end = total > 0 && perPage > 0 ? Math.min(start + dataLen - 1, total) : dataLen;
  
  $('#totalPendingCard').text(total);
  $('#pageSummaryBottom').text(`Page ${current} of ${last} • Showing ${start}-${end} of ${total}`);
}

function renderPagination(data) {
  const current = Number(data.current_page) || 1;
  const last = Number(data.last_page) || 1;
  buildSimplePagination($('#paginationLinks'), current, last);
}

// Compact pagination matching All Data style
function buildSimplePagination($container, current, last) {
  $container.empty();
  current = Number(current) || 1;
  last = Number(last) || 1;

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
  if (page) {
    currentPage = page;
    loadFollowups(page);
  }
});

// Initial load
$(document).ready(function () {
  loadFollowups();
  loadTotalPending();
});

function loadTotalPending() {
  $.ajax({
    url: '/todaypending',
    method: 'GET',
    success: function(response) {
      const count = response.todaypending || 0;
      $('#totalPendingCard').text(count);
    },
    error: function() {
      // Fallback or leave as 0
    }
  });
}

$('#followupSearch').on('keyup', function () {
  let search = $(this).val();

  $.ajax({
    url: '/searchpendingFollowups',
    method: 'GET',
    data: { search: search },
    success: function (data) {
      let tbody = $('#followupsTable tbody');
      tbody.empty();

      if (data.length === 0) {
        tbody.append('<tr><td colspan="15" class="text-center">No records found</td></tr>');
      } else {
        data.forEach((item) => {
          const rawRemark = item.latest_remark || '';
          const displayRemark = rawRemark.length > 12 ? rawRemark.substring(0, 12) + '...' : rawRemark;
          tbody.append(`
            <tr>
              <td>${item.status_name ?? '-'}</td>
              <td>${item.prospectus_name ?? '-'}</td>
              <td>${displayRemark ?? '-'}</td>
              <td>${item.leads_name ?? '-'}</td>
              <td>${item.contact_person ?? '-'}</td>
              <td>${item.contact_number ?? '-'}</td>
              <td>${item.next_follow_up_date ?? '-'}</td>
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
    },
    error: function () {
      alert('Search failed.');
    }
  });
});

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function loadFilteredFollowups(page = 1) {
  $.ajax({
    url: '<?php echo e(route("filter")); ?>?page=' + page,
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
      let tbody = $('#followupsTable tbody');
      tbody.empty();
      if (data.length === 0) {
        tbody.append('<tr><td colspan="15" class="text-center">No records found.</td></tr>');
      } else {
        data.forEach(function (item) {
          const rawRemark = item.last_remark || item.latest_remark || '';
          const displayRemark = rawRemark.length > 12 ? rawRemark.substring(0, 12) + '...' : rawRemark;
          const remark = (rawRemark && rawRemark.length > 12) ? rawRemark.substring(0, 12) + '...' : (rawRemark || '-');
          tbody.append(`
            <tr>
              <td>${item.status_name ?? '-'}</td>
              <td>${item.prospectus_name ?? '-'}</td>
              <td>${remark}</td>
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
      buildSimplePagination($('#paginationfilterLinks'), response.current_page || 1, response.last_page || 1);
      updateSummary({
        current_page: response.current_page || 1,
        last_page: response.last_page || 1,
        total: response.total ?? data.length,
        per_page: response.per_page || data.length,
        data_length: data.length
      });
    }
  });
}

$(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', function () {
  $('#paginationLinks').hide();
  loadFilteredFollowups(1);
});

$(document).on('click', '#paginationfilterLinks .page-link', function (e) {
  e.preventDefault();
  const page = $(this).data('page');
  if (page) {
    $('#paginationLinks').hide();
    loadFilteredFollowups(page);
  }
});

// hide filters (match myleads behavior)
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

// Load filter options (same as myleads)
$(document).ready(function() {
  $.ajax({
    url: "<?php echo e(route('getbusiness')); ?>",
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
    url: "<?php echo e(route('getStatuses')); ?>",
    type: 'GET',
    success: function (data) {
      $('#sales_status').empty().append('<option value="">Select</option>');
      $.each(data, function (key, status) {
        $('#sales_status').append(`<option value="${status.id}">${status.status_name}</option>`);
      });
    },
    error: function () { alert('Failed to load sales statuses.'); }
  });

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
    error: function () { alert("Failed to load states."); }
  });

  $.ajax({
    url: "<?php echo e(route('getsource')); ?>",
    type: "GET",
    success: function (data) {
      $('#lead_source').empty().append('<option value="">Select</option>');
      $.each(data, function (index, type) {
        $('#lead_source').append(`<option value="${type.id}">${type.source_name}</option>`);
      });
    },
    error: function () { $('#lead_source').html('<option value=\"\">Unable to load types</option>'); }
  });

  $.ajax({
    url: "<?php echo e(route('getproduct')); ?>",
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
    url: "<?php echo e(route('allcity')); ?>",
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
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/todaypendingtable.blade.php ENDPATH**/ ?>