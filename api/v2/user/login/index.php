<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    $data = json_decode(file_get_contents('php://input'), true);

    $email = !empty($data['email']) ? $data['email'] : '';
    $password2 = !empty($data['password']) ? $data['password'] : '';

    $command = 'ValidateLogin';
    $postData = array(
        'email' => $email,
        'password2' => $password2,
    );

    $results = localAPI($command, $postData);

    // Prepare the response data

    $ResponseCode = 200;

    if ($results['result'] == 'success') {

        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $results['userid'],
            'stats' => true,
        );

        $UserResults = localAPI($command, $postData);

        $Userdata = $UserResults['client'];

        unset($Userdata['users']);

        // Generate JWT Auth Token

        $ExpireTime = time() + 3600; // Expiration time (1 hour)

        // Payload data
        $payload = [
            'iss' => JWT_ISS, // Issuer (e.g., your application's domain)
            'aud' => JWT_AUD, // Audience (e.g., the intended recipient)
            'iat' => time(), // Issued at time
            'exp' => $ExpireTime, 
            'data' => $Userdata,
        ];

        // Generate the JWT
        $authToken = JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);

        // Save the JWT token to database for revalidation and logout feature
        StoreSession($authToken, $Userdata['client_id'], $ExpireTime);

        // Remove not useful data from user information
        $Userdata = refineUserInformation($Userdata); 

        // *** Set the JWT as an HTTP-only cookie ***
        $serialized = serialize('token', $authToken, [
            'httpOnly' => true,
            'secure' => $_SERVER['HTTPS'] ?? false, // Use HTTPS in production
            'sameSite' => 'Strict', // Prevent CSRF attacks
            'maxAge' => $ExpireTime - time(), // Set the expiration time
            'path' => '/', // Accessible across your entire domain
        ]);
        header('Set-Cookie: ' . $serialized);

        // Prepare the response data (you might not need to send the token in the response anymore)
        $response = [
            'status' => $results['result'],
            'code' => 200,
            // 'authToken' => $authToken, // You can remove this if not needed in the response
            'Userdata' => $Userdata
        ];

    } else {
        $response = [
            'status' => $results['result'],
            'code' => 200, 
            'message' => $results['message']
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