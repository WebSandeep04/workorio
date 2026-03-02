

<?php $__env->startSection('styles'); ?>
<style>
    body {
        background-color: white;
        font-family: 'Helvetica', sans-serif;
        padding: 20px;
    }
    .main-border {
        border: 2px solid #000;
        padding: 5px;
    }
    .header-table {
        width: 100%;
        margin-bottom: 2px;
    }
    .header-table td {
        border: none;
        padding: 0 10px;
        vertical-align: middle; /* Align logo and address vertically middle */
    }
    .logo-section {
        width: 50%;
        text-align: left;
    }
    .logo-section img {
        max-width: 200px;
        height: auto;
    }
    .address-section {
        width: 50%;
        text-align: right;
        font-size: 9px;
        line-height: 1.2;
    }
    .address-block {
        display: inline-block;
        text-align: left;
    }
    .header-border {
        border-bottom: 2px solid #000;
        margin-bottom: 8px;
    }
    .title-bar {
        background-color: #fff;
        border: 2px solid #000;
        text-align: center;
        padding: 4px;
        font-size: 16px;
        font-weight: bold;
        letter-spacing: 2px;
        margin-bottom: 0;
    }
    .customer-details-table {
        width: 100%;
        border-collapse: collapse;
    }
    .customer-details-table td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 11px;
        vertical-align: top;
    }
    .subject-bar {
        background-color: <?php echo e($settings->primary_color ?? '#6f42c1'); ?>;
        color: white;
        text-align: center;
        padding: 5px;
        font-weight: bold;
        font-size: 11px;
        border: 1px solid #000;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .items-table th {
        background-color: <?php echo e($settings->primary_color ?? '#6f42c1'); ?>;
        color: white;
        border: 1px solid #000;
        padding: 5px;
        font-size: 11px;
        text-align: center;
    }
    .items-table td {
        border: 1px solid #000;
        padding: 5px;
        font-size: 11px;
        text-align: center;
    }
    .text-left { text-align: left !important; }
    .sub-section-row {
        background-color: #ffff00;
        font-weight: bold;
        text-align: center;
        color: red;
    }
    .total-label-cell {
        background-color: <?php echo e($settings->primary_color ?? '#6f42c1'); ?>;
        color: white;
        font-weight: bold;
        text-align: left;
    }
    .bank-details {
        text-align: center;
        font-size: 11px;
        font-weight: bold;
        color: #d9534f;
        padding: 10px 0;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .payment-terms {
        padding: 10px;
        font-size: 11px;
    }
    .payment-terms h4 {
        color: #6f42c1;
        text-decoration: underline;
        margin-bottom: 5px;
    }
    .payment-terms ol {
        margin-top: 0;
        padding-left: 20px;
    }
    .payment-terms li {
        margin-bottom: 5px;
        font-weight: bold;
    }
    .footer-section {
        margin-top: 20px;
        text-align: right;
        font-size: 11px;
    }
    .footer-brand {
        margin-top: 40px;
        text-align: center;
        font-weight: bold;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="main-border">
    
    <table class="header-table">
        <tr>
            <td class="logo-section">
                <div style="text-align: left; padding-left: 10px;">
                    <?php if(isset($logo_base64) && $logo_base64): ?>
                        <img src="<?php echo e($logo_base64); ?>" alt="Logo">
                    <?php elseif($settings->logo_path && file_exists(public_path('storage/'.$settings->logo_path))): ?>
                        <img src="<?php echo e(public_path('storage/'.$settings->logo_path)); ?>" alt="Logo">
                    <?php else: ?>
                        
                        <div style="display: inline-block;">
                            <div style="color: <?php echo e($settings->primary_color ?? '#6f42c1'); ?>; font-size: 32px; font-weight: bold; line-height: 1; margin: 0;">
                                airoshelt<span style="font-size: 8px; vertical-align: super;">&reg;</span>
                            </div>
                            <div style="border-top: 1px solid #000; margin-top: 2px; padding-top: 1px;">
                                <small style="font-size: 8px; letter-spacing: 0.5px;">A venture by <strong>UNIQUE AIR CONDITIONING</strong></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </td>
            <td class="address-section">
                <div class="address-block">
                    <strong style="color: <?php echo e($settings->primary_color ?? '#6f42c1'); ?>;"><?php echo e(strtoupper($settings->office_name ?? 'CORPORATE OFFICE')); ?> :</strong> 
                    <?php echo e($settings->office_address ?? 'OFFICE NO 102 1ST FLOOR, H & M ROYAL WING 4, KONDHWA KATRAJ ROAD, SURVEY NUMBER :18/19 OPP TALAB FACTORY'); ?><br>
                    <?php echo e(strtoupper($settings->office_city ?? 'PUNE')); ?>, <?php echo e(strtoupper($settings->office_state ?? 'MAHARASHTRA')); ?> - <?php echo e($settings->office_pincode ?? '411048'); ?><br>
                    <strong>Email:</strong> <?php echo e($settings->email ?? 'sales@airoshelt.com / uniqueacprojects@gmail.com'); ?><br>
                    <strong>Mobile:</strong> <?php echo e($settings->phone ?? '8448441066'); ?> &nbsp;&nbsp;&nbsp;&nbsp; <strong>Tel:</strong> 020-46740006
                </div>
            </td>
        </tr>
    </table>

    <div class="header-border"></div>

    <div class="title-bar">QUOTATION</div>

    
    <table class="customer-details-table">
        <tr>
            <td style="width: 60%;">
                To,<br>
                <?php if($quote->customer_type == 'customer' && $quote->customer): ?>
                    <strong><?php echo e($quote->customer->name); ?></strong><br>
                    <?php echo nl2br(e($quote->customer->address ?? '')); ?><br>
                    <?php if($quote->customer->gst_number): ?> GSTIN :- &nbsp; <?php echo e($quote->customer->gst_number); ?><br> <?php endif; ?>
                    CONTACT : <?php echo e($quote->customer->phone ?? '--'); ?>

                <?php elseif($quote->customer_type == 'prospect'): ?>
                    <?php $prospect = \App\Models\Prospectus::find($quote->customer_id); ?>
                    <?php if($prospect): ?>
                        <strong><?php echo e($prospect->prospectus_name ?? 'N/A'); ?></strong><br>
                        <?php if($prospect->address || $prospect->city || $prospect->state): ?>
                            <?php echo e($prospect->address ?? ''); ?>

                            <?php if($prospect->city || $prospect->state): ?>
                                <br><?php echo e(trim(implode(', ', array_filter([$prospect->city, $prospect->state])))); ?>

                            <?php endif; ?>
                            <br>
                        <?php endif; ?>
                        CONTACT : <?php echo e($prospect->contact_person ?? '--'); ?>

                        <?php if($prospect->contact_number): ?>
                            (<?php echo e($prospect->contact_number); ?>)
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="min-height: 80px;"></div>
                <?php endif; ?>
            </td>
            <td style="width: 40%;">
                Qut. No.: <?php echo e($quote->quotation_number ?? ''); ?><br>
                DATE - <?php echo e(optional($quote->created_at)->format('d-M-Y') ?? ''); ?>

            </td>
        </tr>
    </table>

    <div class="subject-bar">
        SUBJECT : <?php echo e($quote->data['subject'] ?? ''); ?>

    </div>

    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%;">SR NO.</th>
                <th style="width: 47%;">DESCRIPTION</th>
                <th style="width: 10%;">QTY</th>
                <th style="width: 10%;">UNIT</th>
                <th style="width: 12%;">RATE</th>
                <th style="width: 13%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <?php $sr = 1; ?>
            <?php if(isset($quote->data['products']) && count($quote->data['products']) > 0): ?>
                <?php $__currentLoopData = $quote->data['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sr++); ?></td>
                    <td class="text-left">
                        <?php echo e($p['product_name'] ?? ($p['name'] ?? '--')); ?>

                        <?php if(!empty($p['remark'])): ?>
                            <br><small style="font-size: 9px; color: #666;">(<?php echo e($p['remark']); ?>)</small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($p['quantity'] ?? 1); ?></td>
                    <td><?php echo e($p['unit'] ?? 'Nos'); ?></td>
                    <td><?php echo e(number_format($p['price'] ?? 0, 2)); ?></td>
                    <td><?php echo e(number_format(($p['quantity'] ?? 1) * ($p['price'] ?? 0), 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <tr><td colspan="6" style="padding: 20px; color: #999;">No products listed</td></tr>
            <?php endif; ?>

            <?php
                $gross_subtotal = 0;
                foreach(($quote->data['products'] ?? []) as $p) {
                    $gross_subtotal += ($p['quantity'] ?? 1) * ($p['price'] ?? 0);
                }
                $net_taxable = $quote->total_amount ?? 0;
                $discount_val = $gross_subtotal - $net_taxable;
            ?>

            <?php if($discount_val > 0): ?>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Gross Total</td>
                <td><?php echo e(number_format($gross_subtotal, 2)); ?></td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Discount</td>
                <td><?php echo e(number_format($discount_val, 2)); ?></td>
            </tr>
            <?php endif; ?>

            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Basic</td>
                <td><?php echo e(number_format($net_taxable, 2)); ?></td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">GST 18%</td>
                <td><?php echo e(number_format($net_taxable * 0.18, 2)); ?></td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Total</td>
                <td><?php echo e(number_format($net_taxable * 1.18, 2)); ?></td>
            </tr>
            
            
            
        </tbody>
    </table>

    
    <div class="bank-details">
        Bank Details: &nbsp; <?php echo e($settings->bank_details ?? ''); ?>

    </div>

    
    <div class="payment-terms">
        <h4>PAYMENT TERMS :</h4>
        <ol>
            <li style="color: red;">HIGH SITE 100% PAYMENT IN ADVANCE</li>
            <li>POWER SUPPLY FOR THE AIR CONDITIONING UNIT SHALL BE ARRANGED AND PROVIDED BY THE CLIENT.</li>
            <li>WE ENSURE CONSISTENT HIGH-QUALITY STANDARDS IN ALL OUR PRODUCTS AND SERVICES, FROM THE FIRST INSTALLATION AND EVERY TIME THEREAFTER.</li>
            <li>THIS QUOTATION IS VALID FOR A PERIOD OF 7 DAYS FROM THE DATE OF ISSUE.</li>
            <li>MATHADI (LABOUR HANDLING) CHARGES, IF APPLICABLE, SHALL BE BORNE BY THE CUSTOMER.</li>
        </ol>
    </div>

    <div style="margin-top: 30px; font-size: 11px; text-align: center; border-top: 1px dashed #000; padding-top: 10px; line-height: 1.5;">
        <strong>Thank you for connecting with Unique Air Conditioning!</strong><br>
        We are specialized in HVAC, Chillers, VRF, Cold Room, Ductable, Tower, Cassette & Split AC's.<br>
        Also get Tower and Ductable AC on Rental Basis for Events & Corporates<br>
        Please Call us on <strong>8448441066</strong> or <a href="https://g.page/UniqueAC/review?gm" style="color: #000; text-decoration: none;">https://g.page/UniqueAC/review?gm</a><br>
        Website - <a href="http://www.uniqueacprojects.com" style="color: #000; text-decoration: none;">www.uniqueacprojects.com</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('quotation.templates.base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/uniqueac.blade.php ENDPATH**/ ?>