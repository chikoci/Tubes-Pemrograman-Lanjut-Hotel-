<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Hotel System</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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
