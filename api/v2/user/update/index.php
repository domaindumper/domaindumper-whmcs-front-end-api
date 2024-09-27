<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require '../../../init.php';
require '../vendor/autoload.php';
require '../lib/Session.php';


$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $authToken = !empty($_REQUEST['authToken']) ? $_REQUEST['authToken'] : '';
    $firstname = !empty($_REQUEST['firstname']) ? $_REQUEST['firstname'] : '';
    $lastname = !empty($_REQUEST['lastname']) ? $_REQUEST['lastname'] : '';
    $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';

    if (isSessionActive($authToken)) {

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

        unset($Userdata['users']);
        unset($Userdata['userid']);
        unset($Userdata['client_id']);
        unset($Userdata['id']);
        unset($Userdata['owner_user_id']);
        unset($Userdata['uuid']);

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