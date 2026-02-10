<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($limit = null, $offset = 0)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->fetchAll($sql);
    }

    public function getFeatured($limit = 6)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active' AND p.featured = 1 
                ORDER BY p.created_at DESC 
                LIMIT $limit";

        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    public function getBySlug($slug)
    {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ?",
            [$slug]
        );
    }

    public function getBySku($sku)
    {
        return $this->db->fetch(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.sku = ?",
            [$sku]
        );
    }

    public function getByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND (p.sub_category_id IS NULL OR p.sub_category_id = 0) AND p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql, [$categoryId]);
    }

    public function getByCategoryWithoutSubCategory($categoryId, $limit = null)
    {
        // Get products that don't have sub-category OR products that should show on main category
        // (products with sub_category_id but show_in_main_category flag)
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? 
                AND (p.sub_category_id IS NULL OR p.sub_category_id = 0 OR p.show_in_main_category = 1)
                AND p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql, [$categoryId]);
    }

    public function getBySubCategory($subCategoryId, $limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.sub_category_id = ? AND p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql, [$subCategoryId]);
    }

    public function searchAdmin($query)
    {
        $searchTerm = "%{$query}%";
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.name LIKE ? OR p.sku LIKE ?
             ORDER BY p.created_at DESC",
            [$searchTerm, $searchTerm]
        );
    }

    public function search($query)
    {
        $searchTerm = "%{$query}%";
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'active' 
             AND (p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)
             ORDER BY p.created_at DESC",
            [$searchTerm, $searchTerm, $searchTerm]
        );
    }

    public function create($data)
    {
        $sql = "INSERT INTO products (name, slug, description, short_description, price, sale_price, stock, sku, category_id, sub_category_id, image, gallery, colors, status, featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $colorsJson = null;
        if (!empty($data['colors']) && is_array($data['colors'])) {
            $colorsJson = json_encode($data['colors']);
        }

        $this->db->query($sql, [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['short_description'] ?? '',
            $data['price'],
            $data['sale_price'] ?? null,
            $data['stock'] ?? 0,
            !empty($data['sku']) ? $data['sku'] : null,
            $data['category_id'] ?? null,
            $data['sub_category_id'] ?? null,
            $data['image'] ?? '',
            $data['gallery'] ?? null,
            $colorsJson,
            $data['status'] ?? 'active',
            $data['featured'] ?? 0
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET 
                name = ?, slug = ?, description = ?, short_description = ?, 
                price = ?, sale_price = ?, stock = ?, sku = ?, 
                category_id = ?, sub_category_id = ?, show_in_main_category = ?, image = ?, gallery = ?, colors = ?, status = ?, featured = ? 
                WHERE id = ?";

        $colorsJson = null;
        if (!empty($data['colors']) && is_array($data['colors'])) {
            $colorsJson = json_encode($data['colors']);
        }

        return $this->db->query($sql, [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['short_description'] ?? '',
            $data['price'],
            $data['sale_price'] ?? null,
            $data['stock'] ?? 0,
            !empty($data['sku']) ? $data['sku'] : null,
            $data['category_id'] ?? null,
            $data['sub_category_id'] ?? null,
            $data['show_in_main_category'] ?? 0,
            $data['image'] ?? '',
            $data['gallery'] ?? null,
            $colorsJson,
            $data['status'] ?? 'active',
            $data['featured'] ?? 0,
            $id
        ]);
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM products WHERE id = ?", [$id]);
    }
}

