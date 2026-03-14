<?php
// DB Connection Test
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../backend/config/constants.php';

echo "Attempting connection to " . DB_HOST . " for DB " . DB_NAME . "...<br>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>