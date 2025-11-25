<?php
// Helper functions untuk mempermudah development

// Function untuk redirect ke halaman lain
function redirect($route) {
    header("Location: index.php?route=" . $route);
    exit();
}

// Function untuk mengecek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function untuk mengecek apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Admin';
}

// Function untuk require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('auth/login');
    }
}

// Function untuk require admin
function requireAdmin() {
    if (!isAdmin()) {
        redirect('home');
    }
}

// Function untuk mendapatkan current user
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

// Function untuk validasi data
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = isset($data[$field]) ? trim($data[$field]) : '';
        
        // Required validation
        if (in_array('required', $rule) && empty($value)) {
            $errors[$field] = ucfirst($field) . " wajib diisi";
            continue;
        }
        
        // Email validation
        if (in_array('email', $rule) && !empty($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "Format email tidak valid";
            }
        }
        
        // Min length validation
        foreach ($rule as $r) {
            if (strpos($r, 'min:') === 0) {
                $min = (int)substr($r, 4);
                if (strlen($value) < $min) {
                    $errors[$field] = ucfirst($field) . " minimal $min karakter";
                }
            }
        }
        
        // Numeric validation
        if (in_array('numeric', $rule) && !empty($value)) {
            if (!is_numeric($value)) {
                $errors[$field] = ucfirst($field) . " harus berupa angka";
            }
        }
        
        // Phone validation (only numbers, optional +)
        if (in_array('phone', $rule) && !empty($value)) {
            if (!preg_match('/^[0-9+]+$/', $value)) {
                $errors[$field] = "Nomor telepon hanya boleh berisi angka";
            }
        }
    }
    
    return $errors;
}

// Function untuk escape HTML
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Function untuk format currency
function formatRupiah($number) {
    return "Rp " . number_format($number, 0, ',', '.');
}

// Function untuk format date
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

// Function untuk generate URL
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

// Function untuk asset URL
function asset($path) {
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if ($base === '/' || $base === '\\') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

// Function untuk flash message
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

// Function untuk old input
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
