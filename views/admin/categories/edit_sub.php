<?php $pageTitle = 'Edit Sub-Category'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/edit/' . $subCategory['id']); ?>" class="admin-form">
        <div class="form-group">
            <label>Parent Category</label>
            <input type="text" value="<?php echo htmlspecialchars($category['name']); ?>" disabled>
        </div>

        <div class="form-group">
            <label>Sub-Category Name *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($subCategory['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?php echo htmlspecialchars($subCategory['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo $subCategory['sort_order'] ?? 0; ?>" min="0">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo ($subCategory['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($subCategory['status'] ?? 'active') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Sub-Category</button>
            <a href="<?php echo Helper::url('admin/categories'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

