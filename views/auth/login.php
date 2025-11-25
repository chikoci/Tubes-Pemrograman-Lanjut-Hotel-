<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Login</h2>
            
            <?php 
            $errors = $_SESSION['errors'] ?? [];
            $old = $_SESSION['old'] ?? [];
            ?>
            
            <?php if (isset($errors['login'])): ?>
                <div class="alert alert-error">
                    <?php echo e($errors['login']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo url('auth/login'); ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" 
                           value="<?php echo e($old['email'] ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo e($errors['email']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo e($errors['password']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="text-center">
                Belum punya akun? <a href="<?php echo url('auth/register'); ?>">Daftar di sini</a>
            </p>
            <p class="text-center">
            <a href="<?php echo url('auth/forgotPassword'); ?>">Lupa password?</a>
        </div>
    </div>
</body>
</html>
