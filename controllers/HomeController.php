<?php
// Controller Home - untuk halaman utama
class HomeController extends BaseController {
    private $roomTypeModel;

    public function __construct() {
        parent::__construct();
        $this->roomTypeModel = new Room_type_model($this->db);
    }

    // Method untuk menampilkan halaman home
    public function index() {
        $roomTypes = $this->roomTypeModel->getAllWithAvailableRooms();
        
        $this->view('home', ['roomTypes' => $roomTypes]);
    }
}
?>
