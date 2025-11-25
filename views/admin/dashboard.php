<div class="container">
    <h2>Dashboard Admin</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Booking</h3>
            <p class="stat-number"><?php echo $stats['total']; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Booking Pending</h3>
            <p class="stat-number"><?php echo $stats['pending']; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Booking Confirmed</h3>
            <p class="stat-number"><?php echo $stats['confirmed']; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p class="stat-number"><?php echo formatRupiah($stats['revenue']); ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Tipe Kamar</h3>
            <p class="stat-number"><?php echo $stats['total_room_types']; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Kamar</h3>
            <p class="stat-number"><?php echo $stats['total_rooms']; ?></p>
        </div>
    </div>
    
    <div class="quick-links">
        <h3>Menu Admin</h3>
        <div class="links-grid">
            <a href="<?php echo url('admin/roomTypes'); ?>" class="quick-link-card">
                <h4>Manajemen Tipe Kamar</h4>
                <p>Tambah, edit, hapus tipe kamar</p>
            </a>
            
            <a href="<?php echo url('admin/rooms'); ?>" class="quick-link-card">
                <h4>Manajemen Kamar</h4>
                <p>Tambah, edit, hapus kamar</p>
            </a>
            
            <a href="<?php echo url('admin/payments'); ?>" class="quick-link-card">
                <h4>Manajemen Pembayaran</h4>
                <p>Verifikasi pembayaran</p>
            </a>
            
            <a href="<?php echo url('admin/bookings'); ?>" class="quick-link-card">
                <h4>Manajemen Booking</h4>
                <p>Lihat semua booking</p>
            </a>
        </div>
    </div>
</div>
