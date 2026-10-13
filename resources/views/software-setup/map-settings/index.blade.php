@extends('layouts.app')

@section('title', 'Map API Setting')
@section('page_title', 'Map API Setting')

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

  .btn-modern-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
  }

  /* Provider Card Radio Options */
  .provider-choice-card {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8fafc;
    position: relative;
  }

  .provider-choice-card:hover {
    border-color: #cbd5e1;
    background: #fff;
  }

  .provider-choice-card.selected {
    border-color: #434AFA;
    background: #f5f6ff;
  }

  .provider-radio-input {
    position: absolute;
    top: 1.1rem;
    right: 1.1rem;
    transform: scale(1.2);
    accent-color: #434AFA;
  }

  /* Quota Progress Meter */
  .quota-progress {
    height: 7px;
    border-radius: 10px;
    background: #e2e8f0;
    overflow: hidden;
  }

  .quota-progress-bar {
    height: 100%;
    transition: width 0.4s ease;
  }

  /* Tier Step Pills */
  .tier-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    text-transform: uppercase;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
  <!-- Breadcrumb & Header -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size: 12px;">
          <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
          <li class="breadcrumb-item text-muted">Software Setup</li>
          <li class="breadcrumb-item text-muted">Organization Setup</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Map API Setting</li>
        </ol>
      </nav>
      <h4 class="mb-0 fw-bold" style="color: #1e293b;">Map API Setting</h4>
      <p class="text-muted small mb-0">Configure your organization's custom Google Maps and Ola Maps API keys with automatic failover (Bring Your Own API - BYO).</p>
    </div>

    <div>
      <a href="{{ route('tracking.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-sm px-3 py-2">
        <i class="bi bi-geo-alt-fill text-primary"></i>
        <span>Go to Live Tracking</span>
      </a>
    </div>
  </div>

  <div class="row g-4">
    <!-- Main Configuration Card -->
    <div class="col-12 col-lg-8">
      <div class="modern-card">
        <!-- Status Indicator Banner -->
        @if(!empty($setting->is_byo_enabled))
          @php
            $googleLimit = $setting->google_limit ?? 10000;
            $googleUsage = $setting->google_usage ?? 0;
            $googleExhausted = !empty($setting->google_api_key) && $googleLimit > 0 && $googleUsage >= $googleLimit;

            $olaLimit = $setting->ola_limit ?? 50000;
            $olaUsage = $setting->ola_usage ?? 0;
            $olaExhausted = !empty($setting->ola_api_key) && $olaLimit > 0 && $olaUsage >= $olaLimit;

            $pref = $setting->preferred_provider ?? 'google';
            $activeEngine = 'None';
            $isFailover = false;

            if ($pref === 'google') {
                if (!empty($setting->google_api_key) && !$googleExhausted) {
                    $activeEngine = 'Google Maps (Primary)';
                } elseif (!empty($setting->ola_api_key) && !$olaExhausted) {
                    $activeEngine = 'Ola Maps (Failover Active)';
                    $isFailover = true;
                } else {
                    $activeEngine = 'OpenStreetMap (BYO Quotas Exhausted)';
                }
            } else {
                if (!empty($setting->ola_api_key) && !$olaExhausted) {
                    $activeEngine = 'Ola Maps (Primary)';
                } elseif (!empty($setting->google_api_key) && !$googleExhausted) {
                    $activeEngine = 'Google Maps (Failover Active)';
                    $isFailover = true;
                } else {
                    $activeEngine = 'OpenStreetMap (BYO Quotas Exhausted)';
                }
            }
          @endphp

          <div class="alert alert-success d-flex align-items-center gap-2 p-3 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>
              <div class="fw-bold d-flex align-items-center gap-2">
                <span>Bring Your Own API (BYO) is Active</span>
                <span class="badge rounded-pill {{ $isFailover ? 'bg-warning text-dark' : 'bg-success' }}" style="font-size: 11px;">
                  Active: {{ $activeEngine }}
                </span>
              </div>
              <div class="small text-muted" style="color: #15803d !important;">
                Automatic failover is active. <strong>0 Master Product Quota</strong> is consumed.
              </div>
            </div>
          </div>
        @else
          <div class="alert alert-info d-flex align-items-center gap-2 p-3 mb-4 rounded-3 border-0 shadow-sm" style="background-color: #eff6ff; color: #1e40af;">
            <i class="bi bi-info-circle-fill fs-5 text-primary"></i>
            <div>
              <div class="fw-bold">Platform Master Quota Mode Active</div>
              <div class="small text-muted" style="color: #1d4ed8 !important;">
                BYO is disabled. Live tracking utilizes the platform's global master quota with zero-cost OpenStreetMap fallback.
              </div>
            </div>
          </div>
        @endif

        <form id="mapSettingForm" action="{{ route('software-setup.map-settings.save') }}" method="POST">
          @csrf

          <!-- Switch Toggle: BYO Mode -->
          <div class="p-3 rounded-3 mb-4 d-flex align-items-center justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
            <div>
              <label class="form-check-label fw-bold d-block mb-1" for="byoToggle" style="color: #0f172a; cursor: pointer;">
                <i class="bi bi-key-fill text-primary me-1"></i> Enable Bring Your Own API (BYO)
              </label>
              <div class="text-muted small">
                When enabled, your organization uses your own dedicated keys. When one key's monthly quota finishes, the system automatically switches to the other!
              </div>
            </div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" id="byoToggle" name="is_byo_enabled" value="1" {{ !empty($setting->is_byo_enabled) ? 'checked' : '' }} style="cursor: pointer; width: 44px; height: 22px;">
            </div>
          </div>

          <!-- Configuration Fields (Visible when BYO is enabled) -->
          <div id="byoConfigArea" class="{{ !empty($setting->is_byo_enabled) ? '' : 'd-none' }}">
            
            <!-- Provider Selection -->
            <div class="mb-4">
              <label class="form-label-modern mb-1">
                <i class="bi bi-shuffle"></i> Primary Provider Order
              </label>
              <p class="text-muted small mb-2">Select which provider to attempt first. If its monthly limit is reached, it automatically switches to the other provider.</p>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="provider-choice-card {{ ($setting->preferred_provider ?? 'google') === 'google' ? 'selected' : '' }}" onclick="selectProvider('google')">
                    <input class="provider-radio-input" type="radio" name="preferred_provider" id="provider_google" value="google" {{ ($setting->preferred_provider ?? 'google') === 'google' ? 'checked' : '' }}>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <i class="bi bi-google fs-5 text-danger"></i>
                      <span class="fw-bold text-dark">Google First ➔ Ola Second</span>
                    </div>
                    <div class="text-muted small">
                      Attempts Google Maps first. When Google quota finishes, automatically fails over to Ola Maps.
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="provider-choice-card {{ ($setting->preferred_provider ?? '') === 'ola' ? 'selected' : '' }}" onclick="selectProvider('ola')">
                    <input class="provider-radio-input" type="radio" name="preferred_provider" id="provider_ola" value="ola" {{ ($setting->preferred_provider ?? '') === 'ola' ? 'checked' : '' }}>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <i class="bi bi-geo-alt-fill fs-5 text-success"></i>
                      <span class="fw-bold text-dark">Ola First ➔ Google Second</span>
                    </div>
                    <div class="text-muted small">
                      Attempts Ola Maps first. When Ola quota finishes, automatically fails over to Google Maps.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Google Maps API Configuration Card -->
            <div class="p-3 rounded-3 mb-3 border bg-light bg-opacity-50">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-google text-danger fs-5"></i>
                  <span class="fw-bold text-dark">Google Maps Configuration</span>
                </div>
                @php
                  $gLimit = (int) ($setting->google_limit ?? 10000);
                  $gUsage = (int) ($setting->google_usage ?? 0);
                  $gPercent = $gLimit > 0 ? min(round(($gUsage / $gLimit) * 100), 100) : 0;
                @endphp
                <span class="badge rounded-pill {{ $gPercent >= 100 ? 'bg-danger' : ($gPercent >= 80 ? 'bg-warning text-dark' : 'bg-primary') }}" style="font-size: 11px;">
                  {{ number_format($gUsage) }} / {{ number_format($gLimit) }} requests ({{ $gPercent }}%)
                </span>
              </div>

              <!-- Usage Progress Meter -->
              <div class="quota-progress mb-3">
                <div class="quota-progress-bar {{ $gPercent >= 100 ? 'bg-danger' : ($gPercent >= 80 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ $gPercent }}%;"></div>
              </div>

              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label-modern" for="google_api_key">
                    <i class="bi bi-key"></i> Google Maps API Key
                  </label>
                  <div class="position-relative">
                    <input type="password" class="form-control-modern pe-5" id="google_api_key" name="google_api_key" value="{{ $setting->google_api_key ?? '' }}" placeholder="AIzaSy...">
                    <button type="button" class="btn btn-sm text-muted position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" onclick="togglePasswordVisibility('google_api_key', this)">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label-modern" for="google_limit">
                    <i class="bi bi-speedometer2"></i> Monthly Limit
                  </label>
                  <input type="number" class="form-control-modern" id="google_limit" name="google_limit" value="{{ $setting->google_limit ?? 10000 }}" min="0" placeholder="e.g. 10000">
                  <div class="form-text small text-muted" style="font-size: 11px;">0 = Unlimited</div>
                </div>
              </div>
              <div class="form-text small text-muted mt-2">
                Obtain from <a href="https://console.cloud.google.com/google/maps-apis" target="_blank" rel="noopener noreferrer" class="text-decoration-none">Google Cloud Console</a>. Enables Maps JS & Directions API.
              </div>
            </div>

            <!-- Ola Maps API Configuration Card -->
            <div class="p-3 rounded-3 mb-4 border bg-light bg-opacity-50">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                  <span class="fw-bold text-dark">Ola Maps Configuration</span>
                </div>
                @php
                  $oLimit = (int) ($setting->ola_limit ?? 50000);
                  $oUsage = (int) ($setting->ola_usage ?? 0);
                  $oPercent = $oLimit > 0 ? min(round(($oUsage / $oLimit) * 100), 100) : 0;
                @endphp
                <span class="badge rounded-pill {{ $oPercent >= 100 ? 'bg-danger' : ($oPercent >= 80 ? 'bg-warning text-dark' : 'bg-success') }}" style="font-size: 11px;">
                  {{ number_format($oUsage) }} / {{ number_format($oLimit) }} requests ({{ $oPercent }}%)
                </span>
              </div>

              <!-- Usage Progress Meter -->
              <div class="quota-progress mb-3">
                <div class="quota-progress-bar {{ $oPercent >= 100 ? 'bg-danger' : ($oPercent >= 80 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $oPercent }}%;"></div>
              </div>

              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label-modern" for="ola_api_key">
                    <i class="bi bi-key"></i> Ola Maps API Key
                  </label>
                  <div class="position-relative">
                    <input type="password" class="form-control-modern pe-5" id="ola_api_key" name="ola_api_key" value="{{ $setting->ola_api_key ?? '' }}" placeholder="Enter your Ola Maps API key...">
                    <button type="button" class="btn btn-sm text-muted position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" onclick="togglePasswordVisibility('ola_api_key', this)">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label-modern" for="ola_limit">
                    <i class="bi bi-speedometer2"></i> Monthly Limit
                  </label>
                  <input type="number" class="form-control-modern" id="ola_limit" name="ola_limit" value="{{ $setting->ola_limit ?? 50000 }}" min="0" placeholder="e.g. 50000">
                  <div class="form-text small text-muted" style="font-size: 11px;">0 = Unlimited</div>
                </div>
              </div>
              <div class="form-text small text-muted mt-2">
                Obtain from <a href="https://maps.olakrutrim.com" target="_blank" rel="noopener noreferrer" class="text-decoration-none">Ola Krutrim / Ola Maps Portal</a>.
              </div>
            </div>

          </div>

          <!-- Submit Buttons -->
          <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-4">
            <button type="button" class="btn btn-sm btn-light border text-muted" onclick="window.history.back()">
              Cancel
            </button>

            <button type="submit" id="btnSubmitForm" class="btn-modern-primary">
              <span id="btnSubmitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              <i class="bi bi-floppy-fill" id="btnSubmitIcon"></i>
              <span>Save Map Settings</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Informational Architecture & Failover Waterfall Card -->
    <div class="col-12 col-lg-4">
      <div class="modern-card">
        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #0f172a;">
          <i class="bi bi-shield-check text-primary"></i> Automatic Failover Waterfall
        </h6>

        <p class="text-muted small mb-3">
          Your organization's routes follow an uninterrupted failover cascade. When one engine's monthly quota is finished, the next engine takes over automatically:
        </p>

        <!-- Step 1: Primary BYO -->
        <div class="p-3 rounded-2 mb-2" style="background: #faf5ff; border: 1px solid #f3e8ff;">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="fw-bold small" style="color: #7e22ce;">1. Primary Provider (BYO)</span>
            <span class="tier-badge" style="background: #f3e8ff; color: #7e22ce;">Step 1</span>
          </div>
          <div class="text-muted" style="font-size: 11px;">
            Uses your preferred API key (Google or Ola). <strong>0 Master DB quota deducted</strong>.
          </div>
        </div>

        <div class="text-center my-1 text-muted" style="font-size: 12px;">
          <i class="bi bi-arrow-down"></i> <em style="font-size: 10px;">When primary quota finished</em>
        </div>

        <!-- Step 2: Secondary BYO -->
        <div class="p-3 rounded-2 mb-2" style="background: #eef2ff; border: 1px solid #e0e7ff;">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="fw-bold small" style="color: #3730a3;">2. Secondary Provider (BYO)</span>
            <span class="tier-badge" style="background: #e0e7ff; color: #3730a3;">Step 2</span>
          </div>
          <div class="text-muted" style="font-size: 11px;">
            Switches seamlessly to your alternate key (Google or Ola). <strong>0 Master DB quota deducted</strong>.
          </div>
        </div>

        <div class="text-center my-1 text-muted" style="font-size: 12px;">
          <i class="bi bi-arrow-down"></i> <em style="font-size: 10px;">When secondary quota finished</em>
        </div>

        <!-- Step 3: Zero-Cost OSM -->
        <div class="p-3 rounded-2" style="background: #f0fdf4; border: 1px solid #dcfce7;">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="fw-bold small" style="color: #166534;">3. Free OpenStreetMap</span>
            <span class="tier-badge" style="background: #dcfce7; color: #166534;">100% Free</span>
          </div>
          <div class="text-muted" style="font-size: 11px;">
            Zero-cost, unlimited fallback. Guarantees your staff and admins never face map downtime.
          </div>
        </div>

        <hr class="my-3 text-muted">

        <div class="d-flex align-items-start gap-2 text-muted" style="font-size: 12px;">
          <i class="bi bi-arrow-repeat text-primary fs-6 mt-1"></i>
          <div>
            <strong>Monthly Reset:</strong> On the 1st of every month, your usage counters automatically reset to 0, restarting with your primary key.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  // Provider Selection Helper
  function selectProvider(provider) {
    document.getElementById('provider_' + provider).checked = true;
    document.querySelectorAll('.provider-choice-card').forEach(card => card.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
  }

  // Toggle Password Visibility
  function togglePasswordVisibility(fieldId, btnEl) {
    const input = document.getElementById(fieldId);
    if (!input) return;

    if (input.type === 'password') {
      input.type = 'text';
      btnEl.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
      input.type = 'password';
      btnEl.innerHTML = '<i class="bi bi-eye"></i>';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const byoToggle = document.getElementById('byoToggle');
    const byoConfigArea = document.getElementById('byoConfigArea');
    const mapSettingForm = document.getElementById('mapSettingForm');
    const btnSubmit = document.getElementById('btnSubmitForm');
    const btnSpinner = document.getElementById('btnSubmitSpinner');
    const btnIcon = document.getElementById('btnSubmitIcon');

    // Toggle BYO Area visibility
    if (byoToggle && byoConfigArea) {
      byoToggle.addEventListener('change', function() {
        if (this.checked) {
          byoConfigArea.classList.remove('d-none');
        } else {
          byoConfigArea.classList.add('d-none');
        }
      });
    }

    // Handle AJAX Form Submit
    if (mapSettingForm) {
      mapSettingForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        btnSubmit.disabled = true;
        btnSpinner.classList.remove('d-none');
        btnIcon.classList.add('d-none');

        const isByoEnabled = byoToggle.checked;
        const selectedProvider = document.querySelector('input[name="preferred_provider"]:checked')?.value || 'google';
        const googleKey = document.getElementById('google_api_key').value.trim();
        const googleLimit = document.getElementById('google_limit').value;
        const olaKey = document.getElementById('ola_api_key').value.trim();
        const olaLimit = document.getElementById('ola_limit').value;

        // Validation for enabled BYO
        if (isByoEnabled) {
          if (!googleKey && !olaKey) {
            toastr.error('Please enter at least one API Key (Google Maps or Ola Maps).', 'Validation Error');
            btnSubmit.disabled = false;
            btnSpinner.classList.add('d-none');
            btnIcon.classList.remove('d-none');
            return;
          }
        }

        try {
          const resp = await fetch("{{ route('software-setup.map-settings.save') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              is_byo_enabled: isByoEnabled,
              preferred_provider: selectedProvider,
              google_api_key: googleKey,
              google_limit: googleLimit ? parseInt(googleLimit, 10) : 10000,
              ola_api_key: olaKey,
              ola_limit: olaLimit ? parseInt(olaLimit, 10) : 50000
            })
          });

          const data = await resp.json();

          if (data.success) {
            toastr.success(data.message || 'Map settings updated successfully!', 'Saved');
            setTimeout(() => {
              window.location.reload();
            }, 800);
          } else {
            toastr.error(data.message || 'Failed to update map settings.', 'Error');
          }
        } catch (err) {
          console.error('Error saving map settings:', err);
          toastr.error('An unexpected error occurred while saving: ' + err.message, 'Error');
        } finally {
          btnSubmit.disabled = false;
          btnSpinner.classList.add('d-none');
          btnIcon.classList.remove('d-none');
        }
      });
    }
  });
</script>
@endpush
