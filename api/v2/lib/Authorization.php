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
        $headers = getallheaders();
        
        // Debug: Capture all headers
        $debugHeaders = [
            'all_headers' => $headers,
            'auth_header' => isset($headers['Authorization']) ? $headers['Authorization'] : 'Not Set',
            'raw_auth_header' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'Not Set in $_SERVER',
            'request_method' => $_SERVER['REQUEST_METHOD']
        ];
        
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        // Check for Bearer token format
        if (empty($authHeader) || !preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            $this->throwError(401, 'Authorization header must be in format: Bearer {token}', $debugHeaders);
        }

        $this->token = $matches[1];
        $debugHeaders['extracted_token'] = $this->token;
        
        $this->decodeToken($debugHeaders);
    }

    private function decodeToken($debugHeaders = []) {
        try {
            $this->decoded = JWT::decode($this->token, new Key(JWT_SECRET, JWT_ALGORITHM));
            $this->userId = $this->decoded->data->client_id;
            
            // Validate token expiration
            if ($this->decoded->exp < time()) {
                $this->throwError(401, 'Token has expired', $debugHeaders);
            }
        } catch (Exception $e) {
            $this->throwError(401, 'Invalid token: ' . $e->getMessage(), $debugHeaders);
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDecodedToken() {
        return $this->decoded;
    }

    private function throwError($code, $message, $debugInfo = []) {
        http_response_code($code);
        echo json_encode([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'debug' => [
                'headers' => $debugInfo,
                'server_vars' => [
                    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                    'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'Not Set',
                    'CONTENT_TYPE' => $_SERVER['CONTENT_TYPE'] ?? 'Not Set'
                ]
            ]
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

