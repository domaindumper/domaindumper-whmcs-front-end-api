<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '/init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/lib/Register.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (IsRegistrationAllowed()) {

        $firstname = !empty($_REQUEST['firstname']) ? $_REQUEST['firstname'] : '';
        $lastname = !empty($_REQUEST['lastname']) ? $_REQUEST['lastname'] : '';
        $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : '';
        $password2 = !empty($_REQUEST['password2']) ? $_REQUEST['password2'] : '';
        $address1 = !empty($_REQUEST['address1']) ? $_REQUEST['address1'] : '';
        $city = !empty($_REQUEST['city']) ? $_REQUEST['city'] : '';
        $state = !empty($_REQUEST['state']) ? $_REQUEST['state'] : '';
        $postcode = !empty($_REQUEST['postcode']) ? $_REQUEST['postcode'] : '';
        $country = !empty($_REQUEST['country']) ? $_REQUEST['country'] : '';
        $phonenumber = !empty($_REQUEST['phonenumber']) ? $_REQUEST['phonenumber'] : '';

        $command = 'AddClient';
        $postData = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'address1' => $address1,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'phonenumber' => $phonenumber,
            'password2' => $password2,
            'clientip' => GetRealIP(),
        );

        $results = localAPI($command, $postData);

        // Prepare the response data

        $ResponseCode = 200;

        if ($results['result'] == 'success') {

            // Log the user in

            $command = 'ValidateLogin';
            $postData = array(
                'email' => $email,
                'password2' => $password2,
            );

            $results = localAPI($command, $postData);

            $clientid = $results['userid'];


            // Retry data from database

            $command = 'GetClientsDetails';
            $postData = array(
                'clientid' => $results['userid'],
                'stats' => true,
            );

            $UserResults = localAPI($command, $postData);

            $Userdata = $UserResults['client'];

            unset($Userdata['users']);

            // Create a session

            // Payload data
            $payload = [
                'iss' => JWT_ISS, // Issuer (e.g., your application's domain)
                'aud' => JWT_AUD, // Audience (e.g., the intended recipient)
                'iat' => time(), // Issued at time
                'exp' => time() + 3600, // Expiration time (1 hour in this example)
                'data' => $Userdata,
            ];

            // Generate the JWT
            $authToken = JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);

            StoreSession($authToken, $clientid);

            $response = [
                'status' => $results['result'],
                'code' => 200,
                'authToken' => $authToken,
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
        $ResponseCode = 403;
        $response = [
            'status' => 'error',
            'code' => 403,
            'message' => 'To register, you must order a service.'
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