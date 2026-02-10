<?php
class CheckoutController
{
    public function index()
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to checkout');
            Helper::redirect('login');
            return;
        }

        $cartModel = new Cart();
        $items = $cartModel->getItems();

        if (empty($items)) {
            Session::flash('error', 'Your cart is empty');
            Helper::redirect('cart');
            return;
        }

        $total = $cartModel->getTotal();
        $shipping = $cartModel->getShippingCost();
        $grandTotal = $cartModel->getGrandTotal();
        $user = Auth::user();

        $this->view('checkout/index', [
            'items' => $items,
            'total' => $total,
            'shipping' => $shipping,
            'grandTotal' => $grandTotal,
            'user' => $user
        ]);
    }

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('checkout');
            return;
        }

        if (!Auth::check()) {
            Helper::redirect('login');
            return;
        }

        $cartModel = new Cart();
        $items = $cartModel->getItems();

        if (empty($items)) {
            Session::flash('error', 'Your cart is empty');
            Helper::redirect('cart');
            return;
        }

        $grandTotal = $cartModel->getGrandTotal();
        $user = Auth::user();

        // Prepare order data
        $orderData = [
            'user_id' => $user['id'],
            'total_amount' => $grandTotal,
            'shipping_name' => $_POST['name'],
            'shipping_email' => $_POST['email'],
            'shipping_phone' => $_POST['phone'],
            'shipping_address' => $_POST['address'],
            'shipping_city' => $_POST['city'],
            'notes' => $_POST['notes'] ?? '',
            'items' => []
        ];

        foreach ($items as $item) {
            $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
            $orderData['items'][] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'product_price' => $price,
                'quantity' => $item['quantity'],
                'subtotal' => $price * $item['quantity'],
                'color_name' => $item['color_name'] ?? null
            ];
        }

        $orderModel = new Order();
        $orderId = $orderModel->create($orderData);

        // Clear cart
        $cartModel->clear();

        Session::flash('success', 'Order placed successfully!');
        Helper::redirect('orders/' . $orderId);
    }

    protected function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

