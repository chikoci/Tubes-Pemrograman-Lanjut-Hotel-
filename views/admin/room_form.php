<div class="container">
    <h2><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Kamar</h2>
    
    <?php 
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    ?>
    
    <div class="form-card">
        <form method="POST" action="<?php echo url('admin/saveRoom'); ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="room_type_id">Tipe Kamar *</label>
                <select name="room_type_id" id="room_type_id" required>
                    <option value="">Pilih Tipe Kamar</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>"
                                <?php echo ($old['room_type_id'] ?? ($room['room_type_id'] ?? '')) == $type['id'] ? 'selected' : ''; ?>>
                            <?php echo e($type['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['room_type_id'])): ?>
                    <span class="error"><?php echo e($errors['room_type_id']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="room_number">Nomor Kamar *</label>
                <input type="text" name="room_number" id="room_number" 
                       value="<?php echo e($old['room_number'] ?? ($room['room_number'] ?? '')); ?>" required>
                <?php if (isset($errors['room_number'])): ?>
                    <span class="error"><?php echo e($errors['room_number']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="status">Status *</label>
                <select name="status" id="status" required>
                    <option value="Available" 
                            <?php echo ($old['status'] ?? ($room['status'] ?? '')) == 'Available' ? 'selected' : ''; ?>>
                        Available
                    </option>
                    <option value="Maintenance" 
                            <?php echo ($old['status'] ?? ($room['status'] ?? '')) == 'Maintenance' ? 'selected' : ''; ?>>
                        Maintenance
                    </option>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <span class="error"><?php echo e($errors['status']); ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?php echo url('admin/rooms'); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
