<?php
class CartController
{
    public function index()
    {
        $cartModel = new Cart();
        $items = $cartModel->getItems();
        $total = $cartModel->getTotal();

        $shipping = $cartModel->getShippingCost();
        $grandTotal = $cartModel->getGrandTotal();

        $this->view('cart/index', [
            'items' => $items,
            'total' => $total,
            'shipping' => $shipping,
            'grandTotal' => $grandTotal
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('cart');
            return;
        }

        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        $colorName = $_POST['color_name'] ?? null;
        $buyNow = isset($_POST['buy_now']) && $_POST['buy_now'] == '1';

        if ($productId) {
            // Check if product has colors
            $productModel = new Product();
            $product = $productModel->getById($productId);

            if ($product) {
                $colors = [];
                if (!empty($product['colors'])) {
                    $colors = json_decode($product['colors'], true) ?: [];
                }

                // If product has colors, color selection is required
                if (!empty($colors) && empty($colorName)) {
                    Session::flash('error', 'Please select a color for this product.');
                    Helper::redirect('products/' . $product['slug']);
                    return;
                }

                // Validate color stock if color is selected
                if (!empty($colorName)) {
                    $colorStockModel = new ProductColorStock();
                    $colorStock = $colorStockModel->getByProductAndColor($productId, $colorName);
                    $availableStock = $colorStock ? (int) $colorStock['stock'] : 0;

                    if ($availableStock < $quantity) {
                        Session::flash('error', 'Insufficient stock for selected color. Available: ' . $availableStock);
                        Helper::redirect('products/' . $product['slug']);
                        return;
                    }
                } else {
                    // Check general stock
                    if ($product['stock'] < $quantity) {
                        Session::flash('error', 'Insufficient stock. Available: ' . $product['stock']);
                        Helper::redirect('products/' . $product['slug']);
                        return;
                    }
                }
            }

            $cartModel = new Cart();
            $cartModel->add($productId, $quantity, $colorName);
            Session::flash('success', 'Product added to cart!');
        }

        // If Buy Now, redirect to cart, otherwise redirect back to product or cart
        if ($buyNow) {
            Helper::redirect('cart');
        } else {
            // For Add to Cart, redirect back to product page
            if ($product) {
                Helper::redirect('products/' . $product['slug']);
            } else {
                Helper::redirect('cart');
            }
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('cart');
            return;
        }

        $cartId = $_POST['cart_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;

        if ($cartId) {
            $cartModel = new Cart();
            $cartModel->updateQuantity($cartId, $quantity);
            Session::flash('success', 'Cart updated!');
        }

        Helper::redirect('cart');
    }

    public function remove($id)
    {
        $cartModel = new Cart();
        $cartModel->remove($id);
        Session::flash('success', 'Item removed from cart!');
        Helper::redirect('cart');
    }

    protected function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

