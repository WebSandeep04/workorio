@extends('layouts.app')

@section('title', 'Quotation Setup')
@section('page_title', 'Quotation Setup')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem 1rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Page Header */
  .page-header {
    background: #fff;
    padding: 1.5rem 2rem;
    border-radius: 5px;
    color: black;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    animation: fadeInDown 0.6s ease-out;
  }
  
  .page-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  /* Modern Card */
  .modern-card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid #f2f4f7;
  }

  /* Tabs */
  .tabs-container {
    display: flex;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    overflow-x: auto;
    padding: 0 1rem;
  }

  .tab-btn {
    padding: 1rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .tab-btn:hover {
    color: #434AFA;
    background: rgba(67, 74, 250, 0.05);
  }

  .tab-btn.active {
    color: #434AFA;
    border-bottom-color: #434AFA;
    background: #fff;
  }

  .tab-content {
    display: none;
    padding: 2rem;
    animation: fadeIn 0.4s ease-out;
  }

  .tab-content.active {
    display: block;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Forms */
  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.9rem;
  }

  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    width: 100%;
    background-color: #fff;
  }

  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  
  textarea.form-control-modern {
    min-height: 100px;
    resize: vertical;
  }

  /* Buttons */
  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
    padding: 0.75rem 2rem;
    font-size: 1rem;
    box-shadow: 0 4px 6px rgba(67, 74, 250, 0.2);
  }
  
  .btn-modern-primary:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(67, 74, 250, 0.3);
    color: white;
  }

  .btn-add {
    background: #434afa !important;
    color: white;
  }
  
  .btn-add:hover {
    background: #047857;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  }

  .btn-remove {
    background: #fee2e2;
    color: #ef4444;
    border: none;
    padding: 0.6rem;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .btn-remove:hover {
    background: #fecaca;
    color: #dc2626;
  }

  /* Services */
  .service-item {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    align-items: stretch;
  }
  
  .service-item input {
    flex: 1;
  }

  /* Alerts */
  .alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 320px;
  }

  .save-container {
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
  }

  @media (max-width: 768px) {
    .tabs-container {
      padding: 0;
    }
    .tab-btn {
      padding: 0.75rem 1rem;
      font-size: 0.8rem;
    }
    .tab-content {
      padding: 1.5rem;
    }
    .save-container {
        justify-content: center;
    }
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2>
                <i class="bi bi-gear-wide-connected"></i>
                Quotation Setup
            </h2>
        </div>
    </div>

    <div class="modern-card">
        <div class="tabs-container">
            <button class="tab-btn active" data-tab="company">
                <i class="bi bi-building"></i> Company Info
            </button>
            <button class="tab-btn" data-tab="services">
                <i class="bi bi-list-ul"></i> Services
            </button>
            <button class="tab-btn" data-tab="office">
                <i class="bi bi-geo-alt"></i> Office Detail
            </button>
            <button class="tab-btn" data-tab="contact">
                <i class="bi bi-telephone"></i> Contact
            </button>
            <button class="tab-btn" data-tab="legal">
                <i class="bi bi-file-earmark-text"></i> Legal & Financial
            </button>
            <button class="tab-btn" data-tab="pattern">
                <i class="bi bi-palette"></i> Quotes Pattern
            </button>
        </div>

        <form id="quotationSetupForm">
            @csrf
            
            <!-- Company Info Tab -->
            <div class="tab-content active" id="tab-company">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-modern">Company Name</label>
                            <input type="text" class="form-control-modern" id="company_name" name="company_name" placeholder="Enter company name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-modern">Company Description</label>
                            <textarea class="form-control-modern" id="company_description" name="company_description" rows="4" placeholder="Brief description of the company"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Mission</label>
                            <textarea class="form-control-modern" id="mission" name="mission" rows="3" placeholder="Company mission statement"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Vision</label>
                            <textarea class="form-control-modern" id="vision" name="vision" rows="3" placeholder="Company vision statement"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label-modern">Core Values</label>
                    <input type="text" class="form-control-modern" id="core_values" name="core_values" placeholder="e.g., Innovation, Integrity, Transparency">
                    <small class="text-muted mt-1 d-block">Separate values with commas</small>
                </div>
            </div>

            <!-- Services Tab -->
            <div class="tab-content" id="tab-services">
                <h5 class="mb-3" style="color: #434AFA; font-weight: 600;">Manage Services</h5>
                <p class="text-muted mb-4 small">List the services your company offers. These will be available when creating quotations.</p>
                
                <div id="servicesContainer"></div>
                
                <button type="button" class="btn-modern btn-add mt-2" onclick="addServiceField()">
                    <i class="bi bi-plus-circle"></i> Add New Service
                </button>
            </div>

            <!-- Office Detail Tab -->
            <div class="tab-content" id="tab-office">
                <div class="form-group">
                    <label class="form-label-modern">Office Name / Branch</label>
                    <input type="text" class="form-control-modern" id="office_name" name="office_name" placeholder="e.g., Head Office">
                </div>
                <div class="form-group">
                    <label class="form-label-modern">Address</label>
                    <textarea class="form-control-modern" id="office_address" name="office_address" rows="3" placeholder="Street address, building, etc."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">City</label>
                            <input type="text" class="form-control-modern" id="office_city" name="office_city">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">State</label>
                            <input type="text" class="form-control-modern" id="office_state" name="office_state">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Pincode</label>
                            <input type="text" class="form-control-modern" id="office_pincode" name="office_pincode">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Country</label>
                            <input type="text" class="form-control-modern" id="office_country" name="office_country" value="India">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Tab -->
            <div class="tab-content" id="tab-contact">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern"> <i class="bi bi-telephone me-1"></i> Phone Number</label>
                            <input type="text" class="form-control-modern" id="phone" name="phone" placeholder="+91 9876543210">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern"> <i class="bi bi-envelope me-1"></i> Email Address</label>
                            <input type="email" class="form-control-modern" id="email" name="email" placeholder="contact@company.com">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label-modern"> <i class="bi bi-globe me-1"></i> Website URL</label>
                    <input type="text" class="form-control-modern" id="website" name="website" placeholder="https://www.company.com">
                </div>
            </div>

            <!-- Legal & Financial Tab -->
            <div class="tab-content" id="tab-legal">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">GSTIN</label>
                            <input type="text" class="form-control-modern" id="gstin" name="gstin" placeholder="GST Number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">PAN</label>
                            <input type="text" class="form-control-modern" id="pan" name="pan" placeholder="PAN Number">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label-modern">Bank Details</label>
                    <textarea class="form-control-modern" id="bank_details" name="bank_details" rows="5" placeholder="Bank Name: &#10;Account Number: &#10;IFSC Code: &#10;Branch: "></textarea>
                    <small class="text-muted">Enter complete bank details for payment instructions on quotations.</small>
                </div>
            </div>

            <!-- Quotes Pattern Tab -->
            <div class="tab-content" id="tab-pattern">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Quotation Template</label>
                            <select class="form-control-modern" id="template_name" name="template_name">
                                <option value="modern">Modern Pattern (Recommended)</option>
                                <option value="classic">Classic Pattern</option>
                                <option value="compact">Compact Pattern</option>
                            </select>
                            <small class="text-muted mt-1 d-block">Choose the layout pattern for your quotation PDFs.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Primary Branding Color</label>
                            <div class="d-flex gap-2">
                                <input type="color" class="form-control-modern p-1" id="primary_color" name="primary_color" value="#434AFA" style="width: 50px; height: 45px;">
                                <input type="text" class="form-control-modern" id="primary_color_text" value="#434AFA" onkeyup="$('#primary_color').val(this.value)">
                            </div>
                            <small class="text-muted mt-1 d-block">Used for headers, banners, and primary accents.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-modern">Secondary Branding Color</label>
                            <div class="d-flex gap-2">
                                <input type="color" class="form-control-modern p-1" id="secondary_color" name="secondary_color" value="#FF8C00" style="width: 50px; height: 45px;">
                                <input type="text" class="form-control-modern" id="secondary_color_text" value="#FF8C00" onkeyup="$('#secondary_color').val(this.value)">
                            </div>
                            <small class="text-muted mt-1 d-block">Used for dividers, icons, and highlights.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="save-container">
                <button type="submit" class="btn-modern btn-modern-primary" id="saveBtn">
                    <i class="bi bi-check-circle"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tab switching with animation reset
    $('.tab-btn').on('click', function() {
        const tabId = $(this).data('tab');
        
        $('.tab-btn').removeClass('active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('active');
        const $content = $('#tab-' + tabId);
        $content.addClass('active');
    });
    
    // Load existing settings
    loadSettings();
    
    // Form submission
    $('#quotationSetupForm').on('submit', function(e) {
        e.preventDefault();
        saveSettings();
    });
});

function loadSettings() {
    $.get("{{ route('quotation.setup.fetch') }}")
        .done(function(response) {
            if (response.data) {
                const data = response.data;
                
                // Fill form fields
                $('#company_name').val(data.company_name || '');
                $('#company_description').val(data.company_description || '');
                $('#mission').val(data.mission || '');
                $('#vision').val(data.vision || '');
                $('#core_values').val(data.core_values || '');
                $('#office_name').val(data.office_name || '');
                $('#office_address').val(data.office_address || '');
                $('#office_city').val(data.office_city || '');
                $('#office_state').val(data.office_state || '');
                $('#office_pincode').val(data.office_pincode || '');
                $('#office_country').val(data.office_country || 'India');
                $('#phone').val(data.phone || '');
                $('#email').val(data.email || '');
                $('#website').val(data.website || '');
                $('#gstin').val(data.gstin || '');
                $('#pan').val(data.pan || '');
                $('#bank_details').val(data.bank_details || '');
                
                // New Pattern fields
                $('#template_name').val(data.template_name || 'modern');
                $('#primary_color').val(data.primary_color || '#434AFA');
                $('#primary_color_text').val(data.primary_color || '#434AFA');
                $('#secondary_color').val(data.secondary_color || '#FF8C00');
                $('#secondary_color_text').val(data.secondary_color || '#FF8C00');
                
                // Load services
                if (data.services && Array.isArray(data.services)) {
                    $('#servicesContainer').empty();
                    // If empty array, show one empty field
                    if (data.services.length === 0) {
                        addServiceField();
                    } else {
                        data.services.forEach(function(service) {
                            addServiceField(service);
                        });
                    }
                } else {
                    addServiceField();
                }
            } else {
                addServiceField();
            }
        })
        .fail(function() {
            addServiceField();
            showAlert('error', 'Failed to load settings.');
        });
}

function addServiceField(value = '') {
    const serviceHtml = `
        <div class="service-item">
            <input type="text" class="form-control-modern" name="services[]" value="${value}" placeholder="Enter service name">
            <button type="button" class="btn-remove" onclick="$(this).closest('.service-item').remove()" title="Remove Service">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    $('#servicesContainer').append(serviceHtml);
}

function saveSettings() {
    const $btn = $('#saveBtn');
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    const formData = {
        company_name: $('#company_name').val(),
        company_description: $('#company_description').val(),
        mission: $('#mission').val(),
        vision: $('#vision').val(),
        core_values: $('#core_values').val(),
        services: $('input[name="services[]"]').map(function() {
            return $(this).val();
        }).get().filter(v => v.trim() !== ''),
        office_name: $('#office_name').val(),
        office_address: $('#office_address').val(),
        office_city: $('#office_city').val(),
        office_state: $('#office_state').val(),
        office_pincode: $('#office_pincode').val(),
        office_country: $('#office_country').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        website: $('#website').val(),
        gstin: $('#gstin').val(),
        pan: $('#pan').val(),
        bank_details: $('#bank_details').val(),
        template_name: $('#template_name').val(),
        primary_color: $('#primary_color').val(),
        secondary_color: $('#secondary_color').val()
    };

    $.ajax({
        url: "{{ route('quotation.setup.store') }}",
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('success', response.message || 'Settings saved successfully');
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Failed to save settings';
            showAlert('error', message);
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Settings');
        }
    });
}

function showAlert(type, message) {
    // Remove existing alerts
    $('.alert-dismissible').remove();

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? '<i class="bi bi-check-circle me-2"></i>' : '<i class="bi bi-exclamation-circle me-2"></i>';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            ${icon}${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
<style>
  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
@endpush
