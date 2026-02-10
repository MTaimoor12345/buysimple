<div class="container">
    <div class="product-detail">
        <div class="product-images">
            <?php 
            $demoDetailImages = [
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1505740420928-5e56006d30e?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=600&h=600&fit=crop',
                'https://images.unsplash.com/photo-1445205170230-053b83016050?w=600&h=600&fit=crop'
            ];
            $galleryImages = Helper::productGalleryUrls($product['gallery'] ?? '');
            $primaryImage = Helper::productImageUrl($product['image'] ?? '');
            if ($primaryImage) {
                array_unshift($galleryImages, $primaryImage);
                $galleryImages = array_unique($galleryImages);
            }
            $mainImage = !empty($galleryImages) ? $galleryImages[0] : $demoDetailImages[array_rand($demoDetailImages)];
            ?>
            <img src="<?php echo $mainImage; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-product-image" id="mainProductImage">

            <?php if (!empty($galleryImages)): ?>
                <div class="product-thumbnails">
                    <?php foreach ($galleryImages as $index => $img): ?>
                        <img 
                            src="<?php echo htmlspecialchars($img); ?>" 
                            alt="Thumbnail <?php echo $index + 1; ?>" 
                            class="product-thumb-image <?php echo $index === 0 ? 'active' : ''; ?>"
                            data-large-src="<?php echo htmlspecialchars($img); ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-details">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></p>
            
            <div class="product-price">
                <?php if ($product['sale_price']): ?>
                    <span class="price-old"><?php echo Helper::formatPrice($product['price']); ?></span>
                    <span class="price-new"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                <?php else: ?>
                    <span class="price"><?php echo Helper::formatPrice($product['price']); ?></span>
                <?php endif; ?>
            </div>

            <?php 
            $colors = [];
            $colorStockMap = [];
            if (!empty($product['colors'])) {
                $colors = json_decode($product['colors'], true) ?: [];
            }
            if (!empty($colors)): 
                $colorStockModel = new ProductColorStock();
                $colorStocks = $colorStockModel->getByProductId($product['id']);
                foreach ($colorStocks as $cs) {
                    $colorStockMap[$cs['color_name']] = (int)$cs['stock'];
                }
                
                // Filter out colors with 0 stock - only show colors that have stock
                $availableColors = [];
                foreach ($colors as $color) {
                    $colorStock = $colorStockMap[$color['name']] ?? null;
                    // Only include color if it has stock > 0
                    if ($colorStock !== null && $colorStock > 0) {
                        $availableColors[] = $color;
                    }
                }
                
                // Only show color section if there are available colors
                if (!empty($availableColors)):
            ?>
                <div class="product-colors" style="margin: 1.5rem 0;">
                    <strong style="display: block; margin-bottom: 0.75rem;">Select Color:</strong>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        <?php foreach ($availableColors as $index => $color): 
                            $colorStock = $colorStockMap[$color['name']] ?? null;
                            $isAvailable = $colorStock !== null && $colorStock > 0;
                        ?>
                            <div class="color-option" style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem; position: relative;">
                                <input type="radio" 
                                       name="selected_color" 
                                       id="color_<?php echo $index; ?>" 
                                       value="<?php echo htmlspecialchars($color['name']); ?>" 
                                       class="color-radio"
                                       <?php echo $index === 0 ? 'checked' : ''; ?>
                                       <?php echo !$isAvailable ? 'disabled' : ''; ?>
                                       style="position: absolute; opacity: 0; width: 0; height: 0;">
                                <label for="color_<?php echo $index; ?>" 
                                       class="color-label"
                                       style="cursor: <?php echo $isAvailable ? 'pointer' : 'not-allowed'; ?>; display: flex; flex-direction: column; align-items: center; gap: 0.25rem;"
                                       onclick="selectColor('<?php echo htmlspecialchars($color['name']); ?>', <?php echo $index; ?>)">
                                    <div id="color_circle_<?php echo $index; ?>" 
                                         class="color-circle"
                                         style="width: 50px; height: 50px; border-radius: 50%; background: <?php echo htmlspecialchars($color['code'] ?? '#000000'); ?>; border: 3px solid <?php echo $index === 0 ? '#2563EB' : '#e2e8f0'; ?>; transition: all 0.2s; position: relative; <?php echo !$isAvailable ? 'opacity: 0.5;' : ''; ?><?php echo $index === 0 ? '; box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);' : ''; ?>" 
                                         title="<?php echo htmlspecialchars($color['name'] ?? ''); ?> <?php echo $colorStock !== null ? '(Stock: ' . $colorStock . ')' : ''; ?>">
                                    </div>
                                    <span style="font-size: 0.875rem; color: var(--text-color); <?php echo !$isAvailable ? 'opacity: 0.5;' : ''; ?>">
                                        <?php echo htmlspecialchars($color['name'] ?? ''); ?>
                                        <?php if ($colorStock !== null): ?>
                                            <br><small style="font-size: 0.75rem;">(<?php echo $colorStock; ?> available)</small>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php 
                endif; // End of availableColors check
            endif; // End of colors check
            ?>

            <div class="product-meta">
                <!-- <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></p> -->
                <p><strong>Stock:</strong> 
                    <span id="stock_display" class="in-stock">
                        <?php 
                        if (!empty($colors)) {
                            // Use availableColors if it exists, otherwise use all colors
                            $colorsToCheck = isset($availableColors) && !empty($availableColors) ? $availableColors : $colors;
                            if (!empty($colorsToCheck)) {
                                $firstColor = $colorsToCheck[0];
                                // Check if color stock exists in the map
                                if (isset($colorStockMap[$firstColor['name']])) {
                                    $firstColorStock = (int)$colorStockMap[$firstColor['name']];
                                    $displayStock = $firstColorStock;
                                } else {
                                    // If color stock doesn't exist, use general stock
                                    $displayStock = (int)$product['stock'];
                                }
                                
                                if ($displayStock > 0) {
                                    echo 'In Stock';
                                } else {
                                    echo '<span class="out-of-stock">Out of Stock</span>';
                                }
                            } else {
                                // No available colors with stock
                                echo '<span class="out-of-stock">Out of Stock</span>';
                            }
                        } else {
                            if ($product['stock'] > 0) {
                                echo 'In Stock';
                            } else {
                                echo '<span class="out-of-stock">Out of Stock</span>';
                            }
                        }
                        ?>
                    </span>
                </p>
            </div>

            <?php if ($product['short_description']): ?>
                <p class="product-short-desc"><?php echo htmlspecialchars($product['short_description']); ?></p>
            <?php endif; ?>

            <form method="POST" action="<?php echo Helper::url('cart/add'); ?>" class="add-to-cart-form" id="productForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="color_name" id="selected_color_input" value="">
                <div class="quantity-selector">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" id="product_quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                </div>
                <?php if (!empty($colors)): ?>
                    <div id="color_validation_message" style="display: none; color: #ef4444; margin: 1rem 0; padding: 0.75rem; background: #fee2e2; border-radius: 5px; border: 1px solid #fecaca;">
                        <i class="fas fa-exclamation-circle"></i> Please select a color before adding to cart.
                    </div>
                <?php endif; ?>
                <div class="product-buttons" style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary btn-large" id="addToCartBtn" style="flex: 1;">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button type="submit" name="buy_now" value="1" class="btn btn-success btn-large" id="buyNowBtn" style="flex: 1; background: #10b981;">
                        <i class="fas fa-bolt"></i> Buy Now
                    </button>
                </div>
            </form>

            <?php if ($product['description']): ?>
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($relatedProducts)): ?>
        <section class="related-products">
            <h2 class="section-title">Related Products</h2>
            <div class="product-grid">
                <?php foreach ($relatedProducts as $related): ?>
                    <?php if ($related['id'] != $product['id']): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="<?php echo Helper::url('products/' . $related['slug']); ?>">
                                    <?php 
                                    $relatedImgSrc = $related['image'] ? Helper::asset('images/' . $related['image']) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop';
                                    ?>
                                    <img src="<?php echo $relatedImgSrc; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" loading="lazy">
                                </a>
                            </div>
                            <div class="product-info">
                                <h3><a href="<?php echo Helper::url('products/' . $related['slug']); ?>"><?php echo htmlspecialchars($related['name']); ?></a></h3>
                                <div class="product-price">
                                    <?php if ($related['sale_price']): ?>
                                        <span class="price-old"><?php echo Helper::formatPrice($related['price']); ?></span>
                                        <span class="price-new"><?php echo Helper::formatPrice($related['sale_price']); ?></span>
                                    <?php else: ?>
                                        <span class="price"><?php echo Helper::formatPrice($related['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
// Global function to handle color selection
function selectColor(colorName, index) {
    const selectedColorInput = document.getElementById('selected_color_input');
    const colorRadios = document.querySelectorAll('input[name="selected_color"]');
    const quantityInput = document.getElementById('product_quantity');
    const stockDisplay = document.getElementById('stock_display');
    
    // Uncheck all radios
    colorRadios.forEach(radio => {
        radio.checked = false;
    });
    
    // Check the selected radio
    const selectedRadio = document.getElementById('color_' + index);
    if (selectedRadio && !selectedRadio.disabled) {
        selectedRadio.checked = true;
        if (selectedColorInput) {
            selectedColorInput.value = colorName;
        }
        
        // Update visual feedback for all color circles
        const allCircles = document.querySelectorAll('.color-circle');
        allCircles.forEach((circle, idx) => {
            if (idx === index) {
                circle.style.borderColor = '#2563EB';
                circle.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
            } else {
                circle.style.borderColor = '#e2e8f0';
                circle.style.boxShadow = 'none';
            }
        });
        
        // Update stock display immediately
        if (colorName && stockDisplay) {
            // Fetch color stock via AJAX
            fetch('<?php echo Helper::url("api/color-stock"); ?>?product_id=<?php echo $product['id']; ?>&color_name=' + encodeURIComponent(colorName))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let maxStock = <?php echo $product['stock']; ?>;
                        if (data.stock !== null && data.stock !== undefined) {
                            maxStock = parseInt(data.stock);
                        }
                        
                        if (quantityInput) {
                            quantityInput.max = maxStock;
                            if (parseInt(quantityInput.value) > maxStock) {
                                quantityInput.value = maxStock > 0 ? maxStock : 1;
                            }
                        }
                        
                        // Update stock display
                        if (stockDisplay) {
                            if (maxStock > 0) {
                                stockDisplay.innerHTML = 'In Stock (' + maxStock + ')';
                                stockDisplay.className = 'in-stock';
                            } else {
                                stockDisplay.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
                                stockDisplay.className = 'out-of-stock';
                            }
                        }
                        
                        // Update button states
                        if (typeof updateButtonStates === 'function') {
                            updateButtonStates();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching color stock:', error);
                });
        }
        
        // Also call updateMaxQuantity if it exists
        if (typeof updateMaxQuantity === 'function') {
            updateMaxQuantity();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const buyNowBtn = document.getElementById('buyNowBtn');
    const productForm = document.getElementById('productForm');
    const selectedColorInput = document.getElementById('selected_color_input');
    const colorRadios = document.querySelectorAll('input[name="selected_color"]');
    const quantityInput = document.getElementById('product_quantity');
    
    // Update selected color in hidden input when radio changes
    colorRadios.forEach((radio, index) => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                selectedColorInput.value = this.value;
                
                // Update visual feedback
                const allCircles = document.querySelectorAll('.color-circle');
                allCircles.forEach((circle, idx) => {
                    if (idx === index) {
                        circle.style.borderColor = '#2563EB';
                        circle.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
                    } else {
                        circle.style.borderColor = '#e2e8f0';
                        circle.style.boxShadow = 'none';
                    }
                });
                
                updateMaxQuantity();
            }
        });
    });
    
    // Set initial color
    const checkedColor = document.querySelector('input[name="selected_color"]:checked');
    if (checkedColor && checkedColor.value) {
        selectedColorInput.value = checkedColor.value;
        const checkedIndex = Array.from(colorRadios).indexOf(checkedColor);
        if (checkedIndex >= 0) {
            const checkedCircle = document.getElementById('color_circle_' + checkedIndex);
            if (checkedCircle) {
                checkedCircle.style.borderColor = '#2563EB';
                checkedCircle.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
            }
        }
        updateMaxQuantity();
    } else if (colorRadios.length > 0) {
        // If no color is checked but colors exist, check the first available one
        let firstAvailable = null;
        for (let i = 0; i < colorRadios.length; i++) {
            if (!colorRadios[i].disabled && colorRadios[i].value) {
                firstAvailable = colorRadios[i];
                colorRadios[i].checked = true;
                selectedColorInput.value = colorRadios[i].value;
                const firstCircle = document.getElementById('color_circle_' + i);
                if (firstCircle) {
                    firstCircle.style.borderColor = '#2563EB';
                    firstCircle.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
                }
                updateMaxQuantity();
                break;
            }
        }
    }
    
    // Ensure selected color is set before form submission
    if (hasColors && !selectedColorInput.value && colorRadios.length > 0) {
        const firstEnabled = Array.from(colorRadios).find(r => !r.disabled && r.value);
        if (firstEnabled) {
            firstEnabled.checked = true;
            selectedColorInput.value = firstEnabled.value;
        }
    }
    
    // Update max quantity and stock display based on selected color
    function updateMaxQuantity() {
        const selectedColor = selectedColorInput.value;
        const stockDisplay = document.getElementById('stock_display');
        
        if (selectedColor) {
            // Fetch color stock via AJAX
            fetch('<?php echo Helper::url("api/color-stock"); ?>?product_id=<?php echo $product['id']; ?>&color_name=' + encodeURIComponent(selectedColor))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Handle stock value - it can be 0, null, or a number
                        let maxStock = <?php echo $product['stock']; ?>;
                        if (data.stock !== null && data.stock !== undefined) {
                            maxStock = parseInt(data.stock);
                        }
                        
                        quantityInput.max = maxStock;
                        if (parseInt(quantityInput.value) > maxStock) {
                            quantityInput.value = maxStock > 0 ? maxStock : 1;
                        }
                        
                        // Update stock display
                        if (stockDisplay) {
                            if (maxStock > 0) {
                                stockDisplay.innerHTML = 'In Stock (' + maxStock + ')';
                                stockDisplay.className = 'in-stock';
                            } else {
                                stockDisplay.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
                                stockDisplay.className = 'out-of-stock';
                            }
                        }
                        
                        // Update button states
                        if (typeof updateButtonStates === 'function') {
                            updateButtonStates();
                        }
                    } else {
                        // If API fails, use general stock
                        const generalStock = <?php echo $product['stock']; ?>;
                        quantityInput.max = generalStock;
                        if (stockDisplay) {
                            if (generalStock > 0) {
                                stockDisplay.innerHTML = 'In Stock';
                                stockDisplay.className = 'in-stock';
                            } else {
                                stockDisplay.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
                                stockDisplay.className = 'out-of-stock';
                            }
                        }
                        
                        // Update button states
                        if (typeof updateButtonStates === 'function') {
                            updateButtonStates();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching color stock:', error);
                });
        } else {
            const generalStock = <?php echo $product['stock']; ?>;
            quantityInput.max = generalStock;
            if (stockDisplay) {
                if (generalStock > 0) {
                    stockDisplay.innerHTML = 'In Stock';
                    stockDisplay.className = 'in-stock';
                } else {
                    stockDisplay.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
                    stockDisplay.className = 'out-of-stock';
                }
            }
            
            // Update button states
            if (typeof updateButtonStates === 'function') {
                updateButtonStates();
            }
        }
    }
    
    // Form validation and handlers
    const addToCartBtn = document.getElementById('addToCartBtn');
    const colorValidationMessage = document.getElementById('color_validation_message');
    const hasColors = colorRadios.length > 0;
    
    // Function to update button states based on stock
    function updateButtonStates() {
        const selectedColor = selectedColorInput.value;
        let isAvailable = true;
        
        if (hasColors && selectedColor) {
            // Check color stock via the stock display
            const stockDisplay = document.getElementById('stock_display');
            if (stockDisplay) {
                const stockText = stockDisplay.textContent || stockDisplay.innerText;
                isAvailable = !stockText.includes('Out of Stock');
            }
        } else if (hasColors && !selectedColor) {
            isAvailable = false;
        } else {
            // For products without colors, check general stock
            const generalStock = <?php echo $product['stock']; ?>;
            isAvailable = generalStock > 0;
        }
        
        if (addToCartBtn) {
            addToCartBtn.disabled = !isAvailable;
        }
        if (buyNowBtn) {
            buyNowBtn.disabled = !isAvailable;
        }
    }
    
    // Form validation before submit (for Add to Cart)
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            // Ensure color is set if colors exist
            if (hasColors) {
                const checkedRadio = document.querySelector('input[name="selected_color"]:checked');
                if (checkedRadio && checkedRadio.value) {
                    selectedColorInput.value = checkedRadio.value;
                } else {
                    // Try to find any checked radio
                    const anyChecked = Array.from(colorRadios).find(r => r.checked && r.value);
                    if (anyChecked) {
                        selectedColorInput.value = anyChecked.value;
                    }
                }
                
                // Final check - if still no color, prevent submission
                if (!selectedColorInput.value || selectedColorInput.value.trim() === '') {
                    e.preventDefault();
                    e.stopPropagation();
                    if (colorValidationMessage) {
                        colorValidationMessage.style.display = 'block';
                    }
                    alert('Please select a color before adding to cart.');
                    return false;
                }
            }
            // Allow form to submit normally
            return true;
        });
    }
    
    // Buy Now button handler - ensure color is set before form submission
    if (buyNowBtn && productForm) {
        buyNowBtn.addEventListener('click', function(e) {
            // Ensure color is set if colors exist
            if (hasColors) {
                const checkedRadio = document.querySelector('input[name="selected_color"]:checked');
                if (checkedRadio && checkedRadio.value) {
                    if (selectedColorInput) {
                        selectedColorInput.value = checkedRadio.value;
                    }
                } else {
                    // Try to find any checked radio
                    const anyChecked = Array.from(colorRadios).find(r => r.checked && r.value);
                    if (anyChecked && selectedColorInput) {
                        selectedColorInput.value = anyChecked.value;
                    }
                }
                
                if (!selectedColorInput || !selectedColorInput.value || selectedColorInput.value.trim() === '') {
                    e.preventDefault();
                    if (colorValidationMessage) {
                        colorValidationMessage.style.display = 'block';
                    }
                    alert('Please select a color before buying.');
                    return false;
                }
            }
            // Allow form to submit normally with buy_now parameter
        });
    }
    
    // Update button states initially and when stock changes
    updateButtonStates();
    
    // Hide validation message when color is selected
    colorRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                selectedColorInput.value = this.value;
                if (colorValidationMessage) {
                    colorValidationMessage.style.display = 'none';
                }
                updateMaxQuantity();
                updateButtonStates();
            }
        });
    });
});
</script>

