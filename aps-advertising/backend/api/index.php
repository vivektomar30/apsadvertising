<?php
// API Router
require_once '../config/database.php';
require_once '../config/constants.php';

// Get request method and path
// Get path from PATH_INFO if available, or calculate from REQUEST_URI
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace($scriptName, '', $requestUri);

// Remove leading/trailing slashes and ensure leading slash
$path = '/' . trim($path, '/');

// Simple routing
// Simple routing with prefix matching
if (strpos($path, '/auth') === 0) {
    require_once 'auth.php';
} elseif (strpos($path, '/contact') === 0) {
    require_once 'contact.php';
} elseif (strpos($path, '/feedback') === 0) {
    require_once 'feedback.php';
} elseif (strpos($path, '/content') === 0) {
    require_once 'content.php';
} elseif (strpos($path, '/partners') === 0) {
    require_once 'partners.php';
} elseif ($path === '/health') {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'API is running',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Endpoint not found: ' . $path
    ]);
}
?>