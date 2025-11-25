<?php

class Booking_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // buat booking baru
    public function create($data) {
        return $this->qb->table('bookings')->insertGetId($data);
    }

    // ambil booking by ID
    public function find($id) {
        return $this->qb->table('bookings')
            ->select([
                'bookings.*',
                'users.name as user_name', 'users.email as user_email',
                'rooms.room_number',
                'room_types.name as room_type_name'
            ])
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('bookings.id', '=', $id)
            ->first();
    }

    // ambil booking by user
    public function getByUser($userId) {
        return $this->qb->table('bookings')
            ->select([
                'bookings.*',
                'rooms.room_number',
                'room_types.name as room_type_name', 'room_types.price'
            ])
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('bookings.user_id', '=', $userId)
            ->orderBy('bookings.created_at', 'DESC')
            ->get();
    }

    // ambil semua bookings (admin)
    public function getAll() {
        return $this->qb->table('bookings')
            ->select([
                'bookings.*',
                'users.name as user_name', 'users.email as user_email',
                'rooms.room_number',
                'room_types.name as room_type_name'
            ])
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->orderBy('bookings.created_at', 'DESC')
            ->get();
    }

    // update status booking
    public function updateStatus($id, $status) {
        return $this->qb->table('bookings')
            ->where('id', '=', $id)
            ->update(['status' => $status]);
    }

    // hitung jumlah malam
    public function calculateNights($checkIn, $checkOut) {
        $start = new DateTime($checkIn);
        $end = new DateTime($checkOut);
        $diff = $start->diff($end);
        return $diff->days;
    }

    // statistik booking (admin)
    public function getStatistics() {
        $totalBookings = $this->qb->table('bookings')->count();
        
        $pendingCount = $this->qb->table('bookings')
            ->where('status', '=', 'Pending')
            ->count();
        
        $confirmedCount = $this->qb->table('bookings')
            ->where('status', '=', 'Confirmed')
            ->count();
        
        $totalRevenue = $this->qb->table('bookings')
            ->select(['SUM(total_price) as total'])
            ->where('status', '=', 'Confirmed')
            ->first();
        
        return [
            'total' => $totalBookings,
            'pending' => $pendingCount,
            'confirmed' => $confirmedCount,
            'revenue' => $totalRevenue['total'] ?? 0
        ];
    }
}
?>
