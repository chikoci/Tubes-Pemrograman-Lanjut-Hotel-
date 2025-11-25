<div class="container">
    <h2>Manajemen Booking</h2>
    
    <?php if (!empty($bookings)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tamu</th>
                    <th>Email</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo e($booking['user_name']); ?></td>
                        <td><?php echo e($booking['user_email']); ?></td>
                        <td><?php echo e($booking['room_type_name']); ?> (<?php echo e($booking['room_number']); ?>)</td>
                        <td><?php echo formatDate($booking['check_in_date']); ?></td>
                        <td><?php echo formatDate($booking['check_out_date']); ?></td>
                        <td><?php echo formatRupiah($booking['total_price']); ?></td>
                        <td><span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                            <?php echo e($booking['status']); ?>
                        </span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Belum ada booking.</p>
    <?php endif; ?>
</div>
