<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require '../../../../init.php';
require '../../lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';


    $command = 'ResetPassword';
    $postData = array(
        'email' => $email
    );

    $results = localAPI($command, $postData);

    // Prepare the response data

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