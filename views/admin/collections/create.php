<?php $pageTitle = 'Add New Collection'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/collections/create'); ?>" class="admin-form" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" placeholder="Premium Men Watches" required>
        </div>

        <div class="form-group">
            <label>Image *</label>
            <div class="image-upload-options">
                <div class="upload-tabs">
                    <button type="button" class="tab-btn active" onclick="switchUploadTab('url')">Image URL</button>
                    <button type="button" class="tab-btn" onclick="switchUploadTab('file')">Upload File</button>
                </div>
                
                <div id="url-tab" class="upload-tab-content active">
                    <input type="text" name="image" id="image_url" placeholder="https://images.unsplash.com/photo-..." required>
                    <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                        Enter full image URL. Recommended size: 400x500px
                    </small>
                </div>
                
                <div id="file-tab" class="upload-tab-content">
                    <input type="file" name="image_file" id="image_file" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" onchange="previewImage(this)">
                    <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                        Upload image file (Max 5MB). Recommended size: 400x500px
                    </small>
                    <div id="image-preview" style="margin-top: 1rem; display: none;">
                        <img id="preview-img" src="" alt="Preview" style="max-width: 100%; height: 200px; object-fit: cover; border-radius: 10px; border: 2px solid var(--border-color);">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Link (Optional)</label>
            <input type="text" name="link" placeholder="/products or https://example.com">
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                Clicking on the collection image will redirect to this link. Leave empty if image should not be clickable.
            </small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0">
                <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                    Lower numbers appear first
                </small>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Collection</button>
            <a href="<?php echo Helper::url('admin/collections'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

