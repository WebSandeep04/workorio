<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>

<div class="d-flex">
    {{-- Desktop Sidebar (hidden on mobile) --}}
    <div class="d-none d-md-flex align-items-start">
        @include('layouts.sidebar')
    </div>

    <div class="content flex-grow-1">
        @include('layouts.header')
        {{-- Mobile NavBar (visible only on mobile) --}}
        <div class="d-md-none">
            @include('layouts.mobile_nav')
        </div>

        <div class="main p-3">
            {{-- Alert box area --}}
            <div class="alert-container" id="alertBox"></div>
            
            {{-- Page content --}}
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('js/layout.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/notifications.js') }}"></script>

{{-- Auto Logout Script --}}


<!-- Idle Timeout Warning Modal -->




@stack('scripts')
</body>
</html>
