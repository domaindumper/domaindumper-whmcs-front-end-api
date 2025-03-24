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
        // Get headers from multiple sources for Plesk compatibility
        $headers = $this->getAllHeaders();
        
        // Debug information
        $debugHeaders = [
            'all_headers' => $headers,
            'apache_headers' => apache_request_headers(),
            'server_vars' => $_SERVER,
            'request_method' => $_SERVER['REQUEST_METHOD']
        ];

        // Try to get Authorization header
        $authHeader = $this->getAuthorizationHeader();
        $debugHeaders['found_auth_header'] = $authHeader;

        if (empty($authHeader)) {
            $this->throwError(401, 'No Authorization header found', $debugHeaders);
        }

        // Remove 'Bearer ' if it exists
        $this->token = str_replace('Bearer ', '', $authHeader);
        $debugHeaders['extracted_token'] = $this->token;
        
        $this->decodeToken($debugHeaders);
    }

    private function getAllHeaders() {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    private function getAuthorizationHeader() {
        $auth = null;
        $possibleHeaders = [
            'Authorization',
            'HTTP_AUTHORIZATION',
            'REDIRECT_HTTP_AUTHORIZATION',
            'HTTP_X_AUTHORIZATION',
            'X-Authorization'
        ];
        
        // Debug collection
        $headerDebug = [];
        
        // Method 1: Check $_SERVER
        foreach ($_SERVER as $key => $value) {
            $headerDebug['SERVER_' . $key] = $value;
            if (in_array($key, $possibleHeaders)) {
                $auth = $value;
                $headerDebug['found_in'] = '_SERVER[' . $key . ']';
                break;
            }
        }
        
        // Method 2: Check apache_request_headers()
        if (!$auth && function_exists('apache_request_headers')) {
            $apacheHeaders = apache_request_headers();
            $headerDebug['apache_headers'] = $apacheHeaders;
            
            foreach ($apacheHeaders as $key => $value) {
                if (in_array(strtolower($key), array_map('strtolower', $possibleHeaders))) {
                    $auth = $value;
                    $headerDebug['found_in'] = 'apache_headers[' . $key . ']';
                    break;
                }
            }
        }
        
        // Method 3: Check getallheaders()
        if (!$auth) {
            $allHeaders = getallheaders();
            $headerDebug['all_headers'] = $allHeaders;
            
            foreach ($allHeaders as $key => $value) {
                if (in_array(strtolower($key), array_map('strtolower', $possibleHeaders))) {
                    $auth = $value;
                    $headerDebug['found_in'] = 'getallheaders[' . $key . ']';
                    break;
                }
            }
        }
        
        // Log debug information
        error_log('Header Debug: ' . print_r($headerDebug, true));
        
        if (!$auth) {
            $this->throwError(401, 'No authorization header found', [
                'debug_info' => $headerDebug,
                'possible_headers' => $possibleHeaders
            ]);
        }
        
        return $auth;
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
                'php_sapi' => PHP_SAPI,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
            ]
        ], JSON_PRETTY_PRINT);
        exit;
    }
}

