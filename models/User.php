<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $this->db->query(
            "INSERT INTO users (name, email, password, phone, address, city) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['email'],
                $hashedPassword,
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['city'] ?? ''
            ]
        );
        
        return $this->db->lastInsertId();
    }
    
    public function getByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function getAll($limit = null) {
        $sql = "SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        return $this->db->fetchAll($sql);
    }
}

