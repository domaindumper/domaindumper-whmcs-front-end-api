<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

     // Get authToken from the request body
     $data = json_decode(file_get_contents('php://input'), true);
     $authToken = isset($data['authToken']) ? $data['authToken'] : null;

    if (isActiveSession($authToken)) {

        $Session = DestroySession($authToken);

        // Clear the authToken cookie
        header('Set-Cookie: authToken=; HttpOnly; SameSite=Lax; Max-Age=0; Path=/'); // Clear the cookie

        // Prepare the response data
        $ResponseCode = 200;
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'You logged out successfully',
        ];

    } else {

        $Session = DestroySession($authToken);

        // Handle missing authorization header or invalid token
        $ResponseCode = 200;
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'You are already logged out or not logged in'
        ];
    }

} else {

    // Handle invalid request method
    $ResponseCode = 405;
    $response = [
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed'
    ];
}

http_response_code($ResponseCode);
header('Content-Type: application/json');
echo json_encode($response);
?>