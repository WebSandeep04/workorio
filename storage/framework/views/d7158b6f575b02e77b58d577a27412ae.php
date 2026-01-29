<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Workorio</title>

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
<header class=" header-mobile w-100 px-3 px-md-4 py-2 mb-3 m-4 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <img src="/img/logoblack.png" alt="Logo" style="height: 40px;" class="me-2">
    </div>
</header>

<div class="loginBox">
    <div class="login-card">
        <div class="text-center mb-4">
            <!-- <i class="bi bi-building text-primary" style="font-size: 3rem;"></i> -->
            <h3 class="mt-2">
  <span class="gradient-text">WELCOME</span> BACK
</h3>
            <p style="color: black;"><b>Log in</b> to continue your journey!!</p>
        </div>

        <form method="POST" action="<?php echo e(url('/login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="admin@tenant1.com"
                    required
                    autofocus
                    value="<?php echo e(old('email')); ?>"
                />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                />
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" />
                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>
                </div>
                <a href="<?php echo e(url('/forgot-password')); ?>" class="text-decoration-none small">Forgot Password?</a>
            </div>

            <button type="submit" class="btn w-100 loginbtn d-block mx-auto">Login</button>
        </form>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger mt-3">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <!-- <div class="text-center mt-4">
            <span>Don't have an account? <a href="<?php echo e(url('/register')); ?>">Sign up</a></span>
        </div> -->
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/auth/login.blade.php ENDPATH**/ ?>