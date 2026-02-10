<?php
// Start session
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/controllers/admin/' . $class . '.php',
        __DIR__ . '/middleware/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

// Initialize session
Session::start();

// Initialize router
require __DIR__ . '/core/Router.php';
$router = new Router();

// Frontend Routes
$router->get('/', 'HomeController@index');
$router->get('/products', 'ProductController@index');
$router->get('/products/{slug}', 'ProductController@show');
$router->get('/category/{slug}', 'CategoryController@show');
$router->get('/category/{categorySlug}/{subCategorySlug}', 'CategoryController@showSubCategory');
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->get('/cart/remove/{id}', 'CartController@remove');
$router->get('/checkout', 'CheckoutController@index', ['AuthMiddleware']);
$router->post('/checkout/process', 'CheckoutController@process', ['AuthMiddleware']);
$router->get('/orders', 'OrderController@myOrders', ['AuthMiddleware']);
$router->get('/orders/{id}', 'OrderController@show', ['AuthMiddleware']);
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// API Routes
$router->get('/api/product/{id}', 'ApiController@product');
$router->get('/api/product-by-sku', 'ApiController@productBySku');
$router->get('/api/color-stock', 'ApiController@colorStock');
$router->get('/api/sub-categories', 'ApiController@subCategories');
$router->get('/api/cart-count', 'ApiController@cartCount');
$router->get('/api/search', 'ApiController@search');
$router->post('/api/upload/image', 'UploadController@image');

// Admin Routes
$router->get('/admin/dashboard', 'DashboardController@index', ['AdminMiddleware']);
$router->get('/admin/products', 'AdminProductController@index', ['AdminMiddleware']);
$router->get('/admin/products/create', 'AdminProductController@create', ['AdminMiddleware']);
$router->post('/admin/products/create', 'AdminProductController@create', ['AdminMiddleware']);
$router->get('/admin/products/edit/{id}', 'AdminProductController@edit', ['AdminMiddleware']);
$router->post('/admin/products/edit/{id}', 'AdminProductController@edit', ['AdminMiddleware']);
$router->get('/admin/products/stock', 'AdminProductController@stock', ['AdminMiddleware']);
$router->post('/admin/products/stock', 'AdminProductController@stock', ['AdminMiddleware']);
$router->post('/admin/products/update-stock', 'AdminProductController@updateStock', ['AdminMiddleware']);
$router->get('/admin/products/delete/{id}', 'AdminProductController@delete', ['AdminMiddleware']);
$router->get('/admin/hero-slides', 'AdminHeroSlideController@index', ['AdminMiddleware']);
$router->get('/admin/hero-slides/create', 'AdminHeroSlideController@create', ['AdminMiddleware']);
$router->post('/admin/hero-slides/create', 'AdminHeroSlideController@create', ['AdminMiddleware']);
$router->get('/admin/hero-slides/edit/{id}', 'AdminHeroSlideController@edit', ['AdminMiddleware']);
$router->post('/admin/hero-slides/edit/{id}', 'AdminHeroSlideController@edit', ['AdminMiddleware']);
$router->get('/admin/hero-slides/delete/{id}', 'AdminHeroSlideController@delete', ['AdminMiddleware']);
$router->get('/admin/collections', 'AdminCollectionController@index', ['AdminMiddleware']);
$router->get('/admin/collections/create', 'AdminCollectionController@create', ['AdminMiddleware']);
$router->post('/admin/collections/create', 'AdminCollectionController@create', ['AdminMiddleware']);
$router->get('/admin/collections/edit/{id}', 'AdminCollectionController@edit', ['AdminMiddleware']);
$router->post('/admin/collections/edit/{id}', 'AdminCollectionController@edit', ['AdminMiddleware']);
$router->get('/admin/collections/delete/{id}', 'AdminCollectionController@delete', ['AdminMiddleware']);
$router->get('/admin/banner-texts', 'AdminBannerTextController@index', ['AdminMiddleware']);
$router->get('/admin/banner-texts/create', 'AdminBannerTextController@create', ['AdminMiddleware']);
$router->post('/admin/banner-texts/create', 'AdminBannerTextController@create', ['AdminMiddleware']);
$router->get('/admin/banner-texts/edit/{id}', 'AdminBannerTextController@edit', ['AdminMiddleware']);
$router->post('/admin/banner-texts/edit/{id}', 'AdminBannerTextController@edit', ['AdminMiddleware']);
$router->get('/admin/banner-texts/delete/{id}', 'AdminBannerTextController@delete', ['AdminMiddleware']);
$router->get('/admin/site-settings', 'AdminSiteSettingsController@index', ['AdminMiddleware']);
$router->post('/admin/site-settings', 'AdminSiteSettingsController@index', ['AdminMiddleware']);
$router->get('/admin/categories', 'AdminCategoryController@index', ['AdminMiddleware']);
$router->get('/admin/categories/create', 'AdminCategoryController@create', ['AdminMiddleware']);
$router->post('/admin/categories/create', 'AdminCategoryController@create', ['AdminMiddleware']);
$router->get('/admin/categories/edit/{id}', 'AdminCategoryController@edit', ['AdminMiddleware']);
$router->post('/admin/categories/edit/{id}', 'AdminCategoryController@edit', ['AdminMiddleware']);
$router->get('/admin/categories/delete/{id}', 'AdminCategoryController@delete', ['AdminMiddleware']);
$router->get('/admin/categories/{categoryId}/sub-categories/create', 'AdminCategoryController@createSubCategory', ['AdminMiddleware']);
$router->post('/admin/categories/{categoryId}/sub-categories/create', 'AdminCategoryController@createSubCategory', ['AdminMiddleware']);
$router->get('/admin/categories/{categoryId}/sub-categories/edit/{subCategoryId}', 'AdminCategoryController@editSubCategory', ['AdminMiddleware']);
$router->post('/admin/categories/{categoryId}/sub-categories/edit/{subCategoryId}', 'AdminCategoryController@editSubCategory', ['AdminMiddleware']);
$router->get('/admin/categories/{categoryId}/sub-categories/delete/{subCategoryId}', 'AdminCategoryController@deleteSubCategory', ['AdminMiddleware']);
$router->get('/admin/orders', 'AdminOrderController@index', ['AdminMiddleware']);
$router->get('/admin/orders/download', 'AdminOrderController@download', ['AdminMiddleware']);
$router->post('/admin/orders/update-status', 'AdminOrderController@updateStatus', ['AdminMiddleware']);
$router->post('/admin/orders/update-payment-status', 'AdminOrderController@updatePaymentStatus', ['AdminMiddleware']);
$router->get('/admin/orders/delete/{id}', 'AdminOrderController@delete', ['AdminMiddleware']);
$router->get('/admin/orders/{id}', 'AdminOrderController@show', ['AdminMiddleware']);

// Dispatch the route
$router->dispatch();

