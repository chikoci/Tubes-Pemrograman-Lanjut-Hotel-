<div class="container">
    <h2>Pembayaran</h2>
    
    <div class="payment-container">
        <div class="booking-info">
            <h3>Detail Booking</h3>
            <table class="info-table">
                <tr>
                    <td><strong>Nomor Booking:</strong></td>
                    <td>#<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></td>
                </tr>
                <tr>
                    <td><strong>Tipe Kamar:</strong></td>
                    <td><?php echo e($booking['room_type_name']); ?></td>
                </tr>
                <tr>
                    <td><strong>Nomor Kamar:</strong></td>
                    <td><?php echo e($booking['room_number']); ?></td>
                </tr>
                <tr>
                    <td><strong>Check-in:</strong></td>
                    <td><?php echo formatDate($booking['check_in_date']); ?></td>
                </tr>
                <tr>
                    <td><strong>Check-out:</strong></td>
                    <td><?php echo formatDate($booking['check_out_date']); ?></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Pembayaran:</strong></td>
                    <td><strong><?php echo formatRupiah($booking['total_price']); ?></strong></td>
                </tr>
            </table>
        </div>

        <?php if ($existingPayment): ?>
            <div class="payment-status">
                <h3>Status Pembayaran</h3>
                <p>Status: <span class="status-badge status-<?php echo strtolower($existingPayment['status']); ?>">
                    <?php echo e($existingPayment['status']); ?>
                </span></p>
                <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($existingPayment['payment_date'])); ?></p>
                
                <?php if ($existingPayment['status'] === 'Pending'): ?>
                    <p class="note">Pembayaran Anda sedang menunggu verifikasi dari Admin.</p>
                <?php elseif ($existingPayment['status'] === 'Success'): ?>
                    <p class="note">Pembayaran Anda sudah dikonfirmasi. Terima kasih!</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="payment-form">
                <h3>Konfirmasi Pembayaran</h3>
                <p class="payment-instruction">
                    Silakan transfer ke rekening berikut:<br>
                    <strong>Bank BCA: 1234567890</strong><br>
                    a.n. Hotel Management System
                </p>
                
                <form method="POST" action="<?php echo url('booking/confirmPayment'); ?>">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    <input type="hidden" name="amount" value="<?php echo $booking['total_price']; ?>">
                    
                    <p class="note">Setelah melakukan transfer, klik tombol di bawah untuk konfirmasi pembayaran. 
                    Admin akan memverifikasi pembayaran Anda.</p>
                    
                    <button type="submit" class="btn btn-primary btn-block">Saya Sudah Transfer</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="form-actions">
            <a href="<?php echo url('booking/myBookings'); ?>" class="btn btn-secondary">Lihat Semua Booking</a>
        </div>
    </div>
</div>
