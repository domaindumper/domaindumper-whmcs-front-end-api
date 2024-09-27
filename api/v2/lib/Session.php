<?php

function StoreSession($authToken, $client_id)
{
    Illuminate\Database\Capsule\Manager::table('tblusers')
        ->updateOrInsert(
            ['id' => $client_id],
            ['authToken' => $authToken, 'updated_at' => date('Y-m-d H:i:s')]
        );
}

function DestroySession($authToken)
{

    // Remove the authToken from the database

    Illuminate\Database\Capsule\Manager::table('tblusers')->where('authToken', $authToken)->update(['authToken' => '']);

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

    if (Illuminate\Database\Capsule\Manager::table('tblusers')->where('authToken', $authToken)->exists()) {

        // Decode and verify the JWT
        try {
            $decoded = JWT::decode($authToken, JWT_SECRET, [JWT_ALGORITHM]);

            // Check if the JWT is expired
            $currentTimestamp = time();
            if ($decoded->exp < $currentTimestamp) {

                // if JWT is expired then return false and drop authToken from database

                Illuminate\Database\Capsule\Manager::table('tblusers')->where('authToken', $authToken)->update(['authToken' => '']);

                return false;
            } else {

                // if JWT is not expired then return true

                return true;
            }


        } catch (Exception $e) {
            //echo "Invalid JWT: " . $e->getMessage();

            return false;
        }
    } {
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