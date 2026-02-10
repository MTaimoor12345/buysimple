<?php
class AdminBannerTextController {
    public function index() {
        $this->checkAdmin();
        
        $bannerTextModel = new BannerText();
        $texts = $bannerTextModel->getAllAdmin();
        
        $this->view('admin/banner_texts/index', [
            'texts' => $texts
        ]);
    }
    
    public function create() {
        $this->checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bannerTextModel = new BannerText();
            
            $data = [
                'text' => $_POST['text'] ?? '',
                'sort_order' => $_POST['sort_order'] ?? 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            if (empty($data['text'])) {
                Session::flash('error', 'Text is required!');
                Helper::redirect('admin/banner-texts/create');
                return;
            }
            
            $bannerTextModel->create($data);
            Session::flash('success', 'Banner text created successfully!');
            Helper::redirect('admin/banner-texts');
            return;
        }
        
        $this->view('admin/banner_texts/create');
    }
    
    public function edit($id) {
        $this->checkAdmin();
        
        $bannerTextModel = new BannerText();
        $text = $bannerTextModel->getById($id);
        
        if (!$text) {
            http_response_code(404);
            echo "Banner text not found";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'text' => $_POST['text'] ?? '',
                'sort_order' => $_POST['sort_order'] ?? 0,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            if (empty($data['text'])) {
                Session::flash('error', 'Text is required!');
                Helper::redirect('admin/banner-texts/edit/' . $id);
                return;
            }
            
            $bannerTextModel->update($id, $data);
            Session::flash('success', 'Banner text updated successfully!');
            Helper::redirect('admin/banner-texts');
            return;
        }
        
        $this->view('admin/banner_texts/edit', [
            'text' => $text
        ]);
    }
    
    public function delete($id) {
        $this->checkAdmin();
        
        $bannerTextModel = new BannerText();
        $bannerTextModel->delete($id);
        
        Session::flash('success', 'Banner text deleted successfully!');
        Helper::redirect('admin/banner-texts');
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

