@extends('layouts.app')

@section('title', 'IndiaMART Push API Settings')

@push('styles')
<style>
  .settings-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #eef2f6;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    overflow: hidden;
  }
  .settings-header {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    padding: 1.5rem 1.75rem;
  }
  .info-badge {
    background: #f0f9ff;
    border-radius: 8px;
    padding: 1rem;
    border-left: 4px solid #0284c7;
  }
  .webhook-input {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    font-family: monospace;
    font-size: 0.9rem;
    color: #0f172a;
    font-weight: 500;
  }
  .step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
  <!-- Breadcrumb & Header -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1" style="font-size: 12px;">
          <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
          <li class="breadcrumb-item text-muted">Software Setup</li>
          <li class="breadcrumb-item text-muted">Sales Setup</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">IndiaMART Setup</li>
        </ol>
      </nav>
      <h4 class="mb-0 fw-bold" style="color: #1e293b;">IndiaMART Push API Setup</h4>
      <p class="text-muted small mb-0">Configure real-time lead push integration directly from your IndiaMART seller account into your Workorio CRM.</p>
    </div>

    <div>
      <a href="{{ route('indiamart.index') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 shadow-sm px-3 py-2">
        <i class="bi bi-person-lines-fill text-primary"></i>
        <span>Go to External Leads</span>
      </a>
    </div>
  </div>

  <div class="row g-4">
    <!-- Main Webhook Card -->
    <div class="col-12 col-lg-7">
      <div class="settings-card">
        <div class="settings-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1 fw-bold"><i class="bi bi-broadcast me-2"></i>IndiaMART Push API Integration</h5>
            <small class="opacity-75">Dedicated webhook endpoint for your organization</small>
          </div>
          <i class="bi bi-shield-check fs-2 opacity-50"></i>
        </div>

        <div class="p-4">
          <div class="info-badge mb-4">
            <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-lightning-charge-fill me-1"></i>Real-time Automated Lead Flow</h6>
            <p class="small text-muted mb-0">Every time a buyer sends an inquiry or quotation request on IndiaMART, IndiaMART's servers push the lead directly to this webhook. It automatically saves into your External Leads, alerts your sales team, and greets the buyer on WhatsApp.</p>
          </div>

          <!-- Webhook URL Display -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark mb-1">
              Your Dedicated Push API Webhook URL
            </label>
            <div class="input-group">
              <input type="text" class="form-control form-control-lg webhook-input" id="indiamartWebhookInput" value="{{ $webhookUrl }}" readonly>
              <button class="btn btn-primary px-3 d-flex align-items-center gap-1" id="btnCopyWebhook" type="button">
                <i class="bi bi-clipboard"></i> <span id="copyBtnText">Copy URL</span>
              </button>
            </div>
            <small class="text-muted mt-1 d-block">
              Tenant Identifier: <strong class="text-dark">{{ $tenant->tenant_name ?? 'Tenant' }} ({{ $tenant->tenant_code ?? $tenant->id }})</strong>
            </small>
          </div>

          <!-- Test Webhook Ping -->
          <div class="p-3 bg-light rounded-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border">
            <div>
              <div class="fw-bold text-dark small">Test Webhook Connectivity</div>
              <small class="text-muted">Sends a GET ping to verify that your endpoint is reachable by IndiaMART.</small>
            </div>
            <button class="btn btn-sm btn-outline-primary px-3" id="btnTestPing">
              <i class="bi bi-arrow-repeat me-1" id="pingIcon"></i> Test Endpoint
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Instructions / Guide Card -->
    <div class="col-12 col-lg-5">
      <div class="settings-card p-4 h-100">
        <h5 class="fw-bold mb-3" style="color: #1e293b;">
          <i class="bi bi-question-circle-fill text-primary me-2"></i>How to Configure in IndiaMART
        </h5>

        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3">
            <div class="step-circle">1</div>
            <div>
              <div class="fw-bold text-dark small">Log in to IndiaMART Seller Panel</div>
              <div class="text-muted small">Go to <a href="https://seller.indiamart.com" target="_blank" class="text-decoration-none">seller.indiamart.com</a> and log in with your primary registered mobile number.</div>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="step-circle">2</div>
            <div>
              <div class="fw-bold text-dark small">Navigate to CRM Integration</div>
              <div class="text-muted small">Go to <strong>Lead Manager</strong> &gt; <strong>CRM Integration</strong> &gt; <strong>Push API</strong>.</div>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="step-circle">3</div>
            <div>
              <div class="fw-bold text-dark small">Paste the Webhook URL</div>
              <div class="text-muted small">Select <strong>Push API</strong>, paste your dedicated Webhook URL above, choose <strong>JSON</strong> format, and click <strong>Save / Test</strong>.</div>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="step-circle">4</div>
            <div>
              <div class="fw-bold text-dark small">Done!</div>
              <div class="text-muted small">IndiaMART will automatically start pushing leads to your Workorio dashboard in real-time.</div>
            </div>
          </div>
        </div>

        <div class="card bg-light border-0 p-3 rounded-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-link-45deg text-primary fs-5"></i>
            <span class="fw-bold text-dark small">Related Settings</span>
          </div>
          <div class="d-flex flex-column gap-2 small">
            <a href="{{ route('software-setup.msg91-settings.index') }}" class="text-decoration-none text-primary d-flex align-items-center gap-1">
              <i class="bi bi-arrow-right-short"></i> WhatsApp MSG91 Settings (for auto greeting)
            </a>
            <a href="{{ route('indiamart.index') }}" class="text-decoration-none text-primary d-flex align-items-center gap-1">
              <i class="bi bi-arrow-right-short"></i> View All External Leads
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  // Copy Webhook URL
  $('#btnCopyWebhook').on('click', function() {
    const input = document.getElementById('indiamartWebhookInput');
    if (input) {
      input.select();
      navigator.clipboard.writeText(input.value).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: 'IndiaMART Webhook URL copied to clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      }).catch(() => {
        document.execCommand('copy');
      });
    }
  });

  // Test Ping Endpoint
  $('#btnTestPing').on('click', function() {
    const btn = $(this);
    const icon = $('#pingIcon');
    icon.addClass('spin');
    btn.prop('disabled', true);

    const url = $('#indiamartWebhookInput').val();

    $.ajax({
      url: url,
      type: 'GET',
      success: function(res) {
        icon.removeClass('spin');
        btn.prop('disabled', false);
        Swal.fire({
          icon: 'success',
          title: 'Endpoint Active!',
          text: 'The webhook endpoint responded with HTTP 200 OK. IndiaMART can successfully connect.'
        });
      },
      error: function(xhr) {
        icon.removeClass('spin');
        btn.prop('disabled', false);
        Swal.fire({
          icon: 'error',
          title: 'Test Failed',
          text: xhr.responseJSON?.message || 'Unable to reach endpoint.'
        });
      }
    });
  });
});
</script>
@endpush
