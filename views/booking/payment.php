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

        <?php if ($booking['payment_type_id']): ?>
            <div class="payment-status">
                <h3>Status Pembayaran</h3>
                <p>Metode: <strong><?php echo getPaymentIcon($booking['payment_type_name']); ?> <?php echo e($booking['payment_type_name']); ?></strong></p>
                <p>Status: <span class="status-badge status-<?php echo strtolower($booking['payment_status']); ?>">
                    <?php echo e($booking['payment_status']); ?>
                </span></p>
                <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($booking['payment_date'])); ?></p>
                
                <?php if ($booking['payment_status'] === 'Pending'): ?>
                    <p class="note">Pembayaran Anda sedang diproses.</p>
                <?php elseif ($booking['payment_status'] === 'Success'): ?>
                    <p class="note">Pembayaran Anda sudah dikonfirmasi. Terima kasih!</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="payment-form">
                <h3>Pilih Metode Pembayaran</h3>
                
                <form method="POST" action="<?php echo url('booking/confirmPayment'); ?>" enctype="multipart/form-data" id="paymentForm">
                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                    
                    <div class="form-group">
                        <label>Metode Pembayaran *</label>
                        <div class="payment-methods">
                            <?php foreach ($paymentTypes as $type): ?>
                            <label class="payment-method-card">
                                <input type="radio" name="payment_type_id" value="<?php echo $type['id']; ?>" required>
                                <div class="method-content">
                                    <div class="method-icon"><?php echo getPaymentIcon($type['name']); ?></div>
                                    <div class="method-name"><?php echo e($type['name']); ?></div>
                                    <div class="method-desc"><?php echo e($type['description']); ?></div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Instruksi payment -->
                    <div id="qrisInfo" class="payment-info" style="display:none;">
                        <p class="info-box">
                            <strong>📱 Cara Bayar dengan QRIS:</strong><br>
                            1. Klik tombol "Lanjutkan"<br>
                            2. Scan QR Code yang muncul dengan aplikasi e-wallet Anda<br>
                            3. Pembayaran otomatis terkonfirmasi
                        </p>
                    </div>
                    
                    <div id="transferInfo" class="payment-info" style="display:none;">
                        <p class="info-box">
                            <strong>🏦 Informasi Transfer Bank:</strong><br>
                            <strong>Bank BCA:</strong> 1234567890<br>
                            <strong>Bank BRI:</strong> 9876543210<br>
                            <strong>Bank Mandiri:</strong> 5555666677<br>
                            a.n. Hotel Management System<br><br>
                            Upload bukti transfer setelah melakukan pembayaran.
                        </p>
                        
                        <div class="form-group">
                            <label for="payment_proof">Upload Bukti Transfer *</label>
                            <input type="file" name="payment_proof" id="transferProof" accept="image/*,application/pdf" disabled>
                            <small>Format: JPG, PNG, PDF (Max 5MB)</small>
                        </div>
                    </div>
                    
                    <div id="debitInfo" class="payment-info" style="display:none;">
                        <p class="info-box">
                            <strong>💳 Bayar dengan Kartu Debit:</strong><br>
                            Lakukan pembayaran dengan kartu debit Anda, kemudian upload bukti struk pembayaran.
                        </p>
                        
                        <div class="form-group">
                            <label for="payment_proof">Upload Bukti Pembayaran *</label>
                            <input type="file" name="payment_proof" id="debitProof" accept="image/*,application/pdf" disabled>
                            <small>Format: JPG, PNG, PDF (Max 5MB)</small>
                        </div>
                    </div>
                    
                    <div id="cashInfo" class="payment-info" style="display:none;">
                        <p class="info-box">
                            <strong>💵 Pembayaran Cash:</strong><br>
                            Lakukan pembayaran tunai di resepsionis hotel, kemudian upload bukti kuitansi pembayaran.
                        </p>
                        
                        <div class="form-group">
                            <label for="payment_proof">Upload Bukti Kuitansi *</label>
                            <input type="file" name="payment_proof" id="cashProof" accept="image/*,application/pdf" disabled>
                            <small>Format: JPG, PNG, PDF (Max 5MB)</small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Lanjutkan Pembayaran</button>
                </form>
            </div>
        <?php endif; ?>
        
        <div class="form-actions">
            <a href="<?php echo url('booking/myBookings'); ?>" class="btn btn-secondary">Lihat Semua Booking</a>
        </div>
    </div>
</div>
