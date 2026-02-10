<?php $pageTitle = 'Add Stock by SKU'; ?>
<div class="form-container">
    <h2 style="margin-bottom: 2rem; color: var(--dark-color);">Add Stock by SKU</h2>
    
    <?php if (Session::has('success')): ?>
        <div class="alert alert-success" style="background: #10b981; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php echo Session::flash('success'); ?>
        </div>
    <?php endif; ?>
    
    <?php if (Session::has('error')): ?>
        <div class="alert alert-error" style="background: #ef4444; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php echo Session::flash('error'); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo Helper::url('admin/products/stock'); ?>" class="admin-form">
        <div class="form-group">
            <label>SKU *</label>
            <input type="text" 
                   name="sku" 
                   id="sku_input"
                   required 
                   placeholder="Enter Product SKU"
                   autocomplete="off"
                   style="padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 5px; font-size: 1rem; width: 100%; max-width: 400px;">
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Enter the SKU of the product to add stock
            </small>
        </div>
        
        <div id="product_info" style="display: none; margin: 1.5rem 0; padding: 1.5rem; background: #f8fafc; border-radius: 5px; border: 2px solid #e2e8f0;">
            <h3 style="margin-bottom: 1rem; color: var(--dark-color);">Product Information</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <strong>Product Name:</strong>
                    <p id="product_name" style="margin: 0.5rem 0 0 0; color: var(--text-color);"></p>
                </div>
                <div>
                    <strong>Total Stock:</strong>
                    <p id="total_stock" style="margin: 0.5rem 0 0 0; color: var(--text-color); font-weight: bold; font-size: 1.1rem;"></p>
                </div>
                <div>
                    <strong>Price:</strong>
                    <p id="product_price" style="margin: 0.5rem 0 0 0; color: var(--text-color);"></p>
                </div>
            </div>
            <div id="color_stock_info" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e2e8f0;">
                <strong style="display: block; margin-bottom: 0.75rem; color: var(--dark-color);">Color-wise Stock:</strong>
                <div id="color_stock_list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem;">
                    <!-- Color stock items will be inserted here -->
                </div>
            </div>
        </div>

        <div class="form-group" id="color_group" style="display: none;">
            <label>Color * <span id="color_required_indicator" style="color: red; display: none;">(Required)</span></label>
            <select name="color_name" id="color_name" style="padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 5px; font-size: 1rem; width: 100%; max-width: 400px;">
                <option value="">Select Color</option>
            </select>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;" id="color_help_text">
                Color select karein
            </small>
        </div>

        <div class="form-group">
            <label>Stock to Add *</label>
            <input type="number" 
                   name="stock" 
                   required 
                   min="1"
                   placeholder="Enter quantity to add"
                   style="padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 5px; font-size: 1rem; width: 100%; max-width: 400px;">
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Enter the quantity to add to current stock (minimum 1)
            </small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="background: #10b981; padding: 0.75rem 2rem;">
                <i class="fas fa-plus"></i> Add Stock
            </button>
            <a href="<?php echo Helper::url('admin/products'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('sku_input').addEventListener('blur', function() {
    const sku = this.value.trim();
    const productInfo = document.getElementById('product_info');
    const colorSelect = document.getElementById('color_name');
    const colorGroup = document.getElementById('color_group');
    const colorRequiredIndicator = document.getElementById('color_required_indicator');
    const colorHelpText = document.getElementById('color_help_text');
    const stockForm = document.querySelector('form');
    
    if (!sku) {
        productInfo.style.display = 'none';
        colorGroup.style.display = 'none';
        colorSelect.innerHTML = '<option value="">Select Color</option>';
        colorSelect.removeAttribute('required');
        return;
    }
    
    // Fetch product by SKU
    fetch('<?php echo Helper::url("api/product-by-sku"); ?>?sku=' + encodeURIComponent(sku))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.product) {
                const product = data.product;
                document.getElementById('product_name').textContent = product.name;
                
                // Display total stock - use total_stock if available (sum of color stocks), otherwise use general stock
                let totalStock = 0;
                if (product.total_stock !== undefined) {
                    totalStock = parseInt(product.total_stock) || 0;
                } else if (product.color_stocks && product.color_stocks.length > 0) {
                    // Calculate sum of color stocks
                    totalStock = product.color_stocks.reduce((sum, cs) => sum + (parseInt(cs.stock) || 0), 0);
                } else {
                    totalStock = parseInt(product.stock) || 0;
                }
                document.getElementById('total_stock').textContent = totalStock;
                document.getElementById('product_price').textContent = 'PKR ' + parseFloat(product.price).toFixed(2);
                productInfo.style.display = 'block';
                
                // Load colors if available
                colorSelect.innerHTML = '<option value="">Select Color</option>';
                let hasColors = false;
                const colorStockInfo = document.getElementById('color_stock_info');
                const colorStockList = document.getElementById('color_stock_list');
                colorStockList.innerHTML = '';
                
                if (product.colors) {
                    try {
                        const colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                        if (Array.isArray(colors) && colors.length > 0) {
                            hasColors = true;
                            
                            // Display color-wise stock
                            if (product.color_stocks && product.color_stocks.length > 0) {
                                colorStockInfo.style.display = 'block';
                                
                                colors.forEach(color => {
                                    // Find stock for this color
                                    const colorStockData = product.color_stocks.find(cs => cs.color_name === color.name);
                                    const stock = colorStockData ? colorStockData.stock : 0;
                                    
                                    // Add to dropdown
                                    const option = document.createElement('option');
                                    option.value = color.name;
                                    option.textContent = color.name;
                                    colorSelect.appendChild(option);
                                    
                                    // Display color stock
                                    const colorStockItem = document.createElement('div');
                                    colorStockItem.style.cssText = 'padding: 0.5rem; background: white; border-radius: 5px; border: 1px solid #e2e8f0;';
                                    colorStockItem.innerHTML = `
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <div style="width: 20px; height: 20px; border-radius: 50%; background: ${color.code || '#000'}; border: 2px solid #e2e8f0;"></div>
                                            <strong style="font-size: 0.9rem;">${color.name}</strong>
                                        </div>
                                        <div style="color: var(--text-color); font-size: 0.85rem;">
                                            Stock: <strong style="color: ${stock > 0 ? '#10b981' : '#ef4444'}">${stock}</strong>
                                        </div>
                                    `;
                                    colorStockList.appendChild(colorStockItem);
                                });
                            } else {
                                // No color stock data yet, but product has colors
                                colorStockInfo.style.display = 'block';
                                colors.forEach(color => {
                                    const option = document.createElement('option');
                                    option.value = color.name;
                                    option.textContent = color.name;
                                    colorSelect.appendChild(option);
                                    
                                    const colorStockItem = document.createElement('div');
                                    colorStockItem.style.cssText = 'padding: 0.5rem; background: white; border-radius: 5px; border: 1px solid #e2e8f0;';
                                    colorStockItem.innerHTML = `
                                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <div style="width: 20px; height: 20px; border-radius: 50%; background: ${color.code || '#000'}; border: 2px solid #e2e8f0;"></div>
                                            <strong style="font-size: 0.9rem;">${color.name}</strong>
                                        </div>
                                        <div style="color: var(--text-color); font-size: 0.85rem;">
                                            Stock: <strong style="color: #ef4444">0</strong>
                                        </div>
                                    `;
                                    colorStockList.appendChild(colorStockItem);
                                });
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing colors:', e);
                    }
                }
                
                // If product has colors, make color selection required
                if (hasColors) {
                    colorGroup.style.display = 'block';
                    colorSelect.setAttribute('required', 'required');
                    colorRequiredIndicator.style.display = 'inline';
                    colorHelpText.textContent = 'Is product mein colors hain, isliye color select karna zaroori hai';
                } else {
                    colorStockInfo.style.display = 'none';
                    colorGroup.style.display = 'none';
                    colorSelect.removeAttribute('required');
                    colorRequiredIndicator.style.display = 'none';
                    colorHelpText.textContent = 'Color select karein';
                }
            } else {
                productInfo.style.display = 'none';
                colorGroup.style.display = 'none';
                colorSelect.innerHTML = '<option value="">Select Color</option>';
                colorSelect.removeAttribute('required');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            productInfo.style.display = 'none';
            colorGroup.style.display = 'none';
            colorSelect.innerHTML = '<option value="">Select Color</option>';
            colorSelect.removeAttribute('required');
        });
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const colorSelect = document.getElementById('color_name');
    const colorGroup = document.getElementById('color_group');
    
    // If color group is visible and no color is selected, prevent submission
    if (colorGroup.style.display !== 'none' && !colorSelect.value) {
        e.preventDefault();
        alert('Please select a color. This product has colors, so color selection is required.');
        colorSelect.focus();
        return false;
    }
});
</script>

