<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Triserv360 | Quotation</title>
    <style>
        /* PDF specific resets */
        @page {
            margin: 0;
            size: a4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #231F20;
            line-height: 1.4;
            background-color: #FFFFFF;
        }
        * {
            box-sizing: border-box;
        }
        .text-blue { color: #0088CC; }
        .text-orange { color: #FF6100; }
        .bg-blue { background-color: #0088CC; }
        
        /* Layout Helpers */
        .w-100 { width: 100%; }
        .clear { clear: both; }
        .text-right { text-align: right; }
        
        /* Top Bar */
        .top-bar {
            padding: 25px 40px;
            background: transparent;
            width: 100%;
            position: absolute;
            top: 0;
            z-index: 10;
        }
        .main-logo { height: 40px; }
        .contact-info { font-size: 10.5px; color: #000000; font-weight: bold; }
        
        .hero-title {
            font-size: 30px;
            font-weight: bold;
            line-height: 1.1;
            margin: 10px 0 20px;
            text-transform: capitalize;
        }
        .flyer-tag {
            color: #6D6E71;
            font-weight: bold;
            letter-spacing: 2px;
            font-size: 9px;
            margin-bottom: 5px;
        }

        /* Section Title Style */
        .section-title-line {
            margin: 20px 0;
            width: 100%;
            overflow: hidden;
        }
        .blue-box {
            background: #0088CC;
            color: #FFFFFF;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 14px;
            float: left;
        }
        .line-text {
            border-bottom: 3px solid #231F20;
            height: 18px;
            margin-left: 10px;
            float: left;
            width: 55%;
            font-weight: bold;
            font-size: 10px;
            padding-top: 5px;
        }

        /* Services Grid (Using Floats instead of nested tables) */
        .services-container {
            width: 100%;
            margin-top: 15px;
            overflow: hidden;
        }
        .service-column {
            width: 48%;
            float: left;
            margin-bottom: 8px;
        }
        .service-tag {
            font-weight: bold;
            font-size: 10px;
            color: #231F20;
        }
        .service-bullet { color: #0088CC; font-weight: bold; margin-right: 5px; }

        /* Quotation Card (Second Page) */
        .quotation-document-section {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .quotation-card {
            background: #FFFFFF;
            padding: 30px;
            border-top: 8px solid #0088CC;
        }
        .quote-doc-header {
            border-bottom: 1px solid #EEEEEE;
            padding-bottom: 10px;
            margin-bottom: 20px;
            width: 100%;
        }
        .doc-title {
            font-size: 20px;
            color: #0088CC;
            margin: 0;
            font-weight: bold;
        }

        /* Tables */
        .formal-quote-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .formal-quote-table th {
            background: #EEEEEE;
            padding: 8px;
            text-align: left;
            border: 1px solid #DDDDDD;
            font-size: 10px;
        }
        .formal-quote-table td {
            padding: 10px 8px;
            border: 1px solid #EEEEEE;
            font-size: 9.5px;
        }
        tr { page-break-inside: avoid; }

        /* Terms & Conditions (Styled with 5% left/right spacing) */
        .terms-page {
            padding: 20px 10mm; 
        }
        .term-cat-title {
            color: #0088CC;
            font-size: 11px;
            font-weight: bold;
            border-bottom: 2px solid #FF6100;
            padding-bottom: 2px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .term-cat-title2 {
            color: #0088CC;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 2px solid #FF6100;
            padding-bottom: 2px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .term-list {
            list-style: none;
            padding-left: 0;
            margin: 0 0 15px 0;
        }
        .term-list li {
            font-size: 11px;
            color: #444444;
            margin-bottom: 5px;
            padding-left: 12px;
            position: relative;
            line-height: 1.2;
        }
        .term-bullet {
            color: #FF6100 !important;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        .pay-section-tag {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin: 0 0 16px;
      font-size: 12px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      color: #0d558a;
    }

    .pay-section-tag::before {
      content: "";
      width: 10px;
      height: 10px;
      border-radius: 2px;
      background: #ff7b17;
      flex-shrink: 0;
    }

    .pay-terms-list {
      display: grid;
      gap: 12px;
    }

    .pay-term-item {
      display: grid;
      grid-template-columns: 56px 1fr;
      gap: 12px;
      align-items: center;
      padding: 12px;
      border-radius: 18px;
      border: 1px solid #e8eff6;
      background: linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .pay-term-badge {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      display: grid;
      place-items: center;
      background: linear-gradient(180deg, #ff7b17, #ff9a4d);
      color: #ffffff;
      font-size: 18px;
      font-weight: 900;
      box-shadow: 0 10px 18px rgba(255, 123, 23, 0.18);
    }

    .pay-term-text strong {
      display: block;
      margin-bottom: 4px;
      font-size: 15px;
    }

    .pay-term-text span {
      display: block;
      font-size: 12.5px;
      line-height: 1.45;
      color: #617288;
    }

       .banner-container {
    position: relative;
    height: 1121px;
    overflow: hidden;
    margin: 0;
    padding: 0;
}

.banner-img {
    width: 100%;
    position: absolute;
    height: 100%;
    top: 0;
    left: 0;
}


    .text-overlay {
    position: absolute;
    top: 190px;
    left: 320px;
    width: 250px;  /* adjust */
        }

        .about-content{
            color: #6D6E71;
        }

        .terms-section {
            padding: 20px 40px;
        }

        .content-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }

        .left, .right {
            width: 48%;
            height: 40%
        }

        h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #0088CC;
        }

        p {
            font-size: 14px;
            margin: 4px 0;
            color: #333;
        }

        .terms-page,
    .payment-page {
      padding: 14mm;
    }

    .terms-page::before,
    .payment-page::before {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.97)),
        url("quote-bg.jpg") right top / cover no-repeat;
      z-index: 0;
      pointer-events: none;
    }

    .terms-page::after,
    .payment-page::after {
      content: "";
      position: absolute;
      right: -10mm;
      bottom: -10mm;
      width: 84mm;
      height: 84mm;
      background: url("service-bg.jpg") right bottom / contain no-repeat;
      opacity: 0.08;
      z-index: 0;
      pointer-events: none;
    }

    .terms-page > *,
    .payment-page > * {
      position: relative;
      z-index: 1;
    }

    .terms-stack {
      display: grid;
      grid-template-columns: 1fr;
      gap: 14px;
      margin-bottom: 14px;
    }

    .pay-card,
    .payment-card,
    .scan-card,
    .address-card {
      border: 1px solid #d8e4ef;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.97);
      box-shadow: 0 12px 24px rgba(18, 33, 61, 0.05);
      padding: 20px 20px 18px;
    }

        /* Clients Logos */
        .clients-page {
            padding: 5px;
        }
        .logo-box img { max-width: 100%; max-height: 40px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- PAGE 1: COVER PAGE (Hero) -->
    <div class="banner-container">
    
        <img src="https://app.workorio.com/clients/hero.webp" class="banner-img">

        <!-- <div class="top-bar">
            <table class="w-100">
                <tr>
                    <td style="width: 50%;">
                        <?php if(isset($logo_base64) && $logo_base64): ?>
                            <img src="<?php echo e($logo_base64); ?>" class="main-logo">
                        <?php else: ?>
                            <img src="https://triserv360.com/wp-content/uploads/2023/04/logo.png" class="main-logo">
                        <?php endif; ?>
                    </td>
                    <td style="width: 50%; text-align: left;" class="contact-info">
                        <span>Phone: <?php echo e($settings->phone ?? ''); ?></span> &nbsp; &nbsp;
                        <span>Website: <?php echo e($settings->website ?? ''); ?></span>
                    </td>
                </tr>
            </table>
        </div> -->

        <div class="text-overlay" style="width: 400px;">
            <div style="color: #fff; font-size: 24px; font-weight: bold;">
                <span>
                    <?php if($quote->customer_type == 'customer'): ?>
                        <?php echo e(optional($quote->customer)->name ?? 'N/A'); ?>

                    <?php else: ?>
                        <?php 
                            $prospect = $quote->prospect ?? \App\Models\Prospectus::find($quote->prospect_id); 
                        ?>
                        <?php echo e(optional($prospect)->prospectus_name ?? 'N/A'); ?>

                    <?php endif; ?>
                </span>
            </div>
        </div>

    </div>
                
                 
    <div class="page-break"></div>

    <div>
        <img src="https://app.workorio.com/clients/service_page2.png" class="banner-img">
    </div>


    <div class="page-break"></div>


    <!-- PAGE 2: QUOTATION DETAILS -->
    <div class="">
        <div class="quotation-card">
            <table class="quote-doc-header">
                <tr>
                    <td>
                        <div class="doc-title" style="color: #0088CC;">Quotation/Proposed Commercials</div>
                        <div style="font-size: 10px; color: #FF6100; font-weight: bold; letter-spacing: 2px;"><?php echo e(strtoupper($quote->data['subject'] ?? 'Project Estimate & Scope')); ?></div>
                    </td>
                    <td class="text-right" style="font-size: 10px;">
                        <div><strong>Quote #:</strong> <?php echo e($quote->quotation_number); ?></div>
                        <div style="margin: 2px 0;"><strong>Date:</strong> <?php echo e($quote->created_at->format('M d, Y')); ?></div>
                        <div><strong>Valid Till:</strong> <?php echo e($quote->created_at->addDays(15)->format('M d, Y')); ?></div>
                    </td>
                </tr>
            </table>


            <table class="formal-quote-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">S.No</th>
                        <th>Description of Services</th>
                        <th style="width: 50px;" class="text-right">Qty</th>
                        <th style="width: 70px;" class="text-right">Price</th>
                        <th style="width: 60px;" class="text-right">Disc</th>
                        <th style="width: 80px;" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $products = $quote->data['products'] ?? []; 
                        $subtotal_sum = 0;
                    ?>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                            $price = $item['price'] ?? 0;
                            $qty = $item['quantity'] ?? 1;
                            $disc = $item['discount'] ?? 0;
                            $discType = $item['discount_type'] ?? 'percentage';
                            
                            $rowBase = $price * $qty;
                            $rowDiscAmount = ($discType === 'percentage') ? ($rowBase * ($disc / 100)) : $disc;
                            $lineAmount = $rowBase - $rowDiscAmount;
                            $subtotal_sum += $lineAmount;
                        ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td>
                                <strong><?php echo e(optional(\App\Models\SalesProduct::find($item['product_id'] ?? null))->product_name ?? ($item['product_name'] ?? ($item['product_id'] ?? '--'))); ?></strong><br>
                                <span style="font-size: 8.5px; color: #777;"><?php echo e($item['remark'] ?? ''); ?></span>
                            </td>
                            <td class="text-right"><?php echo e($qty); ?> <?php echo e($item['unit'] ?? 'Nos'); ?></td>
                            <td class="text-right"><?php echo e(number_format($price, 2)); ?></td>
                            <td class="text-right">
                                <?php if($disc > 0): ?>
                                    <?php echo e($discType === 'percentage' ? $disc.'%' : number_format($disc, 2)); ?>

                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-right"><?php echo e(number_format($lineAmount, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="5" class="text-right">Subtotal:</td><td class="text-right"><?php echo e(number_format($subtotal_sum, 2)); ?></td></tr>
                    <?php 
                        $discount = $quote->data['discount'] ?? 0;
                        $taxable = max(0, $subtotal_sum - $discount);
                        $gst = round($taxable * 0.18, 2);
                        $grandTotal = $taxable + $gst;
                    ?>
                    <?php if($discount > 0): ?>
                        <tr><td colspan="5" class="text-right">Additional Discount:</td><td class="text-right">-<?php echo e(number_format($discount, 2)); ?></td></tr>
                        <tr><td colspan="5" class="text-right">Taxable Amount:</td><td class="text-right"><?php echo e(number_format($taxable, 2)); ?></td></tr>
                    <?php endif; ?>
                    <tr><td colspan="5" class="text-right">Tax (GST 18%):</td><td class="text-right"><?php echo e(number_format($gst, 2)); ?></td></tr>
                    <tr style="font-size: 13px; color: #0088CC; font-weight: bold;"><td colspan="5" class="text-right" style="border-top: 2px solid #0088CC;">GRAND TOTAL:</td><td class="text-right" style="border-top: 2px solid #0088CC;"><?php echo e(number_format($grandTotal, 2)); ?></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <table style="width:100%; border-collapse: separate; border-spacing: 0 14px; padding: 30px;">

    <!-- Title -->
    <tr>
        <td colspan="2" style="font-size:16px; font-weight:700; letter-spacing: 0.5px; color: #0088CC;">
            <span style="display:inline-block; width:10px; height:10px; background: #ff7b17; margin-right:8px;"></span>
            Terms and Conditions
        </td>
    </tr>

    <!-- Card 1 -->
    <tr>
        <td colspan="2" style="
            border:1px solid #e8eff6;
            border-radius:18px;
            background:#ffffff;
            padding:12px;
        ">
            <table style="width:100%;">
                <tr>
                    <!-- Text -->
                    <td style="padding-left:10px;">
                        <div style="font-weight:bold; font-size:14px;">
                            <?php 
                                $pTerms = $quote->data['payment_terms'] ?? (request('payment_terms') ?? ($settings->payment_terms ?? ''));
                            ?>
                            <?php echo nl2br(e($pTerms)); ?></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>


    <div class="page-break"></div>

        <img src="https://app.workorio.com/clients/account_details.png" class="banner-img">

    <div class="page-break"></div>
    

    <!-- PAGE 4: CLIENTS LIST -->
        <!-- <div class="clients-page" style="padding: 30px; margin-bottom: 4px !important; ">
            <table style="width: 100%; margin-bottom: 25px;">
                <tr>
                    <td style="width: 140px; vertical-align: middle;">
                        <div class="blue-box" style="margin-bottom: 4px !important;">Trusted By Businesses & Partners</div>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: separate; border-spacing: 5px; margin-top: 8px;">
                <?php 
                    $logos = [
                        ['url' => 'https://app.workorio.com/clients/aa.png', 'name' => 'AA'],
                        ['url' => 'https://app.workorio.com/clients/acxiom.png', 'name' => 'Acxiom'],
                        ['url' => 'https://app.workorio.com/clients/ather.png', 'name' => 'Ather'],
                        ['url' => 'https://app.workorio.com/clients/forward-eye-consulting.png', 'name' => 'Forward Eye'],
                        ['url' => 'https://app.workorio.com/clients/rainbow.png', 'name' => 'Rainbow'],
                        ['url' => 'https://app.workorio.com/clients/go-green.png', 'name' => 'Go Green'],
                        ['url' => 'https://app.workorio.com/clients/newkanpurcityhospital.png', 'name' => 'Kanpur Hospital'],
                        ['url' => 'https://app.workorio.com/clients/mega.png', 'name' => 'Mega'],
                        ['url' => 'https://app.workorio.com/clients/dw.png', 'name' => 'DW'],
                        ['url' => 'https://app.workorio.com/clients/ltl.png', 'name' => 'LTL'],
                        ['url' => 'https://app.workorio.com/clients/scpl.png', 'name' => 'SCPL'],
                        ['url' => 'https://app.workorio.com/clients/the-concept-key.png', 'name' => 'Concept Key'],
                        ['url' => 'https://app.workorio.com/clients/zycloud.png', 'name' => 'Zycloud'],
                        ['url' => 'https://app.workorio.com/clients/nd.png', 'name' => 'ND'],
                        ['url' => 'https://app.workorio.com/clients/arna.png', 'name' => 'Arna'],
                        ['url' => 'https://app.workorio.com/clients/sanjay.png', 'name' => 'Sanjay'],
                        ['url' => 'https://app.workorio.com/clients/tb-group.png', 'name' => 'TB Group'],
                        ['url' => 'https://app.workorio.com/clients/omi-international.png', 'name' => 'Omi'],
                        ['url' => 'https://app.workorio.com/clients/reliable-home.png', 'name' => 'Reliable'],
                        ['url' => 'https://app.workorio.com/clients/sms.png', 'name' => 'SMS'],
                        ['url' => 'https://app.workorio.com/clients/softech.png', 'name' => 'Softech'],
                        ['url' => 'https://app.workorio.com/clients/voice.png', 'name' => 'Voice'],
                        ['url' => 'https://app.workorio.com/clients/decarbonization.png', 'name' => 'Decarbonization'],
                        ['url' => 'https://app.workorio.com/clients/bandejjia.png', 'name' => 'Bandejjia'],
                        ['url' => 'https://app.workorio.com/clients/penza.png', 'name' => 'Penza'],
                        ['url' => 'https://app.workorio.com/clients/eil-global.png', 'name' => 'EIL Global'],
                        ['url' => 'https://app.workorio.com/clients/super-house.png', 'name' => 'Super House'],
                        ['url' => 'https://app.workorio.com/clients/mtt.png', 'name' => 'MTT'],
                        ['url' => 'https://app.workorio.com/clients/altawazon.png', 'name' => 'Altawazon'],
                        ['url' => 'https://app.workorio.com/clients/ahlan.png', 'name' => 'Ahlan'],
                        ['url' => 'https://app.workorio.com/clients/maxblocks.png', 'name' => 'Max Block'],
                        ['url' => 'https://app.workorio.com/clients/mcpl.png', 'name' => 'MCPL'],
                        ['url' => 'https://app.workorio.com/clients/mocha.png', 'name' => 'Mocha'],
                        ['url' => 'https://app.workorio.com/clients/mywi.png', 'name' => 'MyWi'],
                        ['url' => 'https://app.workorio.com/clients/noonschool.png', 'name' => 'NoonSchool'],
                        ['url' => 'https://app.workorio.com/clients/omi-international.png', 'name' => 'OMI'],
                        ['url' => 'https://app.workorio.com/clients/one.png', 'name' => 'One'],
                        ['url' => 'https://app.workorio.com/clients/paloma.png', 'name' => 'Paloma'],
                        ['url' => 'https://app.workorio.com/clients/frr.png', 'name' => 'F'],
                        ['url' => 'https://app.workorio.com/clients/futurisk.png', 'name' => 'Futurisk'],
                        ['url' => 'https://app.workorio.com/clients/good-step.png', 'name' => 'Good-step'],
                        ['url' => 'https://app.workorio.com/clients/gopi.png', 'name' => 'Gopi'],
                        ['url' => 'https://app.workorio.com/clients/gpb.png', 'name' => 'GPB'],
                        ['url' => 'https://app.workorio.com/clients/huntman.png', 'name' => 'Huntman'],
                        ['url' => 'https://app.workorio.com/clients/info.png', 'name' => 'Info'],
                        ['url' => 'https://app.workorio.com/clients/injectoplast.png', 'name' => 'IP'],
                        ['url' => 'https://app.workorio.com/clients/katria.png', 'name' => 'Katria'],
                        ['url' => 'https://app.workorio.com/clients/logistics-park.png', 'name' => 'Logistic Park'],
                        ['url' => 'https://app.workorio.com/clients/crown.png', 'name' => 'Crown'],
                        ['url' => 'https://app.workorio.com/clients/dheata.png', 'name' => 'Dheata'],
                        ['url' => 'https://app.workorio.com/clients/e-mentors.png', 'name' => 'E-Mentors'],
                        ['url' => 'https://app.workorio.com/clients/exting.png', 'name' => 'Exting'],
                        ['url' => 'https://app.workorio.com/clients/final-logo.png', 'name' => 'Wonder Wizz'],
                        ['url' => 'https://app.workorio.com/clients/final-logo-1.png', 'name' => 'MP'],
                        ['url' => 'https://app.workorio.com/clients/fiscal-feed.png', 'name' => 'FF'],
                        ['url' => 'https://app.workorio.com/clients/forward-eye.png', 'name' => 'FE'],
                        ['url' => 'https://app.workorio.com/clients/pentacle.png', 'name' => 'Pentacle'],
                        ['url' => 'https://app.workorio.com/clients/prajadhikar.png', 'name' => 'Prajadhikar'],
                        ['url' => 'https://app.workorio.com/clients/prakharpanday.png', 'name' => 'Prakharpanday'],
                        ['url' => 'https://app.workorio.com/clients/protector.png', 'name' => 'Protector'],
                        ['url' => 'https://app.workorio.com/clients/rll.png', 'name' => 'Rll'],
                        ['url' => 'https://app.workorio.com/clients/saavri.png', 'name' => 'Saavri'],
                        ['url' => 'https://app.workorio.com/clients/savvy.png', 'name' => 'Savvy'],
                        ['url' => 'https://app.workorio.com/clients/shree.png', 'name' => 'Shree'],
                        ['url' => 'https://app.workorio.com/clients/snmills.png', 'name' => 'SNM'],
                        ['url' => 'https://app.workorio.com/clients/starkan.png', 'name' => 'Star'],
                        ['url' => 'https://app.workorio.com/clients/sunways(1).png', 'name' => 'Sun'],
                        ['url' => 'https://app.workorio.com/clients/super-wheel.png', 'name' => 'Super'],
                        ['url' => 'https://app.workorio.com/clients/tandh.png', 'name' => 'T&H'],
                        ['url' => 'https://app.workorio.com/clients/tes.png', 'name' => 'T'],
                        ['url' => 'https://app.workorio.com/clients/tpi.png', 'name' => 'TPI'],
                        ['url' => 'https://app.workorio.com/clients/twa.png', 'name' => 'TWA'],
                        ['url' => 'https://app.workorio.com/clients/unic.png', 'name' => 'UNIC'],
                        ['url' => 'https://app.workorio.com/clients/veneta.png', 'name' => 'Veneta'],
                        ['url' => 'https://app.workorio.com/clients/vertikal.png', 'name' => 'Vertikal'],
                        ['url' => 'https://app.workorio.com/clients/wildnet.png', 'name' => 'WildNet'],
                    ];
                ?>
                <?php $__currentLoopData = array_chunk($logos, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td style="background: #f9f9f9; border: 1px solid #E6E7E8; padding: 6px; text-align: center;">
                            <img src="<?php echo e($l['url']); ?>" style="max-width: 100%; max-height: 40px;" alt="<?php echo e($l['name']); ?>">
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
        </div> -->


        <div class="page-break"></div>

    <!-- PAGE 3: TERMS & CONDITIONS -->
    <!-- <div class="terms-page">
        <div>
            <div class="blue-box">Other Terms & Conditions</div>
        </div>

        <table style="width: 100%; border-collapse: separate; border-spacing: 0 5px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                    <div class="term-cat-title">1. Commercial Terms</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Payments once made are non-refundable under any circumstances.</li>
                        <li><span class="term-bullet">•</span> All applicable taxes (GST or others) will be charged extra as per Government of India regulations.</li>
                        <li><span class="term-bullet">•</span> Project work will proceed according to the milestone payment schedule mentioned in the proposal.</li>
                        <li><span class="term-bullet">•</span> Delay in milestone payments may result in temporary suspension of the project.</li>
                        <li><span class="term-bullet">•</span> Ownership of the final project will be transferred to the client only after full and final payment.</li>
                    </ul>

                    <div class="term-cat-title">2. Scope & Project Execution</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Project scope will be limited strictly to the features and deliverables mentioned in
                                this proposal or separate Scope of work document.</li>
                        <li><span class="term-bullet">•</span>Any additional features or work outside the agreed scope will be treated as extra work
                                and billed separately.</li>
                        <li><span class="term-bullet">•</span> Additional revisions beyond agreed revisions may incur additional charges.</li>
                        <li><span class="term-bullet">•</span> Change requests after project approval or development start may impact cost and
                                timeline.</li>
                        <li><span class="term-bullet">•</span> Delay in approvals, feedback, or content submission from the client may extend the
                                project timeline.</li>
                        <li><span class="term-bullet">•</span>The client should nominate one primary contact person for communication and approvals.</li>
                        <li><span class="term-bullet">•</span>If a project remains on hold due to no response from the client for more than six
                                months, it will be considered closed.</li>
                        <li><span class="term-bullet">•</span>Re-initiating a closed project may require revised pricing and timelines.</li>

                    </ul>

                    <div class="term-cat-title">3. Client Responsibities</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> The client will provide all required content including text, images, logos, product
                                information, and documents.</li>
                        <li><span class="term-bullet">•</span> Royalty-free stock images may be used where necessary during development.</li>
                        <li><span class="term-bullet">•</span> The client is responsible for ensuring that all provided materials do not violate
                                copyright or trademark laws.</li>
                        <li><span class="term-bullet">•</span>The client is responsible for obtaining any licenses, approvals, or legal permissions
                                required for their business.</li>
                    </ul>

                    <div class="term-cat-title" style="margin-bottom: 20px;">4. Third-Party Services</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span>Any third-party tools, plugins, APIs, payment gateways, or integrations will be subject
                                to their own pricing and policies.</li>
                        <li><span class="term-bullet">•</span> Triserv 360 Business Solutions Pvt Ltd will not be responsible for service interruptions
                                caused by third-party providers.</li>
                        <li><span class="term-bullet">•</span> If hosting is managed by the client or a third party, we will not be responsible for
                                server downtime or hosting issues.</li>
                        <li><span class="term-bullet">•</span> Unless specified in the project scope, data backup responsibility remains with the
                                client or hosting provider.</li>
                        <li><span class="term-bullet">•</span>Websites will be tested on modern browsers and commonly used devices.</li>
                        <li><span class="term-bullet">•</span>Compatibility issues with outdated browsers may require additional development effort.</li>

                    </ul>
                </td>
                <td style="width: 50%; vertical-align: top; margin-top: 20px !important;">
                    <div class="term-cat-title">5. Maintenance & Support</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Unless mentioned otherwise, project cost includes development and deployment only.</li>
                        <li><span class="term-bullet">•</span> Ongoing maintenance and technical support will require a separate support agreement.</li>
                        <li><span class="term-bullet">•</span> Bugs reported within 15–30 days after delivery related to development will be fixed
                                without additional charges.</li>
                        <li><span class="term-bullet">•</span>Feature changes or enhancements will be treated as new work.</li>

                    </ul>

                    <div class="term-cat-title">6. Confidentiality (NDA)</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Both parties agree to maintain confidentiality of business, project, and technical
                                information shared during the engagement.</li>
                        <li><span class="term-bullet">•</span> Such confidential information shall not be disclosed to third parties without written
                                consent.</li>
                    </ul>

                    <div class="term-cat-title">7. Source Code & IP</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Access to source code, development files, or admin credentials will be provided only
                                after full payment.</li>
                        <li><span class="term-bullet">•</span> Until full payment is received, all development work remains the intellectual property
                                of Triserv 360 Business Solutions Pvt Ltd.</li>
                        <li><span class="term-bullet">•</span> The client shall not copy, distribute, or provide project source code to any third party
                                before full payment.</li>
                        <li><span class="term-bullet">•</span>During development, the project may remain on Triserv 360 development servers or
                                environments.</li>
                        <li><span class="term-bullet">•</span>Unauthorized commercial usage of the developed software without clearing payment may
                                lead to suspension of services or legal action.</li>
                    </ul>

                    <div class="term-cat-title">8. Staff Non-Solicitation</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> The client shall not directly approach, hire, solicit, or offer freelance work or
                                separate payments to employees or team members of Triserv 360 Business Solutions Pvt Ltd
                                without prior written consent.</li>
                        <li><span class="term-bullet">•</span> Any attempt to bypass the company by engaging team members directly for project work may
                                result in termination of services without refund.</li>
                    </ul>

                    <div class="term-cat-title">9. Operational Policies</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Any modification made by the client or third-party developers after project delivery may
                                affect functionality, and we will not be responsible for such issues.</li>
                        <li><span class="term-bullet">•</span> Triserv 360 Business Solutions Pvt Ltd reserves the right to showcase completed projects
                                in its portfolio or marketing materials unless otherwise agreed.</li>
                    </ul>

                    <div class="term-cat-title">10. Liability & Legal</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span>In case of any dispute arising from this project, jurisdiction shall be Kanpur, Uttar
                                Pradesh, India.</li>
                    </ul>
                    
                </td>
            </tr>
        </table>

         <div class="term-cat-title">Acceptance of Proposal</div>
         <p style="font-size: 11px">Approval of this proposal or payment of the advance amount will be considered acceptance of all the terms and conditions mentioned above.</p>

    </div> -->

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/triserv.blade.php ENDPATH**/ ?>