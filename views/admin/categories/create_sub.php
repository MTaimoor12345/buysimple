<?php $pageTitle = 'Add Sub-Category to ' . htmlspecialchars($category['name']); ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/create'); ?>" class="admin-form">
        <div class="form-group">
            <label>Parent Category</label>
            <input type="text" value="<?php echo htmlspecialchars($category['name']); ?>" disabled>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                This sub-category will be added to: <?php echo htmlspecialchars($category['name']); ?>
            </small>
        </div>

        <div class="form-group">
            <label>Sub-Category Name *</label>
            <input type="text" name="name" placeholder="Mobile Phones" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3" placeholder="Sub-category description..."></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0">
                <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                    Lower numbers appear first in dropdown
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
            <button type="submit" class="btn btn-primary">Create Sub-Category</button>
            <a href="<?php echo Helper::url('admin/categories'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

