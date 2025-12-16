<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-secondary btn-block" style="margin-top: 1rem;">Kembali ke Login</a>
            </form>
        </div>
    </div>
</body>
</html>
