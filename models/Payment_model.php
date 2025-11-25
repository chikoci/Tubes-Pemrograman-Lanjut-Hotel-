<?php

class Payment_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // Create payment
    public function create($data) {
        return $this->qb->table('payments')->insertGetId($data);
    }

    // Get payment by booking ID
    public function getByBooking($bookingId) {
        return $this->qb->table('payments')
            ->where('booking_id', '=', $bookingId)
            ->first();
    }

    // Get all pending payments (admin)
    public function getPending() {
        return $this->qb->table('payments')
            ->select([
                'payments.*',
                'bookings.id as booking_id',
                'bookings.check_in_date', 'bookings.check_out_date',
                'users.name as user_name', 'users.email as user_email',
                'rooms.room_number',
                'room_types.name as room_type_name'
            ])
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('payments.status', '=', 'Pending')
            ->orderBy('payments.created_at', 'ASC')
            ->get();
    }

    // Approve payment (with transaction)
    public function approvePayment($paymentId) {
        try {
            $this->db->beginTransaction();
            
            // Get payment info
            $payment = $this->qb->table('payments')
                ->where('id', '=', $paymentId)
                ->first();
            
            if (!$payment) {
                throw new Exception("Payment tidak ditemukan");
            }
            
            // Update payment status
            $this->qb->table('payments')
                ->where('id', '=', $paymentId)
                ->update(['status' => 'Success']);
            
            // Update booking status
            $this->qb->table('bookings')
                ->where('id', '=', $payment['booking_id'])
                ->update(['status' => 'Confirmed']);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Get pending payments count
    public function getPendingCount() {
        return $this->qb->table('payments')
            ->where('status', '=', 'Pending')
            ->count();
    }

    // Get all payments with details (for admin)
    public function getAllWithDetails() {
        return $this->qb->table('payments')
            ->select([
                'payments.*',
                'bookings.id as booking_id',
                'bookings.check_in_date', 'bookings.check_out_date',
                'users.name as user_name', 'users.email as user_email',
                'rooms.room_number',
                'room_types.name as room_type_name'
            ])
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->orderBy('payments.created_at', 'DESC')
            ->get();
    }
}
?>
