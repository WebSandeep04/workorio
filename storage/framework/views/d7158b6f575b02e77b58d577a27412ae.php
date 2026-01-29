<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Workorio</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

<!-- HEADER -->
<span class="w-100 px-4 py-2 m-4 d-flex align-items-center">
    <img src="/img/logoblack.png" alt="Logo" style="height:40px">
</span>

<!-- LOGIN -->
<div class="loginBox">
    <div class="login-card">
        <h3>
            <span class="gradient-text">WELCOME</span> BACK
        </h3>
        <p><b>Log in</b> to Continue your Journey !!</p>

        <form method="POST" action="<?php echo e(url('/login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email"
                       class="form-control"
                       name="email"
                       placeholder="Enter your email"
                       value="<?php echo e(old('email')); ?>"
                       required autofocus>
            </div>

            <div class="mb-2 text-start">
                <label class="form-label">Password</label>
                <input type="password"
                       class="form-control"
                       name="password"
                       placeholder="Enter your password"
                       required>
            </div>

            <div class="text-end mb-3">
                <a href="<?php echo e(url('/forgot-password')); ?>" class="forgot-link">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="btn loginbtn w-100">
                Login
            </button>
        </form>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger mt-3 small">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/auth/login.blade.php ENDPATH**/ ?>