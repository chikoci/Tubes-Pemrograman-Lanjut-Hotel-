<div class="container">
    <h2>Pembayaran QRIS</h2>
    
    <div class="qris-container">
        <div class="qris-card">
            <h3>Scan QR Code untuk Membayar</h3>
            
            <div class="qr-code-wrapper">
                <div id="qrcode"></div>
            </div>
            
            <div class="payment-amount">
                <p>Total Pembayaran:</p>
                <h2><?php echo formatRupiah($booking['total_price']); ?></h2>
            </div>
            
            <div class="qris-instructions">
                <h4>Cara Pembayaran:</h4>
                <ol>
                    <li>QR Code akan otomatis diproses dalam <strong id="countdown">10</strong> detik</li>
                    <li>Tunggu hingga status berubah menjadi "Pembayaran Berhasil"</li>
                    <li>Anda akan otomatis diarahkan ke halaman Booking Saya</li>
                </ol>
                
                <div class="network-info">
                    <strong>ℹ️ Informasi:</strong>
                    <p>Ini adalah simulasi pembayaran QRIS untuk demo.</p>
                    <p>Pembayaran akan otomatis disetujui setelah countdown selesai.</p>
                </div>
            </div>
            
            <div class="payment-status">
                <p id="statusMessage">Memproses pembayaran QRIS...</p>
                <div class="loading-spinner" id="loadingSpinner"></div>
            </div>
            
            <div class="form-actions">
                <a href="<?php echo url('booking/myBookings'); ?>" class="btn btn-secondary">Kembali ke Booking Saya</a>
            </div>
        </div>
    </div>
</div>

<!-- Include QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// Tunggu sampai DOM dan script.js selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    var bookingId = <?php echo $booking['id']; ?>;
    var amount = <?php echo $booking['total_price']; ?>;
    
    // Cek apakah fungsi sudah tersedia
    if (typeof initQrisPayment === 'function') {
        initQrisPayment(bookingId, amount);
    } else {
        // Jika belum, tunggu sebentar dan coba lagi
        setTimeout(function() {
            if (typeof initQrisPayment === 'function') {
                initQrisPayment(bookingId, amount);
            }
        }, 100);
    }
});
</script>
