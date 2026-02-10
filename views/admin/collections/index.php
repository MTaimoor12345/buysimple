<?php $pageTitle = 'Collections'; ?>
<div class="page-header">
    <h1>Collections</h1>
    <a href="<?php echo Helper::url('admin/collections/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Collection
    </a>
</div>

<?php if (empty($collections)): ?>
    <div class="empty-state">
        <p>No collections found. <a href="<?php echo Helper::url('admin/collections/create'); ?>">Create your first collection</a></p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Link</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($collections as $collection): ?>
                <tr>
                    <td><?php echo $collection['id']; ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($collection['image']); ?>" 
                             alt="<?php echo htmlspecialchars($collection['title']); ?>" 
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><?php echo htmlspecialchars($collection['title']); ?></td>
                    <td>
                        <?php if ($collection['link']): ?>
                            <a href="<?php echo htmlspecialchars($collection['link']); ?>" target="_blank">
                                <?php echo htmlspecialchars(substr($collection['link'], 0, 30)); ?>...
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">No link</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $collection['sort_order']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $collection['status'] == 'active' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($collection['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Helper::url('admin/collections/edit/' . $collection['id']); ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="<?php echo Helper::url('admin/collections/delete/' . $collection['id']); ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this collection?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

