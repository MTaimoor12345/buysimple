<?php
class Order
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $orderNumber = Helper::generateOrderNumber();

        $this->db->query(
            "INSERT INTO orders (order_number, user_id, total_amount, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, notes) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $orderNumber,
                $data['user_id'],
                $data['total_amount'],
                $data['shipping_name'],
                $data['shipping_email'],
                $data['shipping_phone'],
                $data['shipping_address'],
                $data['shipping_city'],
                $data['notes'] ?? ''
            ]
        );

        $orderId = $this->db->lastInsertId();

        // Add order items and reduce stock
        $productModel = new Product();
        $colorStockModel = new ProductColorStock();

        foreach ($data['items'] as $item) {
            $this->db->query(
                "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal, color_name) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['product_price'],
                    $item['quantity'],
                    $item['subtotal'],
                    $item['color_name'] ?? null
                ]
            );

            // Reduce stock
            $productId = $item['product_id'];
            $quantity = (int) $item['quantity'];
            $colorName = $item['color_name'] ?? null;

            // Get product to check if it has colors
            $product = $productModel->getById($productId);

            if ($product) {
                $hasColors = !empty($product['colors']);

                if ($hasColors && !empty($colorName)) {
                    // Reduce color-wise stock
                    $colorStockModel->reduceStock($productId, $colorName, $quantity);
                } else {
                    // Reduce general stock
                    $currentStock = (int) $product['stock'];
                    $newStock = max(0, $currentStock - $quantity); // Don't go below 0
                    $this->db->query("UPDATE products SET stock = ? WHERE id = ?", [$newStock, $productId]);
                }
            }
        }

        return $orderId;
    }

    public function getAll($limit = null)
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql);
    }

    public function getById($id)
    {
        return $this->db->fetch("SELECT * FROM orders WHERE id = ?", [$id]);
    }

    public function getByUserId($userId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function getItems($orderId)
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.image 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public function updateStatus($id, $status)
    {
        return $this->db->query("UPDATE orders SET status = ? WHERE id = ?", [$status, $id]);
    }

    public function getOrdersByDateRange($startDate, $endDate)
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE DATE(o.created_at) BETWEEN ? AND ? 
                ORDER BY o.created_at DESC";

        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    public function delete($id)
    {
        $this->db->query("DELETE FROM order_items WHERE order_id = ?", [$id]);
        return $this->db->query("DELETE FROM orders WHERE id = ?", [$id]);
    }

    public function updatePaymentStatus($id, $paymentStatus)
    {
        return $this->db->query("UPDATE orders SET payment_status = ? WHERE id = ?", [$paymentStatus, $id]);
    }
}

