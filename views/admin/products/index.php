<?php $pageTitle = 'Products'; ?>
<div class="page-actions"
    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div class="action-buttons">
        <a href="<?php echo Helper::url('admin/products/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
        <a href="<?php echo Helper::url('admin/products/stock'); ?>" class="btn btn-success">
            <i class="fas fa-box"></i> Add Stock by SKU
        </a>
    </div>

    <form method="GET" action="<?php echo Helper::url('admin/products'); ?>" class="admin-search-form"
        style="display: flex; gap: 10px;">
        <input type="text" name="search" placeholder="Search by name or SKU..."
            value="<?php echo htmlspecialchars($search ?? ''); ?>"
            style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 250px;">
        <button type="submit" class="btn btn-secondary" style="padding: 8px 15px;">
            <i class="fas fa-search"></i>
        </button>
        <?php if (!empty($search)): ?>
            <a href="<?php echo Helper::url('admin/products'); ?>" class="btn btn-danger" style="padding: 8px 15px;">
                <i class="fas fa-times"></i>
            </a>
        <?php endif; ?>
    </form>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr>
                <td colspan="7" class="text-center">No products found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <img src="<?php echo Helper::asset('images/' . ($product['image'] ?: 'placeholder.jpg')); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumb">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>
                        <?php if ($product['sale_price']): ?>
                            <span class="price-old"><?php echo Helper::formatPrice($product['price']); ?></span>
                            <span class="price-new"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                        <?php else: ?>
                            <?php echo Helper::formatPrice($product['price']); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="stock-management" style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="number" class="stock-input" id="stock_<?php echo $product['id']; ?>"
                                value="<?php echo $product['stock']; ?>" data-product-id="<?php echo $product['id']; ?>" min="0"
                                style="width: 100px; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 5px; font-size: 0.9rem; font-weight: 500;">
                            <button type="button" class="btn btn-sm btn-success"
                                onclick="updateStock(<?php echo $product['id']; ?>, document.getElementById('stock_<?php echo $product['id']; ?>').value)"
                                style="padding: 0.5rem 0.75rem; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; transition: all 0.3s;"
                                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $product['status']; ?>">
                            <?php echo ucfirst($product['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Helper::url('admin/products/edit/' . $product['id']); ?>"
                            class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?php echo Helper::url('admin/products/delete/' . $product['id']); ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
    function updateStock(productId, stock) {
        const stockValue = parseInt(stock);
        if (isNaN(stockValue) || stockValue < 0) {
            alert('Please enter a valid stock number');
            return;
        }

        fetch('<?php echo Helper::url("admin/products/update-stock"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId + '&stock=' + stockValue
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const message = document.createElement('div');
                    message.className = 'alert alert-success';
                    message.style.position = 'fixed';
                    message.style.top = '20px';
                    message.style.right = '20px';
                    message.style.zIndex = '9999';
                    message.style.padding = '1rem';
                    message.style.borderRadius = '5px';
                    message.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
                    message.innerHTML = '<i class="fas fa-check-circle"></i> Stock updated successfully!';
                    document.body.appendChild(message);
                    setTimeout(() => {
                        message.remove();
                    }, 3000);
                } else {
                    alert('Error updating stock: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating stock. Please try again.');
            });
    }
</script>