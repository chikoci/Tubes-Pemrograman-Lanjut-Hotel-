<?php
class Payment_type_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    public function getAll() {
        $this->qb->table('payment_types');
        $this->qb->orderBy('id', 'ASC');
        return $this->qb->get();
    }

    public function find($id) {
        $this->qb->table('payment_types');
        $this->qb->where('id', '=', $id);
        return $this->qb->first();
    }

    public function findByName($name) {
        $this->qb->table('payment_types');
        $this->qb->where('name', '=', $name);
        return $this->qb->first();
    }
}
?>
