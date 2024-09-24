<?php

function CreateSession($authToken, $client_id)
{
    Illuminate\Database\Capsule\Manager::table('tblusers')
        ->updateOrInsert(
            ['id' => $client_id],
            ['auth_token' => $authToken, 'created_at' => date('Y-m-d H:i:s')]
        );
}

function DestroySession($authToken)
{
    Illuminate\Database\Capsule\Manager::table('tblusers')->where('auth_token', $authToken)->delete();
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

    // Get the user details from the database

    $command = 'GetClientsDetails';
    $postData = array(
        'clientid' => $decoded->data->client_id,
        'stats' => true,
    );

    $UserResults = localAPI($command, $postData);

    $Userdata = $UserResults['client'];

    unset($Userdata['users']);

    // Remove not usfull data from user information
    unset($Userdata['userid']);
    unset($Userdata['client_id']);
    unset($Userdata['id']);
    unset($Userdata['owner_user_id']);
    unset($Userdata['uuid']);

    return $Userdata;
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