<div class="container">
    <h2>Dashboard Admin</h2>
    
    <!-- Filter Rentang Waktu -->
    <div class="filter-card">
        <form method="GET" action="<?php echo url('admin/dashboard'); ?>" class="filter-form">
            <input type="hidden" name="route" value="admin/dashboard">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" 
                           value="<?php echo e($startDate ?? ''); ?>">
                </div>
                <div class="filter-group">
                    <label for="end_date"><i class="fas fa-calendar-alt"></i> Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" 
                           value="<?php echo e($endDate ?? ''); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="<?php echo url('admin/dashboard'); ?>" class="btn btn-secondary"><i class="fas fa-sync-alt"></i> Reset</a>
                </div>
            </div>
        </form>
        <?php if ($startDate && $endDate): ?>
            <div class="filter-info">
                <i class="fas fa-info-circle"></i>
                Menampilkan data dari <strong><?php echo formatDate($startDate); ?></strong> sampai <strong><?php echo formatDate($endDate); ?></strong>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="stats-grid stats-grid-4">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
            <h3>Total Booking</h3>
            <p class="stat-number"><?php echo $stats['total']; ?></p>
        </div>
        
        <div class="stat-card stat-pending">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <h3>Booking Pending</h3>
            <p class="stat-number"><?php echo $stats['pending']; ?></p>
        </div>
        
        <div class="stat-card stat-confirmed">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <h3>Booking Confirmed</h3>
            <p class="stat-number"><?php echo $stats['confirmed']; ?></p>
        </div>
        
        <div class="stat-card stat-cancelled">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <h3>Booking Cancelled</h3>
            <p class="stat-number"><?php echo $stats['cancelled'] ?? 0; ?></p>
        </div>
    </div>

    <div class="stats-grid stats-grid-3">
        <div class="stat-card stat-revenue">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <h3>Total Revenue</h3>
            <p class="stat-number stat-number-sm"><?php echo formatRupiah($stats['revenue']); ?></p>
        </div>
        
        <div class="stat-card stat-room-types">
            <div class="stat-icon"><i class="fas fa-th-large"></i></div>
            <h3>Tipe Kamar</h3>
            <p class="stat-number"><?php echo $stats['total_room_types']; ?></p>
        </div>
        
        <div class="stat-card stat-rooms">
            <div class="stat-icon"><i class="fas fa-door-open"></i></div>
            <h3>Total Kamar</h3>
            <p class="stat-number"><?php echo $stats['total_rooms']; ?></p>
        </div>
    </div>
    
    <div class="quick-links">
        <h3>Menu Cepat</h3>
        <div class="links-grid">
            <a href="<?php echo url('admin/roomTypes'); ?>" class="quick-link-card">
                <h4>🏷️ Tipe Kamar</h4>
                <p>Kelola tipe dan harga kamar</p>
            </a>
            
            <a href="<?php echo url('admin/rooms'); ?>" class="quick-link-card">
                <h4>🚪 Kelola Kamar</h4>
                <p>Tambah dan atur kamar hotel</p>
            </a>
            
            <a href="<?php echo url('admin/bookings'); ?>" class="quick-link-card">
                <h4>📋 Reservasi</h4>
                <p>Lihat semua data reservasi</p>
            </a>
            
            <a href="<?php echo url('admin/payments'); ?>" class="quick-link-card">
                <h4>💳 Pembayaran</h4>
                <p>Monitor status pembayaran</p>
            </a>
        </div>
    </div>
</div>
