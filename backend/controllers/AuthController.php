<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JwtHandler.php';

class AuthController {
    private $user;
    private $jwt;

    public function __construct($db) {
        $this->user = new User($db);
        $this->jwt = new JwtHandler();
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->email) || !isset($data->password)) {
            http_response_code(400);
            echo json_encode(["message" => "Please provide email and password"]);
            return;
        }

        $this->user->email = $data->email;

        if ($this->user->emailExists()) {
            if (password_verify($data->password, $this->user->password)) {
                $token = $this->jwt->jwtEncodeData(
                    APP_URL,
                    array(
                        "user_id" => $this->user->id,
                        "name" => $this->user->name,
                        "email" => $this->user->email,
                        "role" => $this->user->role
                    )
                );

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "message" => "Login successful",
                    "token" => $token,
                    "user" => array(
                        "id" => $this->user->id,
                        "name" => $this->user->name,
                        "email" => $this->user->email,
                        "role" => $this->user->role
                    )
                ));
            } else {
                http_response_code(401);
                echo json_encode(array("success" => false, "message" => "Invalid password"));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("success" => false, "message" => "User not found"));
        }
    }

    public function me() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => "No token provided"));
            return;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decoded = $this->jwt->jwtDecodeData($token);

        if ($decoded['success']) {
            $this->user->id = $decoded['data']->user_id;
            $userData = $this->user->getUser();
            
            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "user" => $userData
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("success" => false, "message" => $decoded['message']));
        }
    }
}
?>
