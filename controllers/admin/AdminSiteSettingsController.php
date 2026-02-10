<?php
class AdminSiteSettingsController {
    public function index() {
        $this->checkAdmin();
        
        $siteSettingsModel = new SiteSettings();
        $settings = $siteSettingsModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'whatsapp_number' => $_POST['whatsapp_number'] ?? '',
                'contact_email' => $_POST['contact_email'] ?? '',
                'whatsapp_icon_enabled' => isset($_POST['whatsapp_icon_enabled']) ? '1' : '0'
            ];
            
            if (empty($data['whatsapp_number'])) {
                Session::flash('error', 'WhatsApp number is required!');
                Helper::redirect('admin/site-settings');
                return;
            }
            
            if (empty($data['contact_email'])) {
                Session::flash('error', 'Contact email is required!');
                Helper::redirect('admin/site-settings');
                return;
            }
            
            $siteSettingsModel->updateSettings($data);
            Session::flash('success', 'Site settings updated successfully!');
            Helper::redirect('admin/site-settings');
            return;
        }
        
        $this->view('admin/site_settings/index', [
            'settings' => $settings
        ]);
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

