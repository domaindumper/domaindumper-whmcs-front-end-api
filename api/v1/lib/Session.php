<?php

function CreateSession($authToken, $client_id)
{
    Illuminate\Database\Capsule\Manager::table('auth_tokens')
        ->updateOrInsert(
            ['client_id' => $client_id],
            ['auth_token' => $authToken, 'created_at' => date('Y-m-d H:i:s')]
        );
}

function DestroySession($authToken)
{
    Illuminate\Database\Capsule\Manager::table('auth_tokens')->where('auth_token', $authToken)->delete();
}
function UpdateSession($authToken)
{
    $user = Illuminate\Database\Capsule\Manager::table('auth_tokens')->where('auth_token', $authToken)->first();
}

function GetSession($authToken)
{
    $user = Illuminate\Database\Capsule\Manager::table('auth_tokens')->where('auth_token', $authToken)->first();

    return $user;
}

function isSessionActive($authToken)
{
    if (Illuminate\Database\Capsule\Manager::table('auth_tokens')->where('auth_token', $authToken)->exists()) {
        return true;
    }else{
        return false;
    }
}

function isPasswordResetToken($reset_token)
{
    if (Illuminate\Database\Capsule\Manager::table('tblusers')->where('reset_token', $reset_token)->exists()) {
        return true;
    }else{
        return false;
    }
}