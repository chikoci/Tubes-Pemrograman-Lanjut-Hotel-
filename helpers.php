<?php
// Helper functions untuk aplikasi hotel

// Redirect ke halaman lain
function redirect($route) {
    header("Location: index.php?route=" . $route);
    exit();
}

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Cek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Admin';
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

// Require admin
function requireAdmin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
    if (!isAdmin()) {
        redirect('home');
    }
}

// Ambil data user yang login
function currentUser() {
    if (isLoggedIn()) {
        require_once 'models/User_model.php';
        require_once 'config/koneksi.php';
        
        $database = new Database();
        $db = $database->getConnection();
        $userModel = new User_model($db);
        $user = $userModel->find($_SESSION['user_id']);
        
        if ($user) {
            $user['role_name'] = $_SESSION['role_name'];
            return $user;
        }
    }
    return null;
}

// Validasi data input
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        
        if (in_array('required', $rule) && empty($value)) {
            $errors[$field] = ucfirst($field) . " wajib diisi";
            continue;
        }
        
        if (in_array('email', $rule) && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[$field] = "Format email tidak valid";
        }
        
        foreach ($rule as $r) {
            if (strpos($r, 'min:') === 0) {
                $min = (int)substr($r, 4);
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst($field) . " minimal $min karakter";
                }
            }
        }
        
        if (in_array('numeric', $rule) && !empty($value) && !is_numeric($value)) {
            $errors[$field] = ucfirst($field) . " harus berupa angka";
        }
        
        if (in_array('phone', $rule) && !empty($value) && !preg_match('/^[0-9+]+$/', $value)) {
            $errors[$field] = "Nomor telepon hanya boleh berisi angka";
        }
    }
    
    return $errors;
}

// Escape HTML
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Format rupiah
function formatRupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}

// Format tanggal
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Generate URL
function url($route = '', $params = []) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') $base = '';
    
    $url = $base . '/index.php' . ($route ? '?route=' . $route : '');
    
    foreach ($params as $key => $value) {
        $url .= '&' . $key . '=' . urlencode($value);
    }
    
    return $url;
}

// Generate URL untuk asset
function asset($path) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') $base = '';
    return $base . '/' . ltrim($path, '/');
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Old input (untuk form yang error)
function old($field, $default = '') {
    return isset($_SESSION['old'][$field]) ? $_SESSION['old'][$field] : $default;
}

function setOld($data) {
    $_SESSION['old'] = $data;
}

function clearOld() {
    unset($_SESSION['old']);
}

// Get payment icon
function getPaymentIcon($paymentTypeName) {
    $icons = [
        'QRIS' => '📱',
        'Transfer Bank' => '🏦',
        'Debit Bank' => '💳',
        'Cash' => '💵'
    ];
    return $icons[$paymentTypeName] ?? '💰';
}

// Generate random booking code
function generateBookingCode($length = 8) {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Excluded I, O, 0, 1 to avoid confusion
    $code = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, $max)];
    }
    return 'KLW-' . $code; // Prefix with hotel name
}

// Send booking confirmation email
function sendBookingEmail($booking, $userEmail, $userName) {
    $subject = 'Konfirmasi Booking - Kluwa Hotel';
    
    $message = "Halo " . $userName . ",\n\n";
    $message .= "Terima kasih telah melakukan reservasi di Kluwa Hotel.\n\n";
    $message .= "=================================\n";
    $message .= "DETAIL BOOKING\n";
    $message .= "=================================\n\n";
    $message .= "Kode Booking: " . $booking['booking_code'] . "\n";
    $message .= "Tipe Kamar: " . $booking['room_type_name'] . "\n";
    $message .= "Nomor Kamar: " . $booking['room_number'] . "\n";
    $message .= "Check-in: " . formatDate($booking['check_in_date']) . "\n";
    $message .= "Check-out: " . formatDate($booking['check_out_date']) . "\n";
    $message .= "Total Pembayaran: " . formatRupiah($booking['total_price']) . "\n\n";
    $message .= "=================================\n\n";
    $message .= "Silakan tunjukkan kode booking ini saat check-in.\n";
    $message .= "Harap lakukan pembayaran dalam waktu 24 jam untuk mengkonfirmasi reservasi Anda.\n\n";
    $message .= "Jika ada pertanyaan, silakan hubungi kami di:\n";
    $message .= "Telepon: +62 542 123 4567\n";
    $message .= "WhatsApp: +62 812 3456 7890\n";
    $message .= "Email: info@kluwahotel.com\n\n";
    $message .= "Terima kasih,\n";
    $message .= "Tim Kluwa Hotel\n";
    
    @mail($userEmail, $subject, $message);
}
?>
