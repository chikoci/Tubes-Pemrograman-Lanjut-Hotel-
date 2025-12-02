<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Verifikasi Email</h2>
            
            <?php 
            $errors = $_SESSION['errors'] ?? [];
            $flash = getFlash();
            ?>
            
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo e($flash['message']); ?>
                </div>
            <?php endif; ?>
            
            <p class="text-center" style="margin-bottom: 20px;">
                Kode verifikasi telah dikirim ke:<br>
                <strong><?php echo e($email); ?></strong>
            </p>
            
            <form method="POST" action="<?php echo url('auth/verifyRegister'); ?>">
                <div class="form-group">
                    <label for="code">Masukkan Kode Verifikasi (6 digit)</label>
                    <input type="text" name="code" id="code" 
                           maxlength="6" pattern="[0-9]{6}"
                           placeholder="000000"
                           style="text-align: center; font-size: 24px; letter-spacing: 8px;"
                           required>
                    <?php if (isset($errors['code'])): ?>
                        <span class="error"><?php echo e($errors['code']); ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Verifikasi</button>
            </form>
            
            <p class="text-center" style="margin-top: 20px;">
                Tidak menerima kode? 
                <a href="<?php echo url('auth/resendRegisterCode'); ?>">Kirim Ulang</a>
            </p>
            
            <p class="text-center">
                <a href="<?php echo url('auth/register'); ?>">← Kembali ke Form Registrasi</a>
            </p>
        </div>
    </div>
</body>
</html>
