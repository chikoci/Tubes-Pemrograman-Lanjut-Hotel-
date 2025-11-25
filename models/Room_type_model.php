<?php

class Room_type_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // get semua tipe kamar
    public function getAll() {
        return $this->qb->table('room_types')->get();
    }

    // ambil tipe kamar by ID
    public function find($id) {
        return $this->qb->table('room_types')
            ->where('id', '=', $id)
            ->first();
    }

    // buat tipe kamar baru
    public function create($data) {
        return $this->qb->table('room_types')->insertGetId($data);
    }

    // update tipe kamar
    public function update($id, $data) {
        return $this->qb->table('room_types')
            ->where('id', '=', $id)
            ->update($data);
    }

    // hapus tipe kamar
    public function delete($id) {
        // Cek apakah masih ada room yang pakai tipe ini
        $roomCount = $this->qb->table('rooms')
            ->where('room_type_id', '=', $id)
            ->count();

        if ($roomCount > 0) {
            // Tidak boleh hapus, masih dipakai
            return false;
        }

        // Aman untuk dihapus
        return $this->qb->table('room_types')
            ->where('id', '=', $id)
            ->delete();
    }

    // ambil semua dengan jumlah kamar tersedia
    public function getAllWithAvailableRooms() {
        $roomTypes = $this->getAll();
        
        foreach ($roomTypes as &$type) {
            $availableCount = $this->qb->table('rooms')
                ->where('room_type_id', '=', $type['id'])
                ->where('status', '=', 'Available')
                ->count();
            
            $type['available_rooms'] = $availableCount;
        }
        
        return $roomTypes;
    }
}
?>
