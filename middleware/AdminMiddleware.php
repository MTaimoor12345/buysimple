<?php
class AdminMiddleware {
    public function handle() {
        if (!Auth::check() || !Auth::isAdmin()) {
            Session::flash('error', 'Access denied. Admin only.');
            Helper::redirect('login');
            return false;
        }
        return true;
    }
}

