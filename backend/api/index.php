<?php
// API Router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Parse the endpoint
$script_name = $_SERVER['SCRIPT_NAME']; // e.g., /api/index.php or /backend/api/index.php
$request_uri = $_SERVER['REQUEST_URI']; // e.g., /api/user?id=1

// Remove query string
if (false !== $pos = strpos($request_uri, '?')) {
    $request_uri = substr($request_uri, 0, $pos);
}

// Determine the path relative to the script location
$script_dir = dirname($script_name);
// Normalize slashes
$script_dir = str_replace('\\', '/', $script_dir);
$request_uri = str_replace('\\', '/', $request_uri);

if ($script_dir === '/' || $script_dir === '.') {
    $path = ltrim($request_uri, '/');
} else {
    // Remove script dir from request uri
    $path = substr($request_uri, strlen($script_dir));
    $path = ltrim($path, '/');
}

// If calling index.php directly in path (rare but possible)
if (strpos($path, 'index.php') === 0) {
    $path = substr($path, 9);
    $path = ltrim($path, '/');
}

$parts = explode('/', $path);
$endpoint = $parts[0] ?? '';

// Debugging (only if needed, careful not to break JSON)
// error_log("Request: $request_uri, ScriptDir: $script_dir, Path: $path, Endpoint: $endpoint");

// Route to appropriate handler
// Route to appropriate handler
$endpoint = '';
$possible_endpoints = ['auth', 'user', 'contact', 'feedback', 'demos', 'content', 'partners'];

// Extract endpoint and ID from path
// Path might be: user, user/123, feedback/123/approve
$path_parts = explode('/', $path);

// Find which part matches a known endpoint
$endpoint_index = -1;
foreach ($path_parts as $i => $part) {
    if (in_array($part, $possible_endpoints)) {
        $endpoint = $part;
        $endpoint_index = $i;
        break;
    }
}

if ($endpoint) {
    // Check for ID after endpoint
    if (isset($path_parts[$endpoint_index + 1])) {
        $next_part = $path_parts[$endpoint_index + 1];
        // If it's not empty and not query string (already stripping query string above)
        if ($next_part !== '') {
            // It could be an ID or an action (like 'login' if url was /user/login, but user actions are POST body)
            // For REST resource (contact/123), it's ID.
            $_GET['id'] = $next_part;

            // Check for action after ID (e.g. feedback/123/approve)
            if (isset($path_parts[$endpoint_index + 2])) {
                $_GET['action'] = $path_parts[$endpoint_index + 2];
            }
        }
    }

    switch ($endpoint) {
        case 'auth':
        case 'user':
            require_once __DIR__ . '/auth.php';
            break;
        case 'contact':
            require_once __DIR__ . '/contact.php';
            break;
        case 'feedback':
            require_once __DIR__ . '/feedback.php';
            break;
        case 'demos':
            require_once __DIR__ . '/demos.php';
            break;
        case 'content':
            require_once __DIR__ . '/content.php';
            break;
        case 'partners':
            require_once __DIR__ . '/partners.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['message' => 'Endpoint handler not found']);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Endpoint not found']);
}
?>