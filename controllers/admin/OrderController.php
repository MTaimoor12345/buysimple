<?php
class AdminOrderController {
    public function index() {
        $this->checkAdmin();
        
        $orderModel = new Order();
        $orders = $orderModel->getAll();
        
        $this->view('admin/orders/index', [
            'orders' => $orders
        ]);
    }
    
    public function show($id) {
        $this->checkAdmin();
        
        $orderModel = new Order();
        $order = $orderModel->getById($id);
        
        if (!$order) {
            http_response_code(404);
            echo "Order not found";
            return;
        }
        
        $items = $orderModel->getItems($id);
        
        $this->view('admin/orders/show', [
            'order' => $order,
            'items' => $items
        ]);
    }
    
    public function updateStatus() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
            
            if ($orderId && $status) {
                $orderModel = new Order();
                $orderModel->updateStatus($orderId, $status);
                Session::flash('success', 'Order status updated!');
            }
        }
        
        Helper::redirect('admin/orders');
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

