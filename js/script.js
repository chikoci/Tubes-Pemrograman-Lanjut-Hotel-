// JavaScript untuk interaksi tambahan

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
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
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            // Set min check-out date satu hari setelah check-in
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            const minCheckOut = checkInDate.toISOString().split('T')[0];
            checkOutInput.setAttribute('min', minCheckOut);
            
            // Reset check-out jika lebih kecil dari min
            if (checkOutInput.value && checkOutInput.value < minCheckOut) {
                checkOutInput.value = '';
            }
        });
    }
    
    // Konfirmasi delete
    const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Format input harga (auto format ribuan)
    const priceInputs = document.querySelectorAll('input[name="price"]');
    priceInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = parseInt(value);
            }
        });
    });
    
    // Mobile menu toggle (untuk responsive)
    const navbar = document.querySelector('.navbar');
    if (navbar && window.innerWidth < 768) {
        const menuButton = document.createElement('button');
        menuButton.className = 'menu-toggle';
        menuButton.innerHTML = '☰';
        menuButton.style.cssText = 'background:none;border:none;color:white;font-size:1.5rem;cursor:pointer;display:none;';
        
        if (window.innerWidth < 768) {
            menuButton.style.display = 'block';
            navbar.querySelector('.container').prepend(menuButton);
            
            const navMenu = navbar.querySelector('.nav-menu');
            navMenu.style.display = 'none';
            
            menuButton.addEventListener('click', function() {
                navMenu.style.display = navMenu.style.display === 'none' ? 'flex' : 'none';
            });
        }
    }
    
    // Smooth scroll untuk anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
});

// Helper function untuk format currency
function formatCurrency(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

// Helper function untuk format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
