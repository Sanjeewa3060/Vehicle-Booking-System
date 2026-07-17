<?php
class JWT {
    private static $secret_key = "your-secret-key-change-this-in-production";
    
    public static function encode($data) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode($data);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);

        if (count($tokenParts) != 3) {
            return false;
        }

        $base64UrlHeader = $tokenParts[0];
        $base64UrlPayload = $tokenParts[1];
        $signatureProvided = $tokenParts[2];

        // Verify signature using the unpadded base64url segments
        // (matching how the signature was generated in encode()).
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        if (!hash_equals($base64UrlSignature, $signatureProvided)) {
            return false;
        }

        // Decode payload (add padding back before base64 decoding)
        $payload = $base64UrlPayload . str_repeat('=', (4 - strlen($base64UrlPayload) % 4) % 4);
        $decodedPayload = base64_decode(strtr($payload, '-_', '+/'), true);

        if ($decodedPayload === false) {
            return false;
        }

        $data = json_decode($decodedPayload, true);

        if (!$data) {
            return false;
        }

        // Reject expired tokens if an expiry was set
        if (isset($data['exp']) && is_numeric($data['exp']) && $data['exp'] < time()) {
            return false;
        }

        return $data;
    }
    
    public static function getUserFromToken() {
        $authHeader = '';

        // Check various ways the Authorization header might be made available.
        // Under Apache + mod_php the header is often rewritten to REDIRECT_HTTP_AUTHORIZATION.
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['Authorization'])) {
            $authHeader = $_SERVER['Authorization'];
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        }

    if (empty($authHeader)) return false;

    $token = str_replace('Bearer ', '', $authHeader);
    return self::decode($token);
}
}
?>