{{-- resources/views/quotation/templates/premium.blade.php --}}
@extends('quotation.templates.base')

@section('template_styles')
<style>
    /* Add unique CSS for this client format here */
    .header { border-bottom: 5px solid {{ $settings->primary_color }}; }
    /* ... other custom styles ... */
</style>
@endsection

@section('content')
    {{-- Structure the HTML exactly how the new client wants it --}}
    <h1>UNIQUEAC QUOTATION</h1>
    <p>Quote No: {{ $quote->quotation_number }}</p>
    {{-- ... display products, bank details, etc. ... --}}
@endsection
