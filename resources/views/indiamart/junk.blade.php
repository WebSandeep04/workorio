@extends('layouts.app')

@section('title', 'IndiaMART Junk Leads')
@section('page_title', 'IndiaMART Junk Leads')

  @push('styles')
<style>
.data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
    <div class="junk-hero-card shadow-sm">
        <div>
            <p class="eyebrow-text">Cleanup</p>
            <h2 class="hero-title">IndiaMART Junk Leads</h2>
            <p class="hero-subtitle">Review and restore valuable leads or remove the noise. Keep your pipeline fresh and intentional.</p>
        </div>
        <button class="btn btn-refresh" id="imj_refresh">
            <i class="bi bi-arrow-repeat me-1"></i>Refresh
        </button>
    </div>

    <div class="junk-counter-card-wrapper">
        <div class="junk-counter-card">
            <div class="junk-counter-icon icon-rose">
                <img src="{{ asset('img/icons/pending.png') }}" alt="Total Junk">
            </div>
            <div class="junk-counter-content">
                <span class="counter-label">Total Junk Leads</span>
                <span class="counter-value" id="junkCounter">0</span>
            </div>
        </div>
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="imj_search" placeholder="Search subject, contact, email, city..." />
        </div>
    </div>

    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="imj_table">
                <thead>
                    <tr>
                        <th>Query Time</th>
                        <th>Subject</th>
                        <th>Product</th>
                        <th>Junk Reason</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-arrow-repeat me-2"></i>Loading junk leads...
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="table-range-meta" id="indiamartJunkRangeInfo">
        Showing 0-0 from 0 data
    </div>
</div>

<div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="imj_pagination"></ul>
</div>
@endsection

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
  }

  .junk-hero-card {
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

  .junk-hero-card > div:first-child {
    display: none;
  }

  .btn-refresh {
    display: none;
  }

  .junk-counter-card-wrapper {
    margin-bottom: 1rem;
  }

  .junk-counter-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-height: 70px;
    transition: all 0.3s ease;
  }

  .junk-counter-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .junk-counter-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .junk-counter-icon img {
    width: 24px;
    height: 24px;
    object-fit: contain;
  }

  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }

  .junk-counter-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .counter-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .counter-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #000;
    font-family: Montserrat;
  }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    margin-bottom: 0.5rem;
  }

  .form-label-modern {
    color: white;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
  }

  .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #333;
    transition: all 0.3s ease;
    font-size: 10px;
    width: 100%;
  }

  .form-control-modern:focus {
    outline: none;
    border-color: white;
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  .form-control-modern:hover {
    border-color: rgba(255, 255, 255, 0.6);
    background: white;
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

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
  }

  .table-responsive {
    border-radius: 5px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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

  .data-table-card .custom-table tbody td:nth-child(1) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(2) { min-width: 220px; }
  .data-table-card .custom-table tbody td:nth-child(3) { min-width: 180px; }
  .data-table-card .custom-table tbody td:nth-child(4) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(5) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(6) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(7) { min-width: 160px; }
  .data-table-card .custom-table tbody td:nth-child(8) { min-width: 150px; }
  .data-table-card .custom-table tbody td:nth-child(9) { min-width: 180px; }

  .badge-reason {
    display: inline-block;
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
    color: #764ba2;
    background: rgba(118, 75, 162, 0.12);
    border: none;
    cursor: pointer;
  }

  .table-actions .btn {
    font-size: 9px;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
  }

  .btn-restore {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    color: white;
    border: none;
  }

  .btn-delete {
    border: none;
    background: #434AFA;
    color: white;
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

  .modal-confirm-modern .modal-content {
    border: none;
    border-radius: 0;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.35);
  }

  .modal-confirm-modern .warning-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(239, 68, 68, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    font-size: 1.2rem;
    margin-bottom: 0.6rem;
  }

  .modal-confirm-modern .modal-footer {
    border: none;
  }

  .btn-gradient-dark {
    background: #434AFA;
    color: white;
    border: none;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
  }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const perPage = 10;
  let currentPage = 1;
  let searchTimer = null;
  let pendingDeleteId = null;

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`
      <li class="page-item ${current === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">
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
        <a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">
          Next <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    `);
  }

  function updateRangeInfo(from, to, total) {
    const $info = $('#indiamartJunkRangeInfo');
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

  function formatDateTime(value) {
    if (!value) return 'N/A';
    const d = new Date(value);
    if (isNaN(d.getTime())) return value;
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const dd = String(d.getDate()).padStart(2, '0');
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return `${yyyy}-${mm}-${dd} ${hh}:${min}`;
  }

  function renderRows(rows) {
    if (!rows.length) {
      return `
        <tr>
          <td colspan="9" class="text-center py-4 text-muted">
            <i class="bi bi-emoji-smile-upside-down me-2"></i>No junk records found.
          </td>
        </tr>`;
    }

    return rows.map(function (r) {
      return `
        <tr>
          <td>${formatDateTime(r.query_time)}</td>
          <td>${r.subject ?? 'N/A'}</td>
          <td>${r.query_product_name ?? r.product_name ?? 'N/A'}</td>
          <td>
            <span class="imj-reason-btn" style="cursor: pointer;" data-reason="${(r.junk_reason ?? '').replace(/"/g, '&quot;')}">
              ${(r.junk_reason ?? 'Not provided').length > 20 ? `${(r.junk_reason ?? '').slice(0, 20)}...` : (r.junk_reason ?? 'Not provided')}
            </span>
          </td>
          <td>${r.sender_name ?? 'N/A'}<div class="text-muted small">${r.sender_mobile ?? 'N/A'}</div></td>
          <td>${r.sender_email ?? 'N/A'}</td>
          <td>${r.sender_company ?? 'N/A'}</td>
          <td>${r.sender_city ?? 'N/A'}</td>
          <td class="table-actions text-center">
            <button class="btn btn-gradient-dark imj-restore-btn me-1" data-lead-id="${r.id}">Restore</button>
            <button class="btn btn-delete imj-delete-btn" data-lead-id="${r.id}">Delete</button>
          </td>
        </tr>`;
    }).join('');
  }

  function loadJunk(page = 1) {
    const params = {
      per_page: perPage,
      search: $('#imj_search').val()
    };

    $.ajax({
      url: '{{ route("indiamart.junk.fetch") }}?page=' + page,
      type: 'GET',
      data: params,
      success: function (resp) {
        const rows = resp.data || [];
        $('#imj_table tbody').html(renderRows(rows));
        $('#junkCounter').text(resp.total ?? rows.length);
        buildSimplePagination($('#imj_pagination'), resp.current_page || 1, resp.last_page || 1);
            updateRangeInfo(resp.from, resp.to, resp.total);
      },
      error: function () {
        $('#imj_table tbody').html('<tr><td colspan="9" class="text-center py-4 text-danger">Unable to load junk leads.</td></tr>');
      }
    });
  }

  $(document).on('click', '#imj_pagination .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
      currentPage = page;
      loadJunk(page);
    }
  });

  $('#imj_refresh').on('click', function () {
    loadJunk(currentPage);
  });

  $('#imj_search').on('keyup', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      currentPage = 1;
      loadJunk(1);
    }, 250);
  });

  $(document).on('click', '.imj-restore-btn', function () {
    const leadId = $(this).data('lead-id');
    const $btn = $(this);
    $btn.prop('disabled', true).text('Restoring...');

    $.ajax({
      url: '{{ route("indiamart.junk.restore") }}',
      type: 'POST',
      data: { lead_id: leadId },
      success: function (resp) {
        $btn.prop('disabled', false).text('Restore');
        if (resp && resp.success) {
          loadJunk(currentPage);
        } else {
          alert(resp.message || 'Failed to restore.');
        }
      },
      error: function (xhr) {
        $btn.prop('disabled', false).text('Restore');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to restore.';
        alert(msg);
      }
    });
  });

  $(document).on('click', '.imj-delete-btn', function () {
    pendingDeleteId = $(this).data('lead-id');
    $('#imjConfirmError').addClass('d-none').text('');
    new bootstrap.Modal(document.getElementById('imjConfirmModal')).show();
  });

  $(document).on('click', '#imjConfirmDeleteBtn', function () {
    if (!pendingDeleteId) return;
    const $btn = $(this);
    $btn.prop('disabled', true).text('Deleting...');

    $.ajax({
      url: '{{ route("indiamart.junk.delete") }}',
      type: 'POST',
      data: { lead_id: pendingDeleteId },
      success: function (resp) {
        $btn.prop('disabled', false).text('Delete permanently');
        if (resp && resp.success) {
          bootstrap.Modal.getInstance(document.getElementById('imjConfirmModal')).hide();
          loadJunk(currentPage);
        } else {
          $('#imjConfirmError').removeClass('d-none').text(resp.message || 'Failed to delete.');
        }
      },
      error: function (xhr) {
        $btn.prop('disabled', false).text('Delete permanently');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to delete.';
        $('#imjConfirmError').removeClass('d-none').text(msg);
      }
    });
  });

  $(document).ready(function () {
    loadJunk(1);
  });

  $(document).on('click', '.imj-reason-btn', function () {
    const reason = $(this).data('reason') || 'Not provided';
    $('#reasonModalText').text(reason);
    new bootstrap.Modal(document.getElementById('reasonModal')).show();
  });
})();
</script>

<div class="modal fade modal-confirm-modern" id="imjConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header flex-column align-items-center text-center">
        <div class="warning-icon">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <h5 class="modal-title w-100">Delete this lead permanently?</h5>
        <p class="text-muted mb-0" style="font-size:0.9rem;">This action cannot be undone and the lead will be removed from your system forever.</p>
      </div>
      <div class="modal-body pt-0">
        <div id="imjConfirmError" class="alert alert-danger d-none"></div>
      </div>
      <div class="modal-footer" style="border: none;">
        <button type="button" class="btn btn-secondary rounded-0" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary rounded-0" id="imjConfirmDeleteBtn" style="background: #434afa; border-color: #434afa;">Delete permanently</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modern-modal" id="reasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-zoom">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <p class="mb-1 text-uppercase text-white-50" style="font-size:0.6rem; letter-spacing:0.18em;">Reason</p>
          <h5 class="modal-title">Full Junk Reason</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="reasonModalText" class="mb-0" style="font-size:0.85rem;"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endpush
