@extends('quotation.templates.base')

@section('content')
    <!-- Page 1: About -->
    <div class="page">
        <div class="header-banner">
            <h1 style="margin: 0; font-size: 28px;">About {{ $settings->company_name ?? 'Our Company' }}</h1>
        </div>
        <div class="content" style="text-align: center; padding-top: 60px;">
            <div style="max-width: 600px; margin: 0 auto;">
                <p style="font-size: 14px; color: #374151;">{{ $settings->company_description ?? '' }}</p>
                
                @if($settings->mission || $settings->vision)
                    <div style="margin-top: 50px;">
                        <h2 style="color: {{ $settings->primary_color ?? '#434AFA' }}; font-size: 20px;">Mission & Vision</h2>
                        @if($settings->mission)
                            <p><strong>Mission:</strong> {{ $settings->mission }}</p>
                        @endif
                        @if($settings->vision)
                            <p><strong>Vision:</strong> {{ $settings->vision }}</p>
                        @endif
                    </div>
                @endif
                
                @if($settings->core_values)
                    <div style="margin-top: 40px;">
                        <h2 style="color: {{ $settings->primary_color ?? '#434AFA' }}; font-size: 20px;">Core Values</h2>
                        <p>{{ $settings->core_values }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="footer">{{ $settings->company_name }} | {{ $settings->website }}</div>
    </div>

    <div class="page-break"></div>

    <!-- Page 2: Services -->
    <div class="page">
        <div class="header-banner">
            <h1 style="margin: 0; font-size: 28px;">Our Services</h1>
        </div>
        <div class="content">
            <div style="display: block; width: 100%;">
                @php $services = is_string($settings->services) ? json_decode($settings->services, true) : ($settings->services ?? []); @endphp
                @foreach(array_chunk($services, 2) as $row)
                    <div style="width: 100%; margin-bottom: 20px; overflow: hidden;">
                        @foreach($row as $service)
                            <div style="width: 45%; float: left; margin-right: 4%; background: white; padding: 15px; border-left: 5px solid {{ $settings->primary_color ?? '#434AFA' }}; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <strong style="font-size: 14px;">{{ $service }}</strong>
                            </div>
                        @endforeach
                    </div>
                    <div style="clear: both;"></div>
                @endforeach
            </div>
        </div>
        <div class="footer">{{ $settings->company_name }} | {{ $settings->website }}</div>
    </div>

    <div class="page-break"></div>

    <!-- Page 3: Quotation -->
    <div class="page">
        <div class="content" style="padding-top: 20px;">
            <div style="margin-bottom: 30px;">
                <div style="float: left; width: 50%;">
                    <h1 style="color: {{ $settings->primary_color ?? '#434AFA' }}; margin: 0;">QUOTATION</h1>
                    <p style="margin: 5px 0; font-size: 12px;">#{{ $quote->quotation_number }}</p>
                    <p style="margin: 0; font-size: 12px;">Date: {{ $quote->created_at->format('d M, Y') }}</p>
                </div>
                <div style="float: right; width: 50%; text-align: right;">
                    <strong style="font-size: 16px;">{{ $settings->company_name }}</strong><br>
                    <span style="font-size: 11px; color: #6b7280;">
                        {{ $settings->office_address }}<br>
                        {{ $settings->office_city }}, {{ $settings->office_state }} - {{ $settings->office_pincode }}<br>
                        Phone: {{ $settings->phone }} | Email: {{ $settings->email }}
                    </span>
                </div>
                <div style="clear: both;"></div>
            </div>

            <div style="margin-bottom: 30px; background: #f3f4f6; padding: 15px; border-radius: 5px;">
                <div style="float: left; width: 50%;">
                    <strong style="color: #6b7280; font-size: 11px; text-transform: uppercase;">Quote For:</strong><br>
                    @if($quote->customer_type == 'customer')
                        <strong>{{ $quote->customer->name ?? 'N/A' }}</strong><br>
                        {{ $quote->customer->company_name ?? '' }}
                    @else
                        @php $prospect = \App\Models\Prospectus::find($quote->customer_id); @endphp
                        <strong>{{ optional($prospect)->prospectus_name ?? 'N/A' }}</strong><br>
                        {{ $prospect->contact_person ?? '' }}
                    @endif
                </div>
                <div style="float: right; width: 50%; text-align: right;">
                    <strong style="color: #6b7280; font-size: 11px; text-transform: uppercase;">Project Details:</strong><br>
                    Timeline: {{ $quote->data['project_timeline'] ?? 'N/A' }}
                </div>
                <div style="clear: both;"></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Product/Service</th>
                        <th style="width: 25%;">Remark</th>
                        <th style="width: 15%; text-align: right;">Price</th>
                        <th style="width: 15%; text-align: right;">Tax (18%)</th>
                        <th style="width: 15%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $products = $quote->data['products'] ?? []; 
                        $subtotal = 0;
                    @endphp
                    @foreach($products as $index => $item)
                        @php 
                            $price = $item['price'] ?? 0;
                            $tax = round($price * 0.18, 2);
                            $rowTotal = $price + $tax;
                            $subtotal += $rowTotal;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ optional(\App\Models\SalesProduct::find($item['product_id']))->product_name ?? $item['product_id'] }}</td>
                            <td>{{ $item['remark'] ?? '' }}</td>
                            <td style="text-align: right;">₹{{ number_format($price, 2) }}</td>
                            <td style="text-align: right;">₹{{ number_format($tax, 2) }}</td>
                            <td style="text-align: right;">₹{{ number_format($rowTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="float: right; width: 250px;">
                <div style="border-bottom: 1px solid #e5e7eb; padding: 5px 0;">
                    <span style="color: #6b7280;">Subtotal:</span>
                    <span style="float: right;">₹{{ number_format($subtotal, 2) }}</span>
                </div>
                @if(($quote->data['discount'] ?? 0) > 0)
                    <div style="border-bottom: 1px solid #e5e7eb; padding: 5px 0; color: #ef4444;">
                        <span>Discount:</span>
                        <span style="float: right;">-₹{{ number_format($quote->data['discount'], 2) }}</span>
                    </div>
                @endif
                <div style="padding: 10px 0; font-size: 16px; font-weight: bold; color: {{ $settings->primary_color ?? '#434AFA' }};">
                    <span>Grand Total:</span>
                    <span style="float: right;">₹{{ number_format($quote->total_amount, 2) }}</span>
                </div>
            </div>
            <div style="clear: both;"></div>

            <div style="margin-top: 40px;">
                <div class="section-header">Bank Details</div>
                <div style="white-space: pre-line; font-size: 11px;">{{ $settings->bank_details }}</div>
            </div>
        </div>
    </div>
@endsection
