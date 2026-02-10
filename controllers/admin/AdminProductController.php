<?php
class AdminProductController
{
    public function index()
    {
        $this->checkAdmin();

        $productModel = new Product();
        $categoryModel = new Category();

        $search = $_GET['search'] ?? '';

        if ($search) {
            $products = $productModel->searchAdmin($search);
        } else {
            $products = $productModel->getAll();
        }

        $categories = $categoryModel->getAll();

        $this->view('admin/products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search
        ]);
    }

    public function create()
    {
        $this->checkAdmin();

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = new Product();

            // Handle multiple image uploads
            $uploadedImages = [];
            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $uploadResult = $this->handleProductImagesUpload($_FILES['images']);
                if (!$uploadResult['success']) {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/products/create');
                    return;
                }
                $uploadedImages = $uploadResult['files'];
            }

            $primaryImage = $uploadedImages[0] ?? '';
            $galleryJson = !empty($uploadedImages) ? json_encode($uploadedImages) : null;

            $sku = isset($_POST['sku']) ? trim($_POST['sku']) : null;
            if ($sku === '') {
                $sku = null;
            }

            // Handle sub-category
            $subCategoryId = !empty($_POST['sub_category_id']) ? $_POST['sub_category_id'] : null;
            $showInMainCategory = isset($_POST['show_on_main_category']) && $_POST['show_on_main_category'] == '1' ? 1 : 0;

            // Handle colors
            $colors = [];
            if (!empty($_POST['colors_json'])) {
                $colors = json_decode($_POST['colors_json'], true) ?: [];
            }

            $data = [
                'name' => $_POST['name'],
                'slug' => Helper::slugify($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'price' => $_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock' => 0, // Default stock is 0, will be updated separately
                'sku' => $sku,
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'sub_category_id' => $subCategoryId,
                'show_in_main_category' => $showInMainCategory,
                'image' => $primaryImage,
                'gallery' => $galleryJson,
                'colors' => $colors,
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

    public function edit($id)
    {
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
            // Existing gallery images
            $existingGallery = [];
            if (!empty($product['gallery'])) {
                $decoded = json_decode($product['gallery'], true);
                if (is_array($decoded)) {
                    $existingGallery = $decoded;
                }
            }

            // Handle new uploads
            $newImages = [];
            if (isset($_FILES['images']) && is_array($_FILES['images']['name']) && !empty($_FILES['images']['name'][0])) {
                $uploadResult = $this->handleProductImagesUpload($_FILES['images']);
                if (!$uploadResult['success']) {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/products/edit/' . $id);
                    return;
                }
                $newImages = $uploadResult['files'];
            }

            $allImages = !empty($newImages) ? array_merge($newImages, $existingGallery) : $existingGallery;
            $primaryImage = !empty($allImages) ? $allImages[0] : ($product['image'] ?? '');
            $galleryJson = !empty($allImages) ? json_encode($allImages) : null;

            $sku = isset($_POST['sku']) ? trim($_POST['sku']) : null;
            if ($sku === '') {
                $sku = null;
            }

            // Handle sub-category
            $subCategoryId = !empty($_POST['sub_category_id']) ? $_POST['sub_category_id'] : null;
            $showInMainCategory = isset($_POST['show_on_main_category']) && $_POST['show_on_main_category'] == '1' ? 1 : 0;

            // Handle colors
            $colors = [];
            if (!empty($_POST['colors_json'])) {
                $colors = json_decode($_POST['colors_json'], true) ?: [];
            }

            $data = [
                'name' => $_POST['name'],
                'slug' => Helper::slugify($_POST['name']),
                'description' => $_POST['description'] ?? '',
                'short_description' => $_POST['short_description'] ?? '',
                'price' => $_POST['price'],
                'sale_price' => !empty($_POST['sale_price']) ? $_POST['sale_price'] : null,
                'stock' => $product['stock'] ?? 0, // Keep existing stock, don't update from form
                'sku' => $sku,
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'sub_category_id' => $subCategoryId,
                'show_in_main_category' => $showInMainCategory,
                'image' => $primaryImage,
                'gallery' => $galleryJson,
                'colors' => $colors,
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

    public function delete($id)
    {
        $this->checkAdmin();

        $productModel = new Product();
        $productModel->delete($id);

        Session::flash('success', 'Product deleted successfully!');
        Helper::redirect('admin/products');
    }

    public function updateStock()
    {
        $this->checkAdmin();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $productId = $_POST['product_id'] ?? null;
        $stock = $_POST['stock'] ?? null;

        if (!$productId || $stock === null) {
            echo json_encode(['success' => false, 'message' => 'Product ID and stock are required']);
            exit;
        }

        $productModel = new Product();
        $product = $productModel->getById($productId);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        // Update only stock
        $stock = (int) $stock;
        $db = Database::getInstance();
        $db->query("UPDATE products SET stock = ? WHERE id = ?", [$stock, $productId]);

        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
        exit;
    }

    public function stock()
    {
        $this->checkAdmin();

        $productModel = new Product();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sku = $_POST['sku'] ?? '';
            $stockToAdd = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
            $colorName = $_POST['color_name'] ?? '';

            if (empty($sku)) {
                Session::flash('error', 'SKU is required');
                Helper::redirect('admin/products/stock');
                return;
            }

            // Find product by SKU
            $product = $productModel->getBySku($sku);

            if (!$product) {
                Session::flash('error', 'Product with SKU "' . htmlspecialchars($sku) . '" not found');
                Helper::redirect('admin/products/stock');
                return;
            }

            // Check if product has colors
            $colors = [];
            if (!empty($product['colors'])) {
                $colors = json_decode($product['colors'], true) ?: [];
            }

            // If product has colors, color selection is required
            if (!empty($colors) && empty($colorName)) {
                Session::flash('error', 'Is product mein colors hain. Kripya color select karein.');
                Helper::redirect('admin/products/stock');
                return;
            }

            // If color is selected, add color-wise stock
            if (!empty($colorName)) {
                $colorStockModel = new ProductColorStock();
                $colorStock = $colorStockModel->getByProductAndColor($product['id'], $colorName);
                $currentColorStock = $colorStock ? (int) $colorStock['stock'] : 0;

                $colorStockModel->addStock($product['id'], $colorName, $stockToAdd);

                $newColorStock = $currentColorStock + $stockToAdd;
                Session::flash('success', 'Color stock updated successfully! Product: ' . htmlspecialchars($product['name']) . ' - Color: ' . htmlspecialchars($colorName) . ' (Previous: ' . $currentColorStock . ', Added: ' . $stockToAdd . ', New: ' . $newColorStock . ')');
            } else {
                // Update general stock (add to existing stock) - only if product has no colors
                $currentStock = (int) ($product['stock'] ?? 0);
                $newStock = $currentStock + $stockToAdd;

                if ($newStock < 0) {
                    $newStock = 0;
                }

                $db = Database::getInstance();
                $db->query("UPDATE products SET stock = ? WHERE id = ?", [$newStock, $product['id']]);

                Session::flash('success', 'Stock updated successfully! Product: ' . htmlspecialchars($product['name']) . ' (Previous: ' . $currentStock . ', Added: ' . $stockToAdd . ', New: ' . $newStock . ')');
            }

            Helper::redirect('admin/products/stock');
            return;
        }

        $this->view('admin/products/stock', []);
    }

    private function handleProductImagesUpload(array $files): array
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../assets/uploads/products/';

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
                return ['success' => false, 'message' => 'Failed to create upload directory'];
            }
        }

        $uploaded = [];
        foreach ($files['name'] as $index => $name) {
            if (empty($name)) {
                continue;
            }

            $error = $files['error'][$index];
            $tmpName = $files['tmp_name'][$index];
            $size = $files['size'][$index];

            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, $allowedTypes)) {
                continue;
            }

            if ($size > $maxSize) {
                continue;
            }

            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . $index . '_' . uniqid() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $filepath)) {
                $uploaded[] = 'uploads/products/' . $filename;
            }
        }

        if (empty($uploaded)) {
            return ['success' => true, 'files' => []];
        }

        return ['success' => true, 'files' => $uploaded];
    }

    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            Helper::redirect('login');
            exit;
        }
    }

    protected function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../../views/layouts/admin_header.php';
        require __DIR__ . "/../../views/{$view}.php";
        require __DIR__ . '/../../views/layouts/admin_footer.php';
    }
}

