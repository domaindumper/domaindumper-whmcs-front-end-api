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

    $email_preferences_general = !empty($data[email_preferences_general]) ? $data[email_preferences_general] : '';
    $email_preferences_product = !empty($data[email_preferences_product]) ? $data[email_preferences_product] : '';
    $email_preferences_invoice = !empty($data[email_preferences_invoice]) ? $data[email_preferences_invoice] : '';
    $email_preferences_support = !empty($data[email_preferences_support]) ? $data[email_preferences_support] : '';
    $email_preferences_affiliate = !empty($data[email_preferences_affiliate]) ? $data[email_preferences_affiliate] : '';

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
            'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'postcode' => $postcode,
            'country' => $country,
            'phonenumber' => $phonenumber,
            'email_preferences[general]' => $email_preferences_general,
            'email_preferences[product]' => $email_preferences_product,
            'email_preferences[invoice]' => $email_preferences_invoice,
            'email_preferences[support]' => $email_preferences_support,
            'email_preferences[affiliate]' => $email_preferences_affiliate,
            'marketingoptin' => $marketingoptin,
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