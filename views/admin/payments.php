<div class="container">
    <h2>Kelola Pembayaran</h2>
    
    <?php if (!empty($bookings)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 110px;">ID Booking</th>
                        <th style="width: 140px;">Tamu</th>
                        <th style="width: 150px;">Metode</th>
                        <th style="width: 140px;">Total</th>
                        <th style="width: 150px;">Waktu Bayar</th>
                        <th style="width: 120px; text-align: center;">Bukti</th>
                        <th style="width: 120px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($booking['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><strong><?php echo e($booking['user_name']); ?></strong></td>
                            <td>
                                <?php if ($booking['payment_type_id']): ?>
                                    <?php echo getPaymentIcon($booking['payment_type_name']); ?> <?php echo e($booking['payment_type_name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Belum dipilih</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo formatRupiah($booking['total_price']); ?></strong></td>
                            <td>
                                <?php if (!empty($booking['payment_date'])): ?>
                                    <?php echo date('d M Y H:i', strtotime($booking['payment_date'])); ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if (!empty($booking['payment_proof'])): ?>
                                    <a href="<?php echo $booking['payment_proof']; ?>" target="_blank" class="btn btn-small btn-info">
                                        Lihat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <span class="status-badge status-<?php echo strtolower($booking['payment_status']); ?>">
                                    <?php echo e($booking['payment_status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-data">Belum ada data pembayaran.</p>
    <?php endif; ?>
</div>
