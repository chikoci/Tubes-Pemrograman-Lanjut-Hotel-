<?php
// Controller Admin - untuk manajemen hotel (room types, rooms, payments, bookings)
class AdminController {
    private $roomTypeModel;
    private $roomModel;
    private $bookingModel;
    private $paymentModel;

    public function __construct() {
        $db = new Database();
        $this->roomTypeModel = new Room_type_model($db->getConnection());
        $this->roomModel = new Room_model($db->getConnection());
        $this->bookingModel = new Booking_model($db->getConnection());
        $this->paymentModel = new Payment_model($db->getConnection());
    }

    // Method untuk dashboard admin
    public function dashboard() {
        requireAdmin();

        // Get statistics
        $stats = $this->bookingModel->getStatistics();
        
        // Get total room types dan rooms
        $totalRoomTypes = count($this->roomTypeModel->getAll());
        $totalRooms = count($this->roomModel->getAll());
        
        $stats['total_room_types'] = $totalRoomTypes;
        $stats['total_rooms'] = $totalRooms;

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/dashboard.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk manajemen tipe kamar
    public function roomTypes() {
        requireAdmin();

        $roomTypes = $this->roomTypeModel->getAll();

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/room_types.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk form tambah/edit tipe kamar
    public function roomTypeForm() {
        requireAdmin();

        $roomType = null;
        $isEdit = false;

        if (isset($_GET['id'])) {
            $isEdit = true;
            $roomType = $this->roomTypeModel->find($_GET['id']);
            
            if (!$roomType) {
                setFlash('error', 'Tipe kamar tidak ditemukan');
                redirect('admin/roomTypes');
                return;
            }
        }

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/room_type_form.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk save tipe kamar (create/update)
    public function saveRoomType() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'description' => trim($_POST['description']),
                'image' => trim($_POST['image'])
            ];

            // Tentukan mode edit/tambah sejak awal
            $isEdit = !empty($_POST['id']);

            // Validasi menggunakan helper validate()
            $errors = validate($data, [
                'name' => ['required'],
                'price' => ['required', 'numeric']
            ]);

            if (empty($errors)) {
                if ($isEdit) {
                    // Update
                    $updated = $this->roomTypeModel->update($_POST['id'], $data);
                    
                    if ($updated) {
                        setFlash('success', 'Tipe kamar berhasil diupdate');
                    } else {
                        setFlash('error', 'Terjadi kesalahan saat update tipe kamar');
                    }
                } else {
                    // Create
                    $created = $this->roomTypeModel->create($data);
                    
                    if ($created) {
                        setFlash('success', 'Tipe kamar berhasil ditambahkan');
                    } else {
                        setFlash('error', 'Terjadi kesalahan saat menambah tipe kamar');
                    }
                }
                
                redirect('admin/roomTypes');
                return;
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                
                if ($isEdit) {
                    redirect('admin/roomTypeForm&id=' . $_POST['id']);
                } else {
                    redirect('admin/roomTypeForm');
                }
            }
        }

        redirect('admin/roomTypes');
    }

    // Method untuk delete tipe kamar
    public function deleteRoomType() {
        requireAdmin();

        if (isset($_GET['id'])) {
            $deleted = $this->roomTypeModel->delete($_GET['id']);
            
            if ($deleted) {
                setFlash('success', 'Tipe kamar berhasil dihapus');
            } else {
                setFlash('error', 'Tidak bisa menghapus tipe kamar karena masih digunakan oleh kamar. Hapus kamar terkait terlebih dahulu.');
            }
        }

        redirect('admin/roomTypes');
    }

    // Method untuk manajemen kamar
    public function rooms() {
        requireAdmin();

        $rooms = $this->roomModel->getAll();

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/rooms.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk form tambah/edit kamar
    public function roomForm() {
        requireAdmin();

        $room = null;
        $isEdit = false;
        $roomTypes = $this->roomTypeModel->getAll();

        if (isset($_GET['id'])) {
            $isEdit = true;
            $room = $this->roomModel->find($_GET['id']);
            
            if (!$room) {
                setFlash('error', 'Kamar tidak ditemukan');
                redirect('admin/rooms');
                return;
            }
        }

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/room_form.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk save kamar (create/update)
    public function saveRoom() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_type_id' => trim($_POST['room_type_id']),
                'room_number' => trim($_POST['room_number']),
                'status' => trim($_POST['status'])
            ];

            // Tentukan mode edit/tambah sejak awal
            $isEdit = !empty($_POST['id']);

            // Validasi menggunakan helper validate()
            $errors = validate($data, [
                'room_type_id' => ['required'],
                'room_number' => ['required']
            ]);

            if (empty($errors)) {
                if ($isEdit) {
                    // Update
                    $updated = $this->roomModel->update($_POST['id'], $data);
                    
                    if ($updated) {
                        setFlash('success', 'Kamar berhasil diupdate');
                    } else {
                        setFlash('error', 'Terjadi kesalahan saat update kamar');
                    }
                } else {
                    // Create
                    $created = $this->roomModel->create($data);
                    
                    if ($created) {
                        setFlash('success', 'Kamar berhasil ditambahkan');
                    } else {
                        setFlash('error', 'Terjadi kesalahan saat menambah kamar');
                    }
                }
                
                redirect('admin/rooms');
                return;
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                
                if ($isEdit) {
                    redirect('admin/roomForm&id=' . $_POST['id']);
                } else {
                    redirect('admin/roomForm');
                }
            }
        }

        redirect('admin/rooms');
    }

    // Method untuk delete kamar
    public function deleteRoom() {
        requireAdmin();

        if (isset($_GET['id'])) {
            $deleted = $this->roomModel->delete($_GET['id']);
            
            if ($deleted) {
                setFlash('success', 'Kamar berhasil dihapus');
            } else {
                setFlash('error', 'Terjadi kesalahan saat menghapus kamar');
            }
        }

        redirect('admin/rooms');
    }

    // Method untuk manajemen pembayaran
    public function payments() {
        requireAdmin();

        $payments = $this->paymentModel->getAllWithDetails();

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/payments.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk approve payment (menggunakan transaksi PDO)
    public function approvePayment() {
        requireAdmin();

        if (isset($_GET['id'])) {
            $approved = $this->paymentModel->approvePayment($_GET['id']);
            
            if ($approved) {
                setFlash('success', 'Pembayaran berhasil dikonfirmasi');
            } else {
                setFlash('error', 'Terjadi kesalahan saat konfirmasi pembayaran');
            }
        }

        redirect('admin/payments');
    }

    // Method untuk manajemen bookings
    public function bookings() {
        requireAdmin();

        $bookings = $this->bookingModel->getAll();

        // Load view
        include 'views/layouts/header.php';
        include 'views/admin/bookings.php';
        include 'views/layouts/footer.php';
    }
}
?>
