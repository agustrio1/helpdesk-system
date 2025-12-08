<?php
namespace App\Helpers;

class Security {
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::isAuthenticated() && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Escape output for XSS protection
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize input (allow HTML from rich text editor but remove dangerous tags)
     */
    public static function sanitize($input) {
        // Allow certain HTML tags for rich text editor
        $allowed_tags = '<p><br><strong><em><u><s><h1><h2><h3><h4><ul><ol><li><blockquote><a><img><code><pre>';
        return strip_tags($input, $allowed_tags);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Rate limiting
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $period = 60) {
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }
        
        $now = time();
        $rateLimitKey = $key . '_' . floor($now / $period);
        
        if (!isset($_SESSION['rate_limit'][$rateLimitKey])) {
            $_SESSION['rate_limit'][$rateLimitKey] = 1;
            return true;
        }
        
        $_SESSION['rate_limit'][$rateLimitKey]++;
        
        // Clean old entries
        foreach ($_SESSION['rate_limit'] as $k => $v) {
            if (strpos($k, $key) === 0 && $k !== $rateLimitKey) {
                unset($_SESSION['rate_limit'][$k]);
            }
        }
        
        return $_SESSION['rate_limit'][$rateLimitKey] <= $maxAttempts;
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file) {
        $maxSize = $_ENV['MAX_UPLOAD_SIZE'] ?? 5242880; // 5MB default
        $allowedTypes = explode(',', $_ENV['ALLOWED_FILE_TYPES'] ?? 'jpg,jpeg,png,pdf,doc,docx,txt,zip');
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'File too large. Max size: ' . round($maxSize / 1048576, 2) . 'MB'
            ];
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check allowed types
        if (!in_array($extension, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'File type not allowed. Allowed: ' . implode(', ', $allowedTypes)
            ];
        }
        
        // Detect actual MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        // Validate MIME type matches extension
        $validMimeTypes = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'txt' => ['text/plain'],
            'zip' => ['application/zip', 'application/x-zip-compressed']
        ];
        
        if (isset($validMimeTypes[$extension]) && !in_array($mimeType, $validMimeTypes[$extension])) {
            return [
                'success' => false,
                'message' => 'File type mismatch. Invalid file.'
            ];
        }
        
        return [
            'success' => true,
            'mime_type' => $mimeType
        ];
    }
    
    /**
     * Validate image upload (for avatars)
     */
    public static function validateImageUpload($file) {
        $maxSize = 2097152; // 2MB for images
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'Image too large. Max size: 2MB'
            ];
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Check allowed types
        if (!in_array($extension, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Invalid image format. Allowed: JPG, PNG, GIF'
            ];
        }
        
        // Validate it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'success' => false,
                'message' => 'File is not a valid image'
            ];
        }
        
        // Check dimensions (max 2000x2000)
        if ($imageInfo[0] > 2000 || $imageInfo[1] > 2000) {
            return [
                'success' => false,
                'message' => 'Image dimensions too large. Max: 2000x2000px'
            ];
        }
        
        return [
            'success' => true,
            'mime_type' => $imageInfo['mime']
        ];
    }
    
    /**
     * Generate secure filename
     */
    public static function generateSecureFilename($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('file_', true) . '_' . time() . '.' . $extension;
    }
    
    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}