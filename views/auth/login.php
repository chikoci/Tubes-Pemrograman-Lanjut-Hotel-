<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kluwa Hotel</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <a href="<?php echo url('home'); ?>">
                    <span class="logo-icon">✦</span>
                    <span class="logo-text">KLUWA</span>
                </a>
            </div>
            <h2>Selamat Datang</h2>
            <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
            
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
                    <input type="email" name="email" id="email" placeholder="nama@email.com"
                           value="<?php echo e($old['email'] ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo e($errors['email']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo e($errors['password']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>

            <p class="text-center" style="margin-top: 1.5rem;">
                Belum punya akun? <a href="<?php echo url('auth/register'); ?>">Daftar di sini</a>
            </p>
            <p class="text-center">
                <a href="<?php echo url('auth/forgotPassword'); ?>">Lupa password?</a>
            </p>
            <p class="text-center" style="margin-top: 1rem;">
                <a href="<?php echo url('home'); ?>">← Kembali ke Beranda</a>
            </p>
        </div>
    </div>
</body>
</html>
