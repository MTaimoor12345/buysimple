<?php
class HeroSlide {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM hero_slides WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getAllAdmin() {
        return $this->db->fetchAll(
            "SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC"
        );
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM hero_slides WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO hero_slides (title, image, button_text, button_link, image_link, sort_order, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['title'] ?? '',
            $data['image'],
            $data['button_text'] ?? 'Buy Now',
            $data['button_link'] ?? '/products',
            $data['image_link'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE hero_slides SET 
                title = ?, image = ?, button_text = ?, button_link = ?, image_link = ?, sort_order = ?, status = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['title'] ?? '',
            $data['image'],
            $data['button_text'] ?? 'Buy Now',
            $data['button_link'] ?? '/products',
            $data['image_link'] ?? null,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM hero_slides WHERE id = ?", [$id]);
    }
}

