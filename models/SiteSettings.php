<?php
class SiteSettings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function get($key, $default = null) {
        $result = $this->db->fetch(
            "SELECT setting_value FROM site_settings WHERE setting_key = ?",
            [$key]
        );
        
        return $result ? $result['setting_value'] : $default;
    }
    
    public function set($key, $value) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?";
        
        return $this->db->query($sql, [$key, $value, $value]);
    }
    
    public function getAll() {
        $results = $this->db->fetchAll("SELECT * FROM site_settings ORDER BY setting_key ASC");
        $settings = [];
        
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }
    
    public function updateSettings($settings) {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }
}

