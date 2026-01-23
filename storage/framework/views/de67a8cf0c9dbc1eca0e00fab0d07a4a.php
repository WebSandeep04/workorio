

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
    const jsPDFLib = window.jspdf ? window.jspdf.jsPDF : window.jsPDF;
    if (!jsPDFLib) {
        console.error('jsPDF not loaded');
        showAlert('error', 'PDF library failed to load. Please check your internet connection.');
        return;
    }
    const doc = new jsPDFLib({ unit: 'pt', format: 'a4' });
    doc.setFont('helvetica', 'normal');
    const padding = 28; // tighter side padding
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();

    // Helper: draw a subtle page background (very light gray)
    const drawPageBackground = () => {
        doc.setFillColor(248, 250, 252); // light neutral background
        doc.rect(0, 0, pageWidth, pageHeight, 'F');
    };

    // Paint background for first page
    drawPageBackground();

    const quoteId = generateQuoteId();
    const today = new Date();

    // Get ALL company information from settings table - no hardcoded defaults
    const companyName = quotationSettings?.company_name || '';
    const companyDescription = quotationSettings?.company_description || '';
    const mission = quotationSettings?.mission || '';
    const vision = quotationSettings?.vision || '';
    const coreValues = quotationSettings?.core_values || '';
    const services = quotationSettings?.services || [];
    const officeName = quotationSettings?.office_name || '';
    const officeAddress = quotationSettings?.office_address || '';
    const officeCity = quotationSettings?.office_city || '';
    const officeState = quotationSettings?.office_state || '';
    const officePincode = quotationSettings?.office_pincode || '';
    const officeCountry = quotationSettings?.office_country || '';
    const phone = quotationSettings?.phone || '';
    const email = quotationSettings?.email || '';
    const website = quotationSettings?.website || '';
    const gstin = quotationSettings?.gstin || '';
    const pan = quotationSettings?.pan || '';
    const bankDetails = quotationSettings?.bank_details || '';

    // COVER PAGE: About the Company
    try {
        const coverTitle = 'About the Company';
        // Use company description from settings, split into paragraphs
        const aboutLines = companyDescription ? companyDescription.split('\n').filter(l => l.trim()) : [];

        // Mission & Vision section from settings - only show if data exists
        const missionVisionLines = [];
        if (mission || vision || coreValues) {
            if (mission || vision) {
                missionVisionLines.push('Mission & Vision');
                if (mission) {
                    missionVisionLines.push(mission);
                }
                if (vision) {
                    missionVisionLines.push('');
                    missionVisionLines.push('Vision:');
                    missionVisionLines.push(vision);
                }
            }
            if (coreValues) {
                if (missionVisionLines.length > 0) missionVisionLines.push('');
                missionVisionLines.push('Core Values');
                missionVisionLines.push(coreValues);
            }
        }

        // Blue header banner (consistent with Services page)
        doc.setFillColor(25, 118, 210);
        const aboutHeaderH = 90;
        doc.rect(0, 0, pageWidth, aboutHeaderH, 'F');
        doc.setFontSize(18);
        doc.setTextColor(255, 255, 255);
        doc.text(coverTitle, padding, aboutHeaderH - 30, { align: 'left' });

        // Body text - centered
        doc.setFontSize(12);
        doc.setTextColor(15, 23, 42);
        const maxWidth = pageWidth - padding * 2;
        const lineHeight = 18; // px
        const paraGap = 12;
        
        // Render About section
        const wrappedParas = aboutLines.map(p => doc.splitTextToSize(p, maxWidth));
        const contentLines = wrappedParas.reduce((acc, arr) => acc + arr.length, 0);
        const contentHeight = contentLines * lineHeight + (wrappedParas.length - 1) * paraGap;
        let yCursor = Math.max(aboutHeaderH + 20, (pageHeight - contentHeight) / 2);
        wrappedParas.forEach((wrapped, idx) => {
            wrapped.forEach((ln) => {
                doc.text(ln, pageWidth / 2, yCursor, { align: 'center' });
                yCursor += lineHeight;
            });
            if (idx < wrappedParas.length - 1) yCursor += paraGap;
        });

        // Add Mission & Vision section - centered (only if data exists)
        if (missionVisionLines.length > 0) {
            yCursor += 30; // Add some space before mission/vision
            const missionWrappedParas = missionVisionLines.map(p => doc.splitTextToSize(p, maxWidth));
        missionWrappedParas.forEach((wrapped, idx) => {
            const line = missionVisionLines[idx];
            if (line === 'Mission & Vision' || line === 'Core Values' || line === 'Vision:') {
                // Bold headers
                doc.setFont(undefined, 'bold');
                doc.setFontSize(14);
                doc.setTextColor(25, 118, 210);
            } else if (line === '') {
                // Empty line for spacing
                yCursor += lineHeight;
                return;
            } else {
                // Regular text
                doc.setFont(undefined, 'normal');
                doc.setFontSize(12);
                doc.setTextColor(15, 23, 42);
            }
            
            wrapped.forEach((ln) => {
                doc.text(ln, pageWidth / 2, yCursor, { align: 'center' });
                yCursor += lineHeight;
            });
            if (idx < missionWrappedParas.length - 1) yCursor += paraGap;
        });
        }

        // Add second page for Services (only if services exist in table)
        const servicesList = (services && services.length > 0) ? services : [];
        if (servicesList.length > 0) {
            doc.addPage();
            drawPageBackground();
            try {
                const servicesTitle = 'Our Services';
            const iconMap = {
                // 'ERP Development & Implementation': 'ERP',
                // 'Custom Software Development': 'DEV',
                // 'Mobile App Development': 'APP',
                // 'SEO / SMO (Search & Social Optimization)': 'SEO',
                // 'Website Development': 'WEB',
                // 'IT Infrastructure Services': 'IT',
                // 'Fractional CIO Services': 'CIO',
                // 'Staff Augmentation': 'STA',
                // 'Testing & QA': 'QA',
                // 'Cyber Security': 'SEC'
            };

            // Title banner
            doc.setFillColor(25, 118, 210);
            const servicesHeaderH = 90;
            doc.rect(0, 0, pageWidth, servicesHeaderH, 'F');
            doc.setFontSize(18);
            doc.setTextColor(255, 255, 255);
            doc.text(servicesTitle, padding, servicesHeaderH - 34);

            // Cards grid (2 columns, centered vertically)
            const gap = 24; // increased padding between cards/columns
            const colWidth = (pageWidth - padding * 2 - gap) / 2;
            const rowHeight = 48;
            let sx = padding;
            // compute rows to center vertically
            const rows = Math.ceil(servicesList.length / 2);
            const servicesBlockH = rows * rowHeight + (rows - 1) * gap;
            let sy = Math.max(servicesHeaderH + 12, (pageHeight - servicesBlockH) / 2);
            let colorIdx = 0;
            const palette = [
                [59, 130, 246], // blue
                [16, 185, 129], // emerald
                [245, 158, 11], // amber
                [244, 63, 94],  // rose
                [139, 92, 246], // violet
                [34, 197, 94],  // green
                [2, 132, 199],  // sky
                [234, 88, 12],  // orange
                [99, 102, 241], // indigo
                [20, 184, 166]  // teal
            ];

            doc.setFontSize(12);
            doc.setTextColor(23, 23, 23);
            servicesList.forEach((svc, i) => {
                const [r, g, b] = palette[colorIdx % palette.length];
                colorIdx++;
                // shadow
                doc.setFillColor(226, 232, 240);
                doc.roundedRect(sx + 1, sy + 1, colWidth, rowHeight, 6, 6, 'F');
                // card
                doc.setFillColor(255, 255, 255);
                doc.roundedRect(sx, sy, colWidth, rowHeight, 6, 6, 'F');
                // accent bar
                doc.setFillColor(r, g, b);
                doc.roundedRect(sx, sy, 8, rowHeight, 6, 6, 'F');
                // icon bubble
                doc.setFillColor(r, g, b);
                doc.circle(sx + 16, sy + rowHeight / 2, 9, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFont(undefined, 'bold');
                doc.setFontSize(8);
                const code = iconMap[svc] || '•';
                doc.text(code, sx + 16, sy + rowHeight / 2 + 3, { align: 'center' });
                // text
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                doc.setTextColor(31, 41, 55);
                const txt = doc.splitTextToSize(svc, colWidth - 40);
                doc.text(txt, sx + 34, sy + rowHeight / 2 + 4, { align: 'left', baseline: 'middle' });

                // move grid position
                if (sx + colWidth + gap + colWidth <= pageWidth - padding) {
                    sx += colWidth + gap;
                } else {
                    sx = padding;
                    sy += rowHeight + gap;
                }
            });
            } catch (e2) {
                console.warn('Services page render error', e2);
            }
        }
        
        // proceed to next page for quotation
        doc.addPage();
        drawPageBackground();
    } catch (e) {
        // If any error while rendering cover, continue with quotation page
        console.warn('Cover page render error', e);
    }

    // No header for quotation page - content will be centered

    // Products table (columns: Product, Remark, Price, CGST 9%, SGST 9%, Total)
    const tableBody = (data.products || []).map((p) => {
        const price = Number(p.price || 0);
        const cgst = round2(price * 0.09);
        const sgst = round2(price * 0.09);
        const rowTotal = round2(price + cgst + sgst);
        return [
            getProductName(p.product_id),
            p.remark || '',
            formatAmount(price),
            formatAmount(cgst, 2),
            formatAmount(sgst, 2),
            formatAmount(rowTotal)
        ];
    });

    let total = 0;
    (data.products || []).forEach(p => {
        const price = Number(p.price || 0);
        const cgst = round2(price * 0.09);
        const sgst = round2(price * 0.09);
        total += round2(price + cgst + sgst);
    });

    // Get discount amount
    const discount = Number(data.discount || 0);
    const subtotal = total;
    const finalTotal = Math.max(0, round2(total - discount));

    // Center the table on the page
    const tableStartY = 100; // Start table from top of page
    doc.autoTable({
        startY: tableStartY,
        head: [['Product', 'Remark', 'Price', 'CGST 9%', 'SGST 9%', 'Total']],
        body: tableBody,
        styles: { fontSize: 8, cellPadding: {top:5, right:3, bottom:5, left:3}, overflow: 'linebreak', lineColor: [226,232,240], textColor: [15,23,42], halign: 'center', valign: 'top' },
        headStyles: { fillColor: [25, 118, 210], textColor: [255,255,255], halign: 'center', fontSize: 8 },
        bodyStyles: { fillColor: [255,255,255], valign: 'top' },
        alternateRowStyles: { fillColor: [249, 250, 251] },
        columnStyles: {
            // Sum equals ~ tableWidth (A4 usable width ~ 539pt with current padding)
            0: { cellWidth: 140, halign: 'left' },  // Product
            1: { cellWidth: 200, halign: 'left' },  // Remark
            2: { cellWidth: 55, halign: 'right' },  // Price
            3: { cellWidth: 50, halign: 'right' },  // CGST
            4: { cellWidth: 50, halign: 'right' },  // SGST
            5: { cellWidth: 44, fontStyle: 'bold', halign: 'right' } // Total
        },
        theme: 'grid',
        margin: { left: padding, right: padding },
        tableWidth: pageWidth - padding * 2
    });

    const afterTableY = (doc.lastAutoTable && doc.lastAutoTable.finalY) ? doc.lastAutoTable.finalY : (tableStartY + 12);
    // Total row (sit just below the table's last row)
    doc.setFontSize(10);
    let totalY = afterTableY + 10;
    // subtle line above total (right under table border)
    doc.setDrawColor(226,232,240);
    doc.line(padding, totalY - 8, pageWidth - padding, totalY - 8);
    doc.setTextColor(71,85,105);
    // heading left, value right
    doc.text('Subtotal', padding, totalY);
    doc.setTextColor(15,23,42);
    doc.setFontSize(11);
    doc.text(formatAmount(subtotal), pageWidth - padding, totalY, { align: 'right' });
    
    // Discount row (if discount > 0)
    if (discount > 0) {
        totalY += 15;
        doc.setFontSize(10);
        doc.setTextColor(71,85,105);
        doc.text('Discount', padding, totalY);
        doc.setTextColor(220, 38, 38); // Red color for discount
        doc.setFontSize(11);
        doc.text('-' + formatAmount(discount), pageWidth - padding, totalY, { align: 'right' });
    }
    
    // Final total row
    totalY += 15;
    doc.setDrawColor(226,232,240);
    doc.line(padding, totalY - 8, pageWidth - padding, totalY - 8);
    doc.setFontSize(10);
    doc.setTextColor(71,85,105);
    doc.setFont(undefined, 'bold');
    doc.text('Total', padding, totalY);
    doc.setFont(undefined, 'normal');
    doc.setTextColor(15,23,42);
    doc.setFontSize(12);
    doc.text(formatAmount(finalTotal), pageWidth - padding, totalY, { align: 'right' });

    // Amount in words right under total
    doc.setFontSize(10);
    doc.setTextColor(71, 85, 105);
    const words = numberToWordsIndian(Math.round(finalTotal));
    // In words: heading left, value right
    doc.setTextColor(71,85,105);
    doc.text('In words:', padding, totalY + 12);
    doc.setTextColor(15,23,42);
    doc.setFontSize(9);
    doc.text(words, pageWidth - padding, totalY + 12, { align: 'right', maxWidth: pageWidth - (padding * 2) - 120 });
    doc.setTextColor(15, 23, 42);

    // Footer note
    doc.setFontSize(9);
    doc.text('This is a system-generated quotation.', padding, totalY + 50);

    // Add 4th page for Project Timeline and Commercials
    doc.addPage();
    drawPageBackground();
    try {
        const timelineTitle = 'Project Timeline & Commercials';
        
        // Title banner
        doc.setFillColor(25, 118, 210);
        const timelineHeaderH = 90;
        doc.rect(0, 0, pageWidth, timelineHeaderH, 'F');
        doc.setFontSize(18);
        doc.setTextColor(255, 255, 255);
        doc.text(timelineTitle, padding, timelineHeaderH - 34);

        // Calculate total cost (GST included)
        let totalCost = 0;
        (data.products || []).forEach(p => {
            const price = Number(p.price || 0);
            const cgst = round2(price * 0.09);
            const sgst = round2(price * 0.09);
            totalCost += round2(price + cgst + sgst);
        });
        
        // Apply discount
        const discount = Number(data.discount || 0);
        const finalCost = Math.max(0, round2(totalCost - discount));
        
        const commercialItems = [
            `Total Project Cost: ${formatAmount(finalCost)} GST included (18% GST)${discount > 0 ? ` (Discount: ${formatAmount(discount)} applied)` : ''}`,
            'Image Stock: licensed images Included, wherever required',
            'Scope: As per scope shared by the client',
            'Training: Included',
            'Post Go-Live Support: 1 Month included',
            `Annual Maintenance Cost: 20% of the project cost (optional)`
        ];

        // Calculate total content height for vertical centering
        const lineHeight = 18;
        const sectionGap = 65;
        const itemGap = 35;
        const totalContentHeight = (lineHeight * 2) + sectionGap + (commercialItems.length * itemGap) + 120; // Extra padding
        
        // Start content from vertical center
        let yCursor = Math.max(timelineHeaderH + 20, (pageHeight - totalContentHeight) / 2);
        
        // Project Timeline Section
        // Project Timeline Header with orange bar
        doc.setFillColor(255, 140, 0); // Orange color
        doc.rect(padding, yCursor, pageWidth - padding * 2, 4, 'F');
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(255, 140, 0);
        doc.text('Project Timeline', padding, yCursor - 8);
        
        // Timeline details
        yCursor += 25;
        doc.setFontSize(12);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(15, 23, 42);
        doc.text(`Estimated completion timeline: ${data.project_timeline || 'Not specified'}`, padding, yCursor);
        
        // Commercials Section
        yCursor += 50;
        
        // Commercials Header with orange bar
        doc.setFillColor(255, 140, 0); // Orange color
        doc.rect(padding, yCursor, pageWidth - padding * 2, 4, 'F');
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(255, 140, 0);
        doc.text('Commercials', padding, yCursor - 8);
        
        // Commercials details
        yCursor += 25;
        doc.setFontSize(12);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(15, 23, 42);
        
        commercialItems.forEach((item, index) => {
            // Split each item into label and value for proper formatting
            const colonIndex = item.indexOf(':');
            if (colonIndex > -1) {
                const label = item.substring(0, colonIndex + 1);
                const value = item.substring(colonIndex + 1).trim();
                
                // Bold label
                doc.setFont(undefined, 'bold');
                doc.setFontSize(12);
                doc.setTextColor(15, 23, 42);
                doc.text(label, padding, yCursor);
                
                // Regular value with increased spacing
                doc.setFont(undefined, 'normal');
                doc.setFontSize(12);
                doc.setTextColor(15, 23, 42);
                doc.text(value, padding + 180, yCursor);
            } else {
                // Regular text for items without colons
                doc.setFont(undefined, 'normal');
                doc.setFontSize(12);
                doc.setTextColor(15, 23, 42);
                doc.text(item, padding, yCursor);
            }
            yCursor += 20; // Increased spacing between items
        });

        // Add Payment Terms section
        yCursor += 30;
        
        // Payment Terms Header with orange bar
        doc.setFillColor(255, 140, 0); // Orange color
        doc.rect(padding, yCursor, pageWidth - padding * 2, 4, 'F');
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(255, 140, 0);
        doc.text('Payment Terms', padding, yCursor - 8);
        
        // Payment Terms details
        yCursor += 25;
        doc.setFontSize(12);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(15, 23, 42);
        
        const paymentTermsDetails = getPaymentTermsDetails(data.payment_term_id);
        if (paymentTermsDetails) {
            const paymentTerms = [
                `${paymentTermsDetails.advance_percentage}% Advance on project confirmation`,
                `${paymentTermsDetails.design_dev_percentage}% Upon design & development approval`,
                `${paymentTermsDetails.completion_percentage}% Upon completion of development before deployment`
            ];
            
            paymentTerms.forEach((term, index) => {
                // Add checkmark symbol
                doc.setFont(undefined, 'bold');
                doc.text('✓', padding, yCursor);
                doc.setFont(undefined, 'normal');
                doc.text(term, padding + 15, yCursor);
                yCursor += 18;
            });
        } else {
            doc.text('Payment terms not specified', padding, yCursor);
        }

    } catch (e4) {
        console.warn('Timeline page render error', e4);
    }

    // Add 5th page: Official Details (Accounts & Address)
    try {
        doc.addPage();
        drawPageBackground();

        // Header
        const headerH = 60;
        doc.setFillColor(255, 140, 0);
        doc.rect(0, 0, pageWidth, 10, 'F');
        doc.setFontSize(18);
        doc.setTextColor(15, 23, 42);
        doc.text('Official Details', padding, headerH - 20);

        // Orange divider bar under title (same style as Project Timeline)
        doc.setFillColor(255, 140, 0); // same orange
        doc.rect(padding, headerH - 8, pageWidth - padding * 2, 4, 'F');

        // Column metrics (balanced left/right spacing)
        const innerLeft = padding;
        const innerRight = pageWidth - padding;
        const innerWidth = innerRight - innerLeft;
        const colGap = 50; // space between columns
        const colWidth = (innerWidth - colGap) / 2;
        const leftX = innerLeft; // left column start
        const rightX = innerLeft + colWidth + colGap; // right column start

        // Helper icon drawers (size ~ 10-12pt)
        const drawBankIcon = (x, y) => {
            doc.setDrawColor(51, 65, 85);
            // roof
            doc.line(x, y, x + 10, y - 6);
            doc.line(x + 10, y - 6, x + 20, y);
            // base
            doc.rect(x + 2, y, 16, 10);
            // columns
            doc.line(x + 6, y, x + 6, y + 10);
            doc.line(x + 10, y, x + 10, y + 10);
            doc.line(x + 14, y, x + 14, y + 10);
        };
        const drawPinIcon = (x, y) => {
            doc.setDrawColor(51, 65, 85);
            doc.circle(x + 6, y - 4, 3);
            doc.line(x + 6, y - 1, x + 6, y + 8);
        };
        // (Other detailed icons removed as per request)

        // Structured content blocks with icons - using settings
        const bankDetailsLines = bankDetails ? bankDetails.split('\n').filter(l => l.trim()) : [];
        const leftBlocks = [];
        const companyInfoLines = [];
        if (officeName) companyInfoLines.push(officeName);
        else if (companyName) companyInfoLines.push(companyName);
        
        if (companyInfoLines.length > 0 || bankDetailsLines.length > 0) {
            leftBlocks.push({ lines: [...companyInfoLines, ...bankDetailsLines] });
        }
        
        const legalInfoLines = [];
        if (pan) legalInfoLines.push(`PAN - ${pan}`);
        if (gstin) legalInfoLines.push(`GSTIN - ${gstin}`);
        
        if (legalInfoLines.length > 0) {
            leftBlocks.push({ lines: legalInfoLines });
        }
        
        const addressParts = [];
        if (officeAddress) addressParts.push(...officeAddress.split('\n').filter(l => l.trim()));
        const addressLine = addressParts.length > 0 ? addressParts : [];
        if (officeCity || officeState || officePincode) {
            const cityStatePincode = [officeCity, officeState, officePincode].filter(Boolean).join(', ');
            if (cityStatePincode) addressLine.push(cityStatePincode);
        }
        if (officeCountry) addressLine.push(officeCountry);
        
        const rightBlocks = [];
        if (addressLine.length > 0) {
            rightBlocks.push({ lines: addressLine });
        }
        if (phone) rightBlocks.push({ lines: [`Phone: ${phone}`] });
        if (email) rightBlocks.push({ lines: [`Email: ${email}`] });
        if (website) rightBlocks.push({ lines: [`Website: ${website}`] });
        // Measure total content height for vertical centering
        doc.setFontSize(11);
        const measureHeight = (blocks) => {
            let h = 26; // title gap
            blocks.forEach(block => {
                // icon row spacing
                h += 4;
                block.lines.forEach(line => {
                    const wrapped = doc.splitTextToSize(line, colWidth - 18);
                    h += (wrapped.length * 14);
                });
                h += 6; // space between blocks
            });
            return h;
        };
        const leftContentH = measureHeight(leftBlocks);
        const rightContentH = measureHeight(rightBlocks);
        const contentH = Math.max(leftContentH, rightContentH);
        const startY = Math.max(headerH + 10, (pageHeight - contentH) / 2);

        // Subtle vertical divider for visual balance
        const centerX = innerLeft + colWidth + colGap / 2;
        doc.setDrawColor(226, 232, 240);
        doc.setLineWidth(1);
        doc.line(centerX, startY - 22, centerX, startY + contentH - 8);
        doc.setLineWidth(0.5);

        // Draw left column: Accounts
        doc.setFontSize(20);
        doc.setTextColor(15, 23, 42);
        doc.text('Accounts', leftX, startY);
        doc.setFontSize(11);
        doc.setTextColor(51, 65, 85);
        let y = startY + 16;
        const textXOffset = 0; // no per-line icon offset
        leftBlocks.forEach(block => {
            block.lines.forEach(line => {
                const wrapped = doc.splitTextToSize(line, colWidth - textXOffset);
                doc.text(wrapped, leftX + textXOffset, y);
                y += (wrapped.length * 14);
            });
            y += 6;
        });

        // Draw right column: Address
        doc.setFontSize(20);
        doc.setTextColor(15, 23, 42);
        doc.text('Address', rightX, startY);
        doc.setFontSize(11);
        doc.setTextColor(51, 65, 85);
        y = startY + 16;
        rightBlocks.forEach(block => {
            block.lines.forEach(line => {
                const wrapped = doc.splitTextToSize(line, colWidth - textXOffset);
                doc.text(wrapped, rightX + textXOffset, y);
                y += (wrapped.length * 14);
            });
            y += 6;
        });
    } catch (e5) {
        console.warn('Official details page render error', e5);
    }

    // Save PDF to server (also open in new tab for user)
    const blob = doc.output('blob');
    const reader = new FileReader();
    reader.onloadend = function(){
        const base64 = (reader.result || '').toString();
        // Ensure we have only base64 data
        const payload = {
            quotation_number: data.quotation_number,
            customer_type: data.customer_type,
            customer_id: data.customer_id,
            payment_term_id: data.payment_term_id,
            project_timeline: data.project_timeline,
            products: data.products,
            discount: data.discount || 0,
            total_amount: calculateTotalAmount(data.products || [], data.discount || 0),
            status: 'Draft',
            pdf_base64: base64 // contains data:application/pdf;base64,.... prefix
        };
        $.ajax({
            url: "<?php echo e(route('quotation.store')); ?>",
            type: 'POST',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            success: function(r){
                showAlert('success','Quotation saved successfully');
                if (r && r.data && r.data.file_url) {
                    try {
                        const url = r.data.file_url;
                        window.open(url, '_blank');
                    } catch(e) {}
                } else {
                    // Fallback open from blob
                    try { window.open(URL.createObjectURL(blob), '_blank'); } catch(e) {}
                }
            },
            error: function(xhr){
                console.error('Save quotation failed', xhr.responseText);
                showAlert('error','Failed to save quotation');
                // still open locally
                try { window.open(URL.createObjectURL(blob), '_blank'); } catch(e) {}
            }
        });
    };
    reader.readAsDataURL(blob);
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/quotation/create.blade.php ENDPATH**/ ?>