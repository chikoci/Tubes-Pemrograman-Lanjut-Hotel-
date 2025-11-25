<?php

class Payment_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // insert payment
    public function create($data) {
        return $this->qb->table('payments')->insertGetId($data);
    }

    // ambil payment berdasarkan booking ID
    public function getByBooking($bookingId) {
        return $this->qb->table('payments')
            ->where('booking_id', '=', $bookingId)
            ->first();
    }

    // ambil semua pending payments (admin)
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

    // approve payment (pake transaction)
    public function approvePayment($paymentId) {
        try {
            $this->db->beginTransaction();
            
            // ambil info payment
            $payment = $this->qb->table('payments')
                ->where('id', '=', $paymentId)
                ->first();
            
            if (!$payment) {
                throw new Exception("Payment tidak ditemukan");
            }
            
            // update status payment
            $this->qb->table('payments')
                ->where('id', '=', $paymentId)
                ->update(['status' => 'Success']);
            
            // update status booking
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

    // hitung pending payments
    public function getPendingCount() {
        return $this->qb->table('payments')
            ->where('status', '=', 'Pending')
            ->count();
    }

    // ambil semua payments dengan detail (untuk admin)
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
