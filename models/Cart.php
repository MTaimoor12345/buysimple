<?php
class Cart {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    private function getSessionId() {
        if (!Session::has('cart_session')) {
            Session::set('cart_session', uniqid('cart_', true));
        }
        return Session::get('cart_session');
    }
    
    public function add($productId, $quantity = 1, $colorName = null) {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();
        
        // Check if item already exists with same color
        $existing = $this->db->fetch(
            "SELECT * FROM cart WHERE product_id = ? AND (user_id = ? OR session_id = ?) AND (color_name = ? OR (color_name IS NULL AND ? IS NULL))",
            [$productId, $userId, $sessionId, $colorName, $colorName]
        );
        
        if ($existing) {
            // Update quantity
            $this->db->query(
                "UPDATE cart SET quantity = quantity + ? WHERE id = ?",
                [$quantity, $existing['id']]
            );
        } else {
            // Add new item
            $this->db->query(
                "INSERT INTO cart (user_id, session_id, product_id, quantity, color_name) VALUES (?, ?, ?, ?, ?)",
                [$userId, $sessionId, $productId, $quantity, $colorName]
            );
        }
    }
    
    public function getItems() {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();
        
        return $this->db->fetchAll(
            "SELECT c.*, p.name, p.price, p.sale_price, p.image, p.stock, c.color_name 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE (c.user_id = ? OR c.session_id = ?) AND p.status = 'active'
             ORDER BY c.created_at DESC",
            [$userId, $sessionId]
        );
    }
    
    public function updateQuantity($cartId, $quantity) {
        $this->db->query("UPDATE cart SET quantity = ? WHERE id = ?", [$quantity, $cartId]);
    }
    
    public function remove($cartId) {
        $this->db->query("DELETE FROM cart WHERE id = ?", [$cartId]);
    }
    
    public function clear() {
        $userId = Auth::id();
        $sessionId = $this->getSessionId();
        
        $this->db->query(
            "DELETE FROM cart WHERE user_id = ? OR session_id = ?",
            [$userId, $sessionId]
        );
    }
    
    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        
        foreach ($items as $item) {
            $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
            $total += $price * $item['quantity'];
        }
        
        return $total;
    }
    
    public function getCount() {
        $items = $this->getItems();
        return count($items);
    }

    public function getShippingCost() {
        $total = $this->getTotal();
        return ($total >= 5000) ? 0 : 200;
    }

    public function getGrandTotal() {
        return $this->getTotal() + $this->getShippingCost();
    }
}

