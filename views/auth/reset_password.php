<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Reset Password</h2>
            <?php $errors = $_SESSION['errors'] ?? []; ?>
            <form method="POST" action="<?php echo url('auth/resetPassword'); ?>">
                <div class="form-group">
                    <label for="code">Kode Verifikasi *</label>
                    <input type="text" name="code" id="code" required>
                    <?php if (isset($errors['code'])): ?>
                        <span class="error"><?php echo e($errors['code']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password">Password Baru *</label>
                    <input type="password" name="password" id="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo e($errors['password']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <span class="error"><?php echo e($errors['password_confirmation']); ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-secondary btn-block">Kembali ke Login</a>
            </form>
        </div>
    </div>
</body>
</html>
