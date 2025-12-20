<?php
class Room_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    public function getAll() {
        // Query dengan status dinamis berdasarkan booking aktif hari ini
        $today = date('Y-m-d');
        return $this->getAllWithDateRange($today, $today);
    }

    /**
     * Get all rooms with status based on date range
     * Status will be 'Occupied' if there's any confirmed booking overlapping with the date range
     */
    public function getAllWithDateRange($startDate, $endDate) {
        $sql = "
            SELECT r.*, 
                   rt.name as room_type_name, 
                   rt.price,
                   CASE 
                       WHEN r.status = 'Maintenance' THEN 'Maintenance'
                       WHEN EXISTS (
                           SELECT 1 FROM bookings b 
                           WHERE b.room_id = r.id 
                           AND b.status = 'Confirmed'
                           AND (
                               (b.check_in_date <= ? AND b.check_out_date > ?)
                               OR (b.check_in_date < ? AND b.check_out_date >= ?)
                               OR (b.check_in_date >= ? AND b.check_out_date <= ?)
                           )
                       ) THEN 'Occupied'
                       ELSE 'Available'
                   END as display_status
            FROM rooms r
            INNER JOIN room_types rt ON r.room_type_id = rt.id
            ORDER BY r.room_number ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $startDate, $endDate, $endDate, $startDate, $endDate]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Gunakan display_status sebagai status untuk tampilan
        foreach ($rooms as &$room) {
            $room['status'] = $room['display_status'];
        }
        
        return $rooms;
    }

    public function find($id) {
        $this->qb->table('rooms');
        $this->qb->select(['rooms.*', 'room_types.name as room_type_name', 'room_types.price', 'room_types.description']);
        $this->qb->join('room_types', 'rooms.room_type_id', '=', 'room_types.id');
        $this->qb->where('rooms.id', '=', $id);
        return $this->qb->first();
    }

    public function findByRoomNumber($roomNumber) {
        $this->qb->table('rooms');
        $this->qb->where('room_number', '=', $roomNumber);
        return $this->qb->first();
    }

    public function create($data) {
        $this->qb->table('rooms');
        return $this->qb->insertGetId($data);
    }

    public function update($id, $data) {
        $this->qb->table('rooms');
        $this->qb->where('id', '=', $id);
        return $this->qb->update($data);
    }

    public function delete($id) {
        $this->qb->table('rooms');
        $this->qb->where('id', '=', $id);
        return $this->qb->delete();
    }

    public function searchAvailable($checkIn, $checkOut) {
        $sql = "
            SELECT r.id, r.room_number, r.status,
                   rt.id as room_type_id, rt.name as room_type_name, 
                   rt.price, rt.description, rt.image
            FROM rooms r
            INNER JOIN room_types rt ON r.room_type_id = rt.id
            WHERE r.status = 'Available'
            AND r.id NOT IN (
                SELECT b.room_id FROM bookings b
                WHERE b.status != 'Cancelled'
                AND ((b.check_in_date <= ? AND b.check_out_date > ?)
                    OR (b.check_in_date < ? AND b.check_out_date >= ?)
                    OR (b.check_in_date >= ? AND b.check_out_date <= ?))
            )
            ORDER BY rt.price ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$checkIn, $checkIn, $checkOut, $checkOut, $checkIn, $checkOut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isAvailable($roomId, $checkIn, $checkOut) {
        $sql = "
            SELECT COUNT(*) as count FROM bookings
            WHERE room_id = ? AND status != 'Cancelled'
            AND ((check_in_date <= ? AND check_out_date > ?)
                OR (check_in_date < ? AND check_out_date >= ?)
                OR (check_in_date >= ? AND check_out_date <= ?))
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$roomId, $checkIn, $checkIn, $checkOut, $checkOut, $checkIn, $checkOut]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }
}
?>
