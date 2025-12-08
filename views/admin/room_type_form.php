<div class="container">
    <h2><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Tipe Kamar</h2>
    
    <?php 
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    ?>
    
    <div class="form-card">
        <form method="POST" action="<?php echo url('admin/saveRoomType'); ?>" enctype="multipart/form-data">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $roomType['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Nama Tipe Kamar *</label>
                <input type="text" name="name" id="name" 
                       value="<?php echo e($old['name'] ?? ($roomType['name'] ?? '')); ?>" required>
                <?php if (isset($errors['name'])): ?>
                    <span class="error"><?php echo e($errors['name']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="price">Harga per Malam (Rp) *</label>
                <input type="number" name="price" id="price" min="0" step="1000"
                       value="<?php echo e($old['price'] ?? ($roomType['price'] ?? '')); ?>" required>
                <?php if (isset($errors['price'])): ?>
                    <span class="error"><?php echo e($errors['price']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4"><?php echo e($old['description'] ?? ($roomType['description'] ?? '')); ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <span class="error"><?php echo e($errors['description']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Gambar Kamar</label>
                <?php if ($isEdit && !empty($roomType['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo asset('uploads/' . $roomType['image']); ?>" alt="Gambar saat ini" style="max-width: 200px; border-radius: 8px; margin-bottom: 10px;">
                        <p style="color: var(--text-light); font-size: 0.9rem;">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" id="image" accept="image/*">
                <small>Format: JPG, PNG, GIF. Maksimal 2MB</small>
                <?php if (isset($errors['image'])): ?>
                    <span class="error"><?php echo e($errors['image']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo url('admin/roomTypes'); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
