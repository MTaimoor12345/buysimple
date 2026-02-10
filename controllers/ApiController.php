<?php
class ApiController {
    public function product($id) {
        header('Content-Type: application/json');
        
        $productModel = new Product();
        $product = $productModel->getById($id);
        
        if ($product) {
            // Resolve main image and gallery URLs
            $imageUrl = Helper::productImageUrl($product['image'] ?? '');
            $galleryUrls = Helper::productGalleryUrls($product['gallery'] ?? '');
            
            $product['image_url'] = $imageUrl;
            $product['gallery_urls'] = $galleryUrls;
            
            // Get color-wise stock if product has colors
            if (!empty($product['colors'])) {
                $colorStockModel = new ProductColorStock();
                $colorStocks = $colorStockModel->getByProductId($product['id']);
                $colorStockMap = [];
                foreach ($colorStocks as $cs) {
                    $colorStockMap[$cs['color_name']] = (int)$cs['stock'];
                }
                $product['color_stock_map'] = $colorStockMap;
            }

            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
        exit;
    }
    
    public function subCategories() {
        header('Content-Type: application/json');
        
        $categoryId = $_GET['category_id'] ?? null;
        
        if (!$categoryId) {
            echo json_encode([
                'success' => false,
                'message' => 'Category ID is required'
            ]);
            exit;
        }
        
        $subCategoryModel = new SubCategory();
        $subCategories = $subCategoryModel->getByCategoryId($categoryId);
        
        echo json_encode([
            'success' => true,
            'subCategories' => $subCategories
        ]);
        exit;
    }
    
    public function productBySku() {
        header('Content-Type: application/json');
        
        $sku = $_GET['sku'] ?? null;
        
        if (!$sku) {
            echo json_encode([
                'success' => false,
                'message' => 'SKU is required'
            ]);
            exit;
        }
        
        $productModel = new Product();
        $product = $productModel->getBySku($sku);
        
        if ($product) {
            // Get color-wise stock if product has colors
            $colorStocks = [];
            $totalColorStock = 0;
            $hasColors = !empty($product['colors']);
            
            if ($hasColors) {
                $colorStockModel = new ProductColorStock();
                $colorStocksData = $colorStockModel->getByProductId($product['id']);
                foreach ($colorStocksData as $cs) {
                    $stock = (int)$cs['stock'];
                    $colorStocks[] = [
                        'color_name' => $cs['color_name'],
                        'stock' => $stock
                    ];
                    $totalColorStock += $stock;
                }
            }
            
            // If product has colors, use sum of color stocks as total stock
            // Otherwise use the general stock from products table
            if ($hasColors) {
                $product['total_stock'] = $totalColorStock;
            } else {
                $product['total_stock'] = (int)$product['stock'];
            }
            
            $product['color_stocks'] = $colorStocks;
            
            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
        exit;
    }
    
    public function colorStock() {
        header('Content-Type: application/json');
        
        $productId = $_GET['product_id'] ?? null;
        $colorName = $_GET['color_name'] ?? null;
        
        if (!$productId || !$colorName) {
            echo json_encode([
                'success' => false,
                'message' => 'Product ID and Color Name are required'
            ]);
            exit;
        }
        
        $colorStockModel = new ProductColorStock();
        $colorStock = $colorStockModel->getByProductAndColor($productId, $colorName);
        
        echo json_encode([
            'success' => true,
            'stock' => $colorStock ? (int)$colorStock['stock'] : null
        ]);
        exit;
    }
    
    public function cartCount() {
        header('Content-Type: application/json');
        
        $cartModel = new Cart();
        $count = $cartModel->getCount();
        
        echo json_encode([
            'success' => true,
            'count' => (int)$count
        ]);
        exit;
    }
    
    public function search() {
        header('Content-Type: application/json');
        
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            echo json_encode([
                'success' => true,
                'products' => []
            ]);
            exit;
        }
        
        $productModel = new Product();
        $products = $productModel->search($query);
        
        // Limit results to 10 for dropdown
        $products = array_slice($products, 0, 10);
        
        // Format products with image URLs
        foreach ($products as &$product) {
            $product['image_url'] = Helper::productImageUrl($product['image'] ?? '');
        }
        
        echo json_encode([
            'success' => true,
            'products' => $products
        ]);
        exit;
    }
}

