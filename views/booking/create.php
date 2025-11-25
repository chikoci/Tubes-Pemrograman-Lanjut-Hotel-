<div class="container">
    <h2>Konfirmasi Booking</h2>
    
    <div class="booking-summary">
        <h3>Detail Booking</h3>
        
        <table class="summary-table">
            <tr>
                <td><strong>Tipe Kamar:</strong></td>
		        <td><?php echo e($room['room_type_name']); ?></td>
            </tr>
            <tr>
                <td><strong>Nomor Kamar:</strong></td>
                <td><?php echo e($room['room_number']); ?></td>
            </tr>
            <tr>
                <td><strong>Check-in:</strong></td>
                <td><?php echo formatDate($checkIn); ?></td>
            </tr>
            <tr>
                <td><strong>Check-out:</strong></td>
                <td><?php echo formatDate($checkOut); ?></td>
            </tr>
            <tr>
                <td><strong>Lama Menginap:</strong></td>
                <td><?php echo $nights; ?> malam</td>
            </tr>
            <tr>
                <td><strong>Harga per Malam:</strong></td>
                <td><?php echo formatRupiah($room['price']); ?></td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Harga:</strong></td>
                <td><strong><?php echo formatRupiah($totalPrice); ?></strong></td>
            </tr>
        </table>

        <?php if (!empty($room['description'])): ?>
            <div class="room-description">
                <h4>Deskripsi Kamar:</h4>
                <p><?php echo e($room['description']); ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo url('booking/store'); ?>">
            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
            <input type="hidden" name="check_in" value="<?php echo $checkIn; ?>">
            <input type="hidden" name="check_out" value="<?php echo $checkOut; ?>">
            <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Konfirmasi Booking</button>
                <a href="<?php echo url('booking/search'); ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
