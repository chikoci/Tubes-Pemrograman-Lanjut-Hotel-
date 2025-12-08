<?php
class Room_type_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    public function getAll() {
        $this->qb->table('room_types');
        return $this->qb->get();
    }

    public function find($id) {
        $this->qb->table('room_types');
        $this->qb->where('id', '=', $id);
        return $this->qb->first();
    }

    public function findByName($name) {
        $this->qb->table('room_types');
        $this->qb->where('name', '=', $name);
        return $this->qb->first();
    }

    public function create($data) {
        $this->qb->table('room_types');
        return $this->qb->insertGetId($data);
    }

    public function update($id, $data) {
        $this->qb->table('room_types');
        $this->qb->where('id', '=', $id);
        return $this->qb->update($data);
    }

    public function delete($id) {
        // Cek apakah masih ada room yang pakai tipe ini
        $this->qb->table('rooms');
        $this->qb->where('room_type_id', '=', $id);
        $roomCount = $this->qb->count();

        if ($roomCount > 0) {
            return false;
        }

        $this->qb->table('room_types');
        $this->qb->where('id', '=', $id);
        return $this->qb->delete();
    }

    public function getAllWithAvailableRooms() {
        $roomTypes = $this->getAll();
        
        foreach ($roomTypes as $index => $type) {
            $this->qb->table('rooms');
            $this->qb->where('room_type_id', '=', $type['id']);
            $this->qb->where('status', '=', 'Available');
            $roomTypes[$index]['available_rooms'] = $this->qb->count();
        }
        
        return $roomTypes;
    }
}
?>
