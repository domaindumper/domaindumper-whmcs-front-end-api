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

        echo JWT_SECRET;
        
        if (empty($authHeader)) {
            $this->throwError(401, 'X-Authorization header is required');
        }

        // Clean the token - remove Bearer prefix and any whitespace/newlines
        $this->token = trim(str_replace('Bearer', '', $authHeader));
        
        try {
            // Use same JWT configuration as login page
            $this->decoded = JWT::decode(
                $this->token,
                new Key(JWT_SECRET, JWT_ALGORITHM)
            );
            
            // Validate required claims
            if (!isset($this->decoded->iss) || $this->decoded->iss !== JWT_ISS ||
                !isset($this->decoded->aud) || $this->decoded->aud !== JWT_AUD ||
                !isset($this->decoded->exp) || !isset($this->decoded->data->client_id)) {
                throw new Exception('Invalid token structure');
            }

            // Check expiration
            if ($this->decoded->exp < time()) {
                throw new Exception('Token has expired');
            }

            $this->userId = $this->decoded->data->client_id;

        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->throwError(401, 'Token has expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->throwError(401, 'Invalid token signature');
        } catch (\Firebase\JWT\BeforeValidException $e) {
            $this->throwError(401, 'Token not yet valid');
        } catch (\DomainException $e) {
            $this->throwError(401, 'Invalid token format');
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

