@extends('layouts.app')

@section('title', 'Manage Category Fields')
@section('page_title', 'Configuration: ' . $category->name)

@push('styles')
<style>
  .container-fluid {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .config-header {
      background: #434AFA;
      background: linear-gradient(135deg, #434AFA 0%, #3538d4 100%);
      color: white;
      padding: 1.5rem 2rem;
      border-radius: 12px;
      margin-bottom: 2rem;
      box-shadow: 0 4px 20px rgba(67, 74, 250, 0.2);
  }

  .field-list-container {
      background: transparent;
  }
  
  .field-card {
      background: white;
      border: 1px solid #edf2f4;
      border-radius: 10px;
      padding: 1.25rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
      position: relative;
  }
  
  .field-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.06);
      border-color: #e2e8f0;
  }
  
  .field-card .remove-btn {
      position: absolute;
      top: -10px;
      right: -10px;
      width: 28px;
      height: 28px;
      background: #fff;
      border: 1px solid #fee2e2;
      color: #ef4444;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(239, 68, 68, 0.15);
      transition: all 0.2s;
  }
  
  .field-card .remove-btn:hover {
      background: #ef4444;
      color: white;
      transform: scale(1.1);
  }
  
  .form-label-modern {
    color: #64748b;
    font-weight: 600;
    margin-bottom: 0.4rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .form-control-modern {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    color: #1e293b;
    background: #f8fafc;
    transition: all 0.2s ease;
  }
  
  .form-control-modern:focus {
    background: white;
    border-color: #434AFA;
    box-shadow: 0 0 0 3px rgba(67, 74, 250, 0.1);
    outline: none;
  }
  
  .field-type-select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 16px 12px;
  }

  .btn-add-field {
      background: white;
      border: 2px dashed #cbd5e1;
      color: #64748b;
      width: 100%;
      padding: 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.2s;
      margin-top: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
  }
  
  .btn-add-field:hover {
      border-color: #434AFA;
      color: #434AFA;
      background: #f8faff;
  }
  
  .floating-save-bar {
      position: fixed;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      background: white;
      padding: 1rem 2rem;
      border-radius: 50px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 1rem;
      z-index: 100;
      animation: slideUp 0.3s ease-out;
      border: 1px solid #f1f5f9;
  }
  
  @keyframes slideUp {
      from { transform: translate(-50%, 100%); opacity: 0; }
      to { transform: translate(-50%, 0); opacity: 1; }
  }

  .btn-save {
      background: #434AFA;
      color: white;
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 30px;
      font-weight: 600;
      transition: all 0.2s;
      box-shadow: 0 4px 12px rgba(67, 74, 250, 0.3);
  }
  
  .btn-save:hover {
      background: #3538d4;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(67, 74, 250, 0.4);
      color: white;
  }
  
  .empty-state-card {
      text-align: center;
      padding: 4rem 2rem;
      background: white;
      border-radius: 12px;
      border: 1px solid #f1f5f9;
      color: #94a3b8;
  }
  
  .back-link {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    transition: color 0.2s;
  }
  
  .back-link:hover {
      color: white;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- <div class="config-header">
        <a href="{{ route('asset-category.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Asset Categories
        </a>
        <h2 class="mb-1 fw-bold">{{ $category->name }}</h2>
        <p class="mb-0 opacity-75">Configure custom fields for this asset category. These fields will appear when adding assets.</p>
    </div> -->

    <form id="fieldsForm">
        @csrf
        <div id="fieldsContainer" class="field-list-container">
            <!-- Fields will be mounted here -->
            <div id="emptyState" class="empty-state-card" style="display:none;">
                <i class="bi bi-ui-checks-grid" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                <h5 class="fw-bold text-dark">No Custom Fields Yet</h5>
                <p class="mb-0">Start by adding fields like "Warranty Expiry" or "Processor Type" to customize this category.</p>
            </div>
        </div>
        
        <button type="button" class="btn-add-field" id="addFieldBtn">
            <i class="bi bi-plus-circle-fill"></i> Add Custom Field
        </button>
        
        <div class="floating-save-bar">
            <span class="text-muted small me-2 d-none d-md-inline">Make sure to save your changes</span>
            <button type="submit" class="btn-save">
                <i class="bi bi-check-lg me-1"></i> Save Configuration
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const categoryId = {{ $category->id }};
    const $container = $('#fieldsContainer');
    const $emptyState = $('#emptyState');
    
    // Initial fetch to populate fields
    $.get(`/asset-category/${categoryId}`, function(data) {
        $container.empty();
        $container.append($emptyState);
        
        if(data.fields && data.fields.length > 0) {
            data.fields.forEach(field => addFieldRow(field));
        } else {
            $emptyState.show();
        }
    });

    function toggleEmptyState() {
        if($('.field-card').length === 0) {
            $emptyState.show();
        } else {
            $emptyState.hide();
        }
    }

    function addFieldRow(field = null) {
        $emptyState.hide();
        const id = field ? field.id : '';
        const name = field ? field.name : '';
        const type = field ? field.type : 'text';
        
        let options = '';
        if(field && field.options) {
            if(Array.isArray(field.options)) options = field.options.join(', ');
            else if(typeof field.options === 'string') {
                try {
                    options = JSON.parse(field.options).join(', ');
                } catch(e) { options = field.options; }
            }
        }

        const rowHtml = `
            <div class="field-card">
                <input type="hidden" class="field-id" value="${id}">
                <button type="button" class="remove-btn" title="Remove Field"><i class="bi bi-x"></i></button>
                
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label-modern">Field Name</label>
                        <div class="input-group">
                             <span class="input-group-text bg-light border-end-0 border-light"><i class="bi bi-input-cursor-text text-muted"></i></span>
                             <input type="text" class="form-control form-control-modern border-start-0 ps-0 field-name" value="${name}" required placeholder="e.g. Serial Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-modern">Input Type</label>
                        <select class="form-select form-control-modern field-type field-type-select">
                            <option value="text" ${type === 'text' ? 'selected' : ''}>Text Input</option>
                            <option value="dropdown" ${type === 'dropdown' ? 'selected' : ''}>Dropdown Menu</option>
                            <option value="date" ${type === 'date' ? 'selected' : ''}>Date Picker</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-modern">Dropdown Options <span class="text-muted fw-normal text-lowercase ms-1" style="font-size: 0.7rem; font-style: italic;">(comma separated)</span></label>
                        <input type="text" class="form-control form-control-modern field-options" 
                               value="${options}" 
                               placeholder="High, Medium, Low" 
                               ${type !== 'dropdown' ? 'disabled style="background: #f1f5f9; cursor: not-allowed; opacity: 0.6;"' : ''}>
                    </div>
                </div>
            </div>
        `;
        
        const $row = $(rowHtml).hide();
        $container.append($row);
        $row.fadeIn(300);
    }

    $('#addFieldBtn').click(function() {
        addFieldRow();
        $('html, body').animate({ scrollTop: $(document).height() }, 500);
    });

    $(document).on('click', '.remove-btn', function() {
        const $card = $(this).closest('.field-card');
        $card.fadeOut(200, function() { 
            $(this).remove(); 
            toggleEmptyState();
        });
    });

    $(document).on('change', '.field-type', function() {
        const $row = $(this).closest('.field-card');
        const $options = $row.find('.field-options');
        if($(this).val() === 'dropdown') {
            $options.prop('disabled', false).css({'background': '#f8fafc', 'cursor': 'text', 'opacity': '1'}).focus();
        } else {
            $options.prop('disabled', true).css({'background': '#f1f5f9', 'cursor': 'not-allowed', 'opacity': '0.6'});
        }
    });

    $('#fieldsForm').submit(function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        const fields = [];
        $('.field-card').each(function() {
            const $row = $(this);
            const type = $row.find('.field-type').val();
            const field = {
                id: $row.find('.field-id').val() || null,
                name: $row.find('.field-name').val(),
                type: type
            };
            
            if(type === 'dropdown') {
                const opts = $row.find('.field-options').val();
                field.options = opts ? opts.split(',').map(s => s.trim()).filter(s => s) : [];
            } else {
                field.options = null;
            }
            
            fields.push(field);
        });
        
        const categoryName = "{{ $category->name }}";

        $.ajax({
            url: `/asset-category/${categoryId}`,
            type: 'PUT',
            data: {
                _token: '{{ csrf_token() }}',
                name: categoryName,
                fields: fields
            },
            success: function() {
                // Show floating success toast or just alert
                const toast = document.createElement("div");
                toast.className = "position-fixed top-0 end-0 p-3";
                toast.style.zIndex = "1050";
                toast.innerHTML = `
                    <div class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle-fill me-2"></i> Configuration saved successfully!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            },
            error: function(xhr) {
                alert('Error saving fields: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
