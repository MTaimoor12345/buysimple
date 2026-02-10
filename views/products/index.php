<div class="container">
    <div class="page-header">
        <h1>All Products</h1>
        <form method="GET" action="<?php echo Helper::url('products'); ?>" class="search-form">
            <input type="text" name="search" placeholder="Search products..."
                value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="products-layout">
        <aside class="sidebar">
            <h3>Categories</h3>
            <ul class="category-list">
                <li><a href="<?php echo Helper::url('products'); ?>"
                        class="<?php echo !$selectedCategory ? 'active' : ''; ?>">All Products</a></li>
                <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="<?php echo Helper::url('products?category=' . $category['id']); ?>"
                            class="<?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <div class="products-content">
            <div class="products-toolbar">
                <div class="results-count">
                    <span><?php echo count($products); ?> Products Found</span>
                </div>
                <div class="sort-options">
                    <label>Sort by:</label>
                    <select onchange="updateSort(this.value)" class="sort-select">
                        <option value="price-high" <?php echo ($currentSort ?? 'price-high') == 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="price-low" <?php echo ($currentSort ?? '') == 'price-low' ? 'selected' : ''; ?>>
                            Price: Low to High</option>
                        <option value="name" <?php echo ($currentSort ?? '') == 'name' ? 'selected' : ''; ?>>Name: A to Z
                        </option>
                        <option value="default" <?php echo ($currentSort ?? '') == 'default' ? 'selected' : ''; ?>>Default
                        </option>
                    </select>
                </div>
            </div>

            <script>
                function updateSort(sortValue) {
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('sort', sortValue);
                    window.location.search = urlParams.toString();
                }
            </script>

            <?php if (empty($products)): ?>
                <div class="empty-state animate-fade-in">
                    <i class="fas fa-box-open"></i>
                    <p>No products found</p>
                    <a href="<?php echo Helper::url('products'); ?>" class="btn btn-primary">View All Products</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php
                    $demoProductImages = [
                        'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop',
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
                    ];
                    $prodImgIndex = 0;
                    ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $prodImgSrc = Helper::productImageUrl($product['image'] ?? '') ?? $demoProductImages[$prodImgIndex % count($demoProductImages)];
                        $prodImgIndex++;

                        // Calculate discount percentage
                        $discountPercent = 0;
                        if ($product['sale_price'] && $product['price'] > 0) {
                            $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                        }

                        // Generate random rating (for demo)
                        $rating = 4.5 + (rand(0, 10) / 10);
                        ?>
                        <div class="product-card-modern hover-lift" data-category="<?php echo $product['category_id']; ?>"
                            data-product-id="<?php echo $product['id']; ?>" data-product='<?php echo json_encode($product); ?>'>
                            <div class="product-image-modern">
                                <?php if ($product['sale_price']): ?>
                                    <div class="price-was">WAS <?php echo number_format($product['price'], 0); ?> RS</div>
                                    <span class="badge-sale">Sale</span>
                                <?php endif; ?>
                                <a href="<?php echo Helper::url('products/' . $product['slug']); ?>">
                                    <img src="<?php echo $prodImgSrc; ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                </a>
                                <div class="product-overlay-modern">
                                    <button class="btn-quick-view-modern" onclick="quickView(<?php echo $product['id']; ?>)"
                                        title="Quick View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info-modern">
                                <div class="product-header-modern">
                                    <h3 class="product-name-modern">
                                        <a
                                            href="<?php echo Helper::url('products/' . $product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                    </h3>
                                    <?php if ($discountPercent > 0): ?>
                                        <span class="discount-badge"><?php echo $discountPercent; ?>% OFF</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($product['short_description']): ?>
                                    <p class="product-feature">
                                        <?php echo htmlspecialchars(substr($product['short_description'], 0, 50)); ?></p>
                                <?php endif; ?>

                                <div class="product-options-modern">
                                    <?php
                                    // Get product colors and filter by stock
                                    $productColors = [];
                                    if (!empty($product['colors'])) {
                                        $productColors = json_decode($product['colors'], true) ?: [];
                                    }

                                    // Filter colors that have stock
                                    $availableColors = [];
                                    if (!empty($productColors)) {
                                        $colorStockModel = new ProductColorStock();
                                        $colorStocks = $colorStockModel->getByProductId($product['id']);
                                        $colorStockMap = [];
                                        foreach ($colorStocks as $cs) {
                                            $colorStockMap[$cs['color_name']] = (int) $cs['stock'];
                                        }

                                        // Only include colors with stock > 0
                                        foreach ($productColors as $color) {
                                            $colorStock = $colorStockMap[$color['name']] ?? null;
                                            if ($colorStock !== null && $colorStock > 0) {
                                                $availableColors[] = $color;
                                            }
                                        }
                                    }

                                    if (!empty($availableColors)):
                                        ?>
                                        <div class="color-options">
                                            <?php
                                            $maxColorsToShow = 3;
                                            $colorsToShow = array_slice($availableColors, 0, $maxColorsToShow);
                                            $remainingColors = count($availableColors) - $maxColorsToShow;
                                            ?>
                                            <?php foreach ($colorsToShow as $index => $color): ?>
                                                <div class="color-swatch product-card-color-swatch <?php echo $index === 0 ? 'active' : ''; ?>"
                                                    data-color-name="<?php echo htmlspecialchars($color['name']); ?>"
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    style="background: <?php echo htmlspecialchars($color['code'] ?? '#000000'); ?>; cursor: pointer;"
                                                    title="<?php echo htmlspecialchars($color['name'] ?? ''); ?>">
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if ($remainingColors > 0): ?>
                                                <span class="more-colors">+<?php echo $remainingColors; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo number_format($rating, 2); ?></span>
                                    </div>
                                </div>

                                <div class="product-pricing-modern">
                                    <?php if ($product['sale_price']): ?>
                                        <span class="price-original"><?php echo Helper::formatPrice($product['price']); ?></span>
                                        <span
                                            class="price-current"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                                    <?php else: ?>
                                        <span class="price-current"><?php echo Helper::formatPrice($product['price']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php
                                // Calculate total stock for products with colors
                                $totalStock = (int) $product['stock'];
                                if (!empty($availableColors)) {
                                    $totalStock = 0;
                                    foreach ($availableColors as $color) {
                                        $colorStock = $colorStockMap[$color['name']] ?? null;
                                        if ($colorStock !== null && $colorStock > 0) {
                                            $totalStock += $colorStock;
                                        }
                                    }
                                }
                                ?>
                                <form method="POST" action="<?php echo Helper::url('cart/add'); ?>"
                                    class="add-to-cart-form-modern">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="color_name" class="product-card-color-input"
                                        value="<?php echo !empty($availableColors) ? htmlspecialchars($availableColors[0]['name']) : ''; ?>">
                                    <div class="product-card-buttons" style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                        <button type="submit" class="btn-add-to-cart"
                                            style="flex: 1; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;"
                                            <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                        <button type="submit" name="buy_now" value="1" class="btn-buy-now"
                                            style="flex: 1; padding: 0.5rem; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;"
                                            <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-bolt"></i> Buy Now
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>