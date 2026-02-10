<?php
class Helper {
    public static function url($path = '') {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = rtrim($basePath, '/');
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        // Handle empty path
        if (empty($path)) {
            return $basePath ? $basePath . '/' : '/';
        }
        
        return $basePath . '/' . ltrim($path, '/');
    }
    
    public static function asset($path) {
        return self::url('assets/' . ltrim($path, '/'));
    }
    
    public static function redirect($path) {
        header('Location: ' . self::url($path));
        exit;
    }
    
    public static function formatPrice($price) {
        return 'Rs. ' . number_format($price, 2);
    }
    
    public static function slugify($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
    
    public static function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Resolve product image path to full URL.
     * Supports:
     * - Full external URLs (returned as-is)
     * - Paths starting with "uploads/" (stored uploads directory)
     * - Simple filenames (served from assets/images)
     */
    public static function productImageUrl(?string $image): ?string
    {
        if (empty($image)) {
            return null;
        }

        // If it's already a full URL (http/https)
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        // If stored in uploads directory (e.g. "uploads/products/xyz.jpg")
        if (strpos($image, 'uploads/') === 0) {
            return self::asset($image);
        }

        // Fallback: treat as file inside assets/images
        return self::asset('images/' . ltrim($image, '/'));
    }

    /**
     * Convert stored gallery JSON/string into array of full image URLs.
     */
    public static function productGalleryUrls(?string $gallery): array
    {
        if (empty($gallery)) {
            return [];
        }

        $items = json_decode($gallery, true);
        if (!is_array($items)) {
            return [];
        }

        $urls = [];
        foreach ($items as $img) {
            $url = self::productImageUrl(is_string($img) ? $img : null);
            if ($url) {
                $urls[] = $url;
            }
        }

        return $urls;
    }
}

