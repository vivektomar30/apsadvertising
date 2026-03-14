<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../controllers/FeedbackController.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$controller = new FeedbackController($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Determine action based on URI or params if needed, 
// for now implicit REST: POST=submit, GET=getAll, PUT /id/like, PUT /id/approve
// The index.php simple router might not parse ID nicely into $_GET['id'] for REST URLs like /feedback/1/like
// We might need to rely on the current index.php which seems to just include the file.
// Let's assume we might need to parse PATH_INFO or request URI if index.php doesn't help.
// Looking at index.php: it does `require_once 'feedback.php'`.
// It does NOT parse IDs.
// So we need to parse the URI here to extract ID for things like /feedback/123/like

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/')); 
// e.g. api/feedback/123/like
// parts: [api, feedback, 123, like]

if ($method === 'POST') {
    $controller->submit();
} elseif ($method === 'GET') {
    $controller->getAll();
} elseif ($method === 'PUT') {
    // Try to find ID and action from URL
    // This is a bit hacky without a real router, but works for this scope.
    // We look for numeric ID in the path
    $action = '';
    $requestId = null;
    
    foreach ($parts as $part) {
        if (is_numeric($part)) {
            $requestId = $part;
        } elseif ($part === 'like') {
            $action = 'like';
        } elseif ($part === 'approve') {
            $action = 'approve';
        }
    }
    
    if ($requestId) {
        if ($action === 'like') {
            $controller->like($requestId);
        } elseif ($action === 'approve') {
            $controller->approve($requestId);
        } else {
             http_response_code(404);
             echo json_encode(["message" => "Action not found"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "ID required"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>