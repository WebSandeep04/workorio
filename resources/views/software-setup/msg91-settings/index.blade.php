@extends('layouts.app')

@section('title', 'WhatsApp MSG91 Settings')

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
    background: linear-gradient(135deg, #434afa 0%, #2930cc 100%);
    color: #ffffff;
    padding: 1.5rem 1.75rem;
  }
  .form-control-modern {
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.65rem 0.9rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
  }
  .form-control-modern:focus {
    border-color: #434afa;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.15);
  }
  .btn-primary-modern {
    background: #434afa;
    border: none;
    color: #ffffff;
    padding: 0.65rem 1.4rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
  }
  .btn-primary-modern:hover {
    background: #3238d9;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.3);
  }
  .btn-outline-modern {
    border: 1.5px solid #434afa;
    color: #434afa;
    background: #ffffff;
    padding: 0.65rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s;
  }
  .btn-outline-modern:hover {
    background: rgba(67, 74, 250, 0.08);
    color: #3238d9;
    border-color: #3238d9;
  }
  .info-badge {
    background: #f1f5f9;
    border-radius: 8px;
    padding: 1rem;
    border-left: 4px solid #434afa;
  }
  .status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
  }
  .status-configured {
    background: #ecfdf5;
    color: #059669;
  }
  .status-not-configured {
    background: #fef2f2;
    color: #dc2626;
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
          <li class="breadcrumb-item text-muted">Communication Setup</li>
          <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">WhatsApp MSG91 Settings</li>
        </ol>
      </nav>
      <h4 class="mb-0 fw-bold" style="color: #1e293b;">WhatsApp MSG91 Gateway Settings</h4>
      <p class="text-muted small mb-0">Configure your organization's custom MSG91 credentials, verified WhatsApp business number, and template namespace.</p>
    </div>

    <div>
      @if($setting && $setting->auth_key && $setting->whatsapp_number)
        <span class="status-indicator status-configured">
          <i class="bi bi-check-circle-fill"></i> Gateway Configured
        </span>
      @else
        <span class="status-indicator status-not-configured">
          <i class="bi bi-exclamation-triangle-fill"></i> Not Configured
        </span>
      @endif
    </div>
  </div>

  <div class="row g-4">
    <!-- Form Card -->
    <div class="col-12 col-lg-7">
      <div class="settings-card">
        <div class="settings-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1 fw-bold"><i class="bi bi-whatsapp me-2"></i>MSG91 WhatsApp Configuration</h5>
            <small class="opacity-75">Tenant dedicated WhatsApp gateway parameters</small>
          </div>
          <i class="bi bi-shield-lock-fill fs-3 opacity-50"></i>
        </div>

        <div class="p-4">
          <form id="msg91SettingsForm">
            @csrf

            <!-- Auth Key -->
            <div class="mb-3">
              <label for="auth_key" class="form-label fw-semibold" style="color: #334155;">
                MSG91 Auth Key <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px; border-color: #e2e8f0;">
                  <i class="bi bi-key-fill text-muted"></i>
                </span>
                <input type="password" class="form-control form-control-modern border-start-0" 
                       id="auth_key" name="auth_key" 
                       value="{{ $setting->auth_key ?? '' }}" 
                       placeholder="e.g. 439058...YourAuthKey..." required>
                <button type="button" class="btn btn-outline-secondary" id="btnToggleAuthKey" style="border-radius: 0 8px 8px 0; border-color: #e2e8f0;">
                  <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
              </div>
              <small class="text-muted">Find this in your MSG91 Dashboard under API Keys &gt; Auth Key.</small>
            </div>

            <!-- Integrated WhatsApp Number -->
            <div class="mb-3">
              <label for="whatsapp_number" class="form-label fw-semibold" style="color: #334155;">
                Integrated WhatsApp Number <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px; border-color: #e2e8f0;">
                  <i class="bi bi-telephone-fill text-muted"></i>
                </span>
                <input type="text" class="form-control form-control-modern border-start-0" 
                       id="whatsapp_number" name="whatsapp_number" 
                       value="{{ $setting->whatsapp_number ?? '' }}" 
                       placeholder="e.g. 919451820817 (include country code without +)" required>
              </div>
              <small class="text-muted">The registered WhatsApp Business number associated with your MSG91 account (digits only with country code).</small>
            </div>

            <!-- Template Namespace -->
            <div class="mb-4">
              <label for="whatsapp_namespace" class="form-label fw-semibold" style="color: #334155;">
                WhatsApp Template Namespace <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px; border-color: #e2e8f0;">
                  <i class="bi bi-code-slash text-muted"></i>
                </span>
                <input type="text" class="form-control form-control-modern border-start-0" 
                       id="whatsapp_namespace" name="whatsapp_namespace" 
                       value="{{ $setting->whatsapp_namespace ?? '' }}" 
                       placeholder="e.g. 78b03afb_64e5_429f_813d_68bad14fe1cd" required>
              </div>
              <small class="text-muted">Found in your Meta WhatsApp Manager or MSG91 WhatsApp Account details.</small>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2">
              <button type="button" class="btn-outline-modern d-flex align-items-center gap-2" id="btnTestConnection">
                <i class="bi bi-broadcast"></i>
                <span id="testBtnText">Test Connection</span>
              </button>

              <button type="submit" class="btn-primary-modern d-flex align-items-center gap-2" id="btnSaveSettings">
                <i class="bi bi-check2-circle fs-5"></i>
                <span id="saveBtnText">Save Settings</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Instructions / Guide Card -->
    <div class="col-12 col-lg-5">
      <div class="settings-card p-4 h-100">
        <h5 class="fw-bold mb-3" style="color: #1e293b;">
          <i class="bi bi-info-circle-fill text-primary me-2"></i>Configuration Guide
        </h5>
        
        <div class="info-badge mb-3">
          <h6 class="fw-bold mb-1 text-primary">Multi-Tenant Isolation</h6>
          <p class="small text-muted mb-0">Each tenant has an isolated set of credentials. Messages sent from your campaigns or automated leads will strictly use your own MSG91 account and verified sender number.</p>
        </div>

        <ul class="list-group list-group-flush small text-secondary mb-4">
          <li class="list-group-item px-0 py-2 d-flex gap-2">
            <i class="bi bi-1-circle-fill text-primary"></i>
            <div>
              <strong>Step 1: Get Auth Key</strong>
              <div class="text-muted">Login to <a href="https://msg91.com" target="_blank" class="text-decoration-none">msg91.com</a>, navigate to <em>Developer &gt; Auth Key</em>, generate or copy your secret key.</div>
            </div>
          </li>
          <li class="list-group-item px-0 py-2 d-flex gap-2">
            <i class="bi bi-2-circle-fill text-primary"></i>
            <div>
              <strong>Step 2: WhatsApp Number</strong>
              <div class="text-muted">Enter your verified WhatsApp business phone number including the country code (e.g. <code>91</code> for India).</div>
            </div>
          </li>
          <li class="list-group-item px-0 py-2 d-flex gap-2">
            <i class="bi bi-3-circle-fill text-primary"></i>
            <div>
              <strong>Step 3: Template Namespace</strong>
              <div class="text-muted">Copy the Meta template namespace assigned to your WhatsApp Business Account.</div>
            </div>
          </li>
          <li class="list-group-item px-0 py-2 d-flex gap-2">
            <i class="bi bi-4-circle-fill text-primary"></i>
            <div>
              <strong>Step 4: Test Connection</strong>
              <div class="text-muted">Click <em>Test Connection</em> to confirm your templates can be synchronized and your API access is live.</div>
            </div>
          </li>
        </ul>

        <div class="card bg-white border border-primary border-opacity-25 p-3 rounded-3 mb-3 shadow-sm">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-bold text-dark small"><i class="bi bi-link-45deg text-primary me-1"></i>Your Tenant Webhook URLs</span>
            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 10px;">Tenant: {{ $tenant->tenant_name ?? 'Current' }}</span>
          </div>
          <p class="text-muted" style="font-size: 11px;">Configure these two URLs in your MSG91 WhatsApp Webhook dashboard so incoming messages and delivery reports update automatically.</p>

          <!-- Inbound Webhook -->
          <div class="mb-2">
            <label class="form-label mb-1" style="font-size: 11px; font-weight: 600;">Inbound Webhook (Customer Replies)</label>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control bg-light font-monospace" style="font-size: 11px;" value="{{ $inboundWebhookUrl }}" id="inboundWebhookInput" readonly>
              <button class="btn btn-outline-primary btn-copy" type="button" data-clipboard-target="#inboundWebhookInput" title="Copy to clipboard">
                <i class="bi bi-clipboard"></i>
              </button>
            </div>
          </div>

          <!-- Outbound Webhook -->
          <div>
            <label class="form-label mb-1" style="font-size: 11px; font-weight: 600;">Outbound Webhook (Delivery Reports)</label>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control bg-light font-monospace" style="font-size: 11px;" value="{{ $outboundWebhookUrl }}" id="outboundWebhookInput" readonly>
              <button class="btn btn-outline-primary btn-copy" type="button" data-clipboard-target="#outboundWebhookInput" title="Copy to clipboard">
                <i class="bi bi-clipboard"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="card bg-light border-0 p-3 rounded-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-link-45deg text-primary fs-5"></i>
            <span class="fw-bold text-dark small">Quick Links</span>
          </div>
          <div class="d-flex flex-column gap-2 small">
            <a href="{{ route('whatsapp-templates.index') }}" class="text-decoration-none text-primary d-flex align-items-center gap-1">
              <i class="bi bi-arrow-right-short"></i> Go to WhatsApp Templates
            </a>
            <a href="{{ route('whatsapp-campaigns.index') }}" class="text-decoration-none text-primary d-flex align-items-center gap-1">
              <i class="bi bi-arrow-right-short"></i> Go to WhatsApp Campaigns
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
  // Password show/hide toggle
  $('#btnToggleAuthKey').on('click', function() {
    const input = $('#auth_key');
    const icon = $('#toggleIcon');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
  });

  // Submit Settings Form
  $('#msg91SettingsForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnSaveSettings');
    const btnText = $('#saveBtnText');
    const originalText = btnText.text();

    btn.prop('disabled', true);
    btnText.text('Saving...');

    $.ajax({
      url: "{{ route('software-setup.msg91-settings.store') }}",
      type: "POST",
      data: $(this).serialize(),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        btn.prop('disabled', false);
        btnText.text(originalText);
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Settings Saved!',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message || 'Unable to save settings.'
          });
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false);
        btnText.text(originalText);
        const msg = xhr.responseJSON?.message || 'Failed to save settings. Please try again.';
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: msg
        });
      }
    });
  });

  // Test Connection
  $('#btnTestConnection').on('click', function() {
    const btn = $(this);
    const btnText = $('#testBtnText');
    const originalText = btnText.text();

    const authKey = $('#auth_key').val().trim();
    const whatsappNumber = $('#whatsapp_number').val().trim();

    if (!authKey || !whatsappNumber) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Fields',
        text: 'Please enter both the Auth Key and WhatsApp Number before testing.'
      });
      return;
    }

    btn.prop('disabled', true);
    btnText.text('Testing Connection...');

    $.ajax({
      url: "{{ route('software-setup.msg91-settings.test') }}",
      type: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        auth_key: authKey,
        whatsapp_number: whatsappNumber
      },
      success: function(response) {
        btn.prop('disabled', false);
        btnText.text(originalText);
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Connection Successful!',
            text: response.message
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Connection Failed',
            text: response.message
          });
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false);
        btnText.text(originalText);
        const msg = xhr.responseJSON?.message || 'Connection test failed. Check your credentials.';
        Swal.fire({
          icon: 'error',
          title: 'Connection Error',
          text: msg
        });
      }
    });
  // Clipboard Copy for Webhook URLs
  $('.btn-copy').on('click', function() {
    const targetSelector = $(this).data('clipboard-target');
    const input = $(targetSelector);
    if (input.length) {
      input.select();
      navigator.clipboard.writeText(input.val()).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: 'Webhook URL copied to clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      }).catch(() => {
        document.execCommand('copy');
      });
    }
  });
});
</script>
@endpush
