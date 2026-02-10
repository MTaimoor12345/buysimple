<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'E-commerce Store'; ?></title>
    <link rel="stylesheet" href="<?php echo Helper::asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo Helper::asset('css/animations.css'); ?>">
    <link rel="stylesheet" href="<?php echo Helper::asset('css/quick-view.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Meta Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
            n.queue = []; t = b.createElement(e); t.async = !0;
            t.src = v; s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '933459505702573');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=933459505702573&ev=PageView&noscript=1" /></noscript>
    <!-- End Meta Pixel Code -->
</head>

<body>
    <!-- Top Banner -->
    <?php
    $bannerTextModel = new BannerText();
    $bannerTexts = $bannerTextModel->getAll();
    ?>
    <div class="top-banner">
        <div class="container">
            <p id="banner-text">
                <?php
                if (!empty($bannerTexts)) {
                    echo htmlspecialchars($bannerTexts[0]['text']);
                } else {
                    echo 'FLAT PKR 129 DELIVERY CHARGES';
                }
                ?>
            </p>
        </div>
    </div>
    <?php if (count($bannerTexts) > 1): ?>
        <script>
            // Banner texts rotation
            window.bannerTexts = <?php echo json_encode(array_column($bannerTexts, 'text')); ?>;
        </script>
    <?php endif; ?>

    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo Helper::url(); ?>">
                    <img src="<?php echo Helper::url('img/Bu2.png'); ?>" alt="Logo" class="navbar-logo"
                        onerror="this.onerror=null; this.src='<?php echo Helper::url('img/buylogoo.jpg'); ?>'; this.onerror=null; this.src='<?php echo Helper::url('img/buylogoo.jpeg'); ?>';">
                </a>
            </div>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-menu" id="navMenu">
                <a href="<?php echo Helper::url(); ?>">Home</a>
                <?php
                $categoryModel = new Category();
                $categories = $categoryModel->getAll();
                foreach ($categories as $category):
                    $subCategories = $categoryModel->getSubCategories($category['id']);
                    ?>
                    <div class="nav-dropdown">
                        <a href="<?php echo Helper::url('category/' . $category['slug']); ?>" class="nav-dropdown-toggle">
                            <?php echo htmlspecialchars($category['name']); ?>
                            <?php if (!empty($subCategories)): ?>
                                <i class="fas fa-chevron-down"></i>
                            <?php endif; ?>
                        </a>
                        <?php if (!empty($subCategories)): ?>
                            <div class="nav-dropdown-menu">
                                <?php foreach ($subCategories as $subCat): ?>
                                    <a href="<?php echo Helper::url('category/' . $category['slug'] . '/' . $subCat['slug']); ?>">
                                        <?php echo htmlspecialchars($subCat['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (Auth::check()): ?>
                    <a href="<?php echo Helper::url('orders'); ?>">My Orders</a>
                    <a href="<?php echo Helper::url('logout'); ?>">Logout</a>
                    <?php if (Auth::isAdmin()): ?>
                        <a href="<?php echo Helper::url('admin/dashboard'); ?>" class="admin-link">Admin</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo Helper::url('login'); ?>" title="Login">
                        <i class="fas fa-lock"></i>
                    </a>
                    <a href="<?php echo Helper::url('register'); ?>" class="btn-primary" title="Register">
                        <i class="fas fa-user"></i>
                    </a>
                <?php endif; ?>
                <div class="navbar-search-container">
                    <a href="javascript:void(0);" title="Search" class="search-icon-link" id="search-icon-toggle">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="search-dropdown" id="search-dropdown">
                        <form class="search-form-inline" id="search-form-inline">
                            <input type="text" name="search" placeholder="Search products..."
                                class="search-input-inline" id="search-input-inline" autocomplete="off">
                            <button type="submit" class="search-submit-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div class="search-results" id="search-results"></div>
                    </div>
                </div>
                <a href="<?php echo Helper::url('cart'); ?>" title="Cart" class="cart-icon-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php
                    $cartModel = new Cart();
                    echo $cartModel->getCount();
                    ?></span>
                </a>
            </div>
        </div>
    </nav>

    <?php if ($message = Session::flash('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($message = Session::flash('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <main class="main-content">