<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authorization {
    private $token;
    private $decoded;
    private $userId;

    public function __construct() {
        $this->validateAuthHeader();
    }

    private function validateAuthHeader() {
        $authHeader = $_SERVER['HTTP_X_AUTHORIZATION'] ?? '';
        
        if (empty($authHeader)) {
            $this->throwError(401, 'X-Authorization header is required');
        }

        // Clean the token - remove Bearer prefix and any whitespace/newlines
        $this->token = trim(str_replace('Bearer', '', $authHeader));
        
        $this->decodeToken();
    }

    private function decodeToken() {
        try {
            // First try decoding without any algorithm restrictions
            $this->decoded = JWT::decode(
                $this->token,
                new Key(JWT_SECRET, 'HS256')
            );
            
            if (!isset($this->decoded->data->client_id)) {
                throw new Exception('Invalid token structure');
            }

            // Check expiration time
            if ($this->decoded->exp < time()) {
                throw new Exception('Token has expired');
            }

            $this->userId = $this->decoded->data->client_id;
            
            // Verify token matches stored session
            $storedToken = Capsule::table('tblclients')
                ->where('id', $this->userId)
                ->value('authToken');
                
            if (empty($storedToken) || CompressAuthToken($this->token) !== $storedToken) {
                throw new Exception('Invalid session');
            }

        } catch (Exception $e) {
            $this->throwError(401, $e->getMessage());
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDecodedToken() {
        return $this->decoded;
    }

    private function throwError($code, $message) {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ]);
        exit;
    }
}

