<div class="container">
    <div class="hero">
        <h1>Selamat Datang di Hotel Management System</h1>
        <p>Sistem pemesanan kamar hotel yang mudah dan praktis</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="hero-actions">
                <a href="<?php echo url('auth/register'); ?>" class="btn btn-primary">Daftar Sekarang</a>
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-secondary">Login</a>
            </div>
        <?php else: ?>
            <?php if (!isAdmin()): ?>
                <div class="hero-actions">
                    <a href="<?php echo url('booking/search'); ?>" class="btn btn-primary">Cari Kamar</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <h2>Tipe Kamar Kami</h2>
    
    <?php if (!empty($roomTypes)): ?>
        <div class="room-types-grid">
            <?php foreach ($roomTypes as $type): ?>
                <div class="room-type-card">
                    <?php if (!empty($type['image'])): ?>
                        <img src="<?php echo asset('uploads/' . $type['image']); ?>" alt="<?php echo e($type['name']); ?>" class="room-image">
                    <?php else: ?>
                        <div class="room-image-placeholder">
                            <span>Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="room-info">
                        <h3><?php echo e($type['name']); ?></h3>
                        <p class="price"><?php echo formatRupiah($type['price']); ?> / malam</p>
                        
                        <?php if (!empty($type['description'])): ?>
                            <p class="description"><?php echo e($type['description']); ?></p>
                        <?php endif; ?>
                        
                        <p class="availability">
                            Kamar Tersedia: <?php echo $type['available_rooms']; ?>
                        </p>
                        
                        <?php if (isLoggedIn() && !isAdmin()): ?>
                            <a href="<?php echo url('booking/search'); ?>" class="btn btn-small">Pesan Sekarang</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">Belum ada tipe kamar yang tersedia.</p>
    <?php endif; ?>
</div>
