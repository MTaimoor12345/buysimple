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
        $galleryImages = Helper::productGalleryUrls($product['gallery'] ?? '');
        $primaryImage = Helper::productImageUrl($product['image'] ?? '');
        if ($primaryImage && !in_array($primaryImage, $galleryImages)) {
            array_unshift($galleryImages, $primaryImage);
        }
        if (!empty($galleryImages)): ?>
            <div class="form-group">
                <label>Current Images</label>
                <div class="product-images-preview">
                    <?php foreach ($galleryImages as $img): ?>
                        <div class="product-image-thumb">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Product image">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
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

