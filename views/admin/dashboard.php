<?php $pageTitle = 'Dashboard'; ?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_orders']); ?></h3>
            <p>Total Orders</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_products']); ?></h3>
            <p>Total Products</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_users']); ?></h3>
            <p>Total Customers</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo Helper::formatPrice($stats['total_revenue']); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
</div>

<div class="recent-orders">
    <h2>Recent Orders</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentOrders)): ?>
                <tr>
                    <td colspan="6" class="text-center">No orders yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></td>
                        <td><?php echo Helper::formatPrice($order['total_amount']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="<?php echo Helper::url('admin/orders/' . $order['id']); ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

