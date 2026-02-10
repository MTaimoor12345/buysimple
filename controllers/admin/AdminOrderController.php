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
                
                // Check if it's an AJAX request
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Order status updated!']);
                    return;
                }
                
                Session::flash('success', 'Order status updated!');
            }
        }
        
        $redirectUrl = $_POST['redirect'] ?? 'admin/orders';
        Helper::redirect($redirectUrl);
    }
    
    public function updatePaymentStatus() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $paymentStatus = $_POST['payment_status'] ?? null;
            
            if ($orderId && $paymentStatus) {
                $orderModel = new Order();
                $orderModel->updatePaymentStatus($orderId, $paymentStatus);
                
                // Check if it's an AJAX request
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Payment status updated!']);
                    return;
                }
                
                Session::flash('success', 'Payment status updated!');
            }
        }
        
        $redirectUrl = $_POST['redirect'] ?? 'admin/orders';
        Helper::redirect($redirectUrl);
    }
    
    public function download() {
        $this->checkAdmin();
        
        $orderModel = new Order();
        $orders = $orderModel->getAll();
        
        // Set headers for CSV download
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8 (helps Excel display special characters correctly)
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        fputcsv($output, [
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Phone',
            'Shipping Address',
            'City',
            'Total Amount',
            'Order Status',
            'Payment Status',
            'Order Date',
            'Notes'
        ]);
        
        // Add order data
        foreach ($orders as $order) {
            $orderItems = $orderModel->getItems($order['id']);
            $itemsList = [];
            foreach ($orderItems as $item) {
                $itemsList[] = $item['product_name'] . ' (Qty: ' . $item['quantity'] . ', Price: ' . $item['product_price'] . ')';
            }
            
            fputcsv($output, [
                $order['order_number'],
                $order['user_name'] ?? 'Guest',
                $order['user_email'] ?? $order['shipping_email'],
                $order['shipping_phone'],
                $order['shipping_address'],
                $order['shipping_city'],
                $order['total_amount'],
                ucfirst($order['status']),
                ucfirst($order['payment_status']),
                date('Y-m-d H:i:s', strtotime($order['created_at'])),
                $order['notes'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
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

