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
            $this->decoded = JWT::decode(
                $this->token, 
                new Key(JWT_SECRET, 'HS256')  // Explicitly set algorithm to HS256
            );
            
            if (!isset($this->decoded->data->client_id)) {
                throw new Exception('Invalid token structure');
            }

            $this->userId = $this->decoded->data->client_id;
            
            if ($this->decoded->exp < time()) {
                throw new Exception('Token has expired');
            }
        } catch (Exception $e) {
            $this->throwError(401, 'Invalid token: ' . $e->getMessage());
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

