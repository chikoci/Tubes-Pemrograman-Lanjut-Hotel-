<?php
// Helper functions

// redirect ke halaman lain
function redirect($route) {
    header("Location: index.php?route=" . $route);
    exit();
}

// cek apakah user udah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// cek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Admin';
}

// require login dulu
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

// require admin
function requireAdmin() {
    if (!isAdmin()) {
        redirect('home');
    }
}

// ambil data user yang login
function currentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role_name' => $_SESSION['role_name']
        ];
    }
    return null;
}

// validasi data
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        
        // validasi required
        if (in_array('required', $rule) && empty($value)) {
            $errors[$field] = ucfirst($field) . " wajib diisi";
            continue;
        }
        
        // validasi email
        if (in_array('email', $rule) && !empty($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Format email tidak valid";
            }
        }
        
        // validasi minimal karakter
        foreach ($rule as $r) {
            if (strpos($r, 'min:') === 0) {
                $min = (int)substr($r, 4);
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst($field) . " minimal $min karakter";
                }
            }
        }
        
        // validasi angka
        if (in_array('numeric', $rule) && !empty($value)) {
            if (!is_numeric($value)) {
                $errors[$field] = ucfirst($field) . " harus berupa angka";
            }
        }
        
        // validasi nomor telepon (cuma angka)
        if (in_array('phone', $rule) && !empty($value)) {
            if (!preg_match('/^[0-9+]+$/', $value)) {
                $errors[$field] = "Nomor telepon hanya boleh berisi angka";
            }
        }
    }
    
    return $errors;
}

// escape HTML biar aman
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// format rupiah
function formatRupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}

// format tanggal
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// generate URL
function url($route = '', $params = []) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') {
        $base = '';
    }
    $url = $base . '/index.php' . ($route ? '?route=' . $route : '');
    
    // Tambahkan parameter tambahan jika ada
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $url .= '&' . $key . '=' . urlencode($value);
        }
    }
    
    return $url;
}

// generate URL untuk asset
function asset($path) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

// flash message
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
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// ambil old input
function old($field, $default = '') {
    return isset($_SESSION['old'][$field]) ? $_SESSION['old'][$field] : $default;
}

function setOld($data) {
    $_SESSION['old'] = $data;
}

function clearOld() {
    unset($_SESSION['old']);
}
?>
