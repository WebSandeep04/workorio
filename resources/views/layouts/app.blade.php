<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    @php $faviconV = file_exists(public_path('img/w.png')) ? filemtime(public_path('img/w.png')) : '1'; @endphp
    <link rel="icon" type="image/png" href="{{ asset('img/w.png') }}?v={{ $faviconV }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('img/w.png') }}?v={{ $faviconV }}">
    <link rel="apple-touch-icon" href="{{ asset('img/w.png') }}?v={{ $faviconV }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    
    <!-- Modern Plus Jakarta Sans Font (Nexora style) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}?v={{ file_exists(public_path('css/layout.css')) ? filemtime(public_path('css/layout.css')) : '1' }}">
    <link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nexora-select.css') }}?v={{ file_exists(public_path('css/nexora-select.css')) ? filemtime(public_path('css/nexora-select.css')) : '1' }}">
    <link rel="stylesheet" href="{{ asset('css/nexora-date.css') }}?v={{ file_exists(public_path('css/nexora-date.css')) ? filemtime(public_path('css/nexora-date.css')) : '1' }}">
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
        {{-- Unified white card: sticky header + page content scroll together inside --}}
        <div class="app-shell-card">
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
</div>

<script src="{{ asset('js/layout.js') }}?v={{ file_exists(public_path('js/layout.js')) ? filemtime(public_path('js/layout.js')) : '1' }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/notifications.js') }}"></script>
<script src="{{ asset('js/nexora-select.js') }}?v={{ file_exists(public_path('js/nexora-select.js')) ? filemtime(public_path('js/nexora-select.js')) : '1' }}"></script>
<script src="{{ asset('js/nexora-date.js') }}?v={{ file_exists(public_path('js/nexora-date.js')) ? filemtime(public_path('js/nexora-date.js')) : '1' }}"></script>

{{-- Auto Logout Script --}}


<!-- Idle Timeout Warning Modal -->




@stack('scripts')
</body>
</html>
