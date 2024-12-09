<?php
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require __DIR__ . '/../../../init.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../lib/Session.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $headers = apache_request_headers(); 
    $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

    if ($authorizationHeader) {
        $token = str_replace('Bearer ', '', $authorizationHeader); // Extract token from Bearer scheme

        try {
            $decoded = JWT::decode($token, JWT_SECRET, [JWT_ALGORITHM]); 

            // Check if the token is valid in your database (replace with your actual logic)
            $isValidToken = CheckSession($token); // Example function call

            if ($isValidToken) {
                $response = ['status' => true]; // User is logged in
            } else {
                $response = ['status' => false, 'message' => 'Invalid token'];
            }

        } catch (Exception $e) {
            $response = ['status' => false, 'message' => 'Invalid token'];
        }

    } else {
        $response = ['status' => false, 'message' => 'Authorization header missing'];
    }

} else {
    $response = ['status' => false, 'message' => 'Method not allowed'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>