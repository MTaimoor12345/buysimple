<?php $pageTitle = 'Order Details'; ?>
<div class="order-detail-admin">
    <div class="order-header-admin">
        <div>
            <h2>Order #<?php echo htmlspecialchars($order['order_number']); ?></h2>
            <p>Date: <?php echo date('F d, Y h:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <button onclick="window.print()" class="btn btn-primary" style="margin-top: 0;">
                <i class="fas fa-print"></i> Print Order
            </button>
            <form method="POST" action="<?php echo Helper::url('admin/orders/update-status'); ?>" class="status-form" id="order-status-form">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Order Status:</label>
                <select name="status" id="order-status-select" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <span id="order-status-message" style="margin-left: 10px; color: green; font-size: 0.9rem;"></span>
            </form>
        </div>
    </div>

    <div class="order-info-grid">
        <div class="info-card">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['shipping_email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
        </div>

        <div class="info-card">
            <h3>Shipping Address</h3>
            <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city']); ?></p>
        </div>

        <div class="info-card">
            <h3>Order Summary</h3>
            <p><strong>Total Amount:</strong> <?php echo Helper::formatPrice($order['total_amount']); ?></p>
            <p><strong>Payment Status:</strong></p>
            <form method="POST" action="<?php echo Helper::url('admin/orders/update-payment-status'); ?>" class="status-form" id="payment-status-form" style="margin-top: 0.5rem;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="payment_status" id="payment-status-select" style="padding: 0.5rem; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="pending" <?php echo $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo $order['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="failed" <?php echo $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
                <span id="payment-status-message" style="margin-left: 10px; color: green; font-size: 0.9rem;"></span>
            </form>
        </div>
    </div>

    <div class="order-items-admin">
        <h3>Order Items</h3>
        <table class="data-table">
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
                            <?php echo htmlspecialchars($item['product_name']); ?>
                            <?php if (!empty($item['color_name'])): ?>
                                <br><small style="color: #666;">Color: <strong><?php echo htmlspecialchars($item['color_name']); ?></strong></small>
                            <?php endif; ?>
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

    <?php if ($order['notes']): ?>
        <div class="order-notes">
            <h3>Notes</h3>
            <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order Status Update
    const orderStatusForm = document.getElementById('order-status-form');
    const orderStatusSelect = document.getElementById('order-status-select');
    const orderStatusMessage = document.getElementById('order-status-message');
    
    if (orderStatusSelect) {
        orderStatusSelect.addEventListener('change', function() {
            const formData = new FormData(orderStatusForm);
            const xhr = new XMLHttpRequest();
            
            xhr.open('POST', orderStatusForm.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            orderStatusMessage.textContent = '✓ ' + response.message;
                            orderStatusMessage.style.color = 'green';
                            setTimeout(function() {
                                orderStatusMessage.textContent = '';
                            }, 3000);
                        }
                    } catch (e) {
                        // If not JSON, it might be a redirect or error
                        console.error('Error parsing response');
                    }
                }
            };
            
            xhr.send(formData);
        });
    }
    
    // Payment Status Update
    const paymentStatusForm = document.getElementById('payment-status-form');
    const paymentStatusSelect = document.getElementById('payment-status-select');
    const paymentStatusMessage = document.getElementById('payment-status-message');
    
    if (paymentStatusSelect) {
        paymentStatusSelect.addEventListener('change', function() {
            const formData = new FormData(paymentStatusForm);
            const xhr = new XMLHttpRequest();
            
            xhr.open('POST', paymentStatusForm.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            paymentStatusMessage.textContent = '✓ ' + response.message;
                            paymentStatusMessage.style.color = 'green';
                            setTimeout(function() {
                                paymentStatusMessage.textContent = '';
                            }, 3000);
                        }
                    } catch (e) {
                        // If not JSON, it might be a redirect or error
                        console.error('Error parsing response');
                    }
                }
            };
            
            xhr.send(formData);
        });
    }
});
</script>

<style>
@media print {
    /* Hide admin sidebar, header, and navigation */
    .admin-sidebar,
    .admin-header,
    .page-actions,
    .status-form,
    .btn,
    #order-status-message,
    #payment-status-message {
        display: none !important;
    }
    
    /* Show only order content */
    .order-detail-admin {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 20px;
    }
    
    .order-header-admin {
        border-bottom: 2px solid #000;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    .order-header-admin h2 {
        margin: 0;
        font-size: 24px;
    }
    
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .info-card {
        border: 1px solid #ddd;
        padding: 15px;
        background: #f9f9f9;
    }
    
    .info-card h3 {
        margin-top: 0;
        border-bottom: 1px solid #000;
        padding-bottom: 5px;
    }
    
    .order-items-admin {
        margin-top: 30px;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table th,
    .data-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }
    
    .data-table th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
    
    .order-notes {
        margin-top: 30px;
        border-top: 2px solid #000;
        padding-top: 15px;
    }
    
    /* Page break */
    .order-items-admin {
        page-break-inside: avoid;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}
</style>

