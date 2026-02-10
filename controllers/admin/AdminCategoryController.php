<?php
class AdminCategoryController {
    public function index() {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $categories = $categoryModel->getAllAdmin();
        
        // Get sub-categories for each category
        $subCategoryModel = new SubCategory();
        foreach ($categories as &$category) {
            $category['sub_categories'] = $subCategoryModel->getByCategoryId($category['id']);
        }
        
        $this->view('admin/categories/index', [
            'categories' => $categories
        ]);
    }
    
    public function create() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryModel = new Category();
            
            // Handle file upload
            $imageUrl = $_POST['image'] ?? '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['image_file']);
                if ($uploadResult['success']) {
                    $imageUrl = $uploadResult['url'];
                } else {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/categories/create');
                    return;
                }
            }
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'slug' => Helper::slugify($_POST['name'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'image' => $imageUrl,
                'status' => $_POST['status'] ?? 'active',
                'sort_order' => $_POST['sort_order'] ?? 0
            ];
            
            if (empty($data['name'])) {
                Session::flash('error', 'Category name is required!');
                Helper::redirect('admin/categories/create');
                return;
            }
            
            $categoryModel->create($data);
            Session::flash('success', 'Category created successfully!');
            Helper::redirect('admin/categories');
            return;
        }
        
        $this->view('admin/categories/create');
    }
    
    public function edit($id) {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $category = $categoryModel->getById($id);
        
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle file upload
            $imageUrl = $_POST['image'] ?? $category['image'] ?? '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['image_file']);
                if ($uploadResult['success']) {
                    $imageUrl = $uploadResult['url'];
                } else {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/categories/edit/' . $id);
                    return;
                }
            }
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'slug' => Helper::slugify($_POST['name'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'image' => $imageUrl,
                'status' => $_POST['status'] ?? 'active',
                'sort_order' => $_POST['sort_order'] ?? 0
            ];
            
            if (empty($data['name'])) {
                Session::flash('error', 'Category name is required!');
                Helper::redirect('admin/categories/edit/' . $id);
                return;
            }
            
            $categoryModel->update($id, $data);
            Session::flash('success', 'Category updated successfully!');
            Helper::redirect('admin/categories');
            return;
        }
        
        $this->view('admin/categories/edit', [
            'category' => $category
        ]);
    }
    
    public function delete($id) {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $categoryModel->delete($id);
        
        Session::flash('success', 'Category deleted successfully!');
        Helper::redirect('admin/categories');
    }
    
    // Sub-Category Methods
    public function createSubCategory($categoryId) {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $category = $categoryModel->getById($categoryId);
        
        if (!$category) {
            http_response_code(404);
            echo "Category not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subCategoryModel = new SubCategory();
            
            $data = [
                'category_id' => $categoryId,
                'name' => $_POST['name'] ?? '',
                'slug' => Helper::slugify($_POST['name'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'active',
                'sort_order' => $_POST['sort_order'] ?? 0
            ];
            
            if (empty($data['name'])) {
                Session::flash('error', 'Sub-category name is required!');
                Helper::redirect('admin/categories/' . $categoryId . '/sub-categories/create');
                return;
            }
            
            $subCategoryModel->create($data);
            Session::flash('success', 'Sub-category created successfully!');
            Helper::redirect('admin/categories');
            return;
        }
        
        $this->view('admin/categories/create_sub', [
            'category' => $category
        ]);
    }
    
    public function editSubCategory($categoryId, $subCategoryId) {
        $this->checkAdmin();
        
        $categoryModel = new Category();
        $category = $categoryModel->getById($categoryId);
        $subCategoryModel = new SubCategory();
        $subCategory = $subCategoryModel->getById($subCategoryId);
        
        if (!$category || !$subCategory || $subCategory['category_id'] != $categoryId) {
            http_response_code(404);
            echo "Sub-category not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'category_id' => $categoryId,
                'name' => $_POST['name'] ?? '',
                'slug' => Helper::slugify($_POST['name'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'active',
                'sort_order' => $_POST['sort_order'] ?? 0
            ];
            
            if (empty($data['name'])) {
                Session::flash('error', 'Sub-category name is required!');
                Helper::redirect('admin/categories/' . $categoryId . '/sub-categories/edit/' . $subCategoryId);
                return;
            }
            
            $subCategoryModel->update($subCategoryId, $data);
            Session::flash('success', 'Sub-category updated successfully!');
            Helper::redirect('admin/categories');
            return;
        }
        
        $this->view('admin/categories/edit_sub', [
            'category' => $category,
            'subCategory' => $subCategory
        ]);
    }
    
    public function deleteSubCategory($categoryId, $subCategoryId) {
        $this->checkAdmin();
        
        $subCategoryModel = new SubCategory();
        $subCategoryModel->delete($subCategoryId);
        
        Session::flash('success', 'Sub-category deleted successfully!');
        Helper::redirect('admin/categories');
    }
    
    private function handleFileUpload($file) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.'];
        }
        
        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File size too large. Maximum size is 5MB.'];
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../assets/uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'category_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $url = Helper::asset('uploads/categories/' . $filename);
            return ['success' => true, 'url' => $url, 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to upload file'];
        }
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

