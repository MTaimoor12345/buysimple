<div class="container">
    <h1>My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>You haven't placed any orders yet</p>
            <a href="<?php echo Helper::url('products'); ?>" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <p class="order-date"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div class="order-status">
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-details">
                        <p><strong>Total:</strong> <?php echo Helper::formatPrice($order['total_amount']); ?></p>
                        <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
                    </div>
                    <a href="<?php echo Helper::url('orders/' . $order['id']); ?>" class="btn btn-secondary">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

