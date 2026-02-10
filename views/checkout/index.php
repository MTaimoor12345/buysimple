<div class="container">
    <h1>Checkout</h1>

    <div class="checkout-layout">
        <div class="checkout-form-section">
            <form method="POST" action="<?php echo Helper::url('checkout/process'); ?>" class="checkout-form">
                <h2>Shipping Information</h2>

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Address *</label>
                    <textarea name="address" rows="3"
                        required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>City *</label>
                    <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-large">Place Order</button>
            </form>
        </div>

        <div class="checkout-summary">
            <h2>Order Summary</h2>
            <div class="order-items">
                <?php foreach ($items as $item): ?>
                    <?php
                    $price = $item['sale_price'] ? $item['sale_price'] : $item['price'];
                    $subtotal = $price * $item['quantity'];
                    ?>
                    <div class="order-item">
                        <img src="<?php echo Helper::asset('images/' . ($item['image'] ?: 'placeholder.jpg')); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="order-item-info">
                            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                            <?php if (!empty($item['color_name'])): ?>
                                <p style="color: #666; font-size: 0.9em;">Color:
                                    <strong><?php echo htmlspecialchars($item['color_name']); ?></strong></p>
                            <?php endif; ?>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p class="price"><?php echo Helper::formatPrice($subtotal); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span><?php echo Helper::formatPrice($total); ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping:</span>
                    <span><?php echo $shipping == 0 ? 'Free' : Helper::formatPrice($shipping); ?></span>
                </div>
                <div class="total-row final">
                    <span>Total:</span>
                    <span><?php echo Helper::formatPrice($grandTotal); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>