<?php
class AuthController {
    public function login() {
        if (Auth::check()) {
            Helper::redirect('');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (Auth::attempt($email, $password)) {
                $user = Auth::user();
                if ($user['role'] === 'admin') {
                    Helper::redirect('admin/dashboard');
                } else {
                    Helper::redirect('');
                }
            } else {
                Session::flash('error', 'Invalid email or password');
            }
        }
        
        $this->view('auth/login');
    }
    
    public function register() {
        if (Auth::check()) {
            Helper::redirect('');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            
            // Check if email exists
            if ($userModel->getByEmail($_POST['email'])) {
                Session::flash('error', 'Email already exists');
            } else {
                $userId = $userModel->create([
                    'name' => $_POST['name'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'phone' => $_POST['phone'] ?? ''
                ]);
                
                // Auto login
                $user = $userModel->getByEmail($_POST['email']);
                Auth::login($user);
                
                Session::flash('success', 'Registration successful!');
                Helper::redirect('');
            }
        }
        
        $this->view('auth/register');
    }
    
    public function logout() {
        Auth::logout();
        Session::flash('success', 'Logged out successfully');
        Helper::redirect('');
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layouts/footer.php';
    }
}

