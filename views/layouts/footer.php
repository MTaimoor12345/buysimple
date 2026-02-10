    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section footer-brand">
                    <div class="footer-logo">
                        <img src="<?php echo Helper::url('img/buylogoo.png'); ?>" alt="Logo" class="footer-logo-img" style="display: block;" onerror="this.style.display='none';">
                    </div>
                    <p class="footer-tagline">Your trusted shopping destination</p>
                    <p class="footer-description">Discover quality products at unbeatable prices. We deliver excellence in every purchase, ensuring customer satisfaction and building lasting shopping experiences.</p>
                </div>
                <div class="footer-section">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo Helper::url(); ?>">Home</a></li>
                        <li><a href="<?php echo Helper::url('products'); ?>">Products</a></li>
                        <li><a href="<?php echo Helper::url('cart'); ?>">Cart</a></li>
                        <?php if (Auth::check()): ?>
                            <li><a href="<?php echo Helper::url('orders'); ?>">My Orders</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo Helper::url('login'); ?>">Login</a></li>
                            <li><a href="<?php echo Helper::url('register'); ?>">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section footer-contact">
                    <?php 
                    $siteSettings = new SiteSettings();
                    $whatsappNumber = $siteSettings->get('whatsapp_number', '923055666185');
                    $email = $siteSettings->get('contact_email', 'info@itservices.com');
                    ?>
                    <h4 class="footer-heading">Contact Info</h4>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <a href="https://wa.me/<?php echo htmlspecialchars($whatsappNumber); ?>" target="_blank">+<?php echo htmlspecialchars($whatsappNumber); ?></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> E-Shop. All BuySimple reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <?php 
    $whatsappIconEnabled = $siteSettings->get('whatsapp_icon_enabled', '1');
    if ($whatsappIconEnabled == '1'):
    ?>
    <a href="https://wa.me/<?php echo htmlspecialchars($whatsappNumber); ?>" target="_blank" class="whatsapp-float" title="Chat with us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>

    <script>
        // Set base URL for JavaScript
        window.APP_BASE_URL = '<?php echo Helper::url(""); ?>';
        // Cache-busting version for main.js to avoid old cached file
        window.APP_ASSET_VERSION = 'v3';
    </script>
    <script src="<?php echo Helper::asset('js/main.js'); ?>?v=3"></script>
    <script>
        // Navbar Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchIcon = document.getElementById('search-icon-toggle');
            const searchDropdown = document.getElementById('search-dropdown');
            const searchInput = document.getElementById('search-input-inline');
            const searchForm = document.getElementById('search-form-inline');
            const searchResults = document.getElementById('search-results');
            let searchTimeout = null;
            
            // Toggle search dropdown
            if (searchIcon) {
                searchIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    searchDropdown.classList.toggle('active');
                    if (searchDropdown.classList.contains('active')) {
                        searchInput.focus();
                    }
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (searchDropdown && searchIcon && 
                    !searchDropdown.contains(e.target) && 
                    !searchIcon.contains(e.target)) {
                    searchDropdown.classList.remove('active');
                }
            });
            
            // Search as user types
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    
                    clearTimeout(searchTimeout);
                    
                    if (query.length < 2) {
                        searchResults.innerHTML = '';
                        return;
                    }
                    
                    searchTimeout = setTimeout(function() {
                        performSearch(query);
                    }, 300);
                });
            }
            
            // Handle form submission
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query) {
                        window.location.href = '<?php echo Helper::url("products?search="); ?>' + encodeURIComponent(query);
                    }
                });
            }
            
            function performSearch(query) {
                searchResults.innerHTML = '<div class="search-loading">Searching...</div>';
                
                fetch('<?php echo Helper::url("api/search?q="); ?>' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.products.length > 0) {
                            let html = '';
                            data.products.forEach(function(product) {
                                const price = product.sale_price ? product.sale_price : product.price;
                                const formattedPrice = 'Rs. ' + parseFloat(price).toLocaleString('en-PK', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                const imageUrl = product.image_url || '<?php echo Helper::asset("images/placeholder.jpg"); ?>';
                                html += `
                                    <a href="<?php echo Helper::url("products/"); ?>${product.slug}" class="search-result-item">
                                        <img src="${imageUrl}" alt="${product.name}" onerror="this.src='<?php echo Helper::asset("images/placeholder.jpg"); ?>'">
                                        <div class="search-result-item-info">
                                            <h4>${product.name}</h4>
                                            <p>${product.short_description || ''}</p>
                                        </div>
                                        <div class="search-result-price">${formattedPrice}</div>
                                    </a>
                                `;
                            });
                            searchResults.innerHTML = html;
                        } else {
                            searchResults.innerHTML = '<div class="search-no-results">No products found</div>';
                        }
                    })
                    .catch(error => {
                        searchResults.innerHTML = '<div class="search-no-results">Error searching products</div>';
                    });
            }
        });
    </script>
</body>
</html>

