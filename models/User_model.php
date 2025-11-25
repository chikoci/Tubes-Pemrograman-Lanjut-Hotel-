<?php

class User_model {
    protected $db;
    protected $qb;

    public function __construct($db) {
        $this->db = $db;
        $this->qb = new QueryBuilder($db);
    }

    // Method untuk registrasi user baru
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Get role_id untuk Tamu
        $role = $this->qb->table('roles')
            ->where('role_name', '=', 'Tamu')
            ->first();
        
        $data['role_id'] = $role['id'];
        
        // Insert user baru
        return $this->qb->table('users')->insertGetId($data);
    }

    // Method untuk login
    public function login($email, $password) {
        $user = $this->qb->table('users')
            ->select(['users.*', 'roles.role_name'])
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.email', '=', $email)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    // Method untuk cek email sudah ada
    public function emailExists($email, $excludeUserId = null) {
        $query = $this->qb->table('users')
            ->where('email', '=', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->first() !== null;
    }

    // Method untuk get user by ID
    public function find($id) {
        return $this->qb->table('users')
            ->where('id', '=', $id)
            ->first();
    }

    // Method untuk get user by email (digunakan pada fitur lupa password)
    public function getByEmail($email) {
        return $this->qb->table('users')
            ->where('email', '=', $email)
            ->first();
    }

    // Method untuk update profile
    public function updateProfile($id, $data) {
        // Buat array bersih hanya berisi kolom yang ada di tabel users
        $updateData = [
            'name'  => $data['name'],
            'age'   => $data['age'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ];

        // Jika password diisi, hash dan sertakan dalam update
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->qb->table('users')
            ->where('id', '=', $id)
            ->update($updateData);
    }
}
?>
