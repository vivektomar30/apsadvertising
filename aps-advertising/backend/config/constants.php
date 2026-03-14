<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'aps_advertising');

// API Configuration
define('API_BASE_URL', 'https://apsadvertising.com/api');
define('SITE_URL', 'https://apsadvertising.com');

// Security
define('JWT_SECRET', 'aps_advertising_secret_key_2024_change_this');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRE_HOURS', 24);

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_AUDIO_TYPES', ['mp3', 'wav', 'ogg']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'webm', 'mov']);

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Error reporting (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>