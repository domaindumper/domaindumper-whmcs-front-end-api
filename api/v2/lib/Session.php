<?php
// v2/lib/Session.php

date_default_timezone_set('Asia/Kolkata'); // Set the default timezone
// *** CORS Headers ***
$allowedOrigins = [
    'http://localhost:3000',
    'https://www.whoisextractor.com'
];

$origin = $_SERVER['HTTP_ORIGIN'];

if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit; // Terminate the request for OPTIONS
}

//functoion start
function StoreSession($authToken, $client_id, $ExpireTime)
{
    // Use prepared statements to prevent SQL injection
    Illuminate\Database\Capsule\Manager::table('tblclients')
        ->where('id', $client_id)
        ->update(['authToken' => CompressAuthToken($authToken), 'authTokenExpireAt' => date('Y-m-d H:i:s', $ExpireTime), 'updated_at' => date('Y-m-d H:i:s')]);
}

function DestroySession($authToken)
{
    // Use prepared statements to prevent SQL injection
    Illuminate\Database\Capsule\Manager::table('tblclients')
        ->where('authToken', CompressAuthToken($authToken))
        ->update(['authToken' => '', 'authTokenExpireAt' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
}

function UpdateSession($authToken)
{
    $CompressAuthToken = CompressAuthToken($authToken);

    $user = Illuminate\Database\Capsule\Manager::table('tblclients')->where('auth_token', $CompressAuthToken)->first();
}

function GetSession($authToken)
{

    // Decode and verify the JWT
    try {

        $decoded = JWT::decode($authToken, JWT_SECRET, [JWT_ALGORITHM]);

    } catch (Exception $e) {
        //echo "Invalid JWT: " . $e->getMessage();

    }

    // retun client id from Token


    return $decoded->data->client_id;
}

function isActiveSession($authToken)
{
    $CompressAuthToken = CompressAuthToken($authToken);

    $session = Illuminate\Database\Capsule\Manager::table('tblclients')
        ->where('authToken', $CompressAuthToken)
        ->first();

    if ($session) {
        try { 

            // Use timestamp from the database for more accurate comparison
            if ($session->authTokenExpireAt < date('Y-m-d H:i:s')) { 

                // Clear the authToken and authTokenExpireAt fields in the database if the token is expired

                Illuminate\Database\Capsule\Manager::table('tblclients')
                    ->where('authToken', $CompressAuthToken)
                    ->update(['authToken' => '']);
                return false;
            } else {
                return true;
            }

        } catch (Exception $e) {
            // Log the exception for debugging
           // error_log("JWT Verification Failed: " . $e->getMessage()); 
            return false;
        }
    } else {
        return false;
    }
}

function isVaildPasswordResetToken($reset_token)
{
    if (Illuminate\Database\Capsule\Manager::table('tblclients')->where('reset_token', $reset_token)->exists()) {
        return true;
    } else {
        return false;
    }
}

function isVaildPassword($password)
{
    // Define a regular expression pattern for MySQL-supported passwords
    $password_pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';

    if (preg_match($password_pattern, $password)) {
        return true;
    } else {
        return false;
    }
}

function refineUserInformation($Userdata)
{
    if (!is_array($Userdata)) {
        return null;
    }

    // Define the fields we want to keep
    $allowedFields = [
        'client_id',
        'firstname',
        'lastname',
        'email',
        'phonenumber',
        'companyname',
        'address1',
        'address2',
        'city',
        'state',
        'postcode',
        'country'
    ];

    // Create new array with only allowed fields
    $refinedData = [];
    foreach ($allowedFields as $field) {
        $refinedData[$field] = $Userdata[$field] ?? '';
    }

    return $refinedData;
}

function CompressAuthToken($authToken)
{
    $lastDotPosition = strrpos($authToken, '.');
    if ($lastDotPosition !== false) {

        // Saving only last part of JWT token

        return substr($authToken, $lastDotPosition + 1);
    } else {
        return ''; // Handle the case where there is no dot in the string
    }
}