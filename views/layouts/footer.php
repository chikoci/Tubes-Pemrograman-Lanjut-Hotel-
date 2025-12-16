    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section footer-brand">
                    <div class="footer-logo">
                        <span class="logo-icon">✦</span>
                        <span class="logo-text">KLUWA</span>
                    </div>
                    <p class="footer-desc">Pengalaman menginap mewah dengan pemandangan kota Balikpapan yang menakjubkan. Nikmati kenyamanan dan ketenangan di jantung Kalimantan Timur.</p>
                </div>
                <div class="footer-section">
                    <h3>Alamat</h3>
                    <p class="footer-address">
                        <i class="fas fa-map-marker-alt"></i>
                        Jl. Sei Wain No.34, Karang Joang<br>
                        Kec. Balikpapan Utara<br>
                        Balikpapan, Kalimantan Timur 76127
                    </p>
                    <a href="https://maps.app.goo.gl/BCOOY2NeZ8oSFRsq8" target="_blank" class="footer-map-link">
                        <i class="fas fa-directions"></i> Lihat di Google Maps
                    </a>
                </div>
                <div class="footer-section">
                    <h3>Hubungi Kami</h3>
                    <div class="footer-contact-list">
                        <div class="contact-item">
                            <span class="contact-icon"><i class="fas fa-phone"></i></span>
                            <span>+62 542 123 4567</span>
                        </div>
                        <div class="contact-item">
                            <a href="https://wa.me/6281234567890" target="_blank" class="contact-link">
                                <span class="contact-icon wa-icon"><i class="fab fa-whatsapp"></i></span>
                                <span>+62 812 3456 7890</span>
                            </a>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                            <span>info@kluwahotel.com</span>
                        </div>
                    </div>
                    <div class="footer-social">
                        <a href="https://facebook.com" target="_blank" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com" target="_blank" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com" target="_blank" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://wa.me/6281234567890" target="_blank" class="social-icon wa" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <p class="footer-hours">
                        <i class="fas fa-clock"></i> <strong>Resepsionis 24 Jam</strong>
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Kluwa Hotel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo asset('js/script.js'); ?>"></script>
</body>
</html>
<?php
// Clear old input and errors
clearOld();
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
