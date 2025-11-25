<?php
// Controller Booking - untuk proses booking kamar
class BookingController {
    private $roomModel;
    private $bookingModel;
    private $paymentModel;

    public function __construct() {
        $db = new Database();
        $this->roomModel = new Room_model($db->getConnection());
        $this->bookingModel = new Booking_model($db->getConnection());
        $this->paymentModel = new Payment_model($db->getConnection());
    }

    // Method untuk search kamar available
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

        // Load view
        include 'views/layouts/header.php';
        include 'views/booking/search.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk create booking
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

        // Load view
        include 'views/layouts/header.php';
        include 'views/booking/create.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk store booking
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

            // Create booking
            $bookingData = [
                'user_id' => $_SESSION['user_id'],
                'room_id' => $roomId,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'total_price' => $totalPrice,
                'status' => 'Pending'
            ];

            $bookingId = $this->bookingModel->create($bookingData);

            if ($bookingId) {
                setFlash('success', 'Booking berhasil dibuat! Silakan lakukan pembayaran.');
                // Gunakan format route + &param agar URL valid tanpa .htaccess
                redirect('booking/payment&booking_id=' . $bookingId);
                return;
            } else {
                setFlash('error', 'Terjadi kesalahan saat membuat booking');
                redirect('booking/search');
            }
        }

        redirect('booking/search');
    }

    // Method untuk halaman pembayaran
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

        // Cek apakah sudah ada payment
        $existingPayment = $this->paymentModel->getByBooking($bookingId);

        // Load view
        include 'views/layouts/header.php';
        include 'views/booking/payment.php';
        include 'views/layouts/footer.php';
    }

    // Method untuk confirm payment
    public function confirmPayment() {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'];
            $amount = $_POST['amount'];

            // Create payment
            $paymentData = [
                'booking_id' => $bookingId,
                'amount' => $amount,
                'payment_date' => date('Y-m-d H:i:s'),
                'status' => 'Pending'
            ];

            $paymentId = $this->paymentModel->create($paymentData);

            if ($paymentId) {
                setFlash('success', 'Konfirmasi pembayaran berhasil! Menunggu verifikasi admin.');
                redirect('booking/myBookings');
                return;
            } else {
                setFlash('error', 'Terjadi kesalahan saat konfirmasi pembayaran');
            }
        }

        redirect('booking/myBookings');
    }

    // Method untuk melihat booking user
    public function myBookings() {
        requireLogin();

        $bookings = $this->bookingModel->getByUser($_SESSION['user_id']);

        // Get payment info untuk setiap booking
        foreach ($bookings as &$booking) {
            $payment = $this->paymentModel->getByBooking($booking['id']);
            $booking['payment'] = $payment;
        }

        // Load view
        include 'views/layouts/header.php';
        include 'views/booking/my_bookings.php';
        include 'views/layouts/footer.php';
    }
}
?>
