<div class="container">
    <h1>Order Details</h1>

    <div class="order-detail">
        <div class="order-info">
            <h2>Order #<?php echo htmlspecialchars($order['order_number']); ?></h2>
            <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </p>
            <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
        </div>

        <div class="shipping-info">
            <h3>Shipping Address</h3>
            <p><?php echo htmlspecialchars($order['shipping_name']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_email']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_phone']); ?></p>
            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city']); ?></p>
        </div>

        <div class="order-items-section">
            <h3>Order Items</h3>
            <table class="order-items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="order-item-info">
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo Helper::asset('images/' . $item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    <?php endif; ?>
                                    <div>
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                        <?php if (!empty($item['color_name'])): ?>
                                            <br><small style="color: #666; font-size: 0.9em;">Color: <strong><?php echo htmlspecialchars($item['color_name']); ?></strong></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo Helper::formatPrice($item['product_price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo Helper::formatPrice($item['subtotal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td><strong><?php echo Helper::formatPrice($order['total_amount']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

