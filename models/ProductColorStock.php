<?php
class ProductColorStock {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getByProductId($productId) {
        return $this->db->fetchAll(
            "SELECT * FROM product_color_stock WHERE product_id = ? ORDER BY color_name",
            [$productId]
        );
    }
    
    public function getByProductAndColor($productId, $colorName) {
        return $this->db->fetch(
            "SELECT * FROM product_color_stock WHERE product_id = ? AND color_name = ?",
            [$productId, $colorName]
        );
    }
    
    public function create($data) {
        $sql = "INSERT INTO product_color_stock (product_id, color_name, color_code, stock) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE stock = stock + VALUES(stock)";
        
        return $this->db->query($sql, [
            $data['product_id'],
            $data['color_name'],
            $data['color_code'],
            $data['stock'] ?? 0
        ]);
    }
    
    public function updateStock($productId, $colorName, $stock) {
        return $this->db->query(
            "UPDATE product_color_stock SET stock = ? WHERE product_id = ? AND color_name = ?",
            [$stock, $productId, $colorName]
        );
    }
    
    public function addStock($productId, $colorName, $stockToAdd) {
        $existing = $this->getByProductAndColor($productId, $colorName);
        if ($existing) {
            $newStock = $existing['stock'] + $stockToAdd;
            return $this->updateStock($productId, $colorName, $newStock);
        } else {
            // Get color code from product colors
            $productModel = new Product();
            $product = $productModel->getById($productId);
            $colors = [];
            if (!empty($product['colors'])) {
                $colors = json_decode($product['colors'], true) ?: [];
            }
            $colorCode = '#000000';
            foreach ($colors as $color) {
                if ($color['name'] === $colorName) {
                    $colorCode = $color['code'];
                    break;
                }
            }
            return $this->create([
                'product_id' => $productId,
                'color_name' => $colorName,
                'color_code' => $colorCode,
                'stock' => $stockToAdd
            ]);
        }
    }
    
    public function reduceStock($productId, $colorName, $quantity) {
        $existing = $this->getByProductAndColor($productId, $colorName);
        if ($existing) {
            $currentStock = (int)$existing['stock'];
            $newStock = max(0, $currentStock - $quantity); // Don't go below 0
            return $this->updateStock($productId, $colorName, $newStock);
        }
        return false;
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM product_color_stock WHERE id = ?", [$id]);
    }
    
    public function deleteByProduct($productId) {
        return $this->db->query("DELETE FROM product_color_stock WHERE product_id = ?", [$productId]);
    }
}

