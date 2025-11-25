<?php
// Front Controller - Titik masuk utama aplikasi
session_start();

// Include file-file yang diperlukan
require_once 'config/koneksi.php';
require_once 'Models/QueryBuilder.php';
require_once 'helpers.php';

// Autoload untuk models dan controllers
spl_autoload_register(function ($class) {
    $folders = ['Models', 'controllers'];
    
    foreach ($folders as $folder) {
        $file = __DIR__ . '/' . $folder . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Routing sederhana
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Definisi routes
$routes = [
    'home' => ['controller' => 'HomeController', 'method' => 'index'],
    
    // Auth routes
    'auth/login' => ['controller' => 'AuthController', 'method' => 'login'],
    'auth/register' => ['controller' => 'AuthController', 'method' => 'register'],
    'auth/logout' => ['controller' => 'AuthController', 'method' => 'logout'],
    'auth/profile' => ['controller' => 'AuthController', 'method' => 'profile'],
    'auth/updateProfile' => ['controller' => 'AuthController', 'method' => 'updateProfile'],
    'auth/forgotPassword' => ['controller' => 'AuthController', 'method' => 'forgotPassword'],
    'auth/sendResetCode' => ['controller' => 'AuthController', 'method' => 'sendResetCode'],
    'auth/resetPassword' => ['controller' => 'AuthController', 'method' => 'resetPassword'],
    
    // Booking routes
    'booking/search' => ['controller' => 'BookingController', 'method' => 'search'],
    'booking/create' => ['controller' => 'BookingController', 'method' => 'create'],
    'booking/store' => ['controller' => 'BookingController', 'method' => 'store'],
    'booking/payment' => ['controller' => 'BookingController', 'method' => 'payment'],
    'booking/confirmPayment' => ['controller' => 'BookingController', 'method' => 'confirmPayment'],
    'booking/myBookings' => ['controller' => 'BookingController', 'method' => 'myBookings'],
    
    // Admin routes
    'admin/dashboard' => ['controller' => 'AdminController', 'method' => 'dashboard'],
    'admin/roomTypes' => ['controller' => 'AdminController', 'method' => 'roomTypes'],
    'admin/roomTypeForm' => ['controller' => 'AdminController', 'method' => 'roomTypeForm'],
    'admin/saveRoomType' => ['controller' => 'AdminController', 'method' => 'saveRoomType'],
    'admin/deleteRoomType' => ['controller' => 'AdminController', 'method' => 'deleteRoomType'],
    'admin/rooms' => ['controller' => 'AdminController', 'method' => 'rooms'],
    'admin/roomForm' => ['controller' => 'AdminController', 'method' => 'roomForm'],
    'admin/saveRoom' => ['controller' => 'AdminController', 'method' => 'saveRoom'],
    'admin/deleteRoom' => ['controller' => 'AdminController', 'method' => 'deleteRoom'],
    'admin/payments' => ['controller' => 'AdminController', 'method' => 'payments'],
    'admin/approvePayment' => ['controller' => 'AdminController', 'method' => 'approvePayment'],
    'admin/bookings' => ['controller' => 'AdminController', 'method' => 'bookings'],
];

// Cek apakah route ada
if (array_key_exists($route, $routes)) {
    $controllerName = $routes[$route]['controller'];
    $methodName = $routes[$route]['method'];
    
    // Instantiate controller dan panggil method
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    // 404 Not Found
    http_response_code(404);
    echo "<h1>404 - Halaman tidak ditemukan</h1>";
}
?>
