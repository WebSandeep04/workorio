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
        padding: 10px;
        vertical-align: middle;
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
        color: #ce5a16;
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
                    <strong style="color: {{ $settings->primary_color ?? '#6f42c1' }};">{{ strtoupper($settings->office_name ?? 'CORPORATE OFFICE') }} :</strong> 
                    {{ $settings->office_address ?? 'OFFICE NO 102 1ST FLOOR, H & M ROYAL WING 4, KONDHWA KATRAJ ROAD, SURVEY NUMBER :18/19 OPP TALAB FACTORY' }}<br>
                    {{ strtoupper($settings->office_city ?? 'PUNE') }}, {{ strtoupper($settings->office_state ?? 'MAHARASHTRA') }} - {{ $settings->office_pincode ?? '411048' }}<br>
                    <strong>Email:</strong> {{ $settings->email ?? 'sales@airoshelt.com / uniqueacprojects@gmail.com' }}<br>
                    <strong>Mobile:</strong> {{ $settings->phone ?? '8448441066' }} &nbsp;&nbsp;&nbsp;&nbsp; <strong>Tel:</strong> 020-46740006
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
                    @php 
                        $prospect = $quote->prospect ?? \App\Models\Prospectus::find($quote->prospect_id); 
                        $loc = [];
                        if ($prospect && $prospect->city) $loc[] = $prospect->city->city_name;
                        if ($prospect && $prospect->state) $loc[] = $prospect->state->state_name;
                    @endphp
                    @if($prospect)
                        <strong>{{ $prospect->prospectus_name ?? 'N/A' }}</strong><br>
                        @if($prospect->address || !empty($loc))
                            {{ $prospect->address ?? '' }}
                            @if(!empty($loc))
                                <br>{{ implode(', ', $loc) }}
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
                <th style="width: 6%;">SR NO.</th>
                <th style="width: 40%;">DESCRIPTION</th>
                <th style="width: 8%;">QTY</th>
                <th style="width: 8%;">UNIT</th>
                <th style="width: 12%;">RATE</th>
                <th style="width: 12%;">DISC</th>
                <th style="width: 14%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @php $sr = 1; $subtotal_sum = 0; @endphp
            @if(isset($quote->data['products']) && count($quote->data['products']) > 0)
                @foreach($quote->data['products'] as $p)
                @php
                    $price = $p['price'] ?? 0;
                    $qty = $p['quantity'] ?? 1;
                    $disc = $p['discount'] ?? 0;
                    $discType = $p['discount_type'] ?? 'percentage';
                    
                    $rowBase = $price * $qty;
                    $rowDiscAmount = ($discType === 'percentage') ? ($rowBase * ($disc / 100)) : $disc;
                    $lineAmount = $rowBase - $rowDiscAmount;
                    $subtotal_sum += $lineAmount;
                @endphp
                <tr>
                    <td>{{ $sr++ }}</td>
                    <td class="text-left">
                        {{ optional(\App\Models\SalesProduct::find($p['product_id'] ?? null))->product_name ?? ($p['product_name'] ?? ($p['name'] ?? '--')) }}
                        @if(!empty($p['remark']))
                            <br><small style="font-size: 9px; color: #666;">({{ $p['remark'] }})</small>
                        @endif
                    </td>
                    <td>{{ $qty }}</td>
                    <td>{{ $p['unit'] ?? 'Nos' }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>
                        @if($disc > 0)
                            {{ $discType === 'percentage' ? $disc.'%' : number_format($disc, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($lineAmount, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="7" style="padding: 20px; color: #999;">No products listed</td></tr>
            @endif

            @php
                $discount_val = (float)($quote->data['discount'] ?? 0);
                $net_taxable = max(0, $subtotal_sum - $discount_val);
                $gst_val = round($net_taxable * 0.18, 2);
                $final_total = $net_taxable + $gst_val;
            @endphp

            @if($discount_val > 0)
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="total-label-cell">Subtotal</td>
                <td>{{ number_format($subtotal_sum, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="total-label-cell">Addl. Discount</td>
                <td>{{ number_format($discount_val, 2) }}</td>
            </tr>
            @endif

            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="total-label-cell">Taxable Amount</td>
                <td>{{ number_format($net_taxable, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="total-label-cell">GST 18%</td>
                <td>{{ number_format($gst_val, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: none;"></td>
                <td class="total-label-cell" style="font-size: 14px; background-color: #000;">Total Amount</td>
                <td style="font-size: 14px; font-weight: bold;">{{ number_format($final_total, 2) }}</td>
            </tr>
            
            {{-- Section B (To be made dynamic later) --}}
            {{-- <tr class="sub-section-row"><td colspan="6">(B) LOW SIDE</td></tr> --}}
        </tbody>
    </table>

    {{-- Bank Details --}}
    <div class="bank-details">
        {!! $settings->bank_details ?? 'Bank Details: &nbsp;&nbsp; Bank Name: Bank Of India &nbsp;&nbsp; Account Number: 051630150000037 &nbsp;&nbsp; IFSC Code: BKID0000516' !!}
    </div>

    @if(!empty($quote->data['show_payment_terms']))
    <div class="payment-terms">
        <h4>PAYMENT TERMS :</h4>
        @php 
            $pTerms = !empty($quote->data['payment_terms']) ? $quote->data['payment_terms'] : ($settings->payment_terms ?? '');
        @endphp
        @if(!empty($pTerms))
            <ol>
                @foreach(explode("\n", str_replace("\r", "", $pTerms)) as $index => $term)
                    @if(trim($term) !== '')
                        <li @if($index === 0) style="color: red;" @endif>{{ trim($term) }}</li>
                    @endif
                @endforeach
            </ol>
        @endif
    </div>
    @endif

    <div style="margin-top: 30px; font-size: 11px; text-align: center; border-top: 1px dashed #000; padding-top: 10px; line-height: 1.5;">
        <strong>Thank you for connecting with Unique Air Conditioning!</strong><br>
        We are specialized in HVAC, Chillers, VRF, Cold Room, Ductable, Tower, Cassette & Split AC's.<br>
        Also get Tower and Ductable AC on Rental Basis for Events & Corporates<br>
        Please Call us on <strong>8448441066</strong> or <a href="https://g.page/UniqueAC/review?gm" style="color: #000; text-decoration: none;">https://g.page/UniqueAC/review?gm</a><br>
        Website - <a href="http://www.uniqueacprojects.com" style="color: #000; text-decoration: none;">www.uniqueacprojects.com</a>
    </div>
</div>
@endsection
