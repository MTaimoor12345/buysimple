<?php
class ProductController
{
    public function index()
    {
        $productModel = new Product();
        $categoryModel = new Category();

        $categoryId = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $sort = $_GET['sort'] ?? 'price-high'; // Default sort: Price High to Low

        $products = [];

        if ($search) {
            $products = $productModel->search($search);
        } elseif ($categoryId) {
            $products = $productModel->getByCategory($categoryId);
        } else {
            $products = $productModel->getAll();
        }

        // Apply Sorting
        if ($sort == 'price-high') {
            usort($products, function ($a, $b) {
                return $b['price'] - $a['price'];
            });
        } elseif ($sort == 'price-low') {
            usort($products, function ($a, $b) {
                return $a['price'] - $b['price'];
            });
        } elseif ($sort == 'name') {
            usort($products, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        $categories = $categoryModel->getAll();

        $this->view('products/index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchQuery' => $search,
            'currentSort' => $sort
        ]);
    }

    public function show($slug)
    {
        $productModel = new Product();
        $product = $productModel->getBySlug($slug);

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        // Get related products
        $relatedProducts = $productModel->getByCategory($product['category_id'], 4);

        $this->view('products/show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    protected function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

