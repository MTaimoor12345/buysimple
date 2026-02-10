<?php
class AdminHeroSlideController {
    public function index() {
        $this->checkAdmin();
        
        $heroSlideModel = new HeroSlide();
        $slides = $heroSlideModel->getAllAdmin();
        
        $this->view('admin/hero_slides/index', [
            'slides' => $slides
        ]);
    }
    
    public function create() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $heroSlideModel = new HeroSlide();
            
            // Handle file upload
            $imageUrl = $_POST['image'] ?? '';
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['image_file']);
                if ($uploadResult['success']) {
                    $imageUrl = $uploadResult['url'];
                } else {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/hero-slides/create');
                    return;
                }
            }
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'image' => $imageUrl,
                'button_text' => $_POST['button_text'] ?? 'Buy Now',
                'button_link' => $_POST['button_link'] ?? '/products',
                'image_link' => $_POST['image_link'] ?? null,
                'sort_order' => $_POST['sort_order'] ?? 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $heroSlideModel->create($data);
            Session::flash('success', 'Hero slide created successfully!');
            Helper::redirect('admin/hero-slides');
            return;
        }
        
        $this->view('admin/hero_slides/create');
    }
    
    public function edit($id) {
        $this->checkAdmin();
        
        $heroSlideModel = new HeroSlide();
        $slide = $heroSlideModel->getById($id);
        
        if (!$slide) {
            http_response_code(404);
            echo "Slide not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle file upload
            $imageUrl = $_POST['image'] ?? $slide['image'];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['image_file']);
                if ($uploadResult['success']) {
                    $imageUrl = $uploadResult['url'];
                } else {
                    Session::flash('error', $uploadResult['message']);
                    Helper::redirect('admin/hero-slides/edit/' . $id);
                    return;
                }
            }
            
            $data = [
                'title' => $_POST['title'] ?? '',
                'image' => $imageUrl,
                'button_text' => $_POST['button_text'] ?? 'Buy Now',
                'button_link' => $_POST['button_link'] ?? '/products',
                'image_link' => $_POST['image_link'] ?? null,
                'sort_order' => $_POST['sort_order'] ?? 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            $heroSlideModel->update($id, $data);
            Session::flash('success', 'Hero slide updated successfully!');
            Helper::redirect('admin/hero-slides');
            return;
        }
        
        $this->view('admin/hero_slides/edit', [
            'slide' => $slide
        ]);
    }
    
    public function delete($id) {
        $this->checkAdmin();
        
        $heroSlideModel = new HeroSlide();
        $heroSlideModel->delete($id);
        
        Session::flash('success', 'Hero slide deleted successfully!');
        Helper::redirect('admin/hero-slides');
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
        $uploadDir = __DIR__ . '/../../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'hero_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $url = Helper::asset('uploads/' . $filename);
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

