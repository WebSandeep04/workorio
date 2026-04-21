

<?php $__env->startSection('title', 'Create Quotation'); ?>
<?php $__env->startSection('page_title', 'Create Quotation'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body {
        background: #f3f4f6;
    }

    .page-wrapper {
        max-width: 1450px;
        margin: 0 auto;
        padding: 16px;
    }

    /* Top header */
    .page-header {
        background: #434AFA;
        color: #fff;
        padding: 14px 20px;
        border-radius: 6px 6px 0 0;
        font-size: 18px;
        font-weight: 600;
    }

    /* Card */
    .card-box {
        background: #fff;
        border-radius: 0 0 6px 6px;
        padding: 20px;
        border: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .form-control {
        border-radius: 4px;
        font-size: 14px;
        background: #DFDFDF;
    }

    .form-control-modern {
        background: #fff;
    }

    /* Products */
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .btn-add {
        background: #434AFA;
        color: #fff;
        border: none;
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 4px;
    }

    .product-row {
        background: #f0f0f0;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    /* Discount */
    .discount-wrapper {
        display: flex;
        gap: 8px;
        align-items: center;
        max-width: 300px;
    }

    .discount-toggle {
        display: flex;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        overflow: hidden;
    }

    .discount-toggle button {
        border: none;
        background: #fff;
        padding: 6px 10px;
        font-size: 14px;
        width: 50px;
    }

    .discount-toggle .active {
        background: #434afa;
        color: #fff;
    }

    /* Footer buttons */
    .footer-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 45px;
    }

    .totals-preview {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 20px;
        margin-top: 20px;
        max-width: 400px;
        margin-left: auto;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .total-row.grand-total {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 2px solid #434AFA;
        font-weight: 800;
        font-size: 18px;
        color: #434AFA;
    }

    .btn-save {
        background: #434AFA;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 4px;
        font-weight: 600;
        width: 100%;
    }

    .spin {
        animation: spin 1s linear infinite;
        display: inline-block;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .main-row{
        border: 1px solid #E3E3E3;
    }

    .btn-remove-product{
        background: #434AFA;
        border: none;
        border-radius: 3px;
        color: #fff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-wrapper {
            padding: 8px;
        }
        
        .page-header {
            padding: 12px 15px;
            font-size: 16px;
        }

        .card-box {
            padding: 15px;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .main-row {
            margin-left: 0;
            margin-right: 0;
            padding: 0 5px;
        }

        .product-row {
            position: relative;
            padding: 12px;
            padding-top: 45px; /* Space for the absolute delete button */
        }

        .btn-remove-product {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 35px !important;
            height: 35px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
        }

        /* Hide the empty label on mobile for the delete button column */
        .product-row div[class*="col-md-1"] label {
            display: none;
        }

        .discount-wrapper {
            max-width: 100%;
            margin-bottom: 20px;
        }

        .footer-actions {
            flex-direction: column; 
            gap: 12px;
            margin-top: 30px;
        }

        .footer-actions button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
    }

</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-wrapper">

    <div class="page-header">Create Quotation</div>

    <div class="card-box">
        <form id="createQuotationForm">

            
            <div class="section-title">Quotation Details</div>

            <div class="row main-row mb-4 py-3">
                <div class="col-md-6 mb-3">
                    <label>Customer Type</label>
                    <select class="form-control" id="customer_type" name="customer_type">
                        <option value="">Enter...</option>
                        <option value="customer">Customer</option>
                        <option value="prospect">Prospect</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Prospect</label>
                    <select class="form-control" id="customer_id" name="customer_id">
                        <option value="">Enter...</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Subject</label>
                    <input type="text" placeholder="Enter Subject" class="form-control" id="subject" name="subject">
                </div>
            </div>

            
            <div class="products-header">
                <div class="section-title">Products</div>
                <button type="button" class="btn-add" onclick="addProductRow()">+ Add Product</button>
            </div>

            <div id="productsContainer"></div>

            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="section-title">Discount</div>
                    <div class="discount-wrapper mb-3">
                        <input type="number" id="discount" class="form-control" value="0" oninput="updateLiveTotals()">
                        <div class="discount-toggle">
                            <button type="button" class="active" onclick="updateLiveTotals()">%</button>
                            <button type="button" onclick="updateLiveTotals()">₹</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="totals-preview">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="preview-subtotal">₹ 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Discount:</span>
                            <span id="preview-discount">₹ 0.00</span>
                        </div>
                        <div class="total-row" style="font-weight: 600;">
                            <span>Taxable Amount (Basic):</span>
                            <span id="preview-taxable">₹ 0.00</span>
                        </div>
                        <div class="total-row">
                            <span>GST (18%):</span>
                            <span id="preview-gst">₹ 0.00</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Grand Total:</span>
                            <span id="preview-total">₹ 0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="payment_terms_section">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="section-title mb-0">Terms and Conditions</div>
                            <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 12px; border-color: #434AFA; color: #434AFA;" onclick="saveAsDefaultPaymentTerms(this)">
                                <i class="bi bi-save"></i> Set as Default
                            </button>
                        </div>
                        <textarea class="form-control form-control-modern" id="payment_terms" name="payment_terms" rows="6" placeholder="Enter terms separated by new lines..."></textarea>
                        <small class="text-muted">Edits here apply only to this quote. Click "Set as Default" to update your template.</small>
                    </div>
                </div>
            </div>

            <div class="footer-actions">
                <button type="button" id="saveQuotationBtn" class="btn-save" onclick="saveQuotation()">Save Quotation</button>
            </div>

        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- jsPDF for client-side PDF generation -->
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Create quotation page loaded');
    
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Load products and add the first row after data arrives
    loadProducts().then(function() {
        const url = new URL(window.location.href);
        const quoteNo = url.searchParams.get('quote');
        if (quoteNo) {
            prefillFromQuotation(quoteNo).always(function(){
                if ($('.product-select').length === 0) {
                    addProductRow();
                }
            });
        } else {
            addProductRow();
        }
    });
    
    // Handle customer type change
    $('#customer_type').on('change', function() {
        loadCustomerData();
    });

    // Handle discount toggle
    $('.discount-toggle button').on('click', function() {
        $('.discount-toggle button').removeClass('active');
        $(this).addClass('active');
        updateLiveTotals();
    });

    // Initial calculation
    updateLiveTotals();
    
    // Load settings early for pre-filling payment terms
    loadQuotationSettings().done(function() {
        if (quotationSettings && quotationSettings.payment_terms) {
            $('#payment_terms').val(quotationSettings.payment_terms);
        }
    });
});



function saveAsDefaultPaymentTerms(btn) {
    const terms = $('#payment_terms').val();
    const $btn = $(btn);
    const originalContent = $btn.html();
    
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i>');
    
    $.ajax({
        url: "<?php echo e(route('quotation.setup.store')); ?>",
        type: 'POST',
        data: {
            payment_terms: terms
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            showAlert('success', 'Payment terms updated as default');
            // Refresh local quotationSettings
            loadQuotationSettings();
        },
        error: function(xhr) {
            showAlert('error', 'Failed to save default terms');
        },
        complete: function() {
            $btn.prop('disabled', false).html(originalContent);
        }
    });
}

// Global variables to store data
let productsData = [];

// Load products dropdown (returns a promise)
function loadProducts() {
    return $.get('/quotation/products')
        .done(function(response) {
            productsData = response || [];
            populateAllProductSelects();
        })
        .fail(function() {
            console.error('Failed to load products');
            showAlert('error', 'Failed to load products');
        });
}



function populateAllProductSelects() {
    $('.product-select').each(function() {
        const selectEl = $(this);
        const currentVal = selectEl.val();
        selectEl.empty().append('<option value="">Select Product</option>');
        productsData.forEach(function(product) {
            selectEl.append(`<option value="${product.id}">${product.product_name}</option>`);
        });
        if (currentVal) {
            selectEl.val(currentVal);
        }
    });
}

// Add product row
function addProductRow() {
    const rowId = 'product_' + Date.now();
    const productRow = `
        <div class="product-card" id="${rowId}" style="background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
            <div class="row px-3 pt-3">
                <div class="col-md-3 mb-3">
                    <label class="form-label-modern"><i class="bi bi-box"></i> Product</label>
                    <select class="form-control form-control-modern product-select" name="products[${rowId}][product_id]" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="col-md-1 col-6 mb-3">
                    <label class="form-label-modern">Qty</label>
                    <input type="number" class="form-control form-control-modern text-center" name="products[${rowId}][quantity]" step="0.01" value="0">
                </div>
                <div class="col-md-1 col-6 mb-3">
                    <label class="form-label-modern">Unit</label>
                    <input type="text" class="form-control form-control-modern text-center" name="products[${rowId}][unit]" value="Nos">
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <label class="form-label-modern"><i class="bi bi-currency-rupee"></i> Price</label>
                    <input type="number" class="form-control form-control-modern" name="products[${rowId}][price]" step="0.01" value="0" required>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <label class="form-label-modern">Discount</label>
                    <div class="d-flex">
                        <input type="number" class="form-control form-control-modern row-discount" name="products[${rowId}][discount]" step="0.01" value="0" style="border-radius: 4px 0 0 4px;">
                        <select class="form-control form-control-modern row-discount-type" name="products[${rowId}][discount_type]" style="max-width: 45px; border-radius: 0 4px 4px 0; padding: 0 5px; background: #eee; font-size: 12px;">
                            <option value="percentage">%</option>
                            <option value="fixed">₹</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label-modern"><i class="bi bi-calculator"></i> Amount</label>
                    <input type="text" class="form-control form-control-modern row-amount" name="products[${rowId}][amount]" placeholder="0.00" readonly style="background: #f0f1ff; font-weight: 700; color: #434AFA;">
                </div>
                <div class="col-md-1 mb-3">
                    <label class="form-label-modern">&nbsp;</label>
                    <button type="button" class="btn-remove-product w-100" onclick="removeProductRow('${rowId}')" style="height: 38px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div class="row px-3 pb-3">
                <div class="col-md-12">
                    <label class="form-label-modern">Detailed Remark / Product Description</label>
                    <input type="text" class="form-control form-control-modern" name="products[${rowId}][remark]" placeholder="Enter specifics for this item..." style="background: #fff; border: 1px solid #ced4da;">
                </div>
            </div>
        </div>
    `;
    
    $('#productsContainer').append(productRow);
    
    // Populate product dropdown for this row (will be empty until productsData loads)
    const productSelect = $(`#${rowId} .product-select`);
    productSelect.empty().append('<option value="">Select Product</option>');
    if (productsData && productsData.length) {
        productsData.forEach(function(product) {
            productSelect.append(`<option value="${product.id}">${product.product_name}</option>`);
        });
    }
}

// Remove product row
function removeProductRow(rowId) {
    $(`#${rowId}`).remove();
    updateLiveTotals();
}

// Load customer data based on type
function loadCustomerData(selectedId) {
    const customerType = $('#customer_type').val();
    const customerSelect = $('#customer_id');
    
    customerSelect.empty().append('<option value="">Select Customer/Prospect</option>');
    
    if (customerType === 'customer') {
        return $.get('/quotation/customers')
            .done(function(response) {
                response.forEach(function(customer) {
                    const displayName = customer.company_name ? 
                        `${customer.name} (${customer.company_name})` : customer.name;
                    customerSelect.append(`<option value="${customer.id}">${displayName}</option>`);
                });
                if (selectedId) { customerSelect.val(String(selectedId)); }
            })
            .fail(function() {
                console.error('Failed to load customers');
                showAlert('error', 'Failed to load customers');
            });
    } else if (customerType === 'prospect') {
        return $.get('/quotation/prospects')
            .done(function(response) {
                response.forEach(function(prospect) {
                    const displayName = prospect.contact_person ? 
                        `${prospect.prospectus_name} (${prospect.contact_person})` : prospect.prospectus_name;
                    customerSelect.append(`<option value="${prospect.id}">${displayName}</option>`);
                });
                if (selectedId) { customerSelect.val(String(selectedId)); }
            })
            .fail(function() {
                console.error('Failed to load prospects');
                showAlert('error', 'Failed to load prospects');
            });
    }
    return $.Deferred().resolve().promise();
}

// (manual-name UI removed)

// Prefill form from an existing quotation for revision flow
function prefillFromQuotation(quotationNumber){
    // mark header
    try { $('.card-title-modern').append('<span class="badge bg-warning ms-2" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; border: none;">Revision</span>'); } catch(e){}
    return $.get(`<?php echo e(url('/quotation/show')); ?>/${encodeURIComponent(quotationNumber)}`)
        .done(function(resp){
            if(!resp || !resp.quotation){ return; }
            const q = resp.quotation;
            // Basic fields
            if (q.customer_type) $('#customer_type').val(q.customer_type);
            const targetId = (q.customer_type === 'prospect') ? q.prospect_id : q.customer_id;
            loadCustomerData(targetId);
            if (q.data && q.data.subject) $('#subject').val(q.data.subject);

            // Products
            let products = [];
            try { products = (q.data && q.data.products) ? q.data.products : []; } catch(e){}
            if (products && products.length){
                $('#productsContainer').empty();
                products.forEach(function(p){
                    addProductRow();
                    const last = $('#productsContainer .product-card').last();
                    last.find('.product-select').val(String(p.product_id));
                    last.find('input[name*="[quantity]"]').val(p.quantity !== undefined && p.quantity !== null ? p.quantity : 0);
                    last.find('input[name*="[unit]"]').val(p.unit || 'Nos');
                    last.find('input[name*="[price]"]').val(p.price || '');
                    last.find('input[name*="[remark]"]').val(p.remark || '');
                    
                    last.find('input[name*="[discount]"]').val(p.discount || 0);
                    last.find('select[name*="[discount_type]"]').val(p.discount_type || 'percentage');
                    
                    if(p.price) {
                        let baseAmount = parseFloat(p.price) * (parseFloat(p.quantity) || 0);
                        let disc = parseFloat(p.discount || 0);
                        let finalLineAmount = baseAmount;
                        if (p.discount_type === 'percentage') {
                            finalLineAmount = baseAmount - (baseAmount * (disc / 100));
                        } else {
                            finalLineAmount = baseAmount - disc;
                        }
                        last.find('.row-amount').val(round2(finalLineAmount).toFixed(2));
                    }
                });
            }
            
            // Discount
            try {
                const discount = (q.data && q.data.discount) ? q.data.discount : 0;
                $('#discount').val(discount || 0);
            } catch(e){}

            // Payment Terms
            if (q.data && q.data.payment_terms) {
                $('#payment_terms').val(q.data.payment_terms);
            }
            $('#payment_terms_section').show();
        });
}

// Set current date
// (Quotation date handled on backend; no front-end date field needed)

// Generate quotation number
// (Quotation number generated on backend; no front-end field needed)

// Reset form
function resetForm() {
    $('#createQuotationForm')[0].reset();
    $('#customer_id').empty().append('<option value="">Select Customer/Prospect</option>');
    $('#productsContainer').empty();
    $('#discount').val(0);
    addProductRow();
}

// Save quotation function
function saveQuotation() {
    // Collect products data
    const products = [];
    $('.product-select').each(function() {
        const row = $(this).closest('.product-card');
        const productId = $(this).val();
        const price = row.find('input[name*="[price]"]').val();
        const quantity = row.find('input[name*="[quantity]"]').val();
        const unit = row.find('input[name*="[unit]"]').val();
        const remark = row.find('input[name*="[remark]"]').val();
        
        const discount = row.find('input[name*="[discount]"]').val();
        const discountType = row.find('select[name*="[discount_type]"]').val();
        
        if (productId) {
            products.push({
                product_id: productId,
                price: (price !== "" && price !== null) ? parseFloat(price) : 0,
                quantity: (quantity !== "" && quantity !== null) ? parseFloat(quantity) : 0,
                unit: unit || 'Nos',
                remark: remark || '',
                discount: (discount !== "" && discount !== null) ? parseFloat(discount) : 0,
                discount_type: discountType || 'percentage'
            });
        }
    });
    
    const formData = {
        customer_type: $('#customer_type').val(),
        customer_id: $('#customer_id').val(),
        subject: $('#subject').val(),
        products: products,
        discount: parseFloat($('#discount').val() || 0),
        payment_terms: $('#payment_terms').val(),
        show_payment_terms: true
    };
    
    console.log('Saving quotation:', formData);
    
    // UI Loading state
    const $btn = $('#saveQuotationBtn');
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    const restoreBtn = () => {
        $btn.prop('disabled', false).html(originalText);
    };

    // Validate required fields
    if (!formData.customer_type || !formData.customer_id) {
        showAlert('error', 'Please select customer type and customer/prospect.');
        restoreBtn();
        return;
    }
    
    if (products.length === 0) {
        showAlert('error', 'Please add at least one product.');
        restoreBtn();
        return;
    }
    
    // Validate all products have required fields
    const invalidProducts = products.filter(p => p.product_id === "" || p.product_id === null || p.price === "" || p.price === null || isNaN(p.price));
    if (invalidProducts.length > 0) {
        showAlert('error', 'Please fill in all product details (product and price are required).');
        restoreBtn();
        return;
    }
    
    // For revision flow: use existing quotation number passed in query string
    const url = new URL(window.location.href);
    const existingQuote = url.searchParams.get('quote');
    if (existingQuote) {
        formData.quotation_number = existingQuote;
        generateQuotationPdfAndUpload(formData);
        return;
    }

    // Otherwise ask backend to generate a fresh number
    $.get("<?php echo e(route('quotation.generate-number')); ?>", {
        customer_type: $('#customer_type').val(),
        customer_id: $('#customer_id').val()
    })
        .done(function(resp){
            const qno = (resp && resp.quotation_number) ? resp.quotation_number : null;
            if (!qno) { 
                showAlert('error','Failed to get quotation number'); 
                restoreBtn();
                return; 
            }
            formData.quotation_number = qno;
            generateQuotationPdfAndUpload(formData);
        })
        .fail(function(){ 
            showAlert('error','Failed to generate quotation number'); 
            restoreBtn();
        });
}

// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success-modern' : 'alert-error-modern';
    const icon = type === 'success' ? '<i class="bi bi-check-circle me-2"></i>' : '<i class="bi bi-exclamation-circle me-2"></i>';
    const alertHtml = `
        <div class="alert-modern ${alertClass} alert-dismissible fade show" role="alert">
            ${icon}${message}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
        </div>
    `;
    
    // Remove existing alerts
    $('#alertContainer').empty();
    
    // Add new alert
    $('#alertContainer').html(alertHtml);
    
    // Auto-hide success alerts after 3 seconds
    if (type === 'success') {
        setTimeout(function() {
            $('#alertContainer .alert-modern').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
}

// Global variable to store quotation settings
let quotationSettings = null;

// Fetch quotation settings
function loadQuotationSettings() {
    return $.get("<?php echo e(route('quotation.setup.get')); ?>")
        .done(function(response) {
            if (response.data) {
                quotationSettings = response.data;
            }
        })
        .fail(function() {
            console.warn('Failed to load quotation settings, using defaults');
        });
}

// Generate PDF using jsPDF
function generateQuotationPdfAndUpload(data) {
    // Load settings first, then generate PDF
    loadQuotationSettings().always(function() {
        generatePDF(data);
    });
}

function generatePDF(data) {
    const totals = calculateTotalAmount(data.products || [], data.discount || 0);
    const payload = {
        quotation_number: data.quotation_number,
        customer_type: data.customer_type,
        customer_id: data.customer_id,
        subject: data.subject,
        products: data.products,
        discount: totals.discountAmount,
        total_amount: totals.total,
        status: 'Draft'
    };

    const $btn = $('#saveQuotationBtn');
    const originalText = $btn.data('original-text') || $btn.html();
    if(!$btn.data('original-text')) $btn.data('original-text', originalText);
    
    $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

    $.ajax({
        url: "<?php echo e(route('quotation.store')); ?>",
        type: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(r){
            showAlert('success','Quotation saved successfully');
            if (r && r.data && r.data.file_url) {
                // Open the server-generated PDF
                window.open(r.data.file_url, '_blank');
                // Redirect back to list
                setTimeout(() => {
                    window.location.href = "<?php echo e(route('quotation')); ?>";
                }, 1500);
            }
        },
        error: function(xhr){
            console.error('Save quotation failed', xhr.responseText);
            showAlert('error','Failed to save quotation');
            $btn.prop('disabled', false).html($btn.data('original-text') || 'Save Quotation');
        }
    });
}

function calculateTotalAmount(products, discountInput = 0){
    let subtotal = 0;
    products.forEach(p => {
        const price = Number(p.price || 0);
        const qty = Number(p.quantity !== undefined && p.quantity !== null ? p.quantity : 0);
        const disc = Number(p.discount || 0);
        const discType = p.discount_type || 'percentage';
        
        let rowBase = round2(price * qty);
        let rowLineAmount = rowBase;
        
        if (discType === 'percentage') {
            rowLineAmount = rowBase - (rowBase * (disc/100));
        } else {
            rowLineAmount = rowBase - disc;
        }
        
        subtotal += round2(rowLineAmount);
    });
    
    const isPercentage = $('.discount-toggle button:first-child').hasClass('active');
    let discountAmount = isPercentage ? round2(subtotal * (Number(discountInput) / 100)) : Number(discountInput || 0);

    const taxable = Math.max(0, round2(subtotal - discountAmount));
    const gst = round2(taxable * 0.18);
    const total = round2(taxable + gst);

    return {
        subtotal,
        discountAmount,
        taxable,
        gst,
        total
    };
}

function updateLiveTotals() {
    const products = [];
    $('.product-card').each(function() {
        const row = $(this);
        const price = row.find('input[name*="[price]"]').val();
        const quantity = row.find('input[name*="[quantity]"]').val();
        const discount = row.find('input[name*="[discount]"]').val();
        const discountType = row.find('select[name*="[discount_type]"]').val();
        
        const rowAmountInput = row.find('.row-amount');
        const productId = row.find('.product-select').val();

        if (productId) {
            const parsedPrice = (price !== "" && price !== null) ? parseFloat(price) : 0;
            const parsedQuantity = (quantity !== "" && quantity !== null) ? parseFloat(quantity) : 0;
            const parsedDiscount = (discount !== "" && discount !== null) ? parseFloat(discount) : 0;
            
            let rowBase = round2(parsedPrice * parsedQuantity);
            let rowFinal = rowBase;
            if (discountType === 'percentage') {
                rowFinal = rowBase - (rowBase * (parsedDiscount / 100));
            } else {
                rowFinal = rowBase - parsedDiscount;
            }
            
            rowAmountInput.val(round2(rowFinal).toFixed(2));
            
            products.push({
                price: parsedPrice,
                quantity: parsedQuantity,
                discount: parsedDiscount,
                discount_type: discountType
            });
        } else {
            rowAmountInput.val('0.00');
        }
    });

    let subtotal = 0;
    products.forEach(p => {
        let rowBase = round2(p.price * p.quantity);
        if (p.discount_type === 'percentage') {
            subtotal += round2(rowBase - (rowBase * (p.discount / 100)));
        } else {
            subtotal += round2(rowBase - p.discount);
        }
    });

    const discountVal = parseFloat($('#discount').val() || 0);
    const totals = calculateTotalAmount(products, discountVal);

    $('#preview-subtotal').text('₹ ' + formatAmount(totals.subtotal, 2));
    $('#preview-discount').text('₹ ' + formatAmount(totals.discountAmount, 2));
    $('#preview-taxable').text('₹ ' + formatAmount(totals.taxable, 2));
    $('#preview-gst').text('₹ ' + formatAmount(totals.gst, 2));
    $('#preview-total').text('₹ ' + formatAmount(totals.total, 2));
}

// Add event listeners for inputs
$(document).on('input', 'input[name*="[price]"], input[name*="[quantity]"], input[name*="[discount]"]', updateLiveTotals);
$(document).on('change', 'select[name*="[discount_type]"]', updateLiveTotals);
$(document).on('click', '.btn-add', function() {
    setTimeout(updateLiveTotals, 50);
});

function formatCurrency(value) {
    const num = Math.round(Number(value || 0));
    // Manually format number with commas to avoid locale-specific rendering issues
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function round2(n) { return Math.round((n + Number.EPSILON) * 100) / 100; }

// formatAmount: default 0 decimals for whole amounts; for taxes optionally 2
function formatAmount(value, fixedDecimals) {
    const num = Number(value || 0);
    if (typeof fixedDecimals === 'number') {
        return num.toFixed(fixedDecimals);
    }
    // Use manual formatting to avoid superscript issues
    const rounded = Math.round(num);
    return rounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Convert integer to words (Indian numbering system)
function numberToWordsIndian(num) {
    num = Math.floor(Math.abs(Number(num)));
    if (num === 0) return 'Zero Only';
    const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    function twoDigits(n) {
        if (n < 20) return ones[n];
        const t = Math.floor(n / 10);
        const o = n % 10;
        return tens[t] + (o ? ' ' + ones[o] : '');
    }

    function threeDigits(n) {
        const h = Math.floor(n / 100);
        const rest = n % 100;
        let s = '';
        if (h) s += ones[h] + ' Hundred';
        if (rest) s += (s ? ' ' : '') + twoDigits(rest);
        return s;
    }

    const parts = [];
    const crore = Math.floor(num / 10000000);
    num %= 10000000;
    const lakh = Math.floor(num / 100000);
    num %= 100000;
    const thousand = Math.floor(num / 1000);
    num %= 1000;
    const hundred = num;

    if (crore) parts.push(twoDigits(crore) + ' Crore');
    if (lakh) parts.push(twoDigits(lakh) + ' Lakh');
    if (thousand) parts.push(twoDigits(thousand) + ' Thousand');
    if (hundred) parts.push(threeDigits(hundred));

    return 'Rupees ' + parts.join(' ') + ' Only';
}

function capitalize(s) { return (s || '').charAt(0).toUpperCase() + (s || '').slice(1); }

function generateQuoteId() {
    const d = new Date();
    const ymd = d.toISOString().slice(0,10).replace(/-/g, '');
    const hms = String(d.getHours()).padStart(2,'0') + String(d.getMinutes()).padStart(2,'0') + String(d.getSeconds()).padStart(2,'0');
    return `QUOTE-${ymd}-${hms}`;
}

function getProductName(productId) {
    const p = (productsData || []).find(x => String(x.id) === String(productId));
    return p ? p.product_name : String(productId);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/create.blade.php ENDPATH**/ ?>