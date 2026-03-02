@extends('quotation.templates.base')

@section('styles')
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
        background-color: {{ $settings->primary_color ?? '#6f42c1' }};
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
        background-color: {{ $settings->primary_color ?? '#6f42c1' }};
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
        background-color: {{ $settings->primary_color ?? '#6f42c1' }};
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
@endsection

@section('content')
<div class="main-border">
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td class="logo-section">
                <div style="text-align: left; padding-left: 10px;">
                    @if(isset($logo_base64) && $logo_base64)
                        <img src="{{ $logo_base64 }}" alt="Logo">
                    @elseif($settings->logo_path && file_exists(public_path('storage/'.$settings->logo_path)))
                        <img src="{{ public_path('storage/'.$settings->logo_path) }}" alt="Logo">
                    @else
                        {{-- Dummy Logo to match image --}}
                        <div style="display: inline-block;">
                            <div style="color: {{ $settings->primary_color ?? '#6f42c1' }}; font-size: 32px; font-weight: bold; line-height: 1; margin: 0;">
                                airoshelt<span style="font-size: 8px; vertical-align: super;">&reg;</span>
                            </div>
                            <div style="border-top: 1px solid #000; margin-top: 2px; padding-top: 1px;">
                                <small style="font-size: 8px; letter-spacing: 0.5px;">A venture by <strong>UNIQUE AIR CONDITIONING</strong></small>
                            </div>
                        </div>
                    @endif
                </div>
            </td>
            <td class="address-section">
                <div class="address-block">
                    <strong>{{ $settings->company_name ?? 'Triserv Solutions' }} - {{ $settings->office_name ?? 'Head Office' }} :</strong> {{ $settings->office_address ?? 'Krishna Tower Green Park Extension' }}<br>
                    {{ $settings->office_city ?? 'New Delhi' }}, {{ $settings->office_state ?? 'Delhi' }} - {{ $settings->office_pincode ?? '110016' }}<br>
                    @if($settings->email) Email: {{ $settings->email }} @else Email: info@triserv360.com @endif <br>
                    @if($settings->phone) Mobile: {{ $settings->phone }} @else Mobile: +91-9839353494 @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="header-border"></div>

    <div class="title-bar">QUOTATION</div>

    {{-- Customer Info --}}
    <table class="customer-details-table">
        <tr>
            <td style="width: 60%;">
                To,<br>
                @if($quote->customer_type == 'customer' && $quote->customer)
                    <strong>{{ $quote->customer->name }}</strong><br>
                    {!! nl2br(e($quote->customer->address ?? '')) !!}<br>
                    @if($quote->customer->gst_number) GSTIN :- &nbsp; {{ $quote->customer->gst_number }}<br> @endif
                    CONTACT : {{ $quote->customer->phone ?? '--' }}
                @elseif($quote->customer_type == 'prospect')
                    @php $prospect = \App\Models\Prospectus::find($quote->customer_id); @endphp
                    @if($prospect)
                        <strong>{{ $prospect->prospectus_name ?? 'N/A' }}</strong><br>
                        @if($prospect->address || $prospect->city || $prospect->state)
                            {{ $prospect->address ?? '' }}
                            @if($prospect->city || $prospect->state)
                                <br>{{ trim(implode(', ', array_filter([$prospect->city, $prospect->state]))) }}
                            @endif
                            <br>
                        @endif
                        CONTACT : {{ $prospect->contact_person ?? '--' }}
                        @if($prospect->contact_number)
                            ({{ $prospect->contact_number }})
                        @endif
                    @endif
                @else
                    <div style="min-height: 80px;"></div>
                @endif
            </td>
            <td style="width: 40%;">
                Qut. No.: {{ $quote->quotation_number ?? '' }}<br>
                DATE - {{ optional($quote->created_at)->format('d-M-Y') ?? '' }}
            </td>
        </tr>
    </table>

    <div class="subject-bar">
        SUBJECT : {{ $quote->data['subject'] ?? '' }}
    </div>

    {{-- Items Table --}}
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
            @php $sr = 1; @endphp
            @if(isset($quote->data['products']) && count($quote->data['products']) > 0)
                @foreach($quote->data['products'] as $p)
                <tr>
                    <td>{{ $sr++ }}</td>
                    <td class="text-left">
                        {{ $p['product_name'] ?? ($p['name'] ?? '--') }}
                        @if(!empty($p['remark']))
                            <br><small style="font-size: 9px; color: #666;">({{ $p['remark'] }})</small>
                        @endif
                    </td>
                    <td>{{ $p['quantity'] ?? 1 }}</td>
                    <td>{{ $p['unit'] ?? 'Nos' }}</td>
                    <td>{{ number_format($p['price'] ?? 0, 2) }}</td>
                    <td>{{ number_format(($p['quantity'] ?? 1) * ($p['price'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="6" style="padding: 20px; color: #999;">No products listed</td></tr>
            @endif

            @php
                $gross_subtotal = 0;
                foreach(($quote->data['products'] ?? []) as $p) {
                    $gross_subtotal += ($p['quantity'] ?? 1) * ($p['price'] ?? 0);
                }
                $net_taxable = $quote->total_amount ?? 0;
                $discount_val = $gross_subtotal - $net_taxable;
            @endphp

            @if($discount_val > 0)
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Gross Total</td>
                <td>{{ number_format($gross_subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Discount</td>
                <td>{{ number_format($discount_val, 2) }}</td>
            </tr>
            @endif

            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Basic</td>
                <td>{{ number_format($net_taxable, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">GST 18%</td>
                <td>{{ number_format($net_taxable * 0.18, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: none;"></td>
                <td class="total-label-cell">Total</td>
                <td>{{ number_format($net_taxable * 1.18, 2) }}</td>
            </tr>
            
            {{-- Section B (To be made dynamic later) --}}
            {{-- <tr class="sub-section-row"><td colspan="6">(B) LOW SIDE</td></tr> --}}
        </tbody>
    </table>

    {{-- Bank Details --}}
    <div class="bank-details">
        Bank Details: &nbsp; {{ $settings->bank_details ?? '' }}
    </div>

    {{-- Payment Terms --}}
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

    <div class="footer-section">
        <strong>THANKS & REGARDS</strong>
    </div>

    <div class="footer-brand">
        AIROSHELT A Venture by UNIQUE AIR CONDITIONING
    </div>
</div>
@endsection
