<?php
// Front Controller - Titik masuk utama aplikasi
session_start();

// Include file-file yang diperlukan
require_once 'config/koneksi.php';
require_once 'models/QueryBuilder.php';
require_once 'helpers.php';
require_once 'controllers/BaseController.php';

// Autoload untuk models dan controllers
spl_autoload_register(function ($class) {
    $folders = ['models', 'controllers'];
    
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

// Handle route
if ($route === 'home') {
    // Default route
    $controller = new HomeController();
    $controller->index();
} else {
    // Parse route: "controller/method" -> Controller + Method
    $parts = explode('/', $route);
    
    if (count($parts) === 2) {
        $controllerName = ucfirst($parts[0]) . 'Controller'; // auth -> AuthController
        $methodName = $parts[1]; // login -> login
        
        // Cek apakah controller dan method ada
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                http_response_code(404);
                echo "<h1>404 - Method tidak ditemukan</h1>";
            }
        } else {
            http_response_code(404);
            echo "<h1>404 - Controller tidak ditemukan</h1>";
        }
    } else {
        http_response_code(404);
        echo "<h1>404 - Halaman tidak ditemukan</h1>";
    }
}
?>
