<?php
class Auth {
    public static function user() {
        return Session::get('user');
    }
    
    public static function id() {
        $user = self::user();
        return $user['id'] ?? null;
    }
    
    public static function check() {
        return Session::has('user');
    }
    
    public static function isAdmin() {
        $user = self::user();
        return isset($user['role']) && $user['role'] === 'admin';
    }
    
    public static function login($user) {
        unset($user['password']);
        Session::set('user', $user);
    }
    
    public static function logout() {
        Session::remove('user');
    }
    
    public static function attempt($email, $password) {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            self::login($user);
            return true;
        }
        
        return false;
    }
}

