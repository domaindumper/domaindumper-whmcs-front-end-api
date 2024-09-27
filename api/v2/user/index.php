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

    if (isActiveSession($authToken)) {

        $user_id = GetSession($authToken);

        // Here get loggedin user details from database

        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $user_id,
            'stats' => true,
        );

        $results = localAPI($command, $postData);

         // Remove not usfull data from user information

        $Userdata = $results['client'];

        $Userdata = refineUserInformation($Userdata);

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