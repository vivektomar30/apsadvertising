<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../controllers/PartnerController.php';
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$controller = new PartnerController($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->getAll();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
}
?>
