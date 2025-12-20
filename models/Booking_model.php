<?php
class Booking_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    public function create($data) {
        $this->qb->table('bookings');
        return $this->qb->insertGetId($data);
    }

    public function find($id) {
        $this->qb->table('bookings');
        $this->qb->select([
            'bookings.*',
            'users.name as user_name', 'users.email as user_email',
            'rooms.room_number',
            'room_types.name as room_type_name',
            'payment_types.name as payment_type_name'
        ]);
        $this->qb->join('users', 'bookings.user_id', '=', 'users.id');
        $this->qb->join('rooms', 'bookings.room_id', '=', 'rooms.id');
        $this->qb->join('room_types', 'rooms.room_type_id', '=', 'room_types.id');
        $this->qb->leftJoin('payment_types', 'bookings.payment_type_id', '=', 'payment_types.id');
        $this->qb->where('bookings.id', '=', $id);
        return $this->qb->first();
    }

    public function findByCode($bookingCode) {
        $this->qb->table('bookings');
        $this->qb->where('booking_code', '=', $bookingCode);
        return $this->qb->first();
    }

    public function getByUser($userId) {
        $this->qb->table('bookings');
        $this->qb->select([
            'bookings.*',
            'rooms.room_number',
            'room_types.name as room_type_name', 'room_types.price',
            'payment_types.name as payment_type_name'
        ]);
        $this->qb->join('rooms', 'bookings.room_id', '=', 'rooms.id');
        $this->qb->join('room_types', 'rooms.room_type_id', '=', 'room_types.id');
        $this->qb->leftJoin('payment_types', 'bookings.payment_type_id', '=', 'payment_types.id');
        $this->qb->where('bookings.user_id', '=', $userId);
        $this->qb->orderBy('bookings.created_at', 'DESC');
        return $this->qb->get();
    }

    public function getAll() {
        $this->qb->table('bookings');
        $this->qb->select([
            'bookings.*',
            'users.name as user_name', 'users.email as user_email',
            'rooms.room_number',
            'room_types.name as room_type_name',
            'payment_types.name as payment_type_name'
        ]);
        $this->qb->join('users', 'bookings.user_id', '=', 'users.id');
        $this->qb->join('rooms', 'bookings.room_id', '=', 'rooms.id');
        $this->qb->join('room_types', 'rooms.room_type_id', '=', 'room_types.id');
        $this->qb->leftJoin('payment_types', 'bookings.payment_type_id', '=', 'payment_types.id');
        $this->qb->orderBy('bookings.created_at', 'DESC');
        return $this->qb->get();
    }

    public function updateStatus($id, $status) {
        $this->qb->table('bookings');
        $this->qb->where('id', '=', $id);
        return $this->qb->update(['status' => $status]);
    }

    public function calculateNights($checkIn, $checkOut) {
        $start = new DateTime($checkIn);
        $end = new DateTime($checkOut);
        $diff = $start->diff($end);
        return $diff->days;
    }

    public function getStatistics() {
        // Total bookings
        $this->qb->table('bookings');
        $totalBookings = $this->qb->count();
        
        // Pending count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Pending');
        $pendingCount = $this->qb->count();
        
        // Confirmed count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Confirmed');
        $confirmedCount = $this->qb->count();
        
        // Cancelled count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Cancelled');
        $cancelledCount = $this->qb->count();
        
        // Total revenue
        $this->qb->table('bookings');
        $this->qb->select(['SUM(total_price) as total']);
        $this->qb->where('status', '=', 'Confirmed');
        $revenue = $this->qb->first();
        
        return [
            'total' => $totalBookings,
            'pending' => $pendingCount,
            'confirmed' => $confirmedCount,
            'cancelled' => $cancelledCount,
            'revenue' => $revenue['total'] ?? 0
        ];
    }

    public function updatePayment($id, $data) {
        $this->qb->table('bookings');
        $this->qb->where('id', '=', $id);
        return $this->qb->update($data);
    }

    public function approvePayment($id) {
        try {
            $this->db->beginTransaction();
            
            $this->qb->table('bookings');
            $this->qb->where('id', '=', $id);
            $this->qb->update(['payment_status' => 'Success', 'status' => 'Confirmed']);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get statistics with date range filter
     */
    public function getStatisticsFiltered($startDate = null, $endDate = null) {
        $hasFilter = $startDate && $endDate;
        
        // Total bookings
        $this->qb->table('bookings');
        if ($hasFilter) {
            $this->qb->where('DATE(created_at)', '>=', $startDate);
            $this->qb->where('DATE(created_at)', '<=', $endDate);
        }
        $totalBookings = $this->qb->count();
        
        // Pending count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Pending');
        if ($hasFilter) {
            $this->qb->where('DATE(created_at)', '>=', $startDate);
            $this->qb->where('DATE(created_at)', '<=', $endDate);
        }
        $pendingCount = $this->qb->count();
        
        // Confirmed count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Confirmed');
        if ($hasFilter) {
            $this->qb->where('DATE(created_at)', '>=', $startDate);
            $this->qb->where('DATE(created_at)', '<=', $endDate);
        }
        $confirmedCount = $this->qb->count();
        
        // Cancelled count
        $this->qb->table('bookings');
        $this->qb->where('status', '=', 'Cancelled');
        if ($hasFilter) {
            $this->qb->where('DATE(created_at)', '>=', $startDate);
            $this->qb->where('DATE(created_at)', '<=', $endDate);
        }
        $cancelledCount = $this->qb->count();
        
        // Total revenue (from confirmed bookings)
        $sql = "SELECT SUM(total_price) as total FROM bookings WHERE status = 'Confirmed'";
        $params = [];
        if ($hasFilter) {
            $sql .= " AND DATE(created_at) >= ? AND DATE(created_at) <= ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total' => $totalBookings,
            'pending' => $pendingCount,
            'confirmed' => $confirmedCount,
            'cancelled' => $cancelledCount,
            'revenue' => $revenue['total'] ?? 0
        ];
    }
}
?>
