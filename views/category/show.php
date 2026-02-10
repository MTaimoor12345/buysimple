<!-- Category Hero Section -->
<?php if (!empty($category['image'])): ?>
<section class="category-hero">
    <div class="category-hero-image">
        <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" loading="lazy">
    </div>
</section>
<?php endif; ?>

<div class="container">
    <!-- Category Name Heading -->
    <div class="category-heading-section">
        <h1 class="category-heading">
            <?php if (isset($subCategory)): ?>
                <?php echo htmlspecialchars($subCategory['name']); ?>
            <?php else: ?>
                <?php echo htmlspecialchars($category['name']); ?>
            <?php endif; ?>
        </h1>
    </div>

    <!-- Category/Sub-Category Description -->
    <?php if (isset($subCategory) && !empty($subCategory['description'])): ?>
        <div class="category-description-section">
            <p class="category-description"><?php echo htmlspecialchars($subCategory['description']); ?></p>
        </div>
    <?php elseif (!isset($subCategory) && !empty($category['description'])): ?>
        <div class="category-description-section">
            <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>
        </div>
    <?php endif; ?>

    <!-- Products Section -->
    <?php if (!empty($products)): ?>
        <div class="products-section">
            <div class="product-grid">
                <?php 
                $demoProductImages = [
                    'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
                    'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop',
                ];
                $prodImgIndex = 0;
                foreach ($products as $product): 
                    $prodImgSrc = Helper::productImageUrl($product['image'] ?? '') ?? $demoProductImages[$prodImgIndex % count($demoProductImages)];
                    $prodImgIndex++;
                    
                    // Calculate discount percentage
                    $discountPercent = 0;
                    if ($product['sale_price'] && $product['price'] > 0) {
                        $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                    }
                    
                    // Generate random rating (for demo)
                    $rating = 4.5 + (rand(0, 10) / 10);
                    
                    // Get product colors and filter by stock
                    $productColors = [];
                    if (!empty($product['colors'])) {
                        $productColors = json_decode($product['colors'], true) ?: [];
                    }
                    
                    // Filter colors that have stock
                    $availableColors = [];
                    $colorStockMap = [];
                    if (!empty($productColors)) {
                        $colorStockModel = new ProductColorStock();
                        $colorStocks = $colorStockModel->getByProductId($product['id']);
                        foreach ($colorStocks as $cs) {
                            $colorStockMap[$cs['color_name']] = (int)$cs['stock'];
                        }
                        
                        // Only include colors with stock > 0
                        foreach ($productColors as $color) {
                            $colorStock = $colorStockMap[$color['name']] ?? null;
                            if ($colorStock !== null && $colorStock > 0) {
                                $availableColors[] = $color;
                            }
                        }
                    }
                    
                    // Calculate total stock
                    $totalStock = (int)$product['stock'];
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
                    <div class="product-card-modern hover-lift" data-category="<?php echo $product['category_id']; ?>" data-product-id="<?php echo $product['id']; ?>" data-product='<?php echo json_encode($product); ?>'>
                        <div class="product-image-modern">
                            <?php if ($product['sale_price']): ?>
                                <div class="price-was">WAS <?php echo number_format($product['price'], 0); ?> RS</div>
                                <span class="badge-sale">Sale</span>
                            <?php endif; ?>
                            <a href="<?php echo Helper::url('products/' . $product['slug']); ?>">
                                <img src="<?php echo $prodImgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                            </a>
                            <div class="product-overlay-modern">
                                <button class="btn-quick-view-modern" onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info-modern">
                            <div class="product-header-modern">
                                <h3 class="product-name-modern">
                                    <a href="<?php echo Helper::url('products/' . $product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                </h3>
                                <?php if ($discountPercent > 0): ?>
                                    <span class="discount-badge"><?php echo $discountPercent; ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($product['short_description']): ?>
                                <p class="product-feature"><?php echo htmlspecialchars(substr($product['short_description'], 0, 50)); ?></p>
                            <?php endif; ?>
                            
                            <div class="product-options-modern">
                                <?php if (!empty($availableColors)): ?>
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
                                    <span class="price-current"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                                <?php else: ?>
                                    <span class="price-current"><?php echo Helper::formatPrice($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <form method="POST" action="<?php echo Helper::url('cart/add'); ?>" class="add-to-cart-form-modern">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="color_name" class="product-card-color-input" value="<?php echo !empty($availableColors) ? htmlspecialchars($availableColors[0]['name']) : ''; ?>">
                                <div class="product-card-buttons" style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                    <button type="submit" class="btn-add-to-cart" style="flex: 1; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;" <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-shopping-cart"></i> Add to Cart
                                    </button>
                                    <button type="submit" name="buy_now" value="1" class="btn-buy-now" style="flex: 1; padding: 0.5rem; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;" <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-bolt"></i> Buy Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif (isset($subCategory)): ?>
        <div class="no-products">
            <p>No products found in this sub-category.</p>
        </div>
    <?php endif; ?>

    <!-- Sub-Categories with Products (Only show on main category page, not sub-category page) -->
    <?php if (!isset($subCategory) && !empty($subCategoriesWithProducts)): ?>
        <?php foreach ($subCategoriesWithProducts as $item): 
            $subCat = $item['subCategory'];
            $subCatProducts = $item['products'];
        ?>
            <div class="sub-category-section">
                <h2 class="sub-category-heading"><?php echo htmlspecialchars($subCat['name']); ?></h2>
                <?php if (!empty($subCat['description'])): ?>
                    <p class="sub-category-description"><?php echo htmlspecialchars($subCat['description']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($subCatProducts)): ?>
                    <div class="product-grid">
                        <?php 
                        $demoProductImages = [
                            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
                            'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop',
                        ];
                        $prodImgIndex = 0;
                        foreach ($subCatProducts as $product): 
                            $prodImgSrc = Helper::productImageUrl($product['image'] ?? '') ?? $demoProductImages[$prodImgIndex % count($demoProductImages)];
                            $prodImgIndex++;
                            
                            // Calculate discount percentage
                            $discountPercent = 0;
                            if ($product['sale_price'] && $product['price'] > 0) {
                                $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                            }
                            
                            // Generate random rating (for demo)
                            $rating = 4.5 + (rand(0, 10) / 10);
                            
                            // Get product colors and filter by stock
                            $productColors = [];
                            if (!empty($product['colors'])) {
                                $productColors = json_decode($product['colors'], true) ?: [];
                            }
                            
                            // Filter colors that have stock
                            $availableColors = [];
                            $colorStockMap = [];
                            if (!empty($productColors)) {
                                $colorStockModel = new ProductColorStock();
                                $colorStocks = $colorStockModel->getByProductId($product['id']);
                                foreach ($colorStocks as $cs) {
                                    $colorStockMap[$cs['color_name']] = (int)$cs['stock'];
                                }
                                
                                // Only include colors with stock > 0
                                foreach ($productColors as $color) {
                                    $colorStock = $colorStockMap[$color['name']] ?? null;
                                    if ($colorStock !== null && $colorStock > 0) {
                                        $availableColors[] = $color;
                                    }
                                }
                            }
                            
                            // Calculate total stock
                            $totalStock = (int)$product['stock'];
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
                            <div class="product-card-modern hover-lift" data-category="<?php echo $product['category_id']; ?>" data-product-id="<?php echo $product['id']; ?>" data-product='<?php echo json_encode($product); ?>'>
                                <div class="product-image-modern">
                                    <?php if ($product['sale_price']): ?>
                                        <div class="price-was">WAS <?php echo number_format($product['price'], 0); ?> RS</div>
                                        <span class="badge-sale">Sale</span>
                                    <?php endif; ?>
                                    <a href="<?php echo Helper::url('products/' . $product['slug']); ?>">
                                        <img src="<?php echo $prodImgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                    </a>
                                    <div class="product-overlay-modern">
                                        <button class="btn-quick-view-modern" onclick="quickView(<?php echo $product['id']; ?>)" title="Quick View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="product-info-modern">
                                    <div class="product-header-modern">
                                        <h3 class="product-name-modern">
                                            <a href="<?php echo Helper::url('products/' . $product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                        </h3>
                                        <?php if ($discountPercent > 0): ?>
                                            <span class="discount-badge"><?php echo $discountPercent; ?>% OFF</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($product['short_description']): ?>
                                        <p class="product-feature"><?php echo htmlspecialchars(substr($product['short_description'], 0, 50)); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="product-options-modern">
                                        <?php if (!empty($availableColors)): ?>
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
                                            <span class="price-current"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                                        <?php else: ?>
                                            <span class="price-current"><?php echo Helper::formatPrice($product['price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <form method="POST" action="<?php echo Helper::url('cart/add'); ?>" class="add-to-cart-form-modern">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="color_name" class="product-card-color-input" value="<?php echo !empty($availableColors) ? htmlspecialchars($availableColors[0]['name']) : ''; ?>">
                                        <div class="product-card-buttons" style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                            <button type="submit" class="btn-add-to-cart" style="flex: 1; padding: 0.5rem; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;" <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
                                                <i class="fas fa-shopping-cart"></i> Add to Cart
                                            </button>
                                            <button type="submit" name="buy_now" value="1" class="btn-buy-now" style="flex: 1; padding: 0.5rem; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem; transition: all 0.3s;" <?php echo $totalStock == 0 ? 'disabled' : ''; ?>>
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
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
/* Category Hero Section */
.category-hero {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
    margin-bottom: 3rem;
}

.category-hero-image {
    position: relative;
    width: 100%;
    height: 100%;
}

.category-hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Category Heading Section */
.category-heading-section {
    text-align: center;
    margin: 3rem 0 0.25rem 0;
    padding: 2rem 0 0 0;
}

.category-heading {
    font-size: 3rem;
    font-weight: bold;
    color: #2563EB;
    margin: 0;
    text-align: center;
}

/* Category Description Section */
.category-description-section {
    text-align: center;
    margin: 0 0 3rem 0;
    padding: 0 2rem;
}

.category-description {
    font-size: 1.2rem;
    color: var(--text-color);
    line-height: 1.8;
    max-width: 800px;
    margin: 0 auto;
}

/* Section Headings */
.section-heading {
    font-size: 2rem;
    font-weight: bold;
    color: #2563EB;
    text-align: center;
    margin: 3rem 0 2rem 0;
    padding-bottom: 1rem;
    border-bottom: 2px solid #2563EB;
}

/* Products Section */
.products-section {
    margin: 3rem 0;
}

/* Sub-Category Section */
.sub-category-section {
    margin: 4rem 0;
    padding: 2rem 0;
}

.sub-category-heading {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2563EB;
    text-align: center;
    margin: 0 0 0.25rem 0;
}

.sub-category-description {
    text-align: center;
    font-size: 1.1rem;
    color: var(--text-color);
    line-height: 1.8;
    max-width: 800px;
    margin: 0 auto 2rem auto;
    padding: 0 2rem;
}

.sub-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.sub-category-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.3s;
}

.sub-category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    color: var(--primary-color);
}

.sub-category-card h4 {
    margin: 0 0 0.5rem 0;
    color: var(--dark-color);
}

.sub-category-card p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--text-light);
}

.products-section {
    margin-top: 2rem;
}

.no-products {
    text-align: center;
    padding: 3rem;
    color: var(--text-light);
}
</style>

