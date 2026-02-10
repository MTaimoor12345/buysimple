<!-- Hero Carousel -->
<section class="hero-carousel">
    <div class="carousel-container">
        <?php if (!empty($heroSlides)): ?>
            <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="carousel-slide <?php echo $index === 0 ? 'active animate-fade-in' : ''; ?>">
                    <div class="carousel-image-full">
                        <?php if (!empty($slide['image_link'])): ?>
                            <a href="<?php echo htmlspecialchars($slide['image_link']); ?>" class="carousel-image-link">
                                <img src="<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? 'Hero Slide'); ?>" loading="lazy">
                                <div class="carousel-overlay"></div>
                            </a>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? 'Hero Slide'); ?>" loading="lazy">
                            <div class="carousel-overlay"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Default slides if no admin slides -->
            <div class="carousel-slide active animate-fade-in">
                <div class="carousel-image-full">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&h=800&fit=crop" alt="Shopping" loading="lazy">
                    <div class="carousel-overlay"></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Collection Section -->
<?php if (!empty($collections)): ?>
<section class="collection-section">
    <div class="container">
        <div class="collection-grid">
            <?php 
            $cardClasses = ['collection-card-men', 'collection-card-women', 'collection-card-couple'];
            $cardIndex = 0;
            ?>
            <?php foreach ($collections as $collection): ?>
                <div class="collection-card <?php echo $cardClasses[$cardIndex % count($cardClasses)]; ?>">
                    <h3 class="collection-title"><?php echo htmlspecialchars($collection['title']); ?></h3>
                    <div class="collection-image-wrapper">
                        <?php if (!empty($collection['link'])): ?>
                            <a href="<?php echo htmlspecialchars($collection['link']); ?>" class="collection-image-link">
                                <img src="<?php echo htmlspecialchars($collection['image']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" loading="lazy">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($collection['image']); ?>" alt="<?php echo htmlspecialchars($collection['title']); ?>" loading="lazy">
                        <?php endif; ?>
                    </div>
                </div>
                <?php $cardIndex++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->


<!-- <section class="categories">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            <?php 
            $categoryIcons = [
                'fas fa-mobile-alt',
                'fas fa-tshirt',
                'fas fa-home',
                'fas fa-dumbbell',
                'fas fa-book'
            ];
            $categoryImages = [
                'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=300&h=200&fit=crop',
                'https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=300&h=200&fit=crop',
                'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=300&h=200&fit=crop',
                'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=300&h=200&fit=crop',
                'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=300&h=200&fit=crop'
            ];
            $catIndex = 0;
            ?>
            <?php foreach ($categories as $category): ?>
                <a href="<?php echo Helper::url('products?category=' . $category['id']); ?>" class="category-card">
                    <div class="category-image">
                        <img src="<?php echo $categoryImages[$catIndex % count($categoryImages)]; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" loading="lazy">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-icon">
                        <i class="<?php echo $categoryIcons[$catIndex % count($categoryIcons)]; ?>"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                    <?php $catIndex++; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section> -->

<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="product-grid">
            <?php 
            $demoImages = [
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop',
                'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
            ];
            $imageIndex = 0;
            ?>
            <?php foreach ($featuredProducts as $product): ?>
                <?php 
                $imgSrc = Helper::productImageUrl($product['image'] ?? '') ?? $demoImages[$imageIndex % count($demoImages)];
                $imageIndex++;
                
                // Calculate discount percentage
                $discountPercent = 0;
                if ($product['sale_price'] && $product['price'] > 0) {
                    $discountPercent = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                }
                
                // Generate random rating (for demo)
                $rating = 4.5 + (rand(0, 10) / 10);
                ?>
                <div class="product-card-modern" data-product-id="<?php echo $product['id']; ?>" data-product='<?php echo json_encode($product); ?>'>
                    <div class="product-image-modern">
                        <?php if ($product['sale_price']): ?>
                            <div class="price-was">WAS <?php echo number_format($product['price'], 0); ?> RS</div>
                            <span class="badge-sale">Sale</span>
                        <?php endif; ?>
                        <a href="<?php echo Helper::url('products/' . $product['slug']); ?>">
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
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
                                <span class="price-current"><?php echo Helper::formatPrice($product['sale_price']); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo Helper::formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                                <?php 
                                // Calculate total stock for products with colors
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
</section>

<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-item animate-fade-in-up">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Free Shipping</h3>
                <p>On orders over Rs. 5000</p>
            </div>
            <div class="feature-item animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="feature-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3>Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
            <div class="feature-item animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Payment</h3>
                <p>100% secure transactions</p>
            </div>
            <div class="feature-item animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>We're here to help</p>
            </div>
        </div>
    </div>
</section>

