<?php
require_once __DIR__ . '/../config/constants.php';

class JwtHandler {
    protected $jwt_secrect;

    public function __construct() {
        $this->jwt_secrect = JWT_SECRET;
    }

    // Generate JWT Token
    public function jwtEncodeData($iss, $data) {
        $this->token_msg = array();
        $this->token_header = array(
            'typ' => 'JWT',
            'alg' => 'HS256'
        );
        $this->token_payload = array(
            'iss' => $iss,
            'aud' => $iss,
            'iat' => time(),
            'exp' => time() + JWT_EXPIRY,
            'data' => $data
        );

        $this->jwt = $this->jwtEncode();
        return $this->jwt;
    }

    // Decode JWT Token
    public function jwtDecodeData($jwt_token) {
        try {
            $decode = $this->jwtDecode($jwt_token);
            return [
                "success" => true,
                "data" => $decode->data
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    protected function jwtEncode() {
        $header = $this->urlSafeB64Encode(json_encode($this->token_header));
        $payload = $this->urlSafeB64Encode(json_encode($this->token_payload));
        $signature = $this->urlSafeB64Encode(hash_hmac('sha256', $header . '.' . $payload, $this->jwt_secrect, true));

        return $header . '.' . $payload . '.' . $signature;
    }

    protected function jwtDecode($token) {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            throw new Exception("Invalid Token Structure");
        }

        $header = base64_decode($this->urlSafeB64Decode($tokenParts[0]));
        $payload = base64_decode($this->urlSafeB64Decode($tokenParts[1]));
        $signatureProvided = $tokenParts[2];

        // Check expiration
        $expiration = json_decode($payload)->exp;
        $isTokenExpired = ($expiration - time()) < 0;

        // Build a signature based on the header and payload using the secret
        $base64UrlHeader = $this->urlSafeB64Encode($header);
        $base64UrlPayload = $this->urlSafeB64Encode($payload);
        $signature = $this->urlSafeB64Encode(hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->jwt_secrect, true));

        // Verify it matches the signature provided in the token
        if ($isTokenExpired) {
            throw new Exception("Token Expired");
        }
        
        if ($signature != $signatureProvided) {
             throw new Exception("Invalid Signature");
        }

        return json_decode($payload);
    }

    protected function urlSafeB64Encode($data) {
        $b64 = base64_encode($data);
        $b64 = str_replace(array('+', '/', '='), array('-', '_', ''), $b64);
        return $b64;
    }

    protected function urlSafeB64Decode($b64) {
        $b64 = str_replace(array('-', '_'), array('+', '/'), $b64);
        return $b64;
    }
}
?>
