<?php $pageTitle = 'Orders'; ?>
<div class="page-actions"
    style="display: flex; gap: 20px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
    <div class="filter-section">
        <form method="GET" action="<?php echo Helper::url('admin/orders'); ?>"
            style="display: flex; gap: 10px; align-items: flex-end;">
            <div>
                <label for="start_date" style="display: block; font-size: 0.8rem; margin-bottom: 2px;">Start
                    Date</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo $startDate ?? ''; ?>"
                    class="form-control" style="padding: 5px;">
            </div>
            <div>
                <label for="end_date" style="display: block; font-size: 0.8rem; margin-bottom: 2px;">End Date</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo $endDate ?? ''; ?>"
                    class="form-control" style="padding: 5px;">
            </div>
            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 12px;">Filter</button>
            <?php if (!empty($startDate) || !empty($endDate)): ?>
                <a href="<?php echo Helper::url('admin/orders'); ?>" class="btn btn-danger btn-sm"
                    style="padding: 6px 12px;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <a href="<?php echo Helper::url('admin/orders/download'); ?>" class="btn btn-primary">
        <i class="fas fa-download"></i> Download CSV
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
                        <div style="display: flex; gap: 5px;">
                            <a href="<?php echo Helper::url('admin/orders/' . $order['id']); ?>"
                                class="btn btn-sm btn-primary">View</a>
                            <a href="<?php echo Helper::url('admin/orders/delete/' . $order['id']); ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>