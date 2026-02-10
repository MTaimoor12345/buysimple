<?php $pageTitle = 'Orders'; ?>
<div class="page-actions">
    <a href="<?php echo Helper::url('admin/orders/download'); ?>" class="btn btn-primary">
        <i class="fas fa-download"></i> Download All Orders (CSV)
    </a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Order Number</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
            <tr>
                <td colspan="7" class="text-center">No orders found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></td>
                    <td><?php echo Helper::formatPrice($order['total_amount']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $order['payment_status']; ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
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

