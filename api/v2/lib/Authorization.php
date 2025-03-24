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
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->throwError(401, 'No token provided or invalid format');
        }

        $this->token = $matches[1];
        $this->decodeToken();
    }

    private function decodeToken() {
        try {
            $this->decoded = JWT::decode($this->token, new Key(JWT_SECRET, JWT_ALGORITHM));
            $this->userId = $this->decoded->data->client_id;
        } catch (Exception $e) {
            $this->throwError(401, 'Invalid token');
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getDecodedToken() {
        return $this->decoded;
    }

    public function validateRequest() {
        try {
            // Check if token is expired
            if ($this->decoded->exp < time()) {
                $this->throwError(401, 'Token has expired');
            }

            // Validate user exists in WHMCS
            $command = 'GetClientsDetails';
            $postData = array(
                'clientid' => $this->userId,
                'stats' => false,
            );

            $results = localAPI($command, $postData);
            
            if ($results['result'] !== 'success') {
                $this->throwError(404, 'User not found');
            }

            return true;
        } catch (Exception $e) {
            $this->throwError(500, $e->getMessage());
        }
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

