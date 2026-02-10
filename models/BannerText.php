<?php
class BannerText {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM banner_texts WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getAllAdmin() {
        return $this->db->fetchAll(
            "SELECT * FROM banner_texts ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM banner_texts WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO banner_texts (text, sort_order, status) 
                VALUES (?, ?, ?)";
        
        $this->db->query($sql, [
            $data['text'],
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE banner_texts SET 
                text = ?, sort_order = ?, status = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['text'],
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM banner_texts WHERE id = ?", [$id]);
    }
}

