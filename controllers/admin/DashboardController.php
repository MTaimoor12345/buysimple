<?php
class DashboardController {
    public function index() {
        $this->checkAdmin();
        
        $db = Database::getInstance();
        
        $stats = [
            'total_orders' => $db->fetch("SELECT COUNT(*) as count FROM orders")['count'],
            'total_products' => $db->fetch("SELECT COUNT(*) as count FROM products")['count'],
            'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")['count'],
            'total_revenue' => $db->fetch("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'")['total'] ?? 0
        ];
        
        $recentOrders = $db->fetchAll(
            "SELECT o.*, u.name as user_name 
             FROM orders o 
             LEFT JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC 
             LIMIT 10"
        );
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }
    
    private function checkAdmin() {
        if (!Auth::check() || !Auth::isAdmin()) {
            Helper::redirect('login');
            exit;
        }
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../../views/layouts/admin_header.php';
        require __DIR__ . "/../../views/{$view}.php";
        require __DIR__ . '/../../views/layouts/admin_footer.php';
    }
}

