<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require '../../../init.php';
require '../lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $authToken = $_REQUEST['authToken'];

    if (isSessionActive($authToken)) {

        $Session = GetSession($authToken);

        $user_id = $Session->client_id;

        // Here get loggedin user details

        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $user_id,
            'stats' => true,
        );

        $results = localAPI($command, $postData);

        $Userdata = $results['client'];

        unset($Userdata['users']);

        // Prepare the response data
        $ResponseCode = 200;
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User information retrieved successfully',
            'data' => $Userdata
        ];

    } else {

        // Handle missing authorization header

        $ResponseCode = 401;
        $response = [
            'status' => 'error',
            'code' => 401,
            'message' => 'You must be logged in to access this resource.'
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