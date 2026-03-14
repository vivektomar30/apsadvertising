<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../controllers/ContentController.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$controller = new ContentController($db);

$method = $_SERVER['REQUEST_METHOD'];

// Parse URI for RESTful params
$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/')); 
// Expected: api/content/page/home OR api/content/123 (update)

if ($method === 'GET') {
    // Check for /page/{pageName}
    $pageIndex = array_search('page', $parts);
    if ($pageIndex !== false && isset($parts[$pageIndex + 1])) {
        $controller->getPageContent($parts[$pageIndex + 1]);
    } else {
        // Fallback or getAll if appropriate
        http_response_code(404);
        echo json_encode(["message" => "Page not specified"]);
    }
} elseif ($method === 'POST') {
    $controller->create();
} elseif ($method === 'PUT') {
    // extract ID
    $id = null;
    foreach ($parts as $part) {
        if (is_numeric($part)) {
            $id = $part;
            break;
        }
    }
    
    if ($id) {
        $controller->update($id);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID required for update"]);
    }
}
?>
