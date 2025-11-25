<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Password yang ingin kita hash
$password_admin = 'admin123';

// Buat hash
$hash_admin = password_hash($password_admin, PASSWORD_DEFAULT);

echo "<h1>Hash Password Baru</h1>";

echo "<p>Salin hash ini untuk Admin (NIM admin@hotel.com):</p>";
echo "<code>" . $hash_admin . "</code>";

echo "<hr>";
echo "Panjang hash (semua harus 60 karakter): " . strlen($hash_admin);
?>