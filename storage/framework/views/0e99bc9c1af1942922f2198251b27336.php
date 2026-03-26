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
            padding: 15px 40px;
            border-bottom: 1px solid #E6E7E8;
            background: #FFFFFF;
        }
        .main-logo { height: 40px; }
        .contact-info { font-size: 10px; color: #6D6E71; font-weight: bold; }

        /* Hero Section (Cover Page) */
        .hero-page {
            padding: 60px 40px;
            position: relative;
        }
        .flyer-hero-content {
            width: 100%;
            margin-top: 40px;
        }
        .hero-left {
            width: 35%;
            float: left;
            padding-top: 40px;
        }
        .hero-right {
            width: 65%;
            float: left;
            padding-left: 20px;
        }
        .logo-circle {
            width: 160px;
            height: 160px;
            background: #FF6100;
            border-radius: 50%;
            text-align: center;
            display: block;
            margin: 0 auto;
        }
        .logo-circle img {
            width: 100px;
            margin-top: 50px;
        }
        
        .hero-title {
            font-size: 28px;
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
            padding: 5px 15px;
            font-weight: bold;
            font-size: 12px;
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
            font-size: 28px;
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
            padding: 40px 10mm; 
        }
        .term-cat-title {
            color: #0088CC;
            font-size: 11px;
            font-weight: bold;
            border-bottom: 2px solid #FF6100;
            padding-bottom: 2px;
            display: inline-block;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .term-list {
            list-style: none;
            padding-left: 0;
            margin: 0 0 15px 0;
        }
        .term-list li {
            font-size: 9px;
            color: #444444;
            margin-bottom: 5px;
            padding-left: 12px;
            position: relative;
            line-height: 1.4;
        }
        .term-bullet {
            color: #FF6100;
            font-weight: bold;
            position: absolute;
            left: 0;
        }

        /* Clients Logos */
        .clients-page {
            padding: 40px;
        }
        .logo-box img { max-width: 65px; max-height: 28px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <!-- PAGE 1: COVER PAGE (Hero) -->
    <div class="top-bar">
        <table class="w-100">
            <tr>
                <td style="width: 50%;">
                    <?php if(isset($logo_base64) && $logo_base64): ?>
                        <img src="<?php echo e($logo_base64); ?>" class="main-logo">
                    <?php else: ?>
                        <img src="https://triserv360.com/wp-content/uploads/2023/04/logo.png" class="main-logo">
                    <?php endif; ?>
                </td>
                <td style="width: 50%; text-align: right;" class="contact-info">
                    <span>📞 <?php echo e($settings->phone ?? ''); ?></span> &nbsp; &nbsp;
                    <span>🌐 <?php echo e($settings->website ?? ''); ?></span>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="hero-page">
        <div class="flyer-hero-content">
            <div class="hero-left">
                <div class="logo-circle">
                    <?php if(isset($logo_base64) && $logo_base64): ?>
                        <img src="<?php echo e($logo_base64); ?>">
                    <?php else: ?>
                        <img src="https://triserv360.com/wp-content/uploads/2023/04/logo.png">
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-right">
                <div class="flyer-tag">WE ARE JUST GETTING STARTED</div>
                <div class="hero-title">Helping Businesses Achieve <br><span class="text-blue">Excellence</span></div>
                
                <div class="section-title-line">
                    <div class="blue-box">ABOUT US</div>
                    <div class="line-text">STORY OF OUR EXCELLENCE</div>
                </div>
                
                <div style="font-size: 10px; color: #6D6E71; margin-bottom: 25px; line-height: 1.5; clear: both;">
                    <?php echo e($settings->company_description ?? 'A leading provider of enterprise technology solutions, helping businesses transform through digital excellence and innovative services.'); ?>

                </div>

                <div class="services-container">
                    <?php 
                        $servicesArr = is_string($settings->services ?? []) ? json_decode($settings->services, true) : ($settings->services ?? []);
                        $servicesArr = is_array($servicesArr) ? array_slice($servicesArr, 0, 8) : [];
                    ?>
                    <?php $__currentLoopData = $servicesArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="service-column">
                            <span class="service-bullet">></span>
                            <span class="service-tag"><?php echo e($service); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        
        <div style="margin-top: 150px; text-align: center; border-top: 1px solid #E6E7E8; padding-top: 20px;">
            <div style="font-size: 14px; font-weight: bold; color: #0088CC;"><?php echo e(strtoupper($settings->company_name ?? 'TRISERV360')); ?></div>
            <div style="font-size: 10px; color: #6D6E71;"><?php echo e($settings->website ?? ''); ?> | <?php echo e($settings->email ?? ''); ?></div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 2: QUOTATION DETAILS -->
    <div class="quotation-document-section">
        <div class="quotation-card">
            <table class="quote-doc-header">
                <tr>
                    <td>
                        <div class="doc-title" style="color: #0088CC;">QUOTATION</div>
                        <div style="font-size: 10px; color: #FF6100; font-weight: bold; letter-spacing: 2px;"><?php echo e(strtoupper($quote->data['subject'] ?? 'PROJECT ESTIMATE & SCOPE')); ?></div>
                    </td>
                    <td class="text-right" style="font-size: 10px;">
                        <div><strong>Quote #:</strong> <?php echo e($quote->quotation_number); ?></div>
                        <div style="margin: 2px 0;"><strong>Date:</strong> <?php echo e($quote->created_at->format('M d, Y')); ?></div>
                        <div><strong>Valid Till:</strong> <?php echo e($quote->created_at->addDays(15)->format('M d, Y')); ?></div>
                    </td>
                </tr>
            </table>

            <table class="w-100" style="margin-bottom: 20px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="color: #0088CC; font-size: 9px; font-weight: bold; margin-bottom: 3px;">QUOTATION FROM:</div>
                        <div style="font-size: 10px; color: #555;">
                            <strong><?php echo e($settings->company_name ?? 'N/A'); ?></strong><br>
                            <?php echo e($settings->office_address ?? ''); ?><br>
                            <?php echo e($settings->office_city ?? ''); ?>, <?php echo e($settings->office_state ?? ''); ?> - <?php echo e($settings->office_pincode ?? ''); ?><br>
                            Email: <?php echo e($settings->email ?? ''); ?>

                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div style="color: #0088CC; font-size: 9px; font-weight: bold; margin-bottom: 3px;">PREPARED FOR:</div>
                        <div style="font-size: 10px; color: #555;">
                            <?php if($quote->customer_type == 'customer'): ?>
                                <strong><?php echo e(optional($quote->customer)->name ?? 'N/A'); ?></strong><br>
                                <?php echo e(optional($quote->customer)->company_name ?? ''); ?><br>
                                <?php echo e(optional($quote->customer)->address ?? ''); ?>

                            <?php else: ?>
                                <?php 
                                    $prospect = $quote->prospect ?? \App\Models\Prospectus::find($quote->prospect_id); 
                                    $loc = [];
                                    if ($prospect && $prospect->city) $loc[] = $prospect->city->city_name;
                                    if ($prospect && $prospect->state) $loc[] = $prospect->state->state_name;
                                ?>
                                <strong><?php echo e(optional($prospect)->prospectus_name ?? 'N/A'); ?></strong><br>
                                <?php echo e(optional($prospect)->address ?? ''); ?><br>
                                <?php if(!empty($loc)): ?> <?php echo e(implode(', ', $loc)); ?> <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="formal-quote-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">S.No</th>
                        <th>Description of Services</th>
                        <th style="width: 80px;" class="text-right">Price</th>
                        <th style="width: 80px;" class="text-right">Tax(18%)</th>
                        <th style="width: 80px;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $products = $quote->data['products'] ?? []; 
                        $subtotal_sum = 0;
                        $tax_sum = 0;
                    ?>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                            $price = $item['price'] ?? 0;
                            $tax = round($price * 0.18, 2);
                            $rowTotal = $price + $tax;
                            $subtotal_sum += $price;
                            $tax_sum += $tax;
                        ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td>
                                <strong><?php echo e(optional(\App\Models\SalesProduct::find($item['product_id'] ?? null))->product_name ?? ($item['product_name'] ?? ($item['product_id'] ?? '--'))); ?></strong><br>
                                <span style="font-size: 8.5px; color: #777;"><?php echo e($item['remark'] ?? ''); ?></span>
                            </td>
                            <td class="text-right"><?php echo e(number_format($price, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($tax, 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($rowTotal, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="4" class="text-right">Subtotal:</td><td class="text-right"><?php echo e(number_format($subtotal_sum, 2)); ?></td></tr>
                    <tr><td colspan="4" class="text-right">Tax(GST 18%):</td><td class="text-right"><?php echo e(number_format($tax_sum, 2)); ?></td></tr>
                    <?php if(($quote->data['discount'] ?? 0) > 0): ?>
                        <tr><td colspan="4" class="text-right">Discount:</td><td class="text-right">-<?php echo e(number_format($quote->data['discount'], 2)); ?></td></tr>
                    <?php endif; ?>
                    <tr style="font-size: 13px; color: #0088CC; font-weight: bold;"><td colspan="4" class="text-right" style="border-top: 2px solid #0088CC;">GRAND TOTAL:</td><td class="text-right" style="border-top: 2px solid #0088CC;"><?php echo e(number_format($quote->total_amount, 2)); ?></td></tr>
                </tfoot>
            </table>

            <table class="w-100" style="margin-top: 15px;">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        <!-- Dynamic Payment Terms with Fallback to Request -->
                        <div style="color: #0088CC; font-size: 10px; font-weight: bold; margin-bottom: 5px;">PAYMENT TERMS:</div>
                        <div style="font-size: 9.5px; color: #555; white-space: pre-line; padding-right: 20px;">
                            <?php 
                                $pTerms = $quote->data['payment_terms'] ?? (request('payment_terms') ?? ($settings->payment_terms ?? ''));
                            ?>
                            <?php echo nl2br(e($pTerms)); ?>

                        </div>
                    </td>
                    <td style="width: 40%;"></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 3: TERMS & CONDITIONS -->
    <div class="terms-page">
        <div style="margin-bottom: 30px;">
            <div class="blue-box">TERMS & CONDITIONS</div>
        </div>

        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td style="width: 48%; vertical-align: top; padding-right: 20px;">
                    <div class="term-cat-title">1. COMMERCIAL TERMS</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Payments once made are non-refundable under any circumstances.</li>
                        <li><span class="term-bullet">•</span> All applicable taxes (GST) will be charged extra as per govt rules.</li>
                        <li><span class="term-bullet">•</span> Project work will proceed according to the milestone payment schedule.</li>
                        <li><span class="term-bullet">•</span> Delay in milestone payments may result in temporary suspension of work.</li>
                        <li><span class="term-bullet">•</span> Ownership of the project will be transferred only after full and final payment.</li>
                    </ul>

                    <div class="term-cat-title">2. SCOPE & PROJECT EXECUTION</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Project scope is limited strictly to the features mentioned in the proposal.</li>
                        <li><span class="term-bullet">•</span> Any additional work outside the agreed scope will be billed separately.</li>
                        <li><span class="term-bullet">•</span> Additional revisions beyond agreed terms may incur extra charges.</li>
                        <li><span class="term-bullet">•</span> Change requests after project approval may impact cost and timeline.</li>
                        <li><span class="term-bullet">•</span> Delay in approvals or feedback from the client may extend the timeline.</li>
                        <li><span class="term-bullet">•</span> Projects on hold for more than six months will be considered closed.</li>
                    </ul>

                    <div class="term-cat-title">3. CLIENT RESPONSIBILITIES</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Client will provide all required content (text, images, logos).</li>
                        <li><span class="term-bullet">•</span> Client is responsible for ensuring materials don't violate copyright laws.</li>
                        <li><span class="term-bullet">•</span> Client is responsible for any legal permissions required for their business.</li>
                    </ul>

                    <div class="term-cat-title">4. THIRD-PARTY SERVICES</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> 3rd-party tools/hosting are subject to their own pricing and policies.</li>
                        <li><span class="term-bullet">•</span> Not responsible for service interruptions caused by 3rd-party providers.</li>
                        <li><span class="term-bullet">•</span> Data backup responsibility remains with the client or hosting provider.</li>
                        <li><span class="term-bullet">•</span> Websites will be tested on modern browsers and standard devices.</li>
                    </ul>

                    <div class="term-cat-title">5. MAINTENANCE & SUPPORT</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Project cost includes development and deployment only.</li>
                        <li><span class="term-bullet">•</span> Ongoing technical support requires a separate support agreement.</li>
                        <li><span class="term-bullet">•</span> Bugs reported within 30 days post-delivery will be fixed free.</li>
                    </ul>
                </td>
                <td style="width: 48%; vertical-align: top;">
                    <div class="term-cat-title">6. CONFIDENTIALITY (NDA)</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Both parties agree to maintain confidentiality of shared information.</li>
                        <li><span class="term-bullet">•</span> Confidential information shall not be disclosed to 3rd parties without consent.</li>
                    </ul>

                    <div class="term-cat-title">7. SOURCE CODE & IP</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Access to source code files will be provided only after full payment.</li>
                        <li><span class="term-bullet">•</span> Until full payment, development work remains Triserv360 property.</li>
                        <li><span class="term-bullet">•</span> Unauthorized usage of developed software may lead to legal action.</li>
                    </ul>

                    <div class="term-cat-title">8. STAFF NON-SOLICITATION</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Client shall not solict or hire Triserv360 employees directly.</li>
                        <li><span class="term-bullet">•</span> Bypassing the company by engaging team members directly is prohibited.</li>
                    </ul>

                    <div class="term-cat-title">9. OPERATIONAL POLICIES</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> Modifications made by client after delivery may affect functionality.</li>
                        <li><span class="term-bullet">•</span> Triserv360 reserves the right to showcase projects in its portfolio.</li>
                    </ul>

                    <div class="term-cat-title">10. LIABILITY & LEGAL</div>
                    <ul class="term-list">
                        <li><span class="term-bullet">•</span> In case of any dispute, jurisdiction shall be Kanpur, Uttar Pradesh, India.</li>
                    </ul>
                    
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- PAGE 4: CLIENTS LIST -->
    <div class="clients-page">
        <table style="width: 100%; margin-bottom: 25px;">
            <tr>
                <td style="width: 140px; vertical-align: middle;">
                    <div class="blue-box">OUR CLIENTS</div>
                </td>
                <td style="vertical-align: middle; padding-left: 15px;">
                    <div style="font-weight: 800; font-size: 10px; text-transform: uppercase; white-space: nowrap;">STORY OF OUR EXCELLENCE AND MORE</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: separate; border-spacing: 5px;">
            <?php 
                $logos = [
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/ab-group.png', 'name' => 'AB Group'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/acxiom.png', 'name' => 'Acxiom'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2024/01/ather.png', 'name' => 'Ather'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/forward-eye-consulting.png', 'name' => 'Forward Eye'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/rainbow.png', 'name' => 'Rainbow'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/go-green.png', 'name' => 'Go Green'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2025/01/newkanpurcityhospital.png', 'name' => 'Kanpur Hospital'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2025/09/mega.png', 'name' => 'Mega'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/dw.png', 'name' => 'DW'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/ltl.png', 'name' => 'LTL'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/scpl.png', 'name' => 'SCPL'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/the-concept-key.png', 'name' => 'Concept Key'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/zycloud.png', 'name' => 'Zycloud'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2024/01/nd.png', 'name' => 'ND'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2024/02/arna.png', 'name' => 'Arna'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2024/04/sanjay.png', 'name' => 'Sanjay'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/tb-group.png', 'name' => 'TB Group'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/omi-international.png', 'name' => 'Omi'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/reliable-home.png', 'name' => 'Reliable'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/sms.png', 'name' => 'SMS'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/softech.png', 'name' => 'Softech'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/voice.png', 'name' => 'Voice'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2024/01/decarbonization.png', 'name' => 'Decarbonization'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/bandejjia.png', 'name' => 'Bandejjia'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2025/01/penza.png', 'name' => 'Penza'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/07/eil-global.png', 'name' => 'EIL Global'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2025/01/super-house.png', 'name' => 'Super House'],
                    ['url' => 'https://triserv360.com/wp-content/uploads/2023/08/mtt.png', 'name' => 'MTT']
                ];
            ?>
            <?php $__currentLoopData = array_chunk($logos, 7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="background: #f9f9f9; border: 1px solid #E6E7E8; padding: 6px; text-align: center;">
                        <img src="<?php echo e($l['url']); ?>" style="max-width: 60px; max-height: 25px;" alt="<?php echo e($l['name']); ?>">
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

</body>
</html>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/quotation/templates/triserv.blade.php ENDPATH**/ ?>