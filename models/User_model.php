<?php
class User_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    public function register($data) {
        // hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // ambil role_id untuk Tamu
        $this->qb->table('roles');
        $this->qb->where('role_name', '=', 'Tamu');
        $role = $this->qb->first();
        
        $data['role_id'] = $role['id'];
        
        // insert user baru
        $this->qb->table('users');
        return $this->qb->insertGetId($data);
    }

    public function login($email, $password) {
        $this->qb->table('users');
        $this->qb->select(['users.*', 'roles.role_name']);
        $this->qb->join('roles', 'users.role_id', '=', 'roles.id');
        $this->qb->where('users.email', '=', $email);
        $user = $this->qb->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function emailExists($email, $excludeUserId = null) {
        $this->qb->table('users');
        $this->qb->where('email', '=', $email);
        
        if ($excludeUserId) {
            $this->qb->where('id', '!=', $excludeUserId);
        }
        
        $result = $this->qb->first();
        return $result !== null;
    }

    public function find($id) {
        $this->qb->table('users');
        $this->qb->where('id', '=', $id);
        return $this->qb->first();
    }

    public function getByEmail($email) {
        $this->qb->table('users');
        $this->qb->where('email', '=', $email);
        return $this->qb->first();
    }

    public function updateProfile($id, $data) {
        $updateData = [
            'name'  => $data['name'],
            'age'   => $data['age'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $this->qb->table('users');
        $this->qb->where('id', '=', $id);
        return $this->qb->update($updateData);
    }
}
?>
