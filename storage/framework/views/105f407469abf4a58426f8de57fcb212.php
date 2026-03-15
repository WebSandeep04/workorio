<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Super Admin Login - Workorio</title>

    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="superadmin-shell">
    <header class="superadmin-header">
        <div class="d-flex align-items-center">
            <img src="/img/logo.png" alt="Logo" class="brand-logo me-2">
        </div>
    </header>

    <main class="loginBox compact">
        <div class="login-card neon-card compact-card">
            <div class="text-center mb-3">
                <div class="avatar-chip compact-avatar mb-3">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3 class="text-white mb-1">Super Admin Login</h3>
                <p class="text-white-50 mb-0">Enter the master dashboard to manage every tenant.</p>
            </div>

            <form method="POST" action="<?php echo e(url('/superadmin/login')); ?>" class="glow-form">
                <?php echo csrf_field(); ?>

                <div class="mb-2">
                    <label for="email" class="form-label text-white-50 small-label">Email address</label>
                    <div class="input-pill">
                        <i class="bi bi-envelope-open"></i>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="superadmin@master.com"
                            required
                            autofocus
                            value="<?php echo e(old('email')); ?>"
                        />
                    </div>
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label text-white-50 small-label">Password</label>
                    <div class="input-pill">
                        <i class="bi bi-key"></i>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        />
                    </div>
                </div>

                <div class="mb-2 d-flex justify-content-between align-items-center text-white-50 small-label">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" />
                        <label class="form-check-label" for="remember">
                            Keep me signed in
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-glow w-100 compact-btn">Enter Command Center</button>
            </form>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger mt-3">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
        </div>
    </main>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: radial-gradient(circle at top, #1f2937 0%, #0f172a 40%, #070a13 100%);
        min-height: 100vh;
        color: #fff;
    }

    .superadmin-header {
        width: 100%;
        padding: 0.75rem 2rem 0.5rem;
        display: flex;
        align-items: center;
        color: #fff;
    }

    .brand-logo {
        height: 48px;
        filter: drop-shadow(0 4px 10px rgba(0,0,0,0.35));
    }

    .tagline {
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .loginBox {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 1.25rem;
    }

    .loginBox.compact {
        padding: 1.5rem;
    }

    .neon-card {
        background: linear-gradient(145deg, rgba(255,255,255,0.1), rgba(255,255,255,0.03));
        border-radius: 28px;
        padding: 2.5rem;
        width: min(420px, 100%);
        box-shadow:
            0 25px 60px rgba(3, 7, 18, 0.65),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(18px);
    }

    .neon-card.compact-card {
        border-radius: 22px;
        padding: 1.8rem;
    }

    .avatar-chip {
        width: 72px;
        height: 72px;
        border-radius: 24px;
        background: linear-gradient(135deg, #a855f7, #ec4899);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: #fff;
        box-shadow: 0 10px 20px rgba(236, 72, 153, 0.35);
    }

    .compact-avatar {
        width: 60px;
        height: 60px;
        font-size: 1.4rem;
    }

    .input-pill {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 0.35rem 0.75rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .input-pill:focus-within {
        border-color: rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.15);
    }

    .input-pill i {
        color: rgba(255,255,255,0.6);
        margin-right: 0.65rem;
    }

    .input-pill input {
        background: transparent;
        border: none;
        color: #fff;
        flex: 1;
        padding-left: 0;
    }

    .input-pill input::placeholder {
        color: rgba(255,255,255,0.6);
    }

    .input-pill input:focus {
        box-shadow: none;
        background: transparent;
        color: #fff;
    }

    .small-label {
        font-size: 0.8rem;
    }

    .btn-glow {
        background: linear-gradient(135deg, #22d3ee, #3b82f6);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 0.65rem 1rem;
        border-radius: 14px;
        box-shadow: 0 20px 30px rgba(59, 130, 246, 0.35);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .compact-btn {
        padding: 0.55rem 1rem;
        font-size: 0.9rem;
    }

    .btn-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 30px 40px rgba(59, 130, 246, 0.5);
        color: #fff;
    }

    .form-check-input {
        border-radius: 8px;
    }

    @media (max-width: 576px) {
        .superadmin-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }
        .neon-card {
            padding: 2rem 1.5rem;
        }
    }
</style>
</body>
</html><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/auth/superadmin-login.blade.php ENDPATH**/ ?>