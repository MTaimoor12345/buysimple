<?php $pageTitle = 'Edit Hero Slide'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/hero-slides/edit/' . $slide['id']); ?>" class="admin-form" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title (Optional)</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($slide['title'] ?? ''); ?>" placeholder="Welcome to E-Shop">
        </div>

        <div class="form-group">
            <label>Image *</label>
            <div class="image-upload-options">
                <div class="upload-tabs">
                    <button type="button" class="tab-btn active" onclick="switchUploadTab('url')">Image URL</button>
                    <button type="button" class="tab-btn" onclick="switchUploadTab('file')">Upload File</button>
                </div>
                
                <div id="url-tab" class="upload-tab-content active">
                    <input type="text" name="image" id="image_url" value="<?php echo htmlspecialchars($slide['image']); ?>" required>
                    <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                        Enter full image URL. Recommended size: 1920x800px
                    </small>
                </div>
                
                <div id="file-tab" class="upload-tab-content">
                    <input type="file" name="image_file" id="image_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                    <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                        Upload new image file (Max 5MB). Recommended size: 1920x800px
                    </small>
                    <div id="image-preview" style="margin-top: 1rem; display: none;">
                        <img id="preview-img" src="" alt="Preview" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid var(--border-color);">
                    </div>
                </div>
                
                <?php if ($slide['image']): ?>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Current Image:</label>
                        <img src="<?php echo htmlspecialchars($slide['image']); ?>" 
                             alt="Current Image" 
                             style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid var(--border-color);">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Image Link (Optional)</label>
            <input type="text" name="image_link" value="<?php echo htmlspecialchars($slide['image_link'] ?? ''); ?>" placeholder="/products or https://example.com">
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Clicking on the image will redirect to this link. Leave empty if image should not be clickable.
            </small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="button_text" value="<?php echo htmlspecialchars($slide['button_text']); ?>">
            </div>
            <div class="form-group">
                <label>Button Link</label>
                <input type="text" name="button_link" value="<?php echo htmlspecialchars($slide['button_link']); ?>" placeholder="/products">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo $slide['sort_order']; ?>" min="0">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo $slide['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $slide['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Slide</button>
            <a href="<?php echo Helper::url('admin/hero-slides'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

