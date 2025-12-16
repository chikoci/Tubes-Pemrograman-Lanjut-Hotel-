// JavaScript untuk interaksi tambahan

// Toggle User Dropdown Menu
function toggleUserMenu() {
    var menu = document.getElementById('userDropdownMenu');
    if (menu) {
        menu.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    var dropdown = document.querySelector('.nav-user-dropdown');
    var menu = document.getElementById('userDropdownMenu');
    
    if (dropdown && menu && !dropdown.contains(e.target)) {
        menu.classList.remove('show');
    }
});

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle nav-home-only visibility dengan animasi
    handleHomeOnlyNav();
    
    // Auto-close alerts after 5 seconds
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Form validation untuk tanggal booking
    var checkInInput = document.getElementById('check_in');
    var checkOutInput = document.getElementById('check_out');
    
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            // Set min check-out date satu hari setelah check-in
            var checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            var minCheckOut = checkInDate.toISOString().split('T')[0];
            checkOutInput.setAttribute('min', minCheckOut);
            
            // Reset check-out jika lebih kecil dari min
            if (checkOutInput.value && checkOutInput.value < minCheckOut) {
                checkOutInput.value = '';
            }
        });
    }
    
    // Smooth scroll untuk anchor links (termasuk .scroll-link)
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            var href = this.getAttribute('href');
            if (href && href.length > 1) {
                var target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
    
    // Countdown timer untuk halaman my_bookings
    if (document.querySelectorAll('.countdown-timer').length > 0) {
        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    }
    
    // Payment method selection handler untuk halaman payment
    var paymentRadios = document.querySelectorAll('input[name="payment_type_id"]');
    if (paymentRadios.length > 0) {
        paymentRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                // Sembunyikan semua info
                document.querySelectorAll('.payment-info').forEach(function(info) {
                    info.style.display = 'none';
                });
                
                // Disable dan remove required dari semua file input
                document.querySelectorAll('input[type="file"]').forEach(function(input) {
                    input.removeAttribute('required');
                    input.setAttribute('disabled', 'disabled');
                });
                
                // Ambil payment type name dari label
                var label = this.closest('label');
                var typeName = label.querySelector('.method-name').textContent;
                
                // Tampilkan info sesuai metode
                if (typeName === 'QRIS') {
                    document.getElementById('qrisInfo').style.display = 'block';
                } else if (typeName === 'Transfer Bank') {
                    document.getElementById('transferInfo').style.display = 'block';
                    var transferInput = document.getElementById('transferProof');
                    transferInput.removeAttribute('disabled');
                    transferInput.setAttribute('required', 'required');
                } else if (typeName === 'Debit Bank') {
                    document.getElementById('debitInfo').style.display = 'block';
                    var debitInput = document.getElementById('debitProof');
                    debitInput.removeAttribute('disabled');
                    debitInput.setAttribute('required', 'required');
                } else if (typeName === 'Cash') {
                    document.getElementById('cashInfo').style.display = 'block';
                    var cashInput = document.getElementById('cashProof');
                    cashInput.removeAttribute('disabled');
                    cashInput.setAttribute('required', 'required');
                }
            });
        });
    }
    
});

// Countdown timer untuk halaman my_bookings
function updateCountdowns() {
    var timers = document.querySelectorAll('.countdown-timer');
    
    timers.forEach(function(timer) {
        var expireTime = parseInt(timer.dataset.expire);
        var now = Math.floor(Date.now() / 1000);
        var remaining = expireTime - now;
        
        if (remaining <= 0) {
            // Waktu habis, auto-cancel booking
            timer.querySelector('.countdown-display').innerHTML = 
                '<span style="color: #e74c3c;"> Waktu Habis - Booking akan dibatalkan...</span>';
            
            // Reload page untuk update status
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        } else {
            // Hitung jam, menit, detik
            var hours = Math.floor(remaining / 3600);
            var minutes = Math.floor((remaining % 3600) / 60);
            var seconds = remaining % 60;
            
            // Format tampilan
            var display = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
            
            timer.querySelector('.countdown-display').textContent = display;
            
            // Ubah warna berdasarkan sisa waktu
            if (remaining < 3600) {
                timer.style.color = '#e74c3c'; // merah
            } else if (remaining < 6 * 3600) {
                timer.style.color = '#f39c12'; // orange
            } else {
                timer.style.color = '#27ae60'; // hijau
            }
        }
    });
}

// QRIS payment handler
function initQrisPayment(bookingId, amount) {
    // URL dummy untuk QR Code
    var qrUrl = 'QRIS://DEMO/PAYMENT/' + bookingId + '/AMOUNT/' + amount;
    
    // Generate QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: qrUrl,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    // Auto-approve setelah 10 detik
    var countdown = 10;
    var countdownElement = document.getElementById('countdown');
    
    var countdownInterval = setInterval(function() {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            
            document.getElementById('statusMessage').innerHTML = 
                '<span style="color: orange;">Memproses pembayaran...</span>';
            
            // Panggil API untuk approve payment
            fetch('index.php?route=booking/qrisAutoApprove&booking_id=' + bookingId)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        document.getElementById('statusMessage').innerHTML = 
                            '<span style="color: green;">✓ Pembayaran Berhasil!</span>';
                        document.getElementById('loadingSpinner').style.display = 'none';
                        
                        setTimeout(function() {
                            window.location.href = 'index.php?route=booking/myBookings';
                        }, 2000);
                    } else {
                        document.getElementById('statusMessage').innerHTML = 
                            '<span style="color: red;">✗ Gagal memproses pembayaran</span>';
                        document.getElementById('loadingSpinner').style.display = 'none';
                    }
                })
                .catch(function(error) {
                    document.getElementById('statusMessage').innerHTML = 
                        '<span style="color: red;">✗ Terjadi kesalahan</span>';
                    document.getElementById('loadingSpinner').style.display = 'none';
                });
        }
    }, 1000);
}

// Handle nav-home-only visibility
function handleHomeOnlyNav() {
    var page = document.body.getAttribute('data-page') || 'home';
    var homeOnlyItems = document.querySelectorAll('.nav-home-only');
    
    // Halaman yang menyembunyikan menu Kamar & Lokasi
    var hiddenPages = [
        'booking/search',
        'booking/myBookings',
        'booking/create',
        'booking/payment',
        'booking/qris',
        'auth/profile',
        'admin/dashboard',
        'admin/roomTypes',
        'admin/roomTypeForm',
        'admin/rooms',
        'admin/roomForm',
        'admin/bookings',
        'admin/payments'
    ];
    
    var shouldHide = false;
    for (var i = 0; i < hiddenPages.length; i++) {
        if (page.indexOf(hiddenPages[i]) !== -1) {
            shouldHide = true;
            break;
        }
    }
    
    homeOnlyItems.forEach(function(item) {
        if (shouldHide) {
            // Sembunyikan dengan animasi
            item.classList.add('nav-hidden');
            item.classList.remove('nav-visible');
        } else {
            // Tampilkan dengan animasi slide dari kiri
            item.classList.remove('nav-hidden');
            item.classList.add('nav-visible');
        }
    });
}
