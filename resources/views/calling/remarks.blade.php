@extends('layouts.app')

@section('content')
<div class="container mt-3 calling-remarks">
  <div class="row g-3 align-items-stretch">
    <!-- Calling Details -->
    <div class="col-md-3 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Calling Details</strong>
        </div>
        <div class="card-body p-3 small text-light" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <p><strong>Name:</strong> {{ $calling->name ?? '--' }}</p>
          <p><strong>Email:</strong> {{ $calling->email ?? '--' }}</p>
          <p><strong>Phone:</strong> {{ $calling->phone ?? '--' }}</p>
          <p><strong>State:</strong> {{ optional($calling->state)->state_name ?? '--' }}</p>
          <p><strong>City:</strong> {{ optional($calling->city)->city_name ?? '--' }}</p>
          <p><strong>Address:</strong> {{ $calling->address ?? '--' }}</p>
          <p><strong>Calling Type:</strong> {{ optional($calling->callingType)->name ?? 'No Type' }}</p>
          <p><strong>Next Follow-up:</strong> {{ $calling->next_follow_up_date ?? '--' }}</p>
        </div>
      </div>
    </div>

    <!-- Remark Form -->
    <div class="col-md-4 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Add Follow-up</strong>
        </div>
        <div class="card-body p-3 d-flex flex-column text-white" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <form class="flex-grow-1 d-flex flex-column" method="POST" action="{{ route('calling.remarks.store', ['calling' => $calling->id]) }}">
            @csrf
            <input type="hidden" name="remark_id" id="remark_id" value="">
            <div class="mb-2">
              <label class="form-label">Date</label>
              <input type="date" name="remark_date" id="remark_date" class="form-control form-control-sm" value="{{ now()->toDateString() }}">
            </div>
            <div class="mb-2">
              <label class="form-label">Remark</label>
              <textarea name="remark" id="remark" class="form-control form-control-sm" rows="6" style="min-height: 180px;" required></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label">Calling Type</label>
              <select name="calling_type_id" id="calling_type_id" class="form-control form-control-sm">
                <option value="">Select Calling Type</option>
                <!-- Options will be populated by JavaScript -->
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Next Follow-Up Date</label>
              <input type="date" name="next_follow_up_date" id="next_follow_up_date" class="form-control form-control-sm" value="{{ $defaultNextFollowUp }}">
            </div>
            <button type="submit" class="btn btn-warning btn-sm w-100 mt-auto">Save Remark</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Previous Remarks -->
    <div class="col-md-5 d-flex">
      <div class="card shadow-sm w-100 h-100">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
          <strong>Previous Remarks</strong>
        </div>
        <div class="card-body p-3 overflow-auto" style="background: linear-gradient(to right, #6a11cb, #2575fc);" id="callingRemarkList">
          <ul class="list-group small">
            @forelse ($calling->remarks as $r)
              <li class="list-group-item d-flex justify-content-between align-items-start py-2 px-3">
                <div>
                  <strong>{{ optional($r->created_at)->format('Y-m-d') }}:</strong>
                  <div>{{ \Illuminate\Support\Str::limit($r->remark, 120) }}</div>
                </div>
                <button class="btn btn-sm btn-warning ms-2" onclick="fillRemark('{{ $r->id }}','{{ optional($r->created_at)->format('Y-m-d') }}','{{ $r->next_follow_up_date }}', `{{ addslashes($r->remark) }}`)">Edit</button>
              </li>
            @empty
              <li class="list-group-item text-muted">No remarks found.</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@push('styles')
<style>
  .calling-remarks .card { min-height: 520px; }
  #callingRemarkList { max-height: 520px; overflow-y: auto; }
  #callingRemarkList::-webkit-scrollbar { width: 8px; }
  #callingRemarkList::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); border-radius: 4px; }
  #callingRemarkList::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.4); border-radius: 4px; }
  #callingRemarkList { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.4) rgba(255,255,255,0.1); }
</style>
@endpush

@push('scripts')
<script>
  $(document).ready(function() {
    // Load calling type options
    loadCallingTypeOptions();
    
    function loadCallingTypeOptions() {
      $.get('{{ route("getcallingtypes") }}', function(callingTypes) {
        var $callingTypeSelect = $('#calling_type_id');
        $callingTypeSelect.empty().append('<option value="">Select Calling Type</option>');
        
        if (callingTypes && callingTypes.length > 0) {
          callingTypes.forEach(function(callingType) {
            $callingTypeSelect.append('<option value="' + callingType.id + '">' + callingType.name + '</option>');
          });
        }
        
        // Set default calling type from backend
        var defaultCallingTypeId = {{ $defaultCallingType ?? 'null' }};
        if (defaultCallingTypeId) {
          $callingTypeSelect.val(defaultCallingTypeId);
        }
      }).fail(function() {
        console.error('Failed to load calling type options');
      });
    }
  });

  function fillRemark(id, date, nextDate, text) {
    document.getElementById('remark_id').value = id;
    document.getElementById('remark_date').value = date || '';
    document.getElementById('next_follow_up_date').value = nextDate || '';
    document.getElementById('remark').value = text || '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
</script>
@endpush
@endsection


