<?php
// Admin Controller - manajemen hotel
class AdminController extends BaseController {
    private $roomTypeModel;
    private $roomModel;
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->roomTypeModel = new Room_type_model($this->db);
        $this->roomModel = new Room_model($this->db);
        $this->bookingModel = new Booking_model($this->db);
    }

    // Dashboard admin
    public function dashboard() {
        requireAdmin();

        // ambil statistik
        $stats = $this->bookingModel->getStatistics();
        
        // hitung total room types dan rooms
        $totalRoomTypes = count($this->roomTypeModel->getAll());
        $totalRooms = count($this->roomModel->getAll());
        
        $stats['total_room_types'] = $totalRoomTypes;
        $stats['total_rooms'] = $totalRooms;

        $this->view('admin/dashboard', ['stats' => $stats]);
    }

    // manajemen tipe kamar
    public function roomTypes() {
        requireAdmin();

        $roomTypes = $this->roomTypeModel->getAll();

        $this->view('admin/room_types', ['roomTypes' => $roomTypes]);
    }

    // Form tambah/edit tipe kamar
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

        $data = [
            'roomType' => $roomType,
            'isEdit' => $isEdit
        ];
        $this->view('admin/room_type_form', $data);
    }

    // save room type
    public function saveRoomType() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'price' => trim($_POST['price']),
                'description' => trim($_POST['description'])
            ];

            // cek mode edit atau tambah baru
            $isEdit = !empty($_POST['id']);

            // validasi data
            $errors = validate($data, [
                'name' => ['required'],
                'price' => ['required', 'numeric']
            ]);

            // Cek apakah nama tipe kamar sudah ada
            if (empty($errors)) {
                $existingType = $this->roomTypeModel->findByName($data['name']);
                if ($existingType) {
                    // Jika edit, pastikan bukan tipe yang sama
                    if (!$isEdit || ($isEdit && $existingType['id'] != $_POST['id'])) {
                        $errors['name'] = 'Nama tipe kamar sudah digunakan';
                    }
                }
            }

            // Handle upload gambar
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = 'uploads/rooms/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($fileExt, $allowedExt)) {
                    $errors['image'] = 'Format file tidak valid. Gunakan JPG, PNG, GIF, atau WEBP';
                } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                    $errors['image'] = 'Ukuran file maksimal 2MB';
                } else {
                    // Buat nama file dari nama tipe kamar
                    $fileName = strtolower(str_replace(' ', '_', $data['name'])) . '.' . $fileExt;
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                        $imagePath = 'rooms/' . $fileName;
                    } else {
                        $errors['image'] = 'Gagal mengupload gambar';
                    }
                }
            }

            if (empty($errors)) {
                // Tambahkan image ke data jika ada upload baru
                if ($imagePath) {
                    $data['image'] = $imagePath;
                }

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

    // hapus tipe kamar
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

    // Manajemen kamar
    public function rooms() {
        requireAdmin();

        $rooms = $this->roomModel->getAll();

        $this->view('admin/rooms', ['rooms' => $rooms]);
    }

    // form add/edit room
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

        $data = [
            'room' => $room,
            'isEdit' => $isEdit,
            'roomTypes' => $roomTypes
        ];
        $this->view('admin/room_form', $data);
    }

    // save room data
    public function saveRoom() {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_type_id' => trim($_POST['room_type_id']),
                'room_number' => trim($_POST['room_number']),
                'status' => trim($_POST['status'])
            ];

            // cek edit atau tambah baru
            $isEdit = !empty($_POST['id']);

            // validasi data
            $errors = validate($data, [
                'room_type_id' => ['required'],
                'room_number' => ['required']
            ]);

            // Cek apakah nomor kamar sudah dipakai
            if (empty($errors)) {
                $existingRoom = $this->roomModel->findByRoomNumber($data['room_number']);
                if ($existingRoom) {
                    // Jika edit, pastikan bukan kamar yang sama
                    if (!$isEdit || ($isEdit && $existingRoom['id'] != $_POST['id'])) {
                        $errors['room_number'] = 'Nomor kamar sudah digunakan';
                    }
                }
            }

            if (empty($errors)) {
                if ($isEdit) {
                    // update data
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

    // delete room
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

    // manajemen bookings
    public function bookings() {
        requireAdmin();

        $bookings = $this->bookingModel->getAll();

        $this->view('admin/bookings', ['bookings' => $bookings]);
    }

    // manajemen pembayaran
    public function payments() {
        requireAdmin();

        $bookings = $this->bookingModel->getAll();

        $this->view('admin/payments', ['bookings' => $bookings]);
    }
}
?>
