<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Verify OTP</title>

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<!-- header -->
<header class="w-100 px-4 py-2 mb-3 d-flex justify-content-between align-items-center border-bottom shadow-sm">
    <div class="d-flex align-items-center">
        <img src="/img/logo.png" alt="Logo" style="height: 40px;" class="me-2">
    </div>
</header>

<div class="loginBox">
    <div class="login-card">
        <h3 class="text-center mb-4">Verify OTP</h3>
        <p class="text-center text-muted mb-4">
            We've sent a 6-digit OTP to <strong>{{ session('reset_email') }}</strong><br>
            Enter the code below to reset your password.
        </p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ url('/verify-otp') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('reset_email') }}">

            <div class="mb-4">
                <label for="otp" class="form-label">Enter OTP Code</label>
                <input
                    type="text"
                    class="form-control @error('otp') is-invalid @enderror text-center"
                    id="otp"
                    name="otp"
                    placeholder="000000"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    required
                    autofocus
                    style="font-size: 24px; letter-spacing: 8px;"
                />
                @error('otp')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <div class="form-text text-center">
                    <small>Enter the 6-digit code sent to your email</small>
                </div>
            </div>

            <button type="submit" class="btn w-100 loginbtn d-block mx-auto mb-3">
                <i class="bi bi-check-circle me-2"></i>Verify OTP
            </button>

            <div class="text-center mb-3">
                <a href="{{ url('/forgot-password') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back to Forgot Password
                </a>
            </div>

            <div class="text-center">
                <a href="{{ url('/login') }}" class="text-decoration-none">
                    <i class="bi bi-house me-1"></i>Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto-focus and format OTP input
document.getElementById('otp').addEventListener('input', function(e) {
    // Remove non-numeric characters
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Limit to 6 digits
    if (this.value.length > 6) {
        this.value = this.value.slice(0, 6);
    }
});

// Auto-submit when 6 digits are entered
document.getElementById('otp').addEventListener('keyup', function(e) {
    if (this.value.length === 6) {
        document.querySelector('form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true}));
    }
});

// Prevent double submission
document.querySelector('form').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    if (btn.disabled) {
        e.preventDefault();
        return;
    }
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Verifying...';
    // If triggered by keyup, we need to actually submit if not prevented
    // But since this is a submit handler, the browser will submit if we don't preventDefault
    // However, if we triggered it via dispatchEvent, we need to ensure the form submits.
    // Standard button click works fine.
    
    // For keyup trigger:
    if (!e.isTrusted) { // Generated by script
         this.submit();
    }
});
</script>
</body>
</html>
