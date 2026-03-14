<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/DemoController.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

$db = new Database();
$controller = new DemoController($db);

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = '/' . trim($path, '/');

// Extract ID if present (e.g., /123)
$parts = explode('/', trim($path, '/'));
$id = null;
if (isset($parts[0]) && is_numeric($parts[0])) {
    $id = $parts[0];
}

$controller->handleRequest($method, $path, $id);
?>