<?php $pageTitle = 'Edit Banner Text'; ?>
<div class="form-container">
    <form method="POST" action="<?php echo Helper::url('admin/banner-texts/edit/' . $text['id']); ?>" class="admin-form">
        <div class="form-group">
            <label>Banner Text *</label>
            <input type="text" name="text" value="<?php echo htmlspecialchars($text['text']); ?>" required>
            <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                This text will be displayed in the top banner. Multiple texts will rotate every 2 seconds.
            </small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="<?php echo $text['sort_order']; ?>" min="0">
                <small style="color: var(--text-light); margin-top: 0.5rem; display: block;">
                    Lower numbers appear first in rotation
                </small>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?php echo $text['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $text['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Banner Text</button>
            <a href="<?php echo Helper::url('admin/banner-texts'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

