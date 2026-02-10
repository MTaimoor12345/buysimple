<div class="container">
    <h1>Shopping Cart</h1>

    <?php if (empty($items)): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h2>Your cart is empty</h2>
            <p>Add some products to your cart to get started!</p>
            <a href="<?php echo Helper::url('products'); ?>" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
                            $subtotal = $price * $item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <div class="cart-item-info">
                                        <img src="<?php echo Helper::asset('images/' . ($item['image'] ?: 'placeholder.jpg')); ?>"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <div>
                                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                                            <?php if (!empty($item['color_name'])): ?>
                                                <br><small style="color: var(--text-light);">Color:
                                                    <?php echo htmlspecialchars($item['color_name']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo Helper::formatPrice($price); ?></td>
                                <td>
                                    <form method="POST" action="<?php echo Helper::url('cart/update'); ?>"
                                        class="quantity-form">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                            max="<?php echo $item['stock']; ?>" onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td><?php echo Helper::formatPrice($subtotal); ?></td>
                                <td>
                                    <a href="<?php echo Helper::url('cart/remove/' . $item['id']); ?>" class="btn-remove"
                                        onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo Helper::formatPrice($total); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span><?php echo $shipping == 0 ? 'Free' : Helper::formatPrice($shipping); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span><?php echo Helper::formatPrice($grandTotal); ?></span>
                    </div>
                    <a href="<?php echo Helper::url('checkout'); ?>" class="btn btn-primary btn-large btn-block">
                        Proceed to Checkout
                    </a>
                    <a href="<?php echo Helper::url('products'); ?>" class="btn btn-secondary btn-block">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>