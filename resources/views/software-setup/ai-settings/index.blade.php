@extends('layouts.app')

@section('title', 'AI API Settings')
@section('page_title', 'AI API Settings')

@push('styles')
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
  .container-fluid {
    padding: 0.75rem 1.25rem;
  }

  .modern-card {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #fff;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .form-label-modern {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.9rem;
  }

  .form-control-modern {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0.65rem 0.9rem;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    width: 100%;
    color: #334155;
  }

  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.15);
    outline: none;
  }

  .btn-modern-primary {
    background: #434AFA;
    color: white;
    padding: 0.65rem 1.75rem;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(67, 74, 250, 0.25);
  }

  .btn-modern-primary:hover:not(:disabled) {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.35);
    color: white;
  }

  .ai-service-card {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.25rem;
    background: #f8fafc;
    transition: all 0.2s ease;
  }

  .ai-service-card.active-byo {
    border-color: #c7d2fe;
    background: #fdfdff;
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.05);
  }

  .form-switch .form-check-input {
    width: 2.5rem;
    height: 1.35rem;
    cursor: pointer;
  }
  .form-switch .form-check-input:checked {
    background-color: #434AFA;
    border-color: #434AFA;
  }

  .badge-status {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
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
  <div class="modern-card">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h4 class="fw-bold mb-1" style="color: #1e293b; font-size: 1.25rem;">
          <i class="bi bi-cpu text-primary me-2"></i>AI API Settings (Bring Your Own Key)
        </h4>
        <p class="text-muted small mb-0">
          Configure custom Gemini and OpenAI API keys for this tenant. When BYO is active, the system prefers your custom key; otherwise, it seamlessly uses the system default environment key.
        </p>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form action="{{ route('software-setup.ai-settings.save') }}" method="POST" id="aiSettingsForm">
      @csrf

      <div class="row g-4">
        <!-- 1. Google Gemini Section -->
        <div class="col-lg-6">
          <div class="ai-service-card h-100 {{ $setting->is_gemini_byo_enabled ? 'active-byo' : '' }}" id="card_gemini">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e0e7ff; color: #434AFA;">
                  <i class="bi bi-google fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark">Google Gemini AI</h6>
                  <span class="text-muted small">OCR Card Reader & Data Parsing</span>
                </div>
              </div>

              <!-- Switch -->
              <div class="form-check form-switch m-0">
                <input type="hidden" name="is_gemini_byo_enabled" value="0">
                <input class="form-check-input byo-toggle" type="checkbox" name="is_gemini_byo_enabled" id="is_gemini_byo_enabled" value="1" {{ $setting->is_gemini_byo_enabled ? 'checked' : '' }} data-target="gemini_fields">
              </div>
            </div>

            <!-- Active Status Indicator -->
            <div class="mb-3">
              @if($setting->is_gemini_byo_enabled && !empty($setting->gemini_api_key))
                <span class="badge-status bg-success-subtle text-success border border-success-subtle">
                  <i class="bi bi-check-circle-fill"></i> Using Tenant's Custom Key
                </span>
              @elseif($globalGeminiConfigured)
                <span class="badge-status bg-primary-subtle text-primary border border-primary-subtle">
                  <i class="bi bi-shield-check"></i> Using System Default Environment Key
                </span>
              @else
                <span class="badge-status bg-warning-subtle text-warning border border-warning-subtle">
                  <i class="bi bi-exclamation-triangle-fill"></i> Not Configured
                </span>
              @endif
            </div>

            <!-- Input fields -->
            <div id="gemini_fields" style="{{ $setting->is_gemini_byo_enabled ? '' : 'opacity: 0.6;' }}">
              <label class="form-label-modern" for="gemini_api_key">
                <i class="bi bi-key-fill text-muted"></i> Gemini API Key
              </label>
              <div class="input-group mb-2">
                <input type="password" class="form-control form-control-modern" name="gemini_api_key" id="gemini_api_key" value="{{ old('gemini_api_key', $setting->gemini_api_key) }}" placeholder="AIzaSy..." {{ $setting->is_gemini_byo_enabled ? '' : 'readonly' }}>
                <button class="btn btn-outline-secondary toggle-pwd-btn" type="button" data-input="gemini_api_key">
                  <i class="bi bi-eye"></i>
                </button>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Supports `gemini-2.0-flash` & `gemini-flash-lite`</small>
                <button type="button" class="btn btn-outline-primary btn-sm test-key-btn" data-type="gemini">
                  <i class="bi bi-lightning-charge me-1"></i> Test Gemini Key
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. OpenAI Section -->
        <div class="col-lg-6">
          <div class="ai-service-card h-100 {{ $setting->is_openai_byo_enabled ? 'active-byo' : '' }}" id="card_openai">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #e6f4ea; color: #137333;">
                  <i class="bi bi-robot fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark">OpenAI</h6>
                  <span class="text-muted small">AI Task Detector & Interaction Classifier</span>
                </div>
              </div>

              <!-- Switch -->
              <div class="form-check form-switch m-0">
                <input type="hidden" name="is_openai_byo_enabled" value="0">
                <input class="form-check-input byo-toggle" type="checkbox" name="is_openai_byo_enabled" id="is_openai_byo_enabled" value="1" {{ $setting->is_openai_byo_enabled ? 'checked' : '' }} data-target="openai_fields">
              </div>
            </div>

            <!-- Active Status Indicator -->
            <div class="mb-3">
              @if($setting->is_openai_byo_enabled && !empty($setting->openai_api_key))
                <span class="badge-status bg-success-subtle text-success border border-success-subtle">
                  <i class="bi bi-check-circle-fill"></i> Using Tenant's Custom Key
                </span>
              @elseif($globalOpenAiConfigured)
                <span class="badge-status bg-primary-subtle text-primary border border-primary-subtle">
                  <i class="bi bi-shield-check"></i> Using System Default Environment Key
                </span>
              @else
                <span class="badge-status bg-warning-subtle text-warning border border-warning-subtle">
                  <i class="bi bi-exclamation-triangle-fill"></i> Not Configured
                </span>
              @endif
            </div>

            <!-- Input fields -->
            <div id="openai_fields" style="{{ $setting->is_openai_byo_enabled ? '' : 'opacity: 0.6;' }}">
              <label class="form-label-modern" for="openai_api_key">
                <i class="bi bi-key-fill text-muted"></i> OpenAI API Key
              </label>
              <div class="input-group mb-2">
                <input type="password" class="form-control form-control-modern" name="openai_api_key" id="openai_api_key" value="{{ old('openai_api_key', $setting->openai_api_key) }}" placeholder="sk-proj-..." {{ $setting->is_openai_byo_enabled ? '' : 'readonly' }}>
                <button class="btn btn-outline-secondary toggle-pwd-btn" type="button" data-input="openai_api_key">
                  <i class="bi bi-eye"></i>
                </button>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Used for WhatsApp interaction task extraction</small>
                <button type="button" class="btn btn-outline-success btn-sm test-key-btn" data-type="openai">
                  <i class="bi bi-lightning-charge me-1"></i> Test OpenAI Key
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer submit button -->
      <div class="mt-4 pt-3 border-top d-flex justify-content-end">
        <button type="submit" class="btn-modern-primary">
          <i class="bi bi-check2-circle"></i> Save AI Settings
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 4000
    };

    // Toggle BYO Switches
    $('.byo-toggle').on('change', function() {
        const isChecked = $(this).is(':checked');
        const targetId = $(this).data('target');
        const container = $('#' + targetId);
        const card = $(this).closest('.ai-service-card');
        const input = container.find('input[type="password"], input[type="text"]');

        if (isChecked) {
            container.css('opacity', '1');
            input.prop('readonly', false).focus();
            card.addClass('active-byo');
        } else {
            container.css('opacity', '0.6');
            input.prop('readonly', true);
            card.removeClass('active-byo');
        }
    });

    // Toggle Password Peek
    $('.toggle-pwd-btn').on('click', function() {
        const inputId = $(this).data('input');
        const input = $('#' + inputId);
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Test Key Buttons
    $('.test-key-btn').on('click', function() {
        const type = $(this).data('type');
        const btn = $(this);
        const originalHtml = btn.html();
        const input = $('#' + type + '_api_key');
        const keyVal = input.val();

        btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin me-1"></i> Testing...');

        const testUrl = type === 'gemini' 
            ? '{{ route("software-setup.ai-settings.test-gemini") }}' 
            : '{{ route("software-setup.ai-settings.test-openai") }}';

        $.ajax({
            url: testUrl,
            type: 'POST',
            data: { key: keyVal },
            success: function(resp) {
                btn.prop('disabled', false).html(originalHtml);
                if (resp.success) {
                    toastr.success(resp.message);
                } else {
                    toastr.error(resp.message || 'Verification failed');
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalHtml);
                const msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Error validating API key.';
                toastr.error(msg);
            }
        });
    });
});
</script>
@endpush

