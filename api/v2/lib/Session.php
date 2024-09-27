<?php

function StoreSession($authToken, $client_id)
{

    print_r($authToken);
    die();
    Illuminate\Database\Capsule\Manager::table('tblusers')
        ->updateOrInsert(
            ['id' => $client_id],
            ['authToken' => $authToken, 'updated_at' => date('Y-m-d H:i:s')]
        );
}

function DestroySession($authToken)
{
    
// Genrate new token with with past date to Invalidate the token

}
function UpdateSession($authToken)
{
    $user = Illuminate\Database\Capsule\Manager::table('tblusers')->where('auth_token', $authToken)->first();
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

function isSessionActive($authToken)
{
    // Decode and verify the JWT
    try {
        $decoded = JWT::decode($authToken, JWT_SECRET, [JWT_ALGORITHM]);

        // Check if the JWT is expired
        $currentTimestamp = time();
        if ($decoded->exp < $currentTimestamp) {
            return false;
        } else {
            return true;
        }


    } catch (Exception $e) {
        //echo "Invalid JWT: " . $e->getMessage();

        return false;
    }
}

function isPasswordResetToken($reset_token)
{
    if (Illuminate\Database\Capsule\Manager::table('tblusers')->where('reset_token', $reset_token)->exists()) {
        return true;
    } else {
        return false;
    }
}

function isPasswordVaild($password)
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

    $keysToUnset = ['users', 'userid', 'client_id', 'id', 'owner_user_id', 'uuid'];

    foreach ($keysToUnset as $key) {
        if (array_key_exists($key, $Userdata)) {
            unset($Userdata[$key]);
        }
    }

    return $Userdata;
}