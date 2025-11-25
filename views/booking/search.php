<div class="container">
    <h2>Cari Kamar Tersedia</h2>
    
    <?php 
    $errors = $_SESSION['errors'] ?? [];
    ?>
    
    <div class="search-card">
        <form method="POST" action="<?php echo url('booking/search'); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="check_in">Tanggal Check-in *</label>
                    <input type="date" name="check_in" id="check_in" 
                           value="<?php echo e($checkIn); ?>" 
                           min="<?php echo date('Y-m-d'); ?>" required>
                    <?php if (isset($errors['check_in'])): ?>
                        <span class="error"><?php echo e($errors['check_in']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="check_out">Tanggal Check-out *</label>
                    <input type="date" name="check_out" id="check_out" 
                           value="<?php echo e($checkOut); ?>"
                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    <?php if (isset($errors['check_out'])): ?>
                        <span class="error"><?php echo e($errors['check_out']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Cari Kamar</button>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($availableRooms)): ?>
        <h3>Kamar Tersedia (<?php echo count($availableRooms); ?> kamar)</h3>
        
        <div class="rooms-grid">
            <?php foreach ($availableRooms as $room): ?>
                <div class="room-card">
                    <?php if (!empty($room['image'])): ?>
                        <img src="<?php echo asset('uploads/' . $room['image']); ?>" 
                             alt="<?php echo e($room['room_type_name']); ?>" class="room-image">
                    <?php else: ?>
                        <div class="room-image-placeholder">
                            <span>Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="room-details">
                        <h4><?php echo e($room['room_type_name']); ?></h4>
                        <p class="room-number">Nomor Kamar: <?php echo e($room['room_number']); ?></p>
                        <p class="price"><?php echo formatRupiah($room['price']); ?> / malam</p>
                        
                        <?php if (!empty($room['description'])): ?>
                            <p class="description"><?php echo e($room['description']); ?></p>
                        <?php endif; ?>
                        
                        <a href="<?php echo url('booking/create', [
                                'room_id' => $room['id'],
                                'check_in' => $checkIn,
                                'check_out' => $checkOut
                            ]); ?>"
                           class="btn btn-primary btn-block">Booking Sekarang</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
        <div class="no-results">
            <p>Tidak ada kamar tersedia untuk tanggal yang dipilih.</p>
            <p>Silakan coba tanggal lain.</p>
        </div>
    <?php endif; ?>
</div>
