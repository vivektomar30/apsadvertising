<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../controllers/ContactController.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$controller = new ContactController($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Simple routing based on Method and ID
if ($method === 'POST') {
    $controller->submit();
} elseif ($method === 'GET') {
    $controller->getAll();
} elseif ($method === 'PUT' && $id) {
    // Check if it is a status update
    if (strpos($_SERVER['REQUEST_URI'], '/status') !== false) {
        $controller->updateStatus($id);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>