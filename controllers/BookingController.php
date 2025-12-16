<?php
// Booking Controller
class BookingController extends BaseController {
    private $roomModel;
    private $bookingModel;
    private $paymentTypeModel;

    public function __construct() {
        parent::__construct();
        $this->roomModel = new Room_model($this->db);
        $this->bookingModel = new Booking_model($this->db);
        $this->paymentTypeModel = new Payment_type_model($this->db);
    }

    // search available rooms
    public function search() {
        requireLogin();

        $availableRooms = [];
        $checkIn = '';
        $checkOut = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $checkIn = $_POST['check_in'];
            $checkOut = $_POST['check_out'];

            // Validasi input menggunakan helper validate()
            $errors = validate($_POST, [
                'check_in' => ['required'],
                'check_out' => ['required']
            ]);

            if (empty($errors)) {
                // Validasi tanggal
                $today = date('Y-m-d');
                if ($checkIn < $today) {
                    $errors['check_in'] = 'Tanggal check-in tidak boleh kurang dari hari ini';
                }
                if ($checkOut <= $checkIn) {
                    $errors['check_out'] = 'Tanggal check-out harus lebih dari check-in';
                }
            }

            if (empty($errors)) {
                // Search available rooms
                $availableRooms = $this->roomModel->searchAvailable($checkIn, $checkOut);
                
                if (empty($availableRooms)) {
                    setFlash('info', 'Tidak ada kamar tersedia untuk tanggal tersebut');
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }

        $data = [
            'availableRooms' => $availableRooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut
        ];
        $this->view('booking/search', $data);
    }

    // buat booking baru
    public function create() {
        requireLogin();

        if (!isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
            setFlash('error', 'Data booking tidak lengkap');
            redirect('booking/search');
            return;
        }

        $roomId = $_GET['room_id'];
        $checkIn = $_GET['check_in'];
        $checkOut = $_GET['check_out'];

        // Get room details
        $room = $this->roomModel->find($roomId);
        
        if (!$room) {
            setFlash('error', 'Kamar tidak ditemukan');
            redirect('booking/search');
            return;
        }

        // Cek availability
        if (!$this->roomModel->isAvailable($roomId, $checkIn, $checkOut)) {
            setFlash('error', 'Kamar tidak tersedia untuk tanggal tersebut');
            redirect('booking/search');
            return;
        }

        // Hitung total
        $nights = $this->bookingModel->calculateNights($checkIn, $checkOut);
        $totalPrice = $room['price'] * $nights;

        $data = [
            'room' => $room,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'nights' => $nights,
            'totalPrice' => $totalPrice
        ];
        $this->view('booking/create', $data);
    }

    // simpan booking
    public function store() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomId = $_POST['room_id'];
            $checkIn = $_POST['check_in'];
            $checkOut = $_POST['check_out'];
            $totalPrice = $_POST['total_price'];

            // Cek availability lagi
            if (!$this->roomModel->isAvailable($roomId, $checkIn, $checkOut)) {
                setFlash('error', 'Kamar tidak tersedia untuk tanggal tersebut');
                redirect('booking/search');
                return;
            }

            // Validasi user_id dari session
            if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
                setFlash('error', 'Session tidak valid. Silakan login kembali.');
                redirect('auth/login');
                return;
            }

            // Generate unique booking code
            $bookingCode = generateBookingCode();
            
            // Pastikan kode booking unik
            while ($this->bookingModel->findByCode($bookingCode)) {
                $bookingCode = generateBookingCode();
            }

            // Create booking
            date_default_timezone_set('Asia/Jakarta');
            $bookingData = [
                'booking_code' => $bookingCode,
                'user_id' => (int)$_SESSION['user_id'],
                'room_id' => $roomId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_price' => $totalPrice,
                'status' => 'Pending'
            ];

            try {
                $bookingId = $this->bookingModel->create($bookingData);

                if ($bookingId) {
                    // Get full booking details for email
                    $bookingDetails = $this->bookingModel->find($bookingId);
                    
                    // Send confirmation email
                    sendBookingEmail($bookingDetails, $_SESSION['user_email'], $_SESSION['user_name']);
                    
                    setFlash('success', 'Booking berhasil dibuat! Kode booking: ' . $bookingCode . '. Detail telah dikirim ke email Anda.');
                    // Gunakan format route + &param agar URL valid tanpa .htaccess
                    redirect('booking/payment&booking_id=' . $bookingId);
                    return;
                } else {
                    setFlash('error', 'Terjadi kesalahan saat membuat booking');
                    redirect('booking/search');
                }
            } catch (PDOException $e) {
                // Log error untuk debugging
                error_log("Booking error: " . $e->getMessage());
                
                if (strpos($e->getMessage(), 'user_id') !== false) {
                    setFlash('error', 'User ID tidak valid. Silakan logout dan login kembali.');
                    redirect('auth/logout');
                } else {
                    setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
                    redirect('booking/search');
                }
            }
        }

        redirect('booking/search');
    }

    // halaman payment
    public function payment() {
        requireLogin();

        if (!isset($_GET['booking_id'])) {
            redirect('booking/myBookings');
            return;
        }

        $bookingId = $_GET['booking_id'];
        $booking = $this->bookingModel->find($bookingId);

        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            setFlash('error', 'Booking tidak ditemukan');
            redirect('booking/myBookings');
            return;
        }

        // Ambil payment types
        $paymentTypes = $this->paymentTypeModel->getAll();

        $data = [
            'booking' => $booking,
            'paymentTypes' => $paymentTypes
        ];
        $this->view('booking/payment', $data);
    }

    // Method untuk confirm payment
    public function confirmPayment() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'];
            $paymentTypeId = $_POST['payment_type_id'];
            
            // Validasi payment type
            $paymentType = $this->paymentTypeModel->find($paymentTypeId);
            if (!$paymentType) {
                setFlash('error', 'Metode pembayaran tidak valid');
                redirect('booking/payment&booking_id=' . $bookingId);
                return;
            }
            
            $paymentProof = null;
            $paymentStatus = 'Pending';
            
            // Handle upload bukti pembayaran untuk metode non-QRIS
            if ($paymentType['name'] !== 'QRIS') {
                if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0) {
                    $uploadDir = 'uploads/payments/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileExt = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
                    $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
                    
                    if (!in_array(strtolower($fileExt), $allowedExt)) {
                        setFlash('error', 'Format file tidak valid. Gunakan JPG, PNG, atau PDF');
                        redirect('booking/payment&booking_id=' . $bookingId);
                        return;
                    }
                    
                    // max 5MB
                    if ($_FILES['payment_proof']['size'] > 5 * 1024 * 1024) {
                        setFlash('error', 'Ukuran file maksimal 5MB');
                        redirect('booking/payment&booking_id=' . $bookingId);
                        return;
                    }
                    
                    $fileName = 'payment_' . $bookingId . '_' . time() . '.' . $fileExt;
                    $uploadPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadPath)) {
                        $paymentProof = $uploadPath;
                        // Auto approve jika sudah upload bukti
                        $paymentStatus = 'Success';
                    } else {
                        setFlash('error', 'Gagal mengupload bukti pembayaran');
                        redirect('booking/payment&booking_id=' . $bookingId);
                        return;
                    }
                } else {
                    setFlash('error', 'Bukti pembayaran wajib diupload');
                    redirect('booking/payment&booking_id=' . $bookingId);
                    return;
                }
            }

            // Update payment info di booking
            $paymentData = [
                'payment_type_id' => $paymentTypeId,
                'payment_proof' => $paymentProof,
                'payment_date' => date('Y-m-d H:i:s'),
                'payment_status' => $paymentStatus
            ];

            try {
                $updated = $this->bookingModel->updatePayment($bookingId, $paymentData);

                if ($updated) {
                    // Jika auto approved, update booking status
                    if ($paymentStatus === 'Success') {
                        $this->bookingModel->updateStatus($bookingId, 'Confirmed');
                        setFlash('success', 'Pembayaran berhasil! Booking Anda telah dikonfirmasi.');
                        redirect('booking/myBookings');
                    } else {
                        // Untuk QRIS redirect ke halaman scan
                        redirect('booking/qrisPayment&booking_id=' . $bookingId);
                    }
                    return;
                } else {
                    setFlash('error', 'Terjadi kesalahan saat memproses pembayaran');
                    redirect('booking/payment&booking_id=' . $bookingId);
                }
            } catch (PDOException $e) {
                error_log("Payment error: " . $e->getMessage());
                setFlash('error', 'Terjadi kesalahan database saat memproses pembayaran');
                redirect('booking/payment&booking_id=' . $bookingId);
            }
        }

        redirect('booking/myBookings');
    }

    // cancel booking
    public function cancelBooking() {
        requireLogin();
        
        if (!isset($_GET['booking_id'])) {
            redirect('booking/myBookings');
            return;
        }
        
        $bookingId = $_GET['booking_id'];
        $booking = $this->bookingModel->find($bookingId);
        
        // validasi booking ada dan milik user yang login
        if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
            setFlash('error', 'Booking tidak ditemukan');
            redirect('booking/myBookings');
            return;
        }
        
        // cek apakah booking masih bisa dibatalkan (status Pending)
        if ($booking['status'] !== 'Pending') {
            setFlash('error', 'Booking tidak dapat dibatalkan. Status: ' . $booking['status']);
            redirect('booking/myBookings');
            return;
        }
        
        // cek apakah sudah ada payment yang success
        if ($booking['payment_status'] === 'Success') {
            setFlash('error', 'Booking tidak dapat dibatalkan karena sudah dibayar');
            redirect('booking/myBookings');
            return;
        }
        
        // update status booking jadi Cancelled
        $updated = $this->bookingModel->updateStatus($bookingId, 'Cancelled');
        
        if ($updated) {
            setFlash('success', 'Booking berhasil dibatalkan');
        } else {
            setFlash('error', 'Gagal membatalkan booking');
        }
        
        redirect('booking/myBookings');
    }

    // halaman QRIS payment
    public function qrisPayment() {
        requireLogin();
        
        if (!isset($_GET['booking_id'])) {
            redirect('booking/myBookings');
            return;
        }
        
        $bookingId = $_GET['booking_id'];
        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            setFlash('error', 'Booking tidak ditemukan');
            redirect('booking/myBookings');
            return;
        }
        
        if ($booking['user_id'] != $_SESSION['user_id']) {
            setFlash('error', 'Akses ditolak');
            redirect('booking/myBookings');
            return;
        }
        
        $this->view('booking/qris', ['booking' => $booking]);
    }
    
    // auto approve QRIS payment setelah 10 detik
    public function qrisAutoApprove() {
        if (!isset($_GET['booking_id'])) {
            echo json_encode(['success' => false, 'message' => 'Booking ID required']);
            return;
        }
        
        $bookingId = $_GET['booking_id'];
        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            return;
        }
        
        try {
            // Auto approve payment
            $approved = $this->bookingModel->approvePayment($bookingId);
            
            echo json_encode([
                'success' => $approved,
                'message' => $approved ? 'Payment approved' : 'Failed to approve',
                'booking_id' => $bookingId
            ]);
        } catch (PDOException $e) {
            error_log("QRIS approve error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'booking_id' => $bookingId
            ]);
        }
    }

    // Method untuk melihat booking user
    public function myBookings() {
        requireLogin();

        // Auto-cancel booking yang sudah lewat 23 jam
        $this->autoCancelExpiredBookings();

        $bookings = $this->bookingModel->getByUser($_SESSION['user_id']);

        $this->view('booking/my_bookings', ['bookings' => $bookings]);
    }
    
    // auto-cancel booking yang expired (lebih dari 24 jam)
    private function autoCancelExpiredBookings() {
        // Set timezone
        date_default_timezone_set('Asia/Jakarta');
        
        // Ambil semua booking pending milik user
        $userId = $_SESSION['user_id'];
        $bookings = $this->bookingModel->getByUser($userId);
        
        foreach ($bookings as $booking) {
            // Skip jika bukan pending
            if ($booking['status'] !== 'Pending') {
                continue;
            }
            
            // Skip jika sudah dibayar
            if ($booking['payment_status'] === 'Success') {
                continue;
            }
            
            // Hitung selisih waktu dari created_at
            $createdTime = strtotime($booking['created_at']);
            $currentTime = time();
            $diffSeconds = $currentTime - $createdTime;
            
            // Jika lebih dari atau sama dengan 23 jam (82800 detik), cancel otomatis
            if ($diffSeconds >= 82800) {
                $this->bookingModel->updateStatus($booking['id'], 'Cancelled');
            }
        }
    }
}
?>
