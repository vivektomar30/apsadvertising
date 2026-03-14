<?php
require_once '../config/database.php';
require_once '../models/User.php';

$db = new Database();
$userModel = new User($db);

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['action'])) {
                switch ($data['action']) {
                    case 'login':
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
                        
                        // Create JWT token (simplified)
                        $token = base64_encode(json_encode([
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'name' => $user['name'],
                            'role' => $user['role'],
                            'exp' => time() + (60 * 60 * JWT_EXPIRE_HOURS)
                        ]));
                        
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
                        break;
                    
                    case 'register':
                        $name = $data['name'] ?? '';
                        $email = $data['email'] ?? '';
                        $password = $data['password'] ?? '';
                        
                        if (empty($name) || empty($email) || empty($password)) {
                            http_response_code(400);
                            echo json_encode([
                                'success' => false,
                                'message' => 'All fields are required'
                            ]);
                            exit;
                        }
                        
                        if ($userModel->findByEmail($email)) {
                            http_response_code(409);
                            echo json_encode([
                                'success' => false,
                                'message' => 'Email already exists'
                            ]);
                            exit;
                        }
                        
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $userId = $userModel->create([
                            'name' => $name,
                            'email' => $email,
                            'password' => $hashedPassword,
                            'role' => 'editor'
                        ]);
                        
                        http_response_code(201);
                        echo json_encode([
                            'success' => true,
                            'message' => 'User registered successfully',
                            'userId' => $userId
                        ]);
                        break;
                    
                    default:
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Invalid action'
                        ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Action is required'
                ]);
            }
            break;
        
        case 'GET':
            $headers = getallheaders();
            $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
            
            if (empty($token)) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Token required'
                ]);
                exit;
            }
            
            $decoded = json_decode(base64_decode($token), true);
            
            if (!$decoded || $decoded['exp'] < time()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ]);
                exit;
            }
            
            $user = $userModel->findById($decoded['id']);
            
            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                exit;
            }
            
            // Remove password from response
            unset($user['password']);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $user
            ]);
            break;
        
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
?>