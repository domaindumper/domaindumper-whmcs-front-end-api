<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $authToken = $matches[1];

        if (isActiveSession($authToken)) {
            $Session = DestroySession($authToken);

            // Clear the authToken cookie (important: match all attributes from setting the cookie)
            header('Set-Cookie: authToken=; HttpOnly; SameSite=Lax; Max-Age=0; Path=/');  // Or Strict if originally set as Strict
            header('Content-Type: application/json'); // Set content type before outputting
            http_response_code(200); // Set status code before outputting
            echo json_encode([
                'status' => 'success',
                'code' => 200,
                'message' => 'You logged out successfully',
            ]);
            exit; // Stop further execution

        } else {
            $Session = DestroySession($authToken); // Still destroy session if not active

            header('Content-Type: application/json');
            http_response_code(200); // Or 401 Unauthorized if token is invalid
            echo json_encode([
                'status' => 'success', // Or 'error' if you want to indicate invalid token
                'code' => 200, // Or 401
                'message' => 'You are already logged out or not logged in',
            ]);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        http_response_code(401); // Unauthorized
        echo json_encode([
            'status' => 'error',
            'code' => 401,
            'message' => 'Unauthorized. Missing or invalid token.',
        ]);
        exit;
    }


} else {
    header('Content-Type: application/json');
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed',
    ]);
    exit;
}

?>