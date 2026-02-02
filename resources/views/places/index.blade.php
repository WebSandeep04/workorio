@extends('layouts.app')

@section('title', 'Places')
@section('page_title', 'Places')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Table Search & Buttons */
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

  /* Modern Card & Table */
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

  /* Range Info */
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }

  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem 0.5rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .btn-action-edit {
    color: white;
    background: #343AFA !important;
    border-radius: 4px;
  }

  .btn-action-delete {
   color: white;
   background: #343AFA !important;
    border-radius: 4px;
  }

  .btn-action i {
    font-size: 0.8rem;
  }
  
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
  }

  .loading-state, .empty-state {
    text-align: center;
    padding: 1rem;
    color: #667eea;
    font-size: 10px;
  }
  
  .empty-state {
    color: #6c757d;
  }

  .loading-state i, .empty-state i {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
  }

   @media (max-width: 767px){
    .container-fluid{
      padding-left: 0.5rem;
      padding-right: 0.5rem;
      margin-right: 0;
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
  
  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 1rem 1.5rem;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 1rem 1.5rem;
      background: #fff;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
  }
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
  }
  
  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  
  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
  }
  
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
    color: white;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search places..." />
    </div>
    <button class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createPlaceModal">
      <i class="bi bi-plus me-1"></i>Create Place
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        {{-- Use the new ID for the place table --}}
        <table class="table custom-table" id="placesTable">
          <thead>
            <tr>
              <th>Place Name</th>
              <th>Latitude</th>
              <th>Longitude</th>
              <th>Radius (m)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="5" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p class="mt-2 mb-0">Loading places...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="placeRangeInfo">
    Showing 0-0 from 0 data
  </div>
  
  <div class="mt-2 d-flex justify-content-center">
    <ul class="pagination" id="paginationLinks"></ul>
  </div>
</div>

<!-- Create Place Modal -->
<div class="modal fade modal-modern" id="createPlaceModal" tabindex="-1" aria-labelledby="createPlaceModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="createPlaceModalLabel">
          <i class="bi bi-plus text-white"></i>
          Create Place
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="createPlaceForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <div class="mb-2">
            <label for="placename" class="form-label-modern">Place Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="placename" name="placename" required placeholder="Enter place name">
          </div>
          <div class="row">
            <div class="col-md-6 mb-2">
              <label for="latitude" class="form-label-modern">Latitude</label>
              <input type="number" step="any" class="form-control form-control-modern" id="latitude" name="latitude" placeholder="e.g. 28.7041">
            </div>
            <div class="col-md-6 mb-2">
              <label for="longitude" class="form-label-modern">Longitude</label>
              <input type="number" step="any" class="form-control form-control-modern" id="longitude" name="longitude" placeholder="e.g. 77.1025">
            </div>
          </div>
          <div class="mb-2">
            <label for="radius" class="form-label-modern">Radius (meters) <span class="text-danger">*</span></label>
            <input type="number" min="0" class="form-control form-control-modern" id="radius" name="radius" required placeholder="e.g. 50">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Place Modal -->
<div class="modal fade modal-modern" id="editPlaceModal" tabindex="-1" aria-labelledby="editPlaceModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style ="font-size: 1.1rem; font-weight: 600;" id="editPlaceModalLabel">
          <i class="bi bi-pencil-square text-white"></i>
          Edit Place
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPlaceForm">
        <div class="modal-body pt-4 pb-4">
          @csrf
          <input type="hidden" id="edit_place_id">
          <div class="mb-2">
            <label for="edit_placename" class="form-label-modern">Place Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-modern" id="edit_placename" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-2">
              <label for="edit_latitude" class="form-label-modern">Latitude</label>
              <input type="number" step="any" class="form-control form-control-modern" id="edit_latitude">
            </div>
            <div class="col-md-6 mb-2">
              <label for="edit_longitude" class="form-label-modern">Longitude</label>
              <input type="number" step="any" class="form-control form-control-modern" id="edit_longitude">
            </div>
          </div>
          <div class="mb-2">
            <label for="edit_radius" class="form-label-modern">Radius (meters) <span class="text-danger">*</span></label>
            <input type="number" min="0" class="form-control form-control-modern" id="edit_radius" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn-modern btn-modern-primary w-100 justify-content-center" style="background: #434AFA; color: white;">
            <i class="bi bi-check-circle"></i>
            Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function showAlert(type, message) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const alertHtml = `
    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  $('body').append(alertHtml);
  setTimeout(() => $('.alert').fadeOut(), 3000);
}

// Build compact pagination
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
    // Current
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

function updateRangeInfo(from, to, total) {
    const $info = $('#placeRangeInfo');
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

$(function () {
  let searchTimeout;
  loadPlaces();

  function loadPlaces(page = 1) {
    let search = $('#search').val();

    $('#placesTable tbody').html(`
      <tr>
        <td colspan="5" class="loading-state">
          <i class="bi bi-arrow-repeat spin"></i>
          <p class="mt-2 mb-0">Loading places...</p>
        </td>
      </tr>
    `);
    
    // Note: ensure the controller's list method supports pagination if you want true pagination.
    // If list() returns all items, we can mock client-side pagination or just show all.
    // Assuming we want a simple fetch for now based on the previous controller code which returned all results.
    // If you need server side pagination, the controller needs to use paginate().
    // For now, I will use the list endpoint which returns all data, but if you want pagination consistent with source,
    // we should update controller to support it. 
    // BUT the user asked for UI similarity primarily.
    // Let's stick to the route 'places.list' which I defined earlier as returning all items in JSON.
    // If we want pagination, we can adapt on client side or update controller later.
    // For exact visual match, I will assume the server returns a list array for now, 
    // OR we can make it return a paginated structure if needed.
    // The previous controller code I wrote uses `DB::table('places')->get()` (returns array).
    // The `source.fetch` returns a paginated object.
    
    // To properly mimic the UI which has pagination controls, I'll assume for now we might receive a flat list
    // and I can just show them all, OR I can simulate pagination. 
    // Let's just fetch the list.
    
    $.get(`{{ route('places.list') }}`, function (data) {
        // data here is an array of objects based on my previous controller code.
        // We will just render all of them and hide pagination for now or simulate it.
        // To be safe and quick: just render all.
        
      if (!data || data.length === 0) {
        $('#placesTable tbody').html(`
          <tr>
            <td colspan="5" class="empty-state">
              <i class="bi bi-inbox"></i>
              <h5>No Places Found</h5>
              <p>Get started by creating your first place.</p>
            </td>
          </tr>
        `);
        $('#paginationLinks').empty();
        updateRangeInfo(0, 0, 0);
        return;
      }
      
      let rows = '';
      $.each(data, function (i, p) {
        rows += `
          <tr style="animation-delay: ${i * 0.1}s;">
            <td><strong>${p.placename}</strong></td>
            <td>${p.latitude || '-'}</td>
            <td>${p.longitude || '-'}</td>
            <td>${p.radius}</td>
            <td>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn-action btn-action-edit editBtn" 
                    data-id="${p.id}" 
                    data-placename="${p.placename}"
                    data-latitude="${p.latitude}"
                    data-longitude="${p.longitude}"
                    data-radius="${p.radius}"
                    title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-action btn-action-delete deleteBtn" data-id="${p.id}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      $('#placesTable tbody').html(rows);

      // Since the current controller returns all data (no pagination), we can just show "1 / 1" or hide pagination.
      // Or we can manually paginate the array if we want.
      // For simplicity, let's just show range info as "All".
      updateRangeInfo(1, data.length, data.length);
      buildSimplePagination($('#paginationLinks'), 1, 1);
      
    }).fail(function () {
      $('#placesTable tbody').html(`
        <tr>
          <td colspan="5" class="text-danger text-center py-4">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mt-2 mb-0">Failed to load places. Please try again.</p>
          </td>
        </tr>
      `);
    });
  }

  // Pagination click (mocked since we load all)
  $(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
  });
  
  // Search input (client-side filter for now since controller returns all)
  $('#search').on('keyup', function() {
      let value = $(this).val().toLowerCase();
      $("#placesTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
  });

  // Close modals when clicking outside
  $(document).on('click', function (e) {
      if ($(e.target).hasClass('modal')) {
          $('.modal').modal('hide');
      }
  });

  // Create
  $('#createPlaceForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Submit');
    
    $.post("{{ route('places.store') }}", {
      placename: $('#placename').val(),
      latitude: $('#latitude').val(),
      longitude: $('#longitude').val(),
      radius: $('#radius').val(),
      _token: '{{ csrf_token() }}'
    }, function () {
      $('#createPlaceModal').modal('hide');
      $('#createPlaceForm')[0].reset();
      loadPlaces();
      showAlert('success', 'Place created successfully.');
    }).fail(function (xhr) {
      showAlert('error', 'Failed to create place.');
    }).always(function() {
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit');
    });
  });

  // Edit open
  $(document).on('click', '.editBtn', function () {
    $('#edit_place_id').val($(this).data('id'));
    $('#edit_placename').val($(this).data('placename'));
    $('#edit_latitude').val($(this).data('latitude'));
    $('#edit_longitude').val($(this).data('longitude'));
    $('#edit_radius').val($(this).data('radius'));
    $('#editPlaceModal').modal('show');
  });

  // Edit submit
  $('#editPlaceForm').submit(function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Updating...');
    
    let id = $('#edit_place_id').val();
    $.ajax({
      url: `{{ url('/places') }}/${id}`,
      type: 'PUT',
      data: {
        placename: $('#edit_placename').val(),
        latitude: $('#edit_latitude').val(),
        longitude: $('#edit_longitude').val(),
        radius: $('#edit_radius').val(),
        _token: '{{ csrf_token() }}'
      },
      success: function () {
        $('#editPlaceModal').modal('hide');
        loadPlaces();
        showAlert('success', 'Place updated successfully.');
      },
      error: function() {
        showAlert('error', 'Failed to update place.');
      }
    }).always(function() {
      $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update');
    });
  });

  // Delete
  $(document).on('click', '.deleteBtn', function () {
    if (confirm('Are you sure you want to delete this place?')) {
      $.ajax({
        url: `/places/${$(this).data('id')}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function () {
          loadPlaces();
          showAlert('success', 'Place deleted successfully.');
        },
        error: function() {
          showAlert('error', 'Failed to delete place.');
        }
      });
    }
  });
});
</script>
<style>
  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush
