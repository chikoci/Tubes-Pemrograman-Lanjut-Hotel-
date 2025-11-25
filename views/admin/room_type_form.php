<div class="container">
    <h2><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Tipe Kamar</h2>
    
    <?php 
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    ?>
    
    <div class="form-card">
        <form method="POST" action="<?php echo url('admin/saveRoomType'); ?>">
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
                <label for="image">Nama File Gambar (opsional)</label>
                <input type="text" name="image" id="image" 
                       value="<?php echo e($old['image'] ?? ($roomType['image'] ?? '')); ?>"
                       placeholder="contoh: deluxe.jpg">
                <small>Upload gambar ke folder /images/ lalu masukkan nama filenya di sini</small>
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
