<?php
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
    }
    
    public function getAllAdmin() {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }
    
    public function getBySlug($slug) {
        return $this->db->fetch("SELECT * FROM categories WHERE slug = ? AND status = 'active'", [$slug]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO categories (name, slug, description, image, status, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['image'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE categories SET 
                name = ?, slug = ?, description = ?, image = ?, status = ?, sort_order = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['image'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id
        ]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM categories WHERE id = ?", [$id]);
    }
    
    public function getSubCategories($categoryId) {
        return $this->db->fetchAll(
            "SELECT * FROM sub_categories WHERE category_id = ? AND status = 'active' ORDER BY sort_order ASC, name ASC",
            [$categoryId]
        );
    }
    
    public function getSubCategoriesAdmin($categoryId) {
        return $this->db->fetchAll(
            "SELECT * FROM sub_categories WHERE category_id = ? ORDER BY sort_order ASC, name ASC",
            [$categoryId]
        );
    }
}

