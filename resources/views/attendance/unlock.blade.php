@extends('layouts.app')

@section('title', 'Unlock Attendance')
@section('page_title', 'Unlock Attendance')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Table Header - no uppercase, specific shadow */
  .data-table-card .custom-table thead th {
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    text-transform: none !important;
    font-size: 0.75rem !important;
    letter-spacing: normal !important;
  }

  /* Summary Cards */
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
    padding: 0.4rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 55px;
    height: 55px;
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

  .summary-card-icon i {
    font-size: 1.25rem;
  }

  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }

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
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: space-between;
  }

  .table-search-field {
    flex: 1;
    min-width: 200px;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }
  
  .date-filter-input {
      background: #f4f5f7;
      border: 1px solid #e5e7eb;
      border-radius: 2px;
      padding: 0.35rem 0.5rem;
      font-size: 0.85rem;
      color: #111827;
      font-family: Montserrat;
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

  .data-table-card .table-responsive {
    border-radius: 18px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
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
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    font-family: Montserrat;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
    text-transform: none !important;
    letter-spacing: normal !important;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
  }

  .spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">

  <div class="summary-cards">
      <div class="summary-card">
        <div class="summary-card-icon icon-blue">
          <i class="bi bi-unlock fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Unlock Logs</div>
          <div class="summary-card-value text-dark" id="stat_approved_count">0</div>
        </div>
      </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="searchInput" placeholder="Search reason or user..." />
    </div>
    
    <div class="d-flex gap-2">
      <button class="btn btn-sm fw-bold text-white" id="btnUnlockDateModal" data-bs-toggle="modal" data-bs-target="#unlockDateModal" style="background-color: #434afa;">
        <i class="bi bi-unlock me-1"></i> Unlock Entire Date
      </button>

      <button class="btn btn-outline-secondary btn-sm" onclick="resetAndRefresh()">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
  </div>

  <!-- Unlock Date Modal -->
  <div class="modal fade" id="unlockDateModal" tabindex="-1" aria-labelledby="unlockDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #434afa;">
          <h5 class="modal-title text-white" id="unlockDateModalLabel" style="font-family: Montserrat;"><i class="bi bi-unlock me-2"></i>Unlock Attendance by Date</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="unlock_date_input" class="form-label fw-bold">Select Attendance Date to Unlock</label>
            <input type="date" id="unlock_date_input" class="form-control" value="{{ \Carbon\Carbon::today()->toDateString() }}">
            <!-- <small class="text-muted">All approved attendance for this date will be set to pending (is_approved = 0).</small> -->
          </div>
          <div class="mb-3">
            <label for="unlockReason" class="form-label fw-bold">Reason for Unlocking</label>
            <textarea id="unlockReason" class="form-control" rows="3" placeholder="Enter reason for unlocking attendance..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm fw-bold text-white w-100" onclick="unlockByDate()" style="background-color: #434afa;">Confirm Unlock</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Custom Confirmation Modal -->
  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-body text-center p-4">
          <div class="mb-3">
            <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
          </div>
          <h5 class="fw-bold mb-2">Are you sure?</h5>
          <p class="text-muted small mb-4">You are about to unlock attendance for <span id="confirmDateSpan" class="fw-bold text-dark"></span>. This will allow records to be edited again.</p>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light btn-sm w-100 fw-bold underline-none" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-sm text-white w-100 fw-bold" id="btnExecuteUnlock" style="background-color: #434afa;">Yes, Unlock</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Full Reason Modal -->
  <div class="modal fade" id="reasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header border-0 pb-2" style="background-color: #434afa;">
          <h6 class="modal-title fw-bold text-white" style="font-family: Montserrat;">Full Reason</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4 text-center">
          <p id="fullReasonBody" class="text-dark mb-0" style="font-family: Montserrat; line-height: 1.6; word-break: break-all;"></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body p-0">
      <div class="table-responsive">
        <table class="table custom-table mb-0" id="attendanceTable">
          <thead>
            <tr>
              <th>Action Date</th>
              <th>Unlocked Attendance Date</th>
              <th>Reason</th>
              <th>Unlocked By</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="4" class="text-center py-4">
                <i class="bi bi-arrow-repeat spin"></i> Loading logs...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 d-flex justify-content-center">
    <div id="paginationLinks"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;

    window.fetchAttendance = function(page = 1) {
        currentPage = page;
        let searchTerm = $('#searchInput').val();
        
        $('#attendanceTable tbody').html('<tr><td colspan="4" class="text-center py-4"><i class="bi bi-arrow-repeat spin"></i> Loading...</td></tr>');

        $.ajax({
            url: "{{ route('attendance.unlock.fetch') }}",
            type: 'GET',
            data: { page, search: searchTerm },
            success: function(response) {
                let rows = '';
                let data = response.data;
                $('#stat_approved_count').text(response.total || 0);
                
                if (data.length > 0) {
                    data.forEach(function(item) {
                        let fullReason = item.reason || '';
                        let shortReason = fullReason.length > 7 ? fullReason.substring(0, 7) + '...' : fullReason;
                        let reasonHtml = fullReason.length > 7 
                            ? `<span class="text-dark cursor-pointer" onclick="showFullReason(\`${fullReason.replace(/`/g, '\\`').replace(/\n/g, ' ')}\`)">${shortReason}</span>` 
                            : fullReason;

                        rows += `
                            <tr>
                                <td>${item.date}</td>
                                <td>${item.unlock_date}</td>
                                <td>${reasonHtml}</td>
                                <td>${item.unlocked_by}</td>
                            </tr>
                        `;
                    });
                } else {
                    rows = '<tr><td colspan="4" class="text-center py-4">No unlock logs found</td></tr>';
                }
                $('#attendanceTable tbody').html(rows);
                
                // Pagination logic
                if (response.links) {
                    let linkHtml = '';
                    response.links.forEach(link => {
                        let label = String(link.label).replace('&laquo;', '«').replace('&raquo;', '»');
                        if (link.url) {
                            let activeClass = link.active ? 'active' : '';
                            linkHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchAttendance(${link.url.split('page=')[1]})">${label}</a></li>`;
                        } else {
                            linkHtml += `<li class="page-item disabled"><span class="page-link">${label}</span></li>`;
                        }
                    });
                    $('#paginationLinks').html(`<ul class="pagination pagination-sm">${linkHtml}</ul>`);
                }
            }
        });
    }

    window.showFullReason = function(reason) {
        $('#fullReasonBody').text(reason);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('reasonModal')).show();
    }

    window.unlockAttendance = function(id) {
        if (!confirm('Are you sure you want to unlock this attendance?')) return;
        $.post("/attendance/unlock/" + id, { _token: '{{ csrf_token() }}' }, function(res) {
            if (res.success) fetchAttendance(currentPage);
            else alert(res.message);
        });
    }

    window.unlockByDate = function() {
        let date = $('#unlock_date_input').val();
        let reason = $('#unlockReason').val();
        
        if (!date) {
            alert('Please select a date');
            return;
        }
        if (!reason) {
            alert('Please provide a reason for unlocking');
            return;
        }

        // Show custom confirmation modal instead of confirm()
        $('#confirmDateSpan').text(date);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('unlockDateModal')).hide();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmationModal')).show();
    }

    // Handlers for the custom confirmation modal
    $('#btnExecuteUnlock').on('click', function() {
        let date = $('#unlock_date_input').val();
        let reason = $('#unlockReason').val();
        let btn = $(this);
        let originalText = btn.html();

        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Processing...');

        $.post("{{ route('attendance.unlock-by-date') }}", { 
            _token: '{{ csrf_token() }}', 
            date: date,
            reason: reason
        }, function(res) {
            btn.prop('disabled', false).html(originalText);
            if (res.success) {
                alert(res.message);
                $('#unlockReason').val('');
                bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                fetchAttendance(1);
            } else {
                alert(res.message);
                // If failed, maybe show the first modal again?
                bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('unlockDateModal')).show();
            }
        }).fail(function(xhr) {
            btn.prop('disabled', false).html(originalText);
            alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
            bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('unlockDateModal')).show();
        });
    });

    window.bulkUnlock = function() {
        let ids = [];
        $('.row-checkbox:checked').each(function() { ids.push($(this).val()); });
        if (ids.length === 0) return;
        if (!confirm(`Unlock ${ids.length} records?`)) return;
        $.post("{{ route('attendance.unlock-bulk') }}", { _token: '{{ csrf_token() }}', ids: ids }, function(res) {
            if (res.success) fetchAttendance(currentPage);
            else alert(res.message);
        });
    }

    $('#checkAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkButton();
    });

    $(document).on('change', '.row-checkbox', function() {
        toggleBulkButton();
        $('#checkAll').prop('checked', $('.row-checkbox').length === $('.row-checkbox:checked').length);
    });

    function toggleBulkButton() {
        let count = $('.row-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) $('#btnBulkUnlock').show(); else $('#btnBulkUnlock').hide();
    }

    $('#searchInput').on('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => fetchAttendance(1), 500);
    });

    window.resetAndRefresh = function() {
        $('#searchInput').val('');
        fetchAttendance(1);
    }

    fetchAttendance();
});
</script>
@endpush
