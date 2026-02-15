<?php $pageTitle = 'Edit Product'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/products/edit/' . $product['id']); ?>" class="admin-form" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_description" rows="2"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Price *</label>
                <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Sale Price</label>
                <input type="number" name="sale_price" step="0.01" value="<?php echo $product['sale_price'] ?? ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>SKU</label>
            <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Colors (Optional)</label>
            <div id="colors-container">
                <?php 
                $colors = [];
                if (!empty($product['colors'])) {
                    $colors = json_decode($product['colors'], true) ?: [];
                }
                if (empty($colors)) {
                    $colors = [['name' => '', 'code' => '#ff0000']];
                }
                foreach ($colors as $color): 
                ?>
                    <div class="color-input-group" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center;">
                        <input type="text" name="colors[]" value="<?php echo htmlspecialchars($color['name'] ?? ''); ?>" placeholder="Color name (e.g., Red, Blue)" style="flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 5px;">
                        <input type="color" name="color_codes[]" value="<?php echo htmlspecialchars($color['code'] ?? '#ff0000'); ?>" style="width: 60px; height: 40px; border: 1px solid #e2e8f0; border-radius: 5px; cursor: pointer;">
                        <button type="button" class="btn-remove-color" onclick="removeColor(this)" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer;">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addColorField()" style="background: #10b981; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; margin-top: 0.5rem;">
                <i class="fas fa-plus"></i> Add Color
            </button>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Add product colors with their color codes (optional)
            </small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" id="category_id" required onchange="loadSubCategories(this.value)">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Sub-Category (Optional)</label>
            <select name="sub_category_id" id="sub_category_id" onchange="updateCheckboxState()">
                <option value="">Select Sub-Category (Optional)</option>
                <?php if (!empty($subCategories)): ?>
                    <?php foreach ($subCategories as $subCat): ?>
                        <option value="<?php echo $subCat['id']; ?>" 
                                <?php echo $product['sub_category_id'] == $subCat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subCat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Sub-category select karne se product us sub-category mein bhi show hoga
            </small>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="show_on_main_category" id="show_on_main_category" value="1" 
                       <?php echo (!empty($product['show_in_main_category']) || empty($product['sub_category_id'])) ? 'checked' : ''; ?>>
                Show on Main Category Page
            </label>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Agar checked hai to product main category page par bhi show hoga (sub-category ke saath bhi agar select ki hai)
            </small>
        </div>

        <div class="form-group">
            <label>Product Images</label>
            <input type="file" name="images[]" id="product_images_edit" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Nayi images upload karein (optional). Purani images automatic save rahengi.
            </small>
            <div id="product-images-preview-edit" class="product-images-preview" style="margin-top: 1rem; display: none;"></div>
        </div>

        <?php 
        // Get raw image paths for form submission
        $rawImages = [];
        if (!empty($product['image'])) {
            $rawImages[] = $product['image'];
        }
        if (!empty($product['gallery'])) {
            $gallery = json_decode($product['gallery'], true);
            if (is_array($gallery)) {
                $rawImages = array_merge($rawImages, $gallery);
            }
        }
        
        // Get full URLs for display
        $displayImages = [];
        foreach ($rawImages as $img) {
            $displayImages[] = [
                'raw' => $img,
                'url' => Helper::productImageUrl($img)
            ];
        }
        
        if (!empty($displayImages)): ?>
            <div class="form-group">
                <label>Current Images <small>(Drag and drop to reorder)</small></label>
                <div id="sortable-images" class="product-images-preview sortable-grid">
                    <?php foreach ($displayImages as $index => $imgData): ?>
                        <div class="product-image-thumb draggable-item" draggable="true" data-index="<?php echo $index; ?>">
                            <img src="<?php echo htmlspecialchars($imgData['url']); ?>" alt="Product image">
                            <input type="hidden" name="sorted_images[]" value="<?php echo htmlspecialchars($imgData['raw']); ?>">
                            <div class="drag-handle"><i class="fas fa-grip-lines"></i></div>
                            <button type="button" class="btn-remove-image" onclick="removeImage(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <style>
                .sortable-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                    gap: 1rem;
                    padding: 1rem;
                    background: #f8fafc;
                    border-radius: 8px;
                    border: 2px dashed #e2e8f0;
                }
                .draggable-item {
                    position: relative;
                    cursor: move;
                    transition: transform 0.2s, box-shadow 0.2s;
                    background: white;
                    padding: 5px;
                    border-radius: 4px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .draggable-item.dragging {
                    opacity: 0.5;
                    border: 2px dashed #2563EB;
                }
                .draggable-item:hover {
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                .draggable-item img {
                    width: 100%;
                    height: 100px;
                    object-fit: cover;
                    border-radius: 4px;
                    display: block;
                }
                .drag-handle {
                    position: absolute;
                    top: 5px;
                    left: 5px;
                    background: rgba(0,0,0,0.5);
                    color: white;
                    padding: 2px 5px;
                    border-radius: 3px;
                    font-size: 10px;
                }
                .btn-remove-image {
                    position: absolute;
                    top: -5px;
                    right: -5px;
                    background: #ef4444;
                    color: white;
                    border: none;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
                }
            </style>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('sortable-images');
                    if (!container) return;
                    
                    let draggedItem = null;
                    
                    const items = container.querySelectorAll('.draggable-item');
                    
                    items.forEach(item => {
                        item.addEventListener('dragstart', function(e) {
                            draggedItem = this;
                            setTimeout(() => this.classList.add('dragging'), 0);
                        });
                        
                        item.addEventListener('dragend', function() {
                            this.classList.remove('dragging');
                            draggedItem = null;
                        });
                        
                        item.addEventListener('dragenter', function(e) {
                            e.preventDefault();
                            if (this !== draggedItem) {
                                this.style.borderColor = '#2563EB';
                            }
                        });
                        
                        item.addEventListener('dragleave', function() {
                            this.style.borderColor = 'transparent';
                        });
                    });
                    
                    container.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        const afterElement = getDragAfterElement(container, e.clientY, e.clientX);
                        const draggable = document.querySelector('.dragging');
                        if (afterElement == null) {
                            container.appendChild(draggable);
                        } else {
                            container.insertBefore(draggable, afterElement);
                        }
                    });
                    
                    function getDragAfterElement(container, y, x) {
                        const draggableElements = [...container.querySelectorAll('.draggable-item:not(.dragging)')];
                        
                        return draggableElements.reduce((closest, child) => {
                            const box = child.getBoundingClientRect();
                            const offsetX = x - box.left - box.width / 2;
                            const offsetY = y - box.top - box.height / 2;
                            
                            // We care mostly about position, simplified distance check
                            const dist = Math.hypot(offsetX, offsetY);
                            
                            if (dist < closest.offset) {
                                return { offset: dist, element: child };
                            } else {
                                return closest;
                            }
                        }, { offset: Number.POSITIVE_INFINITY }).element;
                    }
                });
                
                function removeImage(btn) {
                    if (confirm('Are you sure you want to remove this image? (Update product to save changes)')) {
                        btn.closest('.draggable-item').remove();
                    }
                }
            </script>
        <?php endif; ?>

        <div class="form-group">
            <label>
                <input type="checkbox" name="featured" value="1" <?php echo $product['featured'] ? 'checked' : ''; ?>> Featured Product
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="<?php echo Helper::url('admin/products'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function updateCheckboxState() {
    const subCategorySelect = document.getElementById('sub_category_id');
    const checkbox = document.getElementById('show_on_main_category');
    
    // If sub-category is selected, checkbox should be checked by default
    if (subCategorySelect.value) {
        checkbox.checked = true;
    }
}

function loadSubCategories(categoryId) {
    const subCategorySelect = document.getElementById('sub_category_id');
    
    // Clear existing options
    subCategorySelect.innerHTML = '<option value="">Select Sub-Category (Optional)</option>';
    
    if (!categoryId) {
        subCategorySelect.disabled = true;
        return;
    }
    
    // Fetch sub-categories via AJAX
    fetch('<?php echo Helper::url("api/sub-categories"); ?>?category_id=' + categoryId)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.subCategories) {
                data.subCategories.forEach(subCat => {
                    const option = document.createElement('option');
                    option.value = subCat.id;
                    option.textContent = subCat.name;
                    // Check if this was the previously selected sub-category
                    <?php if (!empty($product['sub_category_id'])): ?>
                    if (subCat.id == <?php echo $product['sub_category_id']; ?>) {
                        option.selected = true;
                    }
                    <?php endif; ?>
                    subCategorySelect.appendChild(option);
                });
                subCategorySelect.disabled = false;
            } else {
                subCategorySelect.disabled = true;
            }
        })
        .catch(error => {
            console.error('Error loading sub-categories:', error);
            subCategorySelect.disabled = true;
        });
}

// Load sub-categories on page load if category is selected
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category_id');
    if (categorySelect && categorySelect.value) {
        loadSubCategories(categorySelect.value);
    }
    
    // Handle color form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const colorInputs = document.querySelectorAll('input[name="colors[]"]');
            const colorCodeInputs = document.querySelectorAll('input[name="color_codes[]"]');
            const colors = [];
            
            colorInputs.forEach((input, index) => {
                const colorName = input.value.trim();
                const colorCode = colorCodeInputs[index] ? colorCodeInputs[index].value : '#000000';
                if (colorName) {
                    colors.push({
                        name: colorName,
                        code: colorCode
                    });
                }
            });
            
            // Create hidden input with JSON data
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'colors_json';
            hiddenInput.value = JSON.stringify(colors);
            this.appendChild(hiddenInput);
        });
    }
});

function addColorField() {
    const container = document.getElementById('colors-container');
    const colorGroup = document.createElement('div');
    colorGroup.className = 'color-input-group';
    colorGroup.style.cssText = 'display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center;';
    colorGroup.innerHTML = `
        <input type="text" name="colors[]" placeholder="Color name (e.g., Red, Blue)" style="flex: 1; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 5px;">
        <input type="color" name="color_codes[]" value="#ff0000" style="width: 60px; height: 40px; border: 1px solid #e2e8f0; border-radius: 5px; cursor: pointer;">
        <button type="button" class="btn-remove-color" onclick="removeColor(this)" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer;">Remove</button>
    `;
    container.appendChild(colorGroup);
}

function removeColor(btn) {
    btn.closest('.color-input-group').remove();
}
</script>

