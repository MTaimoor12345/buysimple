<?php
class OrderController {
    public function show($id) {
        if (!Auth::check()) {
            Helper::redirect('login');
            return;
        }
        
        $orderModel = new Order();
        $order = $orderModel->getById($id);
        
        if (!$order) {
            http_response_code(404);
            echo "Order not found";
            return;
        }
        
        // Check if user owns this order or is admin
        $user = Auth::user();
        if ($order['user_id'] != $user['id'] && !Auth::isAdmin()) {
            http_response_code(403);
            echo "Access denied";
            return;
        }
        
        $items = $orderModel->getItems($id);
        
        $this->view('orders/show', [
            'order' => $order,
            'items' => $items
        ]);
    }
    
    public function myOrders() {
        if (!Auth::check()) {
            Helper::redirect('login');
            return;
        }
        
        $orderModel = new Order();
        $orders = $orderModel->getByUserId(Auth::id());
        
        $this->view('orders/index', [
            'orders' => $orders
        ]);
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

