

<?php $__env->startSection('title', 'Create Quotation'); ?>
<?php $__env->startSection('page_title', 'Create Quotation'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body {
        background: #f3f4f6;
    }

    .page-wrapper {
        max-width: 1100px;
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



    .btn-save {
        background: #434AFA;
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 4px;
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
                <div class="col-md-6 mb-3">
                    <label>Project Timeline</label>
                    <input type="text" placeholder="Enter Project Timeline" class="form-control" id="project_timeline">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Payment Terms</label>
                    <select class="form-control" id="payment_term_id"></select>
                </div>
            </div>

            
            <div class="products-header">
                <div class="section-title">Products</div>
                <button type="button" class="btn-add" onclick="addProductRow()">+ Add Product</button>
            </div>

            <div id="productsContainer"></div>

            
            <div class="section-title mt-3">% Discount</div>
            <div class="discount-wrapper">
                <input type="number" id="discount" class="form-control" value="0">
                <div class="discount-toggle">
                    <button type="button" class="active">%</button>
                    <button type="button">₹</button>
                </div>
            </div>

            
            <div class="footer-actions">
                <button type="button" class="btn-save" onclick="saveQuotation()">Save Quotation</button>
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
    
    // Load products and payment terms then add the first row after data arrives
    Promise.all([
        loadProducts(),
        loadPaymentTerms()
    ]).then(function() {
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
});

// Global variables to store data
let productsData = [];
let paymentTermsData = [];

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

// Load payment terms dropdown (returns a promise)
function loadPaymentTerms() {
    // Align with how customers/products are fetched under /quotation/* endpoints
    return $.get('/quotation/payment-terms')
        .done(function(response) {
            paymentTermsData = response || [];
            populatePaymentTermsSelect();
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to load payment terms:', xhr.responseText, status, error);
            showAlert('error', 'Failed to load payment terms: ' + error);
        });
}

function populatePaymentTermsSelect() {
    const selectEl = $('#payment_term_id');
    const currentVal = selectEl.val();
    selectEl.empty().append('<option value="">Select Payment Terms</option>');
    
    if (paymentTermsData && paymentTermsData.length > 0) {
        paymentTermsData.forEach(function(term) {
            if (term.is_active) {
                selectEl.append(`<option value="${term.id}">${term.name}</option>`);
            }
        });
    }
    
    if (currentVal) {
        selectEl.val(currentVal);
    }
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
        <div class="product-card" id="${rowId}">
            <div class="row product-row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label-modern">
                            <i class="bi bi-box"></i>
                            Product
                        </label>
                        <select class="form-control form-control-modern product-select" name="products[${rowId}][product_id]" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label-modern">
                            <i class="bi bi-currency-rupee"></i>
                            Price
                        </label>
                        <input type="number" class="form-control form-control-modern" name="products[${rowId}][price]" step="0.01" placeholder="Enter price" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label-modern">
                            <i class="bi bi-chat-left-text"></i>
                            Remark
                        </label>
                        <input type="text" class="form-control form-control-modern" name="products[${rowId}][remark]" placeholder="Enter remark">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="mb-3">
                        <label class="form-label-modern">&nbsp;</label>
                        <button type="button" class="btn-remove-product w-100" onclick="removeProductRow('${rowId}')" title="Remove Product">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
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
            loadCustomerData(q.customer_id);
            if (q.project_timeline) $('#project_timeline').val(q.project_timeline);
            if (q.payment_term_id) $('#payment_term_id').val(String(q.payment_term_id));

            // Products
            let products = [];
            try { products = (q.data && q.data.products) ? q.data.products : []; } catch(e){}
            if (products && products.length){
                $('#productsContainer').empty();
                products.forEach(function(p){
                    addProductRow();
                    const last = $('#productsContainer .product-card').last();
                    last.find('.product-select').val(String(p.product_id));
                    last.find('input[name*="[price]"]').val(p.price || '');
                    last.find('input[name*="[remark]"]').val(p.remark || '');
                });
            }
            
            // Discount
            try {
                const discount = (q.data && q.data.discount) ? q.data.discount : 0;
                $('#discount').val(discount || 0);
            } catch(e){}
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
        const remark = row.find('input[name*="[remark]"]').val();
        
        if (productId && price) {
            products.push({
                product_id: productId,
                price: parseFloat(price),
                remark: remark || ''
            });
        }
    });
    
    const formData = {
        customer_type: $('#customer_type').val(),
        customer_id: $('#customer_id').val(),
        project_timeline: $('#project_timeline').val(),
        payment_term_id: $('#payment_term_id').val(),
        products: products,
        discount: parseFloat($('#discount').val() || 0)
    };
    
    console.log('Saving quotation:', formData);
    
    // Validate required fields
    if (!formData.customer_type || !formData.customer_id) {
        showAlert('error', 'Please select customer type and customer/prospect.');
        return;
    }
    
    if (!formData.project_timeline) {
        showAlert('error', 'Please enter project timeline.');
        return;
    }
    
    if (!formData.payment_term_id) {
        showAlert('error', 'Please select payment terms.');
        return;
    }
    
    if (products.length === 0) {
        showAlert('error', 'Please add at least one product.');
        return;
    }
    
    // Validate all products have required fields
    const invalidProducts = products.filter(p => !p.product_id || !p.price);
    if (invalidProducts.length > 0) {
        showAlert('error', 'Please fill in all product details (product and price are required).');
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
    $.get("<?php echo e(route('quotation.generate-number')); ?>")
        .done(function(resp){
            const qno = (resp && resp.quotation_number) ? resp.quotation_number : null;
            if (!qno) { showAlert('error','Failed to get quotation number'); return; }
            formData.quotation_number = qno;
            generateQuotationPdfAndUpload(formData);
        })
        .fail(function(){ showAlert('error','Failed to generate quotation number'); });
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
    const payload = {
        quotation_number: data.quotation_number,
        customer_type: data.customer_type,
        customer_id: data.customer_id,
        payment_term_id: data.payment_term_id,
        project_timeline: data.project_timeline,
        products: data.products,
        discount: data.discount || 0,
        total_amount: calculateTotalAmount(data.products || [], data.discount || 0),
        status: 'Draft'
    };

    const $btn = $('#saveQuotationBtn');
    const originalText = $btn.html();
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
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

function calculateTotalAmount(products, discount = 0){
    let total = 0;
    products.forEach(p => {
        const price = Number(p.price || 0);
        const cgst = round2(price * 0.09);
        const sgst = round2(price * 0.09);
        total += round2(price + cgst + sgst);
    });
    // Subtract discount
    const discountAmount = Number(discount || 0);
    return Math.max(0, round2(total - discountAmount));
}

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

function getPaymentTermsDetails(paymentTermId) {
    const term = (paymentTermsData || []).find(x => String(x.id) === String(paymentTermId));
    return term || null;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/create.blade.php ENDPATH**/ ?>