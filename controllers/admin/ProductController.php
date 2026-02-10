<?php
class AdminProductController {
    public function index() {
        $this->checkAdmin();
        
        $productModel = new Product();
        $categoryModel = new Category();
        
        $products = $productModel->getAll();
        $categories = $categoryModel->getAll();
        
        $this->view('admin/products/index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
    
    public function create() {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = new Product();
            
            $data = [
                'name' => $_POST['name'],
                'slug' => Helper::slugify($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'price' => $_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock' => $_POST['stock'] ?? 0,
                'sku' => $_POST['sku'] ?? '',
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'image' => $_POST['image'] ?? '',
                'status' => $_POST['status'] ?? 'active',
                'featured' => isset($_POST['featured']) ? 1 : 0
            ];
            
            $productModel->create($data);
            Session::flash('success', 'Product created successfully!');
            Helper::redirect('admin/products');
            return;
        }
        
        $this->view('admin/products/create', [
            'categories' => $categories
        ]);
    }
    
    public function edit($id) {
        $this->checkAdmin();
        
        $productModel = new Product();
        $categoryModel = new Category();
        
        $product = $productModel->getById($id);
        
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'slug' => Helper::slugify($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'price' => $_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock' => $_POST['stock'] ?? 0,
                'sku' => $_POST['sku'] ?? '',
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'image' => $_POST['image'] ?? '',
                'status' => $_POST['status'] ?? 'active',
                'featured' => isset($_POST['featured']) ? 1 : 0
            ];
            
            $productModel->update($id, $data);
            Session::flash('success', 'Product updated successfully!');
            Helper::redirect('admin/products');
            return;
        }
        
        $categories = $categoryModel->getAll();
        
        $this->view('admin/products/edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function delete($id) {
        $this->checkAdmin();
        
        $productModel = new Product();
        $productModel->delete($id);
        
        Session::flash('success', 'Product deleted successfully!');
        Helper::redirect('admin/products');
    }
    
    private function checkAdmin() {
        if (!Auth::check() || !Auth::isAdmin()) {
            Helper::redirect('login');
            exit;
        }
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../../views/layouts/admin_header.php';
        require __DIR__ . "/../../views/{$view}.php";
        require __DIR__ . '/../../views/layouts/admin_footer.php';
    }
}

