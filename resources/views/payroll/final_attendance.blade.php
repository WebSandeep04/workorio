@extends('layouts.app')

@section('title', 'Final Attendance Setup')
@section('page_title', 'Final Attendance Setup')

@push('styles')
<style>
  .modern-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
  .form-control-custom, .form-select-custom { padding: 0.6rem 1rem; border-radius: 6px; border: 1px solid #d1d5db; font-size: 0.9rem; }
  .btn-generate { background-color: #434afa; color: white; padding: 0.6rem 1.5rem; border-radius: 6px; border: none; font-weight: 500; transition: all 0.2s; }
  .btn-generate:hover { background-color: #3138cc; transform: translateY(-1px); }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  .spin { animation: spin 1s linear infinite; }
</style>
@endpush

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="modern-card mt-4">
        <h4 class="mb-4">Generate Final Attendance</h4>
        <p class="text-muted mb-4">Select a month and year to generate the final attendance summary. This will calculate all working days, leaves, and absents based on daily logs and store them in the database for the attendance review.</p>
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Select Month</label>
            <select id="syncMonth" class="form-select form-select-custom">
              @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Select Year</label>
            <select id="syncYear" class="form-select form-select-custom">
              @foreach($years as $yr)
                <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class="mt-4 text-end">
          <button id="btnGenerate" class="btn btn-generate">
            <i class="bi bi-gear-fill me-1"></i> Generate Data
          </button>
        </div>
      </div>
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

  $(function() {
    const syncUrl = "{{ route('payroll.attendance.sync') }}";
    const csrf = $('meta[name="csrf-token"]').attr('content');

    $('#btnGenerate').on('click', function() {
      const btn = $(this);
      const originalHtml = btn.html();
      const month = $('#syncMonth').val();
      const year = $('#syncYear').val();

      btn.html('<i class="bi bi-arrow-repeat spin me-1"></i> Generating...').prop('disabled', true);
      
      $.ajax({
        url: syncUrl,
        type: 'POST',
        data: { month: month, year: year },
        headers: { 'X-CSRF-TOKEN': csrf },
        success: function(res) {
          btn.html(originalHtml).prop('disabled', false);
          if (res.success) {
            showAlert('success', res.message);
          } else {
            showAlert('error', res.message || 'Sync failed.');
          }
        },
        error: function(xhr) {
          btn.html(originalHtml).prop('disabled', false);
          showAlert('error', xhr.responseJSON?.message || 'Failed to sync data.');
        }
      });
    });
  });
</script>
@endpush
