<?php
// Base Controller - parent untuk semua controller
class BaseController {
    protected $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Load view dengan layout
    protected function view($viewPath, $data = []) {
        extract($data);
        include 'views/layouts/header.php';
        include "views/{$viewPath}.php";
        include 'views/layouts/footer.php';
    }
}
?>
