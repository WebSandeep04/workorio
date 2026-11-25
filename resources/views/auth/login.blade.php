<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Workorio</title>

    <!-- Web Icon / Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/w.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/w.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Bootstrap Grid & Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap-grid.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<!-- Circular Radial Masked Dot Pattern (Centered near card side) -->
<div class="circular-dots-bg"></div>
<div class="brand-ambient-glow"></div>

<div class="main-viewport">
    
    <!-- Top Header (Logo Left) -->
    <header class="top-header">
        <div class="d-flex align-items-center">
            <img src="{{ asset('img/logoblack.png') }}" alt="Workorio Logo" class="brand-logo-img" onerror="this.onerror=null; this.src='{{ asset('img/logo.png') }}';">
        </div>
    </header>

    <!-- Centered Content Split Wrapper -->
    <div class="content-wrapper">
        <div class="container-fluid p-0">
            <div class="row align-items-center">
                
                <!-- Left Hero Section with Spacing & Typography -->
                <div class="col-12 col-lg-7 left-col">
                    
                    <div class="pill-badge">
                        <span class="pill-dot"></span>
                        <span>Built for modern teams & businesses</span>
                    </div>

                    <h1 class="headline-text">
                        Manage your business <br>smarter.
                    </h1>

                    <p class="description-text">
                        Welcome to Workorio. Track workforce, automate lead follow-ups, organize operations, and collaborate seamlessly in one place.
                    </p>

                    <div class="trusted-section">
                        <span class="trusted-title">Integrated & trusted with</span>
                        <div class="partner-chips">
                            
                            <!-- Meta (WhatsApp Brand Color: #25D366) -->
                            <div class="partner-logo-item" title="Meta WhatsApp">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C6.48 2 2 6.48 2 12C2 13.85 2.5 15.58 3.38 17.07L2 22L7.1 20.67C8.55 21.52 10.22 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM17.02 16.03C16.81 16.63 15.84 17.18 15.33 17.24C14.88 17.29 14.3 17.3 12.33 16.48C9.82 15.44 8.2 12.87 8.07 12.7C7.95 12.53 7.07 11.36 7.07 10.15C7.07 8.94 7.69 8.35 7.92 8.11C8.12 7.9 8.44 7.8 8.76 7.8C8.86 7.8 8.96 7.81 9.04 7.81C9.28 7.82 9.4 7.83 9.56 8.21C9.76 8.69 10.24 9.87 10.3 9.99C10.36 10.11 10.42 10.27 10.34 10.43C10.26 10.59 10.2 10.67 10.08 10.81C9.96 10.95 9.83 11.12 9.73 11.23C9.61 11.36 9.48 11.5 9.62 11.74C9.76 11.98 10.24 12.76 10.95 13.39C11.87 14.21 12.62 14.47 12.89 14.58C13.1 14.67 13.34 14.65 13.5 14.48C13.7 14.27 13.95 13.91 14.2 13.56C14.38 13.31 14.61 13.28 14.85 13.37C15.09 13.46 16.37 14.09 16.63 14.22C16.89 14.35 17.06 14.41 17.12 14.51C17.18 14.61 17.18 15.19 17.02 16.03Z" fill="#25D366"/>
                                </svg>
                                <span>Meta</span>
                            </div>

                            <!-- MSG91 -->
                            <div class="partner-logo-item" title="MSG91">
                                <img src="{{ asset('img/msg91.jpeg') }}" alt="MSG91" style="height: 22px; width: auto; object-fit: contain; display: block; mix-blend-mode: multiply;">
                            </div>

                            <!-- Ola Maps -->
                            <div class="partner-logo-item" title="Ola Maps">
                                <img src="{{ asset('img/olamap.png') }}" alt="Ola Maps" style="height: 17px; width: auto; object-fit: contain; display: block; mix-blend-mode: multiply;">
                            </div>

                        </div>
                    </div>

                </div>

                <!-- Right Side-by-Side Floating Card -->
                <div class="col-12 col-lg-5 right-col">
                    <div class="login-card">
                        <div class="card-title">
                            <span class="gradient-word">Welcome</span> back
                        </div>
                        <div class="card-subtitle">
                            Sign in to your Workorio workspace.
                        </div>

                        <form method="POST" action="{{ url('/login') }}">
                            @csrf

                            <!-- Email / Username Input -->
                            <div style="margin-bottom: 1.25rem;">
                                <label for="login_id" class="input-label">Work email</label>
                                <input
                                    type="text"
                                    class="custom-input"
                                    id="login_id"
                                    name="login_id"
                                    placeholder="you@company.com"
                                    required
                                    autofocus
                                    value="{{ old('login_id') }}"
                                    autocomplete="username"
                                />
                            </div>

                            <!-- Password Input -->
                            <div style="margin-bottom: 1.25rem;">
                                <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 6px;">
                                    <label for="password" class="input-label" style="margin-bottom: 0;">Password</label>
                                    <a href="{{ url('/forgot-password') }}" class="link-forgot">Forgot password?</a>
                                </div>
                                <div class="input-field-wrap">
                                    <input
                                        type="password"
                                        class="custom-input"
                                        style="padding-right: 40px;"
                                        id="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        required
                                        autocomplete="current-password"
                                    />
                                    <button type="button" class="pass-toggle" id="togglePasswordBtn" aria-label="Toggle password visibility">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Checkbox -->
                            <div class="check-container">
                                <input class="check-input" type="checkbox" name="remember" id="remember" />
                                <label class="check-label" for="remember">
                                    Keep me signed in
                                </label>
                            </div>

                            <!-- Submit Button with Workorio Gradient -->
                            <button type="submit" class="btn-submit">
                                <span>Sign In</span>
                                <i class="bi bi-arrow-right-short" style="font-size: 1.2rem;"></i>
                            </button>
                        </form>

                        @if ($errors->any())
                            <div class="custom-alert">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Empty bottom spacer to keep perfect vertical centering -->
    <div style="height: 15px;"></div>

</div>

<!-- Password Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        }
    });
</script>

</body>
</html>
