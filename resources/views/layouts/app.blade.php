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
<script src="{{ asset('js/notifications.js') }}"></script>

{{-- Auto Logout Script --}}
<form id="auto-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<!-- Idle Timeout Warning Modal -->
<div class="modal fade" id="idleTimeoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #434afa; border-radius: 0;">
        <h5 class="modal-title">Session Timeout Warning</h5>
      </div>
      <div class="modal-body">
        <p>No activity detected. You will be logged out in <strong id="idleCountdown" class="text-danger">15</strong> seconds.</p>
        <p class="mb-0">Do you want to stay logged in?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnStayLoggedIn" style="border-radius: 0;">Resume Session</button>
      </div>
    </div>
  </div>
</div>

<script>
    (function() {
        const IDLE_TIME = 10 * 60 * 1000; // 10 minutes of inactivity before warning
        const WARNING_DURATION = 15;      // 15 seconds countdown
        
        let idleTimer;
        let countdownInterval;
        let isWarningShown = false;
        
        // Ensure DOM elements exist before referencing
        const modalEl = document.getElementById('idleTimeoutModal');
        const countdownEl = document.getElementById('idleCountdown');
        const stayBtn = document.getElementById('btnStayLoggedIn');
        
        // We need a way to initialize the bootstrap modal safely
        // Since this script runs at the end of body, Bootstrap should be loaded
        let bsModal = null;

        function getModal() {
            if (!bsModal && window.bootstrap) {
                bsModal = new bootstrap.Modal(modalEl);
            }
            return bsModal;
        }

        function startIdleTimer() {
            clearTimeout(idleTimer);
            clearInterval(countdownInterval);
            isWarningShown = false;
            
            idleTimer = setTimeout(showWarning, IDLE_TIME);
        }

        function showWarning() {
            isWarningShown = true;
            let secondsLeft = WARNING_DURATION;
            
            // Show Modal
            const modal = getModal();
            if (modal) {
                countdownEl.textContent = secondsLeft;
                modal.show();
                
                countdownInterval = setInterval(() => {
                    secondsLeft--;
                    countdownEl.textContent = secondsLeft;
                    
                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        logoutUser();
                    }
                }, 1000);
            } else {
                // Fallback if bootstrap modal fails
                logoutUser();
            }
        }

        function logoutUser() {
            document.getElementById('auto-logout-form').submit();
        }

        function activityDetected() {
            // Only reset if strict idle timer constitutes "logged in" state
            // If warning is shown, we force them to click "Resume" (better UX than accidental mouse move saving them)
            if (!isWarningShown) {
                startIdleTimer();
            }
        }
        
        if (stayBtn) {
            stayBtn.addEventListener('click', function() {
                const modal = getModal();
                if (modal) modal.hide();
                startIdleTimer();
            });
        }

        const events = ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, activityDetected, true);
        });

        // Init
        startIdleTimer();
    })();
</script>

@stack('scripts')
</body>
</html>
