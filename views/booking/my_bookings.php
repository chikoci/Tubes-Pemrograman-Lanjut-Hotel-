<div class="container">
    <h2>Booking Saya</h2>
    
    <?php if (!empty($bookings)): ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-item">
                    <div class="booking-header">
                        <h3>Booking #<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                        <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo e($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div class="booking-details">
                        <div class="detail-item">
                            <strong>Tipe Kamar:</strong> <?php echo e($booking['room_type_name']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Nomor Kamar:</strong> <?php echo e($booking['room_number']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Check-in:</strong> <?php echo formatDate($booking['check_in_date']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Check-out:</strong> <?php echo formatDate($booking['check_out_date']); ?>
                        </div>
                        <div class="detail-item">
                            <strong>Total:</strong> <?php echo formatRupiah($booking['total_price']); ?>
                        </div>
                        
                        <?php if ($booking['payment']): ?>
                            <div class="detail-item">
                                <strong>Status Pembayaran:</strong> 
                                <span class="status-badge status-<?php echo strtolower($booking['payment']['status']); ?>">
                                    <?php echo e($booking['payment']['status']); ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <div class="detail-item">
                                <strong>Status Pembayaran:</strong> 
                                <span class="status-badge status-pending">Belum Dibayar</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-actions">
                        <?php if (!$booking['payment'] || $booking['payment']['status'] === 'Pending'): ?>
							<a href="<?php echo url('booking/payment', ['booking_id' => $booking['id']]); ?>" 
                               class="btn btn-primary btn-small">
                                <?php echo $booking['payment'] ? 'Lihat Pembayaran' : 'Bayar Sekarang'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>Anda belum memiliki booking.</p>
            <a href="<?php echo url('booking/search'); ?>" class="btn btn-primary">Cari Kamar</a>
        </div>
    <?php endif; ?>
</div>
