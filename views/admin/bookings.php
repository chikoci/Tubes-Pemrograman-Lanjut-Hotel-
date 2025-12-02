<div class="container">
    <h2>Kelola Reservasi</h2>
    
    <?php if (!empty($bookings)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">ID Booking</th>
                        <th style="width: 140px;">Tamu</th>
                        <th>Email</th>
                        <th style="width: 180px;">Kamar</th>
                        <th style="width: 110px;">Check-in</th>
                        <th style="width: 110px;">Check-out</th>
                        <th style="width: 130px;">Total</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><strong><?php echo e($booking['user_name']); ?></strong></td>
                            <td class="text-muted"><?php echo e($booking['user_email']); ?></td>
                            <td><?php echo e($booking['room_type_name']); ?> <span class="text-muted">(<?php echo e($booking['room_number']); ?>)</span></td>
                            <td><?php echo formatDate($booking['check_in_date']); ?></td>
                            <td><?php echo formatDate($booking['check_out_date']); ?></td>
                            <td><strong><?php echo formatRupiah($booking['total_price']); ?></strong></td>
                            <td style="text-align: center;">
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo e($booking['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">Belum ada reservasi.</p>
    <?php endif; ?>
</div>
