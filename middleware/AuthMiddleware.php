<?php
class AuthMiddleware {
    public function handle() {
        if (!Auth::check()) {
            Session::flash('error', 'Please login to access this page');
            Helper::redirect('login');
            return false;
        }
        return true;
    }
}

