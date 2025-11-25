<?php
// Controller Home - untuk halaman utama
class HomeController {
    private $roomTypeModel;

    public function __construct() {
        $db = new Database();
        $this->roomTypeModel = new Room_type_model($db->getConnection());
    }

    // Method untuk menampilkan halaman home
    public function index() {
        // Get semua tipe kamar dengan available rooms
        $roomTypes = $this->roomTypeModel->getAllWithAvailableRooms();
        
        // Load view
        include 'views/layouts/header.php';
        include 'views/home.php';
        include 'views/layouts/footer.php';
    }
}
?>
