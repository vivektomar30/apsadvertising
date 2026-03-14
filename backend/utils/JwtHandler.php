<?php
require_once __DIR__ . '/../config/constants.php';

class JwtHandler
{
    protected $jwt_secret;
    protected $jwt_algo;

    public function __construct()
    {
        $this->jwt_secret = JWT_SECRET;
        $this->jwt_algo = JWT_ALGORITHM;
    }

    public function jwtEncodeData($iss, $data)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => $this->jwt_algo]);
        $payload = json_encode([
            'iss' => $iss,
            'aud' => $iss,
            'iat' => time(),
            'exp' => time() + (JWT_EXPIRE_HOURS * 3600),
            'data' => $data
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->jwt_secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    public function jwtDecodeData($token)
    {
        try {
            $tokenParts = explode('.', $token);

            if (count($tokenParts) != 3) {
                throw new Exception('Invalid token structure');
            }

            $header = $this->base64UrlDecode($tokenParts[0]);
            $payload = $this->base64UrlDecode($tokenParts[1]);
            $signatureProvided = $tokenParts[2];

            // Check expiration
            $payloadData = json_decode($payload);
            if ($payloadData->exp < time()) {
                throw new Exception('Token expired');
            }

            // Verify signature
            $base64UrlHeader = $tokenParts[0];
            $base64UrlPayload = $tokenParts[1];
            $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->jwt_secret, true);
            $base64UrlSignature = $this->base64UrlEncode($signature);

            if ($base64UrlSignature !== $signatureProvided) {
                throw new Exception('Invalid signature');
            }

            return [
                'success' => true,
                'data' => $payloadData->data
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>