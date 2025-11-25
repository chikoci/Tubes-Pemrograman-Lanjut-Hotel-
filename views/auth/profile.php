<div class="container">
    <h2>Profil Saya</h2>
    
    <?php 
    $errors = $_SESSION['errors'] ?? [];
    ?>
    
    <div class="profile-card">
        <form method="POST" action="<?php echo url('auth/updateProfile'); ?>">
            <div class="form-group">
                <label for="name">Nama Lengkap *</label>
                <input type="text" name="name" id="name" 
                       value="<?php echo e($user['name']); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <span class="error"><?php echo e($errors['name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="age">Umur *</label>
                <input type="number" name="age" id="age" min="1" max="150"
                       value="<?php echo e($user['age']); ?>" required>
                <?php if (isset($errors['age'])): ?>
                    <span class="error"><?php echo e($errors['age']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" 
                       value="<?php echo e($user['email']); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?php echo e($errors['email']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">No. Telepon *</label>
                <input type="tel" name="phone" id="phone" pattern="[0-9+]+" 
                       oninput="this.value = this.value.replace(/[^0-9+]/g, '');"
                       value="<?php echo e($user['phone']); ?>" required>
                <?php if (isset($errors['phone'])): ?>
                    <span class="error"><?php echo e($errors['phone']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="current_password">Password Lama (wajib jika ganti password)</label>
                <input type="password" name="current_password" id="current_password">
                <?php if (isset($errors['current_password'])): ?>
                    <span class="error"><?php echo e($errors['current_password']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" id="password">
                <?php if (isset($errors['password'])): ?>
                    <span class="error"><?php echo e($errors['password']); ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Update Profil</button>
            <a href="<?php echo url('home'); ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
