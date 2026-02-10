<?php
class Collection {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM collections WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getAllAdmin() {
        return $this->db->fetchAll(
            "SELECT * FROM collections ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM collections WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO collections (title, image, link, sort_order, status) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['title'],
            $data['image'],
            $data['link'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE collections SET 
                title = ?, image = ?, link = ?, sort_order = ?, status = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['title'],
            $data['image'],
            $data['link'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM collections WHERE id = ?", [$id]);
    }
}

