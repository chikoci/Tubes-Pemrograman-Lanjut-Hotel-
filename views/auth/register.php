<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Daftar Akun Baru</h2>
            
            <?php 
            $errors = $_SESSION['errors'] ?? [];
            $old = $_SESSION['old'] ?? [];
            ?>
            
            <?php if (isset($errors['register'])): ?>
                <div class="alert alert-error">
                    <?php echo e($errors['register']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo url('auth/register'); ?>">
                <div class="form-group">
                    <label for="name">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" 
                           value="<?php echo e($old['name'] ?? ''); ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="error"><?php echo e($errors['name']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="age">Umur *</label>
                    <input type="number" name="age" id="age" min="1" max="150"
                           value="<?php echo e($old['age'] ?? ''); ?>" required>
                    <?php if (isset($errors['age'])): ?>
                        <span class="error"><?php echo e($errors['age']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" 
                           value="<?php echo e($old['email'] ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo e($errors['email']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="phone">No. Telepon *</label>
                    <input type="tel" name="phone" id="phone" pattern="[0-9+]+" 
                           oninput="this.value = this.value.replace(/[^0-9+]/g, '');"
                           value="<?php echo e($old['phone'] ?? ''); ?>" required>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error"><?php echo e($errors['phone']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password * (minimal 6 karakter)</label>
                    <input type="password" name="password" id="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo e($errors['password']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password *</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?php echo e($errors['confirm_password']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>

            <p class="text-center">
                Sudah punya akun? <a href="<?php echo url('auth/login'); ?>">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
