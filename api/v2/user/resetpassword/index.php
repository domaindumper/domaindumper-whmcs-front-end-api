<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    $email = !empty($data['email']) ? $data['email'] : '';

    // No reCAPTCHA verification here 

    $command = 'ResetPassword';
    $postData = array(
        'email' => $email
    );

    $results = localAPI($command, $postData);

    $ResponseCode = 200;

    if ($results['result'] == 'success') {

        $response = [
            'status' => $results['result'],
            'code' => 200,
            'message' => 'If you are a registered user, you will be sent an email with a password reset link.'
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