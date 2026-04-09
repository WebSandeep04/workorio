@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 calling-remarks-page">
  <div class="row g-3">

    <!-- Calling Details -->
    <div class="col-lg-3">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Calling Details
        </div>
        <div class="card-body ui-body small">
          <p><strong>Name :</strong> {{ $calling->name ?? '--' }}</p>
          <p><strong>Email :</strong> {{ $calling->email ?? '--' }}</p>
          <p><strong>Phone :</strong> {{ $calling->phone ?? '--' }}</p>
          <p><strong>State :</strong> {{ $calling->state ?? '--' }}</p>
          <p><strong>City :</strong> {{ $calling->city ?? '--' }}</p>
          <p><strong>Address :</strong> {{ $calling->address ?? '--' }}</p>
          <p><strong>Calling Type :</strong> {{ optional($calling->callingType)->name ?? 'No Type' }}</p>
          <p><strong>Next Follow-up :</strong> {{ $calling->next_follow_up_date ?? '--' }}</p>
        </div>
      </div>
    </div>

    <!-- Add Follow-up -->
    <div class="col-lg-4">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Add Follow-up
        </div>
        <div class="card-body ui-body">
          <form method="POST" action="{{ route('calling.remarks.store', ['calling' => $calling->id]) }}">
            @csrf

            <input type="hidden" name="remark_id" id="remark_id">

            <div class="mb-2">
              <label class="form-label">Date</label>
              <input type="date" name="remark_date" id="remark_date"
                class="form-control form-control-sm"
                value="{{ now()->toDateString() }}">
            </div>

            <div class="mb-2">
              <label class="form-label">Remarks</label>
              <textarea name="remark" id="remark"
                class="form-control form-control-sm"
                rows="6" required></textarea>
            </div>

            <div class="mb-2">
              <label class="form-label">Priority</label>
              <select name="calling_type_id" id="calling_type_id"
                class="form-control form-control-sm">
                <option value="">Choose opt...</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Next Followup Date</label>
              <input type="date" name="next_follow_up_date"
                id="next_follow_up_date"
                class="form-control form-control-sm"
                value="{{ $defaultNextFollowUp }}">
            </div>

            <button class="btn btn-primary btn-sm w-100" style="background: #434AFA !important;">
              Save Remark
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-lg-5">
      <div class="card ui-card h-100">
        <div class="card-header ui-header">
          Previous Remark
        </div>
        <div class="card-body ui-body remark-scroll" id="callingRemarkList">
          @forelse ($calling->remarks as $r)
            <div class="remark-item">
              <div class="remark-date">
                {{ optional($r->created_at)->format('d/m/Y') }}
              </div>
              <div class="remark-text">
                {{ $r->remark }}
              </div>
            </div>
          @empty
            <p class="text-muted small">No remarks found.</p>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('styles')
<style>
/* Page background */
.calling-remarks-page {
  background: #f2f2f2;
}

/* Card */
.ui-card {
  border-radius: 6px;
  border: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

/* Header */
.ui-header {
  background: #434AFA;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
  padding: 10px 14px;
}

.form-label{
  font-weight: 700;
}

/* Body */
.ui-body {
  background: #ffffff;
  font-size: 16px;
}

/* Remarks Scroll */
.remark-scroll {
  max-height: 520px;
  overflow-y: auto;
}

/* Remark Item */
.remark-item {
  border-bottom: 1px solid #eee;
  padding: 8px 0;
}

.remark-date {
  font-size: 15px;
  font-weight: 400;
  color: #333;
}

.remark-text {
  font-size: 16px;
  color: black;
  font-weight: 700;
}

/* Scrollbar */
.remark-scroll::-webkit-scrollbar {
  width: 6px;
}
.remark-scroll::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.form-control{
  background: #DFDFDF;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
  loadCallingTypeOptions();

  function loadCallingTypeOptions() {
    $.get('{{ route("getcallingtypes") }}', function (callingTypes) {
      let $select = $('#calling_type_id');
      $select.empty().append('<option value="">Choose opt...</option>');

      callingTypes.forEach(type => {
        $select.append(`<option value="${type.id}">${type.name}</option>`);
      });

      let defaultType = {{ $defaultCallingType ?? 'null' }};
      if (defaultType) $select.val(defaultType);
    });
  }
});

function fillRemark(id, date, nextDate, text) {
  $('#remark_id').val(id);
  $('#remark_date').val(date);
  $('#next_follow_up_date').val(nextDate);
  $('#remark').val(text);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endpush
