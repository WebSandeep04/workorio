@extends('layouts.app')

@section('title', 'Posting Log')
@section('page_title', 'Posting Log')

@push('styles')
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .filter-dropdown { background: #fff; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; font-size: 0.85rem; outline: none; color: #111827; min-width: 120px; }
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .modern-card-body { padding: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-responsive { border-radius: 5px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; scrollbar-color: #434AFA #e4e7ec; }
  .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .badge-modern-danger { background: #EF4444; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; font-size: 0.75rem; }
  .badge-modern-warning { background: #F59E0B; color: white; padding: 0.35em 0.65em; border-radius: 4px; font-weight: 500; font-size: 0.75rem; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .spin { animation: spin 1s linear infinite; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div style="flex: 1;">
      <h6 class="mb-0 text-muted" style="font-size: 0.85rem;">Review attendance records pending approval or locking that are blocking the Monthly Attendance Review generation.</h6>
    </div>
    
    @php
      $months = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
      $years = range(date('Y') - 5, date('Y') + 1);
    @endphp
    <select id="filterMonth" class="filter-dropdown">
      @foreach($months as $num => $name)
        <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $name }}</option>
      @endforeach
    </select>
    
    <select id="filterYear" class="filter-dropdown">
      @foreach($years as $yr)
        <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
      @endforeach
    </select>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="logTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Employee Code</th>
              <th>Employee Name</th>
              <th>Reason (Blocking Sync)</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data will be loaded here via AJAX -->
            <tr>
              <td colspan="4" class="text-center py-4"><i class="bi bi-arrow-repeat spin d-inline-block"></i> Loading data...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  function loadLog() {
    const month = $('#filterMonth').val();
    const year = $('#filterYear').val();
    const tbody = $('#logTable tbody');
    
    tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-arrow-repeat spin d-inline-block me-2"></i> Loading data...</td></tr>');
    
    $.ajax({
      url: "{{ route('posting-log.fetch') }}",
      type: 'POST',
      data: { month: month, year: year },
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.success && res.data.length > 0) {
          let html = '';
          res.data.forEach(function(item) {
            let reasonBadge = '';
            if (item.reason.includes('Not Approved') && item.reason.includes('Not Locked')) {
                reasonBadge = '<span class="badge-modern-danger">Not Approved & Not Locked</span>';
            } else if (item.reason.includes('Not Approved')) {
                reasonBadge = '<span class="badge-modern-danger">Not Approved</span>';
            } else if (item.reason.includes('Not Locked')) {
                reasonBadge = '<span class="badge-modern-warning">Not Locked</span>';
            }

            html += `<tr>
              <td><i class="bi bi-calendar-event me-2 text-muted"></i> ${item.date}</td>
              <td class="fw-semibold">${item.employee_code}</td>
              <td>${item.employee_name}</td>
              <td>${reasonBadge}</td>
            </tr>`;
          });
          tbody.html(html);
        } else {
          tbody.html(`<tr>
            <td colspan="4" class="text-center py-5">
              <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
              <p class="mt-2 mb-0 fw-semibold">All Clear!</p>
              <p class="text-muted small">No pending approvals or unlocked records found for this month.</p>
            </td>
          </tr>`);
        }
      },
      error: function() {
        tbody.html('<tr><td colspan="4" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Failed to load data.</td></tr>');
      }
    });
  }

  $('#filterMonth, #filterYear').on('change', function() {
    loadLog();
  });

  loadLog();
});
</script>
@endpush
