@extends('layouts.app')

@section('title', 'Configure Leave Rules')
@section('page_title', 'Configure Leave Rules - ' . $employmentType->name)

@push('styles')
<style>
  .modern-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
  .modern-card-header { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1.5rem; }
  .modern-card-body { padding: 1.5rem; }
  
  .leave-rule-row { 
      background: #ffffff; 
      border: 1px solid #e2e8f0; 
      border-radius: 8px; 
      padding: 1rem; 
      margin-bottom: 1rem; 
      transition: all 0.2s;
  }
  .leave-rule-row:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
  
  .form-label { font-size: 0.8rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
  .form-control, .form-select { border-radius: 6px; border: 1px solid #cbd5e1; font-size: 0.9rem; }
  
  .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
  .leave-name-badge { font-weight: 600; font-size: 1.1rem; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold"><i class="bi bi-briefcase me-2 text-primary"></i>{{ $employmentType->name }}</h2>
            <p class="text-muted mb-0">Configure mathematical leave generation rules for this employment type.</p>
        </div>
        <a href="{{ route('employment-types.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Types
        </a>
    </div>

    <form method="POST" action="{{ route('employment-types.save-leave-rules', $employmentType->id) }}">
        @csrf
        
        <div class="modern-card mb-4">
            <div class="modern-card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Active Leave Types Matrix</h5>
            </div>
            <div class="modern-card-body">
                @if($leaveTypes->isEmpty())
                    <div class="alert alert-warning">No Active Leave Types defined. Please create Leave Types first under Software Setup.</div>
                @else
                    @foreach($leaveTypes as $leave)
                        @php 
                            $rule = $rules->get($leave->id); 
                            $isEnabled = $rule ? true : false;
                        @endphp
                        
                        <div class="leave-rule-row">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch d-flex align-items-center gap-3">
                                        <input class="form-check-input mt-0 enable-toggle" type="checkbox" 
                                               name="rules[{{ $leave->id }}][enabled]" 
                                               value="1" 
                                               id="enable_{{ $leave->id }}"
                                               {{ $isEnabled ? 'checked' : '' }}>
                                        <label class="form-check-label leave-name-badge" for="enable_{{ $leave->id }}">
                                            @if($leave->color_code)
                                                <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:{{ $leave->color_code }};"></span>
                                            @endif
                                            {{ $leave->name }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 rule-config-box" id="config_{{ $leave->id }}" style="{{ $isEnabled ? '' : 'display: none; opacity: 0.5;' }}">
                                <div class="col-md-3">
                                    <label class="form-label">Generation Method</label>
                                    <select class="form-select generation-type-select" name="rules[{{ $leave->id }}][generation_type]">
                                        <option value="prefill" {{ ($rule->generation_type ?? '') == 'prefill' ? 'selected' : '' }}>Prefill (Upfront)</option>
                                        <option value="accrual" {{ ($rule->generation_type ?? '') == 'accrual' ? 'selected' : '' }}>Accrual (Earned)</option>
                                        <option value="unlimited" {{ ($rule->generation_type ?? '') == 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 matrix-val-col">
                                    <label class="form-label value-label">Base Days Given</label>
                                    <div class="input-group">
                                        <input type="number" step="1" min="0" class="form-control" 
                                               name="rules[{{ $leave->id }}][value]" 
                                               value="{{ $rule->value ?? 0 }}">
                                        <span class="input-group-text value-suffix">Days</span>
                                    </div>
                                    <small class="text-muted value-hint">Amount allocated instantly.</small>
                                </div>

                                <div class="col-md-3 matrix-cf-col">
                                    <label class="form-label">Carry Forward?</label>
                                    <select class="form-select carry-forward-select" name="rules[{{ $leave->id }}][carry_forward_allowed]">
                                        <option value="0" {{ !($rule->carry_forward_allowed ?? false) ? 'selected' : '' }}>No, Lapses at Year-End</option>
                                        <option value="1" {{ ($rule->carry_forward_allowed ?? false) ? 'selected' : '' }}>Yes, Rolls over</option>
                                    </select>
                                </div>

                                <div class="col-md-3 max-carry-col" style="{{ ($rule->carry_forward_allowed ?? false) ? '' : 'display: none;' }}">
                                    <label class="form-label">Maximum Carry FWD</label>
                                    <input type="number" step="1" min="0" class="form-control" 
                                           name="rules[{{ $leave->id }}][max_carry_forward]" 
                                           value="{{ $rule->max_carry_forward ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            @if($leaveTypes->isNotEmpty())
            <div class="modern-card-header bg-white text-end border-top">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                    <i class="bi bi-save me-2"></i> Save Matrix Rules
                </button>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // Toggle overall enabled box
    $('.enable-toggle').on('change', function() {
        let boxId = $(this).attr('id').replace('enable_', 'config_');
        if($(this).is(':checked')) {
            $('#' + boxId).slideDown().css('opacity', 1);
        } else {
            $('#' + boxId).slideUp().css('opacity', 0.5);
        }
    });

    // Toggle dependent texts based on Generation Method
    $('.generation-type-select').on('change', function() {
        let val = $(this).val();
        let parentRow = $(this).closest('.rule-config-box');
        let valCol = parentRow.find('.matrix-val-col');
        let cfCol = parentRow.find('.matrix-cf-col');
        let maxCol = parentRow.find('.max-carry-col');

        if (val === 'unlimited') {
            valCol.hide().find('input').prop('disabled', true).val('0');
            cfCol.hide().find('select').prop('disabled', true).val('0');
            maxCol.hide().find('input').prop('disabled', true).val('0');
        } else {
            valCol.show().find('input').prop('disabled', false);
            cfCol.show().find('select').prop('disabled', false);

            if (val === 'accrual') {
                parentRow.find('.value-label').text('Valid Days Threshold');
                parentRow.find('.value-hint').text('Days required to earn 1 full leave.');
                parentRow.find('.value-suffix').text('Valid Days');
            } else {
                // Prefill
                parentRow.find('.value-label').text('Base Initial Days');
                parentRow.find('.value-hint').text('Amount allocated instantly.');
                parentRow.find('.value-suffix').text('Days Given');
            }

            if (cfCol.find('select').val() === '1') {
                maxCol.show().find('input').prop('disabled', false);
            }
        }
    });
    
    // Toggle maximum carry forward limits visibility
    $('.carry-forward-select').on('change', function() {
        let val = $(this).val();
        let parentRow = $(this).closest('.rule-config-box');
        
        if (val === "1") {
            parentRow.find('.max-carry-col').fadeIn();
        } else {
            parentRow.find('.max-carry-col').fadeOut();
            parentRow.find('input[name*="[max_carry_forward]"]').val(0);
        }
    });

    // Trigger initial state dynamically for hints mapping
    $('.generation-type-select').trigger('change');
});
</script>
@endpush
