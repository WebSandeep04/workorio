<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Triserv360 | Quotation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* PDF specific resets */
        @page {
            margin: 0;
            size: a4;
        }
        body {
            font-family: 'Inter', sans-serif !important;
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
            padding: 0px 10px 5px;
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
            margin-bottom: 5px;
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
            margin: 0 0 10px 0;
        }
        .term-list li {
            font-size: 10.5px;
            color: #444444;
            margin-bottom: 3px;
            padding-left: 12px;
            position: relative;
            line-height: 1.0;
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
    height: auto;
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
    left: 56%;
    width: 500px; /* same as orange bar */
    margin-left: -250px; /* half of width */
    text-align: center;
}

        .text-overlay2 {
    position: absolute;
    top: 143px;
    left: 0px;
    width: 100%;  /* adjust */
    text-align: center;
  
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
      padding: 10mm;
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

    .payment-page-title {
      font-size: 20px;
      color: #0d558a;
      font-weight: 800;
      margin: 0 0 8px;
    }

    .payment-page-subtitle {
      font-size: 14px;
      color: #617288;
      margin: -5px 0 18px;
    }

    .payment-card-title {
      font-size: 12px;
      color: #0d558a;
      font-weight: 800; 
      text-transform: uppercase;
      margin-bottom: 12px;
    }

    .payment-card-title span {
      display: inline-block;
      width: 8px;
      height: 8px;
      background: #ff7b17;
      border-radius: 2px;
      margin-right: 7px;
    }

    .qr-demo-box {
      width: 128px;
      height: 128px;
      padding: 8px;
      border: 1px solid #e5edf5;
      border-radius: 12px;
      background: #ffffff;
      margin: 0 auto 10px;
    }

    .qr-demo-box img {
      width: 112px;
      height: 112px;
      display: block;
    }

    .payment-upi {
      text-align: center;
      font-size: 9px;
      color: #607085;
      margin-bottom: 10px;
    }

    .payment-cta {
      background: #0d558a;
      color: #ffffff;
      border-radius: 16px;
      padding: 10px 12px;
      text-align: center;
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.02em;
    }

    .account-note {
      text-align: center;
      color: #617288;
      font-size: 10px;
      line-height: 1.4;
      margin-top: 12px;
    }

    .contact-line {
      font-size: 11px;
      color: #203040;
      line-height: 1.55;
      margin: 0 0 8px;
    }

    .contact-label {
      color: #617288;
      font-weight: 700;
    }

    .social-row {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }

    .social-icon {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #ff7b17;
      color: #ffffff;
      font-size: 8px;
      font-weight: 800;
      text-align: center;
      line-height: 22px;
    }

    .social-text {
      font-size: 11px;
      color: #203040;
      font-weight: 700;
      padding-left: 8px;
    }

    .payment-detail-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .payment-detail-table td {
      font-size: 10px;
      color: #203040;
      line-height: 1.35;
      padding: 5px 0;
      border-bottom: 1px solid #edf2f7;
      vertical-align: top;
    }

    .payment-detail-table td:first-child {
      width: 42%;
      color: #617288;
      font-weight: 700;
      padding-right: 8px;
    }

    .mini-info-card {
      border: 1px solid #e8eff6;
      border-radius: 14px;
      background: #f8fbff;
      padding: 10px 12px;
      margin-top: 12px;
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

    @php
        if (!function_exists('amountToWords')) {
            function amountToWords($number) {
                $number = round($number, 2);
                $decimal = round($number - ($no = floor($number)), 2) * 100;
                $hundred = null;
                $digits_length = strlen($no);
                $i = 0;
                $str = array();
                $words = array(0 => '', 1 => 'one', 2 => 'two',
                    3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                    7 => 'seven', 8 => 'eight', 9 => 'nine',
                    10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                    13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                    16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                    19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                    40 => 'forty', 50 => 'fifty', 60 => 'sixty',
                    70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
                $digits = array('', 'hundred','thousand','lakh', 'crore');
                while( $i < $digits_length ) {
                    $divider = ($i == 2) ? 10 : 100;
                    $number = floor($no % $divider);
                    $no = floor($no / $divider);
                    $i += $divider == 10 ? 1 : 2;
                    if ($number) {
                        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                        $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                    } else $str[] = null;
                }
                $Rupees = implode('', array_reverse($str));
                
                $paise = '';
                if ($decimal > 0) {
                    if ($decimal < 21) {
                        $paise = $words[$decimal];
                    } else {
                        $paise = $words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10];
                    }
                    $paise = " and " . $paise . ' Paise';
                }
                
                return ($Rupees ? 'INR ' . ucwords($Rupees) : '') . ucwords($paise) . ' Only';
            }
        }
    @endphp
    <!-- PAGE 1: COVER PAGE (Hero) -->
    <!-- <div class="banner-container">
    
        <img src="https://app.workorio.com/clients/hero.jpeg" class="banner-img">

        <div class="top-bar">
            <table class="w-100">
                <tr>
                    <td style="width: 50%;">
                        @if(isset($logo_base64) && $logo_base64)
                            <img src="{{ $logo_base64 }}" class="main-logo">
                        @else
                            <img src="https://triserv360.com/wp-content/uploads/2023/04/logo.png" class="main-logo">
                        @endif
                    </td>
                    <td style="width: 50%; text-align: left;" class="contact-info">
                        Qut. No.: {{ $quote->quotation_number ?? '' }} @if($quote->version > 1) (v{{ $quote->version }}) @endif<br>
                        <span>Website: {{ $settings->website ?? '' }}</span>
                    </td>
                </tr>
            </table>
        </div> -->

        <!-- <div class ="text-overlay2" style="font-size: 22px; color: #000; font-weight: 700; letter-spacing: 0.5px;">{{ ucwords(strtolower($quote->data['subject'] ?? 'Project Estimate & Scope')) }}</div>

        <div class="text-overlay" style="width: 400px;">
            <div style="color: #fff; font-size: 24px; font-weight: bold;">
                <span >
                    @if($quote->customer_type == 'customer')
                        {{ optional($quote->customer)->name ?? 'N/A' }}
                    @else
                        @php 
                            $prospect = $quote->prospect ?? \App\Models\Prospectus::find($quote->prospect_id); 
                        @endphp
                        {{ optional($prospect)->prospectus_name ?? 'N/A' }}
                    @endif
                </span>
            </div>
        </div>

    </div> --> 
    @php
        $bgSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none">'
               . '<rect width="100" height="100" fill="#ffffff"/>'
               . '<polygon points="100,0 100,0 100,100 0,100" fill="#d3e6ef" opacity="0.55"/>'
               . '<polygon points="110,0 100,0 100,100 22,100" fill="#c2d8e6" opacity="0.45"/>'
               . '</svg>';
        $bgSvgBase64 = base64_encode($bgSvg);
    @endphp

    <div class="banner-container" style="position:relative; padding:30px 40px 0px; background:#ffffff; text-align:center; overflow:hidden;">

        <!-- SVG Diagonal Background (DomPDF compatible) -->
        <img src="data:image/svg+xml;base64,{{ $bgSvgBase64 }}"
             style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:0;"
             alt="">

        <!-- Content -->
        <div style="position:relative; z-index:1;">


    <table style="width:100%; margin-bottom:40px;">
    <tr>
        <td style="width:40%; vertical-align:middle;">
            <img src="https://app.workorio.com/clients/logo/logo.png"
                 style="height:40px;">
        </td>

        <td style="width:40%; text-align:right; vertical-align:middle;">
            <div style="font-size:14px; color: #000;">
                <strong>Phone:</strong> +91-7068171964 | +91-9839353494<br>
                <strong>Website:</strong> www.triserv360.com
            </div>
        </td>
    </tr>
</table>

    <!-- Proposal Text -->
    <div style="font-size:20px; font-weight:400; color:#333; margin-top:-15px; magrin-bottom:-20px;">
        Proposal for
    </div>

    <div style="font-size:22px; font-weight:600; margin-top:-10px; margin-bottom:40px:">
        {{ ucwords(strtolower($quote->data['subject'] ?? 'Project Estimate & Scope')) }}
    </div>

    <!-- Client Name -->
    <div style="text-align:center; margin-top:0;">
    <div style="
        display:inline-block;
        background:#ff8c1a;
        color:#fff;
        border-radius:30px;
        font-size:32px;
        font-weight:700;
        padding:15px 80px;
        text-align:center;
        margin-top:10px;
    ">
       <div style="margin-top:-30px; margin-bottom:-13px; padding:0px;">
                    @if($quote->customer_type == 'customer')
                        {{ optional($quote->customer)->name ?? 'N/A' }}
                    @else
                        @php 
                            $prospect = $quote->prospect ?? \App\Models\Prospectus::find($quote->prospect_id); 
                        @endphp
                        {{ optional($prospect)->prospectus_name ?? 'N/A' }}
                    @endif
    </div>
     </div>
</div>

    <!-- Main Heading -->
    <div style="
        margin-top:10px;
        font-size:35px;
        line-height:1.2;
        font-weight:700;
        color:#111827;
        border-bottom: 1px solid #999;
        padding-bottom:10px;
    ">
        Helping Businesses Achieve Excellence
    </div>

    <h2 style="
        text-align:center;
        font-size:30px;
        font-weight:700;
        color:#1f2937;
        margin-bottom:0px;
        margin-top:0px;
        font-family: 'Inter', sans-serif !important;">
        About Us
    </h2>
    <div style="text-align:center; margin-bottom:-10px;">
        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyOC4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyNDguNiAyNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQ4LjYgMjQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOiMyMTk4Rjk7fQ0KPC9zdHlsZT4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xOTguNSw4LjhjLTE0LjctMi4xLTI5LjQtMi43LTQ0LjItMy43Yy0yMC0xLjMtNDAtMC4zLTU5LjksMC40QzcyLjgsNi4yLDUxLjIsNy45LDI5LjcsOS45DQoJYy04LjQsMC44LTE2LjksMS41LTI1LjQsMi4xQzIuNiwxMi4yLDAuMSwxMi41LDAsOS45Yy0wLjEtMi41LDIuMy0yLjUsNC0yLjdjMjItMiw0NC4xLTMuOCw2Ni4yLTUuM2MyNi42LTEuOSw1My4zLTIuMiw3OS45LTEuOA0KCWMzMS41LDAuNSw2Mi44LDMuNyw5My42LDEwLjljMi4yLDAuNSw1LjQsMC41LDQuOCwzLjdjLTAuNiwzLjEtMy42LDIuMy01LjksMi4xYy0xNy4zLTAuOS0zNC42LTItNTEuOC0yLjcNCgljLTguNC0wLjMtMTYuOS0wLjEtMjUuNC0wLjVjMTQuNiwxLjQsMjkuMiwyLjksNDMuOCw0LjNjMi42LDAuMyw1LjMsMC43LDcuOSwxLjFjMS42LDAuMywzLjcsMC41LDMuNCwyLjhjLTAuMywyLjItMi4zLDIuMS00LDINCgljLTYtMC42LTExLjktMS4yLTE3LjktMS44Yy0zNi4zLTQuMS03Mi44LTQuNC0xMDkuMy0zLjdjLTExLDAuMi0yMS45LDEuMy0zMi45LDEuOWMtMS44LDAuMS00LjcsMC44LTQuOS0yDQoJYy0wLjItMi43LDIuNy0yLjUsNC42LTIuOGMyMC4yLTMuNCw0MC42LTUuMyw2MS02LjVjMTgtMSwzNi0wLjgsNTMuOS0wLjdDMTgwLjIsOC40LDE4OS40LDkuNiwxOTguNSw4Ljh6Ii8+DQo8L3N2Zz4NCg=="
             style="width:180px; height:18px;" alt="underline">
    </div>


    <p style="
        text-align: justify;
        font-size:16px;
        line-height:1.1;
        color:#374151;
        margin-bottom:15px;">
        <strong>Triserv 360</strong> is an India-based emerging Digital Transformation,
        IT Infrastructure Solutions & IT Consulting company specializing in providing
        businesses with Custom Software Development, ERP Development,
        Software Maintenance, Mobile App Development, ERP Implementation,
        IT Management Consultancy, and IT Infrastructure & Cloud Services.
    <br> <br>
        <strong>Triserv 360</strong> helps alleviate customer's pain areas by leveraging
        industry best practices and enabling them to solve problems strategically.
        As a technology partner we align our expertise with customer's business goals.
        Our digital transformation leaders have successfully led various projects in
        Asia Pacific & MENA region. We have also implemented several digital solutions
        to transform business processes, thereby helping maximize ROI.
    </p>

    <p style="
        text-align:center;
        font-size:17px;
        line-height:1.;
        color:#374151;">
        <strong>Triserv 360</strong> has various joint ventures with industry leaders
        around the globe.
    </p>

     

    <table style="width:100%; border-collapse:collapse; margin-top:30px; ">
    <tr>
        <td style="width:50%; text-align:left; padding:5px; vertical-align:middle;">
            <img src="https://app.workorio.com/clients/workorio-logo.png"
                 style="max-width:200px;" alt="Workorio Logo">
        </td>
        <td style="width:50%; text-align:right; padding:5px 10px; vertical-align:middle;">
            <span style="font-size:13px; color:#374151; vertical-align:middle;">powered by -</span>
            <img src="https://app.workorio.com/clients/triserv-logo.png"
                 style="max-width:120px; vertical-align:middle;" alt="Triserv Logo">
        </td>
    </tr>
    </table>
    
    <p style="
        text-align:left;
        font-size:17px;
        line-height:1.;
        color:#000; margin-bottom:-10px; margin-top:10px">
        <strong>Everything App for Your Business
    </p>

<table style="width:100%; border-collapse:separate; border-spacing:8px; margin-top:15px; margin-bottom:20px;">
    <!-- Row 1 -->
    <tr>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/lead.png" style="width:30px; height:30px;" alt="Lead">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Lead Management
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/quotation.png" style="width:30px; height:30px;" alt="Quotation">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Quotation Management
                    </td>
                </tr>
            </table>
        </td>
         
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:40px; padding:12px 0px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/subscription.png" style="width:30px; height:30px;" alt="Subscription">
                    </td>
                    <td style="padding:12px 0px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Subscription Management
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- Row 2 -->
    <tr>
         
         
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#eaf4fb; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/task.png" style="width:30px; height:30px;" alt="Task">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Task Management
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/followup.png" style="width:30px; height:30px;" alt="Follow-up">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Employee Tracking
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#eaf4fb; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/telecalling.png" style="width:30px; height:30px;" alt="Telecalling">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Employee Attendance
                    </td>
                </tr>
            </table>
        </td>


    </tr>
    <!-- Row 3 -->
    <tr>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/testing.png" style="width:30px; height:30px;" alt="Follow-up">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Time Sheet
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#eaf4fb; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/asset.png" style="width:30px; height:30px;" alt="Telecalling">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Asset Management
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:33.33%; vertical-align:middle; padding:0;">
            <table style="width:100%; border-collapse:collapse; background:#f3f4f6; border-radius:8px;">
                <tr>
                    <td style="width:45px; padding:12px 8px 12px 12px; vertical-align:middle;">
                        <img src="https://app.workorio.com/clients/petty.png" style="width:30px; height:30px;" alt="Contact">
                    </td>
                    <td style="padding:12px 12px 12px 0; vertical-align:middle; font-size:13px; font-weight:700; color:#1e293b;">
                        Petty Cash
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<p style="
        text-align:left;
        font-size:17px;
        line-height:1.;
        color:#000; margin-bottom:-10px; margin-top:00px">
        <strong>Seamless Lead Integration Across  </p>
    
<table style="width:100%; border-collapse:collapse; margin-top:20px; ">
    <tr>
        <td style="width:75%; text-align:left; padding:5px; vertical-align:middle;">
            <img src="https://app.workorio.com/clients/indiamart.png"
                 style="max-width:80px;"   > <img src="https://app.workorio.com/clients/tradeIndia.png"
                 style="max-width:80px; "> <img src="https://app.workorio.com/clients/meta.png"
                 style="max-width:80px; " > <img src="https://app.workorio.com/clients/form.png"
                 style="max-width:80px; ">
        </td>
        <td style="width:25%; text-align:right; padding:5px 10px; vertical-align:middle;">
           
        </td>
       
    </tr>
    </table>

    </div> {{-- end content z-index wrapper --}}

</div>


                
                 
    <div class="page-break"></div>

    <!-- <div>
        <img src="https://app.workorio.com/clients/service_page2.png" class="banner-img">
    </div> -->

    @php
        $solutionHandPath = public_path('img/solution-hand-demo.png');
        $solutionHandSrc = file_exists($solutionHandPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($solutionHandPath))
            : '';
    @endphp

    <div style=" background-color:#E6EBEE; position:relative; overflow:hidden;">

    <h1 style="text-align:left; font-size:34px; padding: 40px 0 10px 40px; margin:0 0 10px; color:#1e293b; font-weight:700;">
        Solutions for growth
    </h1>

    <div style="height:1px; background:#7c8794; margin-bottom:20px;"></div>

    <table style="width:100%; padding:0 40px 0; border-collapse:separate; border-spacing:0 18px; position:relative; z-index:2;">

        <tr>
            <td style="width:50%; vertical-align:top; padding:0 14px 0 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; margin-right: 10px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/software.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Software and ERP Solutions</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Custom software development</li>
                                <li>ERP development and implementation</li>
                                <li>System integration</li>
                                <li>Application modernization</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:50%; vertical-align:top; padding:0 0 0 14px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/ecommerce.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Web and Ecommerce</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Website design</li>
                                <li>Web development</li>
                                <li>Ecommerce development</li>
                                <li>Landing page development</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="vertical-align:top; padding:0 14px 0 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/app.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Mobile Applications</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Mobile app development</li>
                                <li>Cross-platform app development</li>
                                <li>App maintenance and support</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>

            <td style="vertical-align:top; padding:0 0 0 14px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/box-megaphone.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Digital Marketing and Growth</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Search engine optimization</li>
                                <li>Social media management</li>
                                <li>Performance marketing</li>
                                <li>LinkedIn outreach</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="vertical-align:top; padding:0 14px 0 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/creative.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Creative and Content</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Video editing</li>
                                <li>Motion graphics and animation</li>
                                <li>Product photo editing</li>
                                <li>Photo retouching</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>

            <td style="vertical-align:top; padding:0 0 0 14px;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/users.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">Consulting and Transformation</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>Digital transformation consulting</li>
                                <li>Technology consulting</li>
                                <li>IT management</li>
                                <li>Cybersecurity consulting</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="vertical-align:top; padding:0 14px 0 0;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:48px; vertical-align:top;">
                            <div style="width:36px; height:36px; background:#ffffff; border-radius:12px; text-align:center; padding:8px; box-shadow:0 6px 14px rgba(15, 23, 42, 0.08);">
                                <img src="https://app.workorio.com/clients/stack.png" style="width:20px; height:20px; padding-top:8px;" alt="icon">
                            </div>
                        </td>
                        <td style="vertical-align:top; padding-left:10px;">
                            <div style="font-size:18px; font-weight:700; color:#111827; margin-bottom:8px;">White-label Partnerships</div>
                            <ul style="margin:0; padding-left:18px; color:#334155; font-size:14px; line-height:1.2;">
                                <li>White-label web development</li>
                                <li>White-label app development</li>
                                <li>White-label marketing support</li>
                                <li>Dedicated remote teams</li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>

            <td style="vertical-align:top; padding:0 0 0 14px;"></td>
        </tr>

    </table>

      <div style="width:100%; height:auto;  ">
        <img src="https://app.workorio.com/clients/solution-hand-demo.png" style="width:100%;   margin-top:-20px  ;">
    </div>
</div>
   


 
   

    <div class="page-break"></div>


    <!-- PAGE 2: QUOTATION DETAILS -->
    <div class="">
        <div class="quotation-card">
            <table class="quote-doc-header">
                <tr>
                    <td>
                        <div class="doc-title" style="color: #0088CC;">Quotation/Proposed Commercials</div>
                        
                    </td>
                    <td class="text-right" style="font-size: 10px;">
                        <div><strong>Quote #:</strong> {{ $quote->quotation_number }} @if($quote->version > 1) (v{{ $quote->version }}) @endif</div>
                        <div style="margin: 2px 0;"><strong>Date:</strong> {{ $quote->created_at->format('M d, Y') }}</div>
                        <div><strong>Valid Till:</strong> {{ $quote->created_at->copy()->addDays(15)->format('M d, Y') }}</div>
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
                    @php 
                        $products = $quote->data['products'] ?? []; 
                        $subtotal_sum = 0;
                    @endphp
                    @foreach($products as $index => $item)
                        @php 
                            $price = $item['price'] ?? 0;
                            $qty = $item['quantity'] ?? 1;
                            $disc = $item['discount'] ?? 0;
                            $discType = $item['discount_type'] ?? 'percentage';
                            
                            $rowBase = $price * $qty;
                            $rowDiscAmount = ($discType === 'percentage') ? ($rowBase * ($disc / 100)) : $disc;
                            $lineAmount = $rowBase - $rowDiscAmount;
                            $subtotal_sum += $lineAmount;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ optional(\App\Models\SalesProduct::find($item['product_id'] ?? null))->product_name ?? ($item['product_name'] ?? ($item['product_id'] ?? '--')) }}</strong><br>
                                <div style="font-size: 8.5px; color: #777; word-wrap: break-word; word-break: break-all; line-height: 1.2; max-width: 350px;">{{ $item['remark'] ?? '' }}</div>
                            </td>
                            <td class="text-right">{{ $qty }} {{ $item['unit'] ?? 'Nos' }}</td>
                            <td class="text-right">{{ number_format($price, 2) }}</td>
                            <td class="text-right">
                                @if($disc > 0)
                                    {{ $discType === 'percentage' ? $disc.'%' : number_format($disc, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">{{ number_format($lineAmount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="5" class="text-right">Subtotal:</td><td class="text-right">{{ number_format($subtotal_sum, 2) }}</td></tr>
                    @php 
                        $discount = $quote->data['discount'] ?? 0;
                        $taxable = max(0, $subtotal_sum - $discount);
                        $gst = round($taxable * 0.18, 2);
                        $grandTotal = $taxable + $gst;
                    @endphp
                    @if($discount > 0)
                        <tr><td colspan="5" class="text-right">Additional Discount:</td><td class="text-right">-{{ number_format($discount, 2) }}</td></tr>
                        <tr><td colspan="5" class="text-right">Taxable Amount:</td><td class="text-right">{{ number_format($taxable, 2) }}</td></tr>
                    @endif
                    <tr><td colspan="5" class="text-right">Tax (GST 18%):</td><td class="text-right">{{ number_format($gst, 2) }}</td></tr>
                    <tr style="font-size: 13px; color: #0088CC; font-weight: bold;"><td colspan="5" class="text-right" style="border-top: 2px solid #0088CC;">GRAND TOTAL:</td><td class="text-right" style="border-top: 2px solid #0088CC;">{{ number_format($grandTotal, 2) }}</td></tr>
                    <tr style="font-size: 10px; font-weight: bold;">
                        <td colspan="6" class="text-right" style="padding-top: 5px;">
                            <span style="color: #555;">Amount in Words:</span> {{ amountToWords($grandTotal) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    

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
                            @php 
                                $pTerms = $quote->data['payment_terms'] ?? (request('payment_terms') ?? ($settings->payment_terms ?? ''));
                            @endphp
                            {!! nl2br(e($pTerms)) !!}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

</table>


    <div class="page-break"></div>

    <div class="payment-page" style="margin-top:-35px; margin-bottom:10px;">
        <h2  >Payment, Proposal Validity & Contact Details</h2>
        

        <div class="payment-card" style="width:90%; margin-bottom:10px; margin-top:-10px;"> 
                        <div class="payment-card-title" style="margin-bottom:-10px;"><span></span>Proposal Validity</div>
                         

                        <div class="mini-info-card"> 
                            <div style="font-size: 13px; color: #203040; line-height: 1.2;">
                                This proposal is valid for <strong>15 days</strong> from the quotation date.<br>
                                <strong>Valid Till:</strong> {{ $quote->created_at->copy()->addDays(15)->format('M d, Y') }}
                            </div>
                        </div>

                        <br>
                        <div class="payment-card-title" style="margin-top:-20px; margin-bottom: -10px;"><span></span>Acceptance & Next Steps</div>
                         

                        <div class="mini-info-card"> 
                            <div style="font-size: 13px; color: #203040; line-height: 1.2;">
                                <strong>Let's Move Forward</strong> <br>
                                We look forward to partnering for this project. Please feel free to reach out for any further clarifications before approval.
                            </div>
                        </div>

                    </div>

      

        <div class="payment-card" style="width:90%; margin-bottom:10px;">
                <div class="payment-card-title" style=" margin-top:-5px;">
                    <span></span>Account Details
                </div>
                            <div style="font-size:18px; font-weight:700;  margin-bottom:-10px; margin-top:-15px; color:#203040;   ">
                                Triserv 360 Business Solutions Private Limited
                            </div>

                <table style="width:100%; border-collapse:collapse; margin-top:10px;">
                    <tr>

 
                        <!-- Left Column -->
                        <td style="width:50%; vertical-align:top; padding-right:40px;">

                           

                            <div style="font-size:12px; line-height:1.0; color:#203040;">
                                <strong>Bank -</strong> ICICI Bank<br>
                                <strong>Branch -</strong> Mall Road, Kanpur<br>
                                <strong>Account No -</strong> 628805026732<br>
                                <strong>IFSC Code -</strong> ICIC0006288<br>
                                <strong>A/C Type -</strong> Current<br>
                                <strong>SWIFT -</strong> ICICINBBCTS
                            </div>

                        </td>

                        <!-- Right Column -->
                        <td style="width:50%; vertical-align:top; padding-left:40px;">

                            <div style="
                                border-left:1px solid #d9e2ec;
                                padding-left:40px;
                                font-size:12px;
                                line-height:1.0;
                                color:#203040;
                                margin-top:10px;
                            ">
                                <strong>PAN -</strong> AAJCT3301R<br>
                                <strong>TAN -</strong> KNPT02038B<br>
                                <strong>GST -</strong> 09AAJCT3301R1ZV
                            </div>

                        </td>
                    </tr>
                </table>
            </div>


       <table style="width: 99%; margin-left:-10px; border-collapse: separate; border-spacing: 10px 0;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="payment-card" style="min-height: 500px;">
                         <div class="payment-card-title">
                    <span></span>Payment QR Code
                </div>
                        
                         
                         <img src="https://app.workorio.com/clients/payment-qr.jpg" style="width:100%;   margin-top:-1px  ;">

                       

                        
                    </div>
                </td>

                <td style="width: 50%; vertical-align: top;">
                    <div class="address-card" style="min-height:500px;">

                <div class="payment-card-title">
                    <span></span>Address
                </div>

                <!-- Office Address -->
                <div style="font-size:13px; color:#203040; line-height:1.2;">
                    710, 7th Floor, 15/63 Krishna Tower 
                    Opposite Green Park Stadium,
                    Kanpur, Uttar Pradesh - 208001
                </div>

                <div style="border-top:1px solid #edf2f7; margin:15px 0;"></div>

                <!-- Contact Information -->
                <div style="font-size:14px; color:#203040; line-height:1.0;">

                    <div>
                        <strong style="color:#0d558a;">Contact Person</strong><br> Shamshad Ahmad
                    </div>

                    <div style="margin-top:8px;">
                        <strong style="color:#0d558a;">Phone</strong><br>
                        +91-7068171964 &nbsp; | &nbsp; +91-9839353494
                    </div>

                    <div style="margin-top:8px;">
                        <strong style="color:#0d558a;">Email</strong><br>
                        shamshad@triserv360.com
                    </div>

                </div>

    <div style="border-top:1px solid #edf2f7; margin:15px 0;"></div>

    <!-- Website Section -->
    <table style="width:100%; border-collapse:collapse;">
        <tr>

            <td style="width:90px; vertical-align:middle;">
                <div style="
                    width:80px;
                    height:80px;
                    border:1px solid #dbe7f3;
                    border-radius:10px;
                    padding:5px;
                ">
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=https://www.triserv360.com"
                        style="width:78px;height:78px;"
                    >
                </div>
            </td>

            <td style="vertical-align:middle; padding-left:10px;">

                <div style="
                    font-size:11px;
                    color:#617288;
                    font-weight:700;
                    text-transform:uppercase;
                ">
                    Visit Our Website
                </div>

                <div style="
                    font-size:16px;
                    font-weight:700;
                    color:#0d558a;
                    margin-top:4px;
                ">
                    www.triserv360.com
                </div>

            </td>

        </tr>
    </table>

    <div style="border-top:1px solid #edf2f7; margin:18px 0;"></div>

    <!-- Social Media -->
<div style="text-align:center; margin-top:20px; margin-bottom:10px;">
    <h3 style="font-size:18px; color:#0d558a; margin:0;">Connect with us</h3>
</div>

    <table style="border-collapse:collapse;">
        <tr>

            <!-- LinkedIn -->
            <td style="padding-right:20px;">
                <table>
                    <tr>
                        <td>
                            <div style="
                                width:28px;
                                height:28px;
                                border-radius:50%;
                                background:#0077B5;
                                color:#fff;
                                text-align:center;
                                line-height:28px;
                                font-size:10px;
                                font-weight:bold;
                            ">
                                <a href="https://www.linkedin.com/company/triserv360/" target="_blank"><img src="https://app.workorio.com/clients/linkedin.png" style="width:50%; margin-top:6px"></a>
                            </div>
                        </td>
                        <td style="
                            padding-left:8px;
                            font-size:11px;
                            font-weight:600; font-family: 'Inter', sans-serif !important;
                        ">
                            <a href="https://www.linkedin.com/company/triserv360/" target="_blank" style="color:#0d558a; text-decoration:none;">triserv360</a>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Instagram -->
            <td style="padding-right:20px;">
                <table>
                    <tr>
                        <td>
                            <div style="
                                width:28px;
                                height:28px;
                                border-radius:50%;
                                background:#E4405F;
                                color:#fff;
                                text-align:center;
                                line-height:28px;
                                font-size:10px;
                                font-weight:bold;
                            ">
                                <a href="https://www.instagram.com/triserv360/" target="_blank"><img src="https://app.workorio.com/clients/instagram.png" style="width:50%; margin-top:6px"></a>
                            </div>
                        </td>
                        <td style="
                            padding-left:8px;
                            font-size:11px;
                            font-weight:600; font-family: 'Inter', sans-serif !important;
                        ">
                            <a href="https://www.instagram.com/triserv360/" target="_blank" style="color:#0d558a; text-decoration:none;">triserv360</a>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Facebook -->
            <td>
                <table>
                    <tr>
                        <td>
                            <div style="
                                width:28px;
                                height:28px;
                                border-radius:50%;
                                background:#1877F2;
                                color:#fff;
                                text-align:center;
                                line-height:28px;
                                font-size:10px;
                                font-weight:bold;
                            ">
                                <a href="https://www.facebook.com/Triserv360" target="_blank"><img src="https://app.workorio.com/clients/facebook.png" style="width:50%; margin-top:6px"></a>
                            </div>
                        </td>
                        <td style="
                            padding-left:8px;
                            font-size:11px;
                            font-weight:600; font-family: 'Inter', sans-serif !important;
                        ">
                            <a href="https://www.facebook.com/Triserv360" target="_blank" style="color:#0d558a; text-decoration:none;">triserv360</a>
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
    </table>

   

</div>
        


        </td>
            </tr>
        </table>
		
		


		
		
    </div>




    <!-- PAGE 4: CLIENTS LIST -->
         <div class="clients-page" style="padding: 30px; margin-bottom: 4px !important; ">
            <table style="width: 100%; margin-bottom: 25px;">
                <tr>
                    <td style="width: 140px; vertical-align: middle;">
                        <div class="blue-box" style="margin-bottom: 4px !important;">Trusted By Businesses & Partners</div>
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: separate; border-spacing: 5px; margin-top: 8px;">
                @php 
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
                @endphp
                @foreach(array_chunk($logos, 6) as $row)
                <tr>
                    @foreach($row as $l)
                        <td style="background: #f9f9f9; border: 1px solid #E6E7E8; padding: 6px; text-align: center;">
                            <img src="{{ $l['url'] }}" style="max-width: 100%; max-height: 40px;" alt="{{ $l['name'] }}">
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </table>
        </div> 


      <div class="page-break"></div>

    <!-- PAGE 3: TERMS & CONDITIONS -->
    <div class="terms-page" style="  margin-top:-20px;">
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

                    <div class="term-cat-title">4. Third-Party Services</div>
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

         <div style="text-align: center; margin-top: 5px;">
            <div class="term-cat-title">Acceptance of Proposal</div>
            <p style="font-size: 11px">Approval of this proposal or payment of the advance amount will be considered acceptance of all the terms and conditions mentioned above.</p>
         </div>

    </div>

    <div style="text-align: center; margin-top: 0px; font-size: 11px; color: #777; width: 100%;">
        --- END OF DOCUMENT ---
    </div>

</body>
</html>
