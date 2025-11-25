<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Lupa Password</h2>
            <?php $errors = $_SESSION['errors'] ?? []; ?>
            <form method="POST" action="<?php echo url('auth/sendResetCode'); ?>">
                <div class="form-group">
                    <label for="email">Email Akun *</label>
                    <input type="email" name="email" id="email" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo e($errors['email']); ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Kirim Kode Verifikasi</button>
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-secondary btn-block">Kembali ke Login</a>
            </form>
        </div>
    </div>
</body>
</html>
