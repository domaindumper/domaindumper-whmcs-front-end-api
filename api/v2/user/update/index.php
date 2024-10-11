<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '/init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/v2/lib/Session.php';


$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    $data = json_decode(file_get_contents('php://input'), associative: true);

    $authToken = !empty($data['authToken']) ? $data['authToken'] : '';
    $firstname = !empty($data['firstname']) ? $data['firstname'] : '';
    $lastname = !empty($data['lastname']) ? $data['lastname'] : '';
    $companyname = !empty($data['companyname']) ? $data['companyname'] : '';
    $email = !empty($data['email']) ? $data['email'] : '';
    $address1 = !empty($data['address1']) ? $data['address1'] : '';
    $address2 = !empty($data['address2']) ? $data['address2'] : '';
    $city = !empty($data['city']) ? $data['city'] : '';
    $state = !empty($data['state']) ? $data['state'] : '';
    $postcode = !empty($data['postcode']) ? $data['postcode'] : '';
    $country = !empty($data['country']) ? $data['country'] : '';
    $phonenumber = !empty($data['phonenumber']) ? $data['phonenumber'] : '';

    $email_general = !empty($data['email_general']) ? $data['email_general'] : '';
    $email_product = !empty($data['email_product']) ? $data['email_product'] : '';
    $email_invoice = !empty($data['email_invoice']) ? $data['email_invoice'] : '';
    $email_affiliate = !empty($data['email_affiliate']) ? $data['email_affiliate'] : '';

    $marketingoptin = !empty($data['marketingoptin']) ? $data['marketingoptin'] : '';

    if (isActiveSession($authToken)) {

        $user_id = GetSession($authToken);

        $command = 'UpdateClient';
        $postData = array(
            'clientid' => $user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'companyname' => $companyname,
            'email' => $email,
            'address1' => $address1,
            // 'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'phonenumber' => $phonenumber,
            'email_preferences[general]' => $email_general,
            'email_preferences[product]' => $email_product,
            'email_preferences[invoice]' => $email_invoice,
            'email_preferences[support]' => $email_support,
            'email_preferences[affiliate]' => $email_affiliate,
            'marketingoptin' => $marketingoptin,
        );

         $results = localAPI($command, $postData);


        // Change User Details also

        // Get User ID from Auth Token

        $decoded = JWT::decode($authToken, JWT_SECRET, [JWT_ALGORITHM]);

        $user_id = $decoded->data->userid;

        $command = 'UpdateUser';
        $postData = array(
            'user_id' => $user_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
        );

        $results = localAPI($command, $postData);

        //print_r($results);

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