// Proxy to backend API

// Check for backend in common locations
if (file_exists(__DIR__ . '/../../backend/api/index.php')) {
// Local development structure: root/public/api -> root/backend/api
require_once __DIR__ . '/../../backend/api/index.php';
} elseif (file_exists(__DIR__ . '/../backend/api/index.php')) {
// Flat hosting structure: public_html/api -> public_html/backend/api
require_once __DIR__ . '/../backend/api/index.php';
} else {
http_response_code(500);
echo json_encode(['error' => 'Backend configuration error: API file not found', 'path' => __DIR__]);
}
?>