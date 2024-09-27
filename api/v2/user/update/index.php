<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '/init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/lib/Session.php';


$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $authToken = !empty($_REQUEST['authToken']) ? $_REQUEST['authToken'] : '';
    $firstname = !empty($_REQUEST['firstname']) ? $_REQUEST['firstname'] : '';
    $lastname = !empty($_REQUEST['lastname']) ? $_REQUEST['lastname'] : '';
    $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';

    if (isActiveSession($authToken)) {

        $user_id = GetSession($authToken);

        $command = 'UpdateClient';
        $postData = array(
            'clientid' => $user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
        );

        $results = localAPI($command, $postData);


        // Prepare the response data

        if ($results['result'] == 'success') {

            $ResponseCode = 200;
            $response = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Your profile has been updated successfully',
            ];

        } else {
            $ResponseCode = 400;
            $response = [
                'status' => 'error',
                'code' => 400,
                'message' => $results['message']
            ];
        }


    } else {

        // Handle missing authorization header

        $ResponseCode = 401;
        $response = [
            'status' => 'error',
            'code' => 401,
            'message' => 'It looks like your session has expired. Please log in again.'
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