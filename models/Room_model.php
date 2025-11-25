<?php

class Room_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // ambil semua rooms
    public function getAll() {
        return $this->qb->table('rooms')
            ->select(['rooms.*', 'room_types.name as room_type_name', 'room_types.price'])
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->get();
    }

    // ambil room by ID
    public function find($id) {
        return $this->qb->table('rooms')
            ->select(['rooms.*', 'room_types.name as room_type_name', 'room_types.price', 'room_types.description'])
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->where('rooms.id', '=', $id)
            ->first();
    }

    // buat room baru
    public function create($data) {
        return $this->qb->table('rooms')->insertGetId($data);
    }

    // update data room
    public function update($id, $data) {
        return $this->qb->table('rooms')
            ->where('id', '=', $id)
            ->update($data);
    }

    // hapus room
    public function delete($id) {
        return $this->qb->table('rooms')
            ->where('id', '=', $id)
            ->delete();
    }

    // cari kamar available
    public function searchAvailable($checkIn, $checkOut) {
        // SQL manual untuk query komplex
        $sql = "
            SELECT 
                r.id, r.room_number, r.status,
                rt.id as room_type_id, rt.name as room_type_name, 
                rt.price, rt.description, rt.image
            FROM rooms r
            INNER JOIN room_types rt ON r.room_type_id = rt.id
            WHERE r.status = 'Available'
            AND r.id NOT IN (
                SELECT b.room_id 
                FROM bookings b
                WHERE b.status != 'Cancelled'
                AND (
                    (b.check_in_date <= ? AND b.check_out_date > ?)
                    OR (b.check_in_date < ? AND b.check_out_date >= ?)
                    OR (b.check_in_date >= ? AND b.check_out_date <= ?)
                )
            )
            ORDER BY rt.price ASC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$checkIn, $checkIn, $checkOut, $checkOut, $checkIn, $checkOut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // cek apakah room tersedia
    public function isAvailable($roomId, $checkIn, $checkOut) {
        $sql = "
            SELECT COUNT(*) as count
            FROM bookings
            WHERE room_id = ?
            AND status != 'Cancelled'
            AND (
                (check_in_date <= ? AND check_out_date > ?)
                OR (check_in_date < ? AND check_out_date >= ?)
                OR (check_in_date >= ? AND check_out_date <= ?)
            )
        ";
        
        $stmt = $this->db->prepare($sql);
        $params = [$roomId, $checkIn, $checkIn, $checkOut, $checkOut, $checkIn, $checkOut];
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
}
?>
