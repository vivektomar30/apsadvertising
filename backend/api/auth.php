<?php
// Headers are already set in constants.php, but we ensure JSON content type here just in case
if (!headers_sent()) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Enable error logging for debugging
ini_set('display_errors', 0); // Don't display errors to output (creates invalid JSON)
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    require_once '../config/database.php';
    require_once '../models/User.php';
    require_once '../utils/JwtHandler.php';

    $db = new Database();
    $userModel = new User($db);
    $jwt = new JwtHandler();

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }

        if (isset($data['action']) && $data['action'] === 'login') {
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Email and password are required'
                ]);
                exit;
            }

            $user = $userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
                exit;
            }

            $token = $jwt->jwtEncodeData('apsadvertising.com', [
                'user_id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'data' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }
    } elseif ($method === 'GET') {
        // Handle /me endpoint
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        // Fallback for Apache/others where Authorization might be hidden
        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        $token = str_replace('Bearer ', '', $authHeader);

        if (empty($token)) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Token required'
            ]);
            exit;
        }

        $decoded = $jwt->jwtDecodeData($token);

        if (!$decoded['success']) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => $decoded['message']
            ]);
            exit;
        }

        $user = $userModel->findById($decoded['data']->user_id);

        if (!$user) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
            exit;
        }

        unset($user['password']);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $user
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server Error: ' . $e->getMessage()
    ]);
}
?>