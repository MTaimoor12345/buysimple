<?php $pageTitle = 'Banner Texts'; ?>
<div class="page-actions">
    <a href="<?php echo Helper::url('admin/banner-texts/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Banner Text
    </a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Text</th>
            <th>Sort Order</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($texts)): ?>
            <tr>
                <td colspan="5" class="text-center">No banner texts found. Add your first banner text!</td>
            </tr>
        <?php else: ?>
            <?php foreach ($texts as $text): ?>
                <tr>
                    <td><?php echo $text['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($text['text']); ?></strong></td>
                    <td><?php echo $text['sort_order']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $text['status']; ?>">
                            <?php echo ucfirst($text['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Helper::url('admin/banner-texts/edit/' . $text['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?php echo Helper::url('admin/banner-texts/delete/' . $text['id']); ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this banner text?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

