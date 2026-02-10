<?php $pageTitle = 'Add New Banner Text'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/banner-texts/create'); ?>" class="admin-form">
        <div class="form-group">
            <label>Banner Text *</label>
            <input type="text" name="text" placeholder="FLAT PKR 129 DELIVERY CHARGES" required>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                This text will be displayed in the top banner. Multiple texts will rotate every 2 seconds.
            </small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0" min="0">
                <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                    Lower numbers appear first in rotation
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
            <button type="submit" class="btn btn-primary">Create Banner Text</button>
            <a href="<?php echo Helper::url('admin/banner-texts'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

