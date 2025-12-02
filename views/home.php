<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge">✦ WELCOME TO KLUWA</span>
        <h1 class="hero-title">Kemewahan & Kenyamanan<br>di Jantung Balikpapan</h1>
        <p class="hero-subtitle">Nikmati pengalaman menginap tak terlupakan dengan pemandangan kota yang memukau dan layanan berkelas internasional</p>
        
        <?php if (!isLoggedIn()): ?>
            <div class="hero-actions">
                <a href="<?php echo url('auth/register'); ?>" class="btn btn-primary btn-lg">Mulai Sekarang</a>
                <a href="<?php echo url('auth/login'); ?>" class="btn btn-outline btn-lg">Masuk</a>
            </div>
        <?php else: ?>
            <?php if (!isAdmin()): ?>
                <div class="hero-actions">
                    <a href="<?php echo url('booking/search'); ?>" class="btn btn-primary btn-lg">Pesan Kamar</a>
                    <a href="#rooms-section" class="btn btn-outline btn-lg scroll-link">Lihat Kamar</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="hero-scroll-indicator">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">🏊</div>
                <h3>Infinity Pool</h3>
                <p>Kolam renang rooftop dengan pemandangan kota</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🍽️</div>
                <h3>Restaurant & Bar</h3>
                <p>Sajian kuliner nusantara & internasional</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">💆</div>
                <h3>Spa & Wellness</h3>
                <p>Relaksasi dengan terapi tradisional Borneo</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">📶</div>
                <h3>High-Speed WiFi</h3>
                <p>Koneksi internet cepat di seluruh area</p>
            </div>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section class="rooms-section" id="rooms-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">AKOMODASI</span>
            <h2 class="section-title">Kamar & Suite Kami</h2>
            <p class="section-subtitle">Pilih kenyamanan yang sesuai dengan kebutuhan Anda</p>
        </div>
        
        <?php if (!empty($roomTypes)): ?>
            <div class="room-types-grid">
                <?php foreach ($roomTypes as $type): ?>
                    <div class="room-type-card">
                        <div class="room-image-wrapper">
                            <?php if (!empty($type['image'])): ?>
                                <img src="<?php echo asset('uploads/' . $type['image']); ?>" alt="<?php echo e($type['name']); ?>" class="room-image">
                            <?php else: ?>
                                <div class="room-image-placeholder">
                                    <span class="placeholder-icon">🛏️</span>
                                </div>
                            <?php endif; ?>
                            <div class="room-badge">
                                <?php if ($type['available_rooms'] > 0): ?>
                                    <span class="badge-available"><?php echo $type['available_rooms']; ?> Tersedia</span>
                                <?php else: ?>
                                    <span class="badge-soldout">Penuh</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="room-info">
                            <h3 class="room-name"><?php echo e($type['name']); ?></h3>
                            
                            <?php if (!empty($type['description'])): ?>
                                <p class="room-description"><?php echo e($type['description']); ?></p>
                            <?php endif; ?>
                            
                            <div class="room-amenities">
                                <?php 
                                $bedTypes = [
                                    'standard' => '🛏️ Twin Bed',
                                    'deluxe' => '🛏️ Queen Bed',
                                    'suite' => '🛏️ King Bed'
                                ];
                                $bedType = $bedTypes[strtolower($type['name'])] ?? '🛏️ King Bed';
                                ?>
                                <span class="amenity"><?php echo $bedType; ?></span>
                                <span class="amenity">📺 Smart TV</span>
                                <span class="amenity">❄️ AC</span>
                            </div>
                            
                            <div class="room-footer">
                                <div class="room-price">
                                    <span class="price-label">Mulai dari</span>
                                    <span class="price-value"><?php echo formatRupiah($type['price']); ?></span>
                                    <span class="price-period">/ malam</span>
                                </div>
                                
                                <?php if (isLoggedIn() && !isAdmin() && $type['available_rooms'] > 0): ?>
                                    <a href="<?php echo url('booking/search'); ?>" class="btn btn-primary btn-book">Pesan</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-rooms">
                <span class="no-rooms-icon">🏨</span>
                <p>Belum ada tipe kamar yang tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Siap untuk Pengalaman Menginap Istimewa?</h2>
            <p>Dapatkan penawaran terbaik dengan memesan langsung melalui website kami</p>
            <?php if (!isLoggedIn()): ?>
                <a href="<?php echo url('auth/register'); ?>" class="btn btn-white btn-lg">Daftar Sekarang</a>
            <?php else: ?>
                <a href="<?php echo url('booking/search'); ?>" class="btn btn-white btn-lg">Pesan Sekarang</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Location Section -->
<section class="location-section" id="location-section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">LOKASI</span>
            <h2 class="section-title">Temukan Kami</h2>
            <p class="section-subtitle">Lokasi strategis di pusat kota Balikpapan</p>
        </div>
        
        <div class="location-content">
            <div class="location-info">
                <div class="location-card">
                    <h3>Kluwa Hotel</h3>
                    <p class="address">
                        Jl. Sei Wain No.34, Karang Joang<br>
                        Kec. Balikpapan Utara<br>
                        Balikpapan, Kalimantan Timur 76127
                    </p>
                    <div class="location-details">
                        <div class="detail-item">
                            <span class="detail-icon">✈️</span>
                            <span>15 menit dari Bandara SAMS Sepinggan</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">🛒</span>
                            <span>10 menit dari Pusat Kota</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">🏖️</span>
                            <span>20 menit dari Pantai Manggar</span>
                        </div>
                    </div>
                    <a href="https://maps.app.goo.gl/oo53Xse7tFfUZeDW9" target="_blank" class="btn btn-primary btn-lg">
                        Buka di Google Maps
                    </a>
                </div>
            </div>
            <div class="location-map">
                <iframe 
                    src="https://maps.google.com/maps?q=Jl.+Sei+Wain+No.34,+Karang+Joang,+Balikpapan&z=18&output=embed"
                    width="100%" 
                    height="100%" 
                    style="border:0; border-radius: 12px;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="google-map-iframe">
                </iframe>
            </div>
        </div>
    </div>
</section>
