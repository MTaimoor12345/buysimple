<?php $pageTitle = 'Hero Slides'; ?>
<div class="page-actions">
    <a href="<?php echo Helper::url('admin/hero-slides/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Slide
    </a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Button Text</th>
            <th>Button Link</th>
            <th>Sort Order</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($slides)): ?>
            <tr>
                <td colspan="7" class="text-center">No slides found. Default images will be shown.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($slides as $slide): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($slide['image']); ?>" 
                             alt="<?php echo htmlspecialchars($slide['title'] ?? 'Slide'); ?>" 
                             style="width: 150px; height: 80px; object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><?php echo htmlspecialchars($slide['title'] ?? 'No Title'); ?></td>
                    <td><?php echo htmlspecialchars($slide['button_text']); ?></td>
                    <td><?php echo htmlspecialchars($slide['button_link']); ?></td>
                    <td><?php echo $slide['sort_order']; ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $slide['status']; ?>">
                            <?php echo ucfirst($slide['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Helper::url('admin/hero-slides/edit/' . $slide['id']); ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?php echo Helper::url('admin/hero-slides/delete/' . $slide['id']); ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this slide?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

