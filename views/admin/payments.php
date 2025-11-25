<div class="container">
    <h2>Manajemen Pembayaran</h2>
    
    <?php if (!empty($payments)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Booking</th>
                    <th>Tamu</th>
                    <th>Kamar</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo $payment['id']; ?></td>
                        <td>#<?php echo str_pad($payment['booking_id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo e($payment['user_name']); ?></td>
                        <td><?php echo e($payment['room_type_name']); ?> (<?php echo e($payment['room_number']); ?>)</td>
                        <td><?php echo formatDate($payment['check_in_date']); ?></td>
                        <td><?php echo formatDate($payment['check_out_date']); ?></td>
                        <td><?php echo formatRupiah($payment['amount']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($payment['payment_date'])); ?></td>
                        <td><span class="status-badge status-<?php echo strtolower($payment['status']); ?>">
                            <?php echo e($payment['status']); ?>
                        </span></td>
                        <td>
                            <?php if ($payment['status'] === 'Pending'): ?>
								<a href="<?php echo url('admin/approvePayment', ['id' => $payment['id']]); ?>" 
                                   class="btn btn-small btn-primary"
                                   onclick="return confirm('Konfirmasi pembayaran ini?')">
                                    Konfirmasi
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Sudah dikonfirmasi</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">Belum ada pembayaran.</p>
    <?php endif; ?>
</div>
