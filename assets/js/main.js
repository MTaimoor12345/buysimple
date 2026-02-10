// Carousel Functionality
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.carousel-slide');
const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    if (index >= slides.length) currentSlideIndex = 0;
    if (index < 0) currentSlideIndex = slides.length - 1;
    
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    if (slides[currentSlideIndex]) {
        slides[currentSlideIndex].classList.add('active');
    }
    if (dots[currentSlideIndex]) {
        dots[currentSlideIndex].classList.add('active');
    }
}

function changeSlide(direction) {
    currentSlideIndex += direction;
    showSlide(currentSlideIndex);
}

function currentSlide(index) {
    currentSlideIndex = index - 1;
    showSlide(currentSlideIndex);
}

// Auto-play carousel
if (slides.length > 0) {
    setInterval(() => {
        currentSlideIndex++;
        showSlide(currentSlideIndex);
    }, 5000);
}

// Wishlist Functionality
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

function toggleWishlist(productId) {
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    
    if (wishlist.includes(productId)) {
        wishlist = wishlist.filter(id => id !== productId);
        icon.classList.remove('fas');
        icon.classList.add('far');
        btn.classList.remove('active');
        showToast('Removed from wishlist', 'info');
    } else {
        wishlist.push(productId);
        icon.classList.remove('far');
        icon.classList.add('fas');
        btn.classList.add('active');
        showToast('Added to wishlist!', 'success');
    }
    
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistCount();
}

function updateWishlistCount() {
    const count = wishlist.length;
    const badge = document.querySelector('.wishlist-count');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.style.display = 'inline-block';
            badge.classList.add('animate-bounce');
            setTimeout(() => badge.classList.remove('animate-bounce'), 1000);
        }
    }
}

// Initialize wishlist buttons
function initWishlist() {
    document.querySelectorAll('.btn-wishlist').forEach(btn => {
        const productId = parseInt(btn.getAttribute('onclick').match(/\d+/)[0]);
        if (wishlist.includes(productId)) {
            btn.classList.add('active');
            const icon = btn.querySelector('i');
            icon.classList.remove('far');
            icon.classList.add('fas');
        }
    });
}

// Get base URL for API calls
function getBaseUrl() {
    // Use global base URL if set
    if (typeof window.APP_BASE_URL !== 'undefined') {
        return window.APP_BASE_URL;
    }
    
    // Fallback: calculate from current path
    const path = window.location.pathname;
    const pathParts = path.split('/').filter(p => p);
    // Remove 'index.php' if present
    const baseParts = pathParts.filter(p => p !== 'index.php');
    return baseParts.length > 0 ? '/' + baseParts.join('/') + '/' : '/';
}

// Quick View Modal
function quickView(productId) {
    // Show loading
    showQuickViewModal(productId);
    
    // Get base URL
    const baseUrl = getBaseUrl();
    
    // Fetch product details via AJAX
    fetch(`${baseUrl}api/product/${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayQuickView(data.product);
            } else {
                showToast('Product not found', 'error');
                closeQuickView();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Fallback: try to get product from page data
            const productCard = document.querySelector(`[data-product-id="${productId}"]`);
            if (productCard) {
                try {
                    const productData = JSON.parse(productCard.dataset.product);
                    displayQuickView(productData);
                } catch(e) {
                    showToast('Failed to load product details', 'error');
                    closeQuickView();
                }
            } else {
                showToast('Failed to load product details', 'error');
                closeQuickView();
            }
        });
}

function showQuickViewModal(productId) {
    // Create modal if doesn't exist
    let modal = document.getElementById('quickViewModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'quickViewModal';
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="quick-view-overlay" onclick="closeQuickView()"></div>
            <div class="quick-view-content">
                <button class="quick-view-close" onclick="closeQuickView()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="quick-view-body">
                    <div class="quick-view-loading">
                        <div class="loading-spinner"></div>
                        <p>Loading product...</p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function displayQuickView(product) {
    const modalBody = document.querySelector('.quick-view-body');
    const mainImage = product.image_url || product.image || 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop';
    modalBody.innerHTML = `
        <div class="quick-view-grid">
            <div class="quick-view-image">
                <img src="${mainImage}" alt="${product.name}">
                ${product.sale_price ? '<span class="badge sale">Sale</span>' : ''}
                ${product.featured ? '<span class="badge featured">Featured</span>' : ''}
            </div>
            <div class="quick-view-info">
                <h2>${product.name}</h2>
                <p class="quick-view-category">${product.category_name || 'Uncategorized'}</p>
                <div class="quick-view-price">
                    ${product.sale_price ? 
                        `<span class="price-old">Rs. ${parseFloat(product.price).toLocaleString('en-PK', {minimumFractionDigits: 2})}</span>
                         <span class="price-new">Rs. ${parseFloat(product.sale_price).toLocaleString('en-PK', {minimumFractionDigits: 2})}</span>` :
                        `<span class="price">Rs. ${parseFloat(product.price).toLocaleString('en-PK', {minimumFractionDigits: 2})}</span>`
                    }
                </div>
                ${product.short_description ? `<p class="quick-view-desc">${product.short_description}</p>` : ''}
                ${(() => {
                    // Parse colors if they exist
                    let colors = [];
                    if (product.colors) {
                        try {
                            colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                            if (!Array.isArray(colors)) colors = [];
                        } catch(e) {
                            colors = [];
                        }
                    }
                    
                    // Filter colors that have stock > 0
                    const colorStockMap = product.color_stock_map || {};
                    const availableColors = colors.filter(color => {
                        const stock = colorStockMap[color.name];
                        return stock !== undefined && stock > 0;
                    });
                    
                    if (availableColors.length > 0) {
                        const maxColorsToShow = 5;
                        const colorsToShow = availableColors.slice(0, maxColorsToShow);
                        const remainingColors = availableColors.length - maxColorsToShow;
                        return `
                            <div class="quick-view-colors" style="margin: 1rem 0;">
                                <strong style="display: block; margin-bottom: 0.5rem;">Select Color:</strong>
                                <div class="color-options" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                                    ${colorsToShow.map((color, index) => `
                                        <div class="quick-view-color-swatch ${index === 0 ? 'active' : ''}" 
                                             data-color-name="${color.name || ''}"
                                             style="background: ${color.code || '#000000'}; width: 35px; height: 35px; border-radius: 50%; border: 3px solid ${index === 0 ? '#2563EB' : '#e2e8f0'}; cursor: pointer; transition: all 0.2s; position: relative;"
                                             title="${color.name || ''}">
                                        </div>
                                    `).join('')}
                                    ${remainingColors > 0 ? `<span style="color: var(--text-light); font-size: 0.9rem;">+${remainingColors}</span>` : ''}
                                </div>
                                <input type="hidden" name="color_name" id="quickViewSelectedColor" value="${availableColors[0] ? availableColors[0].name : ''}">
                            </div>
                        `;
                    }
                    return '';
                })()}
                <div class="quick-view-meta">
                    <p><strong>SKU:</strong> ${product.sku || 'N/A'}</p>
                    <p><strong>Stock:</strong> 
                        <span id="quickViewStockDisplay" class="in-stock">
                            ${(() => {
                                // Show stock for first available color, or total if no colors
                                let displayStock = parseInt(product.stock) || 0;
                                let hasColors = false;
                                
                                if (product.colors) {
                                    try {
                                        const colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                                        if (Array.isArray(colors) && colors.length > 0) {
                                            hasColors = true;
                                            const colorStockMap = product.color_stock_map || {};
                                            // Get first available color's stock
                                            for (let color of colors) {
                                                const stock = colorStockMap[color.name];
                                                if (stock !== undefined && stock > 0) {
                                                    displayStock = parseInt(stock) || 0;
                                                    break;
                                                }
                                            }
                                        }
                                    } catch(e) {
                                        // If parsing fails, use general stock
                                    }
                                }
                                
                                if (displayStock > 0) {
                                    return `In Stock (${displayStock})`;
                                } else {
                                    return `<span class="out-of-stock">Out of Stock</span>`;
                                }
                            })()}
                        </span>
                    </p>
                </div>
                <form class="quick-view-form" id="quickViewForm" onsubmit="addToCartFromQuickView(event, ${product.id}, ${(() => {
                    // Calculate total stock for form submission
                    let totalStock = parseInt(product.stock) || 0;
                    if (product.colors) {
                        try {
                            const colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                            if (Array.isArray(colors) && colors.length > 0) {
                                const colorStockMap = product.color_stock_map || {};
                                totalStock = 0;
                                colors.forEach(color => {
                                    const stock = colorStockMap[color.name];
                                    if (stock !== undefined && stock > 0) {
                                        totalStock += parseInt(stock) || 0;
                                    }
                                });
                            }
                        } catch(e) {}
                    }
                    return totalStock;
                })()})">
                    <div class="quantity-selector">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" id="quickViewQty" value="1" min="1" max="${(() => {
                            // Get stock for first available color, or general stock
                            let maxStock = parseInt(product.stock) || 0;
                            if (product.colors) {
                                try {
                                    const colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                                    if (Array.isArray(colors) && colors.length > 0) {
                                        const colorStockMap = product.color_stock_map || {};
                                        // Get first available color's stock
                                        for (let color of colors) {
                                            const stock = colorStockMap[color.name];
                                            if (stock !== undefined && stock > 0) {
                                                maxStock = parseInt(stock) || 0;
                                                break;
                                            }
                                        }
                                    }
                                } catch(e) {}
                            }
                            return maxStock > 0 ? maxStock : 1;
                        })()}" required>
                    </div>
                    <div class="quick-view-actions">
                        <button type="submit" id="quickViewAddToCartBtn" class="btn btn-primary btn-large" ${(() => {
                            // Get stock for first available color, or general stock
                            let stock = parseInt(product.stock) || 0;
                            if (product.colors) {
                                try {
                                    const colors = typeof product.colors === 'string' ? JSON.parse(product.colors) : product.colors;
                                    if (Array.isArray(colors) && colors.length > 0) {
                                        const colorStockMap = product.color_stock_map || {};
                                        // Get first available color's stock
                                        for (let color of colors) {
                                            const stockVal = colorStockMap[color.name];
                                            if (stockVal !== undefined && stockVal > 0) {
                                                stock = parseInt(stockVal) || 0;
                                                break;
                                            }
                                        }
                                    }
                                } catch(e) {}
                            }
                            return stock == 0 ? 'disabled' : '';
                        })()}>
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <a href="${product.slug ? getBaseUrl() + 'products/' + product.slug : '#'}" class="btn btn-secondary">
                            View Full Details
                        </a>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    // Animate in
    modalBody.classList.add('animate-fade-in');
    
    // Store product data for color selection
    const productData = product;
    
    // Add color selection handlers
    setTimeout(() => {
        const colorSwatches = document.querySelectorAll('.quick-view-color-swatch');
        const selectedColorInput = document.getElementById('quickViewSelectedColor');
        const stockDisplay = document.getElementById('quickViewStockDisplay');
        const quantityInput = document.getElementById('quickViewQty');
        const addToCartBtn = document.getElementById('quickViewAddToCartBtn');
        
        if (colorSwatches.length > 0 && selectedColorInput) {
            // Function to update stock display based on selected color
            function updateStockForColor(colorName) {
                if (!productData.colors || !colorName) {
                    return;
                }
                
                try {
                    const colors = typeof productData.colors === 'string' ? JSON.parse(productData.colors) : productData.colors;
                    const colorStockMap = productData.color_stock_map || {};
                    const stock = colorStockMap[colorName];
                    
                    if (stock !== undefined) {
                        const stockValue = parseInt(stock) || 0;
                        
                        // Update stock display
                        if (stockDisplay) {
                            if (stockValue > 0) {
                                stockDisplay.innerHTML = `In Stock (${stockValue})`;
                                stockDisplay.className = 'in-stock';
                            } else {
                                stockDisplay.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
                                stockDisplay.className = 'out-of-stock';
                            }
                        }
                        
                        // Update quantity input max
                        if (quantityInput) {
                            quantityInput.max = stockValue > 0 ? stockValue : 1;
                            if (parseInt(quantityInput.value) > stockValue) {
                                quantityInput.value = stockValue > 0 ? stockValue : 1;
                            }
                        }
                        
                        // Update button disabled state
                        if (addToCartBtn) {
                            if (stockValue > 0) {
                                addToCartBtn.disabled = false;
                            } else {
                                addToCartBtn.disabled = true;
                            }
                        }
                    }
                } catch(e) {
                    console.error('Error updating stock:', e);
                }
            }
            
            colorSwatches.forEach(swatch => {
                swatch.addEventListener('click', function() {
                    // Remove active class from all swatches
                    colorSwatches.forEach(s => {
                        s.classList.remove('active');
                        s.style.borderColor = '#e2e8f0';
                        s.style.boxShadow = 'none';
                    });
                    
                    // Add active class to clicked swatch
                    this.classList.add('active');
                    this.style.borderColor = '#2563EB';
                    this.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
                    
                    // Update hidden input
                    const colorName = this.getAttribute('data-color-name');
                    if (selectedColorInput) {
                        selectedColorInput.value = colorName || '';
                    }
                    
                    // Update stock display for selected color
                    updateStockForColor(colorName);
                });
            });
            
            // Set first color as active on load
            if (colorSwatches.length > 0) {
                const firstSwatch = colorSwatches[0];
                const firstColorName = firstSwatch.getAttribute('data-color-name');
                firstSwatch.classList.add('active');
                firstSwatch.style.borderColor = '#2563EB';
                firstSwatch.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
                
                // Update stock for first color
                if (firstColorName) {
                    updateStockForColor(firstColorName);
                }
            }
        }
    }, 100);
}

function addToCartFromQuickView(event, productId, stock) {
    event.preventDefault();
    const quantity = parseInt(document.getElementById('quickViewQty').value) || 1;
    
    // Get selected color if available
    const selectedColorInput = document.getElementById('quickViewSelectedColor');
    const colorName = selectedColorInput ? selectedColorInput.value : null;
    
    if (quantity > stock) {
        showToast(`Maximum quantity is ${stock}`, 'error');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    if (colorName) {
        formData.append('color_name', colorName);
    }
    
    // Get base URL
    const baseUrl = getBaseUrl();
    
    // Submit to cart
    fetch(`${baseUrl}cart/add`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok || response.redirected) {
            showToast('Product added to cart!', 'success');
            closeQuickView();
            // Update cart count
            updateCartCount();
            // Reload page after a short delay to update cart
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast('Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback: submit via form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${baseUrl}cart/add`;
        let formHTML = `
            <input type="hidden" name="product_id" value="${productId}">
            <input type="hidden" name="quantity" value="${quantity}">
        `;
        if (colorName) {
            formHTML += `<input type="hidden" name="color_name" value="${colorName}">`;
        }
        form.innerHTML = formHTML;
        document.body.appendChild(form);
        form.submit();
    });
}

function closeQuickView() {
    const modal = document.getElementById('quickViewModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function updateCartCount() {
    // Fetch updated cart count from API
    const baseUrl = getBaseUrl();
    const cartCount = document.querySelector('.cart-count');
    
    if (cartCount) {
        fetch(`${baseUrl}api/cart-count`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartCount.textContent = data.count;
                    cartCount.classList.add('updated');
                    setTimeout(() => cartCount.classList.remove('updated'), 500);
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
                // Fallback: increment counter
                let count = parseInt(cartCount.textContent) || 0;
                count++;
                cartCount.textContent = count;
                cartCount.classList.add('updated');
                setTimeout(() => cartCount.classList.remove('updated'), 500);
            });
    }
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Handle color selection on product cards
    const colorSwatches = document.querySelectorAll('.product-card-color-swatch');
    colorSwatches.forEach(swatch => {
        swatch.addEventListener('click', function(e) {
            e.stopPropagation();
            const colorName = this.getAttribute('data-color-name');
            const productId = this.getAttribute('data-product-id');
            
            // Find the product card form
            const productCard = this.closest('.product-card-modern');
            if (productCard) {
                const form = productCard.querySelector('.add-to-cart-form-modern');
                const colorInput = form ? form.querySelector('.product-card-color-input') : null;
                
                // Update hidden color input
                if (colorInput) {
                    colorInput.value = colorName || '';
                }
                
                // Update active state for all swatches in this product card
                const allSwatches = productCard.querySelectorAll('.product-card-color-swatch');
                allSwatches.forEach(s => {
                    s.classList.remove('active');
                    s.style.borderColor = '#e2e8f0';
                    s.style.boxShadow = 'none';
                });
                
                // Add active state to clicked swatch
                this.classList.add('active');
                this.style.borderColor = '#2563EB';
                this.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
            }
        });
    });
    
    // Set first color as active on load for each product card
    document.querySelectorAll('.product-card-modern').forEach(card => {
        const firstSwatch = card.querySelector('.product-card-color-swatch');
        if (firstSwatch && !firstSwatch.classList.contains('active')) {
            firstSwatch.classList.add('active');
            firstSwatch.style.borderColor = '#2563EB';
            firstSwatch.style.boxShadow = '0 0 0 2px rgba(37, 99, 235, 0.2)';
        }
    });
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickView();
    }
});

// Toast Notification System
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Smooth Scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add to Cart with Animation
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('button');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        btn.disabled = true;
        
        // Simulate loading (remove in production)
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-check"></i> Added!';
            btn.classList.add('animate-bounce');
            
            // Update cart count
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                let count = parseInt(cartCount.textContent) || 0;
                count++;
                cartCount.textContent = count;
                cartCount.classList.add('updated');
                setTimeout(() => cartCount.classList.remove('updated'), 500);
            }
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.classList.remove('animate-bounce');
            }, 1500);
        }, 500);
    });
});

// Product Filter and Sort
function filterProducts(category) {
    const products = document.querySelectorAll('.product-card');
    products.forEach(product => {
        if (category === 'all' || product.dataset.category === category) {
            product.style.display = 'block';
            product.classList.add('animate-fade-in');
        } else {
            product.style.display = 'none';
        }
    });
}

function sortProducts(sortBy) {
    const productGrid = document.querySelector('.product-grid');
    const products = Array.from(productGrid.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        if (sortBy === 'price-low') {
            const priceA = parseFloat(a.querySelector('.price, .price-new').textContent.replace(/[^0-9.]/g, ''));
            const priceB = parseFloat(b.querySelector('.price, .price-new').textContent.replace(/[^0-9.]/g, ''));
            return priceA - priceB;
        } else if (sortBy === 'price-high') {
            const priceA = parseFloat(a.querySelector('.price, .price-new').textContent.replace(/[^0-9.]/g, ''));
            const priceB = parseFloat(b.querySelector('.price, .price-new').textContent.replace(/[^0-9.]/g, ''));
            return priceB - priceA;
        } else if (sortBy === 'name') {
            const nameA = a.querySelector('h3').textContent;
            const nameB = b.querySelector('h3').textContent;
            return nameA.localeCompare(nameB);
        }
        return 0;
    });
    
    products.forEach(product => productGrid.appendChild(product));
}

// Image Lazy Loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('animate-fade-in');
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Scroll to Top Button
function createScrollToTop() {
    const btn = document.createElement('button');
    btn.className = 'fab';
    btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    btn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    document.body.appendChild(btn);
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            btn.style.display = 'flex';
            btn.classList.add('animate-fade-in');
        } else {
            btn.style.display = 'none';
        }
    });
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                    field.classList.add('animate-shake');
                    setTimeout(() => field.classList.remove('animate-shake'), 500);
                } else {
                    field.style.borderColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                showToast('Please fill in all required fields', 'error');
            }
        });
    });

    // Quantity input validation
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const max = parseInt(this.getAttribute('max'));
            const min = parseInt(this.getAttribute('min'));
            let value = parseInt(this.value);

            if (value > max) {
                this.value = max;
                showToast(`Maximum quantity is ${max}`, 'info');
            } else if (value < min) {
                this.value = min;
            }
        });
    });
    
    // Initialize features
    initWishlist();
    updateWishlistCount();
    createScrollToTop();

    // Initialize product gallery on product detail page
    initProductGallery();
    
    // Add stagger animation to product cards (old class support)
    const productCards = document.querySelectorAll('.product-card, .product-card-modern');
    productCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});

function initProductGallery() {
    const mainImage = document.getElementById('mainProductImage');
    if (!mainImage) return;

    const thumbs = document.querySelectorAll('.product-thumb-image');
    if (!thumbs.length) return;

    thumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            const src = thumb.getAttribute('data-large-src') || thumb.getAttribute('src');
            if (src) {
                mainImage.src = src;
            }
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        });
    });
}

// Banner Text Rotation (every 2 seconds)
if (typeof window.bannerTexts !== 'undefined' && window.bannerTexts.length > 1) {
    let currentBannerIndex = 0;
    const bannerTextElement = document.getElementById('banner-text');
    
    if (bannerTextElement) {
        setInterval(() => {
            currentBannerIndex = (currentBannerIndex + 1) % window.bannerTexts.length;
            bannerTextElement.style.opacity = '0';
            
            setTimeout(() => {
                bannerTextElement.textContent = window.bannerTexts[currentBannerIndex];
                bannerTextElement.style.opacity = '1';
            }, 300);
        }, 2000); // Change every 2 seconds
    }
}

// Mobile Menu Toggle
function toggleMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const toggleBtn = document.querySelector('.mobile-menu-toggle');
    
    if (navMenu) {
        navMenu.classList.toggle('active');
        
        // Change icon
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const navMenu = document.getElementById('navMenu');
    const toggleBtn = document.querySelector('.mobile-menu-toggle');
    const navbar = document.querySelector('.navbar');
    
    if (navMenu && navbar && !navbar.contains(event.target) && navMenu.classList.contains('active')) {
        navMenu.classList.remove('active');
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }
});

