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
    $user = Illuminate\Database\Capsule\Manager::table('tblusers')->where('auth_token', $authToken)->first();

    return $user;
}

function isSessionActive($authToken)
{
    // Decode and verify the JWT
    try {
        $decoded = JWT::decode($jwt, JWT_SECRET, [JWT_ALGORITHM]);

        // Access the decoded data
        echo "User ID: " . $decoded->data->user_id . "\n";
        echo "Username: " . $decoded->data->username . "\n";
    } catch (Exception $e) {
        echo "Invalid JWT: " . $e->getMessage();
    }

    die();
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