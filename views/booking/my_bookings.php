<div class="container">
    <h2>Booking Saya</h2>
    
    <?php if (!empty($bookings)): ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $booking): ?>
                <div class="booking-item">
                    <div class="booking-header">
                        <h3>Booking <?php echo e($booking['booking_code'] ?? '#' . str_pad($booking['id'], 5, '0', STR_PAD_LEFT)); ?></h3>
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
                        
                        <div class="detail-item">
                            <strong>Status Pembayaran:</strong> 
                            <?php if ($booking['payment_type_id']): ?>
                                <span class="status-badge status-<?php echo strtolower($booking['payment_status']); ?>">
                                    <?php echo e($booking['payment_status']); ?>
                                </span>
                                <br><small><?php echo getPaymentIcon($booking['payment_type_name']); ?> <?php echo e($booking['payment_type_name']); ?></small>
                            <?php else: ?>
                                <span class="status-badge status-pending">Belum Dibayar</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($booking['status'] === 'Pending'): ?>
                            <?php 
                            // Set timezone
                            date_default_timezone_set('Asia/Jakarta');
                            
                            // Hitung sisa waktu (23 jam dari created_at)
                            $createdTime = strtotime($booking['created_at']);
                            $currentTime = time();
                            $expireTime = $createdTime + (23 * 60 * 60); // Tepat 23 jam
                            $remainingSeconds = $expireTime - $currentTime;
                            ?>
                            <?php if ($remainingSeconds > 0): ?>
                                <div class="detail-item booking-timer">
                                    <strong> Sisa Waktu Pembayaran:</strong>
                                    <span class="countdown-timer" 
                                          data-expire="<?php echo $expireTime; ?>"
                                          data-booking-id="<?php echo $booking['id']; ?>"
                                          style="color: #e74c3c; font-weight: bold; font-size: 1.1rem;">
                                        <span class="countdown-display">Menghitung...</span>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="detail-item">
                                    <strong> Status:</strong>
                                    <span style="color: #e74c3c; font-weight: bold;">Waktu Pembayaran Habis</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="booking-actions">
                        <?php if ($booking['status'] === 'Pending' && $booking['payment_status'] !== 'Success'): ?>
							<a href="<?php echo url('booking/payment', ['booking_id' => $booking['id']]); ?>" 
                               class="btn btn-primary btn-small">
                                <?php echo $booking['payment_type_id'] ? 'Lihat Pembayaran' : 'Bayar Sekarang'; ?>
                            </a>
                            <a href="<?php echo url('booking/cancelBooking', ['booking_id' => $booking['id']]); ?>" 
                               class="btn btn-danger btn-small"
                               onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                                Batalkan Booking
                            </a>
                        <?php elseif ($booking['status'] === 'Confirmed'): ?>
                            <span class="text-success">Booking Dikonfirmasi</span>
                        <?php elseif ($booking['status'] === 'Cancelled'): ?>
                            <span class="text-muted">Booking Dibatalkan</span>
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
