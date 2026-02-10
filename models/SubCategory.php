<?php
class SubCategory {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT sc.*, c.name as category_name 
             FROM sub_categories sc 
             JOIN categories c ON sc.category_id = c.id 
             WHERE sc.status = 'active' 
             ORDER BY c.sort_order ASC, sc.sort_order ASC, sc.name ASC"
        );
    }
    
    public function getAllAdmin() {
        return $this->db->fetchAll(
            "SELECT sc.*, c.name as category_name 
             FROM sub_categories sc 
             JOIN categories c ON sc.category_id = c.id 
             ORDER BY c.sort_order ASC, sc.sort_order ASC, sc.name ASC"
        );
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM sub_categories WHERE id = ?", [$id]);
    }
    
    public function getBySlug($slug, $categoryId) {
        return $this->db->fetch(
            "SELECT * FROM sub_categories WHERE slug = ? AND category_id = ? AND status = 'active'",
            [$slug, $categoryId]
        );
    }
    
    public function getByCategoryId($categoryId) {
        return $this->db->fetchAll(
            "SELECT * FROM sub_categories WHERE category_id = ? AND status = 'active' ORDER BY sort_order ASC, name ASC",
            [$categoryId]
        );
    }
    
    public function create($data) {
        $sql = "INSERT INTO sub_categories (category_id, name, slug, description, status, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE sub_categories SET 
                category_id = ?, name = ?, slug = ?, description = ?, status = ?, sort_order = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM sub_categories WHERE id = ?", [$id]);
    }
}

