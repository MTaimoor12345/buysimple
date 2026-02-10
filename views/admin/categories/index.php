<?php $pageTitle = 'Categories & Sub-Categories'; ?>
<div class="page-actions">
    <a href="<?php echo Helper::url('admin/categories/create'); ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Category
    </a>
</div>

<div class="categories-admin">
    <?php if (empty($categories)): ?>
        <div class="alert alert-info">
            <p>No categories found. Add your first category!</p>
        </div>
    <?php else: ?>
        <?php foreach ($categories as $category): ?>
            <div class="category-card-admin">
                <div class="category-header">
                    <div class="category-info">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p class="category-slug">Slug: <?php echo htmlspecialchars($category['slug']); ?></p>
                        <span class="status-badge status-<?php echo $category['status'] ?? 'active'; ?>">
                            <?php echo ucfirst($category['status'] ?? 'active'); ?>
                        </span>
                    </div>
                    <div class="category-actions">
                        <a href="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/create'); ?>" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Add Sub-Category
                        </a>
                        <a href="<?php echo Helper::url('admin/categories/edit/' . $category['id']); ?>" 
                           class="btn btn-sm btn-primary">Edit</a>
                        <a href="<?php echo Helper::url('admin/categories/delete/' . $category['id']); ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure? This will delete all sub-categories too!')">Delete</a>
                    </div>
                </div>
                
                <?php if (!empty($category['sub_categories'])): ?>
                    <div class="sub-categories-list">
                        <h4>Sub-Categories:</h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($category['sub_categories'] as $subCat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subCat['name']); ?></td>
                                        <td><?php echo htmlspecialchars($subCat['slug']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $subCat['status']; ?>">
                                                <?php echo ucfirst($subCat['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $subCat['sort_order']; ?></td>
                                        <td>
                                            <a href="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/edit/' . $subCat['id']); ?>" 
                                               class="btn btn-sm btn-primary">Edit</a>
                                            <a href="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/delete/' . $subCat['id']); ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-sub-categories">
                        <p>No sub-categories yet. <a href="<?php echo Helper::url('admin/categories/' . $category['id'] . '/sub-categories/create'); ?>">Add one</a></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.categories-admin {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.category-card-admin {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.category-info h3 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.category-slug {
    color: var(--text-light);
    font-size: 0.875rem;
    margin: 0.25rem 0;
}

.category-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.sub-categories-list {
    margin-top: 1rem;
}

.sub-categories-list h4 {
    margin-bottom: 1rem;
    color: var(--dark-color);
    font-size: 1.1rem;
}

.no-sub-categories {
    padding: 1rem;
    background: var(--light-color);
    border-radius: 5px;
    text-align: center;
    color: var(--text-light);
}

.no-sub-categories a {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}
</style>

